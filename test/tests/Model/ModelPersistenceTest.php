<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\NotFoundException;
use Files\Model\ApiKey;
use Files\Model\File;
use Files\Model\FileAction;
use Files\Model\Group;
use Files\Model\Style;
use Files\Model\Workspace;
use Files\Model\ZipListEntry;
use Files\Test\UnboundTransportGuard;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ModelPersistenceTest extends TestCase
{
    use UnboundTransportGuard;

    private $history = [];

    public function testSaveCreatesThenUpdatesWithTheServersValues()
    {
        $this->respondWith([
            self::json(201, ['id' => 7, 'name' => 'Project Alpha']),
            self::json(200, ['id' => 7, 'name' => 'Project Beta (renamed)']),
        ]);

        $workspace = new Workspace(['name' => ' Project Alpha ']);
        $this->assertTrue($workspace->save());
        $this->assertSame([7, 'Project Alpha'], [$workspace->id, $workspace->name]);
        $this->assertSame([7, 'Project Alpha'], [$workspace->getId(), $workspace->getName()]);

        $workspace->name = 'Project Beta';
        $this->assertTrue($workspace->save());
        $this->assertSame('Project Beta (renamed)', $workspace->name);
        $this->assertSame('Project Beta (renamed)', $workspace->getName());

        $this->assertSame(
            [
                ['POST /api/rest/v1/workspaces', ['name' => ' Project Alpha ']],
                ['PATCH /api/rest/v1/workspaces/7', ['id' => 7, 'name' => 'Project Beta']],
            ],
            $this->sentRequests()
        );
    }

    public function testSaveSendsSuppliedFalseZeroAndEmptyValuesOnly()
    {
        $supplied = ['name' => 'Ops', 'ftp_permission' => false, 'notes' => '', 'workspace_id' => 0];
        $this->respondWith([self::json(201, ['id' => 9] + $supplied)]);

        $group = new Group($supplied);
        $this->assertTrue($group->save());

        $this->assertSame([['POST /api/rest/v1/groups', $supplied]], $this->sentRequests());
        $this->assertSame(
            [9, false, '', 0],
            [$group->getId(), $group->getFtpPermission(), $group->getNotes(), $group->getWorkspaceId()]
        );
        $this->assertNull($group->getSftpPermission());
    }

    public function testUpdateRefreshesTheObjectAndReturnsASeparateModelWithTheSameOptions()
    {
        $this->respondWith([
            self::json(200, ['id' => 5, 'name' => 'deploy (canonical)', 'workspace_id' => 99]),
            new Response(204),
            new Response(204),
        ]);

        $apiKey = new ApiKey(['id' => 5, 'name' => 'deploy'], ['workspace_id' => 3, 'api_key' => 'per-call-key']);
        $returned = $apiKey->update(['name' => 'deploy (renamed)']);

        $this->assertInstanceOf(ApiKey::class, $returned);
        $this->assertNotSame($apiKey, $returned);
        foreach ([$apiKey, $returned] as $model) {
            $this->assertSame([5, 'deploy (canonical)', 99], [$model->id, $model->getName(), $model->workspace_id]);
        }

        // A workspace_id attribute in the response is data, not the request options.
        $returned->delete();
        $apiKey->delete();
        $this->assertSame(
            ['PATCH /api/rest/v1/api_keys/5', 'DELETE /api/rest/v1/api_keys/5', 'DELETE /api/rest/v1/api_keys/5'],
            array_column($this->sentRequests(), 0)
        );
        foreach ($this->history as $transaction) {
            $this->assertSame('3', $transaction['request']->getHeaderLine('X-Files-Workspace-Id'));
            $this->assertSame('per-call-key', $transaction['request']->getHeaderLine('X-FilesAPI-Key'));
        }
    }

    public function testFailedUpdateLeavesTheObjectUnchanged()
    {
        $this->respondWith([
            self::json(404, ['type' => 'not-found', 'error' => 'Not Found', 'http-code' => 404]),
        ]);

        $workspace = new Workspace(['id' => 7, 'name' => 'Project Alpha']);
        try {
            $workspace->update(['name' => 'Project Beta']);
            $this->fail('Expected the update to fail');
        } catch (NotFoundException $error) {
            $this->assertSame('not-found', $error->getType());
        }

        $this->assertSame([7, 'Project Alpha'], [$workspace->id, $workspace->getName()]);
        $this->assertCount(1, $this->history);
    }

    public function successesThatAreNotJsonObjects()
    {
        return [
            'JSON null' => ['null'],
            'JSON array' => ['[]'],
            'JSON string' => ['"oops"'],
            'HTML' => ['<html>unexpected success</html>'],
            'empty body' => [''],
        ];
    }

    /**
     * @dataProvider successesThatAreNotJsonObjects
     */
    public function testSaveAndUpdateRejectASuccessThatIsNotAJsonObject($body)
    {
        $this->respondWith([new Response(201, [], $body), new Response(200, [], $body), new Response(200, [], $body)]);

        $draft = new Workspace(['name' => 'Unsaved draft'], ['workspace_id' => 3]);
        $workspace = new Workspace(['id' => 7, 'name' => 'Project Alpha'], ['workspace_id' => 3]);
        $attempts = [
            [201, $draft, function () use ($draft) {
                $draft->save();
            }],
            [200, $workspace, function () use ($workspace) {
                $workspace->update(['name' => 'Project Beta']);
            }],
            [200, $workspace, function () use ($workspace) {
                $workspace->save();
            }],
        ];
        foreach ($attempts as $attempt) {
            list($status, $model, $save) = $attempt;
            $before = [$model->id, $model->getName()];
            try {
                $save();
                $this->fail('Expected the response to be rejected');
            } catch (ApiBadResponseException $error) {
                $this->assertSame($status, $error->getCode());
            }
            $this->assertSame($before, [$model->id, $model->getName()]);
        }

        $this->assertSame([null, 'Unsaved draft'], [$draft->id, $draft->getName()]);
        $this->assertSame(
            ['POST /api/rest/v1/workspaces', 'PATCH /api/rest/v1/workspaces/7', 'PATCH /api/rest/v1/workspaces/7'],
            array_column($this->sentRequests(), 0)
        );
        foreach ($this->history as $transaction) {
            $this->assertSame('3', $transaction['request']->getHeaderLine('X-Files-Workspace-Id'));
        }
    }

    public function testGeneratedResultsAreBuiltOnlyFromJsonObjects()
    {
        $file = new File(['path' => 'archive.zip']);
        $findWorkspace = function () {
            Workspace::find(7);
        };
        $listWorkspaces = function () {
            Workspace::all();
        };
        $listZipEntries = function () use ($file) {
            $file->zipListContents();
        };
        $rejected = [
            'a model that is null' => ['null', $findWorkspace],
            'a foreign model that is HTML' => ['<html>unexpected success</html>', function () use ($file) {
                $file->copy(['destination' => 'copy.zip']);
            }],
            'a list that is null' => ['null', $listWorkspaces],
            'a list wrapped in an object' => ['{"wrapped": {"id": 8}}', $listWorkspaces],
            'a list with a null entry' => ['[{"id": 1}, null]', $listWorkspaces],
            'a member list wrapped in an object' => ['{"entries": {"path": "nested.txt"}}', $listZipEntries],
            'a member list with a number' => ['[1]', $listZipEntries],
        ];
        $responses = [];
        foreach ($rejected as $case) {
            $responses[] = new Response(200, [], $case[0]);
        }
        $responses[] = self::json(200, [['path' => 'a.txt'], new \stdClass()]);
        $this->respondWith($responses);

        $outcomes = [];
        foreach ($rejected as $name => $case) {
            try {
                $case[1]();
                $outcomes[$name] = 'accepted';
            } catch (\Exception $error) {
                $outcomes[$name] = get_class($error) . ' ' . $error->getCode();
            }
        }
        $expected = array_fill_keys(array_keys($rejected), ApiBadResponseException::class . ' 200');
        $this->assertSame($expected, $outcomes);

        // A member operation that returns a list builds one model per entry, even an empty one.
        $entries = $file->zipListContents();
        $this->assertContainsOnlyInstancesOf(ZipListEntry::class, $entries);
        $this->assertSame(['a.txt', null], [$entries[0]->getPath(), $entries[1]->getPath()]);
        $this->assertCount(count($responses), $this->history);
    }

    public function testUpdateTakesAnyJsonObjectAsTheSavedAttributes()
    {
        $this->respondWith([self::json(200, new \stdClass())]);

        $workspace = new Workspace(['id' => 7, 'name' => 'Project Alpha']);
        $returned = $workspace->update(['name' => 'Project Beta']);

        $this->assertInstanceOf(Workspace::class, $returned);
        $this->assertSame([$returned->id, $returned->name], [$workspace->id, $workspace->name]);
    }

    public function testPathKeyedAndUpdateOnlyModelsRefreshWithoutAnId()
    {
        $this->respondWith([
            self::json(200, ['path' => 'docs/a.txt', 'priority_color' => 'red', 'size' => 12]),
            self::json(200, ['path' => 'brand', 'id' => 4, 'thumbnail' => ['name' => 'logo.png']]),
        ]);

        $file = new File(['path' => 'docs/a.txt', 'size' => 12]);
        $file->update(['priority_color' => 'red']);
        $this->assertSame(['docs/a.txt', 'red', 12], [$file->path, $file->priority_color, $file->getSize()]);

        $style = new Style(['path' => 'brand']);
        $this->assertTrue($style->save());
        $this->assertSame([4, 'logo.png'], [$style->getId(), $style->thumbnail->name]);

        $this->assertSame(
            ['PATCH /api/rest/v1/files/docs%2Fa.txt', 'PATCH /api/rest/v1/styles/brand'],
            array_column($this->sentRequests(), 0)
        );
    }

    public function testForeignAndVoidResultsLeaveTheObjectUnchanged()
    {
        $this->respondWith([
            self::json(201, ['status' => 'enqueued', 'file_migration_id' => 1]),
            new Response(204),
        ]);

        $file = new File(['path' => 'docs/a.txt', 'size' => 12]);
        $action = $file->copy(['destination' => 'docs/b.txt']);
        $this->assertInstanceOf(FileAction::class, $action);
        $this->assertSame('enqueued', $action->status);

        $workspace = new Workspace(['id' => 7, 'name' => 'Project Alpha']);
        $this->assertNull($workspace->delete());

        $this->assertSame(['docs/a.txt', 12, null], [$file->path, $file->size, $file->status]);
        $this->assertSame([7, 'Project Alpha'], [$workspace->id, $workspace->name]);
    }

    // A body sent to a caller's sink is not the response's data, so no model can be built from it: each generated
    // result is the same ApiBadResponseException as any other unusable response, without a PHP warning or notice
    // first, and the object called on and the caller's stream are left as they were.
    public function testAResultSentToACallersSinkIsABadResponse()
    {
        $this->respondWith([
            self::json(200, ['id' => 7, 'name' => 'Project Beta']),
            self::json(200, ['status' => 'enqueued']),
            self::json(200, [['path' => 'a.txt']]),
            self::json(200, ['id' => 7, 'name' => 'Project Beta']),
            self::json(200, [['id' => 7, 'name' => 'Project Beta']]),
        ]);
        $sink = fopen('php://temp', 'w+');
        $options = ['sink' => $sink];
        $workspace = new Workspace(['id' => 7, 'name' => 'Project Alpha'], $options);
        $file = new File(['path' => 'archive.zip'], $options);
        $calls = [
            'an update of the object' => function () use ($workspace) {
                $workspace->update(['name' => 'Project Beta']);
            },
            'a member model' => function () use ($file) {
                $file->copy(['destination' => 'copy.zip']);
            },
            'a member list' => function () use ($file) {
                $file->zipListContents();
            },
            'a model' => function () use ($options) {
                Workspace::find(7, [], $options);
            },
            'a list' => function () use ($options) {
                Workspace::all([], $options);
            },
        ];

        set_error_handler(function ($severity, $message) {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity);
        }, E_WARNING | E_NOTICE);
        try {
            foreach ($calls as $case => $call) {
                try {
                    $call();
                    $this->fail('Expected the result to be rejected: ' . $case);
                } catch (ApiBadResponseException $error) {
                    $this->assertSame(200, $error->getCode(), $case);
                }
                $this->assertTrue(is_resource($sink), $case);
            }
        } finally {
            restore_error_handler();
            if (is_resource($sink)) {
                fclose($sink);
            }
        }

        $this->assertSame([7, 'Project Alpha'], [$workspace->id, $workspace->getName()]);
        $this->assertSame(['archive.zip', null], [$file->path, $file->status]);
        $this->assertCount(count($calls), $this->history);
    }

    private function respondWith(array $responses)
    {
        Files::setApiKey('test-key');
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));
        Files::setHandler($stack);
    }

    // Each request as [method and path, decoded JSON body].
    private function sentRequests()
    {
        return array_map(function ($transaction) {
            $request = $transaction['request'];
            return [
                $request->getMethod() . ' ' . $request->getUri()->getPath(),
                json_decode((string) $request->getBody(), true),
            ];
        }, $this->history);
    }

    private static function json($status, $data)
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
    }
}
