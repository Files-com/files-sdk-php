<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Image
 *
 * @package Files
 */
class Image {
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

  // string # Image name
  public function getName() {
    return $this->attributes['name'];
  }

  // string # Image URI
  public function getUri() {
    return $this->attributes['uri'];
  }
}
