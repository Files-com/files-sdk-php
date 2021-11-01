<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UsageDailySnapshot
 *
 * @package Files
 */
class UsageDailySnapshot {
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

  // int64 # ID of the usage record
  public function getId() {
    return @$this->attributes['id'];
  }

  // date # The date of this usage record
  public function getDate() {
    return @$this->attributes['date'];
  }

  // int64 # The quantity of storage held for this site
  public function getCurrentStorage() {
    return @$this->attributes['current_storage'];
  }

  // array # Usage broken down by each top-level folder
  public function getUsageByTopLevelDir() {
    return @$this->attributes['usage_by_top_level_dir'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `date` and `usage_snapshot_id`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  //   filter_like - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/usage_daily_snapshots', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new UsageDailySnapshot((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
