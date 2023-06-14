<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Notification
 *
 * @package Files
 */
class Notification {
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

  // int64 # Notification ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Folder path to notify on This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // int64 # Notification group id
  public function getGroupId() {
    return @$this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // string # Group name if applicable
  public function getGroupName() {
    return @$this->attributes['group_name'];
  }

  public function setGroupName($value) {
    return $this->attributes['group_name'] = $value;
  }

  // array # Only notify on actions made by a member of one of the specified groups
  public function getTriggeringGroupIds() {
    return @$this->attributes['triggering_group_ids'];
  }

  public function setTriggeringGroupIds($value) {
    return $this->attributes['triggering_group_ids'] = $value;
  }

  // array # Only notify on actions made one of the specified users
  public function getTriggeringUserIds() {
    return @$this->attributes['triggering_user_ids'];
  }

  public function setTriggeringUserIds($value) {
    return $this->attributes['triggering_user_ids'] = $value;
  }

  // boolean # Notify when actions are performed by a share recipient?
  public function getTriggerByShareRecipients() {
    return @$this->attributes['trigger_by_share_recipients'];
  }

  public function setTriggerByShareRecipients($value) {
    return $this->attributes['trigger_by_share_recipients'] = $value;
  }

  // boolean # Trigger notification on notification user actions?
  public function getNotifyUserActions() {
    return @$this->attributes['notify_user_actions'];
  }

  public function setNotifyUserActions($value) {
    return $this->attributes['notify_user_actions'] = $value;
  }

  // boolean # Triggers notification when copying files to this path
  public function getNotifyOnCopy() {
    return @$this->attributes['notify_on_copy'];
  }

  public function setNotifyOnCopy($value) {
    return $this->attributes['notify_on_copy'] = $value;
  }

  // boolean # Triggers notification when deleting files from this path
  public function getNotifyOnDelete() {
    return @$this->attributes['notify_on_delete'];
  }

  public function setNotifyOnDelete($value) {
    return $this->attributes['notify_on_delete'] = $value;
  }

  // boolean # Triggers notification when downloading files from this path
  public function getNotifyOnDownload() {
    return @$this->attributes['notify_on_download'];
  }

  public function setNotifyOnDownload($value) {
    return $this->attributes['notify_on_download'] = $value;
  }

  // boolean # Triggers notification when moving files to this path
  public function getNotifyOnMove() {
    return @$this->attributes['notify_on_move'];
  }

  public function setNotifyOnMove($value) {
    return $this->attributes['notify_on_move'] = $value;
  }

  // boolean # Triggers notification when uploading new files to this path
  public function getNotifyOnUpload() {
    return @$this->attributes['notify_on_upload'];
  }

  public function setNotifyOnUpload($value) {
    return $this->attributes['notify_on_upload'] = $value;
  }

  // boolean # Enable notifications for each subfolder in this path
  public function getRecursive() {
    return @$this->attributes['recursive'];
  }

  public function setRecursive($value) {
    return $this->attributes['recursive'] = $value;
  }

  // string # The time interval that notifications are aggregated to
  public function getSendInterval() {
    return @$this->attributes['send_interval'];
  }

  public function setSendInterval($value) {
    return $this->attributes['send_interval'] = $value;
  }

  // string # Custom message to include in notification emails.
  public function getMessage() {
    return @$this->attributes['message'];
  }

  public function setMessage($value) {
    return $this->attributes['message'] = $value;
  }

  // array # Array of filenames (possibly with wildcards) to match for action path
  public function getTriggeringFilenames() {
    return @$this->attributes['triggering_filenames'];
  }

  public function setTriggeringFilenames($value) {
    return $this->attributes['triggering_filenames'] = $value;
  }

  // boolean # Is the user unsubscribed from this notification?
  public function getUnsubscribed() {
    return @$this->attributes['unsubscribed'];
  }

  public function setUnsubscribed($value) {
    return $this->attributes['unsubscribed'] = $value;
  }

  // string # The reason that the user unsubscribed
  public function getUnsubscribedReason() {
    return @$this->attributes['unsubscribed_reason'];
  }

  public function setUnsubscribedReason($value) {
    return $this->attributes['unsubscribed_reason'] = $value;
  }

  // int64 # Notification user ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Notification username
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // boolean # If true, it means that the recipient at this user's email address has manually unsubscribed from all emails, or had their email "hard bounce", which means that we are unable to send mail to this user's current email address. Notifications will resume if the user changes their email address.
  public function getSuppressedEmail() {
    return @$this->attributes['suppressed_email'];
  }

  public function setSuppressedEmail($value) {
    return $this->attributes['suppressed_email'] = $value;
  }

  // Parameters:
  //   notify_on_copy - boolean - If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
  //   notify_on_delete - boolean - Triggers notification when deleting files from this path
  //   notify_on_download - boolean - Triggers notification when downloading files from this path
  //   notify_on_move - boolean - Triggers notification when moving files to this path
  //   notify_on_upload - boolean - Triggers notification when uploading new files to this path
  //   notify_user_actions - boolean - If `true` actions initiated by the user will still result in a notification
  //   recursive - boolean - If `true`, enable notifications for each subfolder in this path
  //   send_interval - string - The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
  //   message - string - Custom message to include in notification emails.
  //   triggering_filenames - array(string) - Array of filenames (possibly with wildcards) to match for action path
  //   triggering_group_ids - array(int64) - Only notify on actions made by a member of one of the specified groups
  //   triggering_user_ids - array(int64) - Only notify on actions made one of the specified users
  //   trigger_by_share_recipients - boolean - Notify when actions are performed by a share recipient?
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

    if (@$params['send_interval'] && !is_string(@$params['send_interval'])) {
      throw new \Files\InvalidParameterException('$send_interval must be of type string; received ' . gettype(@$params['send_interval']));
    }

    if (@$params['message'] && !is_string(@$params['message'])) {
      throw new \Files\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
    }

    if (@$params['triggering_filenames'] && !is_array(@$params['triggering_filenames'])) {
      throw new \Files\InvalidParameterException('$triggering_filenames must be of type array; received ' . gettype(@$params['triggering_filenames']));
    }

    if (@$params['triggering_group_ids'] && !is_array(@$params['triggering_group_ids'])) {
      throw new \Files\InvalidParameterException('$triggering_group_ids must be of type array; received ' . gettype(@$params['triggering_group_ids']));
    }

    if (@$params['triggering_user_ids'] && !is_array(@$params['triggering_user_ids'])) {
      throw new \Files\InvalidParameterException('$triggering_user_ids must be of type array; received ' . gettype(@$params['triggering_user_ids']));
    }

    $response = Api::sendRequest('/notifications/' . @$params['id'] . '', 'PATCH', $params, $this->options);
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

    $response = Api::sendRequest('/notifications/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   user_id - int64 - DEPRECATED: Show notifications for this User ID. Use `filter[user_id]` instead.
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[path]=desc`). Valid fields are `path`, `user_id` or `group_id`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `user_id` or `group_id`.
  //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
  //   path - string - Show notifications for this Path.
  //   include_ancestors - boolean - If `include_ancestors` is `true` and `path` is specified, include notifications for any parent paths. Ignored if `path` is not specified.
  //   group_id - string
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

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['group_id'] && !is_string(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type string; received ' . gettype(@$params['group_id']));
    }

    $response = Api::sendRequest('/notifications', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Notification((array)$obj, $options);
    }

    return $return_array;
  }


  

  // Parameters:
  //   id (required) - int64 - Notification ID.
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

    $response = Api::sendRequest('/notifications/' . @$params['id'] . '', 'GET', $params, $options);

    return new Notification((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }
  

  // Parameters:
  //   user_id - int64 - The id of the user to notify. Provide `user_id`, `username` or `group_id`.
  //   notify_on_copy - boolean - If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
  //   notify_on_delete - boolean - Triggers notification when deleting files from this path
  //   notify_on_download - boolean - Triggers notification when downloading files from this path
  //   notify_on_move - boolean - Triggers notification when moving files to this path
  //   notify_on_upload - boolean - Triggers notification when uploading new files to this path
  //   notify_user_actions - boolean - If `true` actions initiated by the user will still result in a notification
  //   recursive - boolean - If `true`, enable notifications for each subfolder in this path
  //   send_interval - string - The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
  //   message - string - Custom message to include in notification emails.
  //   triggering_filenames - array(string) - Array of filenames (possibly with wildcards) to match for action path
  //   triggering_group_ids - array(int64) - Only notify on actions made by a member of one of the specified groups
  //   triggering_user_ids - array(int64) - Only notify on actions made one of the specified users
  //   trigger_by_share_recipients - boolean - Notify when actions are performed by a share recipient?
  //   group_id - int64 - The ID of the group to notify.  Provide `user_id`, `username` or `group_id`.
  //   path - string - Path
  //   username - string - The username of the user to notify.  Provide `user_id`, `username` or `group_id`.
  public static function create($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['send_interval'] && !is_string(@$params['send_interval'])) {
      throw new \Files\InvalidParameterException('$send_interval must be of type string; received ' . gettype(@$params['send_interval']));
    }

    if (@$params['message'] && !is_string(@$params['message'])) {
      throw new \Files\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
    }

    if (@$params['triggering_filenames'] && !is_array(@$params['triggering_filenames'])) {
      throw new \Files\InvalidParameterException('$triggering_filenames must be of type array; received ' . gettype(@$params['triggering_filenames']));
    }

    if (@$params['triggering_group_ids'] && !is_array(@$params['triggering_group_ids'])) {
      throw new \Files\InvalidParameterException('$triggering_group_ids must be of type array; received ' . gettype(@$params['triggering_group_ids']));
    }

    if (@$params['triggering_user_ids'] && !is_array(@$params['triggering_user_ids'])) {
      throw new \Files\InvalidParameterException('$triggering_user_ids must be of type array; received ' . gettype(@$params['triggering_user_ids']));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
    }

    $response = Api::sendRequest('/notifications', 'POST', $params, $options);

    return new Notification((array)(@$response->data ?: []), $options);
  }

}
