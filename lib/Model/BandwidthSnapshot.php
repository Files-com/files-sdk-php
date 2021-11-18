<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class BandwidthSnapshot
 *
 * @package Files
 */
class BandwidthSnapshot {
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

  // int64 # Site bandwidth ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // double # Site bandwidth report bytes received
  public function getBytesReceived() {
    return @$this->attributes['bytes_received'];
  }

  // double # Site bandwidth report bytes sent
  public function getBytesSent() {
    return @$this->attributes['bytes_sent'];
  }

  // double # Site sync bandwidth report bytes received
  public function getSyncBytesReceived() {
    return @$this->attributes['sync_bytes_received'];
  }

  // double # Site sync bandwidth report bytes sent
  public function getSyncBytesSent() {
    return @$this->attributes['sync_bytes_sent'];
  }

  // double # Site bandwidth report get requests
  public function getRequestsGet() {
    return @$this->attributes['requests_get'];
  }

  // double # Site bandwidth report put requests
  public function getRequestsPut() {
    return @$this->attributes['requests_put'];
  }

  // double # Site bandwidth report other requests
  public function getRequestsOther() {
    return @$this->attributes['requests_other'];
  }

  // date-time # Time the site bandwidth report was logged
  public function getLoggedAt() {
    return @$this->attributes['logged_at'];
  }

  // date-time # Site bandwidth report created at date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // date-time # The last time this site bandwidth report was updated
  public function getUpdatedAt() {
    return @$this->attributes['updated_at'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `logged_at`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `logged_at`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `logged_at`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal to the supplied value. Valid fields are `logged_at`.
  //   filter_like - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `logged_at`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `logged_at`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal to the supplied value. Valid fields are `logged_at`.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/bandwidth_snapshots', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new BandwidthSnapshot((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
