<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiConnectException;
use Files\Exception\ApiRequestException;
use Files\Exception\ApiServerException;
use Files\Exception\FilesException;
use Files\Exception\NotAuthenticated\LockoutRegionMismatchException;
use Files\Exception\NotFoundException;
use Files\Model\File;
use Files\Test\UnboundTransportGuard;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Handler\StreamHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses,PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- The
// close-failing stream wrapper at the end belongs to these tests alone, and PHP names its callbacks.

/**
 * Runs the File download methods against a loopback server (test/fixtures/file-transfer-server.php) with the SDK's
 * default cURL handler and real files. A caught exception stays referenced, with exception arguments enabled: its
 * trace keeps its calls' arguments, so a file the SDK left open would still be open when the test looks.
 */
class FileTransferTest extends TestCase
{
    use UnboundTransportGuard;

    const NOT_FOUND = '{"type":"not-found","http-code":404,"error":"Not Found.  This may be related to your permissions."}';
    const DENIED = '<Error><Code>AccessDenied</Code><Message>Access Denied</Message></Error>';
    const EXPIRED = '<Error><Code>AccessDenied</Code><Message>Request has expired</Message></Error>';

    private $directory;

    public function failedAndRedirectedDownloads()
    {
        $metadata = 'GET /api/rest/v1/files/';

        return [
            'a 404, its error read from the file' => [
                'not-found', 'prior contents', NotFoundException::class,
                [$metadata . 'not-found', 'GET /download/not-found'], self::NOT_FOUND,
            ],
            'a response cut off' => [
                'truncated', null, ApiRequestException::class,
                [$metadata . 'truncated', 'GET /download/truncated'], substr(self::payload(), 0, 4096),
            ],
            'a refused connection, over a file' => [
                'connection-refused', 'prior contents', ApiConnectException::class,
                [$metadata . 'connection-refused'], 'prior contents',
            ],
            'a refused connection' => [
                'connection-refused', null, ApiConnectException::class, [$metadata . 'connection-refused'], null,
            ],
            'a metadata error, over a file' => [
                'metadata-error', 'prior contents', ApiServerException::class,
                [$metadata . 'metadata-error'], 'prior contents',
            ],
            // The redirect's own body reaches the file first; the empty final response starts a new file.
            'a redirect to an empty body, over a file' => [
                'redirect-to-empty', 'prior contents', null,
                [$metadata . 'redirect-to-empty', 'GET /download/redirect-to-empty', 'GET /download/empty'], '',
            ],
        ];
    }

    /**
     * A download to a path leaves no file open once it returns or throws, while the caller keeps the exception. A 404's
     * error is read from the file first; a request that gets no response leaves the destination as it was, and nothing
     * is created or truncated only to be closed.
     *
     * @dataProvider failedAndRedirectedDownloads
     */
    public function testADownloadToAPathLeavesNoFileOpen($remotePath, $prior, $expectedError, $expectedRequests, $expected)
    {
        $this->withServer(function () use ($remotePath, $prior, $expectedError, $expectedRequests, $expected) {
            $destination = $this->fixtureFile('destination.bin', $prior);

            $error = $this->attempt(function () use ($remotePath, $destination) {
                File::downloadToFile($remotePath, $destination, false);
            });

            $this->assertSame($expectedError, $error ? get_class($error) : null);
            if ($error instanceof NotFoundException) {
                $this->assertSame(
                    ['not-found', 404, 'Not Found.  This may be related to your permissions.'],
                    [$error->getType(), $error->getHttpCode(), $error->getError()]
                );
            }
            $this->assertSame($expectedRequests, array_column($this->requests(), 'line'));
            $this->assertSame($expected, self::contents($destination));
            if ($error) {
                $this->assertKeepsCallArguments($error);
            }
            $this->assertNoHandleOpenOn($destination);
        });
    }

