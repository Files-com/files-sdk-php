<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteServerConfigurationFile
 *
 * @package Files
 */
class RemoteServerConfigurationFile {
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

  // int64 # Agent ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // string #
  public function getPermissionSet() {
    return @$this->attributes['permission_set'];
  }

  // string # Files Agent API Token
  public function getApiToken() {
    return @$this->attributes['api_token'];
  }

  // string # Agent local root path
  public function getRoot() {
    return @$this->attributes['root'];
  }

  // int64 # Incoming port for files agent connections
  public function getPort() {
    return @$this->attributes['port'];
  }

  // string
  public function getHostname() {
    return @$this->attributes['hostname'];
  }

  // string # public key
  public function getPublicKey() {
    return @$this->attributes['public_key'];
  }

  // string # private key
  public function getPrivateKey() {
    return @$this->attributes['private_key'];
  }

  // string # either running or shutdown
  public function getStatus() {
    return @$this->attributes['status'];
  }

  // string # agent config version
  public function getConfigVersion() {
    return @$this->attributes['config_version'];
  }

  // string
  public function getServerHostKey() {
    return @$this->attributes['server_host_key'];
  }
}
