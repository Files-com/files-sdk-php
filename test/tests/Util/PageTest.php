<?php

declare(strict_types=1);

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\ApiConnectionException;
use Files\Exception\ApiServerException;
use Files\Model\ApiKey;
use Files\Model\File;
use Files\Model\Folder;
use Files\Test\UnboundTransportGuard;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use UnboundTransportGuard;

    private $history = [];
    private $mock;

    public function testAPageHoldsOneResponseOfTypedItemsAndItsCursor()
    {
        // Files::$autoPaginate is true by default; a page still sends a single request.
        $this->respondWith([self::page([1, 2], 'cursor-2'), self::page([3])]);

        $page = ApiKey::listPage(['per_page' => 2]);

        $this->assertCount(2, $page);
        $this->assertContainsOnlyInstancesOf(ApiKey::class, $page->getItems());
        $this->assertSame([1, 2], self::ids($page));
        $this->assertSame('cursor-2', $page->getNextCursor());
        $this->assertTrue($page->hasNextPage());
        $this->assertSame([['per_page' => '2']], $this->sentQueries());

        $next = $page->nextPage();
        $this->assertSame([3], self::ids($next));
        $this->assertNull($next->getNextCursor());
        $this->assertSame([1, 2], self::ids($page));
        $this->assertSame(
            [['per_page' => '2'], ['per_page' => '2', 'cursor' => 'cursor-2']],
            $this->sentQueries()
        );
    }

    public function testAutoPagingFollowsCursorsAcrossEmptyPagesOneRequestAtATime()
    {
        // Header names are compared without regard to case.
        $this->respondWith([
            self::page([1], 'cursor-2', 'X-Files-Cursor'),
            self::page([], 'cursor-3', 'x-files-cursor'),
            self::page([], 'cursor-4', 'X-FILES-CURSOR'),
            self::page([4]),
        ]);

        $items = ApiKey::listPage(['per_page' => 1])->autoPagingIterator();

        $this->assertInstanceOf(\Generator::class, $items);
        $this->assertSame(1, $items->current()->id);
        $this->assertCount(1, $this->history, 'Later pages are not requested before they are reached');
        $items->next();
        $this->assertSame(4, $items->current()->id);
        $items->next();
        $this->assertFalse($items->valid());

        $this->assertSame([null, 'cursor-2', 'cursor-3', 'cursor-4'], $this->sentCursors());
    }

    public function testBreakingOutOfAutoPagingSendsNoFurtherRequests()
    {
        $this->respondWith([self::page([1, 2], 'cursor-2'), self::page([3])]);

        $seen = [];
        foreach (ApiKey::listPage()->autoPagingIterator() as $apiKey) {
            $seen[] = $apiKey->id;
            if (count($seen) === 2) {
                break;
            }
        }

        $this->assertSame([1, 2], $seen);
        $this->assertCount(1, $this->history);
        $this->assertCount(1, $this->mock);
    }

    public function testAnEmptyLastPageEndsWithoutAnotherRequest()
    {
        $this->respondWith([self::page([])]);

        $page = ApiKey::listPage();

        $this->assertCount(0, $page);
        $this->assertFalse($page->hasNextPage());
        $this->assertNull($page->nextPage());
        $this->assertSame([], iterator_to_array($page->autoPagingIterator()));
        $this->assertCount(1, $this->history);
    }

    public function testAFailedPageRequestLeavesTheCurrentPageUsable()
    {
        $this->respondWith([
            self::page([1], 'cursor-2'),
            new Response(502, [], '<html>Bad Gateway</html>'),
            self::page([2]),
        ]);

        $page = ApiKey::listPage();
        try {
            $page->nextPage();
            $this->fail('Expected the page request to fail');
        } catch (ApiConnectionException $error) {
            $this->assertSame(502, $error->getCode());
            $this->assertInstanceOf(ApiServerException::class, $error);
        }

        $this->assertSame([1], self::ids($page));
        $this->assertSame('cursor-2', $page->getNextCursor());
        $this->assertSame([2], self::ids($page->nextPage()));
        $this->assertSame([null, 'cursor-2', 'cursor-2'], $this->sentCursors());
    }

    public function testRestartFetchesTheFirstPageWithTheOriginalParametersAndOptions()
    {
        Files::setWorkspaceId(123);
        try {
            $this->respondWith([
                self::page(['a.txt'], 'cursor-2'),
                self::page(['b.txt']),
                self::page(['first.txt'], 'cursor-1'),
            ]);

            $options = ['workspace_id' => 456, 'api_key' => 'per-call-key'];
            $filters = ['per_page' => 1, 'sort_by' => ['path' => 'asc']];
            $saved = Folder::listForPage('docs', $filters + ['cursor' => 'saved-cursor'], $options);
            $restarted = $saved->nextPage()->restart();

            $this->assertContainsOnlyInstancesOf(File::class, $restarted->getItems());
            $this->assertSame(['first.txt'], array_map(function ($file) {
                return $file->path;
            }, $restarted->getItems()));
            $this->assertSame('cursor-1', $restarted->getNextCursor());

            $sent = ['per_page' => '1', 'sort_by' => ['path' => 'asc'], 'path' => 'docs'];
            $this->assertSame(
                [$sent + ['cursor' => 'saved-cursor'], $sent + ['cursor' => 'cursor-2'], $sent],
                $this->sentQueries()
            );
            foreach ($this->history as $transaction) {
                $request = $transaction['request'];
                $this->assertSame('/api/rest/v1/folders/docs', $request->getUri()->getPath());
                $this->assertSame('456', $request->getHeaderLine('X-Files-Workspace-Id'));
                $this->assertSame('per-call-key', $request->getHeaderLine('X-FilesAPI-Key'));
            }
        } finally {
            Files::setWorkspaceId(null);
        }
    }

    public function cursorCycles()
    {
        return [
            'the cursor it was sent' => [[], [self::page([1], 'A'), self::page([2], 'A')], [1, 2], 2],
            'an earlier cursor' => [
                [], [self::page([1], 'A'), self::page([2], 'B'), self::page([3], 'A')], [1, 2, 3], 3,
            ],
            'the starting cursor' => [['cursor' => 'S'], [self::page([1], 'T'), self::page([2], 'S')], [1, 2], 2],
            'the opaque cursor 0' => [[], [self::page([1], '0'), self::page([2], '0')], [1, 2], 2],
        ];
    }

    /**
     * @dataProvider cursorCycles
     */
    public function testAUsedCursorIsReportedInsteadOfRepeatingPages($params, $responses, $ids, $requests)
    {
        // One more page than the traversal should reach, so repeating the cycle would show.
        $responses[] = self::page([99]);
        $this->respondWith($responses);

        $seen = [];
        try {
            foreach (ApiKey::listPage($params)->autoPagingIterator() as $apiKey) {
                $seen[] = $apiKey->id;
            }
            $this->fail('Expected the repeated cursor to be reported');
        } catch (ApiBadResponseException $error) {
            $this->assertNotFalse(strpos($error->getMessage(), 'already returned'));
        }

        // The page that returned the used cursor is still read; only following that cursor fails.
        $this->assertSame($ids, $seen);
        $this->assertCount($requests, $this->history);
    }

    public function nextCursorHeaders()
    {
        return [
            'X-Files-Cursor-Next, in any case' => [['x-files-cursor-next' => 'next-2'], ['next-2']],
            'X-Files-Cursor-Next rather than X-Files-Cursor' => [
                ['X-Files-Cursor-Next' => 'next-2', 'X-Files-Cursor' => 'legacy-2'], ['next-2'],
            ],
            'an empty X-Files-Cursor-Next, ending the list' => [
                ['X-Files-Cursor-Next' => '', 'X-Files-Cursor' => 'legacy-2'], [],
            ],
            'X-Files-Cursor without X-Files-Cursor-Next' => [['X-FILES-CURSOR' => 'legacy-2'], ['legacy-2']],
            'the opaque cursor 0' => [['X-Files-Cursor-Next' => '0'], ['0']],
        ];
    }

    /**
     * @dataProvider nextCursorHeaders
     */
    public function testAPageFollowsTheNextCursorHeaderWheneverItIsPresent($headers, $followed)
    {
        $responses = [new Response(200, $headers + ['Content-Type' => 'application/json'], '[{"id": 1}]')];
        foreach ($followed as $index => $cursor) {
            $responses[] = self::page([$index + 2]);
        }
        // The page that following any other cursor would reach.
        $responses[] = self::page([99]);
        $this->respondWith($responses);

        $page = ApiKey::listPage();

        $this->assertSame($followed ? $followed[0] : null, $page->getNextCursor());
        $this->assertSame(range(1, count($followed) + 1), array_map(function ($apiKey) {
            return $apiKey->id;
        }, iterator_to_array($page->autoPagingIterator(), false)));
        $this->assertSame(array_merge([null], $followed), $this->sentCursors());
        $this->assertCount(1, $this->mock);
    }

    public function testRestartForgetsTheCursorsOfTheEarlierTraversal()
    {
        $this->respondWith([
            self::page([1], 'A'),
            self::page([2], 'A'),
            self::page([1], 'A'),
            self::page([2]),
        ]);

        $second = ApiKey::listPage()->nextPage();
        try {
            $second->nextPage();
            $this->fail('Expected the repeated cursor to be reported');
        } catch (ApiBadResponseException $error) {
        }

        $this->assertSame([2], self::ids($second->restart()->nextPage()));
        $this->assertSame([null, 'A', null, 'A'], $this->sentCursors());
    }

    public function firstBranchEndings()
    {
        return [
            'completed' => [self::page([3])],
            'rejected' => [new Response(200, ['Content-Type' => 'application/json'], '[null]')],
        ];
    }

    /**
     * @dataProvider firstBranchEndings
     */
    public function testARefetchedPageFollowsOnlyItsOwnCursors($firstBranchEnd)
    {
        $this->respondWith([
            self::page([1], 'A'),
            self::page([2], 'B'),
            $firstBranchEnd,
            self::page([2], 'C'),
            self::page([4], 'B'),
            self::page([5]),
        ]);

        $first = ApiKey::listPage();
        try {
            $first->nextPage()->nextPage();
        } catch (ApiBadResponseException $error) {
        }

        // Fetching A again now continues with C, then B: a new line of pages that has not used B.
        $refetched = $first->nextPage();
        $this->assertSame('C', $refetched->getNextCursor());
        $this->assertSame([2, 4, 5], array_map(function ($apiKey) {
            return $apiKey->id;
        }, iterator_to_array($refetched->autoPagingIterator(), false)));
        $this->assertSame([null, 'A', 'B', 'A', 'C', 'B'], $this->sentCursors());
    }

    public function itemsThatAreNotJsonObjects()
    {
        return [
            'null' => ['[null]'],
            'an array' => ['[[]]'],
            'a number' => ['[1]'],
            'a string' => ['["oops"]'],
            'a later item' => ['[{"id": 2}, null]'],
        ];
    }

    /**
     * @dataProvider itemsThatAreNotJsonObjects
     */
    public function testAListItemThatIsNotAJsonObjectIsABadResponse($body)
    {
        $this->respondWith([
            self::page([1], 'cursor-2'),
            new Response(200, ['Content-Type' => 'application/json', 'X-Files-Cursor' => 'cursor-3'], $body),
            new Response(200, ['Content-Type' => 'application/json'], '[{}, {"id": 2}]'),
        ]);

        $page = ApiKey::listPage();
        try {
            $page->nextPage();
            $this->fail('Expected the list item to be rejected');
        } catch (ApiBadResponseException $error) {
            $this->assertSame(200, $error->getCode());
        }

        $this->assertSame([1], self::ids($page));
        $this->assertSame('cursor-2', $page->getNextCursor());
        // An empty JSON object is still a list item.
        $this->assertSame([null, 2], self::ids($page->nextPage()));
    }

    public function testAListResponseThatIsNotAnArrayIsABadResponse()
    {
        $this->respondWith([new Response(200, ['Content-Type' => 'application/json'], '{"id": 1}')]);

        $this->expectException(ApiBadResponseException::class);
        ApiKey::listPage();
    }

    private function respondWith(array $responses)
    {
        Files::setApiKey('test-key');
        $this->history = [];
        $this->mock = new MockHandler($responses);
        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->history));
        Files::setHandler($stack);
    }

    private function sentQueries()
    {
        return array_map(function ($transaction) {
            parse_str($transaction['request']->getUri()->getQuery(), $query);
            return $query;
        }, $this->history);
    }

    private function sentCursors()
    {
        return array_map(function ($query) {
            return isset($query['cursor']) ? $query['cursor'] : null;
        }, $this->sentQueries());
    }

    // A list response of items with these ids (or paths), continued by $cursor when it is given.
    private static function page(array $keys, $cursor = null, $cursorHeader = 'X-Files-Cursor')
    {
        $headers = ['Content-Type' => 'application/json'];
        if ($cursor !== null) {
            $headers[$cursorHeader] = $cursor;
        }
        $items = array_map(function ($key) {
            return is_int($key) ? ['id' => $key] : ['path' => $key];
        }, $keys);

        return new Response(200, $headers, json_encode($items));
    }

    private static function ids($page)
    {
        return array_map(function ($item) {
            return $item->id;
        }, iterator_to_array($page, false));
    }
}
