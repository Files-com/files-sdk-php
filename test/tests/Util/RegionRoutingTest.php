<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\FilesException;
use Files\Exception\NotAuthenticated\LockoutRegionMismatchException;
use Files\Model\ApiKey;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class RegionRoutingTest extends TestCase
{
    private function configured($run)
    {
        $original = [Files::getBaseUrl(), Files::getApiKey(), Files::getSessionId(), Files::getWorkspaceId(),
            Files::getHandler(), Files::getRegionRelocation(), Files::$maxNetworkRetries,
            Files::$minNetworkRetryDelay, Files::$maxNetworkRetryDelay, Files::$autoPaginate];
        Files::setBaseUrl('https://api.example.test');
        Files::setApiKey('configured-key');
        Files::setSessionId(null);
        Files::setWorkspaceId(99);
        Files::setRegionRelocation(true);
        Files::$maxNetworkRetries = 3;
        Files::$minNetworkRetryDelay = 0;
        Files::$maxNetworkRetryDelay = 0;
        Files::$autoPaginate = true;
        try {
            $run();
        } finally {
            Files::setBaseUrl($original[0]);
            Files::setApiKey($original[1]);
            Files::setSessionId($original[2]);
            Files::setWorkspaceId($original[3]);
            Files::setHandler($original[4]);
            Files::setRegionRelocation($original[5]);
            Files::$maxNetworkRetries = $original[6];
            Files::$minNetworkRetryDelay = $original[7];
            Files::$maxNetworkRetryDelay = $original[8];
            Files::$autoPaginate = $original[9];
        }
    }

    private function mismatch($host = 'region.example.test:8443', $headers = [])
    {
        return new Response(403, $headers, json_encode([
            'type' => 'not-authenticated/lockout-region-mismatch', 'http-code' => 403,
            'error' => 'Wrong region', 'data' => ['host' => $host],
        ]));
    }

    private function record($answers, &$sent, $nested = false)
    {
        $mock = new MockHandler($answers);
        $handler = function ($request, $options) use ($mock, &$sent) {
            $sent[] = ['request' => $request, 'options' => $options, 'body' => $request->getBody()->getContents()];
            return $mock($request, $options);
        };
        Files::setHandler($nested ? HandlerStack::create($handler) : $handler);
        return $mock;
    }

    public function testPublicApiFollowsTypedAndLegacyFormsWithCapturedContext()
    {
        $this->configured(function () {
            $header = ['x-files-host' => 'region.example.test:8443'];
            $forms = [
                $this->mismatch('region.example.test:8443', ['X-Files-Host' => 'ignored.example.test']),
                new Response(403, $header, '{"type":"not-authenticated/lockout-region-mismatch","http-code":403}'),
                $this->mismatch('', $header),
                $this->mismatch(null, $header),
                new Response(403, $header, '{"type":"not-authenticated/lockout-region-mismatch","data":null}'),
                new Response(403, $header, '{"type":"not-authenticated/lockout-region-mismatch","data":{}}'),
                new Response(403, $header, 'You have connected to a URL with different security settings.'),
            ];
            foreach ($forms as $index => $answer) {
                foreach ([false, true] as $nested) {
                    $sent = [];
                    $freshAnswer = new Response($answer->getStatusCode(), $answer->getHeaders(), (string) $answer->getBody());
                    $mock = $this->record([$freshAnswer, new Response(200, [], '[{"id":7}]')], $sent, $nested);
                    $progress = function () {
                    };
                    $items = ApiKey::all(['label' => 'café & tea'], ['api_key' => 'call-key', 'workspace_id' => 0,
                        'headers' => ['X-Correlation' => 'request-1'], 'progress' => $progress, 'verify' => '/caller/ca.pem']);
                    $this->assertSame(7, $items[0]->id);
                    $this->assertCount(0, $mock);
                    $this->assertCount(2, $sent);
                    $first = $sent[0]['request'];
                    $second = $sent[1]['request'];
                    $this->assertSame('region.example.test', $second->getUri()->getHost());
                    $this->assertSame(8443, $second->getUri()->getPort());
                    $this->assertSame($first->getMethod(), $second->getMethod());
                    $this->assertSame($first->getUri()->getPath(), $second->getUri()->getPath());
                    $this->assertSame($first->getUri()->getQuery(), $second->getUri()->getQuery());
                    $this->assertSame('call-key', $second->getHeaderLine('X-FilesAPI-Key'));
                    $this->assertSame('0', $second->getHeaderLine('X-Files-Workspace-Id'));
                    $this->assertSame('request-1', $second->getHeaderLine('X-Correlation'));
                    $this->assertSame($progress, $sent[1]['options']['progress']);
                    $this->assertSame('/caller/ca.pem', $sent[1]['options']['verify']);
                    $this->assertSame('https://api.example.test', Files::getBaseUrl());
                }
            }
        });
    }

    public function testBodyReplayUsesTheOriginalPositionAndSessionAfterSettingsChange()
    {
        $this->configured(function () {
            $body = new Stream(fopen('php://temp', 'w+'));
            $body->write('skip-body-bytes');
            $body->seek(5);
            $sent = [];
            $connect = new ConnectException('Cannot connect', new Request('PATCH', 'https://api.example.test'));
            $mock = new MockHandler([$connect, $this->mismatch(), new Response(200, [], '{}')]);
            Files::setHandler(function ($request, $options) use (&$sent, $mock) {
                $sent[] = ['request' => $request, 'body' => $request->getBody()->getContents()];
                Files::setSessionId('changed-session');
                Files::setWorkspaceId(1234);
                Files::setBaseUrl('https://changed.example.test');
                Files::setRegionRelocation(false);
                return $mock($request, $options);
            });
            Api::sendRequest('/file_actions/move/report%2Fdaily?mode=copy', 'PATCH', null, [
                'body' => $body, 'session_id' => 'call-session', 'workspace_id' => 23,
                'headers' => ['X-Files-Reauthentication' => 'caller-reauth'],
            ]);
            $this->assertSame(['body-bytes', 'body-bytes', 'body-bytes'], array_column($sent, 'body'));
            $this->assertSame('PATCH', $sent[2]['request']->getMethod());
            $this->assertSame($sent[0]['request']->getUri()->getPath(), $sent[2]['request']->getUri()->getPath());
            $this->assertSame('mode=copy', $sent[2]['request']->getUri()->getQuery());
            $this->assertSame('call-session', $sent[2]['request']->getHeaderLine('X-FilesAPI-Auth'));
            $this->assertSame('23', $sent[2]['request']->getHeaderLine('X-Files-Workspace-Id'));
            $this->assertSame('caller-reauth', $sent[2]['request']->getHeaderLine('X-Files-Reauthentication'));
        });
    }

    public function testConnectionFailuresAndRegionMovesShareOneRetryBudget()
    {
        $this->configured(function () {
            foreach ([new Response(200, [], '[]'), $this->mismatch('third.example.test')] as $final) {
                $sent = [];
                $connect = new ConnectException('Cannot connect', new Request('GET', 'https://api.example.test'));
                $mock = $this->record([$connect, $this->mismatch(), $connect, $final, new Response(200, [], '[]')], $sent);
                try {
                    ApiKey::all();
                    $this->assertSame(200, $final->getStatusCode());
                } catch (LockoutRegionMismatchException $error) {
                    $this->assertSame('third.example.test', $error->getData()->host);
                }
                $this->assertCount(4, $sent);
                $this->assertCount(1, $mock);
                $this->assertSame(['api.example.test', 'api.example.test', 'region.example.test', 'region.example.test'], array_map(function ($send) {
                    return $send['request']->getUri()->getHost();
                }, $sent));
            }
            Files::$maxNetworkRetries = 0;
            foreach ([new Response(200, [], '[]'), $this->mismatch(), $connect] as $answer) {
                $sent = [];
                $mock = $this->record([$answer, new Response(200, [], '[]')], $sent);
                try {
                    ApiKey::all();
                    $this->assertSame(200, $answer->getStatusCode());
                } catch (FilesException $error) {
                    $this->assertTrue($answer instanceof ConnectException || $answer->getStatusCode() === 403);
                }
                $this->assertCount(1, $sent);
                $this->assertCount(1, $mock);
            }
        });
    }

    public function testCyclesConsumeTheSameBoundedBudget()
    {
        $this->configured(function () {
            $sent = [];
            $this->record([$this->mismatch(), $this->mismatch('api.example.test'), $this->mismatch(), $this->mismatch('api.example.test')], $sent);
            try {
                ApiKey::all();
                $this->fail('Expected the final mismatch');
            } catch (LockoutRegionMismatchException $error) {
                $this->assertSame('api.example.test', $error->getData()->host);
            }
            $this->assertCount(4, $sent);
        });
    }

    public function testDisabledAndRefusedPoliciesKeepTheOriginalError()
    {
        $this->configured(function () {
            $observed = [];
            $callback = function ($target, $current) use (&$observed) {
                $observed[] = [$target, $current];
                return false;
            };
            foreach ([false, $callback] as $policy) {
                Files::setRegionRelocation($policy);
                $sent = [];
                $this->record([$this->mismatch()], $sent);
                try {
                    ApiKey::all();
                    $this->fail('Expected a refused mismatch');
                } catch (LockoutRegionMismatchException $error) {
                    $this->assertSame('region.example.test:8443', $error->getData()->host);
                }
                $this->assertCount(1, $sent);
            }
            $this->assertSame([['https://region.example.test:8443', 'https://api.example.test']], $observed);
            $sent = [];
            $this->record([$this->mismatch(), new Response(200, [], '[]')], $sent);
            ApiKey::all([], ['region_relocation' => function () {
                return true;
            }]);
            $this->assertCount(2, $sent);
        });
    }

    public function testUnrelatedOrMalformedResponsesDoNotAuthorizeAnotherSend()
    {
        $this->configured(function () {
            $answers = [];
            foreach (
                ['http://region.example.test', 'https://user@region.example.test', 'region.example.test/path',
                'region.example.test?x=1', 'region.example.test#fragment', "region.example.test\\evil", 'region.example.test other', false, 0, 17, ['host']] as $host
            ) {
                $answers[] = $this->mismatch($host, ['X-Files-Host' => 'fallback.example.test']);
            }
            foreach ([false, 0, 'invalid-data', []] as $details) {
                $answers[] = new Response(403, ['X-Files-Host' => 'fallback.example.test'], json_encode([
                    'type' => 'not-authenticated/lockout-region-mismatch', 'data' => $details,
                ]));
            }
            $answers[] = new Response(403, ['X-Files-Host' => 'region.example.test'], '{"type":"not-authenticated/invalid-credentials"}');
            $answers[] = new Response(403, ['X-Files-Host' => 'region.example.test'], 'Permission denied');
            $answers[] = new Response(403, ['X-Files-Host' => ['one.example.test', 'two.example.test']], 'You have connected to a URL with other settings');
            $answers[] = new Response(401, ['X-Files-Host' => 'region.example.test'], '{"type":"not-authenticated/lockout-region-mismatch"}');
            foreach ($answers as $answer) {
                $sent = [];
                $this->record([$answer], $sent);
                $response = Api::sendRequest('/api_keys', 'GET', null, ['http_errors' => false]);
                $this->assertSame($answer->getStatusCode(), $response->status);
                $this->assertCount(1, $sent);
            }
        });
    }

    public function testForeignThenBackRedirectsNeverAuthorizeRelocation()
    {
        $this->configured(function () {
            foreach ([false, true] as $nested) {
                $sent = [];
                $this->record([new Response(302, ['Location' => 'https://storage.example.test/object']),
                    new Response(302, ['Location' => 'https://api.example.test/api/rest/v1/api_keys']), $this->mismatch()], $sent, $nested);
                try {
                    ApiKey::all();
                    $this->fail('Expected the untrusted mismatch');
                } catch (LockoutRegionMismatchException $error) {
                    $this->assertSame('Wrong region', $error->getError());
                }
                $this->assertCount(3, $sent);
                foreach (array_slice($sent, 1) as $send) {
                    foreach (['X-FilesAPI-Key', 'X-FilesAPI-Auth', 'X-Files-Workspace-Id'] as $name) {
                        $this->assertFalse($send['request']->hasHeader($name));
                    }
                }
            }
        });
    }

    public function testCallerMiddlewareCannotMoveCredentialsBeforeAMismatch()
    {
        $this->configured(function () {
            $sent = [];
            $mock = $this->record([$this->mismatch()], $sent);
            $stack = HandlerStack::create(Files::getHandler());
            $stack->push(Middleware::mapRequest(function ($request) {
                return $request->withUri($request->getUri()->withHost('foreign.example.test'));
            }));
            Files::setHandler($stack);
            try {
                ApiKey::all();
                $this->fail('Expected the foreign mismatch');
            } catch (LockoutRegionMismatchException $error) {
                $this->assertSame('Wrong region', $error->getError());
            }
            $this->assertCount(1, $sent);
            $this->assertFalse($sent[0]['request']->hasHeader('X-FilesAPI-Key'));
            $this->assertFalse($sent[0]['request']->hasHeader('X-Files-Workspace-Id'));
        });
    }

    public function testRedirectsUseTheApprovedOriginForRelativeLocations()
    {
        $this->configured(function () {
            $sent = [];
            $this->record([new Response(302, ['Location' => '/api/rest/v1/actual?kept=yes']), $this->mismatch(),
                new Response(302, ['Location' => '/api/rest/v1/done']), new Response(200, [], '[]')], $sent);
            ApiKey::all();
            $this->assertSame('https://region.example.test:8443/api/rest/v1/actual?kept=yes', (string) $sent[2]['request']->getUri());
            $this->assertSame('https://region.example.test:8443/api/rest/v1/done', (string) $sent[3]['request']->getUri());
            $this->assertSame('configured-key', $sent[3]['request']->getHeaderLine('X-FilesAPI-Key'));
        });
    }

    public function testSameOriginRedirectsKeepOrReplaceTheBodyAsTheirStatusRequires()
    {
        $this->configured(function () {
            foreach ([307, 303] as $status) {
                $body = new Stream(fopen('php://temp', 'w+'));
                $body->write('skip-body-bytes');
                $body->seek(5);
                $sent = [];
                $this->record([new Response($status, ['Location' => '/api/rest/v1/redirected']),
                    $this->mismatch(), new Response(200, [], '{}')], $sent);
                Api::sendRequest('/original', 'POST', null, ['body' => $body]);
                $expected = $status === 307 ? 'body-bytes' : '';
                $this->assertSame(['body-bytes', $expected, $expected], array_column($sent, 'body'));
                $this->assertSame(['POST', $status === 307 ? 'POST' : 'GET', $status === 307 ? 'POST' : 'GET'], array_map(function ($send) {
                    return $send['request']->getMethod();
                }, $sent));
                $this->assertSame('/api/rest/v1/redirected', $sent[2]['request']->getUri()->getPath());
            }
        });
    }

    public function testForwardOnlyRequestBodiesAreNotReplayed()
    {
        $this->configured(function () {
            $source = new Stream(fopen('php://temp', 'w+'));
            $source->write('body');
            $source->rewind();
            $sent = [];
            $this->record([$this->mismatch()], $sent);
            try {
                Api::sendRequest('/users/1', 'PATCH', null, ['body' => new NoSeekStream($source)]);
                $this->fail('Expected a nonreplayable mismatch');
            } catch (LockoutRegionMismatchException $error) {
                $this->assertSame('Wrong region', $error->getError());
            }
            $this->assertCount(1, $sent);
        });
    }

    public function testNonseekableResponseBytesSurviveRefusalAndDiscardClosesOnce()
    {
        $this->configured(function () {
            foreach ([false, true] as $follow) {
                $state = (object) ['closeCalls' => 0];
                $resource = fopen('php://temp', 'w+');
                $backing = new Stream($resource);
                $json = (string) $this->mismatch()->getBody();
                $backing->write($json);
                $backing->rewind();
                $source = new NoSeekStream($backing);
                // FnStream also calls close from its destructor; each fixture keeps its own real resource and counter.
                $stream = FnStream::decorate($source, ['close' => function () use ($state, $source) {
                    ++$state->closeCalls;
                    $source->close();
                }]);
                $sent = [];
                $answer = new Response(403, [], $stream);
                $this->record([$answer, new Response(200, [], '[]')], $sent, true);
                try {
                    try {
                        ApiKey::all([], ['region_relocation' => $follow]);
                        $this->assertTrue($follow);
                    } catch (LockoutRegionMismatchException $error) {
                        $this->assertFalse($follow);
                        $this->assertSame('Wrong region', $error->getError());
                    }
                    $this->assertSame($follow ? 1 : 0, $state->closeCalls);
                    $this->assertSame(!$follow, $stream->isReadable());
                    $this->assertSame(!$follow, is_resource($resource));
                    if (!$follow) {
                        $backing->rewind();
                        $this->assertSame($json, $backing->getContents());
                    }
                    $this->assertCount($follow ? 2 : 1, $sent);
                } finally {
                    $source->close();
                }
            }
        });
    }

    public function testExternalAndTransferUrlsDoNotEnrollInRegionRouting()
    {
        $this->configured(function () {
            foreach (['request', 'file', 'relative-file'] as $kind) {
                $sent = [];
                $this->record([$this->mismatch()], $sent);
                $url = 'https://storage.example.test/part?X-Amz-Signature=unchanged%2Bvalue';
                if ($kind === 'relative-file') {
                    $url = '/part?X-Amz-Signature=unchanged%2Bvalue';
                }
                try {
                    if ($kind !== 'request') {
                        Api::sendFile($url, 'PUT', 'bytes');
                    } else {
                        Api::sendRequest($url, 'GET');
                    }
                    $this->fail('Expected the external mismatch');
                } catch (LockoutRegionMismatchException $error) {
                    $this->assertSame('Wrong region', $error->getError());
                }
                $this->assertCount(1, $sent);
                $this->assertSame($kind === 'relative-file' ? 'https://api.example.test/api/rest/v1' . $url : $url, (string) $sent[0]['request']->getUri());
                $this->assertFalse($sent[0]['request']->hasHeader('X-FilesAPI-Key'));
            }
        });
    }

    public function testRelocationKeepsTheAuthenticationTheCallerHandlerActuallySent()
    {
        $this->configured(function () {
            $sent = [];
            $this->record([$this->mismatch(), new Response(200, [], '[]')], $sent);
            $stack = HandlerStack::create(Files::getHandler());
            $calls = 0;
            $stack->push(Middleware::mapRequest(function ($request) use (&$calls) {
                ++$calls;
                return $request->withHeader('X-FilesAPI-Key', $calls === 1 ? 'caller-key' : 'changed-key');
            }));
            Files::setHandler($stack);
            ApiKey::all();
            $this->assertCount(2, $sent);
            $this->assertSame('caller-key', $sent[0]['request']->getHeaderLine('X-FilesAPI-Key'));
            $this->assertSame('caller-key', $sent[1]['request']->getHeaderLine('X-FilesAPI-Key'));
        });
    }

    public function testFailedBodyPreparationPreservesItsCauseWithoutResending()
    {
        $this->configured(function () {
            $stream = new Stream(fopen('php://temp', 'w+'));
            $stream->write('body');
            $stream->rewind();
            $failure = new \RuntimeException('Cannot restore the caller body');
            $stream = FnStream::decorate($stream, ['seek' => function () use ($failure) {
                throw $failure;
            }]);
            $sent = [];
            $this->record([$this->mismatch()], $sent);
            try {
                Api::sendRequest('/users/1', 'PATCH', null, ['body' => $stream]);
                $this->fail('Expected the preparation failure');
            } catch (FilesException $error) {
                $this->assertSame($failure, $error->getPrevious());
                $this->assertSame($failure->getMessage(), $error->getMessage());
            }
            $this->assertCount(1, $sent);
        });
    }

    public function testFalseyOptionsKeepExistingAuthenticationAndResponseFallbacks()
    {
        $this->configured(function () {
            set_error_handler(function ($severity, $message, $file, $line) {
                throw new \ErrorException($message, 0, $severity, $file, $line);
            });
            try {
                foreach ([false, 0, '0', '', null] as $value) {
                    foreach ([null, 'configured-session'] as $session) {
                        Files::setSessionId($session);
                        $sent = [];
                        $this->record([new Response(200, [], '{"id":1}')], $sent);
                        $response = Api::sendSingleRequest('/api_keys', 'GET', null, [
                            'api_key' => $value, 'session_id' => $value, 'headers' => $value, 'sink' => null,
                        ]);
                        $this->assertSame(1, $response->data->id);
                        $this->assertSame($session ? '' : 'configured-key', $sent[0]['request']->getHeaderLine('X-FilesAPI-Key'));
                        $this->assertSame($session ? $session : '', $sent[0]['request']->getHeaderLine('X-FilesAPI-Auth'));
                    }
                }
                $sink = fopen('php://temp', 'w+');
                try {
                    $sent = [];
                    $this->record([new Response(200, [], '{"id":1}')], $sent);
                    $response = Api::sendSingleRequest('/api_keys', 'GET', null, ['sink' => $sink]);
                    $this->assertFalse(property_exists($response, 'data'));
                    $this->assertTrue(is_resource($sink));
                } finally {
                    fclose($sink);
                }
            } finally {
                restore_error_handler();
            }
        });
    }

    public function testOwnedPartLoopCanDisableTransportConnectionRetries()
    {
        $this->configured(function () {
            $sent = [];
            $url = 'https://storage.example.test/part?X-Amz-Signature=unchanged';
            $error = new ConnectException('Cannot connect', new Request('PUT', $url));
            $mock = $this->record([$error, new Response(200)], $sent);
            try {
                Api::sendFile($url, 'PUT', 'bytes', [], false);
                $this->fail('Expected the single-attempt connection failure');
            } catch (\Files\Exception\ApiConnectException $caught) {
                $this->assertSame('Cannot connect', $caught->getMessage());
            }
            $this->assertCount(1, $sent);
            $this->assertCount(1, $mock);
        });
    }

    public function testCallerResponseSinksAreNeitherReplayedNorClosedByRegionHandling()
    {
        $this->configured(function () {
            foreach ([false, true] as $psrStream) {
                $resource = fopen('php://temp', 'w+');
                $stream = new Stream($resource);
                $json = (string) $this->mismatch()->getBody();
                $stream->write($json);
                $stream->rewind();
                $sends = 0;
                Files::setHandler(function ($request, $options) use (&$sends, $stream) {
                    ++$sends;
                    return \GuzzleHttp\Promise\Create::promiseFor(new Response(403, [], $stream));
                });
                try {
                    Api::sendRequest('/api_keys', 'GET', null, ['sink' => $psrStream ? $stream : $resource]);
                    $this->fail('Expected the original sink response');
                } catch (LockoutRegionMismatchException $error) {
                    $this->assertSame('Wrong region', $error->getError());
                }
                $this->assertSame(1, $sends);
                $this->assertTrue(is_resource($resource));
                $stream->rewind();
                $this->assertSame($json, $stream->getContents());
                $stream->close();
            }
        });
    }

    public function testCallerPromiseRejectionsKeepTheirFailureWithoutNewRetries()
    {
        $this->configured(function () {
            $sends = 0;
            Files::setHandler(function () use (&$sends) {
                ++$sends;
                return new \GuzzleHttp\Promise\RejectedPromise('caller stopped');
            });
            try {
                ApiKey::all();
                $this->fail('Expected the caller rejection');
            } catch (FilesException $error) {
                $this->assertNotFalse(strpos($error->getMessage(), 'caller stopped'));
            }
            $this->assertSame(1, $sends);
        });
    }

    public function testMalformedRequestTargetsCannotDispatchCredentials()
    {
        $this->configured(function () {
            foreach ([[123, 'GET'], ['/api_keys', 123], [false, 'GET']] as $target) {
                foreach ([null, ['limit' => 1]] as $params) {
                    $sent = [];
                    $mock = $this->record([new Response(200, [], '[]')], $sent);
                    try {
                        Api::sendRequest($target[0], $target[1], $params);
                        $this->fail('Expected an invalid request target');
                    } catch (\Files\Exception\InvalidParameterException $error) {
                        $this->assertCount(0, $sent);
                        $this->assertCount(1, $mock);
                    }
                }
                $sent = [];
                $mock = $this->record([new Response(200, [], '{}')], $sent);
                try {
                    Api::sendFile($target[0], $target[1], 'bytes', ['X-FilesAPI-Key' => 'caller-key']);
                    $this->fail('Expected an invalid direct transfer target');
                } catch (\Files\Exception\InvalidParameterException $error) {
                    $this->assertCount(0, $sent);
                    $this->assertCount(1, $mock);
                }
            }
            foreach ([123, true] as $baseUrl) {
                Files::setBaseUrl($baseUrl);
                $sent = [];
                $mock = $this->record([new Response(200, [], '[]')], $sent);
                try {
                    Api::sendRequest('/api_keys', 'GET');
                    $this->fail('Expected an invalid API base URL');
                } catch (\Files\Exception\ConfigurationException $error) {
                    $this->assertCount(0, $sent);
                    $this->assertCount(1, $mock);
                }
            }
        });
    }

    public function testSinglePageBoundaryDoesNotFollowCursors()
    {
        $this->configured(function () {
            $sent = [];
            $mock = $this->record([new Response(200, ['X-Files-Cursor' => 'next'], '[{"id":1}]'),
                new Response(200, [], '[{"id":2}]')], $sent);
            $page = Api::sendSingleRequest('/api_keys', 'GET');
            $this->assertSame(1, $page->data[0]->id);
            $this->assertSame(['next'], Api::headerValues($page->headers, 'X-Files-Cursor'));
            $this->assertCount(1, $sent);
            $this->assertCount(1, $mock);
        });
    }
}
