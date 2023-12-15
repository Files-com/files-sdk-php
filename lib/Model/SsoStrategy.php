<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SsoStrategy
 *
 * @package Files
 */
class SsoStrategy {
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

  // string # SSO Protocol
  public function getProtocol() {
    return @$this->attributes['protocol'];
  }

  // string # Provider name
  public function getProvider() {
    return @$this->attributes['provider'];
  }

  // string # Custom label for the SSO provider on the login page.
  public function getLabel() {
    return @$this->attributes['label'];
  }

  // string # URL holding a custom logo for the SSO provider on the login page.
  public function getLogoUrl() {
    return @$this->attributes['logo_url'];
  }

  // int64 # ID
  public function getId() {
    return @$this->attributes['id'];
  }

  // int64 # Count of users with this SSO Strategy
  public function getUserCount() {
    return @$this->attributes['user_count'];
  }

  // string # Identity provider sha256 cert fingerprint if saml_provider_metadata_url is not available.
  public function getSamlProviderCertFingerprint() {
    return @$this->attributes['saml_provider_cert_fingerprint'];
  }

  // string # Identity provider issuer url
  public function getSamlProviderIssuerUrl() {
    return @$this->attributes['saml_provider_issuer_url'];
  }

  // string # Custom identity provider metadata
  public function getSamlProviderMetadataContent() {
    return @$this->attributes['saml_provider_metadata_content'];
  }

  // string # Metadata URL for the SAML identity provider
  public function getSamlProviderMetadataUrl() {
    return @$this->attributes['saml_provider_metadata_url'];
  }

  // string # Identity provider SLO endpoint
  public function getSamlProviderSloTargetUrl() {
    return @$this->attributes['saml_provider_slo_target_url'];
  }

  // string # Identity provider SSO endpoint if saml_provider_metadata_url is not available.
  public function getSamlProviderSsoTargetUrl() {
    return @$this->attributes['saml_provider_sso_target_url'];
  }

  // string # SCIM authentication type.
  public function getScimAuthenticationMethod() {
    return @$this->attributes['scim_authentication_method'];
  }

  // string # SCIM username.
  public function getScimUsername() {
    return @$this->attributes['scim_username'];
  }

  // string # SCIM OAuth Access Token.
  public function getScimOauthAccessToken() {
    return @$this->attributes['scim_oauth_access_token'];
  }

  // string # SCIM OAuth Access Token Expiration Time.
  public function getScimOauthAccessTokenExpiresAt() {
    return @$this->attributes['scim_oauth_access_token_expires_at'];
  }

  // string # Subdomain
  public function getSubdomain() {
    return @$this->attributes['subdomain'];
  }

  // boolean # Auto-provision users?
  public function getProvisionUsers() {
    return @$this->attributes['provision_users'];
  }

  // boolean # Auto-provision group membership based on group memberships on the SSO side?
  public function getProvisionGroups() {
    return @$this->attributes['provision_groups'];
  }

  // boolean # Auto-deprovision users?
  public function getDeprovisionUsers() {
    return @$this->attributes['deprovision_users'];
  }

  // boolean # Auto-deprovision group membership based on group memberships on the SSO side?
  public function getDeprovisionGroups() {
    return @$this->attributes['deprovision_groups'];
  }

  // string # Method used for deprovisioning users.
  public function getDeprovisionBehavior() {
    return @$this->attributes['deprovision_behavior'];
  }

  // string # Comma-separated list of group names for groups to automatically add all auto-provisioned users to.
  public function getProvisionGroupDefault() {
    return @$this->attributes['provision_group_default'];
  }

  // string # Comma-separated list of group names for groups (with optional wildcards) that will be excluded from auto-provisioning.
  public function getProvisionGroupExclusion() {
    return @$this->attributes['provision_group_exclusion'];
  }

  // string # Comma-separated list of group names for groups (with optional wildcards) that will be auto-provisioned.
  public function getProvisionGroupInclusion() {
    return @$this->attributes['provision_group_inclusion'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to require membership for user provisioning.
  public function getProvisionGroupRequired() {
    return @$this->attributes['provision_group_required'];
  }

  // string # Comma-separated list of group names whose members will be created with email_signup authentication.
  public function getProvisionEmailSignupGroups() {
    return @$this->attributes['provision_email_signup_groups'];
  }

  // string # Comma-separated list of group names whose members will be created as Site Admins.
  public function getProvisionSiteAdminGroups() {
    return @$this->attributes['provision_site_admin_groups'];
  }

  // string # Comma-separated list of group names whose members will be provisioned as Group Admins.
  public function getProvisionGroupAdminGroups() {
    return @$this->attributes['provision_group_admin_groups'];
  }

  // boolean # DEPRECATED: Auto-provisioned users get Sharing permission. Use a Group with the Bundle permission instead.
  public function getProvisionAttachmentsPermission() {
    return @$this->attributes['provision_attachments_permission'];
  }

  // boolean # Auto-provisioned users get WebDAV permission?
  public function getProvisionDavPermission() {
    return @$this->attributes['provision_dav_permission'];
  }

  // boolean # Auto-provisioned users get FTP permission?
  public function getProvisionFtpPermission() {
    return @$this->attributes['provision_ftp_permission'];
  }

  // boolean # Auto-provisioned users get SFTP permission?
  public function getProvisionSftpPermission() {
    return @$this->attributes['provision_sftp_permission'];
  }

  // string # Default time zone for auto provisioned users.
  public function getProvisionTimeZone() {
    return @$this->attributes['provision_time_zone'];
  }

  // string # Default company for auto provisioned users.
  public function getProvisionCompany() {
    return @$this->attributes['provision_company'];
  }

  // string # Base DN for looking up users in LDAP server
  public function getLdapBaseDn() {
    return @$this->attributes['ldap_base_dn'];
  }

  // string # Domain name that will be appended to LDAP usernames
  public function getLdapDomain() {
    return @$this->attributes['ldap_domain'];
  }

  // boolean # Is strategy enabled?  This may become automatically set to `false` after a high number and duration of failures.
  public function getEnabled() {
    return @$this->attributes['enabled'];
  }

  // string # LDAP host
  public function getLdapHost() {
    return @$this->attributes['ldap_host'];
  }

  // string # LDAP backup host
  public function getLdapHost2() {
    return @$this->attributes['ldap_host_2'];
  }

  // string # LDAP backup host
  public function getLdapHost3() {
    return @$this->attributes['ldap_host_3'];
  }

  // int64 # LDAP port
  public function getLdapPort() {
    return @$this->attributes['ldap_port'];
  }

  // boolean # Use secure LDAP?
  public function getLdapSecure() {
    return @$this->attributes['ldap_secure'];
  }

  // string # Username for signing in to LDAP server.
  public function getLdapUsername() {
    return @$this->attributes['ldap_username'];
  }

  // string # LDAP username field
  public function getLdapUsernameField() {
    return @$this->attributes['ldap_username_field'];
  }

  // Synchronize provisioning data with the SSO remote server
  public function sync($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/sso_strategies/' . @$params['id'] . '/sync', 'POST', $params, $this->options);
    return;
  }

  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function all($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/sso_strategies', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new SsoStrategy((array)$obj, $options);
    }

    return $return_array;
  }




  // Parameters:
  //   id (required) - int64 - Sso Strategy ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/sso_strategies/' . @$params['id'] . '', 'GET', $params, $options);

    return new SsoStrategy((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

}
