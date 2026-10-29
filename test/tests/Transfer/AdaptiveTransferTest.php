<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiServerException;
use Files\Exception\ConfigurationException;
use Files\Exception\InvalidParameterException;
use Files\Files;
use Files\Model\File;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Adaptive uploads and downloads through the real SDK: TransferJob and File's helpers with the transfer option, their
 * own curl multi transport and controller, against test/support/LoopbackServer.php, a local HTTP server in a process of
 * its own whose journal records every request it received, when, and how many storage requests were open at once.
 * Where this PHP cannot run adaptive transfers, each test checks only that asking for one is refused before anything is
 * sent.
 */
class AdaptiveTransferTest extends TestCase
{
    const MIB = 1048576;

    // 32-bit integers, an older PHP without the hrtime extension, and a userland class named like the extension's do
    // not qualify. On this PHP, a job either runs, or is refused before any request is sent or file written.
    public function testAdaptiveTransfersRefuseRuntimesWithoutA64BitNativeClockBeforeAnyRequest()
    {
        try {
            MonotonicClock::provider(4, PHP_VERSION);
            $this->fail('32-bit integers qualified for adaptive transfers');
        } catch (ConfigurationException $e) {
            $this->assertTrue(strpos($e->getMessage(), '64-bit') !== false, $e->getMessage());
        }

        if (!extension_loaded('hrtime')) {
            foreach ([false, true] as $userland) {
                if ($userland && !class_exists('HRTime\\PerformanceCounter', false)) {
                    eval('namespace HRTime; class PerformanceCounter { public static function getTicks() { return 1; } public static function getFrequency() { return 1; } }');
                }

                try {
                    MonotonicClock::provider(8, '7.2.34');
                    $this->fail('PHP 7.2 qualified without the hrtime extension' . ($userland ? ', by a userland HRTime\\PerformanceCounter' : ''));
                } catch (ConfigurationException $e) {
                    $this->assertTrue(strpos($e->getMessage(), 'hrtime extension') !== false, $e->getMessage());
                }
            }
        }

        $saved = Files::snapshot();
        $requests = [];
        Files::setBaseUrl('https://app.files.com');
        Files::setApiKey('test-key');
        Files::setHandler(function ($request) use (&$requests) {
            $requests[] = $request;
            return Create::promiseFor(new Response(500));
        });
        $destination = sys_get_temp_dir() . '/files-adaptive-' . md5(uniqid('', true));

        try {
            if (TransferJob::isSupported()) {
                $this->assertTrue(in_array(MonotonicClock::provider(PHP_INT_SIZE, PHP_VERSION), [MonotonicClock::HRTIME, MonotonicClock::PERFORMANCE_COUNTER], true));
                $this->assertSame('serial', (new TransferJob())->statistics()['transport']);
            } else {
                $calls = [
                    'download' => function () use ($destination) {
                        File::downloadToFile('reports/a.csv', $destination, true, ['transfer' => []]);
                    },
                    'upload' => function () {
                        File::uploadData('reports/a.csv', 'hello', [], ['transfer' => []]);
                    },
                ];
                foreach ($calls as $case => $call) {
                    try {
                        $call();
                        $this->fail('An adaptive ' . $case . ' ran on a PHP that cannot run it');
                    } catch (ConfigurationException $e) {
                        $this->assertTrue(strpos($e->getMessage(), 'Adaptive transfers need') === 0, $e->getMessage());
                    }
                }
            }

            $this->assertSame([], $requests);
            $this->assertFalse(file_exists($destination));
        } finally {
            $this->restore($saved);
        }
    }

    // A signed URL's expiry comes only from well-formed parameters: the shared fixtures' substitute URLs, correctly
    // formed provider examples, and nothing from malformed or unknown ones (including the fixture whose se is inside
    // another parameter). Storage retries follow Retry-After, 5xx but 501, and exactly S3's RequestTimeout error.
    public function testSignedUrlExpiryAndStorageRetriesFollowTheirRules()
    {
        $fixtures = json_decode((string) file_get_contents(__DIR__ . '/../../../shared/url_test_data.json'), true);
        $this->assertTrue(is_array($fixtures), 'The shared URL fixtures');
        $noon = gmmktime(12, 0, 0, 1, 1, 2022);
        foreach ($fixtures['substitute_urls'] as $template) {
            if (strpos($template, 'blob.core.windows.net') !== false) {
                $this->assertNull(RetryPolicy::urlExpiry(sprintf($template, '2022-01-01T12:15:00Z')), $template);
            } else {
                $this->assertSame($noon + 900, RetryPolicy::urlExpiry(sprintf($template, '20220101T120000Z', '900')), $template);
            }
        }

        foreach ($fixtures['error_urls'] as $url) {
            $this->assertFalse(is_int(RetryPolicy::urlExpiry($url)), $url);
        }

        $this->assertSame($noon + 3600, RetryPolicy::urlExpiry('https://bucket.s3.amazonaws.com/key?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Date=20220101T120000Z&X-Amz-Expires=3600&X-Amz-Signature=abc'));
        $this->assertSame($noon + 60, RetryPolicy::urlExpiry('https://site.files.com/download?X-Files-Date=20220101T120000Z&X-Files-Expires=60'));
        $this->assertSame($noon, RetryPolicy::urlExpiry('https://account.blob.core.windows.net/c/b.txt?sv=2021-08-06&se=2022-01-01T12:00:00Z&sr=b&sp=r&sig=abc'));
        $this->assertNull(RetryPolicy::urlExpiry('https://example.test/file?token=abc'));
        $this->assertFalse(RetryPolicy::urlExpiry('https://bucket.s3.amazonaws.com/key?X-Amz-Date=20220101T120000Z'));
        $this->assertSame($noon, RetryPolicy::expiresAt('https://bucket.s3.amazonaws.com/key?X-Amz-Date=20220101T120000Z&X-Amz-Expires=3600', '2022-01-01T12:00:00Z'));

        $this->assertSame(['throttle', 2.0], RetryPolicy::storageResponse(503, ['Retry-After' => ['2']], ''));
        $this->assertSame(['throttle', 0.0], RetryPolicy::storageResponse(429, [], ''));
        $this->assertSame(['fail', 0.0], RetryPolicy::storageResponse(503, ['Retry-After' => ['86400']], ''));
        $this->assertSame(['retry', 0.1], RetryPolicy::storageResponse(500, [], ''));
        $this->assertSame(['fail', 0.0], RetryPolicy::storageResponse(501, [], ''));
        $timeout = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<Error><Code>RequestTimeout</Code><Message>Idle</Message></Error>';
        $this->assertSame(['retry', 0.1], RetryPolicy::storageResponse(400, [], $timeout));
        $others = [
            '<Error><Code>RequestTimeoutX</Code></Error>', '<Error><Code>RequestTimeout</Code>', 'RequestTimeout',
            '<Error xmlns="urn:x"><Code>RequestTimeout</Code></Error>', '<!DOCTYPE Error><Error><Code>RequestTimeout</Code></Error>',
            '<Error><Inner><Code>RequestTimeout</Code></Inner></Error>',
        ];
        foreach ($others as $body) {
            $this->assertSame(['fail', 0.0], RetryPolicy::storageResponse(400, [], $body), $body);
        }
    }

    // Only one ordinary multipart upload straight to Amazon S3, signed for its host alone and binding no checksum,
    // length, header or form, lets an upload size its parts; within the SDK's limits and the job's buffer.
    public function testOnlyAnOrdinaryS3MultipartUploadSizesItsOwnParts()
    {
        $url = self::s3Url(1, 'up-1');
        $grant = self::s3Grant(1, 'up-1', 2);
        $this->assertSame('sha256:' . hash('sha256', 'https://bucket.s3.us-east-1.amazonaws.com/key.bin' . "\n" . 'up-1'), UploadPlan::route($grant, 1));
        $variants = [
            'a checksum in its URL' => ['upload_uri' => str_replace('&X-Amz-Signature', '&x-amz-checksum-crc32=AAAA&X-Amz-Signature', $url)],
            'a signed length' => ['upload_uri' => str_replace('X-Amz-SignedHeaders=host', 'X-Amz-SignedHeaders=content-length%3Bhost', $url)],
            'a host that only resembles S3' => ['upload_uri' => str_replace('bucket.s3.us-east-1.amazonaws.com', 'bucket.s3.us-east-1.amazonaws.com.example.test', $url)],
            'an accelerate endpoint' => ['upload_uri' => str_replace('bucket.s3.us-east-1.amazonaws.com', 'bucket.s3-accelerate.amazonaws.com', $url)],
            'granted headers' => ['headers' => ['Content-MD5' => 'AAAA']],
            'direct connection information' => ['direct_connection_info' => ['version' => 1]],
            'an append' => ['action' => 'put/append'],
            'another part number' => ['part_number' => 2],
            'no send instruction' => ['send' => null],
        ];
        foreach ($variants as $case => $change) {
            $this->assertNull(UploadPlan::route(array_merge($grant, $change), 1), $case);
        }

        foreach (['s3.amazonaws.com', 'bucket.s3.amazonaws.com', 's3-us-west-2.amazonaws.com', 'b.s3.dualstack.eu-central-1.amazonaws.com', 's3.cn-north-1.amazonaws.com.cn'] as $host) {
            $this->assertTrue(UploadPlan::generalPurposeEndpoint($host), $host);
        }

        foreach (['bucket.s3-fips.us-east-1.amazonaws.com', 'amazonaws.com', 'evil-amazonaws.com', 'bucket.s3.example.test'] as $host) {
            $this->assertFalse(UploadPlan::generalPurposeEndpoint($host), $host);
        }

        $this->assertSame(8 * self::MIB, UploadPlan::partSize(100 * self::MIB, 10000, PHP_INT_MAX));
        $this->assertSame(8 * self::MIB, UploadPlan::partSize(9 * self::MIB, 2, PHP_INT_MAX));
        $this->assertSame(11 * self::MIB, UploadPlan::partSize(100 * 1024 * self::MIB, 10000, PHP_INT_MAX));
        $this->assertSame(6 * self::MIB, UploadPlan::partSize(100 * self::MIB, 10000, 6 * self::MIB));
        $this->assertNull(UploadPlan::partSize(6 * 1024 * 1024 * self::MIB, 10000, PHP_INT_MAX));
        $this->assertNull(UploadPlan::partSize(20 * 1024 * self::MIB, 10, PHP_INT_MAX));
        try {
            UploadPlan::partSize(20 * 1024 * self::MIB, 10, 1024 * self::MIB);
            $this->fail('A plan beyond the buffer was given');
        } catch (InvalidParameterException $e) {
            $this->assertTrue(strpos($e->getMessage(), 'max_buffer_bytes') !== false, $e->getMessage());
        }
    }

