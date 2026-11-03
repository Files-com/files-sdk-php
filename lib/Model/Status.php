<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Status API model.
 *
 * @property int|null $code Status HTTP code
 * @property string|null $message Error message
 * @property string|null $status Status message
 * @property \stdClass|null $data Additional data
 * @property object[]|null $errors A list of api errors
 * @property int|null $clickwrap_id Required Clickwrap id
 * @property string|null $clickwrap_body Required Clickwrap body
 * @package Files
 */
class Status
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
     * Status HTTP code
     *
     * @return int|null
     */
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }
    /**
     * Error message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * Status message
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Additional data
     *
     * @return \stdClass|null
     */
    public function getData()
    {
        return isset($this->attributes['data']) ? $this->attributes['data'] : null;
    }
    /**
     * A list of api errors
     *
     * @return object[]|null
     */
    public function getErrors()
    {
        return isset($this->attributes['errors']) ? $this->attributes['errors'] : null;
    }
    /**
     * Required Clickwrap id
     *
     * @return int|null
     */
    public function getClickwrapId()
    {
        return isset($this->attributes['clickwrap_id']) ? $this->attributes['clickwrap_id'] : null;
    }
    /**
     * Required Clickwrap body
     *
     * @return string|null
     */
    public function getClickwrapBody()
    {
        return isset($this->attributes['clickwrap_body']) ? $this->attributes['clickwrap_body'] : null;
    }
}
