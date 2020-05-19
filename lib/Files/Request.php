<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Request
 *
 * @package Files
 */
class Request {
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

  // int64 # Request ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // string # Source filename, if applicable
  public function getSource() {
    return $this->attributes['source'];
  }

  public function setSource($value) {
    return $this->attributes['source'] = $value;
  }

  // string # Destination filename
  public function getDestination() {
    return $this->attributes['destination'];
  }

  public function setDestination($value) {
    return $this->attributes['destination'] = $value;
  }

  // string # ID of automation that created request
  public function getAutomationId() {
    return $this->attributes['automation_id'];
  }

  public function setAutomationId($value) {
    return $this->attributes['automation_id'] = $value;
  }

  // string # User making the request (if applicable)
  public function getUserDisplayName() {
    return $this->attributes['user_display_name'];
  }

  public function setUserDisplayName($value) {
    return $this->attributes['user_display_name'] = $value;
  }

  // string # A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
  public function getUserIds() {
    return $this->attributes['user_ids'];
  }

  public function setUserIds($value) {
    return $this->attributes['user_ids'] = $value;
  }

  // string # A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
  public function getGroupIds() {
    return $this->attributes['group_ids'];
  }

  public function setGroupIds($value) {
    return $this->attributes['group_ids'] = $value;
  }

  // List Requests
  //
  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   mine - boolean - Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
  public function folders($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

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

    return Api::sendRequest('/requests/folders/' . rawurlencode($params['path']) . '', 'GET', $params);
  }

  public function save() {
    if ($this->attributes['path']) {
      throw new \BadMethodCallException('The Request object doesn\'t support updates.');
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
  //   mine - boolean - Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
  //   path - string - Path to show requests for.  If omitted, shows all paths. Send `/` to represent the root directory.
  public static function list($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

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

    $response = Api::sendRequest('/requests', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Request((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($path, $params = [], $options = []) {
    return self::list($path, $params, $options);
  }

  // Parameters:
  //   path (required) - string - Folder path on which to request the file.
  //   destination (required) - string - Destination filename (without extension) to request.
  //   user_ids - string - A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
  //   group_ids - string - A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
  public static function create($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if (!$params['destination']) {
      throw new \Error('Parameter missing: destination');
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if ($params['destination'] && !is_string($params['destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination must be of type string; received ' . gettype($destination));
    }

    if ($params['user_ids'] && !is_string($params['user_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_ids must be of type string; received ' . gettype($user_ids));
    }

    if ($params['group_ids'] && !is_string($params['group_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_ids must be of type string; received ' . gettype($group_ids));
    }

    $response = Api::sendRequest('/requests', 'POST', $params);

    return new Request((array)$response->data, $options);
  }

  // Parameters:
  //   id (required) - integer - Request ID.
  public static function delete($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/requests/' . $params['id'] . '', 'DELETE', $params);

    return $response->data;
  }

  public static function destroy($id, $params = [], $options = []) {
    return self::delete($id, $params, $options);
  }
}
