<?php

declare(strict_types=1);

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
    // string # Pushed agent version
    public function getVersion()
    {
        return @$this->attributes['version'];
    }
    // string # Update accepted or reason
    public function getMessage()
    {
        return @$this->attributes['message'];
    }
    // string # Installed agent version
    public function getCurrentVersion()
    {
        return @$this->attributes['current_version'];
    }
    // string # Pending agent version or null
    public function getPendingVersion()
    {
        return @$this->attributes['pending_version'];
    }
    // string # Last error message or null
    public function getLastError()
    {
        return @$this->attributes['last_error'];
    }
    // string # Error code
    public function getError()
    {
        return @$this->attributes['error'];
    }
}
