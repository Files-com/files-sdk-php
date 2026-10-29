<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiServerException;
use Files\Exception\ConfigurationException;
use Files\Exception\NotFoundException;
use Files\Exception\NotImplementedException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    public function testNativeReadsFollowEmptyPagesAndPreserveZeroNamedEntries()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor-Next' => '0'], '[{"path":"reports/zeta"}]'),
            new Response(200, ['x-files-cursor' => 'last'], '[]'),
            new Response(200, [], '[{"path":"reports/0"},{"path":"reports/alpha"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $context = stream_context_create(['files' => ['options' => [
                'api_key' => 'directory-context-key', 'workspace_id' => 123,
            ]]]);
            $directory = opendir('files:///reports', $context);
            try {
                $this->assertTrue(is_resource($directory));
                $this->assertCount(1, $requests);
                $this->assertSame('.', readdir($directory));
                $this->assertSame('..', readdir($directory));
                $this->assertSame('zeta', readdir($directory));
                $this->assertCount(1, $requests);
                $this->assertSame('0', readdir($directory));
                $this->assertSame('alpha', readdir($directory));
                $this->assertSame(false, readdir($directory));
                $this->assertSame(false, readdir($directory));
                $this->assertCount(3, $requests);
                foreach ($requests as $request) {
                    $this->assertSame('directory-context-key', $request['request']->getHeaderLine('X-FilesAPI-Key'));
                    $this->assertSame('123', $request['request']->getHeaderLine('X-Files-Workspace-Id'));
                    parse_str($request['request']->getUri()->getQuery(), $query);
                    $this->assertSame('1000', $query['per_page']);
                }
                parse_str($requests[1]['request']->getUri()->getQuery(), $query);
                $this->assertSame('0', $query['cursor']);
                parse_str($requests[2]['request']->getUri()->getQuery(), $query);
                $this->assertSame('last', $query['cursor']);
            } finally {
                closedir($directory);
            }
        });
    }

    public function testEmptyDirectoriesOpenButMissingDirectoriesRaiseTheirApiError()
    {
        $responses = [
            new Response(200, [], '[]'),
            new Response(404, [], '{"type":"not-found","error":"Not Found","http-code":404}'),
        ];
        $this->withWrapper(new MockHandler($responses), function () {
            $directory = opendir('files://empty');
            try {
                $this->assertSame('.', readdir($directory));
                $this->assertSame('..', readdir($directory));
                $this->assertSame(false, readdir($directory));
            } finally {
                closedir($directory);
            }
            try {
                opendir('files://missing');
                $this->fail('A missing directory must not look like an empty one');
            } catch (NotFoundException $error) {
                $this->assertSame(404, $error->getHttpCode());
            }
        });
    }

    public function testFailedLaterPageCanBeRetriedWithoutLosingTheDirectoryPosition()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor' => 'next'], '[{"path":"reports/first"}]'),
            new Response(500, [], '{"error":"temporary"}'),
            new Response(200, [], '[{"path":"reports/second"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $directory = opendir('files://reports');
            try {
                readdir($directory);
                readdir($directory);
                $this->assertSame('first', readdir($directory));
                try {
                    readdir($directory);
                    $this->fail('A failed page must not appear as EOF');
                } catch (ApiServerException $error) {
                    $this->assertCount(2, $requests);
                }
                $this->assertSame('second', readdir($directory));
                $this->assertSame(false, readdir($directory));
                $this->assertSame(
                    (string) $requests[1]['request']->getUri(),
                    (string) $requests[2]['request']->getUri()
                );
            } finally {
                closedir($directory);
            }
        });
    }

    public function testRewindRefreshesTheListingAndCloseDoesNotFetchUnusedPages()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor' => 'unused'], '[{"path":"reports/old"}]'),
            new Response(200, ['X-Files-Cursor' => 'also-unused'], '[{"path":"reports/new"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $directory = opendir('files://reports');
            readdir($directory);
            readdir($directory);
            $this->assertSame('old', readdir($directory));
            rewinddir($directory);
            $this->assertSame('.', readdir($directory));
            $this->assertSame('..', readdir($directory));
            $this->assertSame('new', readdir($directory));
            closedir($directory);
            $this->assertCount(2, $requests);
            $this->assertSame(
                (string) $requests[0]['request']->getUri(),
                (string) $requests[1]['request']->getUri()
            );
        });
    }

    public function testScandirAndSplIteratorsWorkWithAutomaticPaginationDisabled()
    {
        $handler = function ($request) {
            parse_str($request->getUri()->getQuery(), $query);
            return new FulfilledPromise(isset($query['cursor'])
                ? new Response(200, [], '[{"path":"reports/alpha"}]')
                : new Response(200, ['X-Files-Cursor-Next' => 'next'], '[{"path":"reports/zeta"}]'));
        };
        $this->withWrapper($handler, function () {
            Files::$autoPaginate = false;
            $this->assertSame(['.', '..', 'alpha', 'zeta'], scandir('files://reports'));
            $this->assertSame(['zeta', 'alpha', '..', '.'], scandir('files://reports', SCANDIR_SORT_DESCENDING));
            $names = [];
            foreach (new \DirectoryIterator('files://reports') as $entry) {
                if (!$entry->isDot()) {
                    $names[] = $entry->getFilename();
                }
            }
            $this->assertSame(['zeta', 'alpha'], $names);
        });
    }

    public function testSplChildMetadataPreservesLiteralReservedFilenameCharacters()
    {
        $names = ['hash#.txt', 'question?.txt', 'literal%20.txt', 'name@host.txt', 'space name.txt'];
        $handler = function ($request) use ($names) {
            $path = rawurldecode($request->getUri()->getPath());
            if ($path === '/api/rest/v1/folders/reports') {
                return new FulfilledPromise(new Response(200, [], json_encode(array_map(function ($name) {
                    return ['path' => 'reports/' . $name];
                }, $names))));
            }
            $filePath = substr($path, strlen('/api/rest/v1/file_actions/metadata/'));
            $this->assertTrue(in_array(substr($filePath, strlen('reports/')), $names, true));
            return new FulfilledPromise(new Response(200, [], json_encode([
                'path' => $filePath, 'type' => 'file', 'permissions' => 'r', 'size' => 1,
                'provided_mtime' => null, 'mtime' => null, 'created_at' => null,
            ])));
        };
        $this->withWrapper($handler, function () use ($names) {
            $found = [];
            foreach (new \DirectoryIterator('files://reports') as $entry) {
                if (!$entry->isDot()) {
                    $this->assertTrue($entry->isFile());
                    $found[] = $entry->getFilename();
                }
            }
            $this->assertSame($names, $found);
        });
    }

    public function testOpenDirectoriesBindAccountDefaultsAndRefuseChangedSitesBeforeFetching()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor' => 'next'], '[{"path":"reports/first"}]'),
            new Response(200, ['X-Files-Cursor' => 'last'], '[{"path":"reports/second"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            Files::setWorkspaceId(123);
            $directory = opendir('files://reports');
            try {
                readdir($directory);
                readdir($directory);
                $this->assertSame('first', readdir($directory));
                Files::setApiKey('another-account-key');
                Files::setWorkspaceId(456);
                $this->assertSame('second', readdir($directory));
                $this->assertSame('directory-test-key', $requests[1]['request']->getHeaderLine('X-FilesAPI-Key'));
                $this->assertSame('123', $requests[1]['request']->getHeaderLine('X-Files-Workspace-Id'));
                Files::setBaseUrl('https://another-site.example.test');
                try {
                    readdir($directory);
                    $this->fail('An open directory must not request a different site');
                } catch (ConfigurationException $error) {
                    $this->assertCount(2, $requests);
                }
            } finally {
                closedir($directory);
            }
        });
    }

    public function testNewGlobalSessionsCannotOverrideAnOpenDirectoryApiKey()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor' => 'next'], '[]'),
            new Response(200, [], '[{"path":"reports/wrong-account"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $context = stream_context_create(['files' => ['options' => ['api_key' => 'directory-context-key']]]);
            $directory = opendir('files://reports', $context);
            try {
                readdir($directory);
                readdir($directory);
                Files::setSessionId('another-session');
                try {
                    readdir($directory);
                    $this->fail('The API session fallback must not override the opened account');
                } catch (ConfigurationException $error) {
                    $this->assertCount(1, $requests);
                }
            } finally {
                closedir($directory);
            }
        });
    }

    public function testExplicitSessionAndWorkspaceOptionsIgnoreUnrelatedGlobalChanges()
    {
        $responses = [
            new Response(200, ['X-Files-Cursor' => 'next'], '[]'),
            new Response(200, [], '[{"path":"reports/file"}]'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $context = stream_context_create(['files' => ['options' => [
                'session_id' => 'directory-context-session', 'workspace_id' => null,
            ]]]);
            $directory = opendir('files://reports', $context);
            try {
                readdir($directory);
                readdir($directory);
                Files::setSessionId('another-session');
                Files::setApiKey('another-key');
                Files::setWorkspaceId(456);
                $this->assertSame('file', readdir($directory));
                $this->assertSame('directory-context-session', $requests[1]['request']->getHeaderLine('X-FilesAPI-Auth'));
                $this->assertSame('', $requests[1]['request']->getHeaderLine('X-Files-Workspace-Id'));
            } finally {
                closedir($directory);
            }
        });
    }

    public function testRepeatedCursorsAndMalformedPagesRaiseInsteadOfEndingTheDirectory()
    {
        $invalidResponses = [
            [new Response(200, ['X-Files-Cursor' => 'same'], '[]'),
             new Response(200, ['X-Files-Cursor' => 'same'], '[]')],
            [new Response(200, [], '{"path":"reports/not-a-list"}')],
            [new Response(200, [], '[{"path":"another-folder/file"}]')],
        ];
        foreach ($invalidResponses as $responses) {
            $this->withWrapper(new MockHandler($responses), function () {
                $directory = null;
                try {
                    $directory = opendir('files://reports');
                    while (readdir($directory) !== false) {
                    }
                    $this->fail('An invalid page must raise an API response error');
                } catch (ApiBadResponseException $error) {
                    $this->assertNotSame('', $error->getMessage());
                } finally {
                    if (is_resource($directory)) {
                        closedir($directory);
                    }
                }
            });
        }
    }

    public function testNativeCreationAcceptsZeroNamesAndRefusesUnsupportedRemovalAndModes()
    {
        $responses = [
            new Response(200, [], '{"path":"0","type":"directory"}'),
        ];
        $this->withWrapper(new MockHandler($responses), function (&$requests) {
            $context = stream_context_create(['files' => ['options' => ['api_key' => 'directory-context-key']]]);
            $this->assertTrue(mkdir('files://0', 0777, true, $context));
            $created = json_decode((string) $requests[0]['request']->getBody(), true);
            $this->assertSame(true, $created['mkdir_parents']);
            foreach ($requests as $request) {
                $this->assertSame('directory-context-key', $request['request']->getHeaderLine('X-FilesAPI-Key'));
            }
            try {
                rmdir('files://0', $context);
                $this->fail('rmdir must not emulate directory-only deletion with an unsafe generic delete');
            } catch (NotImplementedException $error) {
                $this->assertCount(1, $requests);
            }
            try {
                mkdir('files://reports/private', 0700);
                $this->fail('Unix permission modes must not be silently ignored');
            } catch (NotImplementedException $error) {
                $this->assertCount(1, $requests);
            }
        });
    }

    private function withWrapper($handler, $consumer)
    {
        $oldHandler = Files::getHandler();
        $oldApiKey = Files::getApiKey();
        $oldBaseUrl = Files::getBaseUrl();
        $oldSessionId = Files::getSessionId();
        $oldWorkspaceId = Files::getWorkspaceId();
        $oldAutoPaginate = Files::$autoPaginate;
        $requests = [];
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::history($requests));
        Files::setHandler($stack);
        Files::setApiKey('directory-test-key');
        Files::setBaseUrl('https://directory.example.test');
        Files::setSessionId(null);
        Files::setWorkspaceId(null);
        Files::$autoPaginate = true;
        StreamWrapper::register();
        try {
            $consumer($requests);
        } finally {
            StreamWrapper::unregister();
            Files::setHandler($oldHandler);
            Files::setApiKey($oldApiKey);
            Files::setBaseUrl($oldBaseUrl);
            Files::setSessionId($oldSessionId);
            Files::setWorkspaceId($oldWorkspaceId);
            Files::$autoPaginate = $oldAutoPaginate;
        }
    }
}
