<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Lock
 *
 * @package Files
 */
class Lock {
  private $attributes = [];
  private $options = [];
  private static $static_mapped_functions = [
    'list' => 'all',
  ];

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

  public static function __callStatic($name, $arguments) {
    if(in_array($name, array_keys(self::$static_mapped_functions))){
      $method = self::$static_mapped_functions[$name];
      if (method_exists(__CLASS__, $method)){
        return @self::$method($arguments);
      }
    }
  }

  public function isLoaded() {
    return !!@$this->attributes['path'];
  }

  // string # Path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // int64 # Lock timeout in seconds
  public function getTimeout() {
    return @$this->attributes['timeout'];
  }

  public function setTimeout($value) {
    return $this->attributes['timeout'] = $value;
  }

  // string # DEPRECATED: Lock depth
  public function getDepth() {
    return @$this->attributes['depth'];
  }

  public function setDepth($value) {
    return $this->attributes['depth'] = $value;
  }

  // boolean # Does lock apply to subfolders?
  public function getRecursive() {
    return @$this->attributes['recursive'];
  }

  public function setRecursive($value) {
    return $this->attributes['recursive'] = $value;
  }

  // string # Owner of the lock.  This can be any arbitrary string.
  public function getOwner() {
    return @$this->attributes['owner'];
  }

  public function setOwner($value) {
    return $this->attributes['owner'] = $value;
  }

  // string # DEPRECATED: Lock scope
  public function getScope() {
    return @$this->attributes['scope'];
  }

  public function setScope($value) {
    return $this->attributes['scope'] = $value;
  }

  // boolean # Is lock exclusive?
  public function getExclusive() {
    return @$this->attributes['exclusive'];
  }

  public function setExclusive($value) {
    return $this->attributes['exclusive'] = $value;
  }

  // string # Lock token.  Use to release lock.
  public function getToken() {
    return @$this->attributes['token'];
  }

  public function setToken($value) {
    return $this->attributes['token'] = $value;
  }

  // string # DEPRECATED: Lock type
  public function getType() {
    return @$this->attributes['type'];
  }

  public function setType($value) {
    return $this->attributes['type'] = $value;
  }

  // boolean # Can lock be modified by users other than its creator?
  public function getAllowAccessByAnyUser() {
    return @$this->attributes['allow_access_by_any_user'];
  }

  public function setAllowAccessByAnyUser($value) {
    return $this->attributes['allow_access_by_any_user'] = $value;
  }

  // int64 # Lock creator user ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Lock creator username
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // Parameters:
  //   token (required) - string - Lock token
  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (!@$params['token']) {
      if (@$this->token) {
        $params['token'] = $this->token;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: token');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['token'] && !is_string(@$params['token'])) {
      throw new \Files\InvalidParameterException('$token must be of type string; received ' . gettype(@$params['token']));
    }

    $response = Api::sendRequest('/locks/' . @$params['path'] . '', 'DELETE', $params, $this->options);
    return;
  }

  public function destroy($params = []) {
    $this->delete($params);
    return;
  }

  public function save() {
      $new_obj = self::create($this->path, $this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
  }


  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   path (required) - string - Path to operate on.
  //   include_children - boolean - Include locks from children objects?
  public static function listFor($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    $response = Api::sendRequest('/locks/' . @$params['path'] . '', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Lock((array)$obj, $options);
    }

    return $return_array;
  }


  // Parameters:
  //   path (required) - string - Path
  //   allow_access_by_any_user - boolean - Allow lock to be updated by any user?
  //   exclusive - boolean - Is lock exclusive?
  //   recursive - string - Does lock apply to subfolders?
  //   timeout - int64 - Lock timeout length
  public static function create($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['recursive'] && !is_string(@$params['recursive'])) {
      throw new \Files\InvalidParameterException('$recursive must be of type string; received ' . gettype(@$params['recursive']));
    }

    if (@$params['timeout'] && !is_int(@$params['timeout'])) {
      throw new \Files\InvalidParameterException('$timeout must be of type int; received ' . gettype(@$params['timeout']));
    }

    $response = Api::sendRequest('/locks/' . @$params['path'] . '', 'POST', $params, $options);

    return new Lock((array)(@$response->data ?: []), $options);
  }

}