    public function expiredDownloads()
    {
        // The metadata and download requests of a download asked for $times times.
        $requests = function ($remotePath, $times) {
            return array_merge(...array_fill(0, $times, ['GET /api/rest/v1/files/' . $remotePath, 'GET /download/' . $remotePath]));
        };

        return [
            'renewed, the same download asked for again' => ['expired-1-full', true, 1, null, $requests('expired-1-full', 2), self::payload()],
            'not renewed with retries turned off' => ['expired-1-full', false, 1, ApiBadResponseException::class, $requests('expired-1-full', 1), 'prior contents'],
            'not renewed without a retry left' => ['expired-1-full', true, 0, ApiBadResponseException::class, $requests('expired-1-full', 1), 'prior contents'],
            'renewed while retries are left' => ['expired-9-full', true, 3, ApiBadResponseException::class, $requests('expired-9-full', 4), 'prior contents'],
            'renewed at most three times, whatever retries are left' => ['expired-9-full', true, 5, ApiBadResponseException::class, $requests('expired-9-full', 4), 'prior contents'],
            'renewed, then not retried from the bytes written past the retries it shares' => [
                'expired-1-truncated', true, 1, ApiRequestException::class, $requests('expired-1-truncated', 2), substr(self::payload(), 0, 4096),
            ],
            'another denial, its body written as before' => ['denied', false, 1, ApiBadResponseException::class, $requests('denied', 1), self::DENIED],
            // The renewal's own lookup fails: retried from the first byte, never after the destination's earlier
            // contents, which no response has written.
            'retried from its first byte when its renewal fails before any of it is written' => [
                'flaky2-expired-1-full', true, 3, null,
                ['GET /api/rest/v1/files/flaky2-expired-1-full', 'GET /download/expired-1-full', 'GET /api/rest/v1/files/flaky2-expired-1-full',
                    'GET /api/rest/v1/files/flaky2-expired-1-full', 'GET /download/expired-1-full'],
                self::payload(),
            ],
            // A body cut short, then an expired URL whose renewal fails: resumed after the bytes it wrote.
            'resumed after the bytes it wrote when a later renewal fails' => [
                'flaky3-steps-truncated.expired.full', true, 3, null,
                ['GET /api/rest/v1/files/flaky3-steps-truncated.expired.full', 'GET /download/steps-truncated.expired.full',
                    'GET /api/rest/v1/files/flaky3-steps-truncated.expired.full', 'GET /download/steps-truncated.expired.full',
                    'GET /api/rest/v1/files/flaky3-steps-truncated.expired.full', 'GET /api/rest/v1/files/flaky3-steps-truncated.expired.full',
                    'GET /download/steps-truncated.expired.full'],
                self::payload(), [null, 'bytes=4096-', 'bytes=4096-'],
            ],
        ];
    }

    /**
     * Storage's word that a download's URL expired renews it by the file's path, and none of that refusal reaches the
     * destination, even when no renewal is left; another refusal's body is written there as before. The download's
     * renewals and retries share Files::$maxNetworkRetries.
     *
     * @dataProvider expiredDownloads
     */
    public function testAnExpiredDownloadUrlIsRenewedWithoutItsRefusalReachingTheFile($remotePath, $enableRetry, $retries, $expectedError, $expectedRequests, $expected, $ranges = null)
    {
        $this->withServer(function () use ($remotePath, $enableRetry, $retries, $expectedError, $expectedRequests, $expected, $ranges) {
            Files::$maxNetworkRetries = $retries;
            $destination = $this->fixtureFile('destination.bin', 'prior contents');

            $error = $this->attempt(function () use ($remotePath, $destination, $enableRetry) {
                File::downloadToFile($remotePath, $destination, $enableRetry);
            });

            $this->assertSame($expectedError, $error ? get_class($error) : null);
            if ($error instanceof ApiBadResponseException) {
                $this->assertSame(403, $error->getCode());
            }
            $this->assertSame($expectedRequests, array_column($this->requests(), 'line'));
            // Unless a row says otherwise, no download resumes after the destination's earlier contents.
            $downloads = array_values(array_filter($this->requests(), function ($request) {
                return strpos($request['line'], 'GET /download/') === 0;
            }));
            $this->assertSame($ranges === null ? array_fill(0, count($downloads), null) : $ranges, array_column($downloads, 'range'));
            $this->assertSame($expected, self::contents($destination));
            $this->assertNoHandleOpenOn($destination);
        });
    }

