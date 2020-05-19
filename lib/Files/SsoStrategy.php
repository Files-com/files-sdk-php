<?php

declare(strict_types=1);

namespace Files;

/**
 * Class SsoStrategy
 *
 * @package Files
 */
class SsoStrategy {
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

  // string # SSO Protocol
  public function getProtocol() {
    return $this->attributes['protocol'];
  }

  // string # Provider name
  public function getProvider() {
    return $this->attributes['provider'];
  }

  // int64 # ID
  public function getId() {
    return $this->attributes['id'];
  }

  // string # Identity provider sha256 cert fingerprint if saml_provider_metadata_url is not available.
  public function getSamlProviderCertFingerprint() {
    return $this->attributes['saml_provider_cert_fingerprint'];
  }

  // string # Identity provider issuer url
  public function getSamlProviderIssuerUrl() {
    return $this->attributes['saml_provider_issuer_url'];
  }

  // string # Metadata URL for the SAML identity provider
  public function getSamlProviderMetadataUrl() {
    return $this->attributes['saml_provider_metadata_url'];
  }

  // string # Identity provider SLO endpoint
  public function getSamlProviderSloTargetUrl() {
    return $this->attributes['saml_provider_slo_target_url'];
  }

  // string # Identity provider SSO endpoint if saml_provider_metadata_url is not available.
  public function getSamlProviderSsoTargetUrl() {
    return $this->attributes['saml_provider_sso_target_url'];
  }

  // string # SCIM authentication type.
  public function getScimAuthenticationMethod() {
    return $this->attributes['scim_authentication_method'];
  }

  // string # SCIM username.
  public function getScimUsername() {
    return $this->attributes['scim_username'];
  }

  // string # Subdomain
  public function getSubdomain() {
    return $this->attributes['subdomain'];
  }

  // boolean # Auto-provision users?
  public function getProvisionUsers() {
    return $this->attributes['provision_users'];
  }

  // boolean # Auto-provision group membership based on group memberships on the SSO side?
  public function getProvisionGroups() {
    return $this->attributes['provision_groups'];
  }

  // string # Comma-separated list of group names for groups to automatically add all auto-provisioned users to.
  public function getProvisionGroupDefault() {
    return $this->attributes['provision_group_default'];
  }

  // string # Comma-separated list of group names for groups (with optional wildcards) that will be excluded from auto-provisioning.
  public function getProvisionGroupExclusion() {
    return $this->attributes['provision_group_exclusion'];
  }

  // string # Comma-separated list of group names for groups (with optional wildcards) that will be auto-provisioned.
  public function getProvisionGroupInclusion() {
    return $this->attributes['provision_group_inclusion'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to require membership for user provisioning.
  public function getProvisionGroupRequired() {
    return $this->attributes['provision_group_required'];
  }

  // boolean # Auto-provisioned users get Sharing permission?
  public function getProvisionAttachmentsPermission() {
    return $this->attributes['provision_attachments_permission'];
  }

  // boolean # Auto-provisioned users get WebDAV permission?
  public function getProvisionDavPermission() {
    return $this->attributes['provision_dav_permission'];
  }

  // boolean # Auto-provisioned users get FTP permission?
  public function getProvisionFtpPermission() {
    return $this->attributes['provision_ftp_permission'];
  }

  // boolean # Auto-provisioned users get SFTP permission?
  public function getProvisionSftpPermission() {
    return $this->attributes['provision_sftp_permission'];
  }

  // string # Default time zone for auto provisioned users.
  public function getProvisionTimeZone() {
    return $this->attributes['provision_time_zone'];
  }

  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
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

    $response = Api::sendRequest('/sso_strategies', 'GET', $params);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new SsoStrategy((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - integer - Sso Strategy ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/sso_strategies/' . $params['id'] . '', 'GET', $params);

    return new SsoStrategy((array)$response->data, $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }
}
