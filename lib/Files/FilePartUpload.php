<?php

declare(strict_types=1);

namespace Files;

/**
 * Class FilePartUpload
 *
 * @package Files
 */
class FilePartUpload {
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

  // object # Content-Type and File to send
  public function getSend() {
    return $this->attributes['send'];
  }

  // string # Type of upload
  public function getAction() {
    return $this->attributes['action'];
  }

  // boolean # If false, rename conflicting files instead of asking for overwrite confirmation
  public function getAskAboutOverwrites() {
    return $this->attributes['ask_about_overwrites'];
  }

  // string # Currently unused
  public function getAvailableParts() {
    return $this->attributes['available_parts'];
  }

  // string # Currently unused
  public function getExpires() {
    return $this->attributes['expires'];
  }

  // object # Additional upload headers
  public function getHeaders() {
    return $this->attributes['headers'];
  }

  // string # Upload method, usually POST
  public function getHttpMethod() {
    return $this->attributes['http_method'];
  }

  // string # Currently unused
  public function getNextPartsize() {
    return $this->attributes['next_partsize'];
  }

  // string # Additional upload parameters
  public function getParameters() {
    return $this->attributes['parameters'];
  }

  // string # Currently unused
  public function getPartNumber() {
    return $this->attributes['part_number'];
  }

  // string # Currently unused
  public function getPartsize() {
    return $this->attributes['partsize'];
  }

  // string # Upload path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  // string # Reference name for this upload part
  public function getRef() {
    return $this->attributes['ref'];
  }

  // string # URI to upload this part to
  public function getUploadUri() {
    return $this->attributes['upload_uri'];
  }
}
