<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UserCipherUse
 *
 * @package Files
 */
class UserCipherUse {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # UserCipherUse ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // string # The protocol and cipher employed
  public function getProtocolCipher() {
    return @$this->attributes['protocol_cipher'];
  }

  // date-time # The earliest recorded use of this combination of interface and protocol and cipher (for this user)
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // string # The interface accessed
  public function getInterface() {
    return @$this->attributes['interface'];
  }

  // date-time # The most recent use of this combination of interface and protocol and cipher (for this user)
  public function getUpdatedAt() {
    return @$this->attributes['updated_at'];
  }

  // int64 # ID of the user who performed this access
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   cursor - string - Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  public static function list($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['page'] && !is_int(@$params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['action'] && !is_string(@$params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    $response = Api::sendRequest('/user_cipher_uses', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new UserCipherUse((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
