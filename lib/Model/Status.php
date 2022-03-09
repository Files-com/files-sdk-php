<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

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

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Status HTTP code
  public function getCode() {
    return @$this->attributes['code'];
  }

  // string # Error message
  public function getMessage() {
    return @$this->attributes['message'];
  }

  // string # Status message
  public function getStatus() {
    return @$this->attributes['status'];
  }

  // Auto # Additional data
  public function getData() {
    return @$this->attributes['data'];
  }

  // Errors # A list of api errors
  public function getErrors() {
    return @$this->attributes['errors'];
  }

  // int64 # Required Clickwrap id
  public function getClickwrapId() {
    return @$this->attributes['clickwrap_id'];
  }

  // string # Required Clickwrap body
  public function getClickwrapBody() {
    return @$this->attributes['clickwrap_body'];
  }
}
