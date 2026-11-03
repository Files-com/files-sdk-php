<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * AgentPushUpdate API model.
 *
 * @property string|null $version Pushed agent version
 * @property string|null $message Update accepted or reason
 * @property string|null $current_version Installed agent version
 * @property string|null $pending_version Pending agent version or null
 * @property string|null $last_error Last error message or null
 * @property string|null $error Error code
 * @package Files
 */
class AgentPushUpdate
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
     * Pushed agent version
     *
     * @return string|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    /**
     * Update accepted or reason
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * Installed agent version
     *
     * @return string|null
     */
    public function getCurrentVersion()
    {
        return isset($this->attributes['current_version']) ? $this->attributes['current_version'] : null;
    }
    /**
     * Pending agent version or null
     *
     * @return string|null
     */
    public function getPendingVersion()
    {
        return isset($this->attributes['pending_version']) ? $this->attributes['pending_version'] : null;
    }
    /**
     * Last error message or null
     *
     * @return string|null
     */
    public function getLastError()
    {
        return isset($this->attributes['last_error']) ? $this->attributes['last_error'] : null;
    }
    /**
     * Error code
     *
     * @return string|null
     */
    public function getError()
    {
        return isset($this->attributes['error']) ? $this->attributes['error'] : null;
    }
}
