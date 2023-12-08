<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Message
 *
 * @package Files
 */
class Message {
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

  // int64 # Message ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Message subject.
  public function getSubject() {
    return @$this->attributes['subject'];
  }

  public function setSubject($value) {
    return $this->attributes['subject'] = $value;
  }

  // string # Message body.
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // array # Comments.
  public function getComments() {
    return @$this->attributes['comments'];
  }

  public function setComments($value) {
    return $this->attributes['comments'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // int64 # Project to which the message should be attached.
  public function getProjectId() {
    return @$this->attributes['project_id'];
  }

  public function setProjectId($value) {
    return $this->attributes['project_id'] = $value;
  }

  // Parameters:
  //   project_id (required) - int64 - Project to which the message should be attached.
  //   subject (required) - string - Message subject.
  //   body (required) - string - Message body.
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

    if (!@$params['project_id']) {
      if (@$this->project_id) {
        $params['project_id'] = $this->project_id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: project_id');
      }
    }

    if (!@$params['subject']) {
      if (@$this->subject) {
        $params['subject'] = $this->subject;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: subject');
      }
    }

    if (!@$params['body']) {
      if (@$this->body) {
        $params['body'] = $this->body;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: body');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \Files\InvalidParameterException('$project_id must be of type int; received ' . gettype(@$params['project_id']));
    }

    if (@$params['subject'] && !is_string(@$params['subject'])) {
      throw new \Files\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \Files\InvalidParameterException('$body must be of type string; received ' . gettype(@$params['body']));
    }

    $response = Api::sendRequest('/messages/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return new Message((array)(@$response->data ?: []), $options);
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

    $response = Api::sendRequest('/messages/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   project_id (required) - int64 - Project for which to return messages.
  public static function all($params = [], $options = []) {
    if (!@$params['project_id']) {
      throw new \Files\MissingParameterException('Parameter missing: project_id');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \Files\InvalidParameterException('$project_id must be of type int; received ' . gettype(@$params['project_id']));
    }

    $response = Api::sendRequest('/messages', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Message((array)$obj, $options);
    }

    return $return_array;
  }




  // Parameters:
  //   id (required) - int64 - Message ID.
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

    $response = Api::sendRequest('/messages/' . @$params['id'] . '', 'GET', $params, $options);

    return new Message((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }


  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   project_id (required) - int64 - Project to which the message should be attached.
  //   subject (required) - string - Message subject.
  //   body (required) - string - Message body.
  public static function create($params = [], $options = []) {
    if (!@$params['project_id']) {
      throw new \Files\MissingParameterException('Parameter missing: project_id');
    }

    if (!@$params['subject']) {
      throw new \Files\MissingParameterException('Parameter missing: subject');
    }

    if (!@$params['body']) {
      throw new \Files\MissingParameterException('Parameter missing: body');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \Files\InvalidParameterException('$project_id must be of type int; received ' . gettype(@$params['project_id']));
    }

    if (@$params['subject'] && !is_string(@$params['subject'])) {
      throw new \Files\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \Files\InvalidParameterException('$body must be of type string; received ' . gettype(@$params['body']));
    }

    $response = Api::sendRequest('/messages', 'POST', $params, $options);

    return new Message((array)(@$response->data ?: []), $options);
  }

}
