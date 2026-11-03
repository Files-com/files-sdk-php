<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An SSOStrategy is a way for users to sign in via another identity provider, such as Okta or Auth0.
 *
 * @property string|null $protocol SSO Protocol
 * @property string|null $provider Provider name
 * @property string|null $label Custom label for the SSO provider on the login page.
 * @property string|null $logo_url URL holding a custom logo for the SSO provider on the login page.
 * @property int|null $id ID
 * @property bool|null $enabled Is strategy enabled? This may become automatically set to `false` after a high number and duration of failures.
 * @property int|null $user_count Count of users with this SSO Strategy
 * @property string|null $saml_provider_cert_fingerprint Identity provider sha256 cert fingerprint if saml_provider_metadata_url is not available.
 * @property string|null $saml_provider_issuer_url Identity provider issuer url
 * @property string|null $saml_provider_metadata_content Custom identity provider metadata
 * @property string|null $saml_provider_metadata_url Metadata URL for the SAML identity provider
 * @property string|null $saml_provider_slo_target_url Identity provider SLO endpoint
 * @property string|null $saml_provider_sso_target_url Identity provider SSO endpoint if saml_provider_metadata_url is not available.
 * @property string|null $scim_authentication_method SCIM authentication type.
 * @property string|null $scim_username SCIM username.
 * @property string|null $scim_oauth_access_token SCIM OAuth Access Token. Only returned when the token is first issued or reset.
 * @property string|null $scim_oauth_access_token_expires_at SCIM OAuth Access Token Expiration Time.
 * @property string|null $subdomain Subdomain or domain name for your auth provider. Example: `https://[subdomain].okta.com/`
 * @property bool|null $provision_users Auto-provision users?
 * @property bool|null $provision_groups Auto-provision group membership based on group memberships on the SSO side?
 * @property bool|null $deprovision_users Auto-deprovision users?
 * @property bool|null $deprovision_groups Auto-deprovision group membership based on group memberships on the SSO side?
 * @property string|null $deprovision_behavior Method used for deprovisioning users.
 * @property string|null $provision_group_default Comma-separated list of group names for groups to automatically add all auto-provisioned users to.
 * @property string|null $provision_group_exclusion Comma-separated list of group names for groups (with optional wildcards) that will be excluded from auto-provisioning.
 * @property string|null $provision_group_inclusion Comma-separated list of group names for groups (with optional wildcards) that will be auto-provisioned.
 * @property string|null $provision_group_required Comma or newline separated list of group names (with optional wildcards) to require membership for user provisioning.
 * @property string|null $provision_email_signup_groups Comma-separated list of group names whose members will be created with email_signup authentication.
 * @property string|null $provision_readonly_site_admin_groups Comma-separated list of group names used to grant or remove Read-Only Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
 * @property string|null $provision_site_admin_groups Comma-separated list of group names used to grant or remove Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
 * @property string|null $provision_group_admin_groups Comma-separated list of group names whose members will be provisioned as Group Admins.
 * @property bool|null $provision_attachments_permission
 * @property bool|null $provision_dav_permission Auto-provisioned users get WebDAV permission?
 * @property bool|null $provision_ftp_permission Auto-provisioned users get FTP permission?
 * @property bool|null $provision_sftp_permission Auto-provisioned users get SFTP permission?
 * @property string|null $provision_time_zone Default time zone for auto provisioned users.
 * @property string|null $provision_company Default company for auto provisioned users.
 * @property string|null $provision_require_2fa 2FA required setting for auto provisioned users. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
 * @property string|null $provision_filesystem_layout File System layout to use for auto provisioned users.
 * @property string|null $provider_identifier URL-friendly, unique identifier for Azure SAML configuration
 * @property string|null $ldap_base_dn Base DN for looking up users in LDAP server
 * @property string|null $ldap_domain Domain name that will be appended to LDAP usernames
 * @property bool|null $display_on_login_page Should this strategy be displayed on the login page?
 * @property string|null $ldap_host LDAP host
 * @property string|null $ldap_host_2 LDAP backup host
 * @property string|null $ldap_host_3 LDAP backup host
 * @property int|null $ldap_port LDAP port
 * @property bool|null $ldap_provisioning_enabled Use LDAP server settings for scheduled provisioning while using this SSO provider for authentication?
 * @property bool|null $ldap_secure Use secure LDAP?
 * @property string|null $ldap_server_certificate How to validate the LDAP server certificate. `require_match` validates the certificate chain and hostname; `allow_any` disables certificate validation.
 * @property string|null $ldap_type LDAP server type
 * @property string|null $ldap_username Username for signing in to LDAP server.
 * @property string|null $ldap_username_field LDAP username field
 * @package Files
 */
