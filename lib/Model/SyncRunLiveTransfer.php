<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SyncRunLiveTransfer
 *
 * @package Files
 */
class SyncRunLiveTransfer
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // string # The file path being transferred. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    // string # Status of this individual transfer
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // int64 # Bytes transferred so far
    public function getBytesCopied()
    {
        return isset($this->attributes['bytes_copied']) ? $this->attributes['bytes_copied'] : null;
    }
    // int64 # Total bytes of the file being transferred
    public function getBytesTotal()
    {
        return isset($this->attributes['bytes_total']) ? $this->attributes['bytes_total'] : null;
    }
    // double # Transfer progress from 0.0 to 1.0
    public function getPercentage()
    {
        return isset($this->attributes['percentage']) ? $this->attributes['percentage'] : null;
    }
    // string # Estimated time remaining (human-readable)
    public function getEta()
    {
        return isset($this->attributes['eta']) ? $this->attributes['eta'] : null;
    }
    // string # When this individual transfer started
    public function getStartedAt()
    {
        return isset($this->attributes['started_at']) ? $this->attributes['started_at'] : null;
    }
}