    // downloadToStream renews an expired URL into a caller's stream or stream resource, kept open across both requests,
    // asking for the same range again, and writes that range alone.
    public function testAnExpiredDownloadIntoACallersStreamAsksForTheSameRangeAgain()
    {
        $this->withServer(function () {
            $path = $this->fixtureFile('resource.bin', '');
            $stream = new Stream(fopen('php://temp', 'w+'));
            $resource = fopen($path, 'w');
            File::downloadToStream('expired-1-stream', $stream, 100, 199);
            File::downloadToStream('expired-1-resource', $resource, 100, 199);
            if (is_resource($resource)) {
                fclose($resource);
            }

            $range = substr(self::payload(), 100, 100);
            $this->assertSame([$range, $range], [(string) $stream, self::contents($path)]);
            $downloads = array_values(array_filter($this->requests(), function ($request) {
                return strpos($request['line'], 'GET /download/') === 0;
            }));
            $this->assertSame(array_fill(0, 4, 'bytes=100-199'), array_column($downloads, 'range'));
            $this->assertCount(8, $this->requests());
        });
    }

    // A handler of the caller's that writes the sink without giving its status, or that answers with a body of its own,
    // giving its status or not, has storage's word that the URL expired judged once it answers, even from a body that
    // fails after its start: the URL is renewed and nothing of the refusal reaches the destination, which ends as the
    // handler's transfers would leave it without one. A caller's stream resource the handler never writes stays open.
    public function testACustomHandlersExpiredDownloadIsRenewedWithoutReachingTheDestination()
    {
        $this->withConfiguration(function () {
            Files::setBaseUrl('https://app.files.com');
            // Whether the handler writes the sink, gives the status to on_headers, and answers with a body that fails
            // after its start; and what the destination holds after.
            $cases = [
                'writing the sink without its status' => [true, false, false, 'bytes'],
                'writing the sink after its status' => [true, true, false, 'bytes'],
                'keeping its own body' => [false, false, false, 'prior contents'],
                'keeping its own body, after its status' => [false, true, false, 'prior contents'],
                'keeping its own body, which fails after its start' => [false, false, true, 'prior contents'],
            ];
            foreach ($cases as $case => list($writes, $status, $failing, $expected)) {
                $destination = $this->fixtureFile('destination.bin', 'prior contents');
                $hosts = [];
                Files::setHandler(self::expiringHandler($hosts, $writes, $status, $failing));

                // downloadToStream retries nothing but a renewal: downloadToFile would retry any failure.
                $error = $this->attempt(function () use ($destination) {
                    File::downloadToStream('remote.bin', $destination);
                });

                $this->assertNull($error, $case);
                $this->assertSame(['app.files.com', 'storage.example.test', 'app.files.com', 'storage.example.test'], $hosts, $case);
                $this->assertSame($expected, self::contents($destination), $case);
            }

            $hosts = [];
            Files::setHandler(self::expiringHandler($hosts, false, true, false));
            $resource = fopen($this->fixtureFile('resource.bin', ''), 'w+');
            $this->borrow($resource);
            gc_collect_cycles();
            $this->assertTrue(is_resource($resource), 'The caller still owns its stream resource');
            $this->assertSame(['app.files.com', 'storage.example.test', 'app.files.com', 'storage.example.test'], $hosts);
            fclose($resource);
            $this->assertNoHandleOpenOn($destination);
        });
    }

    // A storage response with a body as long as its Content-Length says, and 10 bytes shorter, which a handler such as
    // Guzzle's StreamHandler ends normally: storage's text that the URL expired, in part of its body, renews it, but
    // Files.com JSON, which counts only whole, does not, through cURL as through StreamHandler.
    public function testAShortExpiryBodyRenewsOnlyByStoragesText()
    {
        $this->withServer(function () {
            foreach (['cURL' => null, 'StreamHandler' => new StreamHandler()] as $handler => $transport) {
                Files::setHandler($transport);
                foreach (['shortxml' => [2, 'abcd'], 'shortjson' => [1, null]] as $short => list($downloads, $expected)) {
                    $remotePath = $short . '-1-' . strtolower($handler);
                    $sink = new Stream(fopen('php://temp', 'w+'));
                    $before = count($this->requests());
                    $error = $this->attempt(function () use ($remotePath, $sink) {
                        File::downloadToStream($remotePath, $sink, 0, 3);
                    });

                    $lines = array_column(array_slice($this->requests(), $before), 'line');
                    $this->assertSame($downloads, count(array_keys($lines, 'GET /download/' . $remotePath, true)), "$handler $short");
                    $this->assertSame($expected === null, $error !== null, "$handler $short");
                    if ($expected !== null) {
                        $this->assertSame(substr(self::payload(), 0, 4), (string) $sink, "$handler $short");
                    }
                }
            }
        });
    }

