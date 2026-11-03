<?php

declare(strict_types=1);

namespace Files;

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
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\RejectedPromise;
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
