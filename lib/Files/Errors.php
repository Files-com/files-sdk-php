<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Errors
 *
 * @package Files
 */
class Errors {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // array # A list of fields where errors occur
  public function getFields() {
    return $this->attributes['fields'];
  }

  // array # A list of error messages
  public function getMessages() {
    return $this->attributes['messages'];
  }
}
