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

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // int64 # Site bandwidth ID
  public function getId() {
    return $this->attributes['id'];
  }

  // double # Site bandwidth report bytes received
  public function getBytesReceived() {
    return $this->attributes['bytes_received'];
  }

  // double # Site bandwidth report bytes sent
  public function getBytesSent() {
    return $this->attributes['bytes_sent'];
  }

  // double # Site bandwidth report get requests
  public function getRequestsGet() {
    return $this->attributes['requests_get'];
  }

  // double # Site bandwidth report put requests
  public function getRequestsPut() {
    return $this->attributes['requests_put'];
  }

  // double # Site bandwidth report other requests
  public function getRequestsOther() {
    return $this->attributes['requests_other'];
  }

  // date-time # Time the site bandwidth report was logged
  public function getLoggedAt() {
    return $this->attributes['logged_at'];
  }

  // date-time # Site bandwidth report created at date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // date-time # The last time this site bandwidth report was updated
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }

  // Parameters:
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
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
