<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A User represents a human or system/service user with the ability to connect to Files.com via any of the available connectivity methods (unless restricted to specific protocols).
 *
 * @property int|null $id User ID
 * @property string|null $username User's username
 * @property int[]|null $admin_group_ids List of group IDs of which this user is an administrator
 * @property string|null $allowed_ips A list of allowed IPs if applicable. Newline delimited
 * @property bool|null $attachments_permission If `true`, the user can user create Bundles (aka Share Links). Use the bundle permission instead.
 * @property int|null $api_keys_count Number of API keys associated with this user
 * @property string|null $authenticate_until Scheduled Date/Time at which user will be deactivated
 * @property string|null $authentication_method How is this user authenticated?
 * @property string|null $avatar_url URL holding the user's avatar
 * @property bool|null $billable Is this a billable user record?
 * @property bool|null $billing_permission Allow this user to perform operations on the account, payments, and invoices?
 * @property bool|null $bypass_site_allowed_ips Allow this user to skip site-wide IP blacklists?
 * @property bool|null $bypass_user_lifecycle_rules Exempt this user from user lifecycle rules?
 * @property string|null $created_at When this user was created
 * @property bool|null $dav_permission Can the user connect with WebDAV?
 * @property bool|null $disabled Is user disabled? Disabled users cannot log in, and do not count for billing purposes. Users can be automatically disabled after an inactivity period via a Site setting or schedule to be deactivated after specific date.
 * @property bool|null $disabled_expired_or_inactive Computed property that returns true if user disabled or expired or inactive.
 * @property int|null $ai_assistant_personality_id AI Assistant Personality ID assigned directly to this user, if any.
 * @property int|null $desktop_configuration_profile_id Desktop Configuration Profile ID assigned directly to this user, if any.
 * @property int|null $integration_centric_profile_id Integration Centric Profile ID assigned directly to this user, if any.
 * @property string|null $email User email address
 * @property string|null $filesystem_layout File system layout
 * @property string|null $first_login_at User's first login time
 * @property bool|null $ftp_permission Can the user access with FTP/FTPS?
 * @property string|null $group_ids Comma-separated list of group IDs of which this user is a member
 * @property string|null $header_text Text to display to the user in the header of the UI
 * @property string|null $language Preferred language
 * @property string|null $last_login_at User's most recent login time via any protocol
 * @property string|null $last_web_login_at User's most recent login time via web
 * @property string|null $last_ftp_login_at User's most recent login time via FTP
 * @property string|null $last_sftp_login_at User's most recent login time via SFTP
 * @property string|null $last_dav_login_at User's most recent login time via WebDAV
 * @property string|null $last_desktop_login_at User's most recent login time via Desktop app
 * @property string|null $last_restapi_login_at User's most recent login time via Rest API
 * @property string|null $last_api_use_at User's most recent API use time
 * @property string|null $last_active_at User's most recent activity time, which is the latest of most recent login, most recent API use, enablement, or creation
 * @property string|null $last_protocol_cipher The most recent protocol and cipher used
 * @property string|null $lockout_expires Time in the future that the user will no longer be locked out if applicable
 * @property string|null $name User's full name
 * @property string|null $company User's company
 * @property string|null $notes Any internal notes on the user
 * @property int|null $notification_daily_send_time Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
 * @property bool|null $office_integration_enabled Enable integration with Office for the web?
 * @property bool|null $partner_admin Is this user a Partner administrator?
 * @property int|null $partner_id Partner ID if this user belongs to a Partner
 * @property string|null $partner_name Name of the Partner if this user belongs to a Partner
 * @property string|null $password_set_at Last time the user's password was set
 * @property int|null $password_validity_days Number of days to allow user to use the same password
 * @property int|null $primary_group_id Primary group ID for Group Admin scoping
 * @property int|null $public_keys_count Number of public keys associated with this user
 * @property bool|null $receive_admin_alerts Deprecated. Use notify_on_all_site_warnings and granular failure notification preferences instead.
 * @property bool|null $notify_on_all_site_warnings Should the user receive site warnings via email?
 * @property bool|null $notify_on_all_sso_failures Should the user receive sso/scim/ldap configuration/sync failures via email?
 * @property bool|null $notify_on_all_user_security_events Should the user receive user security events via email?
 * @property bool|null $notify_on_all_pending_work_failures Should the user receive pending work failures via email?
 * @property bool|null $notify_on_all_siem_http_destination_failures Should the user receive siem failures via email?
 * @property bool|null $notify_on_all_sync_failures Should the user receive sync failures via email?
 * @property bool|null $notify_on_all_automation_failures Should the user receive automation failures via email?
 * @property bool|null $notify_on_all_expectation_failures Should the user receive expectation failures and misses via email?
 * @property string|null $require_2fa 2FA required setting. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
 * @property string|null $require_login_by Require user to login by specified date otherwise it will be disabled.
 * @property bool|null $active_2fa Is 2fa active for the user?
 * @property bool|null $require_password_change Is a password change required upon next user login?
 * @property bool|null $password_expired Is user's password expired?
 * @property int|null $responsible_group_id ID of the internal Group responsible for this Partner User, overriding the Partner default.
 * @property int|null $responsible_user_id ID of the internal User responsible for this Partner User, overriding the Partner default.
 * @property bool|null $readonly_site_admin Is the user an allowed to view all (non-billing) site configuration for this site?
 * @property bool|null $restapi_permission Can this user access the Web app, Desktop app, SDKs, or REST API? (All of these tools use the API internally, so this is one unified permission set.)
 * @property bool|null $s3_compatible_endpoint_permission Can the user access the S3-compatible endpoint? Defaults to true.
 * @property bool|null $self_managed Does this user manage it's own credentials or is it a shared/bot user?
 * @property bool|null $sftp_permission Can the user access with SFTP?
 * @property bool|null $site_admin Is the user an administrator for this site?
 * @property bool|null $workspace_admin Whether the user is an administrator of their own Custom Workspace. Does not reflect administration granted through Permissions.
 * @property int|null $site_id Site ID
 * @property int|null $workspace_id ID of the Workspace the user belongs to. 0 is the Default Workspace.
 * @property int|null $default_workspace_id Workspace ID the user should land in by default when more than one Workspace is available.
 * @property bool|null $skip_welcome_screen Skip Welcome page in the UI?
 * @property string|null $ftp_tls_session_reuse_required FTP TLS session reuse requirement: use_system_setting, always_require, or never_require
 * @property string|null $ssl_required SSL required setting
 * @property int|null $sso_strategy_id SSO (Single Sign On) strategy ID for the user, if applicable.
 * @property bool|null $externally_managed Is this user managed by a SsoStrategy?
 * @property string|null $tags Comma-separated list of Tags for this user. Tags are used for other features, such as UserLifecycleRules, which can target specific tags. Tags must only contain lowercase letters, numbers, and hyphens.
 * @property string|null $time_zone User time zone
 * @property string|null $type_of_2fa Type(s) of 2FA methods in use, for programmatic use. Will be either `sms`, `totp`, `webauthn`, `yubi`, `email`, or multiple values sorted alphabetically and joined by an underscore. Does not specify whether user has more than one of a given method.
 * @property string|null $type_of_2fa_for_display Type(s) of 2FA methods in use, formatted for displaying in the UI. Unlike `type_of_2fa`, this value will make clear when a user has more than 1 of the same type of method.
 * @property string|null $user_root If filesystem layout is user_root, this path is the root path the user is fixed to for all interfaces. If the filesystem layout is site_root or partner_root, this acts as a root folder only for FTP and SFTP (SFTP applicability also requires a site-wide setting to be set). For partner_root layout, this path is relative to the Partner root folder for all callers and blank opts out of an additional protocol root. In this situation, this path is not applied to the API, Desktop, or Web interface.
 * @property string|null $user_home Home folder for FTP/SFTP. For users with the partner_root filesystem layout, this path is relative to the Partner root folder. In all other cases, it is an absolute path. Only applies to FTP and SFTP, and not any other interface.
 * @property int|null $days_remaining_until_password_expire Number of days remaining until password expires
 * @property string|null $password_expire_at Password expiration datetime
 * @property mixed $avatar_file An image file for your user avatar.
 * @property bool|null $avatar_delete If true, the avatar will be deleted.
 * @property string|null $change_password Used for changing a password on an existing user.
 * @property string|null $change_password_confirmation Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
 * @property string|null $grant_permission Permission to grant on the User Root upon creation, or on the Workspace root when the User Root is blank. For the Default Workspace, a blank User Root uses the site root. `admin` on a Custom Workspace's root grants Workspace Administrator access; on a subfolder it grants Folder Admin access. Can be blank or `admin`, `full`, `read`, `write`, `list`, `read+write`, `list+write`, or `history`.
 * @property int|null $group_id Group ID to associate this user with.
 * @property string|null $imported_password_hash Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash methods are MD5, SHA1, and SHA256.
 * @property string|null $password User password.
 * @property string|null $password_confirmation Optional, but if provided, we will ensure that it matches the value sent in `password`.
 * @property bool|null $announcements_read Signifies that the user has read all the announcements in the UI.
 * @property bool|null $clear_2fa If true when changing authentication_method from `password` to `sso`, remove all two-factor methods. Ignored in all other cases.
 * @property bool|null $convert_to_partner_user Required when assigning a Partner to an existing non-Partner user. If true, convert the user by assigning the partner_id provided.
 * @package Files
 */
