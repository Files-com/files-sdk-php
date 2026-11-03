<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Represents a single in-progress file transfer within a running SyncRun.
 *
 * @property string|null $path The file path being transferred. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $status Status of this individual transfer
 * @property int|null $bytes_copied Bytes transferred so far
 * @property int|null $bytes_total Total bytes of the file being transferred
 * @property float|null $percentage Transfer progress from 0.0 to 1.0
 * @property string|null $eta Estimated time remaining (human-readable)
 * @property string|null $started_at When this individual transfer started
 * @package Files
 */
class SyncRunLiveTransfer
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * The file path being transferred. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Status of this individual transfer
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Bytes transferred so far
     *
     * @return int|null
     */
    public function getBytesCopied()
    {
        return isset($this->attributes['bytes_copied']) ? $this->attributes['bytes_copied'] : null;
    }
    /**
     * Total bytes of the file being transferred
     *
     * @return int|null
     */
    public function getBytesTotal()
    {
        return isset($this->attributes['bytes_total']) ? $this->attributes['bytes_total'] : null;
    }
    /**
     * Transfer progress from 0.0 to 1.0
     *
     * @return float|null
     */
    public function getPercentage()
    {
        return isset($this->attributes['percentage']) ? $this->attributes['percentage'] : null;
    }
    /**
     * Estimated time remaining (human-readable)
     *
     * @return string|null
     */
    public function getEta()
    {
        return isset($this->attributes['eta']) ? $this->attributes['eta'] : null;
    }
    /**
     * When this individual transfer started
     *
     * @return string|null
     */
    public function getStartedAt()
    {
        return isset($this->attributes['started_at']) ? $this->attributes['started_at'] : null;
    }
}
