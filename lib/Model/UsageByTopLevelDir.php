<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UsageByTopLevelDir
 *
 * @package Files
 */
class UsageByTopLevelDir
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
    // string # Directory name
    public function getDir()
    {
        return isset($this->attributes['dir']) ? $this->attributes['dir'] : null;
    }
    // int64 # Usage
    public function getSize()
    {
        return isset($this->attributes['size']) ? $this->attributes['size'] : null;
    }
    // int64 # File count
    public function getCount()
    {
        return isset($this->attributes['count']) ? $this->attributes['count'] : null;
    }
}
