<?php

namespace Files\Stream;

use Files\Api;
use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Exception\NotImplementedException;
use Files\Model\Folder;
use Files\StreamWrapper;
use Files\Util\PathUtil;

/**
 * @internal The native stream wrapper owns one directory handle.
 */
class DirectoryHandle
{
    private $path;
    private $requestContext;
    private $entries = [];
    private $position = 0;
    private $dotPosition = 0;
    private $nextCursor;
    private $requestedCursors = [];
    private $closed = false;

    public function __construct($path, $options)
    {
        if (!is_string($path)) {
            throw new InvalidParameterException('Files directory paths must be strings');
        }
        $this->path = trim($path, '/');
        $this->requestContext = new RequestContext($options);
        $this->loadPage(null);
    }

    public static function create($path, $recursive, $mode, $options)
    {
        if ($mode !== 0777) {
            throw new NotImplementedException('Files.com folders use API permissions rather than Unix permission modes');
        }
        Folder::create(StreamWrapper::apiPath($path), ['mkdir_parents' => $recursive], $options);
        clearstatcache();
        return true;
    }

    public static function remove($path, $options)
    {
        throw new NotImplementedException('Files.com does not provide an atomic directory-only removal; use the explicit File delete API');
    }

    public function read()
    {
        $this->requireOpen();
        if ($this->dotPosition < 2) {
            return $this->dotPosition++ === 0 ? '.' : '..';
        }

        while ($this->position === count($this->entries)) {
            if ($this->nextCursor === null) {
                return false;
            }
            $this->loadPage($this->nextCursor);
        }

        return $this->entries[$this->position++];
    }

    public function rewind()
    {
        $this->requireOpen();
        $this->loadPage(null);
        $this->dotPosition = 0;
        return true;
    }

    public function close()
    {
        $this->closed = true;
        $this->entries = [];
        $this->requestedCursors = [];
        $this->requestContext = null;
        $this->nextCursor = null;
        return true;
    }

    private function requireOpen()
    {
        if ($this->closed) {
            throw new InvalidParameterException('The directory handle is closed');
        }
    }

    private function loadPage($cursor)
    {
        // A failed page fetch does not advance the handle. Retrying readdir retries that page.
        if ($cursor !== null && isset($this->requestedCursors[$cursor])) {
            throw new ApiBadResponseException('The Files.com API repeated a directory cursor');
        }

        // 1,000 is the API's recommended page size, not a limit on the directory's total entries.
        $params = ['per_page' => 1000];
        if ($cursor !== null) {
            $params['cursor'] = $cursor;
        }
        $response = Api::sendSingleRequest('/folders/' . rawurlencode($this->path), 'GET', $params, $this->requestContext->options());
        if (!isset($response->data) || !is_array($response->data)) {
            throw new ApiBadResponseException('The Files.com API returned a directory listing that is not an array');
        }

        $entries = [];
        foreach ($response->data as $item) {
            if (!is_object($item) || !isset($item->path) || !is_string($item->path)) {
                throw new ApiBadResponseException('The Files.com API returned a directory entry without a path');
            }
            $entryPath = ltrim($item->path, '/');
            $separator = strrpos($entryPath, '/');
            $parent = $separator === false ? '' : substr($entryPath, 0, $separator);
            $name = $separator === false ? $entryPath : substr($entryPath, $separator + 1);
            if (
                $name === '' || $name === '.' || $name === '..' || strpos($name, "\0") !== false
                || !PathUtil::same($parent, $this->path)
            ) {
                throw new ApiBadResponseException('The Files.com API returned an entry outside the requested directory');
            }
            $entries[] = $name;
        }

        $nextCursor = null;
        foreach (['X-Files-Cursor-Next', 'X-Files-Cursor'] as $header) {
            $values = Api::headerValues($response->headers, $header);
            if (count($values) > 1) {
                throw new ApiBadResponseException('The Files.com API returned multiple directory cursors');
            }
            if ($values) {
                $value = $values[0];
                if (!is_string($value)) {
                    throw new ApiBadResponseException('The Files.com API returned a directory cursor that is not a string');
                }
                if (trim($value) !== '') {
                    $nextCursor = $value;
                    break;
                }
            }
        }

        if ($cursor === null) {
            $this->requestedCursors = [];
        } else {
            $this->requestedCursors[$cursor] = true;
        }
        $this->entries = $entries;
        $this->position = 0;
        $this->nextCursor = $nextCursor;
    }
}
