<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiConnectException;
use Files\Exception\ApiServerException;
use Files\Exception\ConfigurationException;
use Files\Exception\NotImplementedException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class StreamWrapperTest extends TestCase
{
    private $previous;
    private $files;
    private $requests;
    private $parts;
    private $storageResponse;
    private $failUpload = false;
    private $failFirstConnection = false;
    private $partRetryAllowed = true;
    private $redirectDownload = false;
    private $uploadThrowable;

    /** @before */
    public function initializeService()
    {
        $this->previous = [Files::getBaseUrl(), Files::getApiKey(), Files::getSessionId(), Files::getWorkspaceId(), Files::getHandler()];
        Files::setBaseUrl('https://api.example.test');
        Files::setApiKey('global-key');
        Files::setSessionId(null);
        Files::setWorkspaceId(null);
        $this->files = ['reports/data.bin' => "one\ntwo\nthree\0four"];
        $this->requests = [];
        $this->parts = [];
        $this->storageResponse = null;
        $this->failUpload = false;
        $this->failFirstConnection = false;
        $this->partRetryAllowed = true;
        $this->redirectDownload = false;
        $this->uploadThrowable = null;
        Files::setHandler($this->service());
        StreamWrapper::register();
        clearstatcache();
    }

    /** @after */
    public function restoreConfiguration()
    {
        StreamWrapper::unregister();
        list($base, $key, $session, $workspace, $handler) = $this->previous;
        Files::setBaseUrl($base);
        Files::setApiKey($key);
        Files::setSessionId($session);
        Files::setWorkspaceId($workspace);
        Files::setHandler($handler);
        clearstatcache();
    }

    public function testNativeReadsSeekStatAndSplUseVersionBoundRanges()
    {
        $expected = $this->files['reports/data.bin'];
        $stream = fopen('files://reports/data.bin', 'rb');
        try {
            $this->assertSame('one', fread($stream, 3));
            $this->assertSame(3, ftell($stream));
            $this->assertSame(0, fseek($stream, -4, SEEK_END));
            $this->assertSame('four', stream_get_contents($stream));
            $this->assertTrue(feof($stream));
            $this->assertSame(0, fseek($stream, 0));
            $this->assertSame($expected, stream_get_contents($stream));
            $this->assertSame(strlen($expected), fstat($stream)['size']);
        } finally {
            fclose($stream);
        }
        $spl = new \SplFileObject('files:///reports/data.bin', 'rb');
        $this->assertSame("one\n", $spl->fgets());
        $this->assertSame(strlen($expected), filesize('files://reports/data.bin'));
        $this->assertTrue(is_file('files://reports/data.bin'));
        $this->assertFalse(file_exists('files://missing'));
        $ranges = array_values(array_filter($this->requests, function ($request) {
            return $request['host'] === 'storage.example.test';
        }));
        $this->assertGreaterThanOrEqual(3, count($ranges));
        $this->assertSame(['"revision-1"'], $ranges[1]['headers']['If-Match']);
        foreach ($ranges as $request) {
            $headers = array_change_key_case($request['headers']);
            $this->assertArrayNotHasKey('x-filesapi-key', $headers);
            $this->assertArrayNotHasKey('x-filesapi-auth', $headers);
            $this->assertArrayNotHasKey('x-files-workspace-id', $headers);
        }
    }

    public function testNativeWritesUploadPartsWithoutPublishingUntilClose()
    {
        $context = stream_context_create(['files' => [
            'options' => ['api_key' => 'context-key', 'workspace_id' => 42],
            'upload_params' => ['custom_metadata' => ['kind' => 'native']],
        ]]);
        $stream = fopen('files://reports/new.bin', 'wb', false, $context);
        $this->assertSame(5, fwrite($stream, 'hello'));
        $this->assertSame(5, ftell($stream));
        $this->assertSame(5, fstat($stream)['size']);
        $this->assertTrue(fflush($stream));
        $this->assertArrayNotHasKey('reports/new.bin', $this->files);
        $this->assertSame(['hel'], array_values($this->parts));
        $this->assertSame(1, fwrite($stream, '!'));
        $this->assertTrue(fclose($stream));
        $this->assertSame('hello!', $this->files['reports/new.bin']);
        $this->assertSame(['hel', 'lo!', ''], array_values($this->parts));
        $end = null;
        foreach ($this->requests as $request) {
            $headers = array_change_key_case($request['headers']);
            if ($request['host'] === 'api.example.test') {
                $this->assertSame(['context-key'], $headers['x-filesapi-key']);
                $this->assertSame(['42'], $headers['x-files-workspace-id']);
            } else {
                $this->assertArrayNotHasKey('x-filesapi-key', $headers);
                $this->assertArrayNotHasKey('x-files-workspace-id', $headers);
            }
            if (isset($request['json']['action']) && $request['json']['action'] === 'end') {
                $end = $request['json'];
            }
        }
        $this->assertSame(['kind' => 'native'], $end['custom_metadata']);
        $this->assertSame(6, $end['size']);
        $this->assertSame('global-key', Files::getApiKey());
        $this->assertSame(0, file_put_contents('files://empty.bin', ''));
        $this->assertSame('', $this->files['empty.bin']);
    }

    public function testUnsafeNativeOperationsFailBeforeChangingTheRemoteFile()
    {
        foreach (['a', 'r+', 'w+', 'x', 'c'] as $mode) {
            try {
                fopen('files://reports/data.bin', $mode);
                $this->fail('Expected an unsupported mode error');
            } catch (NotImplementedException $error) {
                $this->assertNotSame('', $error->getMessage());
            }
        }
        try {
            unlink('files://reports/data.bin');
            $this->fail('Expected unsupported native unlink');
        } catch (NotImplementedException $error) {
            $this->assertNotSame('', $error->getMessage());
        }
        $this->assertSame([], $this->requests);
        $stream = fopen('files://reports/new.bin', 'wb');
        try {
            try {
                fseek($stream, 0);
                $this->fail('Expected an unsupported seek error');
            } catch (NotImplementedException $error) {
                $this->assertNotSame('', $error->getMessage());
            }
            try {
                flock($stream, LOCK_EX);
                $this->fail('Expected an unsupported lock error');
            } catch (NotImplementedException $error) {
                $this->assertNotSame('', $error->getMessage());
            }
        } finally {
            fclose($stream);
        }
    }

    public function testBadRangesNeverReturnBytesAndFailedUploadsNeverCommit()
    {
        $this->storageResponse = function ($response) {
            return $response->withStatus(200)->withoutHeader('Content-Range');
        };
        $stream = fopen('files://reports/data.bin', 'rb');
        try {
            try {
                fread($stream, 3);
                $this->fail('Expected an invalid range error');
            } catch (ApiBadResponseException $error) {
                $this->assertSame(0, ftell($stream));
            }
        } finally {
            fclose($stream);
        }
        $this->failUpload = true;
        $stream = fopen('files://failed.bin', 'wb');
        try {
            fwrite($stream, 'hello');
            $this->fail('Expected an upload error');
        } catch (ApiServerException $error) {
            $this->assertArrayNotHasKey('failed.bin', $this->files);
        } finally {
            fclose($stream);
        }
        $this->assertArrayNotHasKey('failed.bin', $this->files);
    }

    public function testChunkedRangesWithoutAnEtagRemainReadableAndCannotOverflowTheBuffer()
    {
        $this->storageResponse = function ($response) {
            return $response->withoutHeader('Content-Length')->withoutHeader('ETag')->withHeader('Transfer-Encoding', 'chunked');
        };
        $expected = $this->files['reports/data.bin'];
        $this->assertSame($expected, file_get_contents('files://reports/data.bin'));
        $this->storageResponse = function ($response) {
            return new Response(206, $response->withoutHeader('Content-Length')->getHeaders(), (string) $response->getBody() . 'extra');
        };
        $stream = fopen('files://reports/data.bin', 'rb');
        try {
            try {
                fread($stream, 3);
                $this->fail('Expected oversized range rejection');
            } catch (ApiBadResponseException $error) {
                $this->assertSame(0, ftell($stream));
            }
        } finally {
            fclose($stream);
        }
    }

    public function testInvalidDirectWriteBytesCannotPublishCoercedContents()
    {
        foreach ([42, false, null] as $bytes) {
            $this->files['invalid-bytes.bin'] = 'existing contents';
            $this->requests = [];
            $file = new NativeFile('invalid-bytes.bin', 'wb');
            $caught = null;
            try {
                $file->write($bytes);
            } catch (\Files\Exception\InvalidParameterException $error) {
                $caught = $error;
            } finally {
                $requestsBeforeClose = count($this->requests);
                $file->close();
            }
            $this->assertNotNull($caught);
            $this->assertSame(1, $requestsBeforeClose);
            $this->assertCount($requestsBeforeClose, $this->requests);
            $this->assertSame('existing contents', $this->files['invalid-bytes.bin']);
        }
    }

    public function testACaughtTransportErrorCannotPublishAnIncompleteNativeWrite()
    {
        if (PHP_VERSION_ID < 70000 || !function_exists('get_resources')) {
            $this->markTestSkipped('This regression requires catchable engine Errors and get_resources');
        }
        foreach ([new \Error('Transport failed after consuming its body'), new \TypeError('Transport callback failed')] as $thrown) {
            $this->files['error.bin'] = 'existing contents';
            $this->requests = [];
            $this->uploadThrowable = $thrown;
            $existingResources = get_resources('stream');
            $stream = fopen('files://error.bin', 'wb');
            $openedResources = array_diff_key(get_resources('stream'), $existingResources);
            $caught = null;
            try {
                fwrite($stream, 'hello');
            } catch (\Throwable $error) {
                $caught = $error;
            } finally {
                $requestsBeforeClose = count($this->requests);
                fclose($stream);
            }
            $this->assertSame($thrown, $caught);
            foreach ($openedResources as $resource) {
                $this->assertFalse(is_resource($resource), 'Closing the failed native stream must release its owned resources');
            }
            $this->assertCount($requestsBeforeClose, $this->requests, 'Close must not replay a failed part or complete the upload');
            $this->assertSame(['', 'hel'], array_map(function ($request) {
                return $request['host'] === 'storage.example.test' ? $request['body'] : '';
            }, $this->requests));
            $this->assertSame('existing contents', $this->files['error.bin']);
        }
    }

    public function testNativeReadsFollowRedirectsWithoutReturningTheRedirectBody()
    {
        $this->redirectDownload = true;
        $this->assertSame($this->files['reports/data.bin'], file_get_contents('files://reports/data.bin'));
        $storage = array_values(array_filter($this->requests, function ($request) {
            return $request['host'] === 'storage.example.test';
        }));
        $this->assertCount(2, $storage);
    }

    public function testNativePathsPreserveReservedCharactersLiterally()
    {
        foreach (['question?.txt', 'hash#.txt', 'literal%20.txt', 'name@host.txt', 'space name.txt', '0'] as $name) {
            $this->files[$name] = $name;
            $this->assertSame($name, file_get_contents('files://' . $name));
            $this->assertSame(strlen($name), filesize('files:///' . $name));
        }
        $this->assertSame(4, file_put_contents('files://0', 'zero'));
        $this->assertSame('zero', file_get_contents('files://0'));
    }

    public function testAConnectionRetryReplaysTheSamePartFromItsBeginning()
    {
        $previous = [Files::$maxNetworkRetries, Files::$minNetworkRetryDelay, Files::$maxNetworkRetryDelay];
        Files::$maxNetworkRetries = 1;
        Files::$minNetworkRetryDelay = 0;
        Files::$maxNetworkRetryDelay = 0;
        $this->failFirstConnection = true;
        try {
            $this->assertSame(5, file_put_contents('files://retry.bin', 'hello'));
            $this->assertSame('hello', $this->files['retry.bin']);
            $attempts = array_values(array_filter($this->requests, function ($request) {
                return $request['host'] === 'storage.example.test';
            }));
            $this->assertSame(['hel', 'hel', 'lo'], array_column($attempts, 'body'));
            $before = count($this->requests);
            $this->partRetryAllowed = false;
            $this->failFirstConnection = true;
            try {
                file_put_contents('files://not-retryable.bin', 'hello');
                $this->fail('Expected the one permitted attempt to fail');
            } catch (ApiConnectException $error) {
                $this->assertArrayNotHasKey('not-retryable.bin', $this->files);
            }
            $attempts = array_values(array_filter(array_slice($this->requests, $before), function ($request) {
                return $request['host'] === 'storage.example.test';
            }));
            $this->assertCount(1, $attempts);
        } finally {
            list(Files::$maxNetworkRetries, Files::$minNetworkRetryDelay, Files::$maxNetworkRetryDelay) = $previous;
        }
    }

    public function testOpenWriteHandlesKeepTheirAccountAndWorkspaceAndRejectAChangedSite()
    {
        Files::setWorkspaceId(10);
        $stream = fopen('files://bound.bin', 'wb');
        Files::setApiKey('other-key');
        Files::setWorkspaceId(20);
        fwrite($stream, 'hello');
        fclose($stream);
        foreach ($this->requests as $request) {
            if ($request['host'] === 'api.example.test') {
                $headers = array_change_key_case($request['headers']);
                $this->assertSame(['global-key'], $headers['x-filesapi-key']);
                $this->assertSame(['10'], $headers['x-files-workspace-id']);
            }
        }
        $stream = fopen('files://wrong-site.bin', 'wb');
        $before = count($this->requests);
        Files::setBaseUrl('https://another.example.test');
        try {
            fwrite($stream, 'hello');
            $this->fail('Expected changed-site rejection');
        } catch (ConfigurationException $error) {
            $this->assertSame($before, count($this->requests));
        } finally {
            fclose($stream);
        }
        $this->assertArrayNotHasKey('wrong-site.bin', $this->files);
        Files::setBaseUrl('https://api.example.test');
        $stream = fopen('files://reports/data.bin', 'rb');
        $before = count($this->requests);
        Files::setBaseUrl('https://another.example.test');
        try {
            fread($stream, 3);
            $this->fail('Expected changed-site rejection before a storage request');
        } catch (ConfigurationException $error) {
            $this->assertSame($before, count($this->requests));
        } finally {
            fclose($stream);
        }
    }

    public function testAChangedGlobalSessionCannotOverrideAnOpenApiKeyHandle()
    {
        $stream = fopen('files://bound-key.bin', 'wb');
        $before = count($this->requests);
        Files::setSessionId('other-session');
        try {
            fwrite($stream, 'hello');
            $this->fail('Expected changed-account rejection');
        } catch (ConfigurationException $error) {
            $this->assertSame($before, count($this->requests));
        } finally {
            fclose($stream);
        }
        $context = stream_context_create(['files' => ['options' => ['session_id' => 'explicit-session', 'workspace_id' => 30]]]);
        $this->requests = [];
        $stream = fopen('files://bound-session.bin', 'wb', false, $context);
        Files::setSessionId('another-session');
        Files::setApiKey('another-key');
        Files::setWorkspaceId(40);
        fwrite($stream, 'hello');
        fclose($stream);
        foreach ($this->requests as $request) {
            if ($request['host'] === 'api.example.test') {
                $headers = array_change_key_case($request['headers']);
                $this->assertSame(['explicit-session'], $headers['x-filesapi-auth']);
                $this->assertArrayNotHasKey('x-filesapi-key', $headers);
                $this->assertSame(['30'], $headers['x-files-workspace-id']);
            }
        }
    }

    private function service()
    {
        return function ($request, array $options) {
            $body = $request->getBody()->getContents();
            $json = json_decode($body, true);
            $uri = $request->getUri();
            $this->requests[] = ['host' => $uri->getHost(), 'headers' => $request->getHeaders(), 'json' => $json, 'body' => $body];
            $path = rawurldecode($uri->getPath());
            if ($uri->getHost() === 'storage.example.test' && $request->getMethod() === 'PUT') {
                if ($this->uploadThrowable !== null) {
                    $error = $this->uploadThrowable;
                    $this->uploadThrowable = null;
                    throw $error;
                }
                if ($this->failFirstConnection) {
                    $this->failFirstConnection = false;
                    return Create::rejectionFor(new ConnectException('Connection failed', $request));
                }
                if ($this->failUpload) {
                    return Create::promiseFor(new Response(500));
                }
                $number = (int) basename($path);
                $this->parts[$number] = $body;
                return Create::promiseFor(new Response(200, ['ETag' => '"part-' . $number . '"']));
            }
            if ($uri->getHost() === 'storage.example.test') {
                if (strpos($path, '/redirect/') === 0) {
                    $response = new Response(302, ['Location' => 'https://storage.example.test/' . rawurlencode(substr($path, strlen('/redirect/')))], str_repeat('redirect body', 10));
                    call_user_func($options['on_headers'], $response);
                    $options['sink']->write((string) $response->getBody());
                    return Create::promiseFor($response);
                }
                $data = $this->files[substr($path, 1)];
                preg_match('/bytes=(\d+)-(\d+)/', $request->getHeaderLine('Range'), $range);
                $start = (int) $range[1];
                $end = (int) $range[2];
                $bytes = substr($data, $start, $end - $start + 1);
                $response = new Response(206, ['Content-Range' => 'bytes ' . $start . '-' . $end . '/' . strlen($data), 'Content-Length' => (string) strlen($bytes), 'ETag' => '"revision-1"'], $bytes);
                if ($this->storageResponse) {
                    $response = call_user_func($this->storageResponse, $response);
                }
                if (isset($options['on_headers'])) {
                    call_user_func($options['on_headers'], $response);
                }
                $options['sink']->write((string) $response->getBody());
                return Create::promiseFor($response);
            }
            if (strpos($path, '/file_actions/begin_upload/') !== false) {
                if (!isset($json['ref'])) {
                    $this->parts = [];
                }
                return Create::promiseFor(new Response(200, [], json_encode([[
                    'part_number' => $json['part'], 'partsize' => 3, 'ref' => 'upload-ref',
                    'upload_uri' => 'https://storage.example.test/upload/' . $json['part'], 'http_method' => 'PUT',
                    'retry_parts' => $this->partRetryAllowed,
                ]])));
            }
            $prefix = strpos($path, '/file_actions/metadata/') !== false ? '/api/rest/v1/file_actions/metadata/' : '/api/rest/v1/files/';
            $name = ltrim(substr($path, strlen($prefix)), '/');
            if ($request->getMethod() === 'POST') {
                $this->files[$name] = implode('', $this->parts);
                return Create::promiseFor(new Response(201, [], '{}'));
            }
            if (!array_key_exists($name, $this->files)) {
                return Create::promiseFor(new Response(404, [], '{"type":"not-found","error":"missing","http-code":404}'));
            }
            $metadata = ['path' => $name, 'type' => 'file', 'size' => strlen($this->files[$name]), 'permissions' => 'rwd', 'mtime' => '2026-10-28T12:00:00Z'];
            if (strpos($path, '/file_actions/metadata/') === false) {
                $metadata['download_uri'] = 'https://storage.example.test/' . ($this->redirectDownload ? 'redirect/' : '') . rawurlencode($name);
            }
            return Create::promiseFor(new Response(200, [], json_encode($metadata)));
        };
    }
}
