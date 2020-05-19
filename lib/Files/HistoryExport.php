<?php

declare(strict_types=1);

namespace Files;

/**
 * Class HistoryExport
 *
 * @package Files
 */
class HistoryExport {
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

  // int64 # History Export ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // date-time # Start date/time of export range.
  public function getStartAt() {
    return $this->attributes['start_at'];
  }

  public function setStartAt($value) {
    return $this->attributes['start_at'] = $value;
  }

  // date-time # End date/time of export range.
  public function getEndAt() {
    return $this->attributes['end_at'];
  }

  public function setEndAt($value) {
    return $this->attributes['end_at'] = $value;
  }

  // string # Status of export.  Will be: `building` or `ready`
  public function getStatus() {
    return $this->attributes['status'];
  }

  public function setStatus($value) {
    return $this->attributes['status'] = $value;
  }

  // string # Filter results by this this action type. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`
  public function getQueryAction() {
    return $this->attributes['query_action'];
  }

  public function setQueryAction($value) {
    return $this->attributes['query_action'] = $value;
  }

  // string # Filter results by this this interface type. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`
  public function getQueryInterface() {
    return $this->attributes['query_interface'];
  }

  public function setQueryInterface($value) {
    return $this->attributes['query_interface'] = $value;
  }

  // int64 # Return results that are actions performed by the user indiciated by this User ID
  public function getQueryUserId() {
    return $this->attributes['query_user_id'];
  }

  public function setQueryUserId($value) {
    return $this->attributes['query_user_id'] = $value;
  }

  // int64 # Return results that are file actions related to the file indicated by this File ID
  public function getQueryFileId() {
    return $this->attributes['query_file_id'];
  }

  public function setQueryFileId($value) {
    return $this->attributes['query_file_id'] = $value;
  }

  // int64 # Return results that are file actions inside the parent folder specified by this folder ID
  public function getQueryParentId() {
    return $this->attributes['query_parent_id'];
  }

  public function setQueryParentId($value) {
    return $this->attributes['query_parent_id'] = $value;
  }

  // string # Return results that are file actions related to this path.
  public function getQueryPath() {
    return $this->attributes['query_path'];
  }

  public function setQueryPath($value) {
    return $this->attributes['query_path'] = $value;
  }

  // string # Return results that are file actions related to files or folders inside this folder path.
  public function getQueryFolder() {
    return $this->attributes['query_folder'];
  }

  public function setQueryFolder($value) {
    return $this->attributes['query_folder'] = $value;
  }

  // string # Return results that are file moves originating from this path.
  public function getQuerySrc() {
    return $this->attributes['query_src'];
  }

  public function setQuerySrc($value) {
    return $this->attributes['query_src'] = $value;
  }

  // string # Return results that are file moves with this path as destination.
  public function getQueryDestination() {
    return $this->attributes['query_destination'];
  }

  public function setQueryDestination($value) {
    return $this->attributes['query_destination'] = $value;
  }

  // string # Filter results by this IP address.
  public function getQueryIp() {
    return $this->attributes['query_ip'];
  }

  public function setQueryIp($value) {
    return $this->attributes['query_ip'] = $value;
  }

  // string # Filter results by this username.
  public function getQueryUsername() {
    return $this->attributes['query_username'];
  }

  public function setQueryUsername($value) {
    return $this->attributes['query_username'] = $value;
  }

  // string # If searching for Histories about login failures, this parameter restricts results to failures of this specific type.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`
  public function getQueryFailureType() {
    return $this->attributes['query_failure_type'];
  }

  public function setQueryFailureType($value) {
    return $this->attributes['query_failure_type'] = $value;
  }

  // int64 # If searching for Histories about specific objects (such as Users, or API Keys), this paremeter restricts results to objects that match this ID.
  public function getQueryTargetId() {
    return $this->attributes['query_target_id'];
  }

  public function setQueryTargetId($value) {
    return $this->attributes['query_target_id'] = $value;
  }

  // string # If searching for Histories about Users, Groups or other objects with names, this parameter restricts results to objects with this name/username.
  public function getQueryTargetName() {
    return $this->attributes['query_target_name'];
  }

  public function setQueryTargetName($value) {
    return $this->attributes['query_target_name'] = $value;
  }

  // string # If searching for Histories about Permisisons, this parameter restricts results to permissions of this level.
  public function getQueryTargetPermission() {
    return $this->attributes['query_target_permission'];
  }

  public function setQueryTargetPermission($value) {
    return $this->attributes['query_target_permission'] = $value;
  }

  // int64 # If searching for Histories about API keys, this parameter restricts results to API keys created by/for this user ID.
  public function getQueryTargetUserId() {
    return $this->attributes['query_target_user_id'];
  }

  public function setQueryTargetUserId($value) {
    return $this->attributes['query_target_user_id'] = $value;
  }

  // string # If searching for Histories about API keys, this parameter restricts results to API keys created by/for this username.
  public function getQueryTargetUsername() {
    return $this->attributes['query_target_username'];
  }

  public function setQueryTargetUsername($value) {
    return $this->attributes['query_target_username'] = $value;
  }

  // string # If searching for Histories about API keys, this parameter restricts results to API keys associated with this platform.
  public function getQueryTargetPlatform() {
    return $this->attributes['query_target_platform'];
  }

  public function setQueryTargetPlatform($value) {
    return $this->attributes['query_target_platform'] = $value;
  }

  // string # If searching for Histories about API keys, this parameter restricts results to API keys with this permission set.
  public function getQueryTargetPermissionSet() {
    return $this->attributes['query_target_permission_set'];
  }

