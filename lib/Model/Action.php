<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An Action is a single record in our history log.
 *
 * @property int|null $id Action ID
 * @property string|null $path Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $when Action occurrence date/time
 * @property string|null $destination The destination path for this action, if applicable
 * @property string|null $display Friendly displayed output
 * @property string|null $ip IP Address that performed this action
 * @property string|null $source The source path for this action, if applicable
 * @property object|null $targets Targets
 * @property int|null $user_id User ID
 * @property string|null $username Username
 * @property bool|null $user_is_from_parent_site true if this change was performed by a user on a parent site.
 * @property string|null $action Type of action
 * @property string|null $failure_type Failure type. If action was a user login or session failure, why did it fail?
 * @property string|null $interface Interface on which this action occurred.
 * @package Files
 */
class Action
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
     * Action ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Action occurrence date/time
     *
     * @return string|null
     */
    public function getWhen()
    {
        return isset($this->attributes['when']) ? $this->attributes['when'] : null;
    }
    /**
     * The destination path for this action, if applicable
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    /**
     * Friendly displayed output
     *
     * @return string|null
     */
    public function getDisplay()
    {
        return isset($this->attributes['display']) ? $this->attributes['display'] : null;
    }
    /**
     * IP Address that performed this action
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * The source path for this action, if applicable
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }
    /**
     * Targets
     *
     * @return object|null
     */
    public function getTargets()
    {
        return isset($this->attributes['targets']) ? $this->attributes['targets'] : null;
    }
    /**
     * User ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * Username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    /**
     * true if this change was performed by a user on a parent site.
     *
     * @return bool|null
     */
    public function getUserIsFromParentSite()
    {
        return isset($this->attributes['user_is_from_parent_site']) ? $this->attributes['user_is_from_parent_site'] : null;
    }
    /**
     * Type of action
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * Failure type.  If action was a user login or session failure, why did it fail?
     *
     * @return string|null
     */
    public function getFailureType()
    {
        return isset($this->attributes['failure_type']) ? $this->attributes['failure_type'] : null;
    }
    /**
     * Interface on which this action occurred.
     *
     * @return string|null
     */
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }
}
