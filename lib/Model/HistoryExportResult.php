<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class HistoryExportResult
 *
 * @package Files
 */
class HistoryExportResult {
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

  // int64 # Action ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // int64 # When the action happened
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // int64 # User ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  // int64 # File ID related to the action
  public function getFileId() {
    return @$this->attributes['file_id'];
  }

  // int64 # ID of the parent folder
  public function getParentId() {
    return @$this->attributes['parent_id'];
  }

  // string # Path of the related action This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  // string # Folder in which the action occurred
  public function getFolder() {
    return @$this->attributes['folder'];
  }

  // string # File move originated from this path
  public function getSrc() {
    return @$this->attributes['src'];
  }

  // string # File moved to this destination folder
  public function getDestination() {
    return @$this->attributes['destination'];
  }

  // string # Client IP that performed the action
  public function getIp() {
    return @$this->attributes['ip'];
  }

  // string # Username of the user that performed the action
  public function getUsername() {
    return @$this->attributes['username'];
  }

  // string # What action was taken. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`
  public function getAction() {
    return @$this->attributes['action'];
  }

  // string # The type of login failure, if applicable.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`, `country_mismatch`
  public function getFailureType() {
    return @$this->attributes['failure_type'];
  }

  // string # Inteface through which the action was taken. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`, `office`, `mobile`, `as2`, `inbound_email`, `remote`
  public function getInterface() {
    return @$this->attributes['interface'];
  }

  // int64 # ID of the object (such as Users, or API Keys) on which the action was taken
  public function getTargetId() {
    return @$this->attributes['target_id'];
  }

  // string # Name of the User, Group or other object with a name related to this action
  public function getTargetName() {
    return @$this->attributes['target_name'];
  }

  // string # Permission level of the action
  public function getTargetPermission() {
    return @$this->attributes['target_permission'];
  }

  // boolean # Whether or not the action was recursive
  public function getTargetRecursive() {
    return @$this->attributes['target_recursive'];
  }

  // int64 # If searching for Histories about API keys, this is when the API key will expire
  public function getTargetExpiresAt() {
    return @$this->attributes['target_expires_at'];
  }

  // string # If searching for Histories about API keys, this represents the permission set of the associated  API key
  public function getTargetPermissionSet() {
    return @$this->attributes['target_permission_set'];
  }

  // string # If searching for Histories about API keys, this is the platform on which the action was taken
  public function getTargetPlatform() {
    return @$this->attributes['target_platform'];
  }

  // string # If searching for Histories about API keys, this is the username on which the action was taken
  public function getTargetUsername() {
    return @$this->attributes['target_username'];
  }

  // int64 # If searching for Histories about API keys, this is the User ID on which the action was taken
  public function getTargetUserId() {
    return @$this->attributes['target_user_id'];
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   history_export_id (required) - int64 - ID of the associated history export.
  public static function list($params = [], $options = []) {
    if (!@$params['history_export_id']) {
      throw new \Files\MissingParameterException('Parameter missing: history_export_id');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['history_export_id'] && !is_int(@$params['history_export_id'])) {
      throw new \Files\InvalidParameterException('$history_export_id must be of type int; received ' . gettype($history_export_id));
    }

    $response = Api::sendRequest('/history_export_results', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new HistoryExportResult((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
