<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A FormField is a single form field used in Form Field Set.
 *
 * @property int|null $id Form field id
 * @property string|null $label Label to be displayed
 * @property bool|null $required Is this a required field?
 * @property string|null $help_text Help text to be displayed
 * @property string|null $field_type Type of Field
 * @property string[]|null $options_for_select Options to display for radio and dropdown
 * @property string|null $default_option Default option for radio and dropdown
 * @property int|null $form_field_set_id Form field set id
 * @package Files
 */
class FormField
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
     * Form field id
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Label to be displayed
     *
     * @return string|null
     */
    public function getLabel()
    {
        return isset($this->attributes['label']) ? $this->attributes['label'] : null;
    }
    /**
     * Is this a required field?
     *
     * @return bool|null
     */
    public function getRequired()
    {
        return isset($this->attributes['required']) ? $this->attributes['required'] : null;
    }
    /**
     * Help text to be displayed
     *
     * @return string|null
     */
    public function getHelpText()
    {
        return isset($this->attributes['help_text']) ? $this->attributes['help_text'] : null;
    }
    /**
     * Type of Field
     *
     * @return string|null
     */
    public function getFieldType()
    {
        return isset($this->attributes['field_type']) ? $this->attributes['field_type'] : null;
    }
    /**
     * Options to display for radio and dropdown
     *
     * @return string[]|null
     */
    public function getOptionsForSelect()
    {
        return isset($this->attributes['options_for_select']) ? $this->attributes['options_for_select'] : null;
    }
    /**
     * Default option for radio and dropdown
     *
     * @return string|null
     */
    public function getDefaultOption()
    {
        return isset($this->attributes['default_option']) ? $this->attributes['default_option'] : null;
    }
    /**
     * Form field set id
     *
     * @return int|null
     */
    public function getFormFieldSetId()
    {
        return isset($this->attributes['form_field_set_id']) ? $this->attributes['form_field_set_id'] : null;
    }
}
