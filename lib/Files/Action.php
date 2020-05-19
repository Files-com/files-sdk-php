<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Action
 *
 * @package Files
 */
class Action {
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

  // int64 # Action ID
  public function getId() {
    return $this->attributes['id'];
  }

  // string # Path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  // date-time # Action occurrence date/time
  public function getWhen() {
    return $this->attributes['when'];
  }

  // string # The destination path for this action, if applicable
  public function getDestination() {
    return $this->attributes['destination'];
  }

  // string # Friendly displayed output
  public function getDisplay() {
    return $this->attributes['display'];
  }

  // string # IP Address that performed this action
  public function getIp() {
    return $this->attributes['ip'];
  }

  // string # The source path for this action, if applicable
  public function getSource() {
    return $this->attributes['source'];
  }

  // array # Targets
  public function getTargets() {
    return $this->attributes['targets'];
  }

  // int64 # User ID
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  // string # Username
  public function getUsername() {
    return $this->attributes['username'];
  }

  // string # Type of action
  public function getAction() {
    return $this->attributes['action'];
  }

  // string # Failure type.  If action was a user login or session failure, why did it fail?
  public function getFailureType() {
    return $this->attributes['failure_type'];
  }

  // string # Interface on which this action occurred.
  public function getInterface() {
    return $this->attributes['interface'];
  }
}
