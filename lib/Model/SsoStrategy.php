<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SsoStrategy
 *
 * @package Files
 */
class SsoStrategy
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // string # SSO Protocol
    public function getProtocol()
    {
        return isset($this->attributes['protocol']) ? $this->attributes['protocol'] : null;
    }
    // string # Provider name
    public function getProvider()
    {
        return isset($this->attributes['provider']) ? $this->attributes['provider'] : null;
    }
    // string # Custom label for the SSO provider on the login page.
    public function getLabel()
    {
        return isset($this->attributes['label']) ? $this->attributes['label'] : null;
    }
    // string # URL holding a custom logo for the SSO provider on the login page.
    public function getLogoUrl()
    {
        return isset($this->attributes['logo_url']) ? $this->attributes['logo_url'] : null;
    }
    // int64 # ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // boolean # Is strategy enabled?  This may become automatically set to `false` after a high number and duration of failures.
    public function getEnabled()
    {
        return isset($this->attributes['enabled']) ? $this->attributes['enabled'] : null;
    }
    // int64 # Count of users with this SSO Strategy
    public function getUserCount()
    {
        return isset($this->attributes['user_count']) ? $this->attributes['user_count'] : null;
    }
    // string # Identity provider sha256 cert fingerprint if saml_provider_metadata_url is not available.
    public function getSamlProviderCertFingerprint()
    {
        return isset($this->attributes['saml_provider_cert_fingerprint']) ? $this->attributes['saml_provider_cert_fingerprint'] : null;
    }
    // string # Identity provider issuer url
    public function getSamlProviderIssuerUrl()
    {
        return isset($this->attributes['saml_provider_issuer_url']) ? $this->attributes['saml_provider_issuer_url'] : null;
    }
    // string # Custom identity provider metadata
    public function getSamlProviderMetadataContent()
    {
        return isset($this->attributes['saml_provider_metadata_content']) ? $this->attributes['saml_provider_metadata_content'] : null;
    }
    // string # Metadata URL for the SAML identity provider
    public function getSamlProviderMetadataUrl()
    {
        return isset($this->attributes['saml_provider_metadata_url']) ? $this->attributes['saml_provider_metadata_url'] : null;
    }
    // string # Identity provider SLO endpoint
    public function getSamlProviderSloTargetUrl()
    {
        return isset($this->attributes['saml_provider_slo_target_url']) ? $this->attributes['saml_provider_slo_target_url'] : null;
    }
    // string # Identity provider SSO endpoint if saml_provider_metadata_url is not available.
    public function getSamlProviderSsoTargetUrl()
    {
        return isset($this->attributes['saml_provider_sso_target_url']) ? $this->attributes['saml_provider_sso_target_url'] : null;
    }
    // string # SCIM authentication type.
    public function getScimAuthenticationMethod()
    {
        return isset($this->attributes['scim_authentication_method']) ? $this->attributes['scim_authentication_method'] : null;
    }
    // string # SCIM username.
    public function getScimUsername()
    {
        return isset($this->attributes['scim_username']) ? $this->attributes['scim_username'] : null;
    }
    // string # SCIM OAuth Access Token. Only returned when the token is first issued or reset.
    public function getScimOauthAccessToken()
    {
        return isset($this->attributes['scim_oauth_access_token']) ? $this->attributes['scim_oauth_access_token'] : null;
    }
    // string # SCIM OAuth Access Token Expiration Time.
    public function getScimOauthAccessTokenExpiresAt()
    {
        return isset($this->attributes['scim_oauth_access_token_expires_at']) ? $this->attributes['scim_oauth_access_token_expires_at'] : null;
    }
    // string # Subdomain or domain name for your auth provider.   Example: `https://[subdomain].okta.com/`
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    // boolean # Auto-provision users?
    public function getProvisionUsers()
    {
        return isset($this->attributes['provision_users']) ? $this->attributes['provision_users'] : null;
    }
    // boolean # Auto-provision group membership based on group memberships on the SSO side?
    public function getProvisionGroups()
    {
        return isset($this->attributes['provision_groups']) ? $this->attributes['provision_groups'] : null;
    }
    // boolean # Auto-deprovision users?
    public function getDeprovisionUsers()
    {
        return isset($this->attributes['deprovision_users']) ? $this->attributes['deprovision_users'] : null;
    }
    // boolean # Auto-deprovision group membership based on group memberships on the SSO side?
    public function getDeprovisionGroups()
    {
        return isset($this->attributes['deprovision_groups']) ? $this->attributes['deprovision_groups'] : null;
    }
    // string # Method used for deprovisioning users.
    public function getDeprovisionBehavior()
    {
        return isset($this->attributes['deprovision_behavior']) ? $this->attributes['deprovision_behavior'] : null;
    }
    // string # Comma-separated list of group names for groups to automatically add all auto-provisioned users to.
    public function getProvisionGroupDefault()
    {
        return isset($this->attributes['provision_group_default']) ? $this->attributes['provision_group_default'] : null;
    }
    // string # Comma-separated list of group names for groups (with optional wildcards) that will be excluded from auto-provisioning.
    public function getProvisionGroupExclusion()
    {
        return isset($this->attributes['provision_group_exclusion']) ? $this->attributes['provision_group_exclusion'] : null;
    }
    // string # Comma-separated list of group names for groups (with optional wildcards) that will be auto-provisioned.
    public function getProvisionGroupInclusion()
    {
        return isset($this->attributes['provision_group_inclusion']) ? $this->attributes['provision_group_inclusion'] : null;
    }
    // string # Comma or newline separated list of group names (with optional wildcards) to require membership for user provisioning.
    public function getProvisionGroupRequired()
    {
        return isset($this->attributes['provision_group_required']) ? $this->attributes['provision_group_required'] : null;
    }
    // string # Comma-separated list of group names whose members will be created with email_signup authentication.
    public function getProvisionEmailSignupGroups()
    {
        return isset($this->attributes['provision_email_signup_groups']) ? $this->attributes['provision_email_signup_groups'] : null;
    }
    // string # Comma-separated list of group names used to grant or remove Read-Only Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
    public function getProvisionReadonlySiteAdminGroups()
    {
        return isset($this->attributes['provision_readonly_site_admin_groups']) ? $this->attributes['provision_readonly_site_admin_groups'] : null;
    }
    // string # Comma-separated list of group names used to grant or remove Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
    public function getProvisionSiteAdminGroups()
    {
        return isset($this->attributes['provision_site_admin_groups']) ? $this->attributes['provision_site_admin_groups'] : null;
    }
    // string # Comma-separated list of group names whose members will be provisioned as Group Admins.
    public function getProvisionGroupAdminGroups()
    {
        return isset($this->attributes['provision_group_admin_groups']) ? $this->attributes['provision_group_admin_groups'] : null;
    }
    // boolean
    public function getProvisionAttachmentsPermission()
    {
        return isset($this->attributes['provision_attachments_permission']) ? $this->attributes['provision_attachments_permission'] : null;
    }
    // boolean # Auto-provisioned users get WebDAV permission?
    public function getProvisionDavPermission()
    {
        return isset($this->attributes['provision_dav_permission']) ? $this->attributes['provision_dav_permission'] : null;
    }
    // boolean # Auto-provisioned users get FTP permission?
    public function getProvisionFtpPermission()
    {
        return isset($this->attributes['provision_ftp_permission']) ? $this->attributes['provision_ftp_permission'] : null;
    }
    // boolean # Auto-provisioned users get SFTP permission?
    public function getProvisionSftpPermission()
    {
        return isset($this->attributes['provision_sftp_permission']) ? $this->attributes['provision_sftp_permission'] : null;
    }
    // string # Default time zone for auto provisioned users.
    public function getProvisionTimeZone()
    {
        return isset($this->attributes['provision_time_zone']) ? $this->attributes['provision_time_zone'] : null;
    }
    // string # Default company for auto provisioned users.
    public function getProvisionCompany()
    {
        return isset($this->attributes['provision_company']) ? $this->attributes['provision_company'] : null;
    }
    // string # 2FA required setting for auto provisioned users. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
    public function getProvisionRequire2fa()
    {
        return isset($this->attributes['provision_require_2fa']) ? $this->attributes['provision_require_2fa'] : null;
    }
    // string # File System layout to use for auto provisioned users.
    public function getProvisionFilesystemLayout()
    {
        return isset($this->attributes['provision_filesystem_layout']) ? $this->attributes['provision_filesystem_layout'] : null;
    }
    // string # URL-friendly, unique identifier for Azure SAML configuration
    public function getProviderIdentifier()
    {
        return isset($this->attributes['provider_identifier']) ? $this->attributes['provider_identifier'] : null;
    }
    // string # Base DN for looking up users in LDAP server
    public function getLdapBaseDn()
    {
        return isset($this->attributes['ldap_base_dn']) ? $this->attributes['ldap_base_dn'] : null;
    }
    // string # Domain name that will be appended to LDAP usernames
    public function getLdapDomain()
    {
        return isset($this->attributes['ldap_domain']) ? $this->attributes['ldap_domain'] : null;
    }
    // boolean # Should this strategy be displayed on the login page?
    public function getDisplayOnLoginPage()
    {
        return isset($this->attributes['display_on_login_page']) ? $this->attributes['display_on_login_page'] : null;
    }
    // string # LDAP host
    public function getLdapHost()
    {
        return isset($this->attributes['ldap_host']) ? $this->attributes['ldap_host'] : null;
    }
    // string # LDAP backup host
    public function getLdapHost2()
    {
        return isset($this->attributes['ldap_host_2']) ? $this->attributes['ldap_host_2'] : null;
    }
    // string # LDAP backup host
    public function getLdapHost3()
    {
        return isset($this->attributes['ldap_host_3']) ? $this->attributes['ldap_host_3'] : null;
    }
    // int64 # LDAP port
    public function getLdapPort()
    {
        return isset($this->attributes['ldap_port']) ? $this->attributes['ldap_port'] : null;
    }
    // boolean # Use LDAP server settings for scheduled provisioning while using this SSO provider for authentication?
    public function getLdapProvisioningEnabled()
    {
        return isset($this->attributes['ldap_provisioning_enabled']) ? $this->attributes['ldap_provisioning_enabled'] : null;
    }
    // boolean # Use secure LDAP?
    public function getLdapSecure()
    {
        return isset($this->attributes['ldap_secure']) ? $this->attributes['ldap_secure'] : null;
    }
    // string # How to validate the LDAP server certificate. `require_match` validates the certificate chain and hostname; `allow_any` disables certificate validation.
    public function getLdapServerCertificate()
    {
        return isset($this->attributes['ldap_server_certificate']) ? $this->attributes['ldap_server_certificate'] : null;
    }
    // string # LDAP server type
    public function getLdapType()
    {
        return isset($this->attributes['ldap_type']) ? $this->attributes['ldap_type'] : null;
    }
    // string # Username for signing in to LDAP server.
    public function getLdapUsername()
    {
        return isset($this->attributes['ldap_username']) ? $this->attributes['ldap_username'] : null;
    }
    // string # LDAP username field
    public function getLdapUsernameField()
    {
        return isset($this->attributes['ldap_username_field']) ? $this->attributes['ldap_username_field'] : null;
    }

    // Synchronize provisioning data with the SSO remote server
    public function sync($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/sso_strategies/' . rawurlencode(strval(@$params['id'])) . '/sync', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/sso_strategies', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new SsoStrategy((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Sso Strategy ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/sso_strategies/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SsoStrategy((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
