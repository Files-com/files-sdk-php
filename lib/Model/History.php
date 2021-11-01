<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

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

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['path'];
  }

  // int64 # Action ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // string # Path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  // date-time # Action occurrence date/time
  public function getWhen() {
    return @$this->attributes['when'];
  }

  // string # The destination path for this action, if applicable
  public function getDestination() {
    return @$this->attributes['destination'];
  }

  // string # Friendly displayed output
  public function getDisplay() {
    return @$this->attributes['display'];
  }

  // string # IP Address that performed this action
  public function getIp() {
    return @$this->attributes['ip'];
  }

  // string # The source path for this action, if applicable
  public function getSource() {
    return @$this->attributes['source'];
  }

  // array # Targets
  public function getTargets() {
    return @$this->attributes['targets'];
  }

  // int64 # User ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  // string # Username
  public function getUsername() {
    return @$this->attributes['username'];
  }

  // string # Type of action
  public function getAction() {
    return @$this->attributes['action'];
  }

  // string # Failure type.  If action was a user login or session failure, why did it fail?
  public function getFailureType() {
    return @$this->attributes['failure_type'];
  }

  // string # Interface on which this action occurred.
  public function getInterface() {
    return @$this->attributes['interface'];
  }

  // Parameters:
  //   start_at - string - Leave blank or set to a date/time to filter earlier entries.
  //   end_at - string - Leave blank or set to a date/time to filter later entries.
  //   display - string - Display format. Leave blank or set to `full` or `parent`.
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `user_id` and `created_at`.
  //   path (required) - string - Path to operate on.
  public static function listForFile($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['start_at'] && !is_string(@$params['start_at'])) {
      throw new \Files\InvalidParameterException('$start_at must be of type string; received ' . gettype($start_at));
    }

    if (@$params['end_at'] && !is_string(@$params['end_at'])) {
      throw new \Files\InvalidParameterException('$end_at must be of type string; received ' . gettype($end_at));
    }

    if (@$params['display'] && !is_string(@$params['display'])) {
      throw new \Files\InvalidParameterException('$display must be of type string; received ' . gettype($display));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/history/files/' . @$params['path'] . '', 'GET', $params, $options);

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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `user_id` and `created_at`.
  //   path (required) - string - Path to operate on.
  public static function listForFolder($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['start_at'] && !is_string(@$params['start_at'])) {
      throw new \Files\InvalidParameterException('$start_at must be of type string; received ' . gettype($start_at));
    }

    if (@$params['end_at'] && !is_string(@$params['end_at'])) {
      throw new \Files\InvalidParameterException('$end_at must be of type string; received ' . gettype($end_at));
    }

    if (@$params['display'] && !is_string(@$params['display'])) {
      throw new \Files\InvalidParameterException('$display must be of type string; received ' . gettype($display));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/history/folders/' . @$params['path'] . '', 'GET', $params, $options);

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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `user_id` and `created_at`.
  //   user_id (required) - int64 - User ID.
  public static function listForUser($user_id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['user_id'] = $user_id;

    if (!@$params['user_id']) {
      throw new \Files\MissingParameterException('Parameter missing: user_id');
    }

    if (@$params['start_at'] && !is_string(@$params['start_at'])) {
      throw new \Files\InvalidParameterException('$start_at must be of type string; received ' . gettype($start_at));
    }

    if (@$params['end_at'] && !is_string(@$params['end_at'])) {
      throw new \Files\InvalidParameterException('$end_at must be of type string; received ' . gettype($end_at));
    }

    if (@$params['display'] && !is_string(@$params['display'])) {
      throw new \Files\InvalidParameterException('$display must be of type string; received ' . gettype($display));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    $response = Api::sendRequest('/history/users/' . @$params['user_id'] . '', 'GET', $params, $options);

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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `user_id` and `created_at`.
  public static function listLogins($params = [], $options = []) {
    if (@$params['start_at'] && !is_string(@$params['start_at'])) {
      throw new \Files\InvalidParameterException('$start_at must be of type string; received ' . gettype($start_at));
    }

    if (@$params['end_at'] && !is_string(@$params['end_at'])) {
      throw new \Files\InvalidParameterException('$end_at must be of type string; received ' . gettype($end_at));
    }

    if (@$params['display'] && !is_string(@$params['display'])) {
      throw new \Files\InvalidParameterException('$display must be of type string; received ' . gettype($display));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/history/login', 'GET', $params, $options);

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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `path`, `folder`, `user_id` or `created_at`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `user_id`, `folder` or `path`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `user_id`, `folder` or `path`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal to the supplied value. Valid fields are `user_id`, `folder` or `path`.
  //   filter_like - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `user_id`, `folder` or `path`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `user_id`, `folder` or `path`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal to the supplied value. Valid fields are `user_id`, `folder` or `path`.
  public static function list($params = [], $options = []) {
    if (@$params['start_at'] && !is_string(@$params['start_at'])) {
      throw new \Files\InvalidParameterException('$start_at must be of type string; received ' . gettype($start_at));
    }

    if (@$params['end_at'] && !is_string(@$params['end_at'])) {
      throw new \Files\InvalidParameterException('$end_at must be of type string; received ' . gettype($end_at));
    }

    if (@$params['display'] && !is_string(@$params['display'])) {
      throw new \Files\InvalidParameterException('$display must be of type string; received ' . gettype($display));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/history', 'GET', $params, $options);

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
