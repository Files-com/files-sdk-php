<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class InboxUpload
 *
 * @package Files
 */
class InboxUpload {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  public function getInboxRegistration() {
    return @$this->attributes['inbox_registration'];
  }

  // string # Upload path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  // date-time # Upload date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   inbox_registration_id - int64 - InboxRegistration ID
  //   inbox_id - int64 - Inbox ID
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['inbox_registration_id'] && !is_int(@$params['inbox_registration_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $inbox_registration_id must be of type int; received ' . gettype($inbox_registration_id));
    }

    if (@$params['inbox_id'] && !is_int(@$params['inbox_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $inbox_id must be of type int; received ' . gettype($inbox_id));
    }

    $response = Api::sendRequest('/inbox_uploads', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new InboxUpload((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
