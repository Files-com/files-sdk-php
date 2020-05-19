<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Group
 *
 * @package Files
 */
class Group {
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

  // int64 # Group ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Group name
  public function getName() {
    return $this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // array # List of user IDs who are group administrators (separated by commas)
  public function getAdminIds() {
    return $this->attributes['admin_ids'];
  }

  public function setAdminIds($value) {
    return $this->attributes['admin_ids'] = $value;
  }

  // string # Notes about this group
  public function getNotes() {
    return $this->attributes['notes'];
  }

  public function setNotes($value) {
    return $this->attributes['notes'] = $value;
  }

  // array # List of user IDs who belong to this group (separated by commas)
  public function getUserIds() {
    return $this->attributes['user_ids'];
  }

  public function setUserIds($value) {
    return $this->attributes['user_ids'] = $value;
  }

  // array # List of usernames who belong to this group (separated by commas)
  public function getUsernames() {
    return $this->attributes['usernames'];
  }

  public function setUsernames($value) {
    return $this->attributes['usernames'] = $value;
  }

  // Parameters:
  //   name - string - Group name.
  //   notes - string - Group notes.
  //   user_ids - string - A list of user ids. If sent as a string, should be comma-delimited.
  //   admin_ids - string - A list of group admin user ids. If sent as a string, should be comma-delimited.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }
    if ($params['notes'] && !is_string($params['notes'])) {
      throw new \InvalidArgumentException('Bad parameter: $notes must be of type string; received ' . gettype($notes));
    }
    if ($params['user_ids'] && !is_string($params['user_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_ids must be of type string; received ' . gettype($user_ids));
    }
    if ($params['admin_ids'] && !is_string($params['admin_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $admin_ids must be of type string; received ' . gettype($admin_ids));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/groups/' . $params['id'] . '', 'PATCH', $params);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/groups/' . $params['id'] . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      return $this->update($this->attributes);
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
  //   ids - string - Comma-separated list of group ids to include in results.
  public static function list($params = [], $options = []) {
    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if ($params['ids'] && !is_string($params['ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $ids must be of type string; received ' . gettype($ids));
    }

    $response = Api::sendRequest('/groups', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Group((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - integer - Group ID.
  public static function find($id, $params = [], $options = []) {
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

    $response = Api::sendRequest('/groups/' . $params['id'] . '', 'GET', $params);

    return new Group((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   name - string - Group name.
  //   notes - string - Group notes.
  //   user_ids - string - A list of user ids. If sent as a string, should be comma-delimited.
  //   admin_ids - string - A list of group admin user ids. If sent as a string, should be comma-delimited.
  public static function create($params = [], $options = []) {
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }

    if ($params['notes'] && !is_string($params['notes'])) {
      throw new \InvalidArgumentException('Bad parameter: $notes must be of type string; received ' . gettype($notes));
    }

    if ($params['user_ids'] && !is_string($params['user_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_ids must be of type string; received ' . gettype($user_ids));
    }

    if ($params['admin_ids'] && !is_string($params['admin_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $admin_ids must be of type string; received ' . gettype($admin_ids));
    }

    $response = Api::sendRequest('/groups', 'POST', $params);

    return new Group((array)$response->data, $options);
  }
}
