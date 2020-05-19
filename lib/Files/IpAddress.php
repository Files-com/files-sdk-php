<?php

declare(strict_types=1);

namespace Files;

/**
 * Class IpAddress
 *
 * @package Files
 */
class IpAddress {
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

  // string # Unique label for list; used by Zapier and other integrations.
  public function getId() {
    return $this->attributes['id'];
  }

  // string # The object that this public IP address list is associated with.
  public function getAssociatedWith() {
    return $this->attributes['associated_with'];
  }

  // int64 # Group ID
  public function getGroupId() {
    return $this->attributes['group_id'];
  }

  // array # A list of IP addresses.
  public function getIpAddresses() {
    return $this->attributes['ip_addresses'];
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

    $response = Api::sendRequest('/ip_addresses', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new IpAddress((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
