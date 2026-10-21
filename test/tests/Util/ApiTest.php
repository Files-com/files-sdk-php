<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiConnectException;
use Files\Exception\ApiRequestException;
use Files\Exception\ApiServerException;
use Files\Exception\InvalidParameterException;
use Files\Exception\MissingParameterException;
use Files\Exception\NotImplementedException;
use Files\Model\ApiKey;
use Files\Model\Bundle;
use Files\Model\File;
use Files\Model\FileUploadPart;
use Files\Model\Folder;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testTransferConnectionErrorsHideSignedUrls()
    {
        $url = 'https://transfer.example.test/private-part?X-Amz-Credential=credential&X-Amz-Signature=signature';
        $originalRetries = Files::$maxNetworkRetries;
        $originalMinDelay = Files::$minNetworkRetryDelay;
        $originalMaxDelay = Files::$maxNetworkRetryDelay;
        $originalLogLevel = Files::$logLevel;
        $originalHandler = Files::getHandler();
        $originalOutput = Logger::getOutputStream();
        Files::$maxNetworkRetries = 1;
        Files::$minNetworkRetryDelay = 0;
        Files::$maxNetworkRetryDelay = 0;

        try {
            foreach ([LogLevel::INFO, LogLevel::DEBUG] as $level) {
                foreach (['PUT', 'GET'] as $method) {
                    $output = fopen('php://memory', 'w+');
                    Logger::setOutputStream($output);
                    Files::$logLevel = $level;
                    $error = new \GuzzleHttp\Exception\ConnectException(
                        'cURL error 7: Failed to connect for ' . $url,
                        new Request($method, $url)
                    );
                    $mock = new MockHandler([$error, $error]);
                    Files::setHandler($mock);

                    try {
                        if ($method === 'PUT') {
                            Api::sendFile($url, $method, 'part');
                        } else {
                            Api::sendRequest($url, $method);
                        }
                        $this->fail('Expected a transfer error');
                    } catch (\Files\Exception\ApiConnectException $caught) {
                        $this->assertNotFalse(strpos($caught->getMessage(), 'cURL error 7'));
                        foreach (['transfer.example.test', 'private-part', 'credential', 'signature'] as $secret) {
                            $this->assertFalse(strpos($caught->getMessage(), $secret));
                        }
                    }
                    $this->assertCount(0, $mock);
                    rewind($output);
                    $logs = stream_get_contents($output);
                    if ($level === LogLevel::INFO) {
                        $this->assertFalse(strpos($logs, $url));
                    } else {
                        $this->assertNotFalse(strpos($logs, $url));
                    }
                    fclose($output);
                }
            }
        } finally {
            Files::$maxNetworkRetries = $originalRetries;
            Files::$minNetworkRetryDelay = $originalMinDelay;
            Files::$maxNetworkRetryDelay = $originalMaxDelay;
            Files::$logLevel = $originalLogLevel;
            Files::setHandler($originalHandler);
            Logger::setOutputStream($originalOutput);
        }
    }

    public function testListApiKeys()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json', 'X-Files-Cursor' => '1234'], '[{"id": 1},{"id": 2}]'),
            new Response(200, ['Content-Type' => 'application/json'], '[{"id": 3},{"id": 4}]')
        ]);
        Files::setHandler($mock);

        $items = ApiKey::all(['per_page' => 2]);

        $this->assertEquals(count($items), 4);
        $this->assertEquals($items[0]->id, 1);
        $this->assertEquals($items[1]->id, 2);
        $this->assertEquals($items[2]->id, 3);
        $this->assertEquals($items[3]->id, 4);
    }

    public function testAutoPaginationReadsTheCursorHeaderInAnyCase()
    {
        Files::setApiKey('test-key');
        foreach (['x-files-cursor', 'X-FILES-CURSOR'] as $header) {
            $mock = new MockHandler([
                new Response(200, ['Content-Type' => 'application/json', $header => '1234'], '[{"id": 1},{"id": 2}]'),
                new Response(200, ['Content-Type' => 'application/json'], '[{"id": 3}]')
            ]);
            Files::setHandler($mock);

            $items = ApiKey::all(['per_page' => 2]);

            $this->assertCount(0, $mock, $header);
            $this->assertSame([1, 2, 3], array_map(function ($item) {
                return $item->id;
            }, $items), $header);
            parse_str($mock->getLastRequest()->getUri()->getQuery(), $query);
            $this->assertSame('1234', $query['cursor'], $header);
        }
    }

    public function testMemberOperationAnsweredWithAnArrayReturnsItsModels()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '[{"ref": "put-1", "part_number": 1}, {"ref": "put-1", "part_number": 2}]'),
        ]);
        Files::setHandler($mock);

        $parts = (new File(['path' => 'folder/a.bin']))->beginUpload(['parts' => 2]);

        $this->assertSame('/api/rest/v1/file_actions/begin_upload/folder%2Fa.bin', $mock->getLastRequest()->getUri()->getPath());
        $this->assertTrue(is_array($parts));
        $this->assertCount(2, $parts);
        foreach ([1, 2] as $index => $number) {
            $this->assertInstanceOf(FileUploadPart::class, $parts[$index]);
            $this->assertSame('put-1', $parts[$index]->getRef());
            $this->assertSame($number, $parts[$index]->getPartNumber());
        }
    }

    public function testNoResponse()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(200, [], ''),
        ]);
        Files::setHandler($mock);

        $bundle = new Bundle(['id' => 1]);

        $this->assertNull($bundle->share());
    }

    public function testEmptyResponse()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '[]'),
        ]);
        Files::setHandler($mock);

        $items = ApiKey::all();

        $this->assertEquals(count($items), 0);
    }

    public function testBadGateway()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(502, [], '<html><head><title>502 Bad Gateway</title></head><body><center><h1>502 Bad Gateway</h1></center><hr><center>files.com</center></body></html>')
        ]);
        Files::setHandler($mock);

        $this->expectException(ApiServerException::class);
        $this->expectExceptionCode(502);
        $this->expectExceptionMessage('Bad Gateway');
        ApiKey::all();
    }

    public function testRequestException()
    {
        Files::setApiKey('test-key');
        $error = 'cURL error 60: SSL certificate problem: unable to get local issuer certificate';
        $mock = new MockHandler([
            new RequestException($error, new Request('GET', '/api_keys'))
        ]);
        Files::setHandler($mock);

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage($error);
        ApiKey::all();
    }

    public function testRegionMismatch()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(403, [], '{
        "data": {
          "host": "test.host"
        },
        "error": "You have connected to a URL that has different security settings than those required for your site.",
        "http-code": 403,
        "title": "Lockout Region Mismatch",
        "type": "not-authenticated/lockout-region-mismatch"
      }')
        ]);
        Files::setHandler($mock);

        try {
            ApiKey::all();
            $this->fail('Expected exception was not thrown');
        } catch (\Files\Exception\NotAuthenticated\LockoutRegionMismatchException $error) {
            $this->assertEquals($error->getError(), 'You have connected to a URL that has different security settings than those required for your site.');
            $this->assertEquals($error->getType(), 'not-authenticated/lockout-region-mismatch');
            $this->assertEquals($error->getTitle(), 'Lockout Region Mismatch');
            $this->assertEquals($error->getData()->host, 'test.host');
            $this->assertEquals($error->getHttpCode(), 403);
        }
    }

    public function testUsesConfiguredWorkspaceId()
    {
        Files::setApiKey('test-key');
        Files::setWorkspaceId(123);

        $history = [];
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        Files::setHandler($stack);

        Folder::listFor('/');

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('123', $request->getHeaderLine('X-Files-Workspace-Id'));

        Files::setWorkspaceId(null);
    }

    public function testUsesPerCallWorkspaceId()
    {
        Files::setApiKey('test-key');
        Files::setWorkspaceId(123);

        $history = [];
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        Files::setHandler($stack);

        Folder::listFor('/', [], ['workspace_id' => 456]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertEquals('456', $request->getHeaderLine('X-Files-Workspace-Id'));

        Files::setWorkspaceId(null);
    }

    public function testAllowsPerCallWorkspaceIdToBeCleared()
    {
        Files::setApiKey('test-key');
        Files::setWorkspaceId(123);

        $history = [];
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        Files::setHandler($stack);

        Folder::listFor('/', [], ['workspace_id' => null]);

        $this->assertCount(1, $history);
        $request = $history[0]['request'];
        $this->assertFalse($request->hasHeader('X-Files-Workspace-Id'));

        Files::setWorkspaceId(null);
    }

    public function testCrossOriginRedirectStripsFilesAuthHeaders()
    {
        Files::setBaseUrl('https://app.files.com');
        Files::setApiKey('test-key');
        Files::setSessionId('test-session');
        Files::setWorkspaceId(123);

        $history = [];
        $mock = new MockHandler([
            new Response(302, ['Location' => 'https://storage.example.test/download']),
            new Response(302, ['Location' => 'https://app.files.com/returned']),
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ]);
        $handler = function ($request, $options) use (&$history, $mock) {
            $history[] = ['request' => $request, 'options' => $options];
            return $mock($request, $options);
        };
        Files::setHandler($handler);

        Api::sendRequest('/folders', 'GET');

        $this->assertCount(3, $history);
        $redirectedRequest = $history[1]['request'];
        $this->assertFalse($redirectedRequest->hasHeader('X-FilesAPI-Key'));
        $this->assertFalse($redirectedRequest->hasHeader('X-FilesAPI-Auth'));
        $this->assertFalse($redirectedRequest->hasHeader('X-Files-Workspace-Id'));
        $returnedRequest = $history[2]['request'];
        $this->assertFalse($returnedRequest->hasHeader('X-FilesAPI-Key'));
        $this->assertFalse($returnedRequest->hasHeader('X-FilesAPI-Auth'));
        $this->assertFalse($returnedRequest->hasHeader('X-Files-Workspace-Id'));

        Files::setSessionId(null);
        Files::setWorkspaceId(null);
    }

    public function testSameOriginRedirectKeepsFilesAuthHeaders()
    {
        Files::setBaseUrl('https://app.files.com');
        Files::setApiKey('test-key');
        Files::setSessionId(null);
        Files::setWorkspaceId(123);

        $history = [];
        $mock = new MockHandler([
            new Response(302, ['Location' => 'https://app.files.com/redirected']),
            new Response(200, ['Content-Type' => 'application/json'], '[]')
        ]);
        $handler = function ($request, $options) use (&$history, $mock) {
            $history[] = ['request' => $request, 'options' => $options];
            return $mock($request, $options);
        };
        Files::setHandler($handler);

        Api::sendRequest('/folders', 'GET');

        $this->assertCount(2, $history);
        $redirectedRequest = $history[1]['request'];
        $this->assertEquals('test-key', $redirectedRequest->getHeaderLine('X-FilesAPI-Key'));
        $this->assertEquals('123', $redirectedRequest->getHeaderLine('X-Files-Workspace-Id'));

        Files::setWorkspaceId(null);
    }

    public function testFileDeleteEncodesReturnedPathBeforeRequest()
    {
        Files::setApiKey('test-key');

        $history = [];
        $mock = new MockHandler([
            new Response(204, [], '')
        ]);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));
        Files::setHandler($stack);

        \Files\Model\File::deletePath('root/data?recursive=true');

        $this->assertCount(1, $history);
        $uri = $history[0]['request']->getUri();
        $this->assertEquals('/api/rest/v1/files/root%2Fdata%3Frecursive%3Dtrue', $uri->getPath());
        $this->assertEquals('', $uri->getQuery());
    }

    // In a workspace, the API can report an upload part's path as the site path of the workspace's file.
    public function testUploadEndsAtItsDestinationNotThePathItsPartsReport()
    {
        Files::setBaseUrl('https://app.files.com');
        Files::setApiKey('test-key');
        Files::setWorkspaceId(123);
        $destination = 'reports/daily.csv';
        $data = 'hello';
        $sourceFilePath = tempnam(sys_get_temp_dir(), 'files-upload');
        file_put_contents($sourceFilePath, $data);
        $uploads = [
            'uploadFile' => function () use ($destination, $sourceFilePath) {
                \Files\Model\File::uploadFile($destination, $sourceFilePath);
            },
            // A file handle: PHP 5.6 reports a php://temp stream's end only after reading past it, which adds a part.
            'uploadData' => function () use ($destination, $sourceFilePath) {
                $stream = fopen($sourceFilePath, 'rb');
                try {
                    \Files\Model\File::uploadData($destination, $stream);
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
            },
        ];

        try {
            foreach ($uploads as $upload => $uploadToDestination) {
                $history = [];
                // Header names are case-insensitive: storage, and any response over HTTP/2, can send etag in lowercase.
                $mock = new MockHandler([
                    $this->uploadPartResponse(1),
                    new Response(200, ['ETag' => '"etag-1"']),
                    $this->uploadPartResponse(2),
                    new Response(200, ['etag' => '"etag-2"']),
                    new Response(201, ['Content-Type' => 'application/json'], '{"path": "reports/daily.csv"}'),
                ]);
                $stack = HandlerStack::create($mock);
                $stack->push(Middleware::history($history));
                Files::setHandler($stack);

                $uploadToDestination();

                $requestLines = array_map(function ($transaction) {
                    return $transaction['request']->getMethod() . ' ' . $transaction['request']->getUri();
                }, $history);
                $this->assertSame([
                    'POST https://app.files.com/api/rest/v1/file_actions/begin_upload/reports%2Fdaily.csv',
                    'PUT https://storage.example.test/part-1?X-Amz-Signature=signed',
                    'POST https://app.files.com/api/rest/v1/file_actions/begin_upload/reports%2Fdaily.csv',
                    'PUT https://storage.example.test/part-2?X-Amz-Signature=signed',
                    'POST https://app.files.com/api/rest/v1/files/reports%2Fdaily.csv',
                ], $requestLines, $upload);

                foreach ([0, 2, 4] as $apiRequest) {
                    $workspaceId = $history[$apiRequest]['request']->getHeaderLine('X-Files-Workspace-Id');
                    $this->assertSame('123', $workspaceId, $upload);
                }
                $this->assertSame('hel', (string) $history[1]['request']->getBody(), $upload);
                $this->assertSame('lo', (string) $history[3]['request']->getBody(), $upload);

                $completion = json_decode((string) $history[4]['request']->getBody(), true);
                $this->assertSame('end', $completion['action'], $upload);
                $this->assertSame('upload-ref', $completion['ref'], $upload);
                $this->assertSame([
                    ['etag' => '"etag-1"', 'part' => 1],
                    ['etag' => '"etag-2"', 'part' => 2],
                ], $completion['etags'], $upload);
                $this->assertSame(strlen($data), $completion['size'], $upload);
            }
        } finally {
            Files::setWorkspaceId(null);
            unlink($sourceFilePath);
        }
    }

    // What a download reports comes from its own response's headers: Content-Length for the bytes received, and
    // Content-Range's total for a range, or else the bytes received. Header names are case-insensitive; the generated
    // simulator sends these in lowercase (content-length, content-range, etag), as any response over HTTP/2 does.
    public function testDownloadReportsItsLengthFromHeadersInAnyCase()
    {
        Files::setApiKey('test-key');
        $metadata = json_encode([
            'path' => 'reports/daily.csv',
            'download_uri' => 'https://storage.example.test/download?X-Amz-Signature=signed',
        ]);
        $lowercase = ['content-type' => 'application/octet-stream', 'content-length' => '5', 'accept-ranges' => 'bytes', 'etag' => '"e"'];
        $downloads = [
            'whole, lowercase' => [new Response(200, $lowercase, 'hello'), null, 'hello', 5, 5],
            'whole, capitalized' => [new Response(200, ['Content-Length' => '5'], 'hello'), null, 'hello', 5, 5],
            'range, lowercase' => [new Response(206, array_merge($lowercase, ['content-length' => '3', 'content-range' => 'bytes 1-3/5']), 'ell'), [1, 3], 'ell', 3, 5],
        ];

        foreach ($downloads as $download => list($response, $range, $bytes, $received, $total)) {
            $mock = new MockHandler([new Response(200, ['Content-Type' => 'application/json'], $metadata), $response]);
            Files::setHandler($mock);
            $destination = tempnam(sys_get_temp_dir(), 'files-download');

            try {
                $result = $range
                    ? File::partialDownloadToFile('reports/daily.csv', $destination, $range[0], $range[1])
                    : File::downloadToFile('reports/daily.csv', $destination);

                $this->assertCount(0, $mock, $download);
                $this->assertSame($bytes, file_get_contents($destination), $download);
                $this->assertSame($received, $result->received, $download);
                $this->assertSame($total, $result->total, $download);
            } finally {
                unlink($destination);
            }
        }
    }

    // Each part goes as its grant says: with the http_method granted (PUT when it gives none) and exactly the headers
    // granted, under the names granted. Content-Type application/octet-stream and the part's Content-Length are added
    // only when the grant names no such header, in any case; a granted Content-Length other than the part's own is
    // refused before the part is sent.
    public function testUploadSendsEachPartAsItsGrantSays()
    {
        $sourceFilePath = $this->uploadSource('hi');
        $octetStream = ['Content-Length' => ['2'], 'Content-Type' => ['application/octet-stream']];
        $grants = [
            'a method and headers' => [
                ['http_method' => 'POST', 'headers' => ['content-type' => 'text/plain; charset=binary', 'x-amz-meta-probe' => 'kept']],
                'POST',
                ['Content-Length' => ['2'], 'content-type' => ['text/plain; charset=binary'], 'x-amz-meta-probe' => ['kept']],
            ],
            'no headers' => [[], 'PUT', $octetStream],
            'no http_method' => [['http_method' => null], 'PUT', $octetStream],
            'its own Content-Length' => [['headers' => ['content-length' => '2']], 'PUT', ['Content-Type' => ['application/octet-stream'], 'content-length' => ['2']]],
        ];

        try {
            foreach ($grants as $case => list($grant, $method, $headers)) {
                list($requests, $error) = $this->uploadAgainst(['grant' => $grant], function () use ($sourceFilePath) {
                    File::uploadFile('reports/daily.csv', $sourceFilePath);
                });

                $this->assertNull($error, $case);
                $parts = self::requestsOf($requests, 'storage');
                $this->assertCount(1, $parts, $case);
                $this->assertSame([$method, 'hi'], [$parts[0]['method'], $parts[0]['body']], $case);
                $sent = array_diff_key($parts[0]['headers'], ['Host' => true, 'User-Agent' => true]);
                ksort($sent);
                $this->assertSame($headers, $sent, $case);
            }

            list($requests, $error) = $this->uploadAgainst(['grant' => ['headers' => ['Content-Length' => '3']]], function () use ($sourceFilePath) {
                File::uploadFile('reports/daily.csv', $sourceFilePath);
            });
            $this->assertInstanceOf(ApiBadResponseException::class, $error);
            $this->assertSame([[], []], [self::requestsOf($requests, 'storage'), self::requestsOf($requests, 'end')]);
        } finally {
            unlink($sourceFilePath);
        }
    }

    // A part is acknowledged only by a success with exactly one ETag that is not empty. Without one the upload stops
    // there: progress is not told, and no later part is planned and no end sent.
    public function testUploadStopsAtAPartNotAcknowledgedByOneETag()
    {
        $answers = [
            'no ETag' => new Response(200),
            'an empty ETag' => new Response(200, ['ETag' => '']),
            'two ETags' => new Response(200, ['ETag' => ['"a"', '"b"']]),
        ];

        foreach ($answers as $case => $answer) {
            $progress = [];
            $service = ['storage' => function () use ($answer) {
                return $answer;
            }];
            list($requests, $error) = $this->uploadAgainst($service, function () use (&$progress) {
                File::uploadData('reports/daily.csv', 'hello', [], ['progress' => function ($event) use (&$progress) {
                    $progress[] = $event;
                }]);
            });

            $this->assertInstanceOf(ApiBadResponseException::class, $error, $case);
            $this->assertSame([], $progress, $case);
            $this->assertCount(1, self::requestsOf($requests, 'begin'), $case);
            $this->assertCount(1, self::requestsOf($requests, 'storage'), $case);
            $this->assertSame([], self::requestsOf($requests, 'end'), $case);
        }
    }

    // A grant asking for form parameters or a form body, which the helpers do not send, is refused before any part is
    // sent; an empty one asks for nothing.
    public function testUploadRefusesAGrantAskingForParametersOrABody()
    {
        $grants = [
            'parameters' => [['parameters' => ['policy' => 'p']], NotImplementedException::class],
            'send' => [['send' => ['file' => 'file']], NotImplementedException::class],
            'empty parameters and send' => [['parameters' => new \stdClass(), 'send' => []], null],
        ];

        foreach ($grants as $case => list($grant, $class)) {
            list($requests, $error) = $this->uploadAgainst(['grant' => $grant], function () {
                File::uploadData('reports/daily.csv', 'hi');
            });

            $sent = [count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))];
            if ($class === null) {
                $this->assertNull($error, $case);
                $this->assertSame([1, 1], $sent, $case);
            } else {
                $this->assertInstanceOf($class, $error, $case);
                $this->assertSame([0, 0], $sent, $case);
            }
        }
    }

    // What begin_upload grants must be exactly the part asked for, of the upload's ref, with a positive partsize and an
    // upload_uri; anything else is refused before that part is sent. The helpers before begin_upload read no list of
    // parts, so this has no counterpart in them.
    public function testUploadRefusesAGrantThatIsNotThePartAskedFor()
    {
        $answers = [
            'two parts' => function ($number, $part) {
                return [$part, array_merge($part, ['part_number' => $number + 1])];
            },
            'no part' => function () {
                return [];
            },
            'another part' => function ($number, $part) {
                return [array_merge($part, ['part_number' => $number + 1])];
            },
            'another ref for part 2' => function ($number, $part) {
                return [array_merge($part, ['ref' => $number === 1 ? 'upload-ref' : 'other-ref'])];
            },
            'partsize 0' => function ($number, $part) {
                return [array_merge($part, ['partsize' => 0])];
            },
            'no upload_uri' => function ($number, $part) {
                return [array_merge($part, ['upload_uri' => ''])];
            },
        ];

        foreach ($answers as $case => $parts) {
            list($requests, $error) = $this->uploadAgainst(['parts' => $parts], function () {
                File::uploadData('reports/daily.csv', 'hello');
            });

            $this->assertInstanceOf(ApiBadResponseException::class, $error, $case);
            $sent = [count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))];
            $this->assertSame([$case === 'another ref for part 2' ? 1 : 0, 0], $sent, $case);
        }
    }

    // A part whose grant says it cannot be retried (retry_parts false) is sent once: a connection failure stops the
    // upload, with no progress, later part or end. Otherwise (true, null or none, the SDK's own policy) a part is
    // retried as any request is, as the upload's API requests (its first begin_upload and its end here) still are.
    public function testUploadRetriesAPartOnlyWhenItsGrantAllows()
    {
        $originalMinDelay = Files::$minNetworkRetryDelay;
        $originalMaxDelay = Files::$maxNetworkRetryDelay;
        Files::$minNetworkRetryDelay = 0;
        Files::$maxNetworkRetryDelay = 0;
        $grants = [
            'retry_parts false' => [['retry_parts' => false], false],
            'retry_parts true' => [['retry_parts' => true], true],
            'retry_parts null' => [['retry_parts' => null], true],
            'no retry_parts' => [[], true],
        ];

        try {
            foreach ($grants as $case => list($grant, $retried)) {
                // Storage that cannot be reached at the first attempt, or at any.
                $failures = [
                    'once' => function ($request, $attempt) {
                        return $attempt === 1 ? new ConnectException('cURL error 7: Failed to connect', $request) : new Response(200, ['ETag' => '"e"']);
                    },
                    'always' => function ($request) {
                        return new ConnectException('cURL error 7: Failed to connect', $request);
                    },
                ];
                foreach ($failures as $failure => $storage) {
                    $what = $case . ', storage failing ' . $failure;
                    $progress = [];
                    $service = ['grant' => $grant, 'storage' => $storage, 'fail_first' => ['begin', 'end']];
                    list($requests, $error) = $this->uploadAgainst($service, function () use (&$progress) {
                        File::uploadData('reports/daily.csv', 'hi', [], ['progress' => function ($event) use (&$progress) {
                            $progress[] = $event;
                        }]);
                    });

                    $sent = [count(self::requestsOf($requests, 'begin')), count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))];
                    if (!$retried) {
                        $this->assertInstanceOf(ApiConnectException::class, $error, $what);
                        $this->assertSame([2, 1, 0], $sent, $what);
                        $this->assertSame([], $progress, $what);
                    } elseif ($failure === 'once') {
                        $this->assertNull($error, $what);
                        $this->assertSame([2, 2, 2], $sent, $what);
                    } else {
                        $this->assertInstanceOf(ApiConnectException::class, $error, $what);
                        $this->assertSame([2, 1 + Files::$maxNetworkRetries, 0], $sent, $what);
                    }
                }
            }
        } finally {
            Files::$minNetworkRetryDelay = $originalMinDelay;
            Files::$maxNetworkRetryDelay = $originalMaxDelay;
        }
    }

    // provided_mtime and custom_metadata, which only the end of an upload takes, go to its end as given (by key, so a
    // null provided_mtime goes as null) and to no begin_upload; the other parameters go with the first part's request.
    public function testUploadSendsTheParametersOfItsEndToItsEnd()
    {
        $sourceFilePath = $this->uploadSource('hello');
        $params = [
            'provided_mtime' => '2001-02-03T04:05:06Z',
            'custom_metadata' => ['team' => 'reports'],
            'mkdir_parents' => true,
            'copy_behaviors' => true,
            'x_unknown' => 'kept',
        ];
        $uploads = [
            'uploadFile' => [function () use ($sourceFilePath, $params) {
                File::uploadFile('reports/daily.csv', $sourceFilePath, $params);
            }, ['provided_mtime' => '2001-02-03T04:05:06Z', 'custom_metadata' => ['team' => 'reports']], ['mkdir_parents' => true, 'copy_behaviors' => true, 'x_unknown' => 'kept']],
            'uploadData, a null provided_mtime' => [function () {
                File::uploadData('reports/daily.csv', 'hello', ['provided_mtime' => null]);
            }, ['provided_mtime' => null], []],
        ];

        try {
            foreach ($uploads as $upload => list($uploadToDestination, $end, $forwarded)) {
                list($requests, $error) = $this->uploadAgainst([], $uploadToDestination);

                $this->assertNull($error, $upload);
                $begins = self::requestsOf($requests, 'begin');
                $this->assertCount(2, $begins, $upload);
                $this->assertSame($forwarded, array_intersect_key($begins[0]['json'], $params), $upload);
                foreach ($begins as $begin) {
                    $this->assertSame([], array_intersect_key($begin['json'], ['provided_mtime' => true, 'custom_metadata' => true]), $upload);
                }
                $ends = self::requestsOf($requests, 'end');
                $this->assertCount(1, $ends, $upload);
                $this->assertSame($end, array_intersect_key($ends[0]['json'], $end), $upload);
                $this->assertSame([], array_intersect_key($ends[0]['json'], ['mkdir_parents' => true, 'copy_behaviors' => true, 'x_unknown' => true]), $upload);
            }
        } finally {
            unlink($sourceFilePath);
        }
    }

    // The helpers own where an upload goes, its parts and ref, and its end's action, size, length, ETags and restart: a
    // caller's parameter of one of those names (part 1 included) is accepted and not sent, so it changes none of them.
    public function testUploadKeepsItsDestinationAndPartsWhateverItsParametersSay()
    {
        $params = [
            'part' => 1,
            'parts' => 9,
            'path' => 'elsewhere/x.csv',
            'ref' => 'caller-ref',
            'restart' => 2,
            'action' => 'append',
            'size' => 99,
            'length' => 99,
            'etags' => [['etag' => 'x', 'part' => 1]],
            'mkdir_parents' => true,
        ];
        list($requests, $error) = $this->uploadAgainst([], function () use ($params) {
            File::uploadData('reports/daily.csv', 'hello', $params);
        });

        $this->assertNull($error);
        foreach ($requests as $request) {
            if ($request['kind'] !== 'storage') {
                $this->assertStringEndsWith('/reports%2Fdaily.csv', $request['path']);
            }
        }
        $begins = self::requestsOf($requests, 'begin');
        $this->assertCount(2, $begins);
        $first = $begins[0]['json'];
        $this->assertSame([1, true], [$first['part'], $first['mkdir_parents']]);
        foreach (['parts' => 9, 'path' => 'elsewhere/x.csv', 'ref' => 'caller-ref', 'restart' => 2, 'action' => 'append', 'size' => 99, 'length' => 99] as $name => $value) {
            $this->assertFalse(isset($first[$name]) && $first[$name] === $value, 'The first begin_upload sent the caller\'s ' . $name);
        }
        $this->assertArrayNotHasKey('etags', $first);
        $this->assertSame(['upload-ref', 2], [$begins[1]['json']['ref'], $begins[1]['json']['part']]);

        $ends = self::requestsOf($requests, 'end');
        $this->assertCount(1, $ends);
        $end = $ends[0]['json'];
        $this->assertSame(['end', 'upload-ref', 5], [$end['action'], $end['ref'], $end['size']]);
        $this->assertSame([['etag' => '"etag-1"', 'part' => 1], ['etag' => '"etag-2"', 'part' => 2]], $end['etags']);
        $this->assertSame([], array_intersect_key($end, ['parts' => true, 'restart' => true, 'length' => true, 'part' => true]));
    }

    // Each part is filled from as many reads as it takes: a source giving at most 3 bytes a read (a socket) still sends
    // parts of the granted partsize.
    public function testUploadFillsEachPartFromShortReads()
    {
        list($source, $writer) = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        fwrite($writer, 'abcdefghij');
        fclose($writer);
        stream_set_chunk_size($source, 3);

        list($requests, $error) = $this->uploadAgainst(['partsize' => 4], function () use ($source) {
            File::uploadData('reports/daily.csv', $source);
        });

        $this->assertNull($error);
        $this->assertSame(['abcd', 'efgh', 'ij'], array_column(self::requestsOf($requests, 'storage'), 'body'));
        $this->assertSame(10, self::requestsOf($requests, 'end')[0]['json']['size']);
    }

    // A source whose read fails (a handle open for writing only), or that gives nothing before its end (a non-blocking
    // socket with nothing to read yet), stops the upload before that part is sent or the upload ends; the caller's
    // stream stays open.
    public function testUploadStopsWhenItsSourceFailsOrGivesNothing()
    {
        $writeOnlyPath = tempnam(sys_get_temp_dir(), 'files-upload');
        list($socket, $peer) = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        stream_set_blocking($socket, false);
        $sources = ['a failing read' => fopen($writeOnlyPath, 'wb'), 'an empty read before the end' => $socket];
        // PHP 7.4 and later report a failed read as a notice, and return false.
        $originalReporting = error_reporting(E_ALL & ~E_NOTICE);

        try {
            foreach ($sources as $case => $source) {
                list($requests, $error) = $this->uploadAgainst([], function () use ($source) {
                    File::uploadData('reports/daily.csv', $source);
                });

                $this->assertInstanceOf(InvalidParameterException::class, $error, $case);
                $this->assertSame([0, 0], [count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))], $case);
                $this->assertTrue(is_resource($source), $case);
            }
        } finally {
            error_reporting($originalReporting);
            foreach ([$sources['a failing read'], $socket, $peer] as $stream) {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            unlink($writeOnlyPath);
        }
    }

    // Kept: an empty source is one empty part, a file ending exactly where a part ends sends one more, empty, part,
    // and empty data is refused.
    public function testUploadOfAnEmptySourceOrOneEndingWhereAPartEnds()
    {
        $empty = fopen('php://temp', 'r+');
        $sourceFilePath = $this->uploadSource('abcdef');
        $uploads = [
            'an empty stream' => [function () use ($empty) {
                File::uploadData('reports/daily.csv', $empty);
            }, [''], 0],
            'a file of two whole parts' => [function () use ($sourceFilePath) {
                File::uploadFile('reports/daily.csv', $sourceFilePath);
            }, ['abc', 'def', ''], 6],
        ];

        try {
            foreach ($uploads as $upload => list($uploadToDestination, $parts, $size)) {
                list($requests, $error) = $this->uploadAgainst([], $uploadToDestination);

                $this->assertNull($error, $upload);
                $this->assertSame($parts, array_column(self::requestsOf($requests, 'storage'), 'body'), $upload);
                $this->assertSame($size, self::requestsOf($requests, 'end')[0]['json']['size'], $upload);
            }

            list($requests, $error) = $this->uploadAgainst([], function () {
                File::uploadData('reports/daily.csv', '');
            });
            $this->assertInstanceOf(MissingParameterException::class, $error);
            $this->assertSame([], $requests);
        } finally {
            unlink($sourceFilePath);
        }
    }

    // A caller's stream stays open when the upload fails before its end, a progress callback throwing after the last
    // part included, and is closed once every part is acknowledged and progress has returned, before the end: so a
    // failing end leaves it closed.
    public function testUploadDataClosesTheCallersStreamOnlyOnceEveryPartIsAcknowledged()
    {
        $thrown = new \RuntimeException('progress failed');
        $cases = [
            'a part failing' => [['storage' => function () {
                return new Response(500);
            }], null, ApiServerException::class, true],
            'progress throwing after the last part' => [[], function ($event) use ($thrown) {
                if ($event['part_number'] === 2) {
                    throw $thrown;
                }
            }, \RuntimeException::class, true],
            'the end failing' => [['end' => function () {
                return new Response(500);
            }], null, ApiServerException::class, false],
            'success' => [[], null, null, false],
        ];

        foreach ($cases as $case => list($service, $progress, $class, $open)) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, 'hello');
            rewind($stream);
            list($requests, $error) = $this->uploadAgainst($service, function () use ($stream, $progress) {
                File::uploadData('reports/daily.csv', $stream, [], $progress ? ['progress' => $progress] : []);
            });

            if ($class === null) {
                $this->assertNull($error, $case);
            } else {
                $this->assertInstanceOf($class, $error, $case);
            }
            $this->assertSame($open, is_resource($stream), $case);
            if ($progress) {
                $this->assertSame($thrown, $error, $case);
                $this->assertSame([2, 0], [count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))], $case);
            }
            if (is_resource($stream)) {
                fclose($stream);
            }
        }
    }

    // uploadFile's own handle on the file is closed when the upload fails, as when it succeeds.
    public function testUploadFileClosesItsOwnHandleOnEveryPath()
    {
        if (!function_exists('get_resources')) {
            $this->markTestSkipped('Counting open handles needs get_resources (PHP 7)');
        }

        $sourceFilePath = $this->uploadSource('hello');
        $handles = function () use ($sourceFilePath) {
            return count(array_filter(get_resources('stream'), function ($stream) use ($sourceFilePath) {
                $meta = stream_get_meta_data($stream);
                return isset($meta['uri']) && $meta['uri'] === $sourceFilePath;
            }));
        };
        $failing = ['storage' => function () {
            return new Response(500);
        }];

        try {
            foreach (['a part failing' => [$failing, ApiServerException::class], 'success' => [[], null]] as $case => list($service, $class)) {
                list(, $error) = $this->uploadAgainst($service, function () use ($sourceFilePath) {
                    File::uploadFile('reports/daily.csv', $sourceFilePath);
                });

                $this->assertSame($class, $error === null ? null : get_class($error), $case);
                $this->assertSame(0, $handles(), $case);
            }
        } finally {
            unlink($sourceFilePath);
        }
    }

    // progress, an option of the helpers alone, is called after each part is acknowledged with total_bytes (null for a
    // caller's stream), acknowledged_bytes, part_number and part_bytes, and reaches no request. The other options go to
    // the upload's API requests only, so a part's storage request carries no API credential or workspace.
    public function testUploadReportsProgressAfterEachAcknowledgedPart()
    {
        $sourceFilePath = $this->uploadSource('hello');
        $events = function ($total) {
            return [
                ['total_bytes' => $total, 'acknowledged_bytes' => 3, 'part_number' => 1, 'part_bytes' => 3],
                ['total_bytes' => $total, 'acknowledged_bytes' => 5, 'part_number' => 2, 'part_bytes' => 2],
            ];
        };
        $uploads = [
            'uploadFile' => [function ($options) use ($sourceFilePath) {
                File::uploadFile('reports/daily.csv', $sourceFilePath, [], $options);
            }, 5, '/reports%2Fdaily.csv'],
            'uploadData, a string' => [function ($options) {
                File::uploadData('reports/daily.csv', 'hello', [], $options);
            }, 5, '/reports%2Fdaily.csv'],
            'uploadData, a stream' => [function ($options) use ($sourceFilePath) {
                File::uploadData('reports/daily.csv', fopen($sourceFilePath, 'rb'), [], $options);
            }, null, '/reports%2Fdaily.csv'],
            'uploadToRemoteServer' => [function ($options) use ($sourceFilePath) {
                File::uploadToRemoteServer(5, 'daily.csv', $sourceFilePath, [], $options);
            }, 5, '/_%2FRemoteServers%2F5%2Fdaily.csv'],
        ];

        try {
            foreach ($uploads as $upload => list($uploadWith, $total, $destination)) {
                $progress = [];
                $options = ['workspace_id' => 77, 'api_key' => 'option-key', 'progress' => function ($event) use (&$progress) {
                    $progress[] = $event;
                }];
                list($requests, $error) = $this->uploadAgainst([], function () use ($uploadWith, $options) {
                    $uploadWith($options);
                });

                $this->assertNull($error, $upload);
                $this->assertSame($events($total), $progress, $upload);
                foreach ($requests as $request) {
                    $this->assertNotContains('progress', $request['options'], $upload);
                    $credentials = array_intersect_key(array_change_key_case($request['headers']), ['x-filesapi-key' => true, 'x-filesapi-auth' => true, 'x-files-workspace-id' => true]);
                    if ($request['kind'] === 'storage') {
                        $this->assertSame([], $credentials, $upload);
                    } else {
                        $this->assertStringEndsWith($destination, $request['path'], $upload);
                        $this->assertSame(['x-filesapi-key' => ['option-key'], 'x-files-workspace-id' => ['77']], $credentials, $upload);
                    }
                }
            }

            list($requests, $error) = $this->uploadAgainst([], function () use ($sourceFilePath) {
                File::uploadFile('reports/daily.csv', $sourceFilePath, [], ['progress' => 'not a callable']);
            });
            $this->assertInstanceOf(InvalidParameterException::class, $error);
            $this->assertSame([], $requests);
        } finally {
            unlink($sourceFilePath);
        }
    }

    // A progress callback that throws cancels the upload where it is: what it throws comes out of the helper as it
    // was, after the one part acknowledged, with no later part and no end.
    public function testUploadCancelledByItsProgressCallbackStopsThere()
    {
        $sourceFilePath = $this->uploadSource('hello');
        $cancelled = new \RuntimeException('cancelled');
        $calls = 0;

        try {
            list($requests, $error) = $this->uploadAgainst([], function () use ($sourceFilePath, $cancelled, &$calls) {
                File::uploadFile('reports/daily.csv', $sourceFilePath, [], ['progress' => function () use ($cancelled, &$calls) {
                    $calls++;
                    throw $cancelled;
                }]);
            });

            $this->assertSame($cancelled, $error);
            $this->assertSame(1, $calls);
            $sent = [count(self::requestsOf($requests, 'begin')), count(self::requestsOf($requests, 'storage')), count(self::requestsOf($requests, 'end'))];
            $this->assertSame([1, 1, 0], $sent);
        } finally {
            unlink($sourceFilePath);
        }
    }

    // A local file holding $data, for an upload test to remove.
    private function uploadSource($data)
    {
        $path = tempnam(sys_get_temp_dir(), 'files-upload');
        file_put_contents($path, $data);
        return $path;
    }

    // Runs $upload against uploadService($service) with the API at https://app.files.com, and returns the requests it
    // sent and what it threw (null when it returned).
    private function uploadAgainst(array $service, $upload)
    {
        Files::setBaseUrl('https://app.files.com');
        Files::setApiKey('test-key');
        $requests = [];
        Files::setHandler($this->uploadService($requests, $service));
        $error = null;
        try {
            $upload();
        } catch (\Exception $e) {
            $error = $e;
        }
        return [$requests, $error];
    }

    // The API and storage the upload helpers' tests run against, as a Guzzle handler, with $service setting how it
    // answers. A request for a part is granted the part it asks for: ref upload-ref, partsize bytes (3 unless given),
    // and the fields of grant. It can come as documented (POST /file_actions/begin_upload/{path}, answered with the
    // list parts gives, from the part number and that part, or else that part alone) or as the helpers before
    // begin_upload sent it (POST /files/{path} with action=put, answered with the part alone), so that either reaches
    // what a test is about. A storage request is answered by storage, given the request and its attempt number (with a
    // response, or an exception it fails with; 200 with an ETag by default), and the end of the upload (POST
    // /files/{path}, action=end) by end (201 by default). fail_first names the kinds of request (begin, storage, end)
    // whose first attempt cannot connect. Every request is kept in $requests; a 21st request for a part is refused, so
    // that a helper that never ends stops.
    private function uploadService(array &$requests, array $service)
    {
        $service += ['partsize' => 3, 'grant' => [], 'parts' => null, 'storage' => null, 'end' => null, 'fail_first' => []];
        $attempts = ['begin' => 0, 'storage' => 0, 'end' => 0];
        return function ($request, array $options) use (&$requests, &$attempts, $service) {
            $body = (string) $request->getBody();
            $json = json_decode($body, true);
            $legacy = is_array($json) && isset($json['action']) && $json['action'] === 'put';
            if ($request->getUri()->getHost() === 'storage.example.test') {
                $kind = 'storage';
            } elseif ($legacy || strpos($request->getUri()->getPath(), '/file_actions/begin_upload/') !== false) {
                $kind = 'begin';
            } else {
                $kind = 'end';
            }
            $requests[] = [
                'kind' => $kind,
                'method' => $request->getMethod(),
                'path' => $request->getUri()->getPath(),
                'headers' => $request->getHeaders(),
                'body' => $body,
                'json' => $json,
                'options' => array_keys($options),
            ];
            $attempt = ++$attempts[$kind];
            if ($attempt === 1 && in_array($kind, $service['fail_first'], true)) {
                return Create::rejectionFor(new ConnectException('cURL error 7: Failed to connect', $request));
            }

            if ($kind === 'storage' || $kind === 'end') {
                if ($service[$kind]) {
                    $answer = call_user_func($service[$kind], $request, $attempt);
                } else {
                    $answer = $kind === 'end'
                        ? new Response(201, ['Content-Type' => 'application/json'], '{}')
                        : new Response(200, ['ETag' => '"etag-' . $attempt . '"']);
                }
                return $answer instanceof \Exception ? Create::rejectionFor($answer) : Create::promiseFor($answer);
            }

            if ($attempt > 20) {
                throw new \LengthException('More than 20 parts were requested');
            }
            $number = isset($json['part']) ? $json['part'] : 1;
            $part = array_merge([
                'http_method' => 'PUT',
                'part_number' => $number,
                'partsize' => $service['partsize'],
                'ref' => 'upload-ref',
                'upload_uri' => 'https://storage.example.test/part-' . $number . '?X-Amz-Signature=signed',
            ], $service['grant']);
            $parts = $service['parts'] ? call_user_func($service['parts'], $number, $part) : [$part];
            return Create::promiseFor(new Response(200, ['Content-Type' => 'application/json'], json_encode($legacy ? $parts[0] : $parts)));
        };
    }

    private static function requestsOf(array $requests, $kind)
    {
        return array_values(array_filter($requests, function ($request) use ($kind) {
            return $request['kind'] === $kind;
        }));
    }

    private function uploadPartResponse($partNumber)
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([[
            'http_method' => 'PUT',
            'part_number' => $partNumber,
            'partsize' => 3,
            'path' => '_/Workspaces/123/reports/daily.csv',
            'ref' => 'upload-ref',
            'upload_uri' => 'https://storage.example.test/part-' . $partNumber . '?X-Amz-Signature=signed',
        ]]));
    }

    public function testNotFound()
    {
        Files::setApiKey('test-key');
        $mock = new MockHandler([
            new Response(404, [], '{
        "type": "not-found",
        "http-code": 404,
        "error": "Not Found.  This may be related to your permissions."
      }')
        ]);
        Files::setHandler($mock);

        try {
            Folder::listFor('/missing', [ 'sort_by' => [ 'path' => 'desc' ] ]);
            $this->fail('Expected exception was not thrown');
        } catch (\Files\Exception\NotFoundException $error) {
            $this->assertEquals($error->getError(), 'Not Found.  This may be related to your permissions.');
            $this->assertEquals($error->getType(), 'not-found');
            $this->assertEquals($error->getHttpCode(), 404);
        }
    }
}
