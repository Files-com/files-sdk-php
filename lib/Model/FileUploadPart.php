<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileUploadPart
 *
 * @package Files
 */
class FileUploadPart {
  private $attributes = [];
  private $options = [];

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

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // object # Content-Type and File to send
  public function getSend() {
    return @$this->attributes['send'];
  }

  // string # Type of upload
  public function getAction() {
    return @$this->attributes['action'];
  }

  // boolean # If `true`, this file exists and you may wish to ask the user for overwrite confirmation
  public function getAskAboutOverwrites() {
    return @$this->attributes['ask_about_overwrites'];
  }

  // int64 # Number of parts in the upload
  public function getAvailableParts() {
    return @$this->attributes['available_parts'];
  }

  // string # Date/time of when this Upload part expires and the URL cannot be used any more
  public function getExpires() {
    return @$this->attributes['expires'];
  }

  // object # Additional upload headers to provide as part of the upload
  public function getHeaders() {
    return @$this->attributes['headers'];
  }

  // string # HTTP Method to use for uploading the part, usually `PUT`
  public function getHttpMethod() {
    return @$this->attributes['http_method'];
  }

  // int64 # Size in bytes for this part
  public function getNextPartsize() {
    return @$this->attributes['next_partsize'];
  }

  // boolean # If `true`, multiple parts may be uploaded in parallel.  If `false`, be sure to only upload one part at a time, in order.
  public function getParallelParts() {
    return @$this->attributes['parallel_parts'];
  }

  // object # Additional HTTP parameters to send with the upload
  public function getParameters() {
    return @$this->attributes['parameters'];
  }

  // int64 # Number of this upload part
  public function getPartNumber() {
    return @$this->attributes['part_number'];
  }

  // int64 # Size in bytes for the next upload part
  public function getPartsize() {
    return @$this->attributes['partsize'];
  }

  // string # New file path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  // string # Reference name for this upload part
  public function getRef() {
    return @$this->attributes['ref'];
  }

  // string # URI to upload this part to
  public function getUploadUri() {
    return @$this->attributes['upload_uri'];
  }
}
