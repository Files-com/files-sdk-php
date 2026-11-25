<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * AutomationApplicationAction API model.
 *
 * @property \stdClass|null $application Application that provides the action.
 * @property string|null $key Stable action key within the application.
 * @property string|null $name Action display name.
 * @property string|null $description What the action does.
 * @property string|null $fingerprint Fingerprint of the action contract shown to the author.
 * @property object[]|null $fields Normalized input fields.
 * @property object|null $output Normalized output schema.
 * @property string[]|null $origins HTTPS origins supported by this action.
 * @property object[]|null $authentication Authentication types accepted by this action.
 * @package Files
 */
class AutomationApplicationAction
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
     * Application that provides the action.
     *
     * @return \stdClass|null
     */
    public function getApplication()
    {
        return isset($this->attributes['application']) ? $this->attributes['application'] : null;
    }
    /**
     * Stable action key within the application.
     *
     * @return string|null
     */
    public function getKey()
    {
        return isset($this->attributes['key']) ? $this->attributes['key'] : null;
    }
    /**
     * Action display name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * What the action does.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }
    /**
     * Fingerprint of the action contract shown to the author.
     *
     * @return string|null
     */
    public function getFingerprint()
    {
        return isset($this->attributes['fingerprint']) ? $this->attributes['fingerprint'] : null;
    }
    /**
     * Normalized input fields.
     *
     * @return object[]|null
     */
    public function getFields()
    {
        return isset($this->attributes['fields']) ? $this->attributes['fields'] : null;
    }
    /**
     * Normalized output schema.
     *
     * @return object|null
     */
    public function getOutput()
    {
        return isset($this->attributes['output']) ? $this->attributes['output'] : null;
    }
    /**
     * HTTPS origins supported by this action.
     *
     * @return string[]|null
     */
    public function getOrigins()
    {
        return isset($this->attributes['origins']) ? $this->attributes['origins'] : null;
    }
    /**
     * Authentication types accepted by this action.
     *
     * @return object[]|null
     */
    public function getAuthentication()
    {
        return isset($this->attributes['authentication']) ? $this->attributes['authentication'] : null;
    }
}
