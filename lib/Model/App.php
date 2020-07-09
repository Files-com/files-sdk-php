<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class App
 *
 * @package Files
 */
class App {
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

  // string # Name of the App
  public function getName() {
    return $this->attributes['name'];
  }

  // string # Long form description of the App
  public function getExtendedDescription() {
    return $this->attributes['extended_description'];
  }

  // string # Collection of named links to documentation
  public function getDocumentationLinks() {
    return $this->attributes['documentation_links'];
  }

  // string # Associated SSO Strategy type, if any
  public function getSsoStrategyType() {
    return $this->attributes['sso_strategy_type'];
  }

  // string # Associated Remote Server type, if any
  public function getRemoteServerType() {
    return $this->attributes['remote_server_type'];
  }

  // string # Associated Folder Behavior type, if any
  public function getFolderBehaviorType() {
    return $this->attributes['folder_behavior_type'];
  }

  // string # Link to external homepage
  public function getExternalHomepageUrl() {
    return $this->attributes['external_homepage_url'];
  }

  // string # The type of the App
  public function getAppType() {
    return $this->attributes['app_type'];
  }

  // boolean # Is featured on the App listing?
  public function getFeatured() {
    return $this->attributes['featured'];
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

    $response = Api::sendRequest('/apps', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new App((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
