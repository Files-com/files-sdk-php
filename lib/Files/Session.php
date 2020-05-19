<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Session
 *
 * @package Files
 */
class Session {
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

  // int64 # Session ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Session language
  public function getLanguage() {
    return $this->attributes['language'];
  }

  public function setLanguage($value) {
    return $this->attributes['language'] = $value;
  }

  // string # Login token. If set, this token will allow your user to log in via browser at the domain in `login_token_domain`.
  public function getLoginToken() {
    return $this->attributes['login_token'];
  }

  public function setLoginToken($value) {
    return $this->attributes['login_token'] = $value;
  }

  // string # Domain to use with `login_token`.
  public function getLoginTokenDomain() {
    return $this->attributes['login_token_domain'];
  }

  public function setLoginTokenDomain($value) {
    return $this->attributes['login_token_domain'] = $value;
  }

  // int64 # Maximum number of files to retrieve per folder for a directory listing.  This is based on the user's plan.
  public function getMaxDirListingSize() {
    return $this->attributes['max_dir_listing_size'];
  }

  public function setMaxDirListingSize($value) {
    return $this->attributes['max_dir_listing_size'] = $value;
  }

  // boolean # Can access multiple regions?
  public function getMultipleRegions() {
    return $this->attributes['multiple_regions'];
  }

  public function setMultipleRegions($value) {
    return $this->attributes['multiple_regions'] = $value;
  }

  // boolean # Is this session read only?
  public function getReadOnly() {
    return $this->attributes['read_only'];
  }

  public function setReadOnly($value) {
    return $this->attributes['read_only'] = $value;
  }

  // string # Initial root path to start the user's session in.
  public function getRootPath() {
    return $this->attributes['root_path'];
  }

  public function setRootPath($value) {
    return $this->attributes['root_path'] = $value;
  }

  // int64 # Site ID
  public function getSiteId() {
    return $this->attributes['site_id'];
  }

  public function setSiteId($value) {
    return $this->attributes['site_id'] = $value;
  }

  // boolean # Is SSL required for this user?  (If so, ensure all your communications with this user use SSL.)
  public function getSslRequired() {
    return $this->attributes['ssl_required'];
  }

  public function setSslRequired($value) {
    return $this->attributes['ssl_required'] = $value;
  }

  // boolean # Is strong TLS disabled for this user? (If this is set to true, the site administrator has signaled that it is ok to use less secure TLS versions for this user.)
  public function getTlsDisabled() {
    return $this->attributes['tls_disabled'];
  }

  public function setTlsDisabled($value) {
    return $this->attributes['tls_disabled'] = $value;
  }

  // boolean # If true, this user needs to add a Two Factor Authentication method before performing any further actions.
  public function getTwoFactorSetupNeeded() {
    return $this->attributes['two_factor_setup_needed'];
  }

  public function setTwoFactorSetupNeeded($value) {
    return $this->attributes['two_factor_setup_needed'] = $value;
  }

  // boolean # Sent only if 2FA setup is needed. Is SMS two factor authentication allowed?
  public function getAllowed2faMethodSms() {
    return $this->attributes['allowed_2fa_method_sms'];
  }

  public function setAllowed2faMethodSms($value) {
    return $this->attributes['allowed_2fa_method_sms'] = $value;
  }

  // boolean # Sent only if 2FA setup is needed. Is TOTP two factor authentication allowed?
  public function getAllowed2faMethodTotp() {
    return $this->attributes['allowed_2fa_method_totp'];
  }

  public function setAllowed2faMethodTotp($value) {
    return $this->attributes['allowed_2fa_method_totp'] = $value;
  }

  // boolean # Sent only if 2FA setup is needed. Is U2F two factor authentication allowed?
  public function getAllowed2faMethodU2f() {
    return $this->attributes['allowed_2fa_method_u2f'];
  }

  public function setAllowed2faMethodU2f($value) {
    return $this->attributes['allowed_2fa_method_u2f'] = $value;
  }

  // boolean # Sent only if 2FA setup is needed. Is Yubikey two factor authentication allowed?
  public function getAllowed2faMethodYubi() {
    return $this->attributes['allowed_2fa_method_yubi'];
  }

  public function setAllowed2faMethodYubi($value) {
    return $this->attributes['allowed_2fa_method_yubi'] = $value;
  }

  // boolean # Allow the user to provide file/folder modified at dates?  If false, the server will always use the current date/time.
  public function getUseProvidedModifiedAt() {
    return $this->attributes['use_provided_modified_at'];
  }

  public function setUseProvidedModifiedAt($value) {
    return $this->attributes['use_provided_modified_at'] = $value;
  }

  // boolean # Does this user want to use Windows line-ending emulation?  (CR vs CRLF)
  public function getWindowsModeFtp() {
    return $this->attributes['windows_mode_ftp'];
  }

  public function setWindowsModeFtp($value) {
    return $this->attributes['windows_mode_ftp'] = $value;
  }

  // string # Username to sign in as
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // string # Password for sign in
  public function getPassword() {
    return $this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # If this user has a 2FA device, provide its OTP or code here.
  public function getOtp() {
    return $this->attributes['otp'];
  }

  public function setOtp($value) {
    return $this->attributes['otp'] = $value;
  }

  // string # Identifier for a partially-completed login
  public function getPartialSessionId() {
    return $this->attributes['partial_session_id'];
  }

  public function setPartialSessionId($value) {
    return $this->attributes['partial_session_id'] = $value;
  }

  public function save() {
    if ($this->attributes['id']) {
      throw new \BadMethodCallException('The Session object doesn\'t support updates.');
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
    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }

    if ($params['otp'] && !is_string($params['otp'])) {
      throw new \InvalidArgumentException('Bad parameter: $otp must be of type string; received ' . gettype($otp));
    }

    if ($params['partial_session_id'] && !is_string($params['partial_session_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $partial_session_id must be of type string; received ' . gettype($partial_session_id));
    }

    $response = Api::sendRequest('/sessions', 'POST', $params);

    return new Session((array)$response->data, $options);
  }

  public static function delete($params = [], $options = []) {
    $response = Api::sendRequest('/sessions', 'DELETE');

    return $response->data;
  }

  public static function destroy($params = [], $options = []) {
    return self::delete($params, $options);
  }
}