class SsoStrategy
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * SSO Protocol
     *
     * @return string|null
     */
    public function getProtocol()
    {
        return isset($this->attributes['protocol']) ? $this->attributes['protocol'] : null;
    }
    /**
     * Provider name
     *
     * @return string|null
     */
    public function getProvider()
    {
        return isset($this->attributes['provider']) ? $this->attributes['provider'] : null;
    }
    /**
     * Custom label for the SSO provider on the login page.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return isset($this->attributes['label']) ? $this->attributes['label'] : null;
    }
    /**
     * URL holding a custom logo for the SSO provider on the login page.
     *
     * @return string|null
     */
    public function getLogoUrl()
    {
        return isset($this->attributes['logo_url']) ? $this->attributes['logo_url'] : null;
    }
    /**
     * ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Is strategy enabled?  This may become automatically set to `false` after a high number and duration of failures.
     *
     * @return bool|null
     */
    public function getEnabled()
    {
        return isset($this->attributes['enabled']) ? $this->attributes['enabled'] : null;
    }
    /**
     * Count of users with this SSO Strategy
     *
     * @return int|null
     */
    public function getUserCount()
    {
        return isset($this->attributes['user_count']) ? $this->attributes['user_count'] : null;
    }
    /**
     * Identity provider sha256 cert fingerprint if saml_provider_metadata_url is not available.
     *
     * @return string|null
     */
    public function getSamlProviderCertFingerprint()
    {
        return isset($this->attributes['saml_provider_cert_fingerprint']) ? $this->attributes['saml_provider_cert_fingerprint'] : null;
    }
    /**
     * Identity provider issuer url
     *
     * @return string|null
     */
    public function getSamlProviderIssuerUrl()
    {
        return isset($this->attributes['saml_provider_issuer_url']) ? $this->attributes['saml_provider_issuer_url'] : null;
    }
    /**
     * Custom identity provider metadata
     *
     * @return string|null
     */
    public function getSamlProviderMetadataContent()
    {
        return isset($this->attributes['saml_provider_metadata_content']) ? $this->attributes['saml_provider_metadata_content'] : null;
    }
    /**
     * Metadata URL for the SAML identity provider
     *
     * @return string|null
     */
    public function getSamlProviderMetadataUrl()
    {
        return isset($this->attributes['saml_provider_metadata_url']) ? $this->attributes['saml_provider_metadata_url'] : null;
    }
    /**
     * Identity provider SLO endpoint
     *
     * @return string|null
     */
    public function getSamlProviderSloTargetUrl()
    {
        return isset($this->attributes['saml_provider_slo_target_url']) ? $this->attributes['saml_provider_slo_target_url'] : null;
    }
    /**
     * Identity provider SSO endpoint if saml_provider_metadata_url is not available.
     *
     * @return string|null
     */
    public function getSamlProviderSsoTargetUrl()
    {
        return isset($this->attributes['saml_provider_sso_target_url']) ? $this->attributes['saml_provider_sso_target_url'] : null;
    }
    /**
     * SCIM authentication type.
     *
     * @return string|null
     */
    public function getScimAuthenticationMethod()
    {
        return isset($this->attributes['scim_authentication_method']) ? $this->attributes['scim_authentication_method'] : null;
    }
    /**
     * SCIM username.
     *
     * @return string|null
     */
    public function getScimUsername()
    {
        return isset($this->attributes['scim_username']) ? $this->attributes['scim_username'] : null;
    }
    /**
     * SCIM OAuth Access Token. Only returned when the token is first issued or reset.
     *
     * @return string|null
     */
    public function getScimOauthAccessToken()
    {
        return isset($this->attributes['scim_oauth_access_token']) ? $this->attributes['scim_oauth_access_token'] : null;
    }
    /**
     * SCIM OAuth Access Token Expiration Time.
     *
     * @return string|null
     */
    public function getScimOauthAccessTokenExpiresAt()
    {
        return isset($this->attributes['scim_oauth_access_token_expires_at']) ? $this->attributes['scim_oauth_access_token_expires_at'] : null;
    }
    /**
     * Subdomain or domain name for your auth provider.   Example: `https://[subdomain].okta.com/`
     *
     * @return string|null
     */
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    /**
     * Auto-provision users?
     *
     * @return bool|null
     */
    public function getProvisionUsers()
    {
        return isset($this->attributes['provision_users']) ? $this->attributes['provision_users'] : null;
    }
    /**
     * Auto-provision group membership based on group memberships on the SSO side?
     *
     * @return bool|null
     */
    public function getProvisionGroups()
    {
        return isset($this->attributes['provision_groups']) ? $this->attributes['provision_groups'] : null;
    }
    /**
     * Auto-deprovision users?
     *
     * @return bool|null
     */
    public function getDeprovisionUsers()
    {
        return isset($this->attributes['deprovision_users']) ? $this->attributes['deprovision_users'] : null;
    }
    /**
     * Auto-deprovision group membership based on group memberships on the SSO side?
     *
     * @return bool|null
     */
    public function getDeprovisionGroups()
    {
        return isset($this->attributes['deprovision_groups']) ? $this->attributes['deprovision_groups'] : null;
    }
    /**
     * Method used for deprovisioning users.
     *
     * @return string|null
     */
    public function getDeprovisionBehavior()
    {
        return isset($this->attributes['deprovision_behavior']) ? $this->attributes['deprovision_behavior'] : null;
    }
    /**
     * Comma-separated list of group names for groups to automatically add all auto-provisioned users to.
     *
     * @return string|null
     */
    public function getProvisionGroupDefault()
    {
        return isset($this->attributes['provision_group_default']) ? $this->attributes['provision_group_default'] : null;
    }
    /**
     * Comma-separated list of group names for groups (with optional wildcards) that will be excluded from auto-provisioning.
     *
     * @return string|null
     */
    public function getProvisionGroupExclusion()
    {
        return isset($this->attributes['provision_group_exclusion']) ? $this->attributes['provision_group_exclusion'] : null;
    }
    /**
     * Comma-separated list of group names for groups (with optional wildcards) that will be auto-provisioned.
     *
     * @return string|null
     */
    public function getProvisionGroupInclusion()
    {
        return isset($this->attributes['provision_group_inclusion']) ? $this->attributes['provision_group_inclusion'] : null;
    }
    /**
     * Comma or newline separated list of group names (with optional wildcards) to require membership for user provisioning.
     *
     * @return string|null
     */
    public function getProvisionGroupRequired()
    {
        return isset($this->attributes['provision_group_required']) ? $this->attributes['provision_group_required'] : null;
    }
    /**
     * Comma-separated list of group names whose members will be created with email_signup authentication.
     *
     * @return string|null
     */
    public function getProvisionEmailSignupGroups()
    {
        return isset($this->attributes['provision_email_signup_groups']) ? $this->attributes['provision_email_signup_groups'] : null;
    }
    /**
     * Comma-separated list of group names used to grant or remove Read-Only Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
     *
     * @return string|null
     */
    public function getProvisionReadonlySiteAdminGroups()
    {
        return isset($this->attributes['provision_readonly_site_admin_groups']) ? $this->attributes['provision_readonly_site_admin_groups'] : null;
    }
    /**
     * Comma-separated list of group names used to grant or remove Site Admin access for users on this SSO provider. When this list is nonempty and a directory sync changes a user's group membership, membership in at least one listed group grants this access; membership in none removes it, including access granted manually. Directory syncs include SCIM requests from any provider on the site, LDAP syncs, and sign-ins that set the user's groups from the identity provider. Membership changes made any other way, including through the web app, API, SDKs, or CLI, do not affect administrator access.
     *
     * @return string|null
     */
    public function getProvisionSiteAdminGroups()
    {
        return isset($this->attributes['provision_site_admin_groups']) ? $this->attributes['provision_site_admin_groups'] : null;
    }
    /**
     * Comma-separated list of group names whose members will be provisioned as Group Admins.
     *
     * @return string|null
     */
    public function getProvisionGroupAdminGroups()
    {
        return isset($this->attributes['provision_group_admin_groups']) ? $this->attributes['provision_group_admin_groups'] : null;
    }
    /**
     * Read provision_attachments_permission.
     *
     * @return bool|null
     */
    public function getProvisionAttachmentsPermission()
    {
        return isset($this->attributes['provision_attachments_permission']) ? $this->attributes['provision_attachments_permission'] : null;
    }
    /**
     * Auto-provisioned users get WebDAV permission?
     *
     * @return bool|null
     */
    public function getProvisionDavPermission()
    {
        return isset($this->attributes['provision_dav_permission']) ? $this->attributes['provision_dav_permission'] : null;
    }
    /**
     * Auto-provisioned users get FTP permission?
     *
     * @return bool|null
     */
    public function getProvisionFtpPermission()
    {
        return isset($this->attributes['provision_ftp_permission']) ? $this->attributes['provision_ftp_permission'] : null;
    }
    /**
     * Auto-provisioned users get SFTP permission?
     *
     * @return bool|null
     */
    public function getProvisionSftpPermission()
    {
        return isset($this->attributes['provision_sftp_permission']) ? $this->attributes['provision_sftp_permission'] : null;
    }
    /**
     * Default time zone for auto provisioned users.
     *
     * @return string|null
     */
    public function getProvisionTimeZone()
    {
        return isset($this->attributes['provision_time_zone']) ? $this->attributes['provision_time_zone'] : null;
    }
    /**
     * Default company for auto provisioned users.
     *
     * @return string|null
     */
    public function getProvisionCompany()
    {
        return isset($this->attributes['provision_company']) ? $this->attributes['provision_company'] : null;
    }
    /**
     * 2FA required setting for auto provisioned users. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
     *
     * @return string|null
     */
    public function getProvisionRequire2fa()
    {
        return isset($this->attributes['provision_require_2fa']) ? $this->attributes['provision_require_2fa'] : null;
    }
    /**
     * File System layout to use for auto provisioned users.
     *
     * @return string|null
     */
    public function getProvisionFilesystemLayout()
    {
        return isset($this->attributes['provision_filesystem_layout']) ? $this->attributes['provision_filesystem_layout'] : null;
    }
    /**
     * URL-friendly, unique identifier for Azure SAML configuration
     *
     * @return string|null
     */
    public function getProviderIdentifier()
    {
        return isset($this->attributes['provider_identifier']) ? $this->attributes['provider_identifier'] : null;
    }
    /**
     * Base DN for looking up users in LDAP server
     *
     * @return string|null
     */
    public function getLdapBaseDn()
    {
        return isset($this->attributes['ldap_base_dn']) ? $this->attributes['ldap_base_dn'] : null;
    }
    /**
     * Domain name that will be appended to LDAP usernames
     *
     * @return string|null
     */
    public function getLdapDomain()
    {
        return isset($this->attributes['ldap_domain']) ? $this->attributes['ldap_domain'] : null;
    }
    /**
     * Should this strategy be displayed on the login page?
     *
     * @return bool|null
     */
    public function getDisplayOnLoginPage()
    {
        return isset($this->attributes['display_on_login_page']) ? $this->attributes['display_on_login_page'] : null;
    }
    /**
     * LDAP host
     *
     * @return string|null
     */
    public function getLdapHost()
    {
        return isset($this->attributes['ldap_host']) ? $this->attributes['ldap_host'] : null;
    }
    /**
     * LDAP backup host
     *
     * @return string|null
     */
    public function getLdapHost2()
    {
        return isset($this->attributes['ldap_host_2']) ? $this->attributes['ldap_host_2'] : null;
    }
    /**
     * LDAP backup host
     *
     * @return string|null
     */
    public function getLdapHost3()
    {
        return isset($this->attributes['ldap_host_3']) ? $this->attributes['ldap_host_3'] : null;
    }
    /**
     * LDAP port
     *
     * @return int|null
     */
    public function getLdapPort()
    {
        return isset($this->attributes['ldap_port']) ? $this->attributes['ldap_port'] : null;
    }
    /**
     * Use LDAP server settings for scheduled provisioning while using this SSO provider for authentication?
     *
     * @return bool|null
     */
    public function getLdapProvisioningEnabled()
    {
        return isset($this->attributes['ldap_provisioning_enabled']) ? $this->attributes['ldap_provisioning_enabled'] : null;
    }
    /**
     * Use secure LDAP?
     *
     * @return bool|null
     */
    public function getLdapSecure()
    {
        return isset($this->attributes['ldap_secure']) ? $this->attributes['ldap_secure'] : null;
    }
    /**
     * How to validate the LDAP server certificate. `require_match` validates the certificate chain and hostname; `allow_any` disables certificate validation.
     *
     * @return string|null
     */
    public function getLdapServerCertificate()
    {
        return isset($this->attributes['ldap_server_certificate']) ? $this->attributes['ldap_server_certificate'] : null;
    }
    /**
     * LDAP server type
     *
     * @return string|null
     */
    public function getLdapType()
    {
        return isset($this->attributes['ldap_type']) ? $this->attributes['ldap_type'] : null;
    }
    /**
     * Username for signing in to LDAP server.
     *
     * @return string|null
     */
    public function getLdapUsername()
    {
        return isset($this->attributes['ldap_username']) ? $this->attributes['ldap_username'] : null;
    }
    /**
     * LDAP username field
     *
     * @return string|null
     */
    public function getLdapUsernameField()
    {
        return isset($this->attributes['ldap_username_field']) ? $this->attributes['ldap_username_field'] : null;
    }

    /**
     * Synchronize provisioning data with the SSO remote server
     *
     * POST /sso_strategies/{id}/sync
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sso Strategy ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/sso_strategies/' . rawurlencode(strval(@$params['id'])) . '/sync', 'POST', $params, $this->options);
        return;
    }

    /**
     * List SSO Strategies
     *
     * GET /sso_strategies
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SsoStrategy[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/sso_strategies', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new SsoStrategy(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/sso_strategies', $params, $options, SsoStrategy::class);
    }

    /**
     * Show SSO Strategy
     *
     * GET /sso_strategies/{id}
     *
     * @param int $id Sso Strategy ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SsoStrategy
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/sso_strategies/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SsoStrategy(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show SSO Strategy
     *
     * GET /sso_strategies/{id}
     *
     * Alias of find().
     *
     * @param int $id Sso Strategy ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SsoStrategy
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
