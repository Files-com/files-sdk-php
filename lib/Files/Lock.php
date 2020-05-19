<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Lock
 *
 * @package Files
 */
class Lock {
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

  // string # Path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // int64 # Lock timeout
  public function getTimeout() {
    return $this->attributes['timeout'];
  }

  public function setTimeout($value) {
    return $this->attributes['timeout'] = $value;
  }

  // string # Lock depth (0 or infinity)
  public function getDepth() {
    return $this->attributes['depth'];
  }

  public function setDepth($value) {
    return $this->attributes['depth'] = $value;
  }

  // string # Owner of lock.  This can be any arbitrary string.
  public function getOwner() {
    return $this->attributes['owner'];
  }

  public function setOwner($value) {
    return $this->attributes['owner'] = $value;
  }

  // string # Lock scope(shared or exclusive)
  public function getScope() {
    return $this->attributes['scope'];
  }

  public function setScope($value) {
    return $this->attributes['scope'] = $value;
  }

  // string # Lock token.  Use to release lock.
  public function getToken() {
    return $this->attributes['token'];
  }

  public function setToken($value) {
    return $this->attributes['token'] = $value;
  }

  // string # Lock type
  public function getType() {
    return $this->attributes['type'];
  }

  public function setType($value) {
    return $this->attributes['type'] = $value;
  }

  // int64 # Lock creator user ID
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Lock creator username
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // Parameters:
  //   token (required) - string - Lock token
  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }
    if ($params['token'] && !is_string($params['token'])) {
      throw new \InvalidArgumentException('Bad parameter: $token must be of type string; received ' . gettype($token));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    if (!$params['token']) {
      if ($this->token) {
        $params['token'] = $this->token;
      } else {
        throw new \Error('Parameter missing: token');
      }
    }

    return Api::sendRequest('/locks/' . rawurlencode($params['path']) . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['path']) {
      throw new \BadMethodCallException('The Lock object doesn\'t support updates.');
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   path (required) - string - Path to operate on.
  //   include_children - boolean - Include locks from children objects?
  public static function listFor($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/locks/' . rawurlencode($params['path']) . '', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Lock((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   path (required) - string - Path
  //   timeout - integer - Lock timeout length
  public static function create($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if ($params['timeout'] && !is_int($params['timeout'])) {
      throw new \InvalidArgumentException('Bad parameter: $timeout must be of type int; received ' . gettype($timeout));
    }

    $response = Api::sendRequest('/locks/' . rawurlencode($params['path']) . '', 'POST', $params);

    return new Lock((array)$response->data, $options);
  }
}
