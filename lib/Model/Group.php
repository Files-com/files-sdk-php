<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Group
 *
 * @package Files
 */
class Group {
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
    return !!@$this->attributes['id'];
  }

  // int64 # Group ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Group name
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # Comma-delimited list of user IDs who are group administrators (separated by commas)
  public function getAdminIds() {
    return @$this->attributes['admin_ids'];
  }

  public function setAdminIds($value) {
    return $this->attributes['admin_ids'] = $value;
  }

  // string # Notes about this group
  public function getNotes() {
    return @$this->attributes['notes'];
  }

  public function setNotes($value) {
    return $this->attributes['notes'] = $value;
  }

  // string # Comma-delimited list of user IDs who belong to this group (separated by commas)
  public function getUserIds() {
    return @$this->attributes['user_ids'];
  }

  public function setUserIds($value) {
    return $this->attributes['user_ids'] = $value;
  }

  // string # Comma-delimited list of usernames who belong to this group (separated by commas)
  public function getUsernames() {
    return @$this->attributes['usernames'];
  }

  public function setUsernames($value) {
    return $this->attributes['usernames'] = $value;
  }

  // Parameters:
  //   notes - string - Group notes.
  //   user_ids - string - A list of user ids. If sent as a string, should be comma-delimited.
  //   admin_ids - string - A list of group admin user ids. If sent as a string, should be comma-delimited.
  //   name - string - Group name.
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

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    if (@$params['notes'] && !is_string(@$params['notes'])) {
      throw new \Files\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
    }

    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \Files\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
    }

    if (@$params['admin_ids'] && !is_string(@$params['admin_ids'])) {
      throw new \Files\InvalidParameterException('$admin_ids must be of type string; received ' . gettype(@$params['admin_ids']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    $response = Api::sendRequest('/groups/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return new Group((array)(@$response->data ?: []), $options);
  }

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

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/groups/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return;
  }

  public function destroy($params = []) {
    $this->delete($params);
    return;
  }

  public function save() {
      if (@$this->attributes['id']) {
        $new_obj = $this->update($this->attributes);
        $this->attributes = $new_obj->attributes;
        return true;
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }


  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[name]=desc`). Valid fields are `name`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `name`.
  //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
  //   ids - string - Comma-separated list of group ids to include in results.
  public static function all($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    if (@$params['ids'] && !is_string(@$params['ids'])) {
      throw new \Files\InvalidParameterException('$ids must be of type string; received ' . gettype(@$params['ids']));
    }

    $response = Api::sendRequest('/groups', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Group((array)$obj, $options);
    }

    return $return_array;
  }




  // Parameters:
  //   id (required) - int64 - Group ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/groups/' . @$params['id'] . '', 'GET', $params, $options);

    return new Group((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }


  // Parameters:
  //   notes - string - Group notes.
  //   user_ids - string - A list of user ids. If sent as a string, should be comma-delimited.
  //   admin_ids - string - A list of group admin user ids. If sent as a string, should be comma-delimited.
  //   name (required) - string - Group name.
  public static function create($params = [], $options = []) {
    if (!@$params['name']) {
      throw new \Files\MissingParameterException('Parameter missing: name');
    }

    if (@$params['notes'] && !is_string(@$params['notes'])) {
      throw new \Files\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
    }

    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \Files\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
    }

    if (@$params['admin_ids'] && !is_string(@$params['admin_ids'])) {
      throw new \Files\InvalidParameterException('$admin_ids must be of type string; received ' . gettype(@$params['admin_ids']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    $response = Api::sendRequest('/groups', 'POST', $params, $options);

    return new Group((array)(@$response->data ?: []), $options);
  }

}
