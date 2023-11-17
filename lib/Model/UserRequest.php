<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UserRequest
 *
 * @package Files
 */
class UserRequest {
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

  // int64 # ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # User's full name
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // email # User email address
  public function getEmail() {
    return @$this->attributes['email'];
  }

  public function setEmail($value) {
    return $this->attributes['email'] = $value;
  }

  // string # Details of the user's request
  public function getDetails() {
    return @$this->attributes['details'];
  }

  public function setDetails($value) {
    return $this->attributes['details'] = $value;
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

    $response = Api::sendRequest('/user_requests/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return;
  }

  public function destroy($params = []) {
    $this->delete($params);
    return;
  }

  public function save() {
      if (@$this->attributes['id']) {
        throw new \Files\NotImplementedException('The UserRequest object doesn\'t support updates.');
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

    $response = Api::sendRequest('/user_requests', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new UserRequest((array)$obj, $options);
    }

    return $return_array;
  }




  // Parameters:
  //   id (required) - int64 - User Request ID.
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

    $response = Api::sendRequest('/user_requests/' . @$params['id'] . '', 'GET', $params, $options);

    return new UserRequest((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }


  // Parameters:
  //   name (required) - string - Name of user requested
  //   email (required) - string - Email of user requested
  //   details (required) - string - Details of the user request
  public static function create($params = [], $options = []) {
    if (!@$params['name']) {
      throw new \Files\MissingParameterException('Parameter missing: name');
    }

    if (!@$params['email']) {
      throw new \Files\MissingParameterException('Parameter missing: email');
    }

    if (!@$params['details']) {
      throw new \Files\MissingParameterException('Parameter missing: details');
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['email'] && !is_string(@$params['email'])) {
      throw new \Files\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
    }

    if (@$params['details'] && !is_string(@$params['details'])) {
      throw new \Files\InvalidParameterException('$details must be of type string; received ' . gettype(@$params['details']));
    }

    $response = Api::sendRequest('/user_requests', 'POST', $params, $options);

    return new UserRequest((array)(@$response->data ?: []), $options);
  }

}
