<?php

declare(strict_types=1);

namespace Files;

/**
 * Class History
 *
 * @package Files
 */
class History {
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

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   path (required) - string - Path to operate on.
  public static function listForFile($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['display'] && !is_string($params['display'])) {
      throw new \InvalidArgumentException('Bad parameter: $display must be of type string; received ' . gettype($display));
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

    $response = Api::sendRequest('/history/files(/*path)', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Action((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   path (required) - string - Path to operate on.
  public static function listForFolder($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['display'] && !is_string($params['display'])) {
      throw new \InvalidArgumentException('Bad parameter: $display must be of type string; received ' . gettype($display));
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

    $response = Api::sendRequest('/history/folders(/*path)', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Action((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   user_id (required) - integer - User ID.
  public static function listForUser($user_id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['user_id'] = $user_id;

    if (!$params['user_id']) {
      throw new \Error('Parameter missing: user_id');
    }

    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['display'] && !is_string($params['display'])) {
      throw new \InvalidArgumentException('Bad parameter: $display must be of type string; received ' . gettype($display));
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

    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/history/users/' . $params['user_id'] . '', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Action((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  public static function listLogins($params = [], $options = []) {
    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['display'] && !is_string($params['display'])) {
      throw new \InvalidArgumentException('Bad parameter: $display must be of type string; received ' . gettype($display));
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

    $response = Api::sendRequest('/history/login', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Action((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  public static function list($params = [], $options = []) {
    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['display'] && !is_string($params['display'])) {
      throw new \InvalidArgumentException('Bad parameter: $display must be of type string; received ' . gettype($display));
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

    $response = Api::sendRequest('/history', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Action((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
