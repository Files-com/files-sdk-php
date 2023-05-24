<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Snapshot
 *
 * @package Files
 */
class Snapshot {
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

  // int64 # The snapshot's unique ID.
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // date-time # When the snapshot expires.
  public function getExpiresAt() {
    return @$this->attributes['expires_at'];
  }

  public function setExpiresAt($value) {
    return $this->attributes['expires_at'] = $value;
  }

  // date-time # When the snapshot was finalized.
  public function getFinalizedAt() {
    return @$this->attributes['finalized_at'];
  }

  public function setFinalizedAt($value) {
    return $this->attributes['finalized_at'] = $value;
  }

  // string # A name for the snapshot.
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // int64 # The user that created this snapshot, if applicable.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // int64 # The bundle using this snapshot, if applicable.
  public function getBundleId() {
    return @$this->attributes['bundle_id'];
  }

  public function setBundleId($value) {
    return $this->attributes['bundle_id'] = $value;
  }

  // array(string) # An array of paths to add to the snapshot.
  public function getPaths() {
    return @$this->attributes['paths'];
  }

  public function setPaths($value) {
    return $this->attributes['paths'] = $value;
  }

  // Parameters:
  //   expires_at - string - When the snapshot expires.
  //   name - string - A name for the snapshot.
  //   paths - array(string) - An array of paths to add to the snapshot.
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

    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['paths'] && !is_array(@$params['paths'])) {
      throw new \Files\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
    }

    $response = Api::sendRequest('/snapshots/' . @$params['id'] . '', 'PATCH', $params, $this->options);
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

    $response = Api::sendRequest('/snapshots/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function all($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/snapshots', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Snapshot((array)$obj, $options);
    }

    return $return_array;
  }


  

  // Parameters:
  //   id (required) - int64 - Snapshot ID.
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

    $response = Api::sendRequest('/snapshots/' . @$params['id'] . '', 'GET', $params, $options);

    return new Snapshot((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }
  

  // Parameters:
  //   expires_at - string - When the snapshot expires.
  //   name - string - A name for the snapshot.
  //   paths - array(string) - An array of paths to add to the snapshot.
  public static function create($params = [], $options = []) {
    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['paths'] && !is_array(@$params['paths'])) {
      throw new \Files\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
    }

    $response = Api::sendRequest('/snapshots', 'POST', $params, $options);

    return new Snapshot((array)(@$response->data ?: []), $options);
  }

}
