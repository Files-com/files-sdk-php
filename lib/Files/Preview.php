<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Preview
 *
 * @package Files
 */
class Preview {
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

  // int64 # Preview ID
  public function getId() {
    return $this->attributes['id'];
  }

  // string # Preview status.  Can be invalid, not_generated, generating, complete, or file_too_large
  public function getStatus() {
    return $this->attributes['status'];
  }

  // string # Link to download preview
  public function getDownloadUri() {
    return $this->attributes['download_uri'];
  }

  // string # Preview status.  Can be invalid, not_generated, generating, complete, or file_too_large
  public function getType() {
    return $this->attributes['type'];
  }

  // int64 # Preview size
  public function getSize() {
    return $this->attributes['size'];
  }
}