    // downloadToFile resumes a download cut off part way from the bytes already written, and ends with every byte.
    public function testDownloadToFileResumesFromTheBytesAlreadyWritten()
    {
        $this->withServer(function () {
            $payload = self::payload();
            $destination = $this->fixtureFile('destination.bin', null);

            $result = File::downloadToFile('truncated-once', $destination);

            $requests = $this->requests();
            $this->assertSame([
                'GET /api/rest/v1/files/truncated-once',
                'GET /download/truncated',
                'GET /api/rest/v1/files/truncated-once',
                'GET /download/full',
            ], array_column($requests, 'line'));
            $this->assertSame('bytes=4096-', $requests[3]['range']);
            $this->assertSame($payload, self::contents($destination));
            $this->assertSame([strlen($payload) - 4096, strlen($payload)], [$result->received, $result->total]);
            $this->assertNoHandleOpenOn($destination);
        });
    }

    // A destination whose close fails, as a stream wrapper's can, never replaces a download's own failure, nor passes
    // for success: a download that succeeds reports the failed close. This holds for the SDK's own file for a path and
    // for resumeDownloadToFile's append handle.
    public function testAFailedCloseNeverReplacesTheDownloadsOwnFailure()
    {
        $this->withServer(function () {
            $this->assertTrue(stream_wrapper_register('closefail', CloseFailingFileStream::class));
            try {
                $path = $this->fixtureFile('destination.bin', 'prior contents');
                $destination = 'closefail://' . $path;
                $download = function ($remotePath) use ($destination) {
                    return $this->attempt(function () use ($remotePath, $destination) {
                        File::downloadToFile($remotePath, $destination, false);
                    });
                };
                $resume = function () use ($destination) {
                    return $this->attempt(function () use ($destination) {
                        File::resumeDownloadToFile('remote.bin', $destination);
                    });
                };

                $error = $download('not-found');
                $this->assertSame(NotFoundException::class, $error ? get_class($error) : null);
                $this->assertSame(self::NOT_FOUND, self::contents($path));

                $error = $download('full');
                $this->assertSame([\RuntimeException::class, CloseFailingFileStream::FAILURE], self::described($error));
                $this->assertSame(self::payload(), self::contents($path));

                $transferFailure = new FilesException('Fixture transfer failure');
                Files::setHandler(new MockHandler([self::downloadMetadata(), $transferFailure]));
                $this->assertSame($transferFailure, $resume());

                Files::setHandler(new MockHandler([
                    self::downloadMetadata(),
                    new Response(206, ['Content-Length' => '3', 'Content-Range' => 'bytes 21504-21506/21507'], 'xyz'),
                ]));
                $error = $resume();
                $this->assertSame([\RuntimeException::class, CloseFailingFileStream::FAILURE], self::described($error));
                $this->assertSame(self::payload() . 'xyz', self::contents($path));
            } finally {
                stream_wrapper_unregister('closefail');
            }
        });
    }