class User
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
     * User ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * User ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * User's username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * User's username
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * List of group IDs of which this user is an administrator
     *
     * @return int[]|null
     */
    public function getAdminGroupIds()
    {
        return isset($this->attributes['admin_group_ids']) ? $this->attributes['admin_group_ids'] : null;
    }

    /**
     * List of group IDs of which this user is an administrator
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setAdminGroupIds($value)
    {
        return $this->attributes['admin_group_ids'] = $value;
    }
    /**
     * A list of allowed IPs if applicable.  Newline delimited
     *
     * @return string|null
     */
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }

    /**
     * A list of allowed IPs if applicable.  Newline delimited
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAllowedIps($value)
    {
        return $this->attributes['allowed_ips'] = $value;
    }
    /**
     * If `true`, the user can user create Bundles (aka Share Links). Use the bundle permission instead.
     *
     * @return bool|null
     */
    public function getAttachmentsPermission()
    {
        return isset($this->attributes['attachments_permission']) ? $this->attributes['attachments_permission'] : null;
    }

    /**
     * If `true`, the user can user create Bundles (aka Share Links). Use the bundle permission instead.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAttachmentsPermission($value)
    {
        return $this->attributes['attachments_permission'] = $value;
    }
    /**
     * Number of API keys associated with this user
     *
     * @return int|null
     */
    public function getApiKeysCount()
    {
        return isset($this->attributes['api_keys_count']) ? $this->attributes['api_keys_count'] : null;
    }

    /**
     * Number of API keys associated with this user
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setApiKeysCount($value)
    {
        return $this->attributes['api_keys_count'] = $value;
    }
    /**
     * Scheduled Date/Time at which user will be deactivated
     *
     * @return string|null
     */
    public function getAuthenticateUntil()
    {
        return isset($this->attributes['authenticate_until']) ? $this->attributes['authenticate_until'] : null;
    }

    /**
     * Scheduled Date/Time at which user will be deactivated
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthenticateUntil($value)
    {
        return $this->attributes['authenticate_until'] = $value;
    }
    /**
     * How is this user authenticated?
     *
     * @return string|null
     */
    public function getAuthenticationMethod()
    {
        return isset($this->attributes['authentication_method']) ? $this->attributes['authentication_method'] : null;
    }

    /**
     * How is this user authenticated?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthenticationMethod($value)
    {
        return $this->attributes['authentication_method'] = $value;
    }
    /**
     * URL holding the user's avatar
     *
     * @return string|null
     */
    public function getAvatarUrl()
    {
        return isset($this->attributes['avatar_url']) ? $this->attributes['avatar_url'] : null;
    }

    /**
     * URL holding the user's avatar
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAvatarUrl($value)
    {
        return $this->attributes['avatar_url'] = $value;
    }
    /**
     * Is this a billable user record?
     *
     * @return bool|null
     */
    public function getBillable()
    {
        return isset($this->attributes['billable']) ? $this->attributes['billable'] : null;
    }

    /**
     * Is this a billable user record?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBillable($value)
    {
        return $this->attributes['billable'] = $value;
    }
    /**
     * Allow this user to perform operations on the account, payments, and invoices?
     *
     * @return bool|null
     */
    public function getBillingPermission()
    {
        return isset($this->attributes['billing_permission']) ? $this->attributes['billing_permission'] : null;
    }

    /**
     * Allow this user to perform operations on the account, payments, and invoices?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBillingPermission($value)
    {
        return $this->attributes['billing_permission'] = $value;
    }
    /**
     * Allow this user to skip site-wide IP blacklists?
     *
     * @return bool|null
     */
    public function getBypassSiteAllowedIps()
    {
        return isset($this->attributes['bypass_site_allowed_ips']) ? $this->attributes['bypass_site_allowed_ips'] : null;
    }

    /**
     * Allow this user to skip site-wide IP blacklists?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBypassSiteAllowedIps($value)
    {
        return $this->attributes['bypass_site_allowed_ips'] = $value;
    }
    /**
     * Exempt this user from user lifecycle rules?
     *
     * @return bool|null
     */
    public function getBypassUserLifecycleRules()
    {
        return isset($this->attributes['bypass_user_lifecycle_rules']) ? $this->attributes['bypass_user_lifecycle_rules'] : null;
    }

    /**
     * Exempt this user from user lifecycle rules?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBypassUserLifecycleRules($value)
    {
        return $this->attributes['bypass_user_lifecycle_rules'] = $value;
    }
    /**
     * When this user was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Can the user connect with WebDAV?
     *
     * @return bool|null
     */
    public function getDavPermission()
    {
        return isset($this->attributes['dav_permission']) ? $this->attributes['dav_permission'] : null;
    }

    /**
     * Can the user connect with WebDAV?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDavPermission($value)
    {
        return $this->attributes['dav_permission'] = $value;
    }
    /**
     * Is user disabled? Disabled users cannot log in, and do not count for billing purposes. Users can be automatically disabled after an inactivity period via a Site setting or schedule to be deactivated after specific date.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * Is user disabled? Disabled users cannot log in, and do not count for billing purposes. Users can be automatically disabled after an inactivity period via a Site setting or schedule to be deactivated after specific date.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * Computed property that returns true if user disabled or expired or inactive.
     *
     * @return bool|null
     */
    public function getDisabledExpiredOrInactive()
    {
        return isset($this->attributes['disabled_expired_or_inactive']) ? $this->attributes['disabled_expired_or_inactive'] : null;
    }

    /**
     * Computed property that returns true if user disabled or expired or inactive.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabledExpiredOrInactive($value)
    {
        return $this->attributes['disabled_expired_or_inactive'] = $value;
    }
    /**
     * AI Assistant Personality ID assigned directly to this user, if any.
     *
     * @return int|null
     */
    public function getAiAssistantPersonalityId()
    {
        return isset($this->attributes['ai_assistant_personality_id']) ? $this->attributes['ai_assistant_personality_id'] : null;
    }

    /**
     * AI Assistant Personality ID assigned directly to this user, if any.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAiAssistantPersonalityId($value)
    {
        return $this->attributes['ai_assistant_personality_id'] = $value;
    }
    /**
     * Desktop Configuration Profile ID assigned directly to this user, if any.
     *
     * @return int|null
     */
    public function getDesktopConfigurationProfileId()
    {
        return isset($this->attributes['desktop_configuration_profile_id']) ? $this->attributes['desktop_configuration_profile_id'] : null;
    }

    /**
     * Desktop Configuration Profile ID assigned directly to this user, if any.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDesktopConfigurationProfileId($value)
    {
        return $this->attributes['desktop_configuration_profile_id'] = $value;
    }
    /**
     * Integration Centric Profile ID assigned directly to this user, if any.
     *
     * @return int|null
     */
    public function getIntegrationCentricProfileId()
    {
        return isset($this->attributes['integration_centric_profile_id']) ? $this->attributes['integration_centric_profile_id'] : null;
    }

    /**
     * Integration Centric Profile ID assigned directly to this user, if any.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setIntegrationCentricProfileId($value)
    {
        return $this->attributes['integration_centric_profile_id'] = $value;
    }
    /**
     * User email address
     *
     * @return string|null
     */
    public function getEmail()
    {
        return isset($this->attributes['email']) ? $this->attributes['email'] : null;
    }

    /**
     * User email address
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEmail($value)
    {
        return $this->attributes['email'] = $value;
    }
    /**
     * File system layout
     *
     * @return string|null
     */
    public function getFilesystemLayout()
    {
        return isset($this->attributes['filesystem_layout']) ? $this->attributes['filesystem_layout'] : null;
    }

    /**
     * File system layout
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFilesystemLayout($value)
    {
        return $this->attributes['filesystem_layout'] = $value;
    }
    /**
     * User's first login time
     *
     * @return string|null
     */
    public function getFirstLoginAt()
    {
        return isset($this->attributes['first_login_at']) ? $this->attributes['first_login_at'] : null;
    }

    /**
     * User's first login time
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFirstLoginAt($value)
    {
        return $this->attributes['first_login_at'] = $value;
    }
    /**
     * Can the user access with FTP/FTPS?
     *
     * @return bool|null
     */
    public function getFtpPermission()
    {
        return isset($this->attributes['ftp_permission']) ? $this->attributes['ftp_permission'] : null;
    }

    /**
     * Can the user access with FTP/FTPS?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFtpPermission($value)
    {
        return $this->attributes['ftp_permission'] = $value;
    }
    /**
     * Comma-separated list of group IDs of which this user is a member
     *
     * @return string|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * Comma-separated list of group IDs of which this user is a member
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    /**
     * Text to display to the user in the header of the UI
     *
     * @return string|null
     */
    public function getHeaderText()
    {
        return isset($this->attributes['header_text']) ? $this->attributes['header_text'] : null;
    }

    /**
     * Text to display to the user in the header of the UI
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHeaderText($value)
    {
        return $this->attributes['header_text'] = $value;
    }
    /**
     * Preferred language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        return isset($this->attributes['language']) ? $this->attributes['language'] : null;
    }

    /**
     * Preferred language
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLanguage($value)
    {
        return $this->attributes['language'] = $value;
    }
    /**
     * User's most recent login time via any protocol
     *
     * @return string|null
     */
    public function getLastLoginAt()
    {
        return isset($this->attributes['last_login_at']) ? $this->attributes['last_login_at'] : null;
    }

    /**
     * User's most recent login time via any protocol
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastLoginAt($value)
    {
        return $this->attributes['last_login_at'] = $value;
    }
    /**
     * User's most recent login time via web
     *
     * @return string|null
     */
    public function getLastWebLoginAt()
    {
        return isset($this->attributes['last_web_login_at']) ? $this->attributes['last_web_login_at'] : null;
    }

    /**
     * User's most recent login time via web
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastWebLoginAt($value)
    {
        return $this->attributes['last_web_login_at'] = $value;
    }
    /**
     * User's most recent login time via FTP
     *
     * @return string|null
     */
    public function getLastFtpLoginAt()
    {
        return isset($this->attributes['last_ftp_login_at']) ? $this->attributes['last_ftp_login_at'] : null;
    }

    /**
     * User's most recent login time via FTP
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastFtpLoginAt($value)
    {
        return $this->attributes['last_ftp_login_at'] = $value;
    }
    /**
     * User's most recent login time via SFTP
     *
     * @return string|null
     */
    public function getLastSftpLoginAt()
    {
        return isset($this->attributes['last_sftp_login_at']) ? $this->attributes['last_sftp_login_at'] : null;
    }

    /**
     * User's most recent login time via SFTP
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastSftpLoginAt($value)
    {
        return $this->attributes['last_sftp_login_at'] = $value;
    }
    /**
     * User's most recent login time via WebDAV
     *
     * @return string|null
     */
    public function getLastDavLoginAt()
    {
        return isset($this->attributes['last_dav_login_at']) ? $this->attributes['last_dav_login_at'] : null;
    }

    /**
     * User's most recent login time via WebDAV
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastDavLoginAt($value)
    {
        return $this->attributes['last_dav_login_at'] = $value;
    }
    /**
     * User's most recent login time via Desktop app
     *
     * @return string|null
     */
    public function getLastDesktopLoginAt()
    {
        return isset($this->attributes['last_desktop_login_at']) ? $this->attributes['last_desktop_login_at'] : null;
    }

    /**
     * User's most recent login time via Desktop app
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastDesktopLoginAt($value)
    {
        return $this->attributes['last_desktop_login_at'] = $value;
    }
    /**
     * User's most recent login time via Rest API
     *
     * @return string|null
     */
    public function getLastRestapiLoginAt()
    {
        return isset($this->attributes['last_restapi_login_at']) ? $this->attributes['last_restapi_login_at'] : null;
    }

    /**
     * User's most recent login time via Rest API
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastRestapiLoginAt($value)
    {
        return $this->attributes['last_restapi_login_at'] = $value;
    }
    /**
     * User's most recent API use time
     *
     * @return string|null
     */
    public function getLastApiUseAt()
    {
        return isset($this->attributes['last_api_use_at']) ? $this->attributes['last_api_use_at'] : null;
    }

    /**
     * User's most recent API use time
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastApiUseAt($value)
    {
        return $this->attributes['last_api_use_at'] = $value;
    }
    /**
     * User's most recent activity time, which is the latest of most recent login, most recent API use, enablement, or creation
     *
     * @return string|null
     */
    public function getLastActiveAt()
    {
        return isset($this->attributes['last_active_at']) ? $this->attributes['last_active_at'] : null;
    }

    /**
     * User's most recent activity time, which is the latest of most recent login, most recent API use, enablement, or creation
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastActiveAt($value)
    {
        return $this->attributes['last_active_at'] = $value;
    }
    /**
     * The most recent protocol and cipher used
     *
     * @return string|null
     */
    public function getLastProtocolCipher()
    {
        return isset($this->attributes['last_protocol_cipher']) ? $this->attributes['last_protocol_cipher'] : null;
    }

    /**
     * The most recent protocol and cipher used
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastProtocolCipher($value)
    {
        return $this->attributes['last_protocol_cipher'] = $value;
    }
    /**
     * Time in the future that the user will no longer be locked out if applicable
     *
     * @return string|null
     */
    public function getLockoutExpires()
    {
        return isset($this->attributes['lockout_expires']) ? $this->attributes['lockout_expires'] : null;
    }

    /**
     * Time in the future that the user will no longer be locked out if applicable
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLockoutExpires($value)
    {
        return $this->attributes['lockout_expires'] = $value;
    }
    /**
     * User's full name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * User's full name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * User's company
     *
     * @return string|null
     */
    public function getCompany()
    {
        return isset($this->attributes['company']) ? $this->attributes['company'] : null;
    }

    /**
     * User's company
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCompany($value)
    {
        return $this->attributes['company'] = $value;
    }
    /**
     * Any internal notes on the user
     *
     * @return string|null
     */
    public function getNotes()
    {
        return isset($this->attributes['notes']) ? $this->attributes['notes'] : null;
    }

    /**
     * Any internal notes on the user
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    /**
     * Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
     *
     * @return int|null
     */
    public function getNotificationDailySendTime()
    {
        return isset($this->attributes['notification_daily_send_time']) ? $this->attributes['notification_daily_send_time'] : null;
    }

    /**
     * Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setNotificationDailySendTime($value)
    {
        return $this->attributes['notification_daily_send_time'] = $value;
    }
    /**
     * Enable integration with Office for the web?
     *
     * @return bool|null
     */
    public function getOfficeIntegrationEnabled()
    {
        return isset($this->attributes['office_integration_enabled']) ? $this->attributes['office_integration_enabled'] : null;
    }

    /**
     * Enable integration with Office for the web?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setOfficeIntegrationEnabled($value)
    {
        return $this->attributes['office_integration_enabled'] = $value;
    }
    /**
     * Is this user a Partner administrator?
     *
     * @return bool|null
     */
    public function getPartnerAdmin()
    {
        return isset($this->attributes['partner_admin']) ? $this->attributes['partner_admin'] : null;
    }

    /**
     * Is this user a Partner administrator?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPartnerAdmin($value)
    {
        return $this->attributes['partner_admin'] = $value;
    }
    /**
     * Partner ID if this user belongs to a Partner
     *
     * @return int|null
     */
    public function getPartnerId()
    {
        return isset($this->attributes['partner_id']) ? $this->attributes['partner_id'] : null;
    }

    /**
     * Partner ID if this user belongs to a Partner
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPartnerId($value)
    {
        return $this->attributes['partner_id'] = $value;
    }
    /**
     * Name of the Partner if this user belongs to a Partner
     *
     * @return string|null
     */
    public function getPartnerName()
    {
        return isset($this->attributes['partner_name']) ? $this->attributes['partner_name'] : null;
    }

    /**
     * Name of the Partner if this user belongs to a Partner
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartnerName($value)
    {
        return $this->attributes['partner_name'] = $value;
    }
    /**
     * Last time the user's password was set
     *
     * @return string|null
     */
    public function getPasswordSetAt()
    {
        return isset($this->attributes['password_set_at']) ? $this->attributes['password_set_at'] : null;
    }

    /**
     * Last time the user's password was set
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPasswordSetAt($value)
    {
        return $this->attributes['password_set_at'] = $value;
    }
    /**
     * Number of days to allow user to use the same password
     *
     * @return int|null
     */
    public function getPasswordValidityDays()
    {
        return isset($this->attributes['password_validity_days']) ? $this->attributes['password_validity_days'] : null;
    }

    /**
     * Number of days to allow user to use the same password
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPasswordValidityDays($value)
    {
        return $this->attributes['password_validity_days'] = $value;
    }
    /**
     * Primary group ID for Group Admin scoping
     *
     * @return int|null
     */
    public function getPrimaryGroupId()
    {
        return isset($this->attributes['primary_group_id']) ? $this->attributes['primary_group_id'] : null;
    }

    /**
     * Primary group ID for Group Admin scoping
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPrimaryGroupId($value)
    {
        return $this->attributes['primary_group_id'] = $value;
    }
    /**
     * Number of public keys associated with this user
     *
     * @return int|null
     */
    public function getPublicKeysCount()
    {
        return isset($this->attributes['public_keys_count']) ? $this->attributes['public_keys_count'] : null;
    }

    /**
     * Number of public keys associated with this user
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPublicKeysCount($value)
    {
        return $this->attributes['public_keys_count'] = $value;
    }
    /**
     * Deprecated. Use notify_on_all_site_warnings and granular failure notification preferences instead.
     *
     * @return bool|null
     */
    public function getReceiveAdminAlerts()
    {
        return isset($this->attributes['receive_admin_alerts']) ? $this->attributes['receive_admin_alerts'] : null;
    }

    /**
     * Deprecated. Use notify_on_all_site_warnings and granular failure notification preferences instead.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setReceiveAdminAlerts($value)
    {
        return $this->attributes['receive_admin_alerts'] = $value;
    }
    /**
     * Should the user receive site warnings via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllSiteWarnings()
    {
        return isset($this->attributes['notify_on_all_site_warnings']) ? $this->attributes['notify_on_all_site_warnings'] : null;
    }

    /**
     * Should the user receive site warnings via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllSiteWarnings($value)
    {
        return $this->attributes['notify_on_all_site_warnings'] = $value;
    }
    /**
     * Should the user receive sso/scim/ldap configuration/sync failures via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllSsoFailures()
    {
        return isset($this->attributes['notify_on_all_sso_failures']) ? $this->attributes['notify_on_all_sso_failures'] : null;
    }

    /**
     * Should the user receive sso/scim/ldap configuration/sync failures via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllSsoFailures($value)
    {
        return $this->attributes['notify_on_all_sso_failures'] = $value;
    }
    /**
     * Should the user receive user security events via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllUserSecurityEvents()
    {
        return isset($this->attributes['notify_on_all_user_security_events']) ? $this->attributes['notify_on_all_user_security_events'] : null;
    }

    /**
     * Should the user receive user security events via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllUserSecurityEvents($value)
    {
        return $this->attributes['notify_on_all_user_security_events'] = $value;
    }
    /**
     * Should the user receive pending work failures via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllPendingWorkFailures()
    {
        return isset($this->attributes['notify_on_all_pending_work_failures']) ? $this->attributes['notify_on_all_pending_work_failures'] : null;
    }

    /**
     * Should the user receive pending work failures via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllPendingWorkFailures($value)
    {
        return $this->attributes['notify_on_all_pending_work_failures'] = $value;
    }
    /**
     * Should the user receive siem failures via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllSiemHttpDestinationFailures()
    {
        return isset($this->attributes['notify_on_all_siem_http_destination_failures']) ? $this->attributes['notify_on_all_siem_http_destination_failures'] : null;
    }

    /**
     * Should the user receive siem failures via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllSiemHttpDestinationFailures($value)
    {
        return $this->attributes['notify_on_all_siem_http_destination_failures'] = $value;
    }
    /**
     * Should the user receive sync failures via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllSyncFailures()
    {
        return isset($this->attributes['notify_on_all_sync_failures']) ? $this->attributes['notify_on_all_sync_failures'] : null;
    }

    /**
     * Should the user receive sync failures via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllSyncFailures($value)
    {
        return $this->attributes['notify_on_all_sync_failures'] = $value;
    }
    /**
     * Should the user receive automation failures via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllAutomationFailures()
    {
        return isset($this->attributes['notify_on_all_automation_failures']) ? $this->attributes['notify_on_all_automation_failures'] : null;
    }

    /**
     * Should the user receive automation failures via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllAutomationFailures($value)
    {
        return $this->attributes['notify_on_all_automation_failures'] = $value;
    }
    /**
     * Should the user receive expectation failures and misses via email?
     *
     * @return bool|null
     */
    public function getNotifyOnAllExpectationFailures()
    {
        return isset($this->attributes['notify_on_all_expectation_failures']) ? $this->attributes['notify_on_all_expectation_failures'] : null;
    }

    /**
     * Should the user receive expectation failures and misses via email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnAllExpectationFailures($value)
    {
        return $this->attributes['notify_on_all_expectation_failures'] = $value;
    }
    /**
     * 2FA required setting. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
     *
     * @return string|null
     */
    public function getRequire2fa()
    {
        return isset($this->attributes['require_2fa']) ? $this->attributes['require_2fa'] : null;
    }

    /**
     * 2FA required setting. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRequire2fa($value)
    {
        return $this->attributes['require_2fa'] = $value;
    }
    /**
     * Require user to login by specified date otherwise it will be disabled.
     *
     * @return string|null
     */
    public function getRequireLoginBy()
    {
        return isset($this->attributes['require_login_by']) ? $this->attributes['require_login_by'] : null;
    }

    /**
     * Require user to login by specified date otherwise it will be disabled.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRequireLoginBy($value)
    {
        return $this->attributes['require_login_by'] = $value;
    }
    /**
     * Is 2fa active for the user?
     *
     * @return bool|null
     */
    public function getActive2fa()
    {
        return isset($this->attributes['active_2fa']) ? $this->attributes['active_2fa'] : null;
    }

    /**
     * Is 2fa active for the user?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setActive2fa($value)
    {
        return $this->attributes['active_2fa'] = $value;
    }
    /**
     * Is a password change required upon next user login?
     *
     * @return bool|null
     */
    public function getRequirePasswordChange()
    {
        return isset($this->attributes['require_password_change']) ? $this->attributes['require_password_change'] : null;
    }

    /**
     * Is a password change required upon next user login?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRequirePasswordChange($value)
    {
        return $this->attributes['require_password_change'] = $value;
    }
    /**
     * Is user's password expired?
     *
     * @return bool|null
     */
    public function getPasswordExpired()
    {
        return isset($this->attributes['password_expired']) ? $this->attributes['password_expired'] : null;
    }

    /**
     * Is user's password expired?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPasswordExpired($value)
    {
        return $this->attributes['password_expired'] = $value;
    }
    /**
     * ID of the internal Group responsible for this Partner User, overriding the Partner default.
     *
     * @return int|null
     */
    public function getResponsibleGroupId()
    {
        return isset($this->attributes['responsible_group_id']) ? $this->attributes['responsible_group_id'] : null;
    }

    /**
     * ID of the internal Group responsible for this Partner User, overriding the Partner default.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setResponsibleGroupId($value)
    {
        return $this->attributes['responsible_group_id'] = $value;
    }
    /**
     * ID of the internal User responsible for this Partner User, overriding the Partner default.
     *
     * @return int|null
     */
    public function getResponsibleUserId()
    {
        return isset($this->attributes['responsible_user_id']) ? $this->attributes['responsible_user_id'] : null;
    }

    /**
     * ID of the internal User responsible for this Partner User, overriding the Partner default.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setResponsibleUserId($value)
    {
        return $this->attributes['responsible_user_id'] = $value;
    }
    /**
     * Is the user an allowed to view all (non-billing) site configuration for this site?
     *
     * @return bool|null
     */
    public function getReadonlySiteAdmin()
    {
        return isset($this->attributes['readonly_site_admin']) ? $this->attributes['readonly_site_admin'] : null;
    }

    /**
     * Is the user an allowed to view all (non-billing) site configuration for this site?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setReadonlySiteAdmin($value)
    {
        return $this->attributes['readonly_site_admin'] = $value;
    }
    /**
     * Can this user access the Web app, Desktop app, SDKs, or REST API?  (All of these tools use the API internally, so this is one unified permission set.)
     *
     * @return bool|null
     */
    public function getRestapiPermission()
    {
        return isset($this->attributes['restapi_permission']) ? $this->attributes['restapi_permission'] : null;
    }

    /**
     * Can this user access the Web app, Desktop app, SDKs, or REST API?  (All of these tools use the API internally, so this is one unified permission set.)
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRestapiPermission($value)
    {
        return $this->attributes['restapi_permission'] = $value;
    }
    /**
     * Can the user access the S3-compatible endpoint? Defaults to true.
     *
     * @return bool|null
     */
    public function getS3CompatibleEndpointPermission()
    {
        return isset($this->attributes['s3_compatible_endpoint_permission']) ? $this->attributes['s3_compatible_endpoint_permission'] : null;
    }

    /**
     * Can the user access the S3-compatible endpoint? Defaults to true.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setS3CompatibleEndpointPermission($value)
    {
        return $this->attributes['s3_compatible_endpoint_permission'] = $value;
    }
    /**
     * Does this user manage it's own credentials or is it a shared/bot user?
     *
     * @return bool|null
     */
    public function getSelfManaged()
    {
        return isset($this->attributes['self_managed']) ? $this->attributes['self_managed'] : null;
    }

    /**
     * Does this user manage it's own credentials or is it a shared/bot user?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSelfManaged($value)
    {
        return $this->attributes['self_managed'] = $value;
    }
    /**
     * Can the user access with SFTP?
     *
     * @return bool|null
     */
    public function getSftpPermission()
    {
        return isset($this->attributes['sftp_permission']) ? $this->attributes['sftp_permission'] : null;
    }

    /**
     * Can the user access with SFTP?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSftpPermission($value)
    {
        return $this->attributes['sftp_permission'] = $value;
    }
    /**
     * Is the user an administrator for this site?
     *
     * @return bool|null
     */
    public function getSiteAdmin()
    {
        return isset($this->attributes['site_admin']) ? $this->attributes['site_admin'] : null;
    }

    /**
     * Is the user an administrator for this site?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSiteAdmin($value)
    {
        return $this->attributes['site_admin'] = $value;
    }
    /**
     * Whether the user is an administrator of their own Custom Workspace. Does not reflect administration granted through Permissions.
     *
     * @return bool|null
     */
    public function getWorkspaceAdmin()
    {
        return isset($this->attributes['workspace_admin']) ? $this->attributes['workspace_admin'] : null;
    }

    /**
     * Whether the user is an administrator of their own Custom Workspace. Does not reflect administration granted through Permissions.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setWorkspaceAdmin($value)
    {
        return $this->attributes['workspace_admin'] = $value;
    }
    /**
     * Site ID
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }

    /**
     * Site ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    /**
     * ID of the Workspace the user belongs to. 0 is the Default Workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * ID of the Workspace the user belongs to. 0 is the Default Workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Workspace ID the user should land in by default when more than one Workspace is available.
     *
     * @return int|null
     */
    public function getDefaultWorkspaceId()
    {
        return isset($this->attributes['default_workspace_id']) ? $this->attributes['default_workspace_id'] : null;
    }

    /**
     * Workspace ID the user should land in by default when more than one Workspace is available.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDefaultWorkspaceId($value)
    {
        return $this->attributes['default_workspace_id'] = $value;
    }
    /**
     * Skip Welcome page in the UI?
     *
     * @return bool|null
     */
    public function getSkipWelcomeScreen()
    {
        return isset($this->attributes['skip_welcome_screen']) ? $this->attributes['skip_welcome_screen'] : null;
    }

    /**
     * Skip Welcome page in the UI?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipWelcomeScreen($value)
    {
        return $this->attributes['skip_welcome_screen'] = $value;
    }
    /**
     * FTP TLS session reuse requirement: use_system_setting, always_require, or never_require
     *
     * @return string|null
     */
    public function getFtpTlsSessionReuseRequired()
    {
        return isset($this->attributes['ftp_tls_session_reuse_required']) ? $this->attributes['ftp_tls_session_reuse_required'] : null;
    }

    /**
     * FTP TLS session reuse requirement: use_system_setting, always_require, or never_require
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFtpTlsSessionReuseRequired($value)
    {
        return $this->attributes['ftp_tls_session_reuse_required'] = $value;
    }
    /**
     * SSL required setting
     *
     * @return string|null
     */
    public function getSslRequired()
    {
        return isset($this->attributes['ssl_required']) ? $this->attributes['ssl_required'] : null;
    }

    /**
     * SSL required setting
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSslRequired($value)
    {
        return $this->attributes['ssl_required'] = $value;
    }
    /**
     * SSO (Single Sign On) strategy ID for the user, if applicable.
     *
     * @return int|null
     */
    public function getSsoStrategyId()
    {
        return isset($this->attributes['sso_strategy_id']) ? $this->attributes['sso_strategy_id'] : null;
    }

    /**
     * SSO (Single Sign On) strategy ID for the user, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSsoStrategyId($value)
    {
        return $this->attributes['sso_strategy_id'] = $value;
    }
    /**
     * Is this user managed by a SsoStrategy?
     *
     * @return bool|null
     */
    public function getExternallyManaged()
    {
        return isset($this->attributes['externally_managed']) ? $this->attributes['externally_managed'] : null;
    }

    /**
     * Is this user managed by a SsoStrategy?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setExternallyManaged($value)
    {
        return $this->attributes['externally_managed'] = $value;
    }
    /**
     * Comma-separated list of Tags for this user. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @return string|null
     */
    public function getTags()
    {
        return isset($this->attributes['tags']) ? $this->attributes['tags'] : null;
    }

    /**
     * Comma-separated list of Tags for this user. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTags($value)
    {
        return $this->attributes['tags'] = $value;
    }
    /**
     * User time zone
     *
     * @return string|null
     */
    public function getTimeZone()
    {
        return isset($this->attributes['time_zone']) ? $this->attributes['time_zone'] : null;
    }

    /**
     * User time zone
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTimeZone($value)
    {
        return $this->attributes['time_zone'] = $value;
    }
    /**
     * Type(s) of 2FA methods in use, for programmatic use.  Will be either `sms`, `totp`, `webauthn`, `yubi`, `email`, or multiple values sorted alphabetically and joined by an underscore.  Does not specify whether user has more than one of a given method.
     *
     * @return string|null
     */
    public function getTypeOf2fa()
    {
        return isset($this->attributes['type_of_2fa']) ? $this->attributes['type_of_2fa'] : null;
    }

    /**
     * Type(s) of 2FA methods in use, for programmatic use.  Will be either `sms`, `totp`, `webauthn`, `yubi`, `email`, or multiple values sorted alphabetically and joined by an underscore.  Does not specify whether user has more than one of a given method.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTypeOf2fa($value)
    {
        return $this->attributes['type_of_2fa'] = $value;
    }
    /**
     * Type(s) of 2FA methods in use, formatted for displaying in the UI.  Unlike `type_of_2fa`, this value will make clear when a user has more than 1 of the same type of method.
     *
     * @return string|null
     */
    public function getTypeOf2faForDisplay()
    {
        return isset($this->attributes['type_of_2fa_for_display']) ? $this->attributes['type_of_2fa_for_display'] : null;
    }

    /**
     * Type(s) of 2FA methods in use, formatted for displaying in the UI.  Unlike `type_of_2fa`, this value will make clear when a user has more than 1 of the same type of method.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTypeOf2faForDisplay($value)
    {
        return $this->attributes['type_of_2fa_for_display'] = $value;
    }
    /**
     * If filesystem layout is user_root, this path is the root path the user is fixed to for all interfaces. If the filesystem layout is site_root or partner_root, this acts as a root folder only for FTP and SFTP (SFTP applicability also requires a site-wide setting to be set). For partner_root layout, this path is relative to the Partner root folder for all callers and blank opts out of an additional protocol root. In this situation, this path is not applied to the API, Desktop, or Web interface.
     *
     * @return string|null
     */
    public function getUserRoot()
    {
        return isset($this->attributes['user_root']) ? $this->attributes['user_root'] : null;
    }

    /**
     * If filesystem layout is user_root, this path is the root path the user is fixed to for all interfaces. If the filesystem layout is site_root or partner_root, this acts as a root folder only for FTP and SFTP (SFTP applicability also requires a site-wide setting to be set). For partner_root layout, this path is relative to the Partner root folder for all callers and blank opts out of an additional protocol root. In this situation, this path is not applied to the API, Desktop, or Web interface.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserRoot($value)
    {
        return $this->attributes['user_root'] = $value;
    }
    /**
     * Home folder for FTP/SFTP. For users with the partner_root filesystem layout, this path is relative to the Partner root folder. In all other cases, it is an absolute path. Only applies to FTP and SFTP, and not any other interface.
     *
     * @return string|null
     */
    public function getUserHome()
    {
        return isset($this->attributes['user_home']) ? $this->attributes['user_home'] : null;
    }

    /**
     * Home folder for FTP/SFTP. For users with the partner_root filesystem layout, this path is relative to the Partner root folder. In all other cases, it is an absolute path. Only applies to FTP and SFTP, and not any other interface.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserHome($value)
    {
        return $this->attributes['user_home'] = $value;
    }
    /**
     * Number of days remaining until password expires
     *
     * @return int|null
     */
    public function getDaysRemainingUntilPasswordExpire()
    {
        return isset($this->attributes['days_remaining_until_password_expire']) ? $this->attributes['days_remaining_until_password_expire'] : null;
    }

    /**
     * Number of days remaining until password expires
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDaysRemainingUntilPasswordExpire($value)
    {
        return $this->attributes['days_remaining_until_password_expire'] = $value;
    }
    /**
     * Password expiration datetime
     *
     * @return string|null
     */
    public function getPasswordExpireAt()
    {
        return isset($this->attributes['password_expire_at']) ? $this->attributes['password_expire_at'] : null;
    }

    /**
     * Password expiration datetime
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPasswordExpireAt($value)
    {
        return $this->attributes['password_expire_at'] = $value;
    }
    /**
     * An image file for your user avatar.
     *
     * @return mixed
     */
    public function getAvatarFile()
    {
        return isset($this->attributes['avatar_file']) ? $this->attributes['avatar_file'] : null;
    }

    /**
     * An image file for your user avatar.
     *
     * @param mixed $value
     * @return mixed The assigned value.
     */
    public function setAvatarFile($value)
    {
        return $this->attributes['avatar_file'] = $value;
    }
    /**
     * If true, the avatar will be deleted.
     *
     * @return bool|null
     */
    public function getAvatarDelete()
    {
        return isset($this->attributes['avatar_delete']) ? $this->attributes['avatar_delete'] : null;
    }

    /**
     * If true, the avatar will be deleted.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAvatarDelete($value)
    {
        return $this->attributes['avatar_delete'] = $value;
    }
    /**
     * Used for changing a password on an existing user.
     *
     * @return string|null
     */
    public function getChangePassword()
    {
        return isset($this->attributes['change_password']) ? $this->attributes['change_password'] : null;
    }

    /**
     * Used for changing a password on an existing user.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setChangePassword($value)
    {
        return $this->attributes['change_password'] = $value;
    }
    /**
     * Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
     *
     * @return string|null
     */
    public function getChangePasswordConfirmation()
    {
        return isset($this->attributes['change_password_confirmation']) ? $this->attributes['change_password_confirmation'] : null;
    }

    /**
     * Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setChangePasswordConfirmation($value)
    {
        return $this->attributes['change_password_confirmation'] = $value;
    }
    /**
     * Permission to grant on the User Root upon creation, or on the Workspace root when the User Root is blank. For the Default Workspace, a blank User Root uses the site root. `admin` on a Custom Workspace's root grants Workspace Administrator access; on a subfolder it grants Folder Admin access. Can be blank or `admin`, `full`, `read`, `write`, `list`, `read+write`, `list+write`, or `history`.
     *
     * @return string|null
     */
    public function getGrantPermission()
    {
        return isset($this->attributes['grant_permission']) ? $this->attributes['grant_permission'] : null;
    }

    /**
     * Permission to grant on the User Root upon creation, or on the Workspace root when the User Root is blank. For the Default Workspace, a blank User Root uses the site root. `admin` on a Custom Workspace's root grants Workspace Administrator access; on a subfolder it grants Folder Admin access. Can be blank or `admin`, `full`, `read`, `write`, `list`, `read+write`, `list+write`, or `history`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGrantPermission($value)
    {
        return $this->attributes['grant_permission'] = $value;
    }
    /**
     * Group ID to associate this user with.
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return isset($this->attributes['group_id']) ? $this->attributes['group_id'] : null;
    }

    /**
     * Group ID to associate this user with.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setGroupId($value)
    {
        return $this->attributes['group_id'] = $value;
    }
    /**
     * Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash methods are MD5, SHA1, and SHA256.
     *
     * @return string|null
     */
    public function getImportedPasswordHash()
    {
        return isset($this->attributes['imported_password_hash']) ? $this->attributes['imported_password_hash'] : null;
    }

    /**
     * Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash methods are MD5, SHA1, and SHA256.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setImportedPasswordHash($value)
    {
        return $this->attributes['imported_password_hash'] = $value;
    }
    /**
     * User password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return isset($this->attributes['password']) ? $this->attributes['password'] : null;
    }

    /**
     * User password.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    /**
     * Optional, but if provided, we will ensure that it matches the value sent in `password`.
     *
     * @return string|null
     */
    public function getPasswordConfirmation()
    {
        return isset($this->attributes['password_confirmation']) ? $this->attributes['password_confirmation'] : null;
    }

    /**
     * Optional, but if provided, we will ensure that it matches the value sent in `password`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPasswordConfirmation($value)
    {
        return $this->attributes['password_confirmation'] = $value;
    }
    /**
     * Signifies that the user has read all the announcements in the UI.
     *
     * @return bool|null
     */
    public function getAnnouncementsRead()
    {
        return isset($this->attributes['announcements_read']) ? $this->attributes['announcements_read'] : null;
    }

    /**
     * Signifies that the user has read all the announcements in the UI.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAnnouncementsRead($value)
    {
        return $this->attributes['announcements_read'] = $value;
    }
    /**
     * If true when changing authentication_method from `password` to `sso`, remove all two-factor methods. Ignored in all other cases.
     *
     * @return bool|null
     */
    public function getClear2fa()
    {
        return isset($this->attributes['clear_2fa']) ? $this->attributes['clear_2fa'] : null;
    }

    /**
     * If true when changing authentication_method from `password` to `sso`, remove all two-factor methods. Ignored in all other cases.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setClear2fa($value)
    {
        return $this->attributes['clear_2fa'] = $value;
    }
    /**
     * Required when assigning a Partner to an existing non-Partner user. If true, convert the user by assigning the partner_id provided.
     *
     * @return bool|null
     */
    public function getConvertToPartnerUser()
    {
        return isset($this->attributes['convert_to_partner_user']) ? $this->attributes['convert_to_partner_user'] : null;
    }

    /**
     * Required when assigning a Partner to an existing non-Partner user. If true, convert the user by assigning the partner_id provided.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setConvertToPartnerUser($value)
    {
        return $this->attributes['convert_to_partner_user'] = $value;
    }

    /**
     * Unlock user who has been locked out due to failed logins
     *
     * POST /users/{id}/unlock
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function unlock($params = [])
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

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '/unlock', 'POST', $params, $this->options);
        return;
    }

    /**
     * Resend user welcome email
     *
     * POST /users/{id}/resend_welcome_email
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function resendWelcomeEmail($params = [])
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

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '/resend_welcome_email', 'POST', $params, $this->options);
        return;
    }

    /**
     * Trigger 2FA Reset process for user who has lost access to their existing 2FA methods
     *
     * POST /users/{id}/2fa/reset
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function user2faReset($params = [])
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

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '/2fa/reset', 'POST', $params, $this->options);
        return;
    }

    /**
     * Update User
     *
     * PATCH /users/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     *     - `avatar_file` (mixed, optional): An image file for your user avatar.
     *     - `avatar_delete` (bool, optional): If true, the avatar will be deleted.
     *     - `change_password` (string, optional): Used for changing a password on an existing user.
     *     - `change_password_confirmation` (string, optional): Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
     *     - `email` (string, optional): User's email.
     *     - `grant_permission` (string, optional): Permission to grant on the User Root upon creation, or on the Workspace root when the User Root is blank. For the Default Workspace, a blank User Root uses the site root. `admin` on a Custom Workspace's root grants Workspace Administrator access; on a subfolder it grants Folder Admin access. Can be blank or `admin`, `full`, `read`, `write`, `list`, `read+write`, `list+write`, or `history`.
     *     - `group_id` (int, optional): Group ID to associate this user with.
     *     - `group_ids` (string, optional): A list of group ids to associate this user with.  Comma delimited.
     *     - `imported_password_hash` (string, optional): Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash methods are MD5, SHA1, and SHA256.
     *     - `password` (string, optional): User password.
     *     - `password_confirmation` (string, optional): Optional, but if provided, we will ensure that it matches the value sent in `password`.
     *     - `announcements_read` (bool, optional): Signifies that the user has read all the announcements in the UI.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned directly to this user, if any.
     *     - `allowed_ips` (string, optional): A list of allowed IPs if applicable.  Newline delimited
     *     - `attachments_permission` (bool, optional): DEPRECATED: If `true`, the user can user create Bundles (aka Share Links). Use the bundle permission instead.
     *     - `authenticate_until` (string, optional): Scheduled Date/Time at which user will be deactivated
     *     - `authentication_method` (string, optional): How is this user authenticated? Allowed values: password, sso, none, email_signup, password_with_imported_hash, password_and_ssh_key.
     *     - `billing_permission` (bool, optional): Allow this user to perform operations on the account, payments, and invoices?
     *     - `bypass_user_lifecycle_rules` (bool, optional): Exempt this user from user lifecycle rules?
     *     - `bypass_site_allowed_ips` (bool, optional): Allow this user to skip site-wide IP blacklists?
     *     - `dav_permission` (bool, optional): Can the user connect with WebDAV?
     *     - `desktop_configuration_profile_id` (int, optional): Desktop Configuration Profile ID assigned directly to this user, if any.
     *     - `default_workspace_id` (int, optional): Workspace ID the user should land in by default when more than one Workspace is available.
     *     - `disabled` (bool, optional): Is user disabled? Disabled users cannot log in, and do not count for billing purposes. Users can be automatically disabled after an inactivity period via a Site setting or schedule to be deactivated after specific date.
     *     - `filesystem_layout` (string, optional): File system layout Allowed values: site_root, user_root, partner_root, integration_centric, workspace_root.
     *     - `ftp_permission` (bool, optional): Can the user access with FTP/FTPS?
     *     - `header_text` (string, optional): Text to display to the user in the header of the UI
     *     - `integration_centric_profile_id` (int, optional): Integration Centric Profile ID assigned directly to this user, if any.
     *     - `language` (string, optional): Preferred language
     *     - `notification_daily_send_time` (int, optional): Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
     *     - `name` (string, optional): User's full name
     *     - `company` (string, optional): User's company
     *     - `notes` (string, optional): Any internal notes on the user
     *     - `office_integration_enabled` (bool, optional): Enable integration with Office for the web?
     *     - `partner_admin` (bool, optional): Is this user a Partner administrator?
     *     - `partner_id` (int, optional): Partner ID if this user belongs to a Partner
     *     - `password_validity_days` (int, optional): Number of days to allow user to use the same password
     *     - `primary_group_id` (int, optional): Primary group ID for Group Admin scoping
     *     - `readonly_site_admin` (bool, optional): Is the user an allowed to view all (non-billing) site configuration for this site?
     *     - `receive_admin_alerts` (bool, optional): Deprecated. Use notify_on_all_site_warnings and granular failure notification preferences instead.
     *     - `notify_on_all_site_warnings` (bool, optional): Should the user receive site warnings via email?
     *     - `notify_on_all_sso_failures` (bool, optional): Should the user receive sso/scim/ldap configuration/sync failures via email?
     *     - `notify_on_all_user_security_events` (bool, optional): Should the user receive user security events via email?
     *     - `notify_on_all_pending_work_failures` (bool, optional): Should the user receive pending work failures via email?
     *     - `notify_on_all_siem_http_destination_failures` (bool, optional): Should the user receive siem failures via email?
     *     - `notify_on_all_sync_failures` (bool, optional): Should the user receive sync failures via email?
     *     - `notify_on_all_automation_failures` (bool, optional): Should the user receive automation failures via email?
     *     - `notify_on_all_expectation_failures` (bool, optional): Should the user receive expectation failures and misses via email?
     *     - `require_login_by` (string, optional): Require user to login by specified date otherwise it will be disabled.
     *     - `require_password_change` (bool, optional): Is a password change required upon next user login?
     *     - `responsible_group_id` (int, optional): ID of the internal Group responsible for this Partner User, overriding the Partner default.
     *     - `responsible_user_id` (int, optional): ID of the internal User responsible for this Partner User, overriding the Partner default.
     *     - `restapi_permission` (bool, optional): Can this user access the Web app, Desktop app, SDKs, or REST API?  (All of these tools use the API internally, so this is one unified permission set.)
     *     - `s3_compatible_endpoint_permission` (bool, optional): Can the user access the S3-compatible endpoint? Defaults to true.
     *     - `self_managed` (bool, optional): Does this user manage it's own credentials or is it a shared/bot user?
     *     - `sftp_permission` (bool, optional): Can the user access with SFTP?
     *     - `site_admin` (bool, optional): Is the user an administrator for this site?
     *     - `skip_welcome_screen` (bool, optional): Skip Welcome page in the UI?
     *     - `ssl_required` (string, optional): SSL required setting Allowed values: use_system_setting, always_require, never_require.
     *     - `ftp_tls_session_reuse_required` (string, optional): FTP TLS session reuse requirement: use_system_setting, always_require, or never_require Allowed values: use_system_setting, always_require, never_require.
     *     - `sso_strategy_id` (int, optional): SSO (Single Sign On) strategy ID for the user, if applicable.
     *     - `require_2fa` (string, optional): 2FA required setting. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed. Allowed values: use_system_setting, always_require, never_require.
     *     - `tags` (string, optional): Comma-separated list of Tags for this user. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `time_zone` (string, optional): User time zone
     *     - `user_root` (string, optional): If filesystem layout is user_root, this path is the root path the user is fixed to for all interfaces. If the filesystem layout is site_root or partner_root, this acts as a root folder only for FTP and SFTP (SFTP applicability also requires a site-wide setting to be set). For partner_root layout, this path is relative to the Partner root folder for all callers and blank opts out of an additional protocol root. In this situation, this path is not applied to the API, Desktop, or Web interface.
     *     - `user_home` (string, optional): Home folder for FTP/SFTP. For users with the partner_root filesystem layout, this path is relative to the Partner root folder. In all other cases, it is an absolute path. Only applies to FTP and SFTP, and not any other interface.
     *     - `workspace_admin` (bool, optional): Whether the user is an administrator of their own Custom Workspace. Does not reflect administration granted through Permissions.
     *     - `username` (string, optional): User's username
     *     - `workspace_id` (int, optional): Workspace ID. Only Site Administrators can change this field. Values supplied by Workspace Administrators, Group Administrators, or other non-Site Administrators using `/user` are ignored.
     *     - `clear_2fa` (bool, optional): If true when changing authentication_method from `password` to `sso`, remove all two-factor methods. Ignored in all other cases.
     *     - `convert_to_partner_user` (bool, optional): Required when assigning a Partner to an existing non-Partner user. If true, convert the user by assigning the partner_id provided.
     * @return \Files\Model\User
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
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

        if (!empty($params['change_password']) && !is_string($params['change_password'])) {
            throw new \Files\Exception\InvalidParameterException('$change_password must be of type string; received ' . gettype(@$params['change_password']));
        }

        if (!empty($params['change_password_confirmation']) && !is_string($params['change_password_confirmation'])) {
            throw new \Files\Exception\InvalidParameterException('$change_password_confirmation must be of type string; received ' . gettype(@$params['change_password_confirmation']));
        }

        if (!empty($params['email']) && !is_string($params['email'])) {
            throw new \Files\Exception\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
        }

        if (!empty($params['grant_permission']) && !is_string($params['grant_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$grant_permission must be of type string; received ' . gettype(@$params['grant_permission']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (!empty($params['imported_password_hash']) && !is_string($params['imported_password_hash'])) {
            throw new \Files\Exception\InvalidParameterException('$imported_password_hash must be of type string; received ' . gettype(@$params['imported_password_hash']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['password_confirmation']) && !is_string($params['password_confirmation'])) {
            throw new \Files\Exception\InvalidParameterException('$password_confirmation must be of type string; received ' . gettype(@$params['password_confirmation']));
        }

        if (!empty($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (!empty($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (!empty($params['authenticate_until']) && !is_string($params['authenticate_until'])) {
            throw new \Files\Exception\InvalidParameterException('$authenticate_until must be of type string; received ' . gettype(@$params['authenticate_until']));
        }

        if (!empty($params['authentication_method']) && !is_string($params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (!empty($params['desktop_configuration_profile_id']) && !is_int($params['desktop_configuration_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_configuration_profile_id must be of type int; received ' . gettype(@$params['desktop_configuration_profile_id']));
        }

        if (!empty($params['default_workspace_id']) && !is_int($params['default_workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$default_workspace_id must be of type int; received ' . gettype(@$params['default_workspace_id']));
        }

        if (!empty($params['filesystem_layout']) && !is_string($params['filesystem_layout'])) {
            throw new \Files\Exception\InvalidParameterException('$filesystem_layout must be of type string; received ' . gettype(@$params['filesystem_layout']));
        }

        if (!empty($params['header_text']) && !is_string($params['header_text'])) {
            throw new \Files\Exception\InvalidParameterException('$header_text must be of type string; received ' . gettype(@$params['header_text']));
        }

        if (!empty($params['integration_centric_profile_id']) && !is_int($params['integration_centric_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_profile_id must be of type int; received ' . gettype(@$params['integration_centric_profile_id']));
        }

        if (!empty($params['language']) && !is_string($params['language'])) {
            throw new \Files\Exception\InvalidParameterException('$language must be of type string; received ' . gettype(@$params['language']));
        }

        if (!empty($params['notification_daily_send_time']) && !is_int($params['notification_daily_send_time'])) {
            throw new \Files\Exception\InvalidParameterException('$notification_daily_send_time must be of type int; received ' . gettype(@$params['notification_daily_send_time']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['company']) && !is_string($params['company'])) {
            throw new \Files\Exception\InvalidParameterException('$company must be of type string; received ' . gettype(@$params['company']));
        }

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['password_validity_days']) && !is_int($params['password_validity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype(@$params['password_validity_days']));
        }

        if (!empty($params['primary_group_id']) && !is_int($params['primary_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$primary_group_id must be of type int; received ' . gettype(@$params['primary_group_id']));
        }

        if (!empty($params['require_login_by']) && !is_string($params['require_login_by'])) {
            throw new \Files\Exception\InvalidParameterException('$require_login_by must be of type string; received ' . gettype(@$params['require_login_by']));
        }

        if (!empty($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (!empty($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (!empty($params['ssl_required']) && !is_string($params['ssl_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_required must be of type string; received ' . gettype(@$params['ssl_required']));
        }

        if (!empty($params['ftp_tls_session_reuse_required']) && !is_string($params['ftp_tls_session_reuse_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_tls_session_reuse_required must be of type string; received ' . gettype(@$params['ftp_tls_session_reuse_required']));
        }

        if (!empty($params['sso_strategy_id']) && !is_int($params['sso_strategy_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sso_strategy_id must be of type int; received ' . gettype(@$params['sso_strategy_id']));
        }

        if (!empty($params['require_2fa']) && !is_string($params['require_2fa'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa must be of type string; received ' . gettype(@$params['require_2fa']));
        }

        if (!empty($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (!empty($params['time_zone']) && !is_string($params['time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$time_zone must be of type string; received ' . gettype(@$params['time_zone']));
        }

        if (!empty($params['user_root']) && !is_string($params['user_root'])) {
            throw new \Files\Exception\InvalidParameterException('$user_root must be of type string; received ' . gettype(@$params['user_root']));
        }

        if (!empty($params['user_home']) && !is_string($params['user_home'])) {
            throw new \Files\Exception\InvalidParameterException('$user_home must be of type string; received ' . gettype(@$params['user_home']));
        }

        if (!empty($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new User((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete User
     *
     * DELETE /users/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     *     - `new_owner_id` (int, optional): Provide a User ID here to transfer ownership of certain resources such as Automations and Share Links (Bundles) to that new user.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
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

        if (!empty($params['new_owner_id']) && !is_int($params['new_owner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$new_owner_id must be of type int; received ' . gettype(@$params['new_owner_id']));
        }

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete User
     *
     * DELETE /users/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User ID.
     *     - `new_owner_id` (int, optional): Provide a User ID here to transfer ownership of certain resources such as Automations and Share Links (Bundles) to that new user.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        if (@$this->attributes['id']) {
            $new_obj = $this->update($this->attributes);
            $this->attributes = $new_obj->attributes;
            return true;
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Users
     *
     * GET /users
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id`, `company`, `name`, `disabled`, `authenticate_until`, `username`, `email`, `site_admin`, `last_desktop_login_at`, `last_login_at`, `password_validity_days` or `ssl_required`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `username`, `name`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until`, `not_site_admin`, `disabled`, `partner_id`, `primary_group_id` or `workspace_id`. Valid field combinations are `[ site_admin, username ]`, `[ not_site_admin, username ]`, `[ workspace_id, username ]`, `[ company, name ]`, `[ workspace_id, name ]`, `[ workspace_id, email ]`, `[ workspace_id, company ]`, `[ workspace_id, site_admin ]`, `[ workspace_id, not_site_admin ]`, `[ workspace_id, disabled ]`, `[ workspace_id, partner_id ]`, `[ workspace_id, site_admin, username ]`, `[ workspace_id, not_site_admin, username ]`, `[ workspace_id, disabled, username ]`, `[ workspace_id, partner_id, username ]` or `[ workspace_id, company, name ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `password_validity_days`, `last_login_at` or `authenticate_until`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `password_validity_days`, `last_login_at` or `authenticate_until`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `username`, `name`, `email` or `company`. Valid field combinations are `[ company, name ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `password_validity_days`, `last_login_at` or `authenticate_until`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `password_validity_days`, `last_login_at` or `authenticate_until`.
     *     - `ids` (string, optional): comma-separated list of User IDs
     *     - `include_parent_site_users` (bool, optional): Include users from the parent site.
     *     - `include_default_workspace_users` (bool, optional): When scoped to a Custom Workspace, include Default Workspace users who have permissions in that Workspace. Available to Site Administrators, and to Workspace Administrators and Folder Admins in that Workspace when the site shares its Default Workspace directory with Workspaces.
     *     - `search` (string, optional): Searches for partial matches of name, username, or email.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\User[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['ids']) && !is_string($params['ids'])) {
            throw new \Files\Exception\InvalidParameterException('$ids must be of type string; received ' . gettype(@$params['ids']));
        }

        if (!empty($params['search']) && !is_string($params['search'])) {
            throw new \Files\Exception\InvalidParameterException('$search must be of type string; received ' . gettype(@$params['search']));
        }

        $response = Api::sendRequest('/users', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new User((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show User
     *
     * GET /users/{id}
     *
     * @param int $id User ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\User
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/users/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new User((array) (@$response->data ?: []), $options);
    }
    /**
     * Show User
     *
     * GET /users/{id}
     *
     * Alias of find().
     *
     * @param int $id User ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\User
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create User
     *
     * POST /users
     *
     * @param array $params Request parameters.
     *     - `avatar_file` (mixed, optional): An image file for your user avatar.
     *     - `avatar_delete` (bool, optional): If true, the avatar will be deleted.
     *     - `change_password` (string, optional): Used for changing a password on an existing user.
     *     - `change_password_confirmation` (string, optional): Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
     *     - `email` (string, optional): User's email.
     *     - `grant_permission` (string, optional): Permission to grant on the User Root upon creation, or on the Workspace root when the User Root is blank. For the Default Workspace, a blank User Root uses the site root. `admin` on a Custom Workspace's root grants Workspace Administrator access; on a subfolder it grants Folder Admin access. Can be blank or `admin`, `full`, `read`, `write`, `list`, `read+write`, `list+write`, or `history`.
     *     - `group_id` (int, optional): Group ID to associate this user with.
     *     - `group_ids` (string, optional): A list of group ids to associate this user with.  Comma delimited.
     *     - `imported_password_hash` (string, optional): Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash methods are MD5, SHA1, and SHA256.
     *     - `password` (string, optional): User password.
     *     - `password_confirmation` (string, optional): Optional, but if provided, we will ensure that it matches the value sent in `password`.
     *     - `announcements_read` (bool, optional): Signifies that the user has read all the announcements in the UI.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned directly to this user, if any.
     *     - `allowed_ips` (string, optional): A list of allowed IPs if applicable.  Newline delimited
     *     - `attachments_permission` (bool, optional): DEPRECATED: If `true`, the user can user create Bundles (aka Share Links). Use the bundle permission instead.
     *     - `authenticate_until` (string, optional): Scheduled Date/Time at which user will be deactivated
     *     - `authentication_method` (string, optional): How is this user authenticated? Allowed values: password, sso, none, email_signup, password_with_imported_hash, password_and_ssh_key.
     *     - `billing_permission` (bool, optional): Allow this user to perform operations on the account, payments, and invoices?
     *     - `bypass_user_lifecycle_rules` (bool, optional): Exempt this user from user lifecycle rules?
     *     - `bypass_site_allowed_ips` (bool, optional): Allow this user to skip site-wide IP blacklists?
     *     - `dav_permission` (bool, optional): Can the user connect with WebDAV?
     *     - `desktop_configuration_profile_id` (int, optional): Desktop Configuration Profile ID assigned directly to this user, if any.
     *     - `default_workspace_id` (int, optional): Workspace ID the user should land in by default when more than one Workspace is available.
     *     - `disabled` (bool, optional): Is user disabled? Disabled users cannot log in, and do not count for billing purposes. Users can be automatically disabled after an inactivity period via a Site setting or schedule to be deactivated after specific date.
     *     - `filesystem_layout` (string, optional): File system layout Allowed values: site_root, user_root, partner_root, integration_centric, workspace_root.
     *     - `ftp_permission` (bool, optional): Can the user access with FTP/FTPS?
     *     - `header_text` (string, optional): Text to display to the user in the header of the UI
     *     - `integration_centric_profile_id` (int, optional): Integration Centric Profile ID assigned directly to this user, if any.
     *     - `language` (string, optional): Preferred language
     *     - `notification_daily_send_time` (int, optional): Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
     *     - `name` (string, optional): User's full name
     *     - `company` (string, optional): User's company
     *     - `notes` (string, optional): Any internal notes on the user
     *     - `office_integration_enabled` (bool, optional): Enable integration with Office for the web?
     *     - `partner_admin` (bool, optional): Is this user a Partner administrator?
     *     - `partner_id` (int, optional): Partner ID if this user belongs to a Partner
     *     - `password_validity_days` (int, optional): Number of days to allow user to use the same password
     *     - `primary_group_id` (int, optional): Primary group ID for Group Admin scoping
     *     - `readonly_site_admin` (bool, optional): Is the user an allowed to view all (non-billing) site configuration for this site?
     *     - `receive_admin_alerts` (bool, optional): Deprecated. Use notify_on_all_site_warnings and granular failure notification preferences instead.
     *     - `notify_on_all_site_warnings` (bool, optional): Should the user receive site warnings via email?
     *     - `notify_on_all_sso_failures` (bool, optional): Should the user receive sso/scim/ldap configuration/sync failures via email?
     *     - `notify_on_all_user_security_events` (bool, optional): Should the user receive user security events via email?
     *     - `notify_on_all_pending_work_failures` (bool, optional): Should the user receive pending work failures via email?
     *     - `notify_on_all_siem_http_destination_failures` (bool, optional): Should the user receive siem failures via email?
     *     - `notify_on_all_sync_failures` (bool, optional): Should the user receive sync failures via email?
     *     - `notify_on_all_automation_failures` (bool, optional): Should the user receive automation failures via email?
     *     - `notify_on_all_expectation_failures` (bool, optional): Should the user receive expectation failures and misses via email?
     *     - `require_login_by` (string, optional): Require user to login by specified date otherwise it will be disabled.
     *     - `require_password_change` (bool, optional): Is a password change required upon next user login?
     *     - `responsible_group_id` (int, optional): ID of the internal Group responsible for this Partner User, overriding the Partner default.
     *     - `responsible_user_id` (int, optional): ID of the internal User responsible for this Partner User, overriding the Partner default.
     *     - `restapi_permission` (bool, optional): Can this user access the Web app, Desktop app, SDKs, or REST API?  (All of these tools use the API internally, so this is one unified permission set.)
     *     - `s3_compatible_endpoint_permission` (bool, optional): Can the user access the S3-compatible endpoint? Defaults to true.
     *     - `self_managed` (bool, optional): Does this user manage it's own credentials or is it a shared/bot user?
     *     - `sftp_permission` (bool, optional): Can the user access with SFTP?
     *     - `site_admin` (bool, optional): Is the user an administrator for this site?
     *     - `skip_welcome_screen` (bool, optional): Skip Welcome page in the UI?
     *     - `ssl_required` (string, optional): SSL required setting Allowed values: use_system_setting, always_require, never_require.
     *     - `ftp_tls_session_reuse_required` (string, optional): FTP TLS session reuse requirement: use_system_setting, always_require, or never_require Allowed values: use_system_setting, always_require, never_require.
     *     - `sso_strategy_id` (int, optional): SSO (Single Sign On) strategy ID for the user, if applicable.
     *     - `require_2fa` (string, optional): 2FA required setting. `use_system_setting` uses the site-wide setting, including SSO exemptions. `always_require` and `never_require` override the site-wide setting when user-level overrides are allowed. Allowed values: use_system_setting, always_require, never_require.
     *     - `tags` (string, optional): Comma-separated list of Tags for this user. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `time_zone` (string, optional): User time zone
     *     - `user_root` (string, optional): If filesystem layout is user_root, this path is the root path the user is fixed to for all interfaces. If the filesystem layout is site_root or partner_root, this acts as a root folder only for FTP and SFTP (SFTP applicability also requires a site-wide setting to be set). For partner_root layout, this path is relative to the Partner root folder for all callers and blank opts out of an additional protocol root. In this situation, this path is not applied to the API, Desktop, or Web interface.
     *     - `user_home` (string, optional): Home folder for FTP/SFTP. For users with the partner_root filesystem layout, this path is relative to the Partner root folder. In all other cases, it is an absolute path. Only applies to FTP and SFTP, and not any other interface.
     *     - `workspace_admin` (bool, optional): Whether the user is an administrator of their own Custom Workspace. Does not reflect administration granted through Permissions.
     *     - `username` (string, required): User's username
     *     - `workspace_id` (int, optional): ID of the Workspace the user belongs to. 0 is the Default Workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\User
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['username'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: username');
        }

        if (!empty($params['change_password']) && !is_string($params['change_password'])) {
            throw new \Files\Exception\InvalidParameterException('$change_password must be of type string; received ' . gettype(@$params['change_password']));
        }

        if (!empty($params['change_password_confirmation']) && !is_string($params['change_password_confirmation'])) {
            throw new \Files\Exception\InvalidParameterException('$change_password_confirmation must be of type string; received ' . gettype(@$params['change_password_confirmation']));
        }

        if (!empty($params['email']) && !is_string($params['email'])) {
            throw new \Files\Exception\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
        }

        if (!empty($params['grant_permission']) && !is_string($params['grant_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$grant_permission must be of type string; received ' . gettype(@$params['grant_permission']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (!empty($params['imported_password_hash']) && !is_string($params['imported_password_hash'])) {
            throw new \Files\Exception\InvalidParameterException('$imported_password_hash must be of type string; received ' . gettype(@$params['imported_password_hash']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['password_confirmation']) && !is_string($params['password_confirmation'])) {
            throw new \Files\Exception\InvalidParameterException('$password_confirmation must be of type string; received ' . gettype(@$params['password_confirmation']));
        }

        if (!empty($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (!empty($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (!empty($params['authenticate_until']) && !is_string($params['authenticate_until'])) {
            throw new \Files\Exception\InvalidParameterException('$authenticate_until must be of type string; received ' . gettype(@$params['authenticate_until']));
        }

        if (!empty($params['authentication_method']) && !is_string($params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (!empty($params['desktop_configuration_profile_id']) && !is_int($params['desktop_configuration_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_configuration_profile_id must be of type int; received ' . gettype(@$params['desktop_configuration_profile_id']));
        }

        if (!empty($params['default_workspace_id']) && !is_int($params['default_workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$default_workspace_id must be of type int; received ' . gettype(@$params['default_workspace_id']));
        }

        if (!empty($params['filesystem_layout']) && !is_string($params['filesystem_layout'])) {
            throw new \Files\Exception\InvalidParameterException('$filesystem_layout must be of type string; received ' . gettype(@$params['filesystem_layout']));
        }

        if (!empty($params['header_text']) && !is_string($params['header_text'])) {
            throw new \Files\Exception\InvalidParameterException('$header_text must be of type string; received ' . gettype(@$params['header_text']));
        }

        if (!empty($params['integration_centric_profile_id']) && !is_int($params['integration_centric_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_profile_id must be of type int; received ' . gettype(@$params['integration_centric_profile_id']));
        }

        if (!empty($params['language']) && !is_string($params['language'])) {
            throw new \Files\Exception\InvalidParameterException('$language must be of type string; received ' . gettype(@$params['language']));
        }

        if (!empty($params['notification_daily_send_time']) && !is_int($params['notification_daily_send_time'])) {
            throw new \Files\Exception\InvalidParameterException('$notification_daily_send_time must be of type int; received ' . gettype(@$params['notification_daily_send_time']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['company']) && !is_string($params['company'])) {
            throw new \Files\Exception\InvalidParameterException('$company must be of type string; received ' . gettype(@$params['company']));
        }

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['password_validity_days']) && !is_int($params['password_validity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype(@$params['password_validity_days']));
        }

        if (!empty($params['primary_group_id']) && !is_int($params['primary_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$primary_group_id must be of type int; received ' . gettype(@$params['primary_group_id']));
        }

        if (!empty($params['require_login_by']) && !is_string($params['require_login_by'])) {
            throw new \Files\Exception\InvalidParameterException('$require_login_by must be of type string; received ' . gettype(@$params['require_login_by']));
        }

        if (!empty($params['responsible_group_id']) && !is_int($params['responsible_group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_group_id must be of type int; received ' . gettype(@$params['responsible_group_id']));
        }

        if (!empty($params['responsible_user_id']) && !is_int($params['responsible_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$responsible_user_id must be of type int; received ' . gettype(@$params['responsible_user_id']));
        }

        if (!empty($params['ssl_required']) && !is_string($params['ssl_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_required must be of type string; received ' . gettype(@$params['ssl_required']));
        }

        if (!empty($params['ftp_tls_session_reuse_required']) && !is_string($params['ftp_tls_session_reuse_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_tls_session_reuse_required must be of type string; received ' . gettype(@$params['ftp_tls_session_reuse_required']));
        }

        if (!empty($params['sso_strategy_id']) && !is_int($params['sso_strategy_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sso_strategy_id must be of type int; received ' . gettype(@$params['sso_strategy_id']));
        }

        if (!empty($params['require_2fa']) && !is_string($params['require_2fa'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa must be of type string; received ' . gettype(@$params['require_2fa']));
        }

        if (!empty($params['tags']) && !is_string($params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (!empty($params['time_zone']) && !is_string($params['time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$time_zone must be of type string; received ' . gettype(@$params['time_zone']));
        }

        if (!empty($params['user_root']) && !is_string($params['user_root'])) {
            throw new \Files\Exception\InvalidParameterException('$user_root must be of type string; received ' . gettype(@$params['user_root']));
        }

        if (!empty($params['user_home']) && !is_string($params['user_home'])) {
            throw new \Files\Exception\InvalidParameterException('$user_home must be of type string; received ' . gettype(@$params['user_home']));
        }

        if (!empty($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/users', 'POST', $params, $options);

        return new User((array) (@$response->data ?: []), $options);
    }
}
