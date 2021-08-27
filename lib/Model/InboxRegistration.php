<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class InboxRegistration
 *
 * @package Files
 */
class InboxRegistration {
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

  // string # Registration cookie code
  public function getCode() {
    return @$this->attributes['code'];
  }

  // string # Registrant name
  public function getName() {
    return @$this->attributes['name'];
  }

  // string # Registrant company name
  public function getCompany() {
    return @$this->attributes['company'];
  }

  // string # Registrant email address
  public function getEmail() {
    return @$this->attributes['email'];
  }

  // string # Clickwrap text that was shown to the registrant
  public function getClickwrapBody() {
    return @$this->attributes['clickwrap_body'];
  }

  // int64 # Id of associated form field set
  public function getFormFieldSetId() {
    return @$this->attributes['form_field_set_id'];
  }

  // string # Data for form field set with form field ids as keys and user data as values
  public function getFormFieldData() {
    return @$this->attributes['form_field_data'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   folder_behavior_id - int64 - ID of the associated Inbox.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['folder_behavior_id'] && !is_int(@$params['folder_behavior_id'])) {
      throw new \Files\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype($folder_behavior_id));
    }

    $response = Api::sendRequest('/inbox_registrations', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new InboxRegistration((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
