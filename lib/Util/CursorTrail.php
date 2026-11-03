<?php

namespace Files\Util;

/**
 * The cursors followed to reach one line of pages, each with the position of the page it fetched.
 *
 * Following pages one after another extends one trail in place. Fetching the next page again
 * from an earlier page starts a trail of its own, holding only that page's part, so a line of
 * pages never sees the cursors of another. A trail holds cursors only, never pages or items.
 *
 * @internal Page uses this to refuse cursors that would repeat pages.
 */
final class CursorTrail
{
    /** @var array cursor => position of the page it fetched */
    private $positions = [];

    /** @var int position of the last page on this trail */
    private $lastPosition = 0;

    /**
     * @param string|null $startCursor the cursor that fetched the first page, if any
     */
    public function __construct($startCursor = null)
    {
        if ($startCursor !== null) {
            $this->positions[$startCursor] = 0;
        }
    }

    /**
     * Whether $cursor fetched the page at $position or a page before it on this trail.
     */
    public function reached($cursor, $position)
    {
        return isset($this->positions[$cursor]) && $this->positions[$cursor] <= $position;
    }

    /**
     * Records that $cursor fetched the page after the one at $position, and returns the trail of
     * that new page: this one, if $position was its last page, or else a new one.
     */
    public function extend($cursor, $position)
    {
        $trail = $this;
        if ($position !== $this->lastPosition) {
            $trail = new self();
            $trail->positions = array_filter($this->positions, function ($reachedAt) use ($position) {
                return $reachedAt <= $position;
            });
        }

        $trail->positions[$cursor] = $position + 1;
        $trail->lastPosition = $position + 1;

        return $trail;
    }
}
