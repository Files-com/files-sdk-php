<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class BundleRecipient
 *
 * @package Files
 */
class BundleRecipient {
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

  // string # The recipient's company.
  public function getCompany() {
    return @$this->attributes['company'];
  }

  public function setCompany($value) {
    return $this->attributes['company'] = $value;
  }

  // string # The recipient's name.
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # A note sent to the recipient with the bundle.
  public function getNote() {
    return @$this->attributes['note'];
  }

  public function setNote($value) {
    return $this->attributes['note'] = $value;
  }

  // string # The recipient's email address.
  public function getRecipient() {
    return @$this->attributes['recipient'];
  }

  public function setRecipient($value) {
    return $this->attributes['recipient'] = $value;
  }

  // date-time # When the Bundle was shared with this recipient.
  public function getSentAt() {
    return @$this->attributes['sent_at'];
  }

  public function setSentAt($value) {
    return $this->attributes['sent_at'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // int64 # Bundle to share.
  public function getBundleId() {
    return @$this->attributes['bundle_id'];
  }

  public function setBundleId($value) {
    return $this->attributes['bundle_id'] = $value;
  }

  // boolean # Set to true to share the link with the recipient upon creation.
  public function getShareAfterCreate() {
    return @$this->attributes['share_after_create'];
  }

  public function setShareAfterCreate($value) {
    return $this->attributes['share_after_create'] = $value;
  }

  public function save() {
      if (@$this->attributes['id']) {
        throw new \Files\NotImplementedException('The BundleRecipient object doesn\'t support updates.');
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
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[has_registrations]=desc`). Valid fields are `has_registrations`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `has_registrations`.
  //   bundle_id (required) - int64 - List recipients for the bundle with this ID.
  public static function all($params = [], $options = []) {
    if (!@$params['bundle_id']) {
      throw new \Files\MissingParameterException('Parameter missing: bundle_id');
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

    if (@$params['bundle_id'] && !is_int(@$params['bundle_id'])) {
      throw new \Files\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
    }

    $response = Api::sendRequest('/bundle_recipients', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new BundleRecipient((array)$obj, $options);
    }

    return $return_array;
  }


  

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   bundle_id (required) - int64 - Bundle to share.
  //   recipient (required) - string - Email addresses to share this bundle with.
  //   name - string - Name of recipient.
  //   company - string - Company of recipient.
  //   note - string - Note to include in email.
  //   share_after_create - boolean - Set to true to share the link with the recipient upon creation.
  public static function create($params = [], $options = []) {
    if (!@$params['bundle_id']) {
      throw new \Files\MissingParameterException('Parameter missing: bundle_id');
    }

    if (!@$params['recipient']) {
      throw new \Files\MissingParameterException('Parameter missing: recipient');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['bundle_id'] && !is_int(@$params['bundle_id'])) {
      throw new \Files\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
    }

    if (@$params['recipient'] && !is_string(@$params['recipient'])) {
      throw new \Files\InvalidParameterException('$recipient must be of type string; received ' . gettype(@$params['recipient']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['company'] && !is_string(@$params['company'])) {
      throw new \Files\InvalidParameterException('$company must be of type string; received ' . gettype(@$params['company']));
    }

    if (@$params['note'] && !is_string(@$params['note'])) {
      throw new \Files\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
    }

    $response = Api::sendRequest('/bundle_recipients', 'POST', $params, $options);

    return new BundleRecipient((array)(@$response->data ?: []), $options);
  }

}
