<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class GroupUser
 *
 * @package Files
 */
class GroupUser {
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

  // string # Group name
  public function getGroupName() {
    return @$this->attributes['group_name'];
  }

  public function setGroupName($value) {
    return $this->attributes['group_name'] = $value;
  }

  // int64 # Group ID
  public function getGroupId() {
    return @$this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // int64 # User ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // boolean # Is this user an administrator of this group?
  public function getAdmin() {
    return @$this->attributes['admin'];
  }

  public function setAdmin($value) {
    return $this->attributes['admin'] = $value;
  }

  // array # A list of usernames for users in this group
  public function getUsernames() {
    return @$this->attributes['usernames'];
  }

  public function setUsernames($value) {
    return $this->attributes['usernames'] = $value;
  }

  // int64 # Group User ID.
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // Parameters:
  //   group_id (required) - int64 - Group ID to add user to.
  //   user_id (required) - int64 - User ID to add to group.
  //   admin - boolean - Is the user a group administrator?
  public function update($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (!@$params['group_id']) {
      if (@$this->group_id) {
        $params['group_id'] = $this->group_id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: group_id');
      }
    }

    if (!@$params['user_id']) {
      if (@$this->user_id) {
        $params['user_id'] = $this->user_id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: user_id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/group_users/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   group_id (required) - int64 - Group ID from which to remove user.
  //   user_id (required) - int64 - User ID to remove from group.
  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (!@$params['group_id']) {
      if (@$this->group_id) {
        $params['group_id'] = $this->group_id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: group_id');
      }
    }

    if (!@$params['user_id']) {
      if (@$this->user_id) {
        $params['user_id'] = $this->user_id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: user_id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/group_users/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   user_id - int64 - User ID.  If provided, will return group_users of this user.
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   group_id - int64 - Group ID.  If provided, will return group_users of this group.
  public static function list($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    $response = Api::sendRequest('/group_users', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new GroupUser((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   group_id (required) - int64 - Group ID to add user to.
  //   user_id (required) - int64 - User ID to add to group.
  //   admin - boolean - Is the user a group administrator?
  public static function create($params = [], $options = []) {
    if (!@$params['group_id']) {
      throw new \Files\MissingParameterException('Parameter missing: group_id');
    }

    if (!@$params['user_id']) {
      throw new \Files\MissingParameterException('Parameter missing: user_id');
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/group_users', 'POST', $params, $options);

    return new GroupUser((array)(@$response->data ?: []), $options);
  }
}
