<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class WebhookTest
 *
 * @package Files
 */
class WebhookTest {
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

  // int64 # Status HTTP code
  public function getCode() {
    return @$this->attributes['code'];
  }

  public function setCode($value) {
    return $this->attributes['code'] = $value;
  }

  // string # Error message
  public function getMessage() {
    return @$this->attributes['message'];
  }

  public function setMessage($value) {
    return $this->attributes['message'] = $value;
  }

  // string # Status message
  public function getStatus() {
    return @$this->attributes['status'];
  }

  public function setStatus($value) {
    return $this->attributes['status'] = $value;
  }

  // Additional data
  public function getData() {
    return @$this->attributes['data'];
  }

  public function setData($value) {
    return $this->attributes['data'] = $value;
  }

  // boolean # The success status of the webhook test
  public function getSuccess() {
    return @$this->attributes['success'];
  }

  public function setSuccess($value) {
    return $this->attributes['success'] = $value;
  }

  // string # URL for testing the webhook.
  public function getUrl() {
    return @$this->attributes['url'];
  }

  public function setUrl($value) {
    return $this->attributes['url'] = $value;
  }

  // string # HTTP method(GET or POST).
  public function getMethod() {
    return @$this->attributes['method'];
  }

  public function setMethod($value) {
    return $this->attributes['method'] = $value;
  }

  // string # HTTP encoding method.  Can be JSON, XML, or RAW (form data).
  public function getEncoding() {
    return @$this->attributes['encoding'];
  }

  public function setEncoding($value) {
    return $this->attributes['encoding'] = $value;
  }

  // object # Additional request headers.
  public function getHeaders() {
    return @$this->attributes['headers'];
  }

  public function setHeaders($value) {
    return $this->attributes['headers'] = $value;
  }

  // object # Additional body parameters.
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // string # raw body text
  public function getRawBody() {
    return @$this->attributes['raw_body'];
  }

  public function setRawBody($value) {
    return $this->attributes['raw_body'] = $value;
  }

  // boolean # Send the file data as the request body?
  public function getFileAsBody() {
    return @$this->attributes['file_as_body'];
  }

  public function setFileAsBody($value) {
    return $this->attributes['file_as_body'] = $value;
  }

  // string # Send the file data as a named parameter in the request POST body
  public function getFileFormField() {
    return @$this->attributes['file_form_field'];
  }

  public function setFileFormField($value) {
    return $this->attributes['file_form_field'] = $value;
  }

  // string # action for test body
  public function getAction() {
    return @$this->attributes['action'];
  }

  public function setAction($value) {
    return $this->attributes['action'] = $value;
  }

  public function save() {
      if (@$this->attributes['id']) {
        throw new \BadMethodCallException('The WebhookTest object doesn\'t support updates.');
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   url (required) - string - URL for testing the webhook.
  //   method - string - HTTP method(GET or POST).
  //   encoding - string - HTTP encoding method.  Can be JSON, XML, or RAW (form data).
  //   headers - object - Additional request headers.
  //   body - object - Additional body parameters.
  //   raw_body - string - raw body text
  //   file_as_body - boolean - Send the file data as the request body?
  //   file_form_field - string - Send the file data as a named parameter in the request POST body
  //   action - string - action for test body
  public static function create($params = [], $options = []) {
    if (!@$params['url']) {
      throw new \Error('Parameter missing: url');
    }

    if (@$params['url'] && !is_string(@$params['url'])) {
      throw new \InvalidArgumentException('Bad parameter: $url must be of type string; received ' . gettype($url));
    }

    if (@$params['method'] && !is_string(@$params['method'])) {
      throw new \InvalidArgumentException('Bad parameter: $method must be of type string; received ' . gettype($method));
    }

    if (@$params['encoding'] && !is_string(@$params['encoding'])) {
      throw new \InvalidArgumentException('Bad parameter: $encoding must be of type string; received ' . gettype($encoding));
    }

    if (@$params['raw_body'] && !is_string(@$params['raw_body'])) {
      throw new \InvalidArgumentException('Bad parameter: $raw_body must be of type string; received ' . gettype($raw_body));
    }

    if (@$params['file_form_field'] && !is_string(@$params['file_form_field'])) {
      throw new \InvalidArgumentException('Bad parameter: $file_form_field must be of type string; received ' . gettype($file_form_field));
    }

    if (@$params['action'] && !is_string(@$params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    $response = Api::sendRequest('/webhook_tests', 'POST', $params, $options);

    return new WebhookTest((array)(@$response->data ?: []), $options);
  }
}
