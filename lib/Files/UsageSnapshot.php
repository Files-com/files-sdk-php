<?php

declare(strict_types=1);

namespace Files;

/**
 * Class UsageSnapshot
 *
 * @package Files
 */
class UsageSnapshot {
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

  // int64 # Site usage ID
  public function getId() {
    return $this->attributes['id'];
  }

  // date-time # Site usage report start date/time
  public function getStartAt() {
    return $this->attributes['start_at'];
  }

  // date-time # Site usage report end date/time
  public function getEndAt() {
    return $this->attributes['end_at'];
  }

  // date-time # Site usage report created at date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // double # Current site usage as of report
  public function getCurrentStorage() {
    return $this->attributes['current_storage'];
  }

  // double # Site usage report highest usage in time period
  public function getHighWaterStorage() {
    return $this->attributes['high_water_storage'];
  }

  // int64 # Number of downloads in report time period
  public function getTotalDownloads() {
    return $this->attributes['total_downloads'];
  }

  // int64 # Number of uploads in time period
  public function getTotalUploads() {
    return $this->attributes['total_uploads'];
  }

  // date-time # The last time this site usage report was updated
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }

  // object # A map of root folders to their total usage
  public function getUsageByTopLevelDir() {
    return $this->attributes['usage_by_top_level_dir'];
  }

  // double # Usage for root folder
  public function getRootStorage() {
    return $this->attributes['root_storage'];
  }

  // double # Usage for files that are deleted but uploaded within last 30 days
  public function getDeletedFilesCountedInMinimum() {
    return $this->attributes['deleted_files_counted_in_minimum'];
  }

  // double # Usage for files that are deleted but retained as backups
  public function getDeletedFilesStorage() {
    return $this->attributes['deleted_files_storage'];
  }

  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  public static function list($params = [], $options = []) {
    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    $response = Api::sendRequest('/usage_snapshots', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new UsageSnapshot((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
