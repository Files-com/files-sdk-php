<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Permission
 *
 * @package Files
 */
class Permission {
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

  // int64 # Permission ID
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

  // int64 # User ID
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # User's username
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // int64 # Group ID
  public function getGroupId() {
    return $this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // string # Group name if applicable
  public function getGroupName() {
    return $this->attributes['group_name'];
  }

  public function setGroupName($value) {
    return $this->attributes['group_name'] = $value;
  }

  // string # Permission type
  public function getPermission() {
    return $this->attributes['permission'];
  }

  public function setPermission($value) {
    return $this->attributes['permission'] = $value;
  }

  // boolean # Does this permission apply to subfolders?
  public function getRecursive() {
    return $this->attributes['recursive'];
  }

  public function setRecursive($value) {
    return $this->attributes['recursive'] = $value;
  }

  public function save() {
    if ($this->attributes['path']) {
      throw new \BadMethodCallException('The Permission object doesn\'t support updates.');
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
  //   path - string - Permission path.  If provided, will scope permissions to this path.
  //   group_id - string - Group ID.  If provided, will scope permissions to this group.
  //   user_id - string - User ID.  If provided, will scope permissions to this user.
  //   include_groups - boolean - If searching by user or group, also include user's permissions that are inherited from its groups?
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

    if ($params['group_id'] && !is_string($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type string; received ' . gettype($group_id));
    }

    if ($params['user_id'] && !is_string($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type string; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/permissions', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Permission((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($path, $params = [], $options = []) {
    return self::list($path, $params, $options);
  }

  // Parameters:
  //   group_id - integer - Group ID
  //   path - string - Folder path
  //   permission - string -  Permission type.  Can be `admin`, `full`, `readonly`, `writeonly`, `previewonly`, or `history`
  //   recursive - boolean - Apply to subfolders recursively?
  //   user_id - integer - User ID.  Provide `username` or `user_id`
  //   username - string - User username.  Provide `username` or `user_id`
  public static function create($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if ($params['group_id'] && !is_int($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type int; received ' . gettype($group_id));
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if ($params['permission'] && !is_string($params['permission'])) {
      throw new \InvalidArgumentException('Bad parameter: $permission must be of type string; received ' . gettype($permission));
    }

    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/permissions', 'POST', $params);

    return new Permission((array)$response->data, $options);
  }

  // Parameters:
  //   id (required) - integer - Permission ID.
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

    $response = Api::sendRequest('/permissions/' . $params['id'] . '', 'DELETE', $params);

    return $response->data;
  }

  public static function destroy($id, $params = [], $options = []) {
    return self::delete($id, $params, $options);
  }
}