  public function setQueryTargetPermissionSet($value) {
    return $this->attributes['query_target_permission_set'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
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

    return Api::sendRequest('/history_exports/' . $params['id'] . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      throw new \BadMethodCallException('The HistoryExport object doesn\'t support updates.');
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   user_id - integer - User ID.  Provide a value of `0` to operate the current session's user.
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
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

    $response = Api::sendRequest('/history_exports', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new HistoryExport((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - integer - History Export ID.
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

    $response = Api::sendRequest('/history_exports/' . $params['id'] . '', 'GET', $params);

    return new HistoryExport((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - integer - User ID.  Provide a value of `0` to operate the current session's user.
  //   start_at - string - Start date/time of export range.
  //   end_at - string - End date/time of export range.
  //   query_action - string - Filter results by this this action type. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`
  //   query_interface - string - Filter results by this this interface type. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`
  //   query_user_id - integer - Return results that are actions performed by the user indiciated by this User ID
  //   query_file_id - integer - Return results that are file actions related to the file indicated by this File ID
  //   query_parent_id - integer - Return results that are file actions inside the parent folder specified by this folder ID
  //   query_path - string - Return results that are file actions related to this path.
  //   query_folder - string - Return results that are file actions related to files or folders inside this folder path.
  //   query_src - string - Return results that are file moves originating from this path.
  //   query_destination - string - Return results that are file moves with this path as destination.
  //   query_ip - string - Filter results by this IP address.
  //   query_username - string - Filter results by this username.
  //   query_failure_type - string - If searching for Histories about login failures, this parameter restricts results to failures of this specific type.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`
  //   query_target_id - integer - If searching for Histories about specific objects (such as Users, or API Keys), this paremeter restricts results to objects that match this ID.
  //   query_target_name - string - If searching for Histories about Users, Groups or other objects with names, this parameter restricts results to objects with this name/username.
  //   query_target_permission - string - If searching for Histories about Permisisons, this parameter restricts results to permissions of this level.
  //   query_target_user_id - integer - If searching for Histories about API keys, this parameter restricts results to API keys created by/for this user ID.
  //   query_target_username - string - If searching for Histories about API keys, this parameter restricts results to API keys created by/for this username.
  //   query_target_platform - string - If searching for Histories about API keys, this parameter restricts results to API keys associated with this platform.
  //   query_target_permission_set - string - If searching for Histories about API keys, this parameter restricts results to API keys with this permission set.
  public static function create($params = [], $options = []) {
    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['start_at'] && !is_string($params['start_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $start_at must be of type string; received ' . gettype($start_at));
    }

    if ($params['end_at'] && !is_string($params['end_at'])) {
      throw new \InvalidArgumentException('Bad parameter: $end_at must be of type string; received ' . gettype($end_at));
    }

    if ($params['query_action'] && !is_string($params['query_action'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_action must be of type string; received ' . gettype($query_action));
    }

    if ($params['query_interface'] && !is_string($params['query_interface'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_interface must be of type string; received ' . gettype($query_interface));
    }

    if ($params['query_user_id'] && !is_int($params['query_user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_user_id must be of type int; received ' . gettype($query_user_id));
    }

    if ($params['query_file_id'] && !is_int($params['query_file_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_file_id must be of type int; received ' . gettype($query_file_id));
    }

    if ($params['query_parent_id'] && !is_int($params['query_parent_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_parent_id must be of type int; received ' . gettype($query_parent_id));
    }

    if ($params['query_path'] && !is_string($params['query_path'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_path must be of type string; received ' . gettype($query_path));
    }

    if ($params['query_folder'] && !is_string($params['query_folder'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_folder must be of type string; received ' . gettype($query_folder));
    }

    if ($params['query_src'] && !is_string($params['query_src'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_src must be of type string; received ' . gettype($query_src));
    }

    if ($params['query_destination'] && !is_string($params['query_destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_destination must be of type string; received ' . gettype($query_destination));
    }

    if ($params['query_ip'] && !is_string($params['query_ip'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_ip must be of type string; received ' . gettype($query_ip));
    }

    if ($params['query_username'] && !is_string($params['query_username'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_username must be of type string; received ' . gettype($query_username));
    }

    if ($params['query_failure_type'] && !is_string($params['query_failure_type'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_failure_type must be of type string; received ' . gettype($query_failure_type));
    }

    if ($params['query_target_id'] && !is_int($params['query_target_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_id must be of type int; received ' . gettype($query_target_id));
    }

    if ($params['query_target_name'] && !is_string($params['query_target_name'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_name must be of type string; received ' . gettype($query_target_name));
    }

    if ($params['query_target_permission'] && !is_string($params['query_target_permission'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_permission must be of type string; received ' . gettype($query_target_permission));
    }

    if ($params['query_target_user_id'] && !is_int($params['query_target_user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_user_id must be of type int; received ' . gettype($query_target_user_id));
    }

    if ($params['query_target_username'] && !is_string($params['query_target_username'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_username must be of type string; received ' . gettype($query_target_username));
    }

    if ($params['query_target_platform'] && !is_string($params['query_target_platform'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_platform must be of type string; received ' . gettype($query_target_platform));
    }

    if ($params['query_target_permission_set'] && !is_string($params['query_target_permission_set'])) {
      throw new \InvalidArgumentException('Bad parameter: $query_target_permission_set must be of type string; received ' . gettype($query_target_permission_set));
    }

    $response = Api::sendRequest('/history_exports', 'POST', $params);

    return new HistoryExport((array)$response->data, $options);
  }
}
