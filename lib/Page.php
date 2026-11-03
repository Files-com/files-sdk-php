<?php

namespace Files;

/**
 * One page of a Files.com list, plus what is needed to fetch the pages after it.
 *
 * Get one from a generated list method's page counterpart, such as ApiKey::listPage() or
 * Folder::listForPage(). foreach visits only the items on this page. autoPagingIterator()
 * visits every item from this page onward, fetching later pages as it reaches them.
 * nextPage() fetches the following page yourself, and restart() fetches the first page
 * again. A page never changes after it is fetched, so a failed request leaves it usable. A
 * page whose cursor already led to it or an earlier page is still read, but following that
 * cursor raises Exception\ApiBadResponseException instead of repeating pages.
 *
 * Every page is requested with the parameters and options of the call that fetched its first page, and no page
 * number is added. Settings those options do not override, such as the API key Files::setApiKey() sets, are read as
 * each page is requested. Files::$autoPaginate does not affect pages.
 *
 * @template T
 * @implements \IteratorAggregate<int, T>
 */
class Page implements \IteratorAggregate, \Countable
{
    /** @var T[] */
    private $items = [];

    /** @var string|null */
    private $nextCursor = null;

    /** @var string */
    private $path;

    /** @var array the list parameters, without a cursor, sent for every page */
    private $params;

    /** @var array */
    private $options;

    /** @var class-string<T> */
    private $itemClass;

    /** @var int this page's place in its line of pages: 0 for the page that restart() would fetch */
    private $position = 0;

    /** @var Util\CursorTrail the cursors followed to reach this page */
    private $trail;

    private function __construct($path, $params, $options, $itemClass, Util\CursorTrail $trail)
    {
        $this->path = $path;
        $this->params = $params;
        $this->options = $options;
        $this->itemClass = $itemClass;
        $this->trail = $trail;
    }

    /**
     * @internal Generated page methods call this; use them instead.
     *
     * @param string $path
     * @param array $params the list parameters; a 'cursor' starts from that page
     * @param array $options
     * @param class-string<T> $itemClass
     * @return Page<T>
     */
    public static function fetch($path, $params, $options, $itemClass)
    {
        $params = $params ?: [];
        $cursor = isset($params['cursor']) && $params['cursor'] !== '' ? $params['cursor'] : null;
        unset($params['cursor']);

        $unfetched = new self($path, $params, $options ?: [], $itemClass, new Util\CursorTrail($cursor));
        return $unfetched->fetchWith($cursor);
    }

    /**
     * The items on this page only.
     *
     * @return T[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * The opaque cursor for the next page, or null on the last page. Pass it back unchanged as the
     * 'cursor' parameter of the same page method to continue from there later.
     *
     * @return string|null
     */
    public function getNextCursor()
    {
        return $this->nextCursor;
    }

    /**
     * Whether the API reported another page. That page may be empty and still not be the last.
     *
     * @return bool
     */
    public function hasNextPage()
    {
        return $this->nextCursor !== null;
    }

    /**
     * Fetches the page after this one with the same parameters and options.
     *
     * @return Page<T>|null the next page, or null without a request when this is the last page
     * @throws Exception\ApiBadResponseException when the API returned a cursor that already led to
     *     this page or an earlier one, since following it would repeat those pages forever
     * @throws Exception\FilesException when the page cannot be fetched
     */
    public function nextPage()
    {
        if (!$this->hasNextPage()) {
            return null;
        }

        if ($this->trail->reached($this->nextCursor, $this->position)) {
            throw new Exception\ApiBadResponseException(Api::REPEATED_CURSOR_MESSAGE, 0);
        }

        $next = $this->fetchWith($this->nextCursor);
        // Only a page that was fetched and is valid extends the trail; a failed attempt leaves none.
        $next->trail = $this->trail->extend($this->nextCursor, $this->position);
        $next->position = $this->position + 1;

        return $next;
    }

    /**
     * Fetches the first page again with the same parameters and options, dropping any cursor.
     *
     * @return Page<T>
     * @throws Exception\FilesException
     */
    public function restart()
    {
        $unfetched = new self($this->path, $this->params, $this->options, $this->itemClass, new Util\CursorTrail());
        return $unfetched->fetchWith(null);
    }

    /**
     * Yields every item from this page onward. Each later page is requested only when iteration
     * reaches it, so breaking out of the loop sends no further requests.
     *
     * @return \Generator<int, T>
     * @throws Exception\FilesException when a later page cannot be fetched
     */
    public function autoPagingIterator()
    {
        return self::itemsFrom($this);
    }

    /**
     * Iterates the items on this page only.
     *
     * @return \ArrayIterator<int, T>
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * The number of items on this page.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->items);
    }

    // A static generator refers only to the page it is reading, so earlier pages can be freed.
    private static function itemsFrom(Page $page)
    {
        do {
            foreach ($page->items as $item) {
                yield $item;
            }
            $page = $page->nextPage();
        } while ($page !== null);
    }

    // Fetches the page for $cursor with this page's parameters and options, returning it only when all of it is valid:
    // a JSON array of JSON objects.
    private function fetchWith($cursor)
    {
        $params = $this->params;
        if ($cursor !== null) {
            $params['cursor'] = $cursor;
        }

        $response = Api::sendSingleRequest($this->path, 'GET', $params, $this->options);
        $itemClass = $this->itemClass;
        $items = [];
        $data = isset($response->data) ? $response->data : null;
        foreach (Api::listEntries($data, $response->status) as $item) {
            $items[] = new $itemClass(Api::modelAttributes($item, $response->status), $this->options);
        }

        $page = clone $this;
        $page->items = $items;
        $page->nextCursor = Api::nextCursor($response);

        return $page;
    }
}
