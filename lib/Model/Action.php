<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Action
 *
 * @package Files
 */
class Action
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
    // int64 # Action ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // date-time # Action occurrence date/time
    public function getWhen()
    {
        return @$this->attributes['when'];
    }
    // string # The destination path for this action, if applicable
    public function getDestination()
    {
        return @$this->attributes['destination'];
    }
    // string # Friendly displayed output
    public function getDisplay()
    {
        return @$this->attributes['display'];
    }
    // string # IP Address that performed this action
    public function getIp()
    {
        return @$this->attributes['ip'];
    }
    // string # The source path for this action, if applicable
    public function getSource()
    {
        return @$this->attributes['source'];
    }
    // array(object) # Targets
    public function getTargets()
    {
        return @$this->attributes['targets'];
    }
    // int64 # User ID
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }
    // string # Username
    public function getUsername()
    {
        return @$this->attributes['username'];
    }
    // boolean # true if this change was performed by a user on a parent site.
    public function getUserIsFromParentSite()
    {
        return @$this->attributes['user_is_from_parent_site'];
    }
    // string # Type of action
    public function getAction()
    {
        return @$this->attributes['action'];
    }
    // string # Failure type.  If action was a user login or session failure, why did it fail?
    public function getFailureType()
    {
        return @$this->attributes['failure_type'];
    }
    // string # Interface on which this action occurred.
    public function getInterface()
    {
        return @$this->attributes['interface'];
    }
}
