<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FormField
 *
 * @package Files
 */
class FormField {
  private $attributes = [];
  private $options = [];
  private static $static_mapped_functions = [
    'list' => 'all',
  ];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public static function __callStatic($name, $arguments) {
    if(in_array($name, array_keys(self::$static_mapped_functions))){
      $method = self::$static_mapped_functions[$name];
      if (method_exists(__CLASS__, $method)){ 
        return @self::$method($arguments);
      }
    }
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Form field id
  public function getId() {
    return @$this->attributes['id'];
  }

  // string # Label to be displayed
  public function getLabel() {
    return @$this->attributes['label'];
  }

  // boolean # Is this a required field?
  public function getRequired() {
    return @$this->attributes['required'];
  }

  // string # Help text to be displayed
  public function getHelpText() {
    return @$this->attributes['help_text'];
  }

  // string # Type of Field
  public function getFieldType() {
    return @$this->attributes['field_type'];
  }

  // array # Options to display for radio and dropdown
  public function getOptionsForSelect() {
    return @$this->attributes['options_for_select'];
  }

  // string # Default option for radio and dropdown
  public function getDefaultOption() {
    return @$this->attributes['default_option'];
  }

  // int64 # Form field set id
  public function getFormFieldSetId() {
    return @$this->attributes['form_field_set_id'];
  }
}
