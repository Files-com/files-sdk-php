<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Bundle
 *
 * @package Files
 */
class Bundle {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // string # Bundle code.  This code forms the end part of the Public URL.
  public function getCode() {
    return $this->attributes['code'];
  }

  public function setCode($value) {
    return $this->attributes['code'] = $value;
  }

  // string # Public URL of Share Link
  public function getUrl() {
    return $this->attributes['url'];
  }

  public function setUrl($value) {
    return $this->attributes['url'] = $value;
  }

  // string # Public description
  public function getDescription() {
    return $this->attributes['description'];
  }

  public function setDescription($value) {
    return $this->attributes['description'] = $value;
  }

  // boolean # Is this bundle password protected?
  public function getPasswordProtected() {
    return $this->attributes['password_protected'];
  }

  public function setPasswordProtected($value) {
    return $this->attributes['password_protected'] = $value;
  }

  // boolean # Show a registration page that captures the downloader's name and email address?
  public function getRequireRegistration() {
    return $this->attributes['require_registration'];
  }

  public function setRequireRegistration($value) {
    return $this->attributes['require_registration'] = $value;
  }

  // boolean # Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  public function getRequireShareRecipient() {
    return $this->attributes['require_share_recipient'];
  }

  public function setRequireShareRecipient($value) {
    return $this->attributes['require_share_recipient'] = $value;
  }

  // string # Legal text that must be agreed to prior to accessing Bundle.
  public function getClickwrapBody() {
    return $this->attributes['clickwrap_body'];
  }

  public function setClickwrapBody($value) {
    return $this->attributes['clickwrap_body'] = $value;
  }

  // int64 # Bundle ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // date-time # Bundle created at date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // date-time # Bundle expiration date/time
  public function getExpiresAt() {
    return $this->attributes['expires_at'];
  }

  public function setExpiresAt($value) {
    return $this->attributes['expires_at'] = $value;
  }

  // int64 # Maximum number of times bundle can be accessed
  public function getMaxUses() {
    return $this->attributes['max_uses'];
  }

  public function setMaxUses($value) {
    return $this->attributes['max_uses'] = $value;
  }

  // string # Bundle internal note
  public function getNote() {
    return $this->attributes['note'];
  }

  public function setNote($value) {
    return $this->attributes['note'] = $value;
  }

  // int64 # Bundle creator user ID
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Bundle creator username
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // int64 # ID of the clickwrap to use with this bundle.
  public function getClickwrapId() {
    return $this->attributes['clickwrap_id'];
  }

  public function setClickwrapId($value) {
    return $this->attributes['clickwrap_id'] = $value;
  }

  // int64 # ID of the associated inbox, if available.
  public function getInboxId() {
    return $this->attributes['inbox_id'];
  }

  public function setInboxId($value) {
    return $this->attributes['inbox_id'] = $value;
  }

  // array # A list of paths in this bundle
  public function getPaths() {
    return $this->attributes['paths'];
  }

  public function setPaths($value) {
    return $this->attributes['paths'] = $value;
  }

  // string # Password for this bundle.
  public function getPassword() {
    return $this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // Send email(s) with a link to bundle
  //
  // Parameters:
  //   to (required) - array(string) - A list of email addresses to share this bundle with.
  //   note - string - Note to include in email.
  public function share($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if ($params['to'] && !is_array($params['to'])) {
      throw new \InvalidArgumentException('Bad parameter: $to must be of type array; received ' . gettype($to));
    }
    if ($params['note'] && !is_string($params['note'])) {
      throw new \InvalidArgumentException('Bad parameter: $note must be of type string; received ' . gettype($note));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    if (!$params['to']) {
      if ($this->to) {
        $params['to'] = $this->to;
      } else {
        throw new \Error('Parameter missing: to');
      }
    }

    return Api::sendRequest('/bundles/' . $params['id'] . '/share', 'POST', $params, $this->options);
  }

  // Parameters:
  //   password - string - Password for this bundle.
  //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
  //   code - string - Bundle code.  This code forms the end part of the Public URL.
  //   description - string - Public description
  //   expires_at - string - Bundle expiration date/time
  //   inbox_id - int64 - ID of the associated inbox, if available.
  //   max_uses - int64 - Maximum number of times bundle can be accessed
  //   note - string - Bundle internal note
  //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
  //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }
    if ($params['clickwrap_id'] && !is_int($params['clickwrap_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $clickwrap_id must be of type int; received ' . gettype($clickwrap_id));
    }
    if ($params['code'] && !is_string($params['code'])) {
      throw new \InvalidArgumentException('Bad parameter: $code must be of type string; received ' . gettype($code));
    }
    if ($params['description'] && !is_string($params['description'])) {
      throw new \InvalidArgumentException('Bad parameter: $description must be of type string; received ' . gettype($description));
    }
    if ($params['expires_at'] && !is_string($params['expires_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $expires_at must be of type string; received ' . gettype($expires_at));
    }
    if ($params['inbox_id'] && !is_int($params['inbox_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $inbox_id must be of type int; received ' . gettype($inbox_id));
    }
    if ($params['max_uses'] && !is_int($params['max_uses'])) {
      throw new \InvalidArgumentException('Bad parameter: $max_uses must be of type int; received ' . gettype($max_uses));
    }
    if ($params['note'] && !is_string($params['note'])) {
      throw new \InvalidArgumentException('Bad parameter: $note must be of type string; received ' . gettype($note));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/bundles/' . $params['id'] . '', 'PATCH', $params, $this->options);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/bundles/' . $params['id'] . '', 'DELETE', $params, $this->options);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      return $this->update($this->attributes);
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   cursor - string - Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `site_id`, `created_at` or `code`.
  //   filter - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `created_at`.
  //   filter_gt - object - If set, return records where the specifiied field is greater than the supplied value. Valid fields are `created_at`.
  //   filter_gteq - object - If set, return records where the specifiied field is greater than or equal to the supplied value. Valid fields are `created_at`.
  //   filter_like - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `created_at`.
  //   filter_lt - object - If set, return records where the specifiied field is less than the supplied value. Valid fields are `created_at`.
  //   filter_lteq - object - If set, return records where the specifiied field is less than or equal to the supplied value. Valid fields are `created_at`.
  public static function list($params = [], $options = []) {
    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if ($params['cursor'] && !is_string($params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    $response = Api::sendRequest('/bundles', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Bundle((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Bundle ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/bundles/' . $params['id'] . '', 'GET', $params, $options);

    return new Bundle((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   paths (required) - array(string) - A list of paths to include in this bundle.
  //   password - string - Password for this bundle.
  //   expires_at - string - Bundle expiration date/time
  //   max_uses - int64 - Maximum number of times bundle can be accessed
  //   description - string - Public description
  //   note - string - Bundle internal note
  //   code - string - Bundle code.  This code forms the end part of the Public URL.
  //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
  //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
  //   inbox_id - int64 - ID of the associated inbox, if available.
  //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  public static function create($params = [], $options = []) {
    if (!$params['paths']) {
      throw new \Error('Parameter missing: paths');
    }

    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['paths'] && !is_array($params['paths'])) {
      throw new \InvalidArgumentException('Bad parameter: $paths must be of type array; received ' . gettype($paths));
    }

    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }

    if ($params['expires_at'] && !is_string($params['expires_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $expires_at must be of type string; received ' . gettype($expires_at));
    }

    if ($params['max_uses'] && !is_int($params['max_uses'])) {
      throw new \InvalidArgumentException('Bad parameter: $max_uses must be of type int; received ' . gettype($max_uses));
    }

    if ($params['description'] && !is_string($params['description'])) {
      throw new \InvalidArgumentException('Bad parameter: $description must be of type string; received ' . gettype($description));
    }

    if ($params['note'] && !is_string($params['note'])) {
      throw new \InvalidArgumentException('Bad parameter: $note must be of type string; received ' . gettype($note));
    }

    if ($params['code'] && !is_string($params['code'])) {
      throw new \InvalidArgumentException('Bad parameter: $code must be of type string; received ' . gettype($code));
    }

    if ($params['clickwrap_id'] && !is_int($params['clickwrap_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $clickwrap_id must be of type int; received ' . gettype($clickwrap_id));
    }

    if ($params['inbox_id'] && !is_int($params['inbox_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $inbox_id must be of type int; received ' . gettype($inbox_id));
    }

    $response = Api::sendRequest('/bundles', 'POST', $params, $options);

    return new Bundle((array)$response->data, $options);
  }
}
