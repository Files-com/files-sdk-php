<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Auto
 *
 * @package Files
 */
class Auto {
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

  // object # Additional data
  public function getDynamic() {
    return $this->attributes['dynamic'];
  }
}
