<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ApiKey
 *
 * @package Files
 */
class ApiKey {
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

  // int64 # API Key ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Unique label that describes this API key.  Useful for external systems where you may have API keys from multiple accounts and want a human-readable label for each key.
  public function getDescriptiveLabel() {
    return @$this->attributes['descriptive_label'];
  }

  public function setDescriptiveLabel($value) {
    return $this->attributes['descriptive_label'] = $value;
  }

  // string # User-supplied description of API key.
  public function getDescription() {
    return @$this->attributes['description'];
  }

  public function setDescription($value) {
    return $this->attributes['description'] = $value;
  }

  // date-time # Time which API Key was created
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // date-time # API Key expiration date
  public function getExpiresAt() {
    return @$this->attributes['expires_at'];
  }

  public function setExpiresAt($value) {
    return $this->attributes['expires_at'] = $value;
  }

  // string # API Key actual key string
  public function getKey() {
    return @$this->attributes['key'];
  }

  public function setKey($value) {
    return $this->attributes['key'] = $value;
  }

  // date-time # API Key last used - note this value is only updated once per 3 hour period, so the 'actual' time of last use may be up to 3 hours later than this timestamp.
  public function getLastUseAt() {
    return @$this->attributes['last_use_at'];
  }

  public function setLastUseAt($value) {
    return $this->attributes['last_use_at'] = $value;
  }

  // string # Internal name for the API Key.  For your use.
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # Folder path restriction for this api key. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // string # Permissions for this API Key.  Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations).  Additional permission sets may become available in the future, such as for a Site Admin to give a key with no administrator privileges.  If you have ideas for permission sets, please let us know.
  public function getPermissionSet() {
    return @$this->attributes['permission_set'];
  }

  public function setPermissionSet($value) {
    return $this->attributes['permission_set'] = $value;
  }

  // string # If this API key represents a Desktop app, what platform was it created on?
  public function getPlatform() {
    return @$this->attributes['platform'];
  }

  public function setPlatform($value) {
    return $this->attributes['platform'] = $value;
  }

  // string # URL for API host.
  public function getUrl() {
    return @$this->attributes['url'];
  }

  public function setUrl($value) {
    return $this->attributes['url'] = $value;
  }

  // int64 # User ID for the owner of this API Key.  May be blank for Site-wide API Keys.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // Parameters:
  //   name - string - Internal name for the API Key.  For your use.
  //   description - string - User-supplied description of API key.
  //   expires_at - string - API Key expiration date
  //   permission_set - string - Permissions for this API Key.  Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations).  Additional permission sets may become available in the future, such as for a Site Admin to give a key with no administrator privileges.  If you have ideas for permission sets, please let us know.
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

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
      throw new \Files\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
    }

    $response = Api::sendRequest('/api_keys/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
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

    $response = Api::sendRequest('/api_keys/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[expires_at]=desc`). Valid fields are `expires_at`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `expires_at`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `expires_at`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `expires_at`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `expires_at`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `expires_at`.
  public static function all($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/api_keys', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new ApiKey((array)$obj, $options);
    }

    return $return_array;
  }


  

  public static function findCurrent($params = [], $options = []) {
    $response = Api::sendRequest('/api_key', 'GET', $options);

    return new ApiKey((array)(@$response->data ?: []), $options);
  }


  // Parameters:
  //   id (required) - int64 - Api Key ID.
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

    $response = Api::sendRequest('/api_keys/' . @$params['id'] . '', 'GET', $params, $options);

    return new ApiKey((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }
  

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   name - string - Internal name for the API Key.  For your use.
  //   description - string - User-supplied description of API key.
  //   expires_at - string - API Key expiration date
  //   permission_set - string - Permissions for this API Key.  Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations).  Additional permission sets may become available in the future, such as for a Site Admin to give a key with no administrator privileges.  If you have ideas for permission sets, please let us know.
  //   path - string - Folder path restriction for this api key.
  public static function create($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
      throw new \Files\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    $response = Api::sendRequest('/api_keys', 'POST', $params, $options);

    return new ApiKey((array)(@$response->data ?: []), $options);
  }


  // Parameters:
  //   expires_at - string - API Key expiration date
  //   name - string - Internal name for the API Key.  For your use.
  //   permission_set - string - Permissions for this API Key.  Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations).  Additional permission sets may become available in the future, such as for a Site Admin to give a key with no administrator privileges.  If you have ideas for permission sets, please let us know.
  public static function updateCurrent($params = [], $options = []) {
    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
      throw new \Files\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
    }

    $response = Api::sendRequest('/api_key', 'PATCH', $params, $options);

    return new ApiKey((array)(@$response->data ?: []), $options);
  }


  public static function deleteCurrent($params = [], $options = []) {
    $response = Api::sendRequest('/api_key', 'DELETE', $options);

    return $response->data;
  }

}