    // Through a handler set with Files::setHandler (the serial transport), an upload whose answers are one ordinary S3
    // multipart upload sends the parts it planned (8 MiB, then the rest), each to the signed URL its grant gave, exactly
    // as issued, with the ordinary send instruction honored; with_direct_connection_info goes with every negotiation. A
    // later answer for another S3 upload stops it before that part, without ending it.
    public function testAnS3UploadSendsThePartsItPlannedAndStopsWhenItsUploadChanges()
    {
        if (!$this->adaptive()) {
            return;
        }

        $saved = Files::snapshot();
        $source = $this->localFile(self::content(9 * self::MIB + 5));
        // The one signing time of the URLs the service issues and of those they are compared with, however long the
        // upload takes.
        $date = gmdate('Ymd\THis\Z');
        try {
            foreach (['up-1' => null, 'up-2' => ApiBadResponseException::class] as $later => $failure) {
                $requests = [];
                Files::setBaseUrl('https://app.files.com');
                Files::setApiKey('test-key');
                Files::setHandler($this->s3Service($requests, $later, $date));
                $job = new TransferJob();
                $job->uploadFile('reports/big.bin', $source, ['mkdir_parents' => true]);
                $error = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $error = $e;
                }

                $this->assertSame('serial', $job->statistics()['transport']);
                $parts = array_values(array_filter($requests, function ($request) {
                    return $request['kind'] === 'storage';
                }));
                $begins = array_values(array_filter($requests, function ($request) {
                    return $request['kind'] === 'begin';
                }));
                $ends = array_values(array_filter($requests, function ($request) {
                    return $request['kind'] === 'end';
                }));
                foreach ($begins as $begin) {
                    $this->assertTrue($begin['json']['with_direct_connection_info']);
                }

                $this->assertSame([9 * self::MIB + 5, true], [$begins[0]['json']['size'], $begins[0]['json']['mkdir_parents']]);
                $this->assertSame(self::s3Url(1, 'up-1', $date), $parts[0]['uri']);
                $this->assertSame([8 * self::MIB, '8388608'], [$parts[0]['bytes'], $parts[0]['length']]);
                if ($failure === null) {
                    $this->assertNull($error);
                    $this->assertCount(2, $parts);
                    $this->assertSame([self::MIB + 5, '1048581', self::s3Url(2, 'up-1', $date)], [$parts[1]['bytes'], $parts[1]['length'], $parts[1]['uri']]);
                    $this->assertCount(1, $ends);
                    $this->assertSame([9 * self::MIB + 5, [1, 2]], [$ends[0]['json']['size'], array_column($ends[0]['json']['etags'], 'part')]);
                } else {
                    $this->assertInstanceOf($failure, $error);
                    $this->assertTrue(strpos($error->getMessage(), 'changed') !== false, $error->getMessage());
                    $this->assertSame([1, 0], [count($parts), count($ends)]);
                }
            }
        } finally {
            $this->restore($saved);
            unlink($source);
        }
    }

    // Aggressive startup with a limit of four requests: four parts go at once (the server holds them until four are
    // open), never more; each part number's body is that part's bytes, whatever order the parts arrived in, every part
    // number once; and the upload ends once with every part's ETag, in order, and the file's size.
    public function testAggressiveUploadsSendPartsConcurrentlyWithinTheRequestLimit()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(32);
        $source = $this->localFile($data);
        $config = ['upload' => ['partsize' => 4], 'hold' => ['kind' => 'storage', 'until_open' => 4, 'timeout' => 5]];
        try {
            $this->withServer($config, function ($server) use ($source, $data) {
                $job = new TransferJob(['startup' => 'aggressive', 'max_concurrent_requests' => 4]);
                $handle = $job->uploadFile('loop/a.bin', $source);
                $job->wait();

                $journal = $this->journal($server);
                $parts = $this->entries($journal, 'storage');
                $this->assertSame(['completed', 32], [$handle->status(), $handle->transferredBytes()]);
                $this->assertSame(4, $journal['max_open']);
                $this->assertCount(8, $parts);
                $numbers = [];
                foreach ($parts as $part) {
                    parse_str($part['query'], $query);
                    $number = (int) $query['part'];
                    $numbers[] = $number;
                    $this->assertSame([200, hash('sha256', substr($data, 4 * ($number - 1), 4))], [$part['status'], self::partSha($journal, $part)], 'part ' . $number);
                    $this->assertTrue(empty($part['hold_timed_out']));
                }

                sort($numbers);
                $this->assertSame(range(1, 8), $numbers);
                $ends = $this->entries($journal, 'end');
                $this->assertCount(1, $ends);
                $this->assertSame([32, range(1, 8)], [$ends[0]['json']['size'], array_column($ends[0]['json']['etags'], 'part')]);
                $statistics = $job->statistics();
                $this->assertSame(['concurrent', 4, 0, 0], [$statistics['transport'], $statistics['peak_content_in_flight'], $statistics['in_flight'], $statistics['reserved_bytes']]);
                $this->assertTrue($statistics['peak_in_flight'] <= 4);
            });
        } finally {
            unlink($source);
        }
    }

    // Default startup begins an S3 upload with one part in flight: while storage holds it, no other part is sent.
    public function testDefaultStartupSendsOnePartAtFirst()
    {
        if (!$this->adaptive()) {
            return;
        }

        $config = ['upload' => ['partsize' => 4], 'hold' => ['kind' => 'storage', 'until_open' => 2, 'timeout' => 0.3]];
        $this->withServer($config, function ($server) {
            $job = new TransferJob(['max_concurrent_requests' => 8]);
            $job->uploadData('loop/default.bin', self::content(12));
            $job->wait();

            $journal = $this->journal($server);
            $parts = $this->entries($journal, 'storage');
            $this->assertCount(3, $parts);
            $this->assertSame(1, $journal['max_open']);
            $this->assertTrue(!empty($parts[0]['hold_timed_out']));
            for ($index = 1; $index < 3; $index++) {
                $this->assertTrue($parts[$index]['received_ms'] >= $parts[$index - 1]['answered_ms']);
            }
        });
    }

    // A part storage fails (a 500, then S3's RequestTimeout) is sent again with exactly its bytes; each part is
    // acknowledged, and reported, once.
    public function testAFailedPartIsSentAgainWithItsExactBytesAndAcknowledgedOnce()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(16);
        $config = ['upload' => ['partsize' => 4, 'fail' => [2 => [500]], 'request_timeout' => [3 => 1]]];
        $this->withServer($config, function ($server) use ($data) {
            $reports = [];
            $job = new TransferJob(['concurrency' => 2]);
            $job->uploadData('loop/retry.bin', $data, [], ['progress' => function ($report) use (&$reports) {
                $reports[] = $report;
            }]);
            $job->wait();

            $journal = $this->journal($server);
            $byPart = [];
            foreach ($this->entries($journal, 'storage') as $part) {
                parse_str($part['query'], $query);
                $byPart[(int) $query['part']][] = [$part['status'], $part['body_sha256']];
            }

            ksort($byPart);
            $this->assertSame([1, 2, 3, 4], array_keys($byPart));
            $this->assertSame([[500, hash('sha256', substr($data, 4, 4))], [200, hash('sha256', substr($data, 4, 4))]], $byPart[2]);
            $this->assertSame([[400, hash('sha256', substr($data, 8, 4))], [200, hash('sha256', substr($data, 8, 4))]], $byPart[3]);
            $numbers = array_column($reports, 'part_number');
            sort($numbers);
            $this->assertSame([1, 2, 3, 4], $numbers);
            $this->assertSame([4, 8, 12, 16], array_column($reports, 'acknowledged_bytes'));
            $this->assertSame(16, $reports[3]['total_bytes']);
            $this->assertTrue($reports[3]['elapsed_seconds'] > 0 && $reports[3]['bytes_per_second'] > 0);
            $this->assertSame(2, $job->statistics()['retries']);
            $this->assertCount(1, $this->entries($journal, 'end'));
        });
    }

    // A part whose grant says retry_parts false is sent once: its failure fails the upload, which is never ended, and
    // uploadFile's own handles on the file are closed.
    public function testAPartThatMayNotBeRetriedFailsTheUploadWithoutEndingIt()
    {
        if (!$this->adaptive()) {
            return;
        }

        $source = $this->localFile(self::content(8));
        $config = ['upload' => ['partsize' => 4, 'retry_parts' => false, 'fail' => [1 => [500]]]];
        try {
            $this->withServer($config, function ($server) use ($source) {
                try {
                    File::uploadFile('loop/once.bin', $source, [], ['transfer' => ['concurrency' => 1]]);
                    $this->fail('The upload ended though its part failed');
                } catch (ApiServerException $e) {
                    $this->assertSame(500, $e->getCode());
                    $this->assertTrue(strpos($e->getMessage(), '127.0.0.1') === false, $e->getMessage());
                }

                $journal = $this->journal($server);
                $this->assertSame([1, 0], [count($this->entries($journal, 'storage')), count($this->entries($journal, 'end'))]);
                if (function_exists('get_resources')) {
                    $open = array_filter(get_resources('stream'), function ($stream) use ($source) {
                        $meta = stream_get_meta_data($stream);
                        return isset($meta['uri']) && $meta['uri'] === $source;
                    });
                    $this->assertSame([], array_values($open));
                }
            });
        } finally {
            unlink($source);
        }
    }

    // A part granted a URL that has expired is renewed (begin_upload by ref and part, with with_direct_connection_info)
    // before it is sent; storage never sees the expired URL.
    public function testAnExpiredPartUrlIsRenewedBeforeItIsSent()
    {
        if (!$this->adaptive()) {
            return;
        }

        $config = ['upload' => ['partsize' => 4, 'expired_first_grant' => true]];
        $this->withServer($config, function ($server) {
            $job = new TransferJob(['startup' => 'aggressive', 'max_concurrent_requests' => 4]);
            $job->uploadData('loop/renew.bin', self::content(8));
            $job->wait();

            $journal = $this->journal($server);
            foreach ($this->entries($journal, 'storage') as $part) {
                parse_str($part['query'], $query);
                $this->assertSame(['200', '2'], [(string) $part['status'], $query['gen']]);
            }

            $renewals = array_filter($this->entries($journal, 'begin'), function ($begin) {
                return isset($begin['json']['ref']) && $begin['json']['parts'] === 1;
            });
            $this->assertCount(2, $renewals);
            foreach ($renewals as $renewal) {
                $this->assertTrue($renewal['json']['with_direct_connection_info']);
            }

            $this->assertSame(2, $job->statistics()['renewals']);
        });
    }

    // A caller's stream's parts are read only as the job's buffer holds them (each its bytes and its send buffer), and
    // given back once acknowledged; storage receives each part number once, with that part's bytes, whatever order the
    // parts arrived in. A part larger than the whole buffer is refused before anything is read or sent.
    public function testAStreamsPartsAreHeldWithinTheBufferLimit()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $this->withServer(['upload' => ['partsize' => 8]], function ($server) use ($data) {
            $stream = fopen('php://temp', 'w+b');
            fwrite($stream, $data);
            rewind($stream);
            $job = new TransferJob(['startup' => 'aggressive', 'max_concurrent_requests' => 8, 'max_buffer_bytes' => 32]);
            $job->uploadData('loop/stream.bin', $stream, [], ['size' => 40]);
            $job->wait();

            $statistics = $job->statistics();
            $this->assertTrue($statistics['peak_reserved_bytes'] >= 16 && $statistics['peak_reserved_bytes'] <= 32, 'peak ' . $statistics['peak_reserved_bytes']);
            $this->assertSame(0, $statistics['reserved_bytes']);
            $this->assertFalse(is_resource($stream), 'The stream was not closed once its parts were acknowledged');
            $journal = $this->journal($server);
            $parts = $this->entries($journal, 'storage');
            $this->assertSame([40, 5], [$this->entries($journal, 'end')[0]['json']['size'], count($parts)]);
            $received = [];
            foreach ($parts as $part) {
                parse_str($part['query'], $query);
                $received[(int) $query['part']] = self::partSha($journal, $part);
            }

            ksort($received);
            $expected = [];
            for ($number = 1; $number <= 5; $number++) {
                $expected[$number] = hash('sha256', substr($data, 8 * ($number - 1), 8));
            }

            $this->assertSame($expected, $received);

            $this->control($server, 'reset', ['upload' => ['partsize' => 100]]);
            $stream = fopen('php://temp', 'w+b');
            fwrite($stream, self::content(200));
            rewind($stream);
            $job = new TransferJob(['max_buffer_bytes' => 150]);
            $job->uploadData('loop/large.bin', $stream, [], ['size' => 200]);
            try {
                $job->wait();
                $this->fail('A part larger than the buffer was sent');
            } catch (InvalidParameterException $e) {
                $this->assertTrue(strpos($e->getMessage(), 'max_buffer_bytes') !== false, $e->getMessage());
            }

            $this->assertSame([true, 0], [is_resource($stream), ftell($stream)]);
            $journal = $this->journal($server);
            $this->assertSame([0, 0], [count($this->entries($journal, 'storage')), count($this->entries($journal, 'end'))]);
            fclose($stream);
        });
    }

    // What a progress callback throws stops the job: wait throws it as it was, nothing more is sent, what was in flight
    // is settled, nothing is held, and the upload is not ended.
    public function testAProgressCallbacksExceptionStopsTheJobWithoutEndingTheUpload()
    {
        if (!$this->adaptive()) {
            return;
        }

        $config = ['upload' => ['partsize' => 4], 'hold' => ['kind' => 'storage', 'until_open' => 2, 'timeout' => 5]];
        $this->withServer($config, function ($server) {
            $thrown = new \RuntimeException('stop here');
            $job = new TransferJob(['concurrency' => 2]);
            $handle = $job->uploadData('loop/stop.bin', self::content(32), [], ['progress' => function () use ($thrown) {
                throw $thrown;
            }]);
            foreach ([1, 2] as $wait) {
                try {
                    $job->wait();
                    $this->fail('wait returned after its callback threw');
                } catch (\RuntimeException $e) {
                    $this->assertSame($thrown, $e, 'wait #' . $wait);
                }
            }

            $journal = $this->journal($server);
            $this->assertSame([2, 0], [count($this->entries($journal, 'storage')), count($this->entries($journal, 'end'))]);
            $statistics = $job->statistics();
            $this->assertSame([0, 0, 'cancelled'], [$statistics['in_flight'], $statistics['reserved_bytes'], $handle->status()]);
        });
    }

    // cancel stops the job: wait throws a TransferCancelledException, the upload is not ended, and the job takes no
    // more transfers.
    public function testCancellingTheJobThrowsTransferCancelled()
    {
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer(['upload' => ['partsize' => 4]], function ($server) {
            $job = null;
            $job = new TransferJob(['concurrency' => 1]);
            $job->uploadData('loop/cancel.bin', self::content(16), [], ['progress' => function () use (&$job) {
                $job->cancel();
            }]);
            try {
                $job->wait();
                $this->fail('A cancelled job completed');
            } catch (TransferCancelledException $e) {
                $this->assertSame([1, 0], [count($this->entries($this->journal($server), 'storage')), count($this->entries($this->journal($server), 'end'))]);
            }

            try {
                $job->uploadData('loop/later.bin', 'abc');
                $this->fail('A stopped job took another transfer');
            } catch (InvalidParameterException $e) {
                $this->assertTrue(strpos($e->getMessage(), 'stopped') !== false);
            }
        });
    }

    // Pausing from a progress callback returns from wait with the caller's stream where it was read to; resuming sends
    // the rest without sending an acknowledged part again, and ends the upload once.
    public function testAPausedStreamUploadResumesWithoutSendingAcknowledgedParts()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(16);
        $this->withServer(['upload' => ['partsize' => 4]], function ($server) use ($data) {
            $stream = fopen('php://temp', 'w+b');
            fwrite($stream, $data);
            rewind($stream);
            $reports = [];
            $job = null;
            $job = new TransferJob(['concurrency' => 1]);
            $handle = $job->uploadData('loop/pause.bin', $stream, [], ['size' => 16, 'progress' => function ($report) use (&$job, &$reports) {
                $reports[] = $report;
                if (count($reports) === 1) {
                    $job->pause();
                }
            }]);
            $job->wait();

            $this->assertSame(['paused', true, 4, 4], [$handle->status(), $job->isPaused(), ftell($stream), $handle->transferredBytes()]);
            $checkpoint = $handle->checkpoint();
            $this->assertSame([1, true], [$checkpoint['parts'][0]['part'], $checkpoint['parts'][0]['acknowledged']]);
            $this->assertCount(1, $this->entries($this->journal($server), 'storage'));
            $this->assertSame(0, $job->statistics()['reserved_bytes']);

            $job->resume();
            $job->wait();
            $journal = $this->journal($server);
            $numbers = [];
            foreach ($this->entries($journal, 'storage') as $part) {
                parse_str($part['query'], $query);
                $numbers[] = (int) $query['part'];
            }

            $this->assertSame([1, 2, 3, 4], $numbers);
            $this->assertSame(['completed', 16], [$handle->status(), $this->entries($journal, 'end')[0]['json']['size']]);
            $this->assertSame([16, 4], [$reports[3]['acknowledged_bytes'], count($reports)]);
            $this->assertFalse(is_resource($stream));
        });
    }

    // The uploads of one job teach one controller, every file's bytes included; another job learns alone.
    public function testTheTransfersOfAJobShareOneControllerAndAnotherJobLearnsAlone()
    {
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer(['upload' => ['partsize' => 4]], function ($server) {
            $job = new TransferJob(['max_concurrent_requests' => 8]);
            foreach (['loop/s1.bin', 'loop/s2.bin', 'loop/s3.bin'] as $path) {
                $job->uploadData($path, self::content(4, $path));
            }

            $job->wait();
            $other = new TransferJob(['max_concurrent_requests' => 8]);
            $other->uploadData('loop/s4.bin', self::content(4));
            $other->wait();

            foreach ([[$job, 12], [$other, 4]] as $case) {
                $controllers = $case[0]->statistics()['controllers'];
                $this->assertCount(1, $controllers);
                $controller = reset($controllers);
                $this->assertSame(['s3-default', $case[1]], [$controller['policy'], $controller['accepted_bytes']]);
            }

            $this->assertCount(4, $this->entries($this->journal($server), 'end'));
        });
    }

    // A download bound to its version by its first range's strong ETag fetches its other ranges concurrently, each
    // with If-Match, and replaces the destination only once complete.
    public function testAVersionBoundDownloadFetchesItsRangesConcurrently()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $destination = $this->localPath();
        $config = $this->downloadConfig($data, ['hold' => ['kind' => 'download', 'until_open' => 4, 'timeout' => 5, 'after' => 1]]);
        try {
            $this->withServer($config, function ($server) use ($data, $destination) {
                $reports = [];
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $handle = $job->downloadToFile('loop/d.bin', $destination, ['progress' => function ($report) use (&$reports) {
                    $reports[] = $report;
                }]);
                $job->wait();

                $this->assertSame($data, file_get_contents($destination));
                $this->assertEquals((object) ['received' => 40, 'total' => 40], $handle->result());
                $journal = $this->journal($server);
                $downloads = $this->entries($journal, 'download');
                $this->assertCount(5, $downloads);
                $this->assertSame(['bytes=0-7', false], [$downloads[0]['headers']['range'], isset($downloads[0]['headers']['if-match'])]);
                for ($index = 1; $index < 5; $index++) {
                    $this->assertSame('"v1"', $downloads[$index]['headers']['if-match']);
                }

                $this->assertSame(4, $journal['max_open']);
                $this->assertSame(40, $reports[count($reports) - 1]['received_bytes']);
                $this->assertSame([], $this->staging($destination));
            });
        } finally {
            @unlink($destination);
        }
    }

    // Range responses that are wrong or cut short are fetched again; storage that ignores the range, gives no strong
    // ETag or cuts a whole response short is downloaded whole, from its first byte each time.
    public function testDownloadsRetryBadRangesAndRestartWholeResponsesFromTheirFirstByte()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $cases = [
            'a range answered with another range' => [['download' => ['wrong_range' => [8 => 1]]], 6],
            'a range cut short' => [['download' => ['truncate' => [16 => 1]]], 6],
            'the range ignored' => [['download' => ['mode' => 'ignore_range']], 1],
            'no ETag' => [['download' => ['mode' => 'no_etag']], 2],
            'a weak ETag' => [['download' => ['mode' => 'weak_etag']], 2],
            'a whole response cut short' => [['download' => ['mode' => 'no_etag', 'truncate' => ['whole' => 2]]], 3],
        ];
        $this->withServer([], function ($server) use ($data, $cases) {
            foreach ($cases as $case => list($change, $requests)) {
                $this->control($server, 'reset', $this->downloadConfig($data, $change));
                $destination = $this->localPath();
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $job->downloadToFile('loop/d.bin', $destination);
                $job->wait();

                $this->assertSame($data, file_get_contents($destination), $case);
                $downloads = $this->entries($this->journal($server), 'download');
                $this->assertCount($requests, $downloads, $case);
                if ($requests > 1 && $requests < 6) {
                    $this->assertFalse(isset($downloads[count($downloads) - 1]['headers']['range']), $case);
                }

                $this->assertSame([], $this->staging($destination), $case);
                unlink($destination);
            }
        });
    }

    // A file that changes during its download (a 412 for If-Match) and a response from the Files.com web app fail the
    // download before the destination is written; its staging file is removed.
    public function testADownloadFailsBeforeWritingWhenTheFileChangesOrTheWebAppAnswers()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $cases = [
            'changed' => [['download' => ['change_after' => 2]], 'changed during its download'],
            'web app' => [['download' => ['frontend' => true]], 'X-Files-Frontend-App'],
        ];
        $this->withServer([], function ($server) use ($data, $cases) {
            foreach ($cases as $case => list($change, $message)) {
                $this->control($server, 'reset', $this->downloadConfig($data, $change));
                $destination = $this->localPath();
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $handle = $job->downloadToFile('loop/d.bin', $destination);
                try {
                    $job->wait();
                    $this->fail('The ' . $case . ' download completed');
                } catch (ApiBadResponseException $e) {
                    $this->assertTrue(strpos($e->getMessage(), $message) !== false, $e->getMessage());
                }

                $this->assertSame(['failed', false, []], [$handle->status(), file_exists($destination), $this->staging($destination)], $case);
            }
        });
    }

    // A download to a caller's stream writes its ranges in order, after what the stream held, and leaves it open: five
    // ranges of 8 bytes within a 24-byte buffer, and five of 64 KiB within a 192 KiB buffer, each of which the transport
    // may hand over in several pieces that its range's buffer is built from. Either way the download completes with every
    // reservation given back.
    public function testADownloadToAStreamWritesItsRangesInOrder()
    {
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer([], function ($server) {
            // [download_part_bytes, max_buffer_bytes]: at most three ranges are held at once.
            foreach ([[8, 24], [65536, 196608]] as list($part, $buffer)) {
                $data = self::content(5 * $part);
                $this->control($server, 'reset', $this->downloadConfig($data, []));
                $stream = fopen('php://temp', 'w+b');
                fwrite($stream, 'prefix:');
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => $part, 'max_buffer_bytes' => $buffer]);
                $handle = $job->downloadToStream('loop/d.bin', $stream);
                $job->wait();

                $what = 'ranges of ' . $part . ' bytes';
                $statistics = $job->statistics();
                $this->assertTrue(is_resource($stream), $what);
                rewind($stream);
                $this->assertSame('prefix:' . $data, stream_get_contents($stream), $what);
                $this->assertSame(['completed', 0], [$handle->status(), $statistics['reserved_bytes']], $what);
                $this->assertEquals((object) ['received' => 5 * $part, 'total' => 5 * $part], $handle->result(), $what);
                $this->assertTrue($statistics['peak_reserved_bytes'] <= $buffer, $what . ': peak ' . $statistics['peak_reserved_bytes']);
                $this->assertCount(5, $this->entries($this->journal($server), 'download'), $what);
                fclose($stream);
            }
        });
    }

    // A job keeps the credentials it was made with, whatever the SDK is set to later, and a job made later takes the
    // SDK's settings as they are then; each is seen in real requests of each job. A redirect to another origin strips
    // the credentials from that redirect chain alone: the job's later API requests, over the same pooled transport,
    // carry them; storage requests never do. An upload's first-part parameters go with its first begin_upload alone, not
    // with the later one by its ref.
    public function testAJobKeepsItsCredentialsAndRedirectsStripThemFromOneChainOnly()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(16);
        $destination = $this->localPath();
        try {
            $this->withServer($this->downloadConfig($data, ['download' => ['redirect_metadata' => true], 'upload' => ['partsize' => 8]]), function ($server) use ($data, $destination) {
                Files::setApiKey('key-a');
                Files::setWorkspaceId(7);
                $job = new TransferJob(['concurrency' => 2, 'download_part_bytes' => 8]);
                Files::setApiKey('key-b');
                Files::setWorkspaceId(9);
                $job->downloadToFile('loop/d.bin', $destination);
                $job->wait();
                // Two parts of 8 bytes: a first begin_upload, and a later one by the upload's ref.
                $job->uploadData('loop/u.bin', self::content(16, 'u'), ['mkdir_parents' => true], ['progress' => function () {
                }]);
                $job->wait();
                $later = new TransferJob();
                $later->uploadData('loop/v.bin', 'abcd', ['mkdir_parents' => true]);
                $later->wait();

                $this->assertSame($data, file_get_contents($destination));
                $journal = $this->journal($server);
                $metadata = $this->entries($journal, 'metadata');
                $this->assertCount(2, $metadata);
                $this->assertSame([$server['ports'][0], 'key-a', '7'], [$metadata[0]['port'], $metadata[0]['headers']['x-filesapi-key'], $metadata[0]['headers']['x-files-workspace-id']]);
                $this->assertSame([$server['ports'][1], false, false], [$metadata[1]['port'], isset($metadata[1]['headers']['x-filesapi-key']), isset($metadata[1]['headers']['x-files-workspace-id'])]);
                $begins = $this->entries($journal, 'begin');
                $ends = $this->entries($journal, 'end');
                $byJob = ['loop/u.bin' => ['key-a', '7', 2], 'loop/v.bin' => ['key-b', '9', 1]];
                $seen = 0;
                foreach ($byJob as $path => $expected) {
                    $ownBegins = self::forPath($begins, $path);
                    $ownEnds = self::forPath($ends, $path);
                    $this->assertSame([$expected[2], 1], [count($ownBegins), count($ownEnds)], $path);
                    foreach (array_merge($ownBegins, $ownEnds) as $entry) {
                        $this->assertSame([$expected[0], $expected[1]], [$entry['headers']['x-filesapi-key'], $entry['headers']['x-files-workspace-id']], $path);
                        $this->assertFalse(isset($entry['json']['progress']) || isset($entry['json']['transfer']));
                    }

                    $this->assertTrue($ownBegins[0]['json']['mkdir_parents'], $path);
                    $seen += count($ownBegins) + count($ownEnds);
                }

                $this->assertSame(count($begins) + count($ends), $seen);
                $continued = self::forPath($begins, 'loop/u.bin')[1];
                $this->assertSame([false, 2], [isset($continued['json']['mkdir_parents']), $continued['json']['part']]);
                $this->assertTrue(isset($continued['json']['ref']));
                foreach (array_merge($this->entries($journal, 'storage'), $this->entries($journal, 'download')) as $entry) {
                    $this->assertFalse(isset($entry['headers']['x-filesapi-key']) || isset($entry['headers']['x-files-workspace-id']));
                }
            });
        } finally {
            @unlink($destination);
        }
    }

    // A handler set with Files::setHandler that is not a CurlMultiHandler is used as it is, one request at a time, and
    // reported as the serial transport; explicit concurrency with it is refused before any request.
    public function testAHandlerThatIsNotACurlMultiHandlerSendsOneRequestAtATime()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(12);
        $this->withServer(['upload' => ['partsize' => 4]], function ($server) use ($data) {
            $inner = new CurlHandler();
            Files::setHandler(function ($request, array $options) use ($inner) {
                return $inner($request, $options);
            });
            $job = new TransferJob();
            $job->uploadData('loop/serial.bin', $data);
            $job->wait();

            $journal = $this->journal($server);
            $this->assertSame(['serial', 1, 3], [$job->statistics()['transport'], $journal['max_open'], count($this->entries($journal, 'storage'))]);
            foreach (['concurrency' => 4, 'startup' => 'aggressive'] as $option => $value) {
                try {
                    new TransferJob([$option => $value]);
                    $this->fail('The serial transport took ' . $option);
                } catch (InvalidParameterException $e) {
                    $this->assertTrue(strpos($e->getMessage(), 'one request at a time') !== false, $e->getMessage());
                }
            }

            $this->assertCount(count($journal['entries']), $this->journal($server)['entries']);
        });
    }

    // A RequestTimeout is retried only as S3's whole, well-formed error. Malformed XML (an end tag with more than its
    // name, a bare &, an unquoted or repeated attribute, a declaration after white space or of another version, a
    // control character, or a reference to a character XML does not allow, such as &#0;), a body past 64 KiB, and one
    // its reader knows was cut short are not; nor is a real response whose first 64 KiB look like the error but which
    // goes on, from storage for an upload part or for a download range: each fails at its first attempt.
    public function testOnlyAWholeWellFormedRequestTimeoutIsRetried()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<Error><Code>RequestTimeout</Code><Message>Idle</Message></Error>';
        $wellFormed = [
            $xml, '<Error><Code>RequestTimeout</Code ></Error>',
            '<?xml version=\'1.0\'?><Error><Code>RequestTimeout</Code><Message a="1" b=\'&amp;\'>x &lt; y &#65; &#x42;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message a="&#x9;">&#0065; &#xD7FF; &#xE000; &#x10FFFF;</Message></Error>',
        ];
        foreach ($wellFormed as $body) {
            $this->assertSame(['retry', 0.1], RetryPolicy::storageResponse(400, [], $body), $body);
        }

        $malformed = [
            '<Error><Code>RequestTimeout</Code broken></Error>',
            '<Error><Code>RequestTimeout</Code><Message>a & b</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message a=1>x</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message a="1" a="2">x</Message></Error>',
            "\n" . '<?xml version="1.0"?><Error><Code>RequestTimeout</Code></Error>',
            '<?xml version="2.0"?><Error><Code>RequestTimeout</Code></Error>',
            "<Error><Code>RequestTimeout</Code><Message>\x01</Message></Error>",
            '<Error><Code>RequestTimeout</Code><Message>&#0;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message>&#x0;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message a="&#1;">x</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message>&#xD800;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message>&#xFFFF;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message>&#x110000;</Message></Error>',
            '<Error><Code>RequestTimeout</Code><Message>&#1114112;</Message></Error>',
            str_pad('<Error><Code>RequestTimeout</Code></Error>', 65537, ' '),
        ];
        foreach ($malformed as $body) {
            $this->assertSame(['fail', 0.0], RetryPolicy::storageResponse(400, [], $body), substr($body, 0, 80));
        }

        $this->assertSame(['fail', 0.0], RetryPolicy::storageResponse(400, [], $xml, false));
        $this->assertSame(['retry', 0.1], RetryPolicy::storageResponse(500, [], '', false));
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer(['upload' => ['partsize' => 4, 'request_timeout_padded' => [1 => 1]]], function ($server) {
            $job = new TransferJob(['concurrency' => 1]);
            $handle = $job->uploadData('loop/padded.bin', self::content(8));
            try {
                $job->wait();
                $this->fail('A part answered with a long body that begins like RequestTimeout was retried to success');
            } catch (ApiBadResponseException $e) {
                $this->assertSame(400, $e->getCode());
            }

            $journal = $this->journal($server);
            $parts = $this->entries($journal, 'storage');
            $this->assertSame([1, 400, 0], [count($parts), $parts[0]['status'], count($this->entries($journal, 'end'))]);
            $this->assertSame([0, 'failed'], [$job->statistics()['retries'], $handle->status()]);
        });

        $data = self::content(40);
        $this->withServer($this->downloadConfig($data, ['download' => ['timeout_padded' => [8 => 1]]]), function ($server) {
            $destination = $this->localPath();
            $job = new TransferJob(['concurrency' => 1, 'download_part_bytes' => 8]);
            $handle = $job->downloadToFile('loop/d.bin', $destination);
            try {
                $job->wait();
                $this->fail('A range answered with a long body that begins like RequestTimeout was retried to success');
            } catch (ApiBadResponseException $e) {
                $this->assertSame(400, $e->getCode());
            }

            $ranges = array_filter($this->entries($this->journal($server), 'download'), function ($entry) {
                return $entry['headers']['range'] === 'bytes=8-15';
            });
            $this->assertCount(1, $ranges);
            $this->assertSame([0, 'failed', false, []], [$job->statistics()['retries'], $handle->status(), file_exists($destination), $this->staging($destination)]);
        });
    }

    // Each download attempt is judged by the URL it was sent with. The first URL lives 17 s. The job's expiry clock
    // (RetryPolicy's, set here through reflection) stands still until the server holds two later ranges sent with that
    // URL (concurrency 2), however long sending them took: here the first range's report takes 3 s, past the 2 s the
    // real clock would leave before renewing. Then the clock is real again: the job renews the URL, within 15 s of its
    // expiry, while those ranges are still out, and storage refuses them as expired only once their own URL has expired
    // and the second URL was given. Each is retried with that second URL, as one of its retries and without another
    // renewal, and the file arrives whole. A 403 for a URL stating no expiry is no expiry: it fails the download without
    // a renewal.
    public function testAnExpiredAttemptIsRetriedWithTheNewerUrlAndUnknownExpiryIsRefused()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $destinations = [$this->localPath(), $this->localPath()];
        $clock = new \ReflectionProperty(RetryPolicy::class, 'expiryClock');
        if (PHP_VERSION_ID < 80100) {
            $clock->setAccessible(true);
        }

        try {
            $this->withServer($this->downloadConfig($data, ['download' => ['expiring_url' => 17]]), function ($server) use ($data, $destinations, $clock) {
                // Taken before the first URL is given, which this time therefore finds more than 15 s from expiring.
                $still = time();
                $held = false;
                $clock->setValue(null, function () use ($server, $still, &$held) {
                    if (!$held) {
                        $old = 0;
                        foreach ($this->entries($this->journal($server), 'download') as $entry) {
                            parse_str($entry['query'], $query);
                            $old += $query['gen'] === '1' && $entry['headers']['range'] !== 'bytes=0-7' ? 1 : 0;
                        }

                        $held = $old === 2;
                    }

                    return $held ? time() : $still;
                });
                $delayed = false;
                $started = microtime(true);
                $job = new TransferJob(['concurrency' => 2, 'download_part_bytes' => 8]);
                $job->downloadToFile('loop/d.bin', $destinations[0], ['progress' => function () use (&$delayed) {
                    if (!$delayed) {
                        $delayed = true;
                        usleep(3000000);
                    }
                }]);
                $job->wait();
                $elapsed = microtime(true) - $started;
                $clock->setValue(null, null);

                $this->assertSame($data, file_get_contents($destinations[0]));
                $journal = $this->journal($server);
                $metadata = $this->entries($journal, 'metadata');
                $this->assertCount(2, $metadata);
                $attempts = [];
                foreach ($this->entries($journal, 'download') as $entry) {
                    parse_str($entry['query'], $query);
                    $attempts[$entry['headers']['range']][] = [$query['gen'], $entry['status'], $entry['received_ms'], $entry['answered_ms']];
                }

                foreach (['bytes=8-15', 'bytes=16-23'] as $range) {
                    $this->assertCount(2, $attempts[$range], $range);
                    $this->assertSame([['1', 403], ['2', 206]], [array_slice($attempts[$range][0], 0, 2), array_slice($attempts[$range][1], 0, 2)], $range);
                    $this->assertTrue($attempts[$range][0][2] >= $attempts['bytes=0-7'][0][3] + 3000, $range . ' went out before the first range\'s report returned');
                    $this->assertTrue($attempts[$range][0][2] < $metadata[1]['received_ms'], $range . ' went out after the renewal');
                    $this->assertTrue($attempts[$range][0][3] >= $metadata[1]['answered_ms'], $range . ' was refused before the second URL was given');
                }

                $statistics = $job->statistics();
                $this->assertSame([true, 1, 2], [$held, $statistics['renewals'], $statistics['retries']]);
                $this->assertTrue($elapsed < 40, 'The download took ' . $elapsed . ' s');

                $this->control($server, 'reset', $this->downloadConfig($data, ['download' => ['unsigned' => true, 'forbidden' => true]]));
                $job = new TransferJob(['download_part_bytes' => 8]);
                $handle = $job->downloadToFile('loop/d.bin', $destinations[1]);
                try {
                    $job->wait();
                    $this->fail('A 403 for a URL stating no expiry was retried to success');
                } catch (ApiBadResponseException $e) {
                    $this->assertSame(403, $e->getCode());
                }

                $journal = $this->journal($server);
                $this->assertSame(
                    [1, 1, 0, 'failed', false],
                    [count($this->entries($journal, 'metadata')), count($this->entries($journal, 'download')), $job->statistics()['renewals'], $handle->status(), file_exists($destinations[1])]
                );
            });
        } finally {
            $clock->setValue(null, null);
            foreach ($destinations as $destination) {
                @unlink($destination);
            }
        }
    }

    // A download is published only after its own final report returns. A final report that throws or cancels leaves
    // the existing destination as it was and removes the staging file; one that pauses keeps the verified staging file,
    // which resuming moves into place without fetching anything again.
    public function testADownloadIsPublishedOnlyAfterItsFinalReportReturns()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $this->withServer($this->downloadConfig($data, []), function ($server) use ($data) {
            foreach (['throw', 'cancel', 'pause'] as $case) {
                $this->control($server, 'reset', $this->downloadConfig($data, []));
                $destination = $this->localPath();
                file_put_contents($destination, 'older');
                $thrown = new \RuntimeException('final report');
                $job = null;
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $handle = $job->downloadToFile('loop/d.bin', $destination, ['progress' => function ($report) use (&$job, $case, $thrown) {
                    if ($report['received_bytes'] !== $report['total_bytes']) {
                        return;
                    }

                    if ($case === 'throw') {
                        throw $thrown;
                    }

                    if ($case === 'cancel') {
                        $job->cancel();
                    } else {
                        $job->pause();
                    }
                }]);
                try {
                    $job->wait();
                    $this->assertSame('pause', $case, 'wait returned after the final report stopped the job');
                } catch (TransferCancelledException $e) {
                    $this->assertSame('cancel', $case);
                } catch (\RuntimeException $e) {
                    $this->assertSame([$thrown, 'throw'], [$e, $case]);
                }

                $this->assertSame(['older', 5], [file_get_contents($destination), count($this->entries($this->journal($server), 'download'))], $case);
                if ($case !== 'pause') {
                    $this->assertSame(['cancelled', []], [$handle->status(), $this->staging($destination)], $case);
                    unlink($destination);
                    continue;
                }

                $this->assertSame(['paused', true], [$handle->status(), $job->isPaused()]);
                $this->assertCount(1, $this->staging($destination));
                $job->resume();
                $job->wait();
                $this->assertSame(
                    [$data, 'completed', [], 5],
                    [file_get_contents($destination), $handle->status(), $this->staging($destination), count($this->entries($this->journal($server), 'download'))]
                );
                $this->assertEquals((object) ['received' => 40, 'total' => 40], $handle->result());
                unlink($destination);
            }
        });
    }

    // Storage that serves the first range and then answers ranges whole is downloaded whole from its first byte. The
    // eight bytes accepted from the first range are delivered again, not accepted again: the job and the handle count
    // 40 bytes, received bytes never go down, and after a pause the resumed call's rate counts only its own 32 bytes.
    public function testAWholeRestartCountsEachByteOnce()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $config = $this->downloadConfig($data, ['download' => ['mode' => 'ranges_once']]);
        $this->withServer($config, function ($server) use ($data, $config) {
            foreach ([false, true] as $pause) {
                $this->control($server, 'reset', $config);
                $destination = $this->localPath();
                $reports = [];
                $job = null;
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $handle = $job->downloadToFile('loop/d.bin', $destination, ['progress' => function ($report) use (&$job, &$reports, $pause) {
                    $reports[] = $report;
                    if ($pause && count($reports) === 1) {
                        $job->pause();
                    }
                }]);
                $job->wait();
                if ($pause) {
                    $this->assertSame(['paused', 8, 8], [$handle->status(), $handle->transferredBytes(), $job->statistics()['accepted_bytes']]);
                    $job->resume();
                    $job->wait();
                }

                $this->assertSame(
                    [$data, 'completed', 40, 40],
                    [file_get_contents($destination), $handle->status(), $handle->transferredBytes(), $job->statistics()['accepted_bytes']]
                );
                $received = array_column($reports, 'received_bytes');
                $sorted = $received;
                sort($sorted);
                $this->assertSame([8, 40, $sorted], [$received[0], $received[count($received) - 1], $received]);
                $last = $reports[count($reports) - 1];
                $credited = $last['bytes_per_second'] * $last['elapsed_seconds'];
                $this->assertTrue(abs($credited - ($pause ? 32 : 40)) < 0.001, 'The last rate credits ' . $credited . ' bytes');
                $wholes = array_filter($this->entries($this->journal($server), 'download'), function ($entry) {
                    return !isset($entry['headers']['range']);
                });
                $this->assertCount(1, $wholes);
                unlink($destination);
            }
        });
    }

    // A caller's stream with an 8-byte buffer and 8-byte parts, from storage that answers the first request whole or
    // gives no ETag: the 40-byte whole response is held within its attempt's 8-byte charge, the rest spilled to a
    // temporary file, and stays charged through its final report and a pause, unwritten to the stream until resuming
    // publishes it, once. Cancelled from its final report, it gives its charge up and writes nothing.
    public function testAWholeResponseForAStreamIsHeldWithinItsChargeUntilPublished()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $this->withServer([], function ($server) use ($data) {
            foreach (['ignore_range' => 1, 'no_etag' => 2] as $mode => $requests) {
                foreach (['pause', 'cancel'] as $case) {
                    $what = $mode . ', ' . $case;
                    $this->control($server, 'reset', $this->downloadConfig($data, ['download' => ['mode' => $mode]]));
                    $stream = fopen('php://temp', 'w+b');
                    fwrite($stream, 'prefix:');
                    $held = null;
                    $job = null;
                    $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8, 'max_buffer_bytes' => 8]);
                    $handle = $job->downloadToStream('loop/d.bin', $stream, ['progress' => function () use (&$job, &$held, $case) {
                        $held = $job->statistics()['reserved_bytes'];
                        if ($case === 'pause') {
                            $job->pause();
                        } else {
                            $job->cancel();
                        }
                    }]);
                    try {
                        $job->wait();
                        $this->assertSame('pause', $case, $what);
                    } catch (TransferCancelledException $e) {
                        $this->assertSame('cancel', $case, $what);
                    }

                    rewind($stream);
                    $this->assertSame(
                        [8, 'prefix:', $requests],
                        [$held, stream_get_contents($stream), count($this->entries($this->journal($server), 'download'))],
                        $what
                    );
                    if ($case === 'cancel') {
                        $this->assertSame(['cancelled', 0], [$handle->status(), $job->statistics()['reserved_bytes']], $what);
                        fclose($stream);
                        continue;
                    }

                    $this->assertSame(['paused', 8], [$handle->status(), $job->statistics()['reserved_bytes']], $what);
                    $job->resume();
                    $job->wait();
                    rewind($stream);
                    $statistics = $job->statistics();
                    $this->assertSame(
                        ['prefix:' . $data, 'completed', 0, $requests],
                        [stream_get_contents($stream), $handle->status(), $statistics['reserved_bytes'], count($this->entries($this->journal($server), 'download'))],
                        $what
                    );
                    $this->assertTrue($statistics['peak_reserved_bytes'] <= 8, $what . ': peak ' . $statistics['peak_reserved_bytes']);
                    fclose($stream);
                }
            }
        });
    }

    // A begin_upload that connects and is then never answered is ended by the job's read timeout (here 1 s), even while
    // a pause, which waits for API requests in flight, is pending: the failure then stops the job, as any API failure
    // during a pause does, and stays its error; nothing more is sent or ended. Without the stall limit the job would wait
    // for the fixture's 20 s.
    public function testAStalledApiRequestEndsAtTheReadTimeoutEvenWhilePausing()
    {
        if (!$this->adaptive()) {
            return;
        }

        $readTimeout = Files::$readTimeout;
        try {
            $this->withServer(['upload' => ['partsize' => 4], 'api_stall' => ['loop/stalled.bin'], 'stall_seconds' => 20], function ($server) {
                Files::$readTimeout = 1;
                $job = null;
                $job = new TransferJob();
                $stalled = $job->uploadData('loop/stalled.bin', self::content(4));
                $pausing = $job->uploadData('loop/pausing.bin', self::content(4), [], ['progress' => function () use (&$job) {
                    $job->pause();
                }]);
                $started = microtime(true);
                $failure = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $failure = $e;
                }

                $elapsed = microtime(true) - $started;
                $this->assertNotNull($failure, 'wait returned while a begin_upload was stalled');
                $this->assertFalse($failure instanceof TransferCancelledException);
                $this->assertTrue($elapsed < 10, 'The stalled request ended after ' . $elapsed . ' s');
                $this->assertSame([false, 'failed', 'cancelled'], [$job->isPaused(), $stalled->status(), $pausing->status()]);
                try {
                    $job->wait();
                    $this->fail('A stopped job waited without its error');
                } catch (\Exception $e) {
                    $this->assertSame($failure, $e);
                }

                $journal = $this->journal($server);
                $stalls = array_filter($this->entries($journal, 'begin'), function ($entry) {
                    return !empty($entry['stalled']);
                });
                $this->assertSame([1, 1, 0], [count($stalls), count($this->entries($journal, 'storage')), count($this->entries($journal, 'end'))]);
            });
        } finally {
            Files::$readTimeout = $readTimeout;
        }
    }

    // A begin_upload, or an upload's end, that reached the server, which acted on it, and was then never answered is
    // ended by the job's read timeout (here 1 s) and, though retries remain and nothing pauses the job, is not sent
    // again: it may have taken effect. The server sees one upload session for the path, the upload fails, and one whose
    // end stalled is ended once. A begin_upload that could not connect at all reached no server, and is sent again.
    public function testAnApiRequestThatReachedTheServerIsNotSentAgainAfterItsReadTimeout()
    {
        if (!$this->adaptive()) {
            return;
        }

        $readTimeout = Files::$readTimeout;
        $config = ['upload' => ['partsize' => 4], 'api_stall' => ['loop/begun.bin'], 'end_stall' => ['loop/ended.bin'], 'stall_seconds' => 20];
        try {
            $this->withServer($config, function ($server) {
                Files::$readTimeout = 1;
                // [upload sessions, parts, ends] the server saw for each path.
                foreach (['loop/begun.bin' => [1, 0, 0], 'loop/ended.bin' => [1, 1, 1]] as $path => $expected) {
                    $job = new TransferJob(['max_retries' => 2]);
                    $handle = $job->uploadData($path, self::content(4));
                    $started = microtime(true);
                    $failure = null;
                    try {
                        $job->wait();
                    } catch (\Exception $e) {
                        $failure = $e;
                    }

                    $elapsed = microtime(true) - $started;
                    $this->assertNotNull($failure, 'The upload to ' . $path . ' completed though its request was never answered');
                    $this->assertFalse($failure instanceof TransferCancelledException, $path);
                    $this->assertTrue($elapsed < 10, $path . ' failed after ' . $elapsed . ' s');
                    $journal = $this->journal($server);
                    $refs = array_keys(array_filter($journal['uploads'], function ($upload) use ($path) {
                        return $upload['path'] === $path;
                    }));
                    $parts = array_filter($this->entries($journal, 'storage'), function ($entry) use ($refs) {
                        parse_str($entry['query'], $query);
                        return in_array($query['ref'], $refs, true);
                    });
                    $stalled = array_filter(array_merge($this->entries($journal, 'begin'), $this->entries($journal, 'end')), function ($entry) {
                        return !empty($entry['stalled']);
                    });
                    $this->assertSame($expected, [count($refs), count($parts), count(self::forPath($this->entries($journal, 'end'), $path))], $path);
                    $this->assertSame(
                        [1, 1, 0, 'failed'],
                        [count(self::forPath($this->entries($journal, 'begin'), $path)), count(self::forPath($stalled, $path)), $job->statistics()['retries'], $handle->status()],
                        $path
                    );
                }

                // A port nothing listens on.
                $listener = stream_socket_server('tcp://127.0.0.1:0');
                $address = stream_socket_get_name($listener, false);
                fclose($listener);
                Files::setBaseUrl('http://' . $address);
                $job = new TransferJob(['max_retries' => 1]);
                $handle = $job->uploadData('loop/nowhere.bin', self::content(4));
                $failure = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $failure = $e;
                }

                $this->assertNotNull($failure, 'An upload to a closed port completed');
                $this->assertSame([1, 'failed'], [$job->statistics()['retries'], $handle->status()]);
            });
        } finally {
            Files::$readTimeout = $readTimeout;
        }
    }

    // API requests take the job's copied connection and read timeouts, the read timeout also as their stall limit,
    // unless the transfer's own options give others; seen as the requests reach the transport.
    public function testApiRequestsTakeTheCopiedTimeoutsUnlessTheirOwnOptionsGiveOthers()
    {
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer(['upload' => ['partsize' => 4]], function ($server) {
            $seen = [];
            $inner = new CurlHandler();
            Files::setHandler(function ($request, array $options) use ($inner, &$seen) {
                $path = rawurldecode($request->getUri()->getPath());
                if (strpos($path, '/api/rest/v1/') === 0) {
                    $seen[basename($path)][] = [
                        isset($options['connect_timeout']) ? (float) $options['connect_timeout'] : null,
                        isset($options['read_timeout']) ? (float) $options['read_timeout'] : null,
                        isset($options['curl'][CURLOPT_LOW_SPEED_TIME]) ? $options['curl'][CURLOPT_LOW_SPEED_TIME] : null,
                    ];
                }

                return $inner($request, $options);
            });
            $job = new TransferJob();
            $job->uploadData('loop/own.bin', self::content(4), [], ['connect_timeout' => 2.5, 'read_timeout' => 7]);
            $job->uploadData('loop/copied.bin', self::content(4));
            $job->wait();

            $this->assertCount(2, $this->entries($this->journal($server), 'end'));
            $copied = [(float) Files::$connectTimeout, (float) Files::$readTimeout, max(1, (int) ceil((float) Files::$readTimeout))];
            foreach (['own.bin' => [2.5, 7.0, 7], 'copied.bin' => $copied] as $name => $expected) {
                $this->assertCount(2, $seen[$name], $name);
                foreach ($seen[$name] as $options) {
                    $this->assertSame($expected, $options, $name);
                }
            }
        });
    }

    // A custom handler answering with a lazy body, read only after its response: the reading (four reads of 0.1 s
    // here) is part of the download's elapsed time, and its rate counts only this call's bytes; to a caller's stream
    // with an 8-byte buffer, each range arrives whole and in order within that buffer. A body that goes on past the
    // length it declares (nine bytes for eight) is never accepted, for a file or a stream: each attempt is refused once
    // its ninth byte shows, the download fails when its retries are spent, and nothing is published.
    public function testALazyCustomBodyIsReadInTheDownloadsTimeAndAcceptedOnlyAtItsEnd()
    {
        if (!$this->adaptive()) {
            return;
        }

        $saved = Files::snapshot();
        $data = self::content(16);
        $destination = $this->localPath();
        try {
            Files::setBaseUrl('https://app.files.com');
            Files::setApiKey('test-key');
            $extra = '';
            $ranges = [];
            Files::setHandler(function ($request) use ($data, &$extra, &$ranges) {
                if ($request->getUri()->getHost() === 'app.files.com') {
                    $metadata = ['path' => 'lazy.bin', 'download_uri' => 'https://storage.example.test/lazy.bin'];
                    return Create::promiseFor(new Response(200, ['Content-Type' => 'application/json'], json_encode($metadata)));
                }

                $ranges[] = $request->getHeaderLine('Range');
                preg_match('/\Abytes=([0-9]+)-([0-9]+)\z/', $request->getHeaderLine('Range'), $match);
                $start = (int) $match[1];
                $end = min((int) $match[2], 15);
                $headers = ['Content-Range' => 'bytes ' . $start . '-' . $end . '/16', 'ETag' => '"v1"', 'Content-Length' => (string) ($end - $start + 1)];
                return Create::promiseFor(new Response(206, $headers, self::lazyBody((string) substr($data, $start, $end - $start + 1) . $extra)));
            });
            $reports = [];
            $job = new TransferJob(['download_part_bytes' => 8]);
            $job->downloadToFile('lazy.bin', $destination, ['progress' => function ($report) use (&$reports) {
                $reports[] = $report;
            }]);
            $job->wait();

            $this->assertSame($data, file_get_contents($destination));
            $this->assertCount(2, $reports);
            $this->assertTrue($reports[0]['elapsed_seconds'] >= 0.2, 'The first range reported ' . $reports[0]['elapsed_seconds'] . ' s');
            $this->assertTrue($reports[1]['elapsed_seconds'] >= 0.4, 'The download reported ' . $reports[1]['elapsed_seconds'] . ' s');
            $credited = $reports[1]['bytes_per_second'] * $reports[1]['elapsed_seconds'];
            $this->assertTrue(abs($credited - 16) < 0.001, 'The rate credits ' . $credited . ' bytes');

            $stream = fopen('php://temp', 'w+b');
            fwrite($stream, 'prefix:');
            $job = new TransferJob(['download_part_bytes' => 8, 'max_buffer_bytes' => 8]);
            $job->downloadToStream('lazy.bin', $stream);
            $job->wait();
            rewind($stream);
            $statistics = $job->statistics();
            $this->assertSame(['prefix:' . $data, 0], [stream_get_contents($stream), $statistics['reserved_bytes']]);
            $this->assertTrue($statistics['peak_reserved_bytes'] <= 8, 'peak ' . $statistics['peak_reserved_bytes']);
            fclose($stream);

            $extra = 'x';
            foreach (['file', 'stream'] as $kind) {
                $ranges = [];
                $job = new TransferJob(['download_part_bytes' => 8, 'max_buffer_bytes' => 8, 'max_retries' => 1]);
                if ($kind === 'file') {
                    $target = $this->localPath();
                    $handle = $job->downloadToFile('lazy.bin', $target);
                } else {
                    $target = fopen('php://temp', 'w+b');
                    fwrite($target, 'prefix:');
                    $handle = $job->downloadToStream('lazy.bin', $target);
                }

                $failure = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $failure = $e;
                }

                $this->assertInstanceOf(ApiBadResponseException::class, $failure, $kind);
                $statistics = $job->statistics();
                $this->assertSame([['bytes=0-7', 'bytes=0-7'], 'failed', 1, 0], [$ranges, $handle->status(), $statistics['retries'], $statistics['reserved_bytes']], $kind);
                if ($kind === 'file') {
                    $this->assertSame([false, []], [file_exists($target), $this->staging($target)]);
                } else {
                    rewind($target);
                    $this->assertSame('prefix:', stream_get_contents($target));
                    fclose($target);
                }
            }
        } finally {
            $this->restore($saved);
            @unlink($destination);
        }
    }

    // Through a handler set with Files::setHandler that answers with bodies of its own, writing nothing to the sink:
    // storage's whole, well-formed RequestTimeout error for a range is read from its body to classify it, and retried,
    // and the file arrives exactly; the same error naming a character XML does not allow (&#0;) is not retried.
    public function testACustomHandlersErrorBodyIsReadWholeToClassifyIt()
    {
        if (!$this->adaptive()) {
            return;
        }

        $saved = Files::snapshot();
        $data = self::content(16);
        $destinations = [$this->localPath(), $this->localPath()];
        try {
            Files::setBaseUrl('https://app.files.com');
            Files::setApiKey('test-key');
            $message = 'Idle';
            $ranges = [];
            Files::setHandler(function ($request) use ($data, &$message, &$ranges) {
                if ($request->getUri()->getHost() === 'app.files.com') {
                    $metadata = ['path' => 'timeout.bin', 'download_uri' => 'https://storage.example.test/timeout.bin'];
                    return Create::promiseFor(new Response(200, ['Content-Type' => 'application/json'], json_encode($metadata)));
                }

                $range = $request->getHeaderLine('Range');
                $ranges[] = $range;
                if ($range === 'bytes=8-15' && count(array_keys($ranges, $range, true)) === 1) {
                    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<Error><Code>RequestTimeout</Code><Message>' . $message . '</Message></Error>';
                    return Create::promiseFor(new Response(400, ['Content-Type' => 'application/xml'], $xml));
                }

                preg_match('/\Abytes=([0-9]+)-([0-9]+)\z/', $range, $match);
                $start = (int) $match[1];
                $end = min((int) $match[2], 15);
                $headers = ['Content-Range' => 'bytes ' . $start . '-' . $end . '/16', 'ETag' => '"v1"', 'Content-Length' => (string) ($end - $start + 1)];
                return Create::promiseFor(new Response(206, $headers, (string) substr($data, $start, $end - $start + 1)));
            });
            $job = new TransferJob(['download_part_bytes' => 8]);
            $job->downloadToFile('timeout.bin', $destinations[0]);
            $job->wait();
            $this->assertSame(
                [$data, ['bytes=0-7', 'bytes=8-15', 'bytes=8-15'], 1],
                [file_get_contents($destinations[0]), $ranges, $job->statistics()['retries']]
            );

            $message = '&#0;';
            $ranges = [];
            $job = new TransferJob(['download_part_bytes' => 8]);
            $handle = $job->downloadToFile('timeout.bin', $destinations[1]);
            $failure = null;
            try {
                $job->wait();
            } catch (\Exception $e) {
                $failure = $e;
            }

            $this->assertInstanceOf(ApiBadResponseException::class, $failure);
            $this->assertSame(
                [400, ['bytes=0-7', 'bytes=8-15'], 0, 'failed', false],
                [$failure->getCode(), $ranges, $job->statistics()['retries'], $handle->status(), file_exists($destinations[1])]
            );
        } finally {
            $this->restore($saved);
            foreach ($destinations as $destination) {
                @unlink($destination);
            }
        }
    }

    // Aggressive startup starts every upload at once but not downloads. The job's own opening decision
    // (TransferEngine::startable), with the controllers such a job has, at once and 0.6 s later: twelve queued downloads
    // start only as far as the paced limit (preparationLimit, under twelve) allows, as every transfer would without
    // Aggressive startup, while twelve queued uploads all start; one count covers both directions, so uploads queued
    // after downloads take only what those left. Through the real transport, twelve downloads and twelve uploads arrive
    // whole, the uploads all started before the first of them reports, and the job's request cap holds throughout.
    public function testAggressiveStartupStartsUploadsAtOnceButPacesDownloads()
    {
        if (!$this->adaptive()) {
            return;
        }

        $controllers = [
            'upload' => new ConcurrencyController(ConcurrencyController::storageUpload(4, 'aggressive'), 0.0),
            'download' => new ConcurrencyController(ConcurrencyController::download(4), 0.0),
        ];
        $memory = TransferEngine::preparationMemory(12);
        $downloads = array_fill(0, 12, 'download');
        $uploads = array_fill(0, 12, 'upload');
        foreach ([0.0, 0.6] as $now) {
            list($limit) = TransferEngine::preparationLimit($memory, $controllers, 12, 0, true, $now);
            $this->assertTrue($limit < 12, 'The paced limit is ' . $limit);
            foreach (['aggressive' => range(0, 11), 'default' => range(0, $limit - 1)] as $startup => $started) {
                $options = ['max_concurrent_files' => 12, 'startup' => $startup, 'concurrency' => null, 'initial_target' => null];
                $this->assertSame(range(0, $limit - 1), TransferEngine::startable($options, $memory, $controllers, 0, 0, $downloads, $now)[0], $startup . ' downloads at ' . $now . ' s');
                $this->assertSame($started, TransferEngine::startable($options, $memory, $controllers, 0, 0, $uploads, $now)[0], $startup . ' uploads at ' . $now . ' s');
            }

            $options = ['max_concurrent_files' => 12, 'startup' => 'aggressive', 'concurrency' => null, 'initial_target' => null];
            $both = array_merge($downloads, $uploads);
            $this->assertSame(array_merge(range(0, $limit - 1), range(12, 23 - $limit)), TransferEngine::startable($options, $memory, $controllers, 0, 0, $both, $now)[0], 'both at ' . $now . ' s');
        }

        $data = self::content(40);
        $this->withServer($this->downloadConfig($data, []), function ($server) use ($data) {
            foreach (['download', 'upload'] as $direction) {
                $created = microtime(true);
                $job = new TransferJob(['startup' => 'aggressive', 'max_concurrent_requests' => 4, 'download_part_bytes' => 8]);
                $handles = [];
                $first = null;
                $progress = function () use (&$handles, &$first, $created) {
                    if ($first !== null) {
                        return;
                    }

                    $started = 0;
                    foreach ($handles as $handle) {
                        $started += $handle->status() === 'queued' ? 0 : 1;
                    }

                    $first = [$started, microtime(true) - $created];
                };
                $destinations = [];
                for ($index = 0; $index < 12; $index++) {
                    if ($direction === 'download') {
                        $destinations[] = $this->localPath();
                        $handles[] = $job->downloadToFile('loop/d.bin', $destinations[$index], ['progress' => $progress]);
                    } else {
                        $handles[] = $job->uploadData('loop/many-' . $index . '.bin', self::content(4, (string) $index), [], ['progress' => $progress]);
                    }
                }

                $job->wait();
                foreach ($handles as $handle) {
                    $this->assertSame('completed', $handle->status(), $direction);
                }

                foreach ($destinations as $destination) {
                    $this->assertSame($data, file_get_contents($destination));
                    unlink($destination);
                }

                $this->assertTrue($job->statistics()['peak_in_flight'] <= 4, $direction);
                if ($direction === 'upload') {
                    $this->assertSame(12, $first[0], 'uploads started before the first report');
                }
            }
        });
    }

    // Whether this PHP runs adaptive transfers. Where it does not, checks that a job is refused, and the test ends.
    private function adaptive()
    {
        if (TransferJob::isSupported()) {
            return true;
        }

        try {
            new TransferJob();
            $this->fail('A TransferJob was made on a PHP that cannot run it');
        } catch (ConfigurationException $e) {
            $this->assertTrue(strpos($e->getMessage(), 'Adaptive transfers need') === 0, $e->getMessage());
        }

        return false;
    }

    // Runs $test with a loopback server configured with $config, and the SDK pointed at it with its own transport; the
    // SDK's configuration is restored and the server stopped afterwards.
    private function withServer(array $config, $test)
    {
        $server = $this->startServer();
        $saved = Files::snapshot();
        try {
            $this->control($server, 'reset', $config);
            Files::setBaseUrl('http://127.0.0.1:' . $server['ports'][0]);
            Files::setApiKey('loopback-key');
            Files::setSessionId(null);
            Files::setWorkspaceId(null);
            Files::setHandler(null);
            $test($server);
        } finally {
            $this->restore($saved);
            $this->stopServer($server);
        }
    }

    private function restore(array $saved)
    {
        Files::setBaseUrl($saved['base_url']);
        Files::setApiKey($saved['api_key']);
        Files::setSessionId($saved['session_id']);
        Files::setWorkspaceId($saved['workspace_id']);
        Files::setLanguage($saved['language']);
        Files::setHandler($saved['handler']);
    }

    private function startServer()
    {
        $script = __DIR__ . '/../../support/LoopbackServer.php';
        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script);
        $process = proc_open($command, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        $this->assertTrue(is_resource($process), 'The loopback server did not start');
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $line = '';
        $deadline = microtime(true) + 15;
        while (strpos($line, "\n") === false && microtime(true) < $deadline) {
            $read = [$pipes[1]];
            $write = null;
            $except = null;
            if (stream_select($read, $write, $except, 0, 200000) > 0) {
                $chunk = fread($pipes[1], 1024);
                if ($chunk === false || ($chunk === '' && feof($pipes[1]))) {
                    break;
                }

                $line .= $chunk;
            }
        }

        $started = json_decode(trim($line), true);
        $server = ['process' => $process, 'pipes' => $pipes, 'ports' => is_array($started) && isset($started['ports']) ? $started['ports'] : null];
        if ($server['ports'] === null) {
            $error = (string) stream_get_contents($pipes[2]);
            $this->stopServer($server);
            $this->fail('The loopback server did not start: ' . $line . $error);
        }

        return $server;
    }

    private function stopServer(array $server)
    {
        if (is_resource($server['pipes'][0])) {
            fclose($server['pipes'][0]);
        }

        $deadline = microtime(true) + 5;
        while (proc_get_status($server['process'])['running'] && microtime(true) < $deadline) {
            usleep(10000);
        }

        if (proc_get_status($server['process'])['running']) {
            proc_terminate($server['process']);
        }

        foreach ([1, 2] as $index) {
            if (is_resource($server['pipes'][$index])) {
                fclose($server['pipes'][$index]);
            }
        }

        proc_close($server['process']);
    }

    // A control request to the loopback server: reset (with a configuration), release or journal.
    private function control(array $server, $name, $body = null)
    {
        $client = new Client(['handler' => HandlerStack::create(new CurlHandler())]);
        $url = 'http://127.0.0.1:' . $server['ports'][0] . '/__control/' . $name;
        $response = $body === null ? $client->request('GET', $url) : $client->request('POST', $url, ['body' => json_encode($body)]);
        return json_decode((string) $response->getBody(), true);
    }

    private function journal(array $server)
    {
        return $this->control($server, 'journal');
    }

    // The journal's entries of one kind: storage (upload parts), download, begin (begin_upload), end (an upload's end)
    // or metadata (a download URL), in the order received; optionally only those carrying the API key $key.
    private function entries(array $journal, $kind, $key = null)
    {
        $entries = array_filter($journal['entries'], function ($entry) use ($kind, $key) {
            $path = $entry['path'];
            if ($key !== null && (!isset($entry['headers']['x-filesapi-key']) || $entry['headers']['x-filesapi-key'] !== $key)) {
                return false;
            }

            switch ($kind) {
                case 'storage':
                    return $path === '/storage/up';
                case 'download':
                    return $path === '/download';
                case 'begin':
                    return strpos($path, '/api/rest/v1/file_actions/begin_upload/') === 0;
                case 'end':
                    return $entry['method'] === 'POST' && strpos($path, '/api/rest/v1/files/') === 0;
                default:
                    return $entry['method'] === 'GET' && strpos($path, '/api/rest/v1/files/') === 0;
            }
        });
        return array_values($entries);
    }

    // The entries among $entries (begin_upload requests or upload ends) for the remote file $path.
    private static function forPath(array $entries, $path)
    {
        return array_values(array_filter($entries, function ($entry) use ($path) {
            $decoded = rawurldecode($entry['path']);
            return substr($decoded, -strlen('/' . $path)) === '/' . $path;
        }));
    }

    // The SHA-256 of the bytes storage kept for a part entry.
    private static function partSha(array $journal, array $entry)
    {
        return $entry['body_sha256'];
    }

    private function downloadConfig($data, array $change)
    {
        return array_replace_recursive(['download' => ['files' => ['loop/d.bin' => ['content' => base64_encode($data), 'etag' => '"v1"']]]], $change);
    }

    // The staging files a download to $destination left beside it.
    private function staging($destination)
    {
        $found = glob(dirname($destination) . DIRECTORY_SEPARATOR . '.' . basename($destination) . '.files-download-*');
        return $found === false ? [] : $found;
    }

    private function localFile($data)
    {
        $path = tempnam(sys_get_temp_dir(), 'files-adaptive');
        file_put_contents($path, $data);
        return $path;
    }

    // A path in the temporary folder where nothing is yet.
    private function localPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'files-adaptive-' . md5(uniqid('', true) . mt_rand());
    }

    // $length bytes that differ from part to part.
    private static function content($length, $seed = 'files')
    {
        $data = '';
        for ($block = 0; strlen($data) < $length; $block++) {
            $data .= md5($seed . $block, true);
        }

        return (string) substr($data, 0, $length);
    }

    // A response body that gives at most four bytes a read, each read taking 0.1 s, and whose size is not known.
    private static function lazyBody($bytes)
    {
        $position = 0;
        $length = strlen($bytes);
        $refuse = function () {
            throw new \RuntimeException('The lazy body is only read');
        };
        return new \GuzzleHttp\Psr7\FnStream([
            '__toString' => function () {
                return '';
            },
            'close' => function () {
            },
            'detach' => function () {
                return null;
            },
            'getSize' => function () {
                return null;
            },
            'tell' => function () use (&$position) {
                return $position;
            },
            'eof' => function () use (&$position, $length) {
                return $position >= $length;
            },
            'isSeekable' => function () {
                return false;
            },
            'seek' => $refuse,
            'rewind' => $refuse,
            'isWritable' => function () {
                return false;
            },
            'write' => $refuse,
            'isReadable' => function () {
                return true;
            },
            'read' => function ($count) use ($bytes, &$position) {
                usleep(100000);
                $chunk = (string) substr($bytes, $position, min(4, (int) $count));
                $position += strlen($chunk);
                return $chunk;
            },
            'getContents' => function () {
                return '';
            },
            'getMetadata' => function ($key = null) {
                return $key === null ? [] : null;
            },
        ]);
    }

    // A presigned UploadPart URL, signed at $date (now unless given).
    private static function s3Url($number, $uploadId, $date = null)
    {
        return 'https://bucket.s3.us-east-1.amazonaws.com/key.bin?partNumber=' . $number . '&uploadId=' . $uploadId
            . '&X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=AKIDEXAMPLE%2F20990101%2Fus-east-1%2Fs3%2Faws4_request'
            . '&X-Amz-Date=' . ($date === null ? gmdate('Ymd\THis\Z') : $date) . '&X-Amz-Expires=900&X-Amz-SignedHeaders=host&X-Amz-Signature=abc';
    }

    private static function s3Grant($number, $uploadId, $availableParts, $date = null)
    {
        return [
            'action' => 'put/write', 'http_method' => 'PUT', 'part_number' => $number, 'partsize' => 5 * self::MIB, 'ref' => 'ref-s3',
            'upload_uri' => self::s3Url($number, $uploadId, $date), 'headers' => [], 'parameters' => [],
            'send' => ['partsize' => 'required-header Content-Length', 'partdata' => 'body'], 'parallel_parts' => true, 'retry_parts' => true,
            'available_parts' => $availableParts, 'direct_connection_info' => null,
        ];
    }

    // The API and S3 the planning test runs against, as a Guzzle handler: begin_upload grants ordinary S3 parts (of
    // upload up-1 for part 1, and of $laterUpload for the later ones) signed at $date, S3 acknowledges each part, and the
    // end succeeds.
    private function s3Service(array &$requests, $laterUpload, $date)
    {
        return function ($request) use (&$requests, $laterUpload, $date) {
            $body = (string) $request->getBody();
            $host = $request->getUri()->getHost();
            $path = $request->getUri()->getPath();
            $kind = $host === 'bucket.s3.us-east-1.amazonaws.com' ? 'storage' : (strpos($path, '/file_actions/begin_upload/') !== false ? 'begin' : 'end');
            $json = json_decode($body, true);
            $requests[] = [
                'kind' => $kind, 'uri' => (string) $request->getUri(), 'bytes' => strlen($body), 'length' => $request->getHeaderLine('Content-Length'),
                'json' => $json,
            ];
            if ($kind === 'storage') {
                return Create::promiseFor(new Response(200, ['ETag' => '"s3-' . count($requests) . '"']));
            }

            if ($kind === 'end') {
                return Create::promiseFor(new Response(201, ['Content-Type' => 'application/json'], '{}'));
            }

            $grants = [];
            for ($number = $json['part']; $number < $json['part'] + $json['parts']; $number++) {
                $grant = self::s3Grant($number, $number === 1 ? 'up-1' : $laterUpload, 2, $date);
                $grant['headers'] = new \stdClass();
                $grant['parameters'] = new \stdClass();
                $grants[] = $grant;
            }

            return Create::promiseFor(new Response(200, ['Content-Type' => 'application/json'], json_encode($grants)));
        };
    }

    // A handler set with Files::setHandler that answers a request itself, here with a 307 to a port nothing listens on,
    // tells the job nothing of that transfer: it calls no on_stats or on_headers. The default redirect middleware follows
    // the 307, and the real cURL request there cannot connect, having sent nothing; but the handler that answered the 307
    // has the request. So a first begin_upload, or an upload's end, answered so is not sent again: each reaches that
    // handler once, its upload fails, and nothing more reaches the server.
    public function testARequestAnsweredBeforeItsRedirectCouldNotConnectIsNotSentAgain()
    {
        if (!$this->adaptive()) {
            return;
        }

        $this->withServer(['upload' => ['partsize' => 4]], function ($server) {
            $listener = stream_socket_server('tcp://127.0.0.1:0');
            $nowhere = 'http://' . stream_socket_get_name($listener, false);
            fclose($listener);
            $inner = new CurlHandler();
            $answered = 0;
            $answering = ['/api/rest/v1/file_actions/begin_upload/loop/redirected-begin.bin', '/api/rest/v1/files/loop/redirected-end.bin'];
            Files::setHandler(function ($request, array $options) use ($inner, $nowhere, $answering, &$answered) {
                if (strpos((string) $request->getUri(), $nowhere) === 0 || !in_array(rawurldecode($request->getUri()->getPath()), $answering, true)) {
                    return $inner($request, $options);
                }

                $answered++;
                return Create::promiseFor(new Response(307, ['Location' => $nowhere . $request->getUri()->getPath()]));
            });
            // [begin_upload requests, parts, ends] that reached the server for each path.
            foreach (['loop/redirected-begin.bin' => [0, 0, 0], 'loop/redirected-end.bin' => [1, 1, 0]] as $path => $expected) {
                $answered = 0;
                $job = new TransferJob(['max_retries' => 2]);
                $handle = $job->uploadData($path, self::content(4));
                $failure = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $failure = $e;
                }

                $journal = $this->journal($server);
                $refs = array_keys(array_filter($journal['uploads'], function ($upload) use ($path) {
                    return $upload['path'] === $path;
                }));
                $parts = array_filter($this->entries($journal, 'storage'), function ($entry) use ($refs) {
                    parse_str($entry['query'], $query);
                    return in_array($query['ref'], $refs, true);
                });
                $this->assertNotNull($failure, 'The upload to ' . $path . ' completed though its redirect led nowhere');
                $this->assertSame([1, 0, 'failed'], [$answered, $job->statistics()['retries'], $handle->status()], $path);
                $this->assertSame(
                    $expected,
                    [count(self::forPath($this->entries($journal, 'begin'), $path)), count($parts), count(self::forPath($this->entries($journal, 'end'), $path))],
                    $path
                );
            }
        });
    }

    // A caller's stream that takes only part of a write, as one whose buffer is full may (here three bytes of what it is
    // given, and nothing of the rest, so that fwrite returns three), and takes the rest when written again: the download
    // writes the rest from the exact byte where the stream stopped, every byte once and in order, within its 8-byte
    // buffer, both for ranges written as they complete and for a whole response held until it is published. A stream that
    // takes nothing of a write fails the download, which then holds nothing. Each stream opened is closed, with the
    // stream under it, however the test ends, and Guzzle's stream wrapper stays registered only if it was before.
    public function testACallersStreamThatTakesPartOfAWriteIsWrittenTheRest()
    {
        if (!$this->adaptive()) {
            return;
        }

        $data = self::content(40);
        $registered = in_array('guzzle', stream_get_wrappers(), true);
        $owned = [];
        try {
            $this->withServer([], function ($server) use ($data, &$owned) {
                // [how storage answers, the first writes the stream is offered and takes]: a range of 8 bytes is taken 3 of,
                // then its other 5; a whole response, written 4 bytes at a time, 3 of, then from its fourth byte on.
                $cases = [
                    'ranges' => [[], [[8, 3], [5, 0], [5, 5]]],
                    'whole' => [['download' => ['mode' => 'ignore_range']], [[4, 3], [1, 0], [4, 4]]],
                ];
                foreach ($cases as $case => list($change, $writes)) {
                    $this->control($server, 'reset', $this->downloadConfig($data, $change));
                    $fixture = self::shortWriteStream([3, 0], $owned);
                    $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8, 'max_buffer_bytes' => 8]);
                    $handle = $job->downloadToStream('loop/d.bin', $fixture->resource);
                    $job->wait();
                    fclose($fixture->resource);

                    $statistics = $job->statistics();
                    $this->assertSame(
                        [$data, 'completed', $writes, 0],
                        [$fixture->bytes, $handle->status(), array_slice($fixture->writes, 0, 3), $statistics['reserved_bytes']],
                        $case
                    );
                    $this->assertTrue($statistics['peak_reserved_bytes'] <= 8, $case . ': peak ' . $statistics['peak_reserved_bytes']);
                }

                $this->control($server, 'reset', $this->downloadConfig($data, []));
                $fixture = self::shortWriteStream([0], $owned);
                $job = new TransferJob(['max_concurrent_requests' => 4, 'download_part_bytes' => 8, 'max_buffer_bytes' => 8]);
                $handle = $job->downloadToStream('loop/d.bin', $fixture->resource);
                $failure = null;
                try {
                    $job->wait();
                } catch (\Exception $e) {
                    $failure = $e;
                }

                fclose($fixture->resource);
                $this->assertInstanceOf(InvalidParameterException::class, $failure);
                $this->assertSame(
                    ['', 'failed', [[8, 0]], 0],
                    [$fixture->bytes, $handle->status(), $fixture->writes, $job->statistics()['reserved_bytes']]
                );
            });
        } finally {
            foreach ($owned as $fixture) {
                if (is_resource($fixture->resource)) {
                    fclose($fixture->resource);
                }

                // The decorated stream's close is the backing stream's: closing the backing stream closes both.
                $fixture->backing->close();
            }

            if (!$registered && in_array('guzzle', stream_get_wrappers(), true)) {
                stream_wrapper_unregister('guzzle');
            }
        }
    }

    // A caller's stream for the test above: a real PHP stream resource, Guzzle's StreamWrapper over a stream whose
    // writes the test scripts. Each write PHP makes to it takes at most the next of $limits bytes of what it is
    // offered (all of them once $limits is empty) and returns that count; bytes keeps what it took, and writes each
    // offer and take. PHP's fwrite goes on writing while a write takes something, so the limits [3, 0] make an fwrite
    // of eight bytes return three, and the next fwrite is taken whole. The fixture joins $owned once it has anything
    // to close: its resource, and the temporary stream under it (backing), whose close is also the decorated stream's.
    private static function shortWriteStream(array $limits, array &$owned)
    {
        $fixture = new \stdClass();
        $fixture->limits = $limits;
        $fixture->bytes = '';
        $fixture->writes = [];
        $fixture->resource = null;
        $fixture->backing = new \GuzzleHttp\Psr7\Stream(fopen('php://temp', 'w+b'));
        $owned[] = $fixture;
        $decorated = \GuzzleHttp\Psr7\FnStream::decorate($fixture->backing, [
            'write' => function ($data) use ($fixture) {
                $taken = $fixture->limits ? min(strlen($data), array_shift($fixture->limits)) : strlen($data);
                $fixture->bytes .= (string) substr($data, 0, $taken);
                $fixture->writes[] = [strlen($data), $taken];
                return $taken;
            },
        ]);
        $fixture->resource = \GuzzleHttp\Psr7\StreamWrapper::getResource($decorated);
        return $fixture;
    }
}