    // A response from a handler set through Files::setHandler() is the handler's: the SDK neither closes its body nor
    // opens a lazy body only to close it, and the destination path stays as it was. A handler given with the request
    // itself sends it instead of the SDK's own, so the path stays Guzzle's, a new file for each of that handler's
    // transfers.
    public function testAHandlersResponseBodiesAreLeftToIt()
    {
        $this->withServer(function () {
            $destination = $this->fixtureFile('destination.bin', 'prior contents');
            $download = function () use ($destination) {
                Api::sendRequest('https://storage.example.test/download', 'GET', null, ['sink' => $destination]);
            };

            $body = new Stream(fopen('php://temp', 'w+'));
            $body->write(self::NOT_FOUND);
            Files::setHandler(function ($request, array $options) use ($body) {
                return new FulfilledPromise(new Response(404, ['Content-Type' => 'application/json'], $body));
            });
            $error = $this->attempt($download);
            $this->assertSame(NotFoundException::class, $error ? get_class($error) : null);
            $this->assertTrue($body->isReadable(), 'The handler still owns its response body');
            $this->assertSame(self::NOT_FOUND, (string) $body);
            $this->assertSame('prior contents', self::contents($destination));

            $lazyBody = new LazyOpenStream($destination, 'w+');
            Files::setHandler(function ($request, array $options) use ($lazyBody) {
                return new RejectedPromise(
                    new RequestException('cURL error 18: transfer closed', $request, new Response(200, [], $lazyBody))
                );
            });
            $error = $this->attempt($download);
            $this->assertSame(ApiRequestException::class, $error ? get_class($error) : null);
            $this->assertSame('prior contents', self::contents($destination));

            // The request's own retrying handler, over the real cURL transport, asks once more after the first answer;
            // that longer first answer leaves none of its bytes behind the shorter final one.
            Files::setHandler(null);
            $retrying = new HandlerStack(new CurlHandler());
            $retrying->push(Middleware::retry(function ($retries) {
                return $retries === 0;
            }, function () {
                return 0;
            }));
            Api::sendRequest(
                Files::getBaseUrl() . '/api/rest/v1/files/truncated-once',
                'GET',
                null,
                ['sink' => $destination, 'handler' => $retrying]
            );
            $port = parse_url(Files::getBaseUrl(), PHP_URL_PORT);
            $this->assertSame(
                ['GET /api/rest/v1/files/truncated-once', 'GET /api/rest/v1/files/truncated-once'],
                array_column($this->requests(), 'line')
            );
            $this->assertSame(
                json_encode(['path' => 'truncated-once', 'download_uri' => "http://127.0.0.1:$port/download/full"]),
                self::contents($destination)
            );
        });
    }

    // The SDK's own file for a path does not keep an API request from consulting the region policy, as a path sink
    // never did, while a caller's own stream still does: a response may already have written to it.
    public function testAnOwnedPathStillConsultsTheRegionPolicyButACallerStreamDoesNot()
    {
        $this->withServer(function () {
            $consulted = [];
            $policy = function ($target, $current) use (&$consulted) {
                $consulted[] = [$target, $current];
                return false;
            };
            $destination = $this->fixtureFile('destination.bin', null);
            $callerStream = new Stream(fopen('php://temp', 'w+'));
            $sinks = [
                'an owned path' => [$destination, [['https://other.example.test', Files::getBaseUrl()]]],
                'a caller stream' => [$callerStream, []],
            ];

            $errors = [];
            foreach ($sinks as $case => list($sink, $expectedConsultations)) {
                $consulted = [];
                $errors[$case] = $this->attempt(function () use ($sink, $policy) {
                    Api::sendRequest('/region', 'GET', null, ['sink' => $sink, 'region_relocation' => $policy]);
                });

                $error = $errors[$case];
                $this->assertSame(LockoutRegionMismatchException::class, $error ? get_class($error) : null, $case);
                $this->assertSame($expectedConsultations, $consulted, $case);
            }

            $this->assertSame(['GET /api/rest/v1/region', 'GET /api/rest/v1/region'], array_column($this->requests(), 'line'));
            $this->assertTrue($callerStream->isWritable(), 'The caller still owns its stream');
            $this->assertNotFalse(strpos((string) $callerStream, 'lockout-region-mismatch'));
            $this->assertNotFalse(strpos((string) self::contents($destination), 'lockout-region-mismatch'));
            $this->assertKeepsCallArguments($errors['an owned path']);
            $this->assertNoHandleOpenOn($destination);
        });
    }

