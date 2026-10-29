<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FormField
 *
 * @package Files
 */
class FormField
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
    // int64 # Form field id
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # Label to be displayed
    public function getLabel()
    {
        return isset($this->attributes['label']) ? $this->attributes['label'] : null;
    }
    // boolean # Is this a required field?
    public function getRequired()
    {
        return isset($this->attributes['required']) ? $this->attributes['required'] : null;
    }
    // string # Help text to be displayed
    public function getHelpText()
    {
        return isset($this->attributes['help_text']) ? $this->attributes['help_text'] : null;
    }
    // string # Type of Field
    public function getFieldType()
    {
        return isset($this->attributes['field_type']) ? $this->attributes['field_type'] : null;
    }
    // array(string) # Options to display for radio and dropdown
    public function getOptionsForSelect()
    {
        return isset($this->attributes['options_for_select']) ? $this->attributes['options_for_select'] : null;
    }
    // string # Default option for radio and dropdown
    public function getDefaultOption()
    {
        return isset($this->attributes['default_option']) ? $this->attributes['default_option'] : null;
    }
    // int64 # Form field set id
    public function getFormFieldSetId()
    {
        return isset($this->attributes['form_field_set_id']) ? $this->attributes['form_field_set_id'] : null;
    }
}
