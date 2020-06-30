<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class BundleDownload
 *
 * @package Files
 */
class BundleDownload {
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

  // string # Download method (file or full_zip)
  public function getDownloadMethod() {
    return $this->attributes['download_method'];
  }

  // string # Download path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  // date-time # Download date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // Parameters:
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   bundle_registration_id (required) - int64 - BundleRegistration ID
  public static function list($params = [], $options = []) {
    if (!$params['bundle_registration_id']) {
      throw new \Error('Parameter missing: bundle_registration_id');
    }

    if ($params['page'] && !is_int($params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if ($params['per_page'] && !is_int($params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if ($params['action'] && !is_string($params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if ($params['bundle_registration_id'] && !is_int($params['bundle_registration_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $bundle_registration_id must be of type int; received ' . gettype($bundle_registration_id));
    }

    $response = Api::sendRequest('/bundle_downloads', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new BundleDownload((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
