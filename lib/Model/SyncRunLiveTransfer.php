<?php

declare(strict_types=1);

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
        return @$this->attributes[$name];
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
        return @$this->attributes['path'];
    }
    // string # Status of this individual transfer
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // int64 # Bytes transferred so far
    public function getBytesCopied()
    {
        return @$this->attributes['bytes_copied'];
    }
    // int64 # Total bytes of the file being transferred
    public function getBytesTotal()
    {
        return @$this->attributes['bytes_total'];
    }
    // double # Transfer progress from 0.0 to 1.0
    public function getPercentage()
    {
        return @$this->attributes['percentage'];
    }
    // string # Estimated time remaining (human-readable)
    public function getEta()
    {
        return @$this->attributes['eta'];
    }
    // string # When this individual transfer started
    public function getStartedAt()
    {
        return @$this->attributes['started_at'];
    }
}
