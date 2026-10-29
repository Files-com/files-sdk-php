<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Status
 *
 * @package Files
 */
class Status
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
    // int64 # Status HTTP code
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }
    // string # Error message
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    // string # Status message
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // Auto # Additional data
    public function getData()
    {
        return isset($this->attributes['data']) ? $this->attributes['data'] : null;
    }
    // array(object) # A list of api errors
    public function getErrors()
    {
        return isset($this->attributes['errors']) ? $this->attributes['errors'] : null;
    }
    // int64 # Required Clickwrap id
    public function getClickwrapId()
    {
        return isset($this->attributes['clickwrap_id']) ? $this->attributes['clickwrap_id'] : null;
    }
    // string # Required Clickwrap body
    public function getClickwrapBody()
    {
        return isset($this->attributes['clickwrap_body']) ? $this->attributes['clickwrap_body'] : null;
    }
}
