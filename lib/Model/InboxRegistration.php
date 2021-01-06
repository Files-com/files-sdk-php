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

  // int64 # Id of associated form field set
  public function getFormFieldSetId() {
    return @$this->attributes['form_field_set_id'];
  }

  // string # Data for form field set with form field ids as keys and user data as values
  public function getFormFieldData() {
    return @$this->attributes['form_field_data'];
  }
}
