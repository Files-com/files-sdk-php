<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ExternalEvent
 *
 * @package Files
 */
class ExternalEvent {
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

  // int64 # Event ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Type of event being recorded.
  public function getEventType() {
    return @$this->attributes['event_type'];
  }

  public function setEventType($value) {
    return $this->attributes['event_type'] = $value;
  }

  // string # Status of event.
  public function getStatus() {
    return @$this->attributes['status'];
  }

  public function setStatus($value) {
    return $this->attributes['status'] = $value;
  }

  // string # Event body
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // date-time # External event create date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // string # Link to log file.
  public function getBodyUrl() {
    return @$this->attributes['body_url'];
  }

  public function setBodyUrl($value) {
    return $this->attributes['body_url'] = $value;
  }

  // int64 # Folder Behavior ID
  public function getFolderBehaviorId() {
    return @$this->attributes['folder_behavior_id'];
  }

  public function setFolderBehaviorId($value) {
    return $this->attributes['folder_behavior_id'] = $value;
  }

  // int64 # For sync events, the number of files handled successfully.
  public function getSuccessfulFiles() {
    return @$this->attributes['successful_files'];
  }

  public function setSuccessfulFiles($value) {
    return $this->attributes['successful_files'] = $value;
  }

  // int64 # For sync events, the number of files that encountered errors.
  public function getErroredFiles() {
    return @$this->attributes['errored_files'];
  }

  public function setErroredFiles($value) {
    return $this->attributes['errored_files'] = $value;
  }

  // int64 # For sync events, the total number of bytes synced.
  public function getBytesSynced() {
    return @$this->attributes['bytes_synced'];
  }

  public function setBytesSynced($value) {
    return $this->attributes['bytes_synced'] = $value;
  }

  // string # Associated Remote Server type, if any
  public function getRemoteServerType() {
    return @$this->attributes['remote_server_type'];
  }

  public function setRemoteServerType($value) {
    return $this->attributes['remote_server_type'] = $value;
  }

  public function save() {
      if (@$this->attributes['id']) {
        throw new \BadMethodCallException('The ExternalEvent object doesn\'t support updates.');
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `remote_server_type`, `event_type`, `created_at`, `status`, `site_id` or `folder_behavior_id`.
  //   filter - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  //   filter_gt - object - If set, return records where the specifiied field is greater than the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  //   filter_gteq - object - If set, return records where the specifiied field is greater than or equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  //   filter_like - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  //   filter_lt - object - If set, return records where the specifiied field is less than the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  //   filter_lteq - object - If set, return records where the specifiied field is less than or equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status` or `folder_behavior_id`.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/external_events', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new ExternalEvent((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - External Event ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/external_events/' . @$params['id'] . '', 'GET', $params, $options);

    return new ExternalEvent((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   status (required) - string - Status of event.
  //   body (required) - string - Event body
  public static function create($params = [], $options = []) {
    if (!@$params['status']) {
      throw new \Error('Parameter missing: status');
    }

    if (!@$params['body']) {
      throw new \Error('Parameter missing: body');
    }

    if (@$params['status'] && !is_string(@$params['status'])) {
      throw new \InvalidArgumentException('Bad parameter: $status must be of type string; received ' . gettype($status));
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \InvalidArgumentException('Bad parameter: $body must be of type string; received ' . gettype($body));
    }

    $response = Api::sendRequest('/external_events', 'POST', $params, $options);

    return new ExternalEvent((array)(@$response->data ?: []), $options);
  }
}
