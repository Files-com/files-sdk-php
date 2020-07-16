<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PublicIpAddress
 *
 * @package Files
 */
class PublicIpAddress {
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

  // string # The public IP address.
  public function getIpAddress() {
    return $this->attributes['ip_address'];
  }

  // string # The name of the frontend server.
  public function getServerName() {
    return $this->attributes['server_name'];
  }
}
