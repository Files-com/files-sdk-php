<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SyncJob
 *
 * @package Files
 */
class SyncJob {
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

  // date-time # Job enqueued at
  public function getQueuedAt() {
    return @$this->attributes['queued_at'];
  }

  // date-time # Job updated at
  public function getUpdatedAt() {
    return @$this->attributes['updated_at'];
  }

  // string # Status of the job
  public function getStatus() {
    return @$this->attributes['status'];
  }

  // string # Most recent reported status of sync worker
  public function getRegionalWorkerStatus() {
    return @$this->attributes['regional_worker_status'];
  }

  // string #
  public function getUuid() {
    return @$this->attributes['uuid'];
  }

  // int64 #
  public function getFolderBehaviorId() {
    return @$this->attributes['folder_behavior_id'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/sync_jobs', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new SyncJob((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