    // Runs $test against the loopback server, as withConfiguration does.
    private function withServer($test)
    {
        $this->withConfiguration(function () use ($test) {
            $port = self::unusedPort();
            $server = proc_open(
                'exec ' . escapeshellarg(PHP_BINARY) . " -S 127.0.0.1:$port "
                    . escapeshellarg(__DIR__ . '/../../fixtures/file-transfer-server.php'),
                [
                    ['file', '/dev/null', 'r'],
                    ['file', "{$this->directory}/server.log", 'a'],
                    ['file', "{$this->directory}/server.log", 'a'],
                ],
                $pipes,
                null,
                [
                    'FILES_TEST_STATE_DIR' => $this->directory,
                    'FILES_TEST_CLOSED_PORT' => (string) self::unusedPort(),
                ]
            );

            try {
                $this->waitForServer($port);
                Files::setBaseUrl("http://127.0.0.1:$port");
                call_user_func($test);
            } finally {
                proc_terminate($server);
                proc_close($server);
            }
        });
    }

    // Runs $test with a new directory for its files, exception arguments kept, the SDK's default handler, one network
    // retry without delay, and the SDK configuration it changes restored afterwards.
    private function withConfiguration($test)
    {
        $this->directory = tempnam(sys_get_temp_dir(), 'files-transfer-test-');
        unlink($this->directory);
        mkdir($this->directory);
        $this->directory = realpath($this->directory);
        $snapshot = Files::snapshot();
        // PHP before 7.4 always keeps exception arguments.
        $ignoreArguments = ini_set('zend.exception_ignore_args', '0');

        try {
            Files::setApiKey('local-fixture-only');
            Files::setHandler(null);
            Files::$maxNetworkRetries = 1;
            Files::$minNetworkRetryDelay = 0;
            Files::$maxNetworkRetryDelay = 0;
            Files::$connectTimeout = 1;

            call_user_func($test);
        } finally {
            if ($ignoreArguments !== false) {
                ini_set('zend.exception_ignore_args', $ignoreArguments);
            }
            Files::setBaseUrl($snapshot['base_url']);
            Files::setApiKey($snapshot['api_key']);
            Files::setHandler($snapshot['handler']);
            Files::$maxNetworkRetries = $snapshot['max_network_retries'];
            Files::$minNetworkRetryDelay = $snapshot['min_network_retry_delay'];
            Files::$maxNetworkRetryDelay = $snapshot['max_network_retry_delay'];
            Files::$connectTimeout = $snapshot['connect_timeout'];
            array_map('unlink', glob("{$this->directory}/*"));
            rmdir($this->directory);
        }
    }

    private function waitForServer($port)
    {
        for ($attempt = 0; $attempt < 250; $attempt++) {
            $connection = @fsockopen('127.0.0.1', $port, $errorCode, $errorMessage, 0.1);
            if ($connection) {
                fclose($connection);
                return;
            }
            usleep(20000);
        }

        throw new \RuntimeException('Fixture server did not start: ' . self::contents("{$this->directory}/server.log"));
    }

    // The requests the fixture server received, in order.
    private function requests()
    {
        $log = self::contents("{$this->directory}/requests.jsonl");

        return $log === null ? [] : array_map(function ($line) {
            return json_decode($line, true);
        }, explode("\n", trim($log)));
    }

    private function attempt($transfer)
    {
        try {
            call_user_func($transfer);
        } catch (\Exception $error) {
            return $error;
        }

        return null;
    }

    private function fixtureFile($name, $contents)
    {
        $path = "{$this->directory}/$name";
        if ($contents !== null) {
            file_put_contents($path, $contents);
        }

        return $path;
    }

    // Fails while this process has a handle open on $path. Runs last in a test, since it skips the test where PHP cannot
    // list its handles (before PHP 7).
    private function assertNoHandleOpenOn($path)
    {
        if (!function_exists('get_resources')) {
            $this->markTestSkipped('Listing open handles needs get_resources (PHP 7)');
        }

        $open = array_filter(get_resources('stream'), function ($stream) use ($path) {
            $meta = stream_get_meta_data($stream);
            return isset($meta['uri']) && $meta['uri'] === $path;
        });
        $this->assertCount(0, $open, "A handle on $path is still open");
    }

