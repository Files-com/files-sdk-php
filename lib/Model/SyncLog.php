<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SyncLog
 *
 * @package Files
 */
class SyncLog {
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

  // date-time # Start Time of Action
  public function getTimestamp() {
    return @$this->attributes['timestamp'];
  }

  // int64 # Sync ID
  public function getSyncId() {
    return @$this->attributes['sync_id'];
  }

  // int64 # External Event ID
  public function getExternalEventId() {
    return @$this->attributes['external_event_id'];
  }

  // string # Error type, if applicable
  public function getErrorType() {
    return @$this->attributes['error_type'];
  }

  // string # Message
  public function getMessage() {
    return @$this->attributes['message'];
  }

  // string # Operation type
  public function getOperation() {
    return @$this->attributes['operation'];
  }

  // string # File path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  // string # Status
  public function getStatus() {
    return @$this->attributes['status'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `external_event_id`, `operation`, `status`, `sync_id` or `type`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ type ]`, `[ start_date, end_date ]`, `[ start_date, external_event_id ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, sync_id ]`, `[ start_date, type ]`, `[ end_date, external_event_id ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, sync_id ]`, `[ end_date, type ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, type ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, type ]`, `[ status, sync_id ]`, `[ status, type ]`, `[ sync_id, type ]`, `[ start_date, end_date, external_event_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, sync_id ]`, `[ start_date, end_date, type ]`, `[ start_date, external_event_id, operation ]`, `[ start_date, external_event_id, status ]`, `[ start_date, external_event_id, sync_id ]`, `[ start_date, external_event_id, type ]`, `[ start_date, operation, status ]`, `[ start_date, operation, sync_id ]`, `[ start_date, operation, type ]`, `[ start_date, status, sync_id ]`, `[ start_date, status, type ]`, `[ start_date, sync_id, type ]`, `[ end_date, external_event_id, operation ]`, `[ end_date, external_event_id, status ]`, `[ end_date, external_event_id, sync_id ]`, `[ end_date, external_event_id, type ]`, `[ end_date, operation, status ]`, `[ end_date, operation, sync_id ]`, `[ end_date, operation, type ]`, `[ end_date, status, sync_id ]`, `[ end_date, status, type ]`, `[ end_date, sync_id, type ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, type ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, type ]`, `[ external_event_id, sync_id, type ]`, `[ operation, status, sync_id ]`, `[ operation, status, type ]`, `[ operation, sync_id, type ]`, `[ status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation ]`, `[ start_date, end_date, external_event_id, status ]`, `[ start_date, end_date, external_event_id, sync_id ]`, `[ start_date, end_date, external_event_id, type ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, sync_id ]`, `[ start_date, end_date, operation, type ]`, `[ start_date, end_date, status, sync_id ]`, `[ start_date, end_date, status, type ]`, `[ start_date, end_date, sync_id, type ]`, `[ start_date, external_event_id, operation, status ]`, `[ start_date, external_event_id, operation, sync_id ]`, `[ start_date, external_event_id, operation, type ]`, `[ start_date, external_event_id, status, sync_id ]`, `[ start_date, external_event_id, status, type ]`, `[ start_date, external_event_id, sync_id, type ]`, `[ start_date, operation, status, sync_id ]`, `[ start_date, operation, status, type ]`, `[ start_date, operation, sync_id, type ]`, `[ start_date, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status ]`, `[ end_date, external_event_id, operation, sync_id ]`, `[ end_date, external_event_id, operation, type ]`, `[ end_date, external_event_id, status, sync_id ]`, `[ end_date, external_event_id, status, type ]`, `[ end_date, external_event_id, sync_id, type ]`, `[ end_date, operation, status, sync_id ]`, `[ end_date, operation, status, type ]`, `[ end_date, operation, sync_id, type ]`, `[ end_date, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, type ]`, `[ external_event_id, operation, sync_id, type ]`, `[ external_event_id, status, sync_id, type ]`, `[ operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status ]`, `[ start_date, end_date, external_event_id, operation, sync_id ]`, `[ start_date, end_date, external_event_id, operation, type ]`, `[ start_date, end_date, external_event_id, status, sync_id ]`, `[ start_date, end_date, external_event_id, status, type ]`, `[ start_date, end_date, external_event_id, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id ]`, `[ start_date, end_date, operation, status, type ]`, `[ start_date, end_date, operation, sync_id, type ]`, `[ start_date, end_date, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id ]`, `[ start_date, external_event_id, operation, status, type ]`, `[ start_date, external_event_id, operation, sync_id, type ]`, `[ start_date, external_event_id, status, sync_id, type ]`, `[ start_date, operation, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status, sync_id ]`, `[ end_date, external_event_id, operation, status, type ]`, `[ end_date, external_event_id, operation, sync_id, type ]`, `[ end_date, external_event_id, status, sync_id, type ]`, `[ end_date, operation, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status, sync_id ]`, `[ start_date, end_date, external_event_id, operation, status, type ]`, `[ start_date, end_date, external_event_id, operation, sync_id, type ]`, `[ start_date, end_date, external_event_id, status, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id, type ]` or `[ end_date, external_event_id, operation, status, sync_id, type ]`.
  //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `operation` and `status`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ type ]`, `[ start_date, end_date ]`, `[ start_date, external_event_id ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, sync_id ]`, `[ start_date, type ]`, `[ end_date, external_event_id ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, sync_id ]`, `[ end_date, type ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, type ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, type ]`, `[ status, sync_id ]`, `[ status, type ]`, `[ sync_id, type ]`, `[ start_date, end_date, external_event_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, sync_id ]`, `[ start_date, end_date, type ]`, `[ start_date, external_event_id, operation ]`, `[ start_date, external_event_id, status ]`, `[ start_date, external_event_id, sync_id ]`, `[ start_date, external_event_id, type ]`, `[ start_date, operation, status ]`, `[ start_date, operation, sync_id ]`, `[ start_date, operation, type ]`, `[ start_date, status, sync_id ]`, `[ start_date, status, type ]`, `[ start_date, sync_id, type ]`, `[ end_date, external_event_id, operation ]`, `[ end_date, external_event_id, status ]`, `[ end_date, external_event_id, sync_id ]`, `[ end_date, external_event_id, type ]`, `[ end_date, operation, status ]`, `[ end_date, operation, sync_id ]`, `[ end_date, operation, type ]`, `[ end_date, status, sync_id ]`, `[ end_date, status, type ]`, `[ end_date, sync_id, type ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, type ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, type ]`, `[ external_event_id, sync_id, type ]`, `[ operation, status, sync_id ]`, `[ operation, status, type ]`, `[ operation, sync_id, type ]`, `[ status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation ]`, `[ start_date, end_date, external_event_id, status ]`, `[ start_date, end_date, external_event_id, sync_id ]`, `[ start_date, end_date, external_event_id, type ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, sync_id ]`, `[ start_date, end_date, operation, type ]`, `[ start_date, end_date, status, sync_id ]`, `[ start_date, end_date, status, type ]`, `[ start_date, end_date, sync_id, type ]`, `[ start_date, external_event_id, operation, status ]`, `[ start_date, external_event_id, operation, sync_id ]`, `[ start_date, external_event_id, operation, type ]`, `[ start_date, external_event_id, status, sync_id ]`, `[ start_date, external_event_id, status, type ]`, `[ start_date, external_event_id, sync_id, type ]`, `[ start_date, operation, status, sync_id ]`, `[ start_date, operation, status, type ]`, `[ start_date, operation, sync_id, type ]`, `[ start_date, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status ]`, `[ end_date, external_event_id, operation, sync_id ]`, `[ end_date, external_event_id, operation, type ]`, `[ end_date, external_event_id, status, sync_id ]`, `[ end_date, external_event_id, status, type ]`, `[ end_date, external_event_id, sync_id, type ]`, `[ end_date, operation, status, sync_id ]`, `[ end_date, operation, status, type ]`, `[ end_date, operation, sync_id, type ]`, `[ end_date, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, type ]`, `[ external_event_id, operation, sync_id, type ]`, `[ external_event_id, status, sync_id, type ]`, `[ operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status ]`, `[ start_date, end_date, external_event_id, operation, sync_id ]`, `[ start_date, end_date, external_event_id, operation, type ]`, `[ start_date, end_date, external_event_id, status, sync_id ]`, `[ start_date, end_date, external_event_id, status, type ]`, `[ start_date, end_date, external_event_id, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id ]`, `[ start_date, end_date, operation, status, type ]`, `[ start_date, end_date, operation, sync_id, type ]`, `[ start_date, end_date, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id ]`, `[ start_date, external_event_id, operation, status, type ]`, `[ start_date, external_event_id, operation, sync_id, type ]`, `[ start_date, external_event_id, status, sync_id, type ]`, `[ start_date, operation, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status, sync_id ]`, `[ end_date, external_event_id, operation, status, type ]`, `[ end_date, external_event_id, operation, sync_id, type ]`, `[ end_date, external_event_id, status, sync_id, type ]`, `[ end_date, operation, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status, sync_id ]`, `[ start_date, end_date, external_event_id, operation, status, type ]`, `[ start_date, end_date, external_event_id, operation, sync_id, type ]`, `[ start_date, end_date, external_event_id, status, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id, type ]` or `[ end_date, external_event_id, operation, status, sync_id, type ]`.
  public static function all($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/sync_logs', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new SyncLog((array)$obj, $options);
    }

    return $return_array;
  }



}
