<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AgentPushUpdate
 *
 * @package Files
 */
class AgentPushUpdate
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
    // string # Pushed agent version
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    // string # Update accepted or reason
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    // string # Installed agent version
    public function getCurrentVersion()
    {
        return isset($this->attributes['current_version']) ? $this->attributes['current_version'] : null;
    }
    // string # Pending agent version or null
    public function getPendingVersion()
    {
        return isset($this->attributes['pending_version']) ? $this->attributes['pending_version'] : null;
    }
    // string # Last error message or null
    public function getLastError()
    {
        return isset($this->attributes['last_error']) ? $this->attributes['last_error'] : null;
    }
    // string # Error code
    public function getError()
    {
        return isset($this->attributes['error']) ? $this->attributes['error'] : null;
    }
}
