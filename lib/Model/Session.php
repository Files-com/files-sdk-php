<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Session
 *
 * @package Files
 */
class Session {
  private $attributes = [];
  private $options = [];
  private static $static_mapped_functions = [
    'list' => 'all',
  ];

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

  public static function __callStatic($name, $arguments) {
    if(in_array($name, array_keys(self::$static_mapped_functions))){
      $method = self::$static_mapped_functions[$name];
      if (method_exists(__CLASS__, $method)){ 
        return @self::$method($arguments);
      }
    }
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // string # Session ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Session language
  public function getLanguage() {
    return @$this->attributes['language'];
  }

  public function setLanguage($value) {
    return $this->attributes['language'] = $value;
  }

  // boolean # Is this session read only?
  public function getReadOnly() {
    return @$this->attributes['read_only'];
  }

  public function setReadOnly($value) {
    return $this->attributes['read_only'] = $value;
  }

  // boolean # Are insecure SFTP ciphers allowed for this user? (If this is set to true, the site administrator has signaled that it is ok to use less secure SSH ciphers for this user.)
  public function getSftpInsecureCiphers() {
    return @$this->attributes['sftp_insecure_ciphers'];
  }

  public function setSftpInsecureCiphers($value) {
    return $this->attributes['sftp_insecure_ciphers'] = $value;
  }

  // string # Username to sign in as
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // string # Password for sign in
  public function getPassword() {
    return @$this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # If this user has a 2FA device, provide its OTP or code here.
  public function getOtp() {
    return @$this->attributes['otp'];
  }

  public function setOtp($value) {
    return $this->attributes['otp'] = $value;
  }

  // string # Identifier for a partially-completed login
  public function getPartialSessionId() {
    return @$this->attributes['partial_session_id'];
  }

  public function setPartialSessionId($value) {
    return $this->attributes['partial_session_id'] = $value;
  }

  public function save() {
      if (@$this->attributes['id']) {
        throw new \Files\NotImplementedException('The Session object doesn\'t support updates.');
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }


  // Parameters:
  //   username - string - Username to sign in as
  //   password - string - Password for sign in
  //   otp - string - If this user has a 2FA device, provide its OTP or code here.
  //   partial_session_id - string - Identifier for a partially-completed login
  public static function create($params = [], $options = []) {
    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
    }

    if (@$params['otp'] && !is_string(@$params['otp'])) {
      throw new \Files\InvalidParameterException('$otp must be of type string; received ' . gettype(@$params['otp']));
    }

    if (@$params['partial_session_id'] && !is_string(@$params['partial_session_id'])) {
      throw new \Files\InvalidParameterException('$partial_session_id must be of type string; received ' . gettype(@$params['partial_session_id']));
    }

    $response = Api::sendRequest('/sessions', 'POST', $params, $options);

    return new Session((array)(@$response->data ?: []), $options);
  }


  public static function delete($params = [], $options = []) {
    $response = Api::sendRequest('/sessions', 'DELETE', $options);

    return $response->data;
  }


  public static function destroy($params = [], $options = []) {
    return self::delete($params, $options);
  }
  
}
