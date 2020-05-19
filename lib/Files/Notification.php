<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Notification
 *
 * @package Files
 */
class Notification {
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

  // int64 # Notification ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Folder path to notify on This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // int64 # Notification group id
  public function getGroupId() {
    return $this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // string # Group name if applicable
  public function getGroupName() {
    return $this->attributes['group_name'];
  }

  public function setGroupName($value) {
    return $this->attributes['group_name'] = $value;
  }

  // boolean # Trigger notification on notification user actions?
  public function getNotifyUserActions() {
    return $this->attributes['notify_user_actions'];
  }

  public function setNotifyUserActions($value) {
    return $this->attributes['notify_user_actions'] = $value;
  }

  // boolean # Triggers notification when moving or copying files to this path
  public function getNotifyOnCopy() {
    return $this->attributes['notify_on_copy'];
  }

  public function setNotifyOnCopy($value) {
    return $this->attributes['notify_on_copy'] = $value;
  }

  // string # The time interval that notifications are aggregated to
  public function getSendInterval() {
    return $this->attributes['send_interval'];
  }

  public function setSendInterval($value) {
    return $this->attributes['send_interval'] = $value;
  }

  // boolean # Is the user unsubscribed from this notification?
  public function getUnsubscribed() {
    return $this->attributes['unsubscribed'];
  }

  public function setUnsubscribed($value) {
    return $this->attributes['unsubscribed'] = $value;
  }

  // string # The reason that the user unsubscribed
  public function getUnsubscribedReason() {
    return $this->attributes['unsubscribed_reason'];
  }

  public function setUnsubscribedReason($value) {
    return $this->attributes['unsubscribed_reason'] = $value;
  }

  // int64 # Notification user ID
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Notification username
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // boolean # If true, it means that the recipient at this user's email address has manually unsubscribed from all emails, or had their email "hard bounce", which means that we are unable to send mail to this user's current email address. Notifications will resume if the user changes their email address.
  public function getSuppressedEmail() {
    return $this->attributes['suppressed_email'];
  }

  public function setSuppressedEmail($value) {
    return $this->attributes['suppressed_email'] = $value;
  }

  // Parameters:
  //   notify_on_copy - boolean - If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
  //   notify_user_actions - boolean - If `true` actions initiated by the user will still result in a notification
  //   send_interval - string - The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
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
    if ($params['send_interval'] && !is_string($params['send_interval'])) {
      throw new \InvalidArgumentException('Bad parameter: $send_interval must be of type string; received ' . gettype($send_interval));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/notifications/' . $params['id'] . '', 'PATCH', $params);
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

    return Api::sendRequest('/notifications/' . $params['id'] . '', 'DELETE', $params);
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
  //   user_id - integer - Show notifications for this User ID.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   group_id - integer - Show notifications for this Group ID.
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

    if ($params['group_id'] && !is_int($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type int; received ' . gettype($group_id));
    }

    $response = Api::sendRequest('/notifications', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Notification((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - integer - Notification ID.
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

    $response = Api::sendRequest('/notifications/' . $params['id'] . '', 'GET', $params);

    return new Notification((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - integer - The id of the user to notify. Provide `user_id`, `username` or `group_id`.
  //   notify_on_copy - boolean - If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
  //   notify_user_actions - boolean - If `true` actions initiated by the user will still result in a notification
  //   send_interval - string - The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
  //   group_id - integer - The ID of the group to notify.  Provide `user_id`, `username` or `group_id`.
  //   path - string - Path
  //   username - string - The username of the user to notify.  Provide `user_id`, `username` or `group_id`.
  public static function create($params = [], $options = []) {
    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['send_interval'] && !is_string($params['send_interval'])) {
      throw new \InvalidArgumentException('Bad parameter: $send_interval must be of type string; received ' . gettype($send_interval));
    }

    if ($params['group_id'] && !is_int($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type int; received ' . gettype($group_id));
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/notifications', 'POST', $params);

    return new Notification((array)$response->data, $options);
  }
}
