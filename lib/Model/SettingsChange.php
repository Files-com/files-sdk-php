<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SettingsChange
 *
 * @package Files
 */
class SettingsChange {
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

  // object # Specifics on what changed.
  public function getChangeDetails() {
    return $this->attributes['change_details'];
  }

  // date-time # The time this change was made
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // int64 # The user id responsible for this change
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  // Parameters:
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   cursor - string - Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `site_id`, `api_key_id`, `created_at` or `user_id`.
  //   filter - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `api_key_id` and `user_id`.
  //   filter_gt - object - If set, return records where the specifiied field is greater than the supplied value. Valid fields are `api_key_id` and `user_id`.
  //   filter_gteq - object - If set, return records where the specifiied field is greater than or equal to the supplied value. Valid fields are `api_key_id` and `user_id`.
  //   filter_like - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `api_key_id` and `user_id`.
  //   filter_lt - object - If set, return records where the specifiied field is less than the supplied value. Valid fields are `api_key_id` and `user_id`.
  //   filter_lteq - object - If set, return records where the specifiied field is less than or equal to the supplied value. Valid fields are `api_key_id` and `user_id`.
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

    if ($params['cursor'] && !is_string($params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    $response = Api::sendRequest('/settings_changes', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new SettingsChange((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
