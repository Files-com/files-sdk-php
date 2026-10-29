<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\FileActionException;
use Files\Exception\FilesException;
use Files\Exception\InvalidParameterException;
use Files\Model\File;
use Files\Model\FileAction;
use Files\Model\FileMigration;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FileActionTest extends TestCase
{
    private function response($data)
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($data));
    }

    private function withResponses($responses, $run)
    {
        $originalHandler = Files::getHandler();
        $originalUrl = Files::getBaseUrl();
        $originalKey = Files::getApiKey();
        $originalSession = Files::getSessionId();
        $originalWorkspace = Files::getWorkspaceId();
        $history = [];
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $handler->push(Middleware::history($history));
        Files::setHandler($handler);
        Files::setBaseUrl('https://actions.example.test');
        Files::setApiKey('test-key');
        Files::setSessionId(null);
        Files::setWorkspaceId(null);
        try {
            $run($history, $mock);
        } finally {
            Files::setHandler($originalHandler);
            Files::setBaseUrl($originalUrl);
            Files::setApiKey($originalKey);
            Files::setSessionId($originalSession);
            Files::setWorkspaceId($originalWorkspace);
        }
    }

    public function testAnAcceptedCopyReturnsBeforeTheExplicitWaitPollsItsMigration()
    {
        $this->withResponses([
            $this->response(['status' => 'pending', 'file_migration_id' => 7]),
            $this->response(['id' => 7, 'status' => 'processing', 'files_moved' => 1]),
            $this->response(['id' => 7, 'status' => 'completed', 'files_moved' => 2]),
        ], function (&$history, $mock) {
            $file = new File(['path' => 'reports/a.csv'], [
                'session_id' => 'object-session',
                'workspace_id' => 42,
                'headers' => ['X-Custom' => 'object-header'],
                'timeout' => 0.25,
            ]);
            $action = $file->copy(['destination' => 'reports/b.csv']);
            $this->assertInstanceOf(FileAction::class, $action);
            $this->assertSame('pending', $action->getStatus());
            $this->assertCount(1, $history);
            $this->assertCount(2, $mock);

            $seen = [];
            $migration = $action->waitForCompletion(['interval' => 0.001, 'progress' => function ($migration) use (&$seen) {
                $seen[] = $migration->getStatus();
                // A callback can edit a model, but it cannot confirm completion for the API.
                $migration->status = 'completed';
            }]);
            $this->assertInstanceOf(FileMigration::class, $migration);
            $this->assertSame('completed', $migration->getStatus());
            $this->assertSame(2, $migration->getFilesMoved());
            $this->assertSame(['processing', 'completed'], $seen);
            $this->assertSame(['POST', 'GET', 'GET'], array_map(function ($entry) {
                return $entry['request']->getMethod();
            }, $history));
            $this->assertSame([
                '/api/rest/v1/file_actions/copy/reports%2Fa.csv',
                '/api/rest/v1/file_migrations/7',
                '/api/rest/v1/file_migrations/7',
            ], array_map(function ($entry) {
                return $entry['request']->getUri()->getPath();
            }, $history));
            $this->assertSame(['destination' => 'reports/b.csv', 'path' => 'reports/a.csv'], json_decode((string) $history[0]['request']->getBody(), true));
            foreach ($history as $entry) {
                $this->assertSame('object-session', $entry['request']->getHeaderLine('X-FilesAPI-Auth'));
                $this->assertSame('', $entry['request']->getHeaderLine('X-FilesAPI-Key'));
                $this->assertSame('42', $entry['request']->getHeaderLine('X-Files-Workspace-Id'));
                $this->assertSame('object-header', $entry['request']->getHeaderLine('X-Custom'));
                $this->assertSame(0.25, $entry['options']['timeout']);
            }
            $this->assertCount(0, $mock);
        });
    }

    public function testOnlyAnExactlyCompletedActionCanFinishWithoutAMigration()
    {
        $this->withResponses([], function (&$history) {
            $this->assertNull((new FileAction(['status' => 'completed']))->waitForCompletion());
            foreach ([['status' => 'pending'], ['status' => 'complete'], [], ['status' => 'completed', 'file_migration_id' => '7'], ['file_migration_id' => 0]] as $attributes) {
                $action = new FileAction($attributes);
                try {
                    $action->waitForCompletion();
                    $this->fail('An unobservable action must not be reported as completed');
                } catch (FileActionException $error) {
                    $this->assertSame($action, $error->getFileAction());
                    $this->assertNull($error->getFileMigration());
                }
            }
            $this->assertCount(0, $history);
        });
    }

    public function testFailedCancelledUnknownAndMismatchedMigrationsDoNotReportSuccess()
    {
        $cases = [
            ['id' => 7, 'status' => 'failed', 'failure_message' => 'disk full'],
            ['id' => 7, 'status' => 'cancelled', 'failure_message' => 'stopped by owner'],
            ['id' => 7, 'status' => 'complete'],
            ['id' => 8, 'status' => 'completed'],
            [],
        ];
        foreach ($cases as $data) {
            $this->withResponses([$this->response($data)], function (&$history) use ($data) {
                // A migration ID remains authoritative even if the action's own status says completed.
                $action = new FileAction(['status' => 'completed', 'file_migration_id' => 7]);
                try {
                    $action->waitForCompletion();
                    $this->fail('The migration did not confirm completion');
                } catch (FileActionException $error) {
                    $this->assertSame($action, $error->getFileAction());
                    if (isset($data['failure_message'])) {
                        $this->assertNotFalse(strpos($error->getMessage(), $data['failure_message']));
                        $this->assertSame($data['status'], $error->getFileMigration()->getStatus());
                    }
                }
                $this->assertCount(1, $history);
            });
        }
    }

    public function testAPollFailurePropagatesUnchanged()
    {
        $failure = new FilesException('The migration lookup failed');
        $this->withResponses([$failure], function (&$history) use ($failure) {
            try {
                (new FileAction(['file_migration_id' => 7]))->waitForCompletion();
                $this->fail('The poll failure must propagate');
            } catch (FilesException $error) {
                $this->assertSame($failure, $error);
            }
            $this->assertCount(1, $history);
        });
    }

    public function testThrowingFromProgressStopsWaitingAndPreservesTheException()
    {
        $cancelled = new \RuntimeException('Cancelled by caller');
        $this->withResponses([
            $this->response(['id' => 7, 'status' => 'processing']),
            $this->response(['id' => 7, 'status' => 'completed']),
        ], function (&$history, $mock) use ($cancelled) {
            try {
                (new FileAction(['file_migration_id' => 7]))->waitForCompletion(['progress' => function () use ($cancelled) {
                    throw $cancelled;
                }]);
                $this->fail('The cancellation must propagate');
            } catch (\RuntimeException $error) {
                $this->assertSame($cancelled, $error);
            }
            $this->assertCount(1, $history);
            $this->assertCount(1, $mock);
        });
    }

    public function testThePollingTimeoutBoundsTheSleepAndDoesNotSendAnotherPoll()
    {
        $this->withResponses([
            $this->response(['id' => 7, 'status' => 'processing']),
            $this->response(['id' => 7, 'status' => 'completed']),
        ], function (&$history, $mock) {
            $started = microtime(true);
            try {
                (new FileAction(['file_migration_id' => 7]))->waitForCompletion(['interval' => 5, 'timeout' => 0.02]);
                $this->fail('The wait must time out');
            } catch (FileActionException $error) {
                $this->assertNotFalse(strpos($error->getMessage(), 'timed out'));
                $this->assertSame('processing', $error->getFileMigration()->getStatus());
            }
            $this->assertGreaterThanOrEqual(0.015, microtime(true) - $started);
            $this->assertLessThan(1.0, microtime(true) - $started);
            $this->assertCount(1, $history);
            $this->assertCount(1, $mock);
        });
    }

    public function testAResponseArrivingAfterThePollingTimeoutDoesNotConfirmCompletion()
    {
        $this->withResponses([function () {
            usleep(20000);
            return $this->response(['id' => 7, 'status' => 'completed']);
        }], function (&$history) {
            try {
                (new FileAction(['file_migration_id' => 7]))->waitForCompletion(['timeout' => 0.005]);
                $this->fail('The expired wait must time out');
            } catch (FileActionException $error) {
                $this->assertNotFalse(strpos($error->getMessage(), 'timed out'));
            }
            $this->assertCount(1, $history);
        });
    }

    public function testInvalidWaitOptionsAreRejectedBeforePolling()
    {
        $this->withResponses([], function (&$history) {
            foreach ([false, ['interval' => 0], ['interval' => INF], ['interval' => '1'], ['timeout' => -1], ['timeout' => true], ['progress' => 'no_such_callback']] as $options) {
                try {
                    (new FileAction(['file_migration_id' => 7]))->waitForCompletion($options);
                    $this->fail('The wait option must be rejected');
                } catch (InvalidParameterException $error) {
                    $this->assertNotSame('', $error->getMessage());
                }
            }
            $this->assertCount(0, $history);
        });
    }
}
