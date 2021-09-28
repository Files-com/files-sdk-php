<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PublicKey
 *
 * @package Files
 */
class PublicKey {
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

  // int64 # Public key ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Public key title
  public function getTitle() {
    return @$this->attributes['title'];
  }

  public function setTitle($value) {
    return $this->attributes['title'] = $value;
  }

  // date-time # Public key created at date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // string # Public key fingerprint
  public function getFingerprint() {
    return @$this->attributes['fingerprint'];
  }

  public function setFingerprint($value) {
    return $this->attributes['fingerprint'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Actual contents of SSH key.
  public function getPublicKey() {
    return @$this->attributes['public_key'];
  }

  public function setPublicKey($value) {
    return $this->attributes['public_key'] = $value;
  }

  // Parameters:
  //   title (required) - string - Internal reference for key.
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

    if (!@$params['title']) {
      if (@$this->title) {
        $params['title'] = $this->title;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: title');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['title'] && !is_string(@$params['title'])) {
      throw new \Files\InvalidParameterException('$title must be of type string; received ' . gettype($title));
    }

    $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'PATCH', $params, $this->options);
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
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

    $response = Api::sendRequest('/public_keys', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new PublicKey((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Public Key ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'GET', $params, $options);

    return new PublicKey((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   title (required) - string - Internal reference for key.
  //   public_key (required) - string - Actual contents of SSH key.
  public static function create($params = [], $options = []) {
    if (!@$params['title']) {
      throw new \Files\MissingParameterException('Parameter missing: title');
    }

    if (!@$params['public_key']) {
      throw new \Files\MissingParameterException('Parameter missing: public_key');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['title'] && !is_string(@$params['title'])) {
      throw new \Files\InvalidParameterException('$title must be of type string; received ' . gettype($title));
    }

    if (@$params['public_key'] && !is_string(@$params['public_key'])) {
      throw new \Files\InvalidParameterException('$public_key must be of type string; received ' . gettype($public_key));
    }

    $response = Api::sendRequest('/public_keys', 'POST', $params, $options);

    return new PublicKey((array)(@$response->data ?: []), $options);
  }
}
