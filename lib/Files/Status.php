<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Status
 *
 * @package Files
 */
class Status {
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

  // int64 # Status http code
  public function getCode() {
    return $this->attributes['code'];
  }

  // string # Error message
  public function getMessage() {
    return $this->attributes['message'];
  }

  // string # Status message
  public function getStatus() {
    return $this->attributes['status'];
  }

  public function getData() {
    return $this->attributes['data'];
  }

  // array # A list of api errors
  public function getErrors() {
    return $this->attributes['errors'];
  }
}
