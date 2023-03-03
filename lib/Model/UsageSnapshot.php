<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

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

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Usage snapshot ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // date-time # Usage snapshot start date/time
  public function getStartAt() {
    return @$this->attributes['start_at'];
  }

  // date-time # Usage snapshot end date/time
  public function getEndAt() {
    return @$this->attributes['end_at'];
  }

  // double # Highest user count number in time period
  public function getHighWaterUserCount() {
    return @$this->attributes['high_water_user_count'];
  }

  // double # Current total Storage Usage GB as of end date (not necessarily high water mark, which is used for billing)
  public function getCurrentStorage() {
    return @$this->attributes['current_storage'];
  }

  // double # Highest Storage Usage GB recorded in time period (used for billing)
  public function getHighWaterStorage() {
    return @$this->attributes['high_water_storage'];
  }

  // object # Storage Usage - map of root folders to their usage as of end date (not necessarily high water mark, which is used for billing)
  public function getUsageByTopLevelDir() {
    return @$this->attributes['usage_by_top_level_dir'];
  }

  // double # Storage Usage for root folder as of end date (not necessarily high water mark, which is used for billing)
  public function getRootStorage() {
    return @$this->attributes['root_storage'];
  }

  // double # Storage Usage for files that are deleted but uploaded within last 30 days as of end date (not necessarily high water mark, which is used for billing)
  public function getDeletedFilesCountedInMinimum() {
    return @$this->attributes['deleted_files_counted_in_minimum'];
  }

  // double # Storage Usage for files that are deleted but retained as backups as of end date (not necessarily high water mark, which is used for billing)
  public function getDeletedFilesStorage() {
    return @$this->attributes['deleted_files_storage'];
  }

  // double # Storage + Transfer Usage - Total Billable amount
  public function getTotalBillableUsage() {
    return @$this->attributes['total_billable_usage'];
  }

  // double # Transfer usage for period - Total Billable amount
  public function getTotalBillableTransferUsage() {
    return @$this->attributes['total_billable_transfer_usage'];
  }

  // double # Transfer Usage for period - Outbound GB from Files Native Storage
  public function getBytesSent() {
    return @$this->attributes['bytes_sent'];
  }

  // double # Transfer Usage for period - Inbound GB to Remote Servers (Sync/Mount)
  public function getSyncBytesReceived() {
    return @$this->attributes['sync_bytes_received'];
  }

  // double # Transfer Usage for period - Outbound GB from Remote Servers (Sync/Mount)
  public function getSyncBytesSent() {
    return @$this->attributes['sync_bytes_sent'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/usage_snapshots', 'GET', $params, $options);

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