    private function assertKeepsCallArguments(\Exception $error)
    {
        $this->assertNotEmpty(array_filter(array_column($error->getTrace(), 'args')), 'Exception arguments are kept');
    }

    private static function described($error)
    {
        return $error ? [get_class($error), $error->getMessage()] : null;
    }

    // A handler for downloads of remote.bin from storage.example.test whose first storage answer is S3's expired-URL
    // refusal, and whose next is 5 bytes. It writes each body to the sink when $writes, gives each response to
    // on_headers when $status, and answers the refusal with a body that fails after its start when $failing. $hosts gets
    // each request's host.
    private static function expiringHandler(array &$hosts, $writes, $status, $failing)
    {
        return function ($request, array $options) use (&$hosts, $writes, $status, $failing) {
            $hosts[] = $request->getUri()->getHost();
            if ($request->getUri()->getHost() !== 'storage.example.test') {
                return new FulfilledPromise(self::downloadMetadata());
            }

            $refused = count(array_keys($hosts, 'storage.example.test', true)) === 1;
            $body = $refused ? self::EXPIRED : 'bytes';
            if ($refused && $failing) {
                $reads = 0;
                $body = FnStream::decorate(new Stream(fopen('php://temp', 'r+')), [
                    'isSeekable' => function () {
                        return false;
                    },
                    'eof' => function () {
                        return false;
                    },
                    'read' => function () use (&$reads) {
                        if ($reads++ === 0) {
                            return self::EXPIRED;
                        }
                        throw new \RuntimeException('The response body failed');
                    },
                ]);
            }
            $response = $refused ? new Response(403, ['Content-Type' => 'application/xml'], $body) : new Response(200, ['Content-Length' => '5'], $body);
            if ($status && isset($options['on_headers'])) {
                call_user_func($options['on_headers'], $response);
            }
            if ($writes) {
                $options['sink']->write((string) $response->getBody());
            }

            return new FulfilledPromise($response);
        };
    }

    // Downloads remote.bin into the caller's $resource, in a scope of its own, so that nothing of the call outlives it.
    private function borrow($resource)
    {
        $this->assertNull($this->attempt(function () use ($resource) {
            File::downloadToStream('remote.bin', $resource);
        }));
    }

    private static function downloadMetadata()
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'path' => 'remote.bin',
            'download_uri' => 'https://storage.example.test/download',
        ]));
    }

    private static function contents($path)
    {
        clearstatcache();

        return file_exists($path) ? file_get_contents($path) : null;
    }

    private static function payload()
    {
        return str_repeat("known transfer bytes\n", 1024);
    }

    private static function unusedPort()
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0');
        $address = stream_socket_get_name($socket, false);
        fclose($socket);

        return intval(substr(strrchr($address, ':'), 1));
    }
}

/**
 * Local files through closefail://<absolute path>, whose close fails, after closing the file, for a handle opened to
 * write: the destination with a failing close that FileTransferTest's cleanup cases need.
 */
class CloseFailingFileStream
{
    const FAILURE = 'Closing the destination failed';

    public $context;

    private $handle;
    private $writing = false;

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $this->handle = fopen(self::localPath($path), $mode);
        $this->writing = strpbrk($mode, 'wax+') !== false;

        return $this->handle !== false;
    }

    public function stream_read($count)
    {
        return fread($this->handle, $count);
    }

    public function stream_write($data)
    {
        return fwrite($this->handle, $data);
    }

    public function stream_tell()
    {
        return ftell($this->handle);
    }

    public function stream_eof()
    {
        return feof($this->handle);
    }

    public function stream_seek($offset, $whence)
    {
        return fseek($this->handle, $offset, $whence) === 0;
    }

    public function stream_stat()
    {
        return fstat($this->handle);
    }

    public function stream_flush()
    {
        return fflush($this->handle);
    }

    public function url_stat($path, $flags)
    {
        return @stat(self::localPath($path));
    }

    public function stream_close()
    {
        fclose($this->handle);
        if ($this->writing) {
            throw new \RuntimeException(self::FAILURE);
        }
    }

    private static function localPath($url)
    {
        return substr($url, strlen('closefail://'));
    }
}
