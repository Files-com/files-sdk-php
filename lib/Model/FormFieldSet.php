<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FormFieldSet
 *
 * @package Files
 */
class FormFieldSet {
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

  // int64 # Form field set id
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Title to be displayed
  public function getTitle() {
    return @$this->attributes['title'];
  }

  public function setTitle($value) {
    return $this->attributes['title'] = $value;
  }

  // int64 # Layout of the form
  public function getFormLayout() {
    return @$this->attributes['form_layout'];
  }

  public function setFormLayout($value) {
    return $this->attributes['form_layout'] = $value;
  }

  // Associated form fields
  public function getFormFields() {
    return @$this->attributes['form_fields'];
  }

  public function setFormFields($value) {
    return $this->attributes['form_fields'] = $value;
  }

  // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing name
  public function getSkipName() {
    return @$this->attributes['skip_name'];
  }

  public function setSkipName($value) {
    return $this->attributes['skip_name'] = $value;
  }

  // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing email
  public function getSkipEmail() {
    return @$this->attributes['skip_email'];
  }

  public function setSkipEmail($value) {
    return $this->attributes['skip_email'] = $value;
  }

  // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing company
  public function getSkipCompany() {
    return @$this->attributes['skip_company'];
  }

  public function setSkipCompany($value) {
    return $this->attributes['skip_company'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // Parameters:
  //   title - string - Title to be displayed
  //   skip_email - boolean - Skip validating form email
  //   skip_name - boolean - Skip validating form name
  //   skip_company - boolean - Skip validating company
  //   form_fields - array(object)
  public function update($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['title'] && !is_string(@$params['title'])) {
      throw new \Files\InvalidParameterException('$title must be of type string; received ' . gettype($title));
    }

    if (@$params['form_fields'] && !is_array(@$params['form_fields'])) {
      throw new \Files\InvalidParameterException('$form_fields must be of type array; received ' . gettype($form_fields));
    }

    $response = Api::sendRequest('/form_field_sets/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/form_field_sets/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/form_field_sets', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new FormFieldSet((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Form Field Set ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/form_field_sets/' . @$params['id'] . '', 'GET', $params, $options);

    return new FormFieldSet((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   title - string - Title to be displayed
  //   skip_email - boolean - Skip validating form email
  //   skip_name - boolean - Skip validating form name
  //   skip_company - boolean - Skip validating company
  //   form_fields - array(object)
  public static function create($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['title'] && !is_string(@$params['title'])) {
      throw new \Files\InvalidParameterException('$title must be of type string; received ' . gettype($title));
    }

    if (@$params['form_fields'] && !is_array(@$params['form_fields'])) {
      throw new \Files\InvalidParameterException('$form_fields must be of type array; received ' . gettype($form_fields));
    }

    $response = Api::sendRequest('/form_field_sets', 'POST', $params, $options);

    return new FormFieldSet((array)(@$response->data ?: []), $options);
  }
}
