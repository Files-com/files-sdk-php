<?php

declare(strict_types=1);

namespace Files;

/**
 * Class DnsRecord
 *
 * @package Files
 */
class DnsRecord {
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

  // string # Unique label for DNS record; used by Zapier and other integrations.
  public function getId() {
    return $this->attributes['id'];
  }

  // string # DNS record domain name
  public function getDomain() {
    return $this->attributes['domain'];
  }

  // string # DNS record type
  public function getRrtype() {
    return $this->attributes['rrtype'];
  }

  // string # DNS record value
  public function getValue() {
    return $this->attributes['value'];
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

    $response = Api::sendRequest('/dns_records', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new DnsRecord((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
