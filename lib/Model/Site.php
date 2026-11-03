<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Site is the place you'll come to update site settings, as well as manage site-wide API keys.
 *
 * @property int|null $id Site Id
 * @property string|null $name Site name
 * @property string[]|null $additional_text_file_types Additional extensions that are considered text files
 * @property object|null $ai_feature_availability Availability settings for AI features. Each feature requires the site_admins, workspace_admins, folder_admins, and all_users keys. Optional selected_group_members defaults to false; when true, members of at least one group in group_ids get access regardless of the other options. All availability options are additive: any enabled option matching the user grants access. Optional group_ids is an array of integer IDs of active groups on this site, from any workspace. Omitted or empty group_ids grants no access through selected_group_members and does not affect other options. Disabling all AI features overrides these settings.
 * @property bool|null $allowed_2fa_method_sms Is SMS two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_totp Is TOTP two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_webauthn Is WebAuthn two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_yubi Is yubikey two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_email Is OTP via email two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_static Is OTP via static codes for two factor authentication allowed?
 * @property bool|null $allowed_2fa_method_bypass_for_ftp_sftp_dav Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
 * @property int|null $admin_user_id User ID for the main site administrator
 * @property bool|null $admins_bypass_locked_subfolders Allow admins to bypass the locked subfolders setting.
 * @property bool|null $allow_bundle_names Are manual Bundle names allowed?
 * @property bool|null $allow_user_level_2fa_override Allow the site-wide two-factor authentication requirement to be overriden on a per-user-basis?
 * @property bool|null $allow_user_level_allowed_ip_override Allow the site-wide allowed IP restriction to be overriden on a per-user-basis?
 * @property bool|null $allow_user_level_ftp_tls_session_reuse_override Allow per-user overrides of the FTP TLS session reuse requirement?
 * @property bool|null $allow_user_level_ssl_override Allow the site-wide FTP SSL requirement to be overriden on a per-user-basis?
 * @property string|null $allowed_countries Comma separated list of allowed Country codes
 * @property string|null $allowed_ips List of allowed IP addresses
 * @property bool|null $enforce_parent_site_user_ip_restrictions Require Parent Site users entering this Child Site to match its allowed IP addresses in addition to their existing IP restrictions. Applies to all Workspaces. Defaults to false.
 * @property bool|null $always_mkdir_parents Create parent directories if they do not exist during uploads? This is primarily used to work around broken upload clients that assume servers will perform this step.
 * @property int|null $as2_message_retention_days Number of days to retain AS2 messages (incoming and outgoing).
 * @property bool|null $ask_about_overwrites If false, rename conflicting files instead of asking for overwrite confirmation. Only applies to web interface.
 * @property string|null $bundle_activity_notifications Do Bundle owners receive activity notifications?
 * @property int|null $bundle_expiration Site-wide Bundle expiration in days
 * @property string|null $bundle_not_found_message Custom error message to show when bundle is not found.
 * @property bool|null $bundle_password_required Do Bundles require password protection?
 * @property bool|null $bundles_default_owned_by_primary_group If true, new Share Links created by a user with a primary group will default to that group as owner.
 * @property string[]|null $bundle_recipient_blacklist_domains List of email domains to disallow when entering a Bundle/Inbox recipients
 * @property bool|null $bundle_recipient_blacklist_free_email_domains Disallow free email domains for Bundle/Inbox recipients?
 * @property string|null $bundle_registration_notifications Do Bundle owners receive registration notification?
 * @property bool|null $bundle_require_registration Do Bundles require registration?
 * @property bool|null $bundle_require_share_recipient Do Bundles require recipients for sharing?
 * @property bool|null $bundle_send_one_time_password_to_recipient_at_registration If true, new Share Links must send a one-time password to the recipient when they register. Requires bundle_require_share_recipient and cannot be enabled with bundle_password_required.
 * @property bool|null $bundle_require_note Do Bundles require internal notes?
 * @property bool|null $bundle_send_shared_receipts Do Bundle creators receive receipts of invitations?
 * @property string|null $bundle_upload_receipt_notifications Do Bundle uploaders receive upload confirmation notifications?
 * @property \stdClass|null $bundle_watermark_attachment Preview watermark image applied to all bundle items.
 * @property object|null $bundle_watermark_value Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
 * @property bool|null $calculate_file_checksums_crc32 Calculate CRC32 checksums for files?
 * @property bool|null $calculate_file_checksums_md5 Calculate MD5 checksums for files?
 * @property bool|null $calculate_file_checksums_sha1 Calculate SHA1 checksums for files?
 * @property bool|null $calculate_file_checksums_sha256 Calculate SHA256 checksums for files?
 * @property bool|null $child_site_folder_admins_can_see_parent_site_directory If true, administrators on this site's Child Sites, including Folder Admins, can list this site's Default Workspace users and groups and grant them folder permissions and notifications on Child Site paths.
 * @property bool|null $workspace_folder_admins_can_see_default_workspace_directory If true, Workspace Administrators and Folder Admins in a Custom Workspace can list the Default Workspace users and groups that have permissions in that Workspace and choose them as Email Notification recipients.
 * @property bool|null $uploads_via_email_authentication Require email authentication, virus, and spam checks for incoming emails to Inboxes and Incoming Email Automations in every Workspace on this site?
 * @property string|null $color2_left Page link and button color
 * @property string|null $color2_link Top bar link color
 * @property string|null $color2_text Page link and button color
 * @property string|null $color2_top Top bar background color
 * @property string|null $color2_top_text Top bar text color
 * @property string|null $contact_name Site main contact name
 * @property string|null $created_at Time this site was created
 * @property string|null $currency Preferred currency
 * @property bool|null $custom_namespace Is this site using a custom namespace for users?
 * @property bool|null $dav_enabled Is WebDAV enabled?
 * @property bool|null $dav_user_root_enabled Use user FTP roots also for WebDAV?
 * @property int|null $days_to_retain_backups Number of days to keep deleted files
 * @property bool|null $document_edits_in_bundle_allowed If true, allow public viewers of Bundles with full permissions to use document editing integrations.
 * @property string|null $default_time_zone Site default time zone
 * @property bool|null $desktop_app Is the desktop app enabled?
 * @property bool|null $desktop_app_session_ip_pinning Is desktop app session IP pinning enabled?
 * @property int|null $desktop_app_session_lifetime Desktop app session lifetime (in hours)
 * @property bool|null $legacy_checksums_mode Use legacy checksums mode?
 * @property bool|null $legacy_pairing_enabled Allow legacy app pairing in all workspaces? Disabled for new sites; existing sites remain enabled until changed. Disabling blocks new pairing exchanges without revoking existing app credentials or affecting OAuth sign-in. Site administrators can re-enable it.
 * @property string|null $last_pairing_at Time of the last completed legacy app pairing across all workspaces. Failed or incomplete attempts do not count. Read-only; empty until a pairing completes after tracking begins.
 * @property bool|null $migrate_remote_server_sync_to_sync If true, we will migrate all remote server syncs to the new Sync model.
 * @property bool|null $mcp_dcr_enabled Is OAuth DCR (dynamic client registration) for MCP enabled?
 * @property bool|null $mobile_app Is the mobile app enabled?
 * @property bool|null $mobile_app_session_ip_pinning Is mobile app session IP pinning enabled?
 * @property int|null $mobile_app_session_lifetime Mobile app session lifetime (in hours)
 * @property string|null $disallowed_countries Comma separated list of disallowed Country codes
 * @property bool|null $disable_all_ai_features If true, all AI features are disabled for this site.
 * @property bool|null $disable_files_certificate_generation If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
 * @property bool|null $disable_notifications Are notifications disabled?
 * @property bool|null $file_activity_events_enabled If true, file operations produce file_action.activity.v1 EventChannel events in all workspaces. Defaults to false; subscription filters and workspace access still control delivery.
 * @property bool|null $disable_password_reset Is password reset disabled?
 * @property string|null $domain Custom domain
 * @property bool|null $domain_hsts_header Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
 * @property string|null $domain_letsencrypt_chain Letsencrypt chain to use when registering SSL Certificate for domain. No longer used as of 2026.
 * @property string|null $email Main email for this site
 * @property bool|null $fedramp Are FedRAMP security restrictions enabled for this site?
 * @property bool|null $files_com_remote_server_enabled Allow other Files.com sites to use this site's API keys for native Files.com Remote Server connections? Defaults to true and applies to all keys and workspaces on this site. When false, new pairings and access through existing connections are rejected without revoking keys or deleting connections. Re-enabling permits access again with usable keys. Does not disable this site's connections to other sites, ordinary API access, or Connected Sites.
 * @property bool|null $ftp_enabled Is FTP enabled?
 * @property string|null $reply_to_email Reply-to email for this site
 * @property bool|null $non_sso_groups_allowed If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
 * @property bool|null $non_sso_users_allowed If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
 * @property bool|null $folder_permissions_groups_only If true, permissions for this site must be bound to a group (not a user).
 * @property bool|null $group_admins_can_add_users Allow group admins to create users in their groups
 * @property bool|null $group_admins_can_manage_group_memberships Allow group admins to add or remove existing users in their groups
 * @property bool|null $group_admins_can_delete_users Allow group admins to delete users in their groups
 * @property bool|null $group_admins_can_enable_disable_users Allow group admins to enable or disable users in their groups
 * @property bool|null $group_admins_can_modify_users Allow group admins to modify users in their groups
 * @property bool|null $group_admins_can_bypass_user_lifecycle_rules Allow group admins to exempt users in their groups from lifecycle rules
 * @property bool|null $group_admins_can_reset_passwords Allow group admins to reset passwords for users in their groups
 * @property bool|null $group_admins_can_set_user_password Allow group admins to set password authentication method
 * @property bool|null $hipaa Is there a signed HIPAA BAA between Files.com and this site?
 * @property \stdClass|null $icon128 Branded icon 128x128
 * @property \stdClass|null $icon16 Branded icon 16x16
 * @property \stdClass|null $icon32 Branded icon 32x32
 * @property \stdClass|null $icon48 Branded icon 48x48
 * @property string|null $immutable_files_set_at Can files be modified?
 * @property string|null $integration_centric_remote_server_admin_access Administrator access to user-owned Remote Servers in every Workspace: always_visible (default), never_visible, user_choice_default_visible, or user_choice_default_personal. Forced modes override owner preferences; choice modes use the preference or their default.
 * @property bool|null $include_password_in_welcome_email Include password in emails to new users?
 * @property string|null $language Site default language
 * @property string|null $login_help_text Login help text
 * @property \stdClass|null $logo Branded logo
 * @property \stdClass|null $login_page_background_image Branded login page background
 * @property int|null $max_prior_passwords Number of prior passwords to disallow
 * @property object|null $managed_site_settings Site settings managed by the parent site. Each key identifies a managed setting, with a null value. Non-secret effective values are returned in their own Site settings fields.
 * @property string|null $motd_text A message to show users when they connect via FTP or SFTP.
 * @property bool|null $motd_use_for_ftp Show message to users connecting via FTP
 * @property bool|null $motd_use_for_sftp Show message to users connecting via SFTP
 * @property string|null $next_billing_amount Next billing amount
 * @property string|null $next_billing_date Next billing date
 * @property bool|null $office_integration_available If true, allows users to use a document editing integration.
 * @property string|null $office_integration_type Which document editing integration to support. Files.com Editor or Microsoft Office for the Web.
 * @property string|null $oncehub_link Link to scheduling a meeting with our Sales team
 * @property bool|null $opt_out_global Use servers in the USA only?
 * @property bool|null $overdue Is this site's billing overdue?
 * @property int|null $password_min_length Shortest password length for users
 * @property bool|null $password_require_letter Require a letter in passwords?
 * @property bool|null $password_require_mixed Require lower and upper case letters in passwords?
 * @property bool|null $password_require_number Require a number in passwords?
 * @property bool|null $password_require_special Require special characters in password?
 * @property bool|null $password_require_unbreached Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
 * @property bool|null $password_requirements_apply_to_bundles Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
 * @property int|null $password_validity_days Number of days password is valid
 * @property string|null $phone Site phone number
 * @property bool|null $pin_all_remote_servers_to_site_region If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
 * @property bool|null $prevent_root_permissions_for_non_site_admins If true, we will prevent non-administrators from receiving any permissions directly on the root folder. This is commonly used to prevent the accidental application of permissions.
 * @property bool|null $protocol_access_groups_only If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored. Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
 * @property bool|null $require_2fa Require two-factor authentication for all users?
 * @property bool|null $restrict_root_folder_behaviors_to_site_admins If true, only site admins may create, modify, or delete any behavior at the site root, or a skip that would disable one.
 * @property bool|null $root_folder_behaviors_apply_to_workspaces If true, supported protective behaviors at the site root also apply within named workspaces. Requires restrict_root_folder_behaviors_to_site_admins to be enabled.
 * @property bool|null $require_2fa_exempt_all_sso_users If true, SSO users using the default user-level two-factor authentication setting are exempt from the site-wide two-factor authentication requirement.
 * @property string|null $require_2fa_stop_time If set, requirement for two-factor authentication has been scheduled to end on this date-time.
 * @property bool|null $revoke_bundle_access_on_disable_or_delete Auto-removes bundles for disabled/deleted users and enforces bundle expiry within user access period.
 * @property string|null $require_2fa_user_type What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
 * @property bool|null $require_logout_from_bundles_and_inboxes If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
 * @property \stdClass|null $session Current session
 * @property bool|null $s3_compatible_endpoint_enabled Is the S3-compatible endpoint enabled for all users and workspaces on this site? Defaults to true. When false, user and group S3 permissions do not allow access.
 * @property bool|null $sftp_enabled Is SFTP enabled?
 * @property bool|null $sftp_finalize_partial_uploads Finalize partial SFTP uploads from interrupted connections? Default: true.
 * @property string|null $sftp_host_key_type Sftp Host Key Type
 * @property int|null $active_sftp_host_key_id Id of the currently selected custom SFTP Host Key
 * @property int[]|null $active_sftp_host_key_ids Ids of the selected custom SFTP Host Keys
 * @property bool|null $sftp_insecure_ciphers If true, we will allow weak and known insecure ciphers to be used for SFTP connections. Enabling this setting severely weakens the security of your site and it is not recommend, except as a last resort for compatibility.
 * @property bool|null $sftp_insecure_diffie_hellman If true, we will allow weak Diffie Hellman parameters to be used within ciphers for SFTP that are otherwise on our secure list. This has the effect of making the cipher weaker than our normal threshold for security, but is required to support certain legacy or broken SSH and MFT clients. Enabling this weakens security, but not nearly as much as enabling the full `sftp_insecure_ciphers` option.
 * @property bool|null $sftp_user_root_enabled Use user FTP roots also for SFTP?
 * @property bool|null $sharing_enabled Allow bundle creation
 * @property bool|null $show_user_notifications_log_in_link Show log in link in user notifications?
 * @property bool|null $show_request_access_link Show request access link for users without access? Currently unused.
 * @property string|null $site_footer Custom site footer text for authenticated pages
 * @property string|null $site_header Custom site header text for authenticated pages
 * @property string|null $site_public_footer Custom site footer text for public pages
 * @property string|null $site_public_header Custom site header text for public pages
 * @property string|null $smtp_address SMTP server hostname or IP
 * @property string|null $smtp_authentication SMTP server authentication type
 * @property string|null $smtp_from From address to use when mailing through custom SMTP
 * @property int|null $smtp_port SMTP server port
 * @property string|null $smtp_ssl Custom SMTP encryption mode: if_available (default) uses STARTTLS when offered and otherwise sends credentials and messages unencrypted. A certificate-verified STARTTLS connection automatically changes if_available to require unless smtp_ssl is managed by a parent policy. require requires STARTTLS before authentication; require_implicit uses TLS from connection start; never disables TLS. TLS verifies the server certificate against smtp_address. Encryption is never automatically downgraded.
 * @property string|null $smtp_username SMTP server username
 * @property int|null $session_expiry_minutes Session expiry in minutes
 * @property bool|null $snapshot_sharing_enabled Allow snapshot share links creation
 * @property bool|null $ftp_tls_session_reuse_required Require TLS-protected FTP data connections to reuse the control connection TLS session? Applies to all workspaces.
 * @property bool|null $ssl_required Is SSL required? Disabling this is insecure.
 * @property string|null $subdomain Site subdomain
 * @property string|null $switch_to_plan_date If switching plans, when does the new plan take effect?
 * @property int|null $trial_days_left Number of days left in trial
 * @property string|null $trial_until When does this Site trial expire?
 * @property bool|null $use_dedicated_ips_for_smtp If using custom SMTP, should we use dedicated IPs to deliver emails?
 * @property bool|null $use_provided_modified_at Allow uploaders to set `provided_modified_at` for uploaded files?
 * @property \stdClass|null $user User of current session
 * @property bool|null $user_lockout Will users be locked out after incorrect login attempts?
 * @property int|null $user_lockout_lock_period How many hours to lock user out for failed password?
 * @property int|null $user_lockout_tries Number of login tries within `user_lockout_within` hours before users are locked out
 * @property int|null $user_lockout_within Number of hours for user lockout window
 * @property bool|null $user_requests_enabled Enable User Requests feature
 * @property bool|null $user_requests_notify_admins Send email to site admins when a user request is received?
 * @property bool|null $users_can_create_api_keys Allow users to create their own API keys?
 * @property bool|null $users_can_create_ssh_keys Allow users to create their own SSH keys?
 * @property string|null $username_display How usernames are displayed in the web UI. Can be `username_only`, `full_name_only`, `full_name_username`, `full_name_company`, or `full_name_username_company`.
 * @property string|null $welcome_custom_text Custom text send in user welcome email
 * @property string|null $email_footer_custom_text Custom footer text for system-generated emails (as Markdown). Supports standard strftime date/time patterns like %Y (4-digit year), %m (month), %d (day).
 * @property string|null $welcome_email_cc Include this email in welcome emails if enabled
 * @property string|null $welcome_email_subject Include this email subject in welcome emails if enabled
 * @property bool|null $welcome_email_enabled Will the welcome email be sent to new users?
 * @property string|null $welcome_screen Does the welcome screen appear?
 * @property bool|null $windows_mode_ftp Does FTP user Windows emulation mode?
 * @package Files
 */
class Site
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
     * Site Id
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Site name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * Additional extensions that are considered text files
     *
     * @return string[]|null
     */
    public function getAdditionalTextFileTypes()
    {
        return isset($this->attributes['additional_text_file_types']) ? $this->attributes['additional_text_file_types'] : null;
    }
    /**
     * Availability settings for AI features. Each feature requires the site_admins, workspace_admins, folder_admins, and all_users keys. Optional selected_group_members defaults to false; when true, members of at least one group in group_ids get access regardless of the other options. All availability options are additive: any enabled option matching the user grants access. Optional group_ids is an array of integer IDs of active groups on this site, from any workspace. Omitted or empty group_ids grants no access through selected_group_members and does not affect other options. Disabling all AI features overrides these settings.
     *
     * @return object|null
     */
    public function getAiFeatureAvailability()
    {
        return isset($this->attributes['ai_feature_availability']) ? $this->attributes['ai_feature_availability'] : null;
    }
    /**
     * Is SMS two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodSms()
    {
        return isset($this->attributes['allowed_2fa_method_sms']) ? $this->attributes['allowed_2fa_method_sms'] : null;
    }
    /**
     * Is TOTP two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodTotp()
    {
        return isset($this->attributes['allowed_2fa_method_totp']) ? $this->attributes['allowed_2fa_method_totp'] : null;
    }
    /**
     * Is WebAuthn two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodWebauthn()
    {
        return isset($this->attributes['allowed_2fa_method_webauthn']) ? $this->attributes['allowed_2fa_method_webauthn'] : null;
    }
    /**
     * Is yubikey two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodYubi()
    {
        return isset($this->attributes['allowed_2fa_method_yubi']) ? $this->attributes['allowed_2fa_method_yubi'] : null;
    }
    /**
     * Is OTP via email two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodEmail()
    {
        return isset($this->attributes['allowed_2fa_method_email']) ? $this->attributes['allowed_2fa_method_email'] : null;
    }
    /**
     * Is OTP via static codes for two factor authentication allowed?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodStatic()
    {
        return isset($this->attributes['allowed_2fa_method_static']) ? $this->attributes['allowed_2fa_method_static'] : null;
    }
    /**
     * Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
     *
     * @return bool|null
     */
    public function getAllowed2faMethodBypassForFtpSftpDav()
    {
        return isset($this->attributes['allowed_2fa_method_bypass_for_ftp_sftp_dav']) ? $this->attributes['allowed_2fa_method_bypass_for_ftp_sftp_dav'] : null;
    }
    /**
     * User ID for the main site administrator
     *
     * @return int|null
     */
    public function getAdminUserId()
    {
        return isset($this->attributes['admin_user_id']) ? $this->attributes['admin_user_id'] : null;
    }
    /**
     * Allow admins to bypass the locked subfolders setting.
     *
     * @return bool|null
     */
    public function getAdminsBypassLockedSubfolders()
    {
        return isset($this->attributes['admins_bypass_locked_subfolders']) ? $this->attributes['admins_bypass_locked_subfolders'] : null;
    }
    /**
     * Are manual Bundle names allowed?
     *
     * @return bool|null
     */
    public function getAllowBundleNames()
    {
        return isset($this->attributes['allow_bundle_names']) ? $this->attributes['allow_bundle_names'] : null;
    }
    /**
     * Allow the site-wide two-factor authentication requirement to be overriden on a per-user-basis?
     *
     * @return bool|null
     */
    public function getAllowUserLevel2faOverride()
    {
        return isset($this->attributes['allow_user_level_2fa_override']) ? $this->attributes['allow_user_level_2fa_override'] : null;
    }
    /**
     * Allow the site-wide allowed IP restriction to be overriden on a per-user-basis?
     *
     * @return bool|null
     */
    public function getAllowUserLevelAllowedIpOverride()
    {
        return isset($this->attributes['allow_user_level_allowed_ip_override']) ? $this->attributes['allow_user_level_allowed_ip_override'] : null;
    }
    /**
     * Allow per-user overrides of the FTP TLS session reuse requirement?
     *
     * @return bool|null
     */
    public function getAllowUserLevelFtpTlsSessionReuseOverride()
    {
        return isset($this->attributes['allow_user_level_ftp_tls_session_reuse_override']) ? $this->attributes['allow_user_level_ftp_tls_session_reuse_override'] : null;
    }
    /**
     * Allow the site-wide FTP SSL requirement to be overriden on a per-user-basis?
     *
     * @return bool|null
     */
    public function getAllowUserLevelSslOverride()
    {
        return isset($this->attributes['allow_user_level_ssl_override']) ? $this->attributes['allow_user_level_ssl_override'] : null;
    }
    /**
     * Comma separated list of allowed Country codes
     *
     * @return string|null
     */
    public function getAllowedCountries()
    {
        return isset($this->attributes['allowed_countries']) ? $this->attributes['allowed_countries'] : null;
    }
    /**
     * List of allowed IP addresses
     *
     * @return string|null
     */
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }
    /**
     * Require Parent Site users entering this Child Site to match its allowed IP addresses in addition to their existing IP restrictions. Applies to all Workspaces. Defaults to false.
     *
     * @return bool|null
     */
    public function getEnforceParentSiteUserIpRestrictions()
    {
        return isset($this->attributes['enforce_parent_site_user_ip_restrictions']) ? $this->attributes['enforce_parent_site_user_ip_restrictions'] : null;
    }
    /**
     * Create parent directories if they do not exist during uploads?  This is primarily used to work around broken upload clients that assume servers will perform this step.
     *
     * @return bool|null
     */
    public function getAlwaysMkdirParents()
    {
        return isset($this->attributes['always_mkdir_parents']) ? $this->attributes['always_mkdir_parents'] : null;
    }
    /**
     * Number of days to retain AS2 messages (incoming and outgoing).
     *
     * @return int|null
     */
    public function getAs2MessageRetentionDays()
    {
        return isset($this->attributes['as2_message_retention_days']) ? $this->attributes['as2_message_retention_days'] : null;
    }
    /**
     * If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
     *
     * @return bool|null
     */
    public function getAskAboutOverwrites()
    {
        return isset($this->attributes['ask_about_overwrites']) ? $this->attributes['ask_about_overwrites'] : null;
    }
    /**
     * Do Bundle owners receive activity notifications?
     *
     * @return string|null
     */
    public function getBundleActivityNotifications()
    {
        return isset($this->attributes['bundle_activity_notifications']) ? $this->attributes['bundle_activity_notifications'] : null;
    }
    /**
     * Site-wide Bundle expiration in days
     *
     * @return int|null
     */
    public function getBundleExpiration()
    {
        return isset($this->attributes['bundle_expiration']) ? $this->attributes['bundle_expiration'] : null;
    }
    /**
     * Custom error message to show when bundle is not found.
     *
     * @return string|null
     */
    public function getBundleNotFoundMessage()
    {
        return isset($this->attributes['bundle_not_found_message']) ? $this->attributes['bundle_not_found_message'] : null;
    }
    /**
     * Do Bundles require password protection?
     *
     * @return bool|null
     */
    public function getBundlePasswordRequired()
    {
        return isset($this->attributes['bundle_password_required']) ? $this->attributes['bundle_password_required'] : null;
    }
    /**
     * If true, new Share Links created by a user with a primary group will default to that group as owner.
     *
     * @return bool|null
     */
    public function getBundlesDefaultOwnedByPrimaryGroup()
    {
        return isset($this->attributes['bundles_default_owned_by_primary_group']) ? $this->attributes['bundles_default_owned_by_primary_group'] : null;
    }
    /**
     * List of email domains to disallow when entering a Bundle/Inbox recipients
     *
     * @return string[]|null
     */
    public function getBundleRecipientBlacklistDomains()
    {
        return isset($this->attributes['bundle_recipient_blacklist_domains']) ? $this->attributes['bundle_recipient_blacklist_domains'] : null;
    }
    /**
     * Disallow free email domains for Bundle/Inbox recipients?
     *
     * @return bool|null
     */
    public function getBundleRecipientBlacklistFreeEmailDomains()
    {
        return isset($this->attributes['bundle_recipient_blacklist_free_email_domains']) ? $this->attributes['bundle_recipient_blacklist_free_email_domains'] : null;
    }
    /**
     * Do Bundle owners receive registration notification?
     *
     * @return string|null
     */
    public function getBundleRegistrationNotifications()
    {
        return isset($this->attributes['bundle_registration_notifications']) ? $this->attributes['bundle_registration_notifications'] : null;
    }
    /**
     * Do Bundles require registration?
     *
     * @return bool|null
     */
    public function getBundleRequireRegistration()
    {
        return isset($this->attributes['bundle_require_registration']) ? $this->attributes['bundle_require_registration'] : null;
    }
    /**
     * Do Bundles require recipients for sharing?
     *
     * @return bool|null
     */
    public function getBundleRequireShareRecipient()
    {
        return isset($this->attributes['bundle_require_share_recipient']) ? $this->attributes['bundle_require_share_recipient'] : null;
    }
    /**
     * If true, new Share Links must send a one-time password to the recipient when they register. Requires bundle_require_share_recipient and cannot be enabled with bundle_password_required.
     *
     * @return bool|null
     */
    public function getBundleSendOneTimePasswordToRecipientAtRegistration()
    {
        return isset($this->attributes['bundle_send_one_time_password_to_recipient_at_registration']) ? $this->attributes['bundle_send_one_time_password_to_recipient_at_registration'] : null;
    }
    /**
     * Do Bundles require internal notes?
     *
     * @return bool|null
     */
    public function getBundleRequireNote()
    {
        return isset($this->attributes['bundle_require_note']) ? $this->attributes['bundle_require_note'] : null;
    }
    /**
     * Do Bundle creators receive receipts of invitations?
     *
     * @return bool|null
     */
    public function getBundleSendSharedReceipts()
    {
        return isset($this->attributes['bundle_send_shared_receipts']) ? $this->attributes['bundle_send_shared_receipts'] : null;
    }
    /**
     * Do Bundle uploaders receive upload confirmation notifications?
     *
     * @return string|null
     */
    public function getBundleUploadReceiptNotifications()
    {
        return isset($this->attributes['bundle_upload_receipt_notifications']) ? $this->attributes['bundle_upload_receipt_notifications'] : null;
    }
    /**
     * Preview watermark image applied to all bundle items.
     *
     * @return \stdClass|null
     */
    public function getBundleWatermarkAttachment()
    {
        return isset($this->attributes['bundle_watermark_attachment']) ? $this->attributes['bundle_watermark_attachment'] : null;
    }
    /**
     * Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     *
     * @return object|null
     */
    public function getBundleWatermarkValue()
    {
        return isset($this->attributes['bundle_watermark_value']) ? $this->attributes['bundle_watermark_value'] : null;
    }
    /**
     * Calculate CRC32 checksums for files?
     *
     * @return bool|null
     */
    public function getCalculateFileChecksumsCrc32()
    {
        return isset($this->attributes['calculate_file_checksums_crc32']) ? $this->attributes['calculate_file_checksums_crc32'] : null;
    }
    /**
     * Calculate MD5 checksums for files?
     *
     * @return bool|null
     */
    public function getCalculateFileChecksumsMd5()
    {
        return isset($this->attributes['calculate_file_checksums_md5']) ? $this->attributes['calculate_file_checksums_md5'] : null;
    }
    /**
     * Calculate SHA1 checksums for files?
     *
     * @return bool|null
     */
    public function getCalculateFileChecksumsSha1()
    {
        return isset($this->attributes['calculate_file_checksums_sha1']) ? $this->attributes['calculate_file_checksums_sha1'] : null;
    }
    /**
     * Calculate SHA256 checksums for files?
     *
     * @return bool|null
     */
    public function getCalculateFileChecksumsSha256()
    {
        return isset($this->attributes['calculate_file_checksums_sha256']) ? $this->attributes['calculate_file_checksums_sha256'] : null;
    }
    /**
     * If true, administrators on this site's Child Sites, including Folder Admins, can list this site's Default Workspace users and groups and grant them folder permissions and notifications on Child Site paths.
     *
     * @return bool|null
     */
    public function getChildSiteFolderAdminsCanSeeParentSiteDirectory()
    {
        return isset($this->attributes['child_site_folder_admins_can_see_parent_site_directory']) ? $this->attributes['child_site_folder_admins_can_see_parent_site_directory'] : null;
    }
    /**
     * If true, Workspace Administrators and Folder Admins in a Custom Workspace can list the Default Workspace users and groups that have permissions in that Workspace and choose them as Email Notification recipients.
     *
     * @return bool|null
     */
    public function getWorkspaceFolderAdminsCanSeeDefaultWorkspaceDirectory()
    {
        return isset($this->attributes['workspace_folder_admins_can_see_default_workspace_directory']) ? $this->attributes['workspace_folder_admins_can_see_default_workspace_directory'] : null;
    }
    /**
     * Require email authentication, virus, and spam checks for incoming emails to Inboxes and Incoming Email Automations in every Workspace on this site?
     *
     * @return bool|null
     */
    public function getUploadsViaEmailAuthentication()
    {
        return isset($this->attributes['uploads_via_email_authentication']) ? $this->attributes['uploads_via_email_authentication'] : null;
    }
    /**
     * Page link and button color
     *
     * @return string|null
     */
    public function getColor2Left()
    {
        return isset($this->attributes['color2_left']) ? $this->attributes['color2_left'] : null;
    }
    /**
     * Top bar link color
     *
     * @return string|null
     */
    public function getColor2Link()
    {
        return isset($this->attributes['color2_link']) ? $this->attributes['color2_link'] : null;
    }
    /**
     * Page link and button color
     *
     * @return string|null
     */
    public function getColor2Text()
    {
        return isset($this->attributes['color2_text']) ? $this->attributes['color2_text'] : null;
    }
    /**
     * Top bar background color
     *
     * @return string|null
     */
    public function getColor2Top()
    {
        return isset($this->attributes['color2_top']) ? $this->attributes['color2_top'] : null;
    }
    /**
     * Top bar text color
     *
     * @return string|null
     */
    public function getColor2TopText()
    {
        return isset($this->attributes['color2_top_text']) ? $this->attributes['color2_top_text'] : null;
    }
    /**
     * Site main contact name
     *
     * @return string|null
     */
    public function getContactName()
    {
        return isset($this->attributes['contact_name']) ? $this->attributes['contact_name'] : null;
    }
    /**
     * Time this site was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Preferred currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return isset($this->attributes['currency']) ? $this->attributes['currency'] : null;
    }
    /**
     * Is this site using a custom namespace for users?
     *
     * @return bool|null
     */
    public function getCustomNamespace()
    {
        return isset($this->attributes['custom_namespace']) ? $this->attributes['custom_namespace'] : null;
    }
    /**
     * Is WebDAV enabled?
     *
     * @return bool|null
     */
    public function getDavEnabled()
    {
        return isset($this->attributes['dav_enabled']) ? $this->attributes['dav_enabled'] : null;
    }
    /**
     * Use user FTP roots also for WebDAV?
     *
     * @return bool|null
     */
    public function getDavUserRootEnabled()
    {
        return isset($this->attributes['dav_user_root_enabled']) ? $this->attributes['dav_user_root_enabled'] : null;
    }
    /**
     * Number of days to keep deleted files
     *
     * @return int|null
     */
    public function getDaysToRetainBackups()
    {
        return isset($this->attributes['days_to_retain_backups']) ? $this->attributes['days_to_retain_backups'] : null;
    }
    /**
     * If true, allow public viewers of Bundles with full permissions to use document editing integrations.
     *
     * @return bool|null
     */
    public function getDocumentEditsInBundleAllowed()
    {
        return isset($this->attributes['document_edits_in_bundle_allowed']) ? $this->attributes['document_edits_in_bundle_allowed'] : null;
    }
    /**
     * Site default time zone
     *
     * @return string|null
     */
    public function getDefaultTimeZone()
    {
        return isset($this->attributes['default_time_zone']) ? $this->attributes['default_time_zone'] : null;
    }
    /**
     * Is the desktop app enabled?
     *
     * @return bool|null
     */
    public function getDesktopApp()
    {
        return isset($this->attributes['desktop_app']) ? $this->attributes['desktop_app'] : null;
    }
    /**
     * Is desktop app session IP pinning enabled?
     *
     * @return bool|null
     */
    public function getDesktopAppSessionIpPinning()
    {
        return isset($this->attributes['desktop_app_session_ip_pinning']) ? $this->attributes['desktop_app_session_ip_pinning'] : null;
    }
    /**
     * Desktop app session lifetime (in hours)
     *
     * @return int|null
     */
    public function getDesktopAppSessionLifetime()
    {
        return isset($this->attributes['desktop_app_session_lifetime']) ? $this->attributes['desktop_app_session_lifetime'] : null;
    }
    /**
     * Use legacy checksums mode?
     *
     * @return bool|null
     */
    public function getLegacyChecksumsMode()
    {
        return isset($this->attributes['legacy_checksums_mode']) ? $this->attributes['legacy_checksums_mode'] : null;
    }
    /**
     * Allow legacy app pairing in all workspaces? Disabled for new sites; existing sites remain enabled until changed. Disabling blocks new pairing exchanges without revoking existing app credentials or affecting OAuth sign-in. Site administrators can re-enable it.
     *
     * @return bool|null
     */
    public function getLegacyPairingEnabled()
    {
        return isset($this->attributes['legacy_pairing_enabled']) ? $this->attributes['legacy_pairing_enabled'] : null;
    }
    /**
     * Time of the last completed legacy app pairing across all workspaces. Failed or incomplete attempts do not count. Read-only; empty until a pairing completes after tracking begins.
     *
     * @return string|null
     */
    public function getLastPairingAt()
    {
        return isset($this->attributes['last_pairing_at']) ? $this->attributes['last_pairing_at'] : null;
    }
    /**
     * If true, we will migrate all remote server syncs to the new Sync model.
     *
     * @return bool|null
     */
    public function getMigrateRemoteServerSyncToSync()
    {
        return isset($this->attributes['migrate_remote_server_sync_to_sync']) ? $this->attributes['migrate_remote_server_sync_to_sync'] : null;
    }
    /**
     * Is OAuth DCR (dynamic client registration) for MCP enabled?
     *
     * @return bool|null
     */
    public function getMcpDcrEnabled()
    {
        return isset($this->attributes['mcp_dcr_enabled']) ? $this->attributes['mcp_dcr_enabled'] : null;
    }
    /**
     * Is the mobile app enabled?
     *
     * @return bool|null
     */
    public function getMobileApp()
    {
        return isset($this->attributes['mobile_app']) ? $this->attributes['mobile_app'] : null;
    }
    /**
     * Is mobile app session IP pinning enabled?
     *
     * @return bool|null
     */
    public function getMobileAppSessionIpPinning()
    {
        return isset($this->attributes['mobile_app_session_ip_pinning']) ? $this->attributes['mobile_app_session_ip_pinning'] : null;
    }
    /**
     * Mobile app session lifetime (in hours)
     *
     * @return int|null
     */
    public function getMobileAppSessionLifetime()
    {
        return isset($this->attributes['mobile_app_session_lifetime']) ? $this->attributes['mobile_app_session_lifetime'] : null;
    }
    /**
     * Comma separated list of disallowed Country codes
     *
     * @return string|null
     */
    public function getDisallowedCountries()
    {
        return isset($this->attributes['disallowed_countries']) ? $this->attributes['disallowed_countries'] : null;
    }
    /**
     * If true, all AI features are disabled for this site.
     *
     * @return bool|null
     */
    public function getDisableAllAiFeatures()
    {
        return isset($this->attributes['disable_all_ai_features']) ? $this->attributes['disable_all_ai_features'] : null;
    }
    /**
     * If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
     *
     * @return bool|null
     */
    public function getDisableFilesCertificateGeneration()
    {
        return isset($this->attributes['disable_files_certificate_generation']) ? $this->attributes['disable_files_certificate_generation'] : null;
    }
    /**
     * Are notifications disabled?
     *
     * @return bool|null
     */
    public function getDisableNotifications()
    {
        return isset($this->attributes['disable_notifications']) ? $this->attributes['disable_notifications'] : null;
    }
    /**
     * If true, file operations produce file_action.activity.v1 EventChannel events in all workspaces. Defaults to false; subscription filters and workspace access still control delivery.
     *
     * @return bool|null
     */
    public function getFileActivityEventsEnabled()
    {
        return isset($this->attributes['file_activity_events_enabled']) ? $this->attributes['file_activity_events_enabled'] : null;
    }
    /**
     * Is password reset disabled?
     *
     * @return bool|null
     */
    public function getDisablePasswordReset()
    {
        return isset($this->attributes['disable_password_reset']) ? $this->attributes['disable_password_reset'] : null;
    }
    /**
     * Custom domain
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->attributes['domain']) ? $this->attributes['domain'] : null;
    }
    /**
     * Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
     *
     * @return bool|null
     */
    public function getDomainHstsHeader()
    {
        return isset($this->attributes['domain_hsts_header']) ? $this->attributes['domain_hsts_header'] : null;
    }
    /**
     * Letsencrypt chain to use when registering SSL Certificate for domain. No longer used as of 2026.
     *
     * @return string|null
     */
    public function getDomainLetsencryptChain()
    {
        return isset($this->attributes['domain_letsencrypt_chain']) ? $this->attributes['domain_letsencrypt_chain'] : null;
    }
    /**
     * Main email for this site
     *
     * @return string|null
     */
    public function getEmail()
    {
        return isset($this->attributes['email']) ? $this->attributes['email'] : null;
    }
    /**
     * Are FedRAMP security restrictions enabled for this site?
     *
     * @return bool|null
     */
    public function getFedramp()
    {
        return isset($this->attributes['fedramp']) ? $this->attributes['fedramp'] : null;
    }
    /**
     * Allow other Files.com sites to use this site's API keys for native Files.com Remote Server connections? Defaults to true and applies to all keys and workspaces on this site. When false, new pairings and access through existing connections are rejected without revoking keys or deleting connections. Re-enabling permits access again with usable keys. Does not disable this site's connections to other sites, ordinary API access, or Connected Sites.
     *
     * @return bool|null
     */
    public function getFilesComRemoteServerEnabled()
    {
        return isset($this->attributes['files_com_remote_server_enabled']) ? $this->attributes['files_com_remote_server_enabled'] : null;
    }
    /**
     * Is FTP enabled?
     *
     * @return bool|null
     */
    public function getFtpEnabled()
    {
        return isset($this->attributes['ftp_enabled']) ? $this->attributes['ftp_enabled'] : null;
    }
    /**
     * Reply-to email for this site
     *
     * @return string|null
     */
    public function getReplyToEmail()
    {
        return isset($this->attributes['reply_to_email']) ? $this->attributes['reply_to_email'] : null;
    }
    /**
     * If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
     *
     * @return bool|null
     */
    public function getNonSsoGroupsAllowed()
    {
        return isset($this->attributes['non_sso_groups_allowed']) ? $this->attributes['non_sso_groups_allowed'] : null;
    }
    /**
     * If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
     *
     * @return bool|null
     */
    public function getNonSsoUsersAllowed()
    {
        return isset($this->attributes['non_sso_users_allowed']) ? $this->attributes['non_sso_users_allowed'] : null;
    }
    /**
     * If true, permissions for this site must be bound to a group (not a user).
     *
     * @return bool|null
     */
    public function getFolderPermissionsGroupsOnly()
    {
        return isset($this->attributes['folder_permissions_groups_only']) ? $this->attributes['folder_permissions_groups_only'] : null;
    }
    /**
     * Allow group admins to create users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanAddUsers()
    {
        return isset($this->attributes['group_admins_can_add_users']) ? $this->attributes['group_admins_can_add_users'] : null;
    }
    /**
     * Allow group admins to add or remove existing users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanManageGroupMemberships()
    {
        return isset($this->attributes['group_admins_can_manage_group_memberships']) ? $this->attributes['group_admins_can_manage_group_memberships'] : null;
    }
    /**
     * Allow group admins to delete users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanDeleteUsers()
    {
        return isset($this->attributes['group_admins_can_delete_users']) ? $this->attributes['group_admins_can_delete_users'] : null;
    }
    /**
     * Allow group admins to enable or disable users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanEnableDisableUsers()
    {
        return isset($this->attributes['group_admins_can_enable_disable_users']) ? $this->attributes['group_admins_can_enable_disable_users'] : null;
    }
    /**
     * Allow group admins to modify users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanModifyUsers()
    {
        return isset($this->attributes['group_admins_can_modify_users']) ? $this->attributes['group_admins_can_modify_users'] : null;
    }
    /**
     * Allow group admins to exempt users in their groups from lifecycle rules
     *
     * @return bool|null
     */
    public function getGroupAdminsCanBypassUserLifecycleRules()
    {
        return isset($this->attributes['group_admins_can_bypass_user_lifecycle_rules']) ? $this->attributes['group_admins_can_bypass_user_lifecycle_rules'] : null;
    }
    /**
     * Allow group admins to reset passwords for users in their groups
     *
     * @return bool|null
     */
    public function getGroupAdminsCanResetPasswords()
    {
        return isset($this->attributes['group_admins_can_reset_passwords']) ? $this->attributes['group_admins_can_reset_passwords'] : null;
    }
    /**
     * Allow group admins to set password authentication method
     *
     * @return bool|null
     */
    public function getGroupAdminsCanSetUserPassword()
    {
        return isset($this->attributes['group_admins_can_set_user_password']) ? $this->attributes['group_admins_can_set_user_password'] : null;
    }
    /**
     * Is there a signed HIPAA BAA between Files.com and this site?
     *
     * @return bool|null
     */
    public function getHipaa()
    {
        return isset($this->attributes['hipaa']) ? $this->attributes['hipaa'] : null;
    }
    /**
     * Branded icon 128x128
     *
     * @return \stdClass|null
     */
    public function getIcon128()
    {
        return isset($this->attributes['icon128']) ? $this->attributes['icon128'] : null;
    }
    /**
     * Branded icon 16x16
     *
     * @return \stdClass|null
     */
    public function getIcon16()
    {
        return isset($this->attributes['icon16']) ? $this->attributes['icon16'] : null;
    }
    /**
     * Branded icon 32x32
     *
     * @return \stdClass|null
     */
    public function getIcon32()
    {
        return isset($this->attributes['icon32']) ? $this->attributes['icon32'] : null;
    }
    /**
     * Branded icon 48x48
     *
     * @return \stdClass|null
     */
    public function getIcon48()
    {
        return isset($this->attributes['icon48']) ? $this->attributes['icon48'] : null;
    }
    /**
     * Can files be modified?
     *
     * @return string|null
     */
    public function getImmutableFilesSetAt()
    {
        return isset($this->attributes['immutable_files_set_at']) ? $this->attributes['immutable_files_set_at'] : null;
    }
    /**
     * Administrator access to user-owned Remote Servers in every Workspace: always_visible (default), never_visible, user_choice_default_visible, or user_choice_default_personal. Forced modes override owner preferences; choice modes use the preference or their default.
     *
     * @return string|null
     */
    public function getIntegrationCentricRemoteServerAdminAccess()
    {
        return isset($this->attributes['integration_centric_remote_server_admin_access']) ? $this->attributes['integration_centric_remote_server_admin_access'] : null;
    }
    /**
     * Include password in emails to new users?
     *
     * @return bool|null
     */
    public function getIncludePasswordInWelcomeEmail()
    {
        return isset($this->attributes['include_password_in_welcome_email']) ? $this->attributes['include_password_in_welcome_email'] : null;
    }
    /**
     * Site default language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        return isset($this->attributes['language']) ? $this->attributes['language'] : null;
    }
    /**
     * Login help text
     *
     * @return string|null
     */
    public function getLoginHelpText()
    {
        return isset($this->attributes['login_help_text']) ? $this->attributes['login_help_text'] : null;
    }
    /**
     * Branded logo
     *
     * @return \stdClass|null
     */
    public function getLogo()
    {
        return isset($this->attributes['logo']) ? $this->attributes['logo'] : null;
    }
    /**
     * Branded login page background
     *
     * @return \stdClass|null
     */
    public function getLoginPageBackgroundImage()
    {
        return isset($this->attributes['login_page_background_image']) ? $this->attributes['login_page_background_image'] : null;
    }
    /**
     * Number of prior passwords to disallow
     *
     * @return int|null
     */
    public function getMaxPriorPasswords()
    {
        return isset($this->attributes['max_prior_passwords']) ? $this->attributes['max_prior_passwords'] : null;
    }
    /**
     * Site settings managed by the parent site. Each key identifies a managed setting, with a null value. Non-secret effective values are returned in their own Site settings fields.
     *
     * @return object|null
     */
    public function getManagedSiteSettings()
    {
        return isset($this->attributes['managed_site_settings']) ? $this->attributes['managed_site_settings'] : null;
    }
    /**
     * A message to show users when they connect via FTP or SFTP.
     *
     * @return string|null
     */
    public function getMotdText()
    {
        return isset($this->attributes['motd_text']) ? $this->attributes['motd_text'] : null;
    }
    /**
     * Show message to users connecting via FTP
     *
     * @return bool|null
     */
    public function getMotdUseForFtp()
    {
        return isset($this->attributes['motd_use_for_ftp']) ? $this->attributes['motd_use_for_ftp'] : null;
    }
    /**
     * Show message to users connecting via SFTP
     *
     * @return bool|null
     */
    public function getMotdUseForSftp()
    {
        return isset($this->attributes['motd_use_for_sftp']) ? $this->attributes['motd_use_for_sftp'] : null;
    }
    /**
     * Next billing amount
     *
     * @return string|null
     */
    public function getNextBillingAmount()
    {
        return isset($this->attributes['next_billing_amount']) ? $this->attributes['next_billing_amount'] : null;
    }
    /**
     * Next billing date
     *
     * @return string|null
     */
    public function getNextBillingDate()
    {
        return isset($this->attributes['next_billing_date']) ? $this->attributes['next_billing_date'] : null;
    }
    /**
     * If true, allows users to use a document editing integration.
     *
     * @return bool|null
     */
    public function getOfficeIntegrationAvailable()
    {
        return isset($this->attributes['office_integration_available']) ? $this->attributes['office_integration_available'] : null;
    }
    /**
     * Which document editing integration to support. Files.com Editor or Microsoft Office for the Web.
     *
     * @return string|null
     */
    public function getOfficeIntegrationType()
    {
        return isset($this->attributes['office_integration_type']) ? $this->attributes['office_integration_type'] : null;
    }
    /**
     * Link to scheduling a meeting with our Sales team
     *
     * @return string|null
     */
    public function getOncehubLink()
    {
        return isset($this->attributes['oncehub_link']) ? $this->attributes['oncehub_link'] : null;
    }
    /**
     * Use servers in the USA only?
     *
     * @return bool|null
     */
    public function getOptOutGlobal()
    {
        return isset($this->attributes['opt_out_global']) ? $this->attributes['opt_out_global'] : null;
    }
    /**
     * Is this site's billing overdue?
     *
     * @return bool|null
     */
    public function getOverdue()
    {
        return isset($this->attributes['overdue']) ? $this->attributes['overdue'] : null;
    }
    /**
     * Shortest password length for users
     *
     * @return int|null
     */
    public function getPasswordMinLength()
    {
        return isset($this->attributes['password_min_length']) ? $this->attributes['password_min_length'] : null;
    }
    /**
     * Require a letter in passwords?
     *
     * @return bool|null
     */
    public function getPasswordRequireLetter()
    {
        return isset($this->attributes['password_require_letter']) ? $this->attributes['password_require_letter'] : null;
    }
    /**
     * Require lower and upper case letters in passwords?
     *
     * @return bool|null
     */
    public function getPasswordRequireMixed()
    {
        return isset($this->attributes['password_require_mixed']) ? $this->attributes['password_require_mixed'] : null;
    }
    /**
     * Require a number in passwords?
     *
     * @return bool|null
     */
    public function getPasswordRequireNumber()
    {
        return isset($this->attributes['password_require_number']) ? $this->attributes['password_require_number'] : null;
    }
    /**
     * Require special characters in password?
     *
     * @return bool|null
     */
    public function getPasswordRequireSpecial()
    {
        return isset($this->attributes['password_require_special']) ? $this->attributes['password_require_special'] : null;
    }
    /**
     * Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
     *
     * @return bool|null
     */
    public function getPasswordRequireUnbreached()
    {
        return isset($this->attributes['password_require_unbreached']) ? $this->attributes['password_require_unbreached'] : null;
    }
    /**
     * Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
     *
     * @return bool|null
     */
    public function getPasswordRequirementsApplyToBundles()
    {
        return isset($this->attributes['password_requirements_apply_to_bundles']) ? $this->attributes['password_requirements_apply_to_bundles'] : null;
    }
    /**
     * Number of days password is valid
     *
     * @return int|null
     */
    public function getPasswordValidityDays()
    {
        return isset($this->attributes['password_validity_days']) ? $this->attributes['password_validity_days'] : null;
    }
    /**
     * Site phone number
     *
     * @return string|null
     */
    public function getPhone()
    {
        return isset($this->attributes['phone']) ? $this->attributes['phone'] : null;
    }
    /**
     * If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
     *
     * @return bool|null
     */
    public function getPinAllRemoteServersToSiteRegion()
    {
        return isset($this->attributes['pin_all_remote_servers_to_site_region']) ? $this->attributes['pin_all_remote_servers_to_site_region'] : null;
    }
    /**
     * If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
     *
     * @return bool|null
     */
    public function getPreventRootPermissionsForNonSiteAdmins()
    {
        return isset($this->attributes['prevent_root_permissions_for_non_site_admins']) ? $this->attributes['prevent_root_permissions_for_non_site_admins'] : null;
    }
    /**
     * If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
     *
     * @return bool|null
     */
    public function getProtocolAccessGroupsOnly()
    {
        return isset($this->attributes['protocol_access_groups_only']) ? $this->attributes['protocol_access_groups_only'] : null;
    }
    /**
     * Require two-factor authentication for all users?
     *
     * @return bool|null
     */
    public function getRequire2fa()
    {
        return isset($this->attributes['require_2fa']) ? $this->attributes['require_2fa'] : null;
    }
    /**
     * If true, only site admins may create, modify, or delete any behavior at the site root, or a skip that would disable one.
     *
     * @return bool|null
     */
    public function getRestrictRootFolderBehaviorsToSiteAdmins()
    {
        return isset($this->attributes['restrict_root_folder_behaviors_to_site_admins']) ? $this->attributes['restrict_root_folder_behaviors_to_site_admins'] : null;
    }
    /**
     * If true, supported protective behaviors at the site root also apply within named workspaces. Requires restrict_root_folder_behaviors_to_site_admins to be enabled.
     *
     * @return bool|null
     */
    public function getRootFolderBehaviorsApplyToWorkspaces()
    {
        return isset($this->attributes['root_folder_behaviors_apply_to_workspaces']) ? $this->attributes['root_folder_behaviors_apply_to_workspaces'] : null;
    }
    /**
     * If true, SSO users using the default user-level two-factor authentication setting are exempt from the site-wide two-factor authentication requirement.
     *
     * @return bool|null
     */
    public function getRequire2faExemptAllSsoUsers()
    {
        return isset($this->attributes['require_2fa_exempt_all_sso_users']) ? $this->attributes['require_2fa_exempt_all_sso_users'] : null;
    }
    /**
     * If set, requirement for two-factor authentication has been scheduled to end on this date-time.
     *
     * @return string|null
     */
    public function getRequire2faStopTime()
    {
        return isset($this->attributes['require_2fa_stop_time']) ? $this->attributes['require_2fa_stop_time'] : null;
    }
    /**
     * Auto-removes bundles for disabled/deleted users and enforces bundle expiry within user access period.
     *
     * @return bool|null
     */
    public function getRevokeBundleAccessOnDisableOrDelete()
    {
        return isset($this->attributes['revoke_bundle_access_on_disable_or_delete']) ? $this->attributes['revoke_bundle_access_on_disable_or_delete'] : null;
    }
    /**
     * What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
     *
     * @return string|null
     */
    public function getRequire2faUserType()
    {
        return isset($this->attributes['require_2fa_user_type']) ? $this->attributes['require_2fa_user_type'] : null;
    }
    /**
     * If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
     *
     * @return bool|null
     */
    public function getRequireLogoutFromBundlesAndInboxes()
    {
        return isset($this->attributes['require_logout_from_bundles_and_inboxes']) ? $this->attributes['require_logout_from_bundles_and_inboxes'] : null;
    }
    /**
     * Current session
     *
     * @return \stdClass|null
     */
    public function getSession()
    {
        return isset($this->attributes['session']) ? $this->attributes['session'] : null;
    }
    /**
     * Is the S3-compatible endpoint enabled for all users and workspaces on this site? Defaults to true. When false, user and group S3 permissions do not allow access.
     *
     * @return bool|null
     */
    public function getS3CompatibleEndpointEnabled()
    {
        return isset($this->attributes['s3_compatible_endpoint_enabled']) ? $this->attributes['s3_compatible_endpoint_enabled'] : null;
    }
    /**
     * Is SFTP enabled?
     *
     * @return bool|null
     */
    public function getSftpEnabled()
    {
        return isset($this->attributes['sftp_enabled']) ? $this->attributes['sftp_enabled'] : null;
    }
    /**
     * Finalize partial SFTP uploads from interrupted connections? Default: true.
     *
     * @return bool|null
     */
    public function getSftpFinalizePartialUploads()
    {
        return isset($this->attributes['sftp_finalize_partial_uploads']) ? $this->attributes['sftp_finalize_partial_uploads'] : null;
    }
    /**
     * Sftp Host Key Type
     *
     * @return string|null
     */
    public function getSftpHostKeyType()
    {
        return isset($this->attributes['sftp_host_key_type']) ? $this->attributes['sftp_host_key_type'] : null;
    }
    /**
     * Id of the currently selected custom SFTP Host Key
     *
     * @return int|null
     */
    public function getActiveSftpHostKeyId()
    {
        return isset($this->attributes['active_sftp_host_key_id']) ? $this->attributes['active_sftp_host_key_id'] : null;
    }
    /**
     * Ids of the selected custom SFTP Host Keys
     *
     * @return int[]|null
     */
    public function getActiveSftpHostKeyIds()
    {
        return isset($this->attributes['active_sftp_host_key_ids']) ? $this->attributes['active_sftp_host_key_ids'] : null;
    }
    /**
     * If true, we will allow weak and known insecure ciphers to be used for SFTP connections.  Enabling this setting severely weakens the security of your site and it is not recommend, except as a last resort for compatibility.
     *
     * @return bool|null
     */
    public function getSftpInsecureCiphers()
    {
        return isset($this->attributes['sftp_insecure_ciphers']) ? $this->attributes['sftp_insecure_ciphers'] : null;
    }
    /**
     * If true, we will allow weak Diffie Hellman parameters to be used within ciphers for SFTP that are otherwise on our secure list.  This has the effect of making the cipher weaker than our normal threshold for security, but is required to support certain legacy or broken SSH and MFT clients.  Enabling this weakens security, but not nearly as much as enabling the full `sftp_insecure_ciphers` option.
     *
     * @return bool|null
     */
    public function getSftpInsecureDiffieHellman()
    {
        return isset($this->attributes['sftp_insecure_diffie_hellman']) ? $this->attributes['sftp_insecure_diffie_hellman'] : null;
    }
    /**
     * Use user FTP roots also for SFTP?
     *
     * @return bool|null
     */
    public function getSftpUserRootEnabled()
    {
        return isset($this->attributes['sftp_user_root_enabled']) ? $this->attributes['sftp_user_root_enabled'] : null;
    }
    /**
     * Allow bundle creation
     *
     * @return bool|null
     */
    public function getSharingEnabled()
    {
        return isset($this->attributes['sharing_enabled']) ? $this->attributes['sharing_enabled'] : null;
    }
    /**
     * Show log in link in user notifications?
     *
     * @return bool|null
     */
    public function getShowUserNotificationsLogInLink()
    {
        return isset($this->attributes['show_user_notifications_log_in_link']) ? $this->attributes['show_user_notifications_log_in_link'] : null;
    }
    /**
     * Show request access link for users without access?  Currently unused.
     *
     * @return bool|null
     */
    public function getShowRequestAccessLink()
    {
        return isset($this->attributes['show_request_access_link']) ? $this->attributes['show_request_access_link'] : null;
    }
    /**
     * Custom site footer text for authenticated pages
     *
     * @return string|null
     */
    public function getSiteFooter()
    {
        return isset($this->attributes['site_footer']) ? $this->attributes['site_footer'] : null;
    }
    /**
     * Custom site header text for authenticated pages
     *
     * @return string|null
     */
    public function getSiteHeader()
    {
        return isset($this->attributes['site_header']) ? $this->attributes['site_header'] : null;
    }
    /**
     * Custom site footer text for public pages
     *
     * @return string|null
     */
    public function getSitePublicFooter()
    {
        return isset($this->attributes['site_public_footer']) ? $this->attributes['site_public_footer'] : null;
    }
    /**
     * Custom site header text for public pages
     *
     * @return string|null
     */
    public function getSitePublicHeader()
    {
        return isset($this->attributes['site_public_header']) ? $this->attributes['site_public_header'] : null;
    }
    /**
     * SMTP server hostname or IP
     *
     * @return string|null
     */
    public function getSmtpAddress()
    {
        return isset($this->attributes['smtp_address']) ? $this->attributes['smtp_address'] : null;
    }
    /**
     * SMTP server authentication type
     *
     * @return string|null
     */
    public function getSmtpAuthentication()
    {
        return isset($this->attributes['smtp_authentication']) ? $this->attributes['smtp_authentication'] : null;
    }
    /**
     * From address to use when mailing through custom SMTP
     *
     * @return string|null
     */
    public function getSmtpFrom()
    {
        return isset($this->attributes['smtp_from']) ? $this->attributes['smtp_from'] : null;
    }
    /**
     * SMTP server port
     *
     * @return int|null
     */
    public function getSmtpPort()
    {
        return isset($this->attributes['smtp_port']) ? $this->attributes['smtp_port'] : null;
    }
    /**
     * Custom SMTP encryption mode: if_available (default) uses STARTTLS when offered and otherwise sends credentials and messages unencrypted. A certificate-verified STARTTLS connection automatically changes if_available to require unless smtp_ssl is managed by a parent policy. require requires STARTTLS before authentication; require_implicit uses TLS from connection start; never disables TLS. TLS verifies the server certificate against smtp_address. Encryption is never automatically downgraded.
     *
     * @return string|null
     */
    public function getSmtpSsl()
    {
        return isset($this->attributes['smtp_ssl']) ? $this->attributes['smtp_ssl'] : null;
    }
    /**
     * SMTP server username
     *
     * @return string|null
     */
    public function getSmtpUsername()
    {
        return isset($this->attributes['smtp_username']) ? $this->attributes['smtp_username'] : null;
    }
    /**
     * Session expiry in minutes
     *
     * @return int|null
     */
    public function getSessionExpiryMinutes()
    {
        return isset($this->attributes['session_expiry_minutes']) ? $this->attributes['session_expiry_minutes'] : null;
    }
    /**
     * Allow snapshot share links creation
     *
     * @return bool|null
     */
    public function getSnapshotSharingEnabled()
    {
        return isset($this->attributes['snapshot_sharing_enabled']) ? $this->attributes['snapshot_sharing_enabled'] : null;
    }
    /**
     * Require TLS-protected FTP data connections to reuse the control connection TLS session? Applies to all workspaces.
     *
     * @return bool|null
     */
    public function getFtpTlsSessionReuseRequired()
    {
        return isset($this->attributes['ftp_tls_session_reuse_required']) ? $this->attributes['ftp_tls_session_reuse_required'] : null;
    }
    /**
     * Is SSL required?  Disabling this is insecure.
     *
     * @return bool|null
     */
    public function getSslRequired()
    {
        return isset($this->attributes['ssl_required']) ? $this->attributes['ssl_required'] : null;
    }
    /**
     * Site subdomain
     *
     * @return string|null
     */
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    /**
     * If switching plans, when does the new plan take effect?
     *
     * @return string|null
     */
    public function getSwitchToPlanDate()
    {
        return isset($this->attributes['switch_to_plan_date']) ? $this->attributes['switch_to_plan_date'] : null;
    }
    /**
     * Number of days left in trial
     *
     * @return int|null
     */
    public function getTrialDaysLeft()
    {
        return isset($this->attributes['trial_days_left']) ? $this->attributes['trial_days_left'] : null;
    }
    /**
     * When does this Site trial expire?
     *
     * @return string|null
     */
    public function getTrialUntil()
    {
        return isset($this->attributes['trial_until']) ? $this->attributes['trial_until'] : null;
    }
    /**
     * If using custom SMTP, should we use dedicated IPs to deliver emails?
     *
     * @return bool|null
     */
    public function getUseDedicatedIpsForSmtp()
    {
        return isset($this->attributes['use_dedicated_ips_for_smtp']) ? $this->attributes['use_dedicated_ips_for_smtp'] : null;
    }
    /**
     * Allow uploaders to set `provided_modified_at` for uploaded files?
     *
     * @return bool|null
     */
    public function getUseProvidedModifiedAt()
    {
        return isset($this->attributes['use_provided_modified_at']) ? $this->attributes['use_provided_modified_at'] : null;
    }
    /**
     * User of current session
     *
     * @return \stdClass|null
     */
    public function getUser()
    {
        return isset($this->attributes['user']) ? $this->attributes['user'] : null;
    }
    /**
     * Will users be locked out after incorrect login attempts?
     *
     * @return bool|null
     */
    public function getUserLockout()
    {
        return isset($this->attributes['user_lockout']) ? $this->attributes['user_lockout'] : null;
    }
    /**
     * How many hours to lock user out for failed password?
     *
     * @return int|null
     */
    public function getUserLockoutLockPeriod()
    {
        return isset($this->attributes['user_lockout_lock_period']) ? $this->attributes['user_lockout_lock_period'] : null;
    }
    /**
     * Number of login tries within `user_lockout_within` hours before users are locked out
     *
     * @return int|null
     */
    public function getUserLockoutTries()
    {
        return isset($this->attributes['user_lockout_tries']) ? $this->attributes['user_lockout_tries'] : null;
    }
    /**
     * Number of hours for user lockout window
     *
     * @return int|null
     */
    public function getUserLockoutWithin()
    {
        return isset($this->attributes['user_lockout_within']) ? $this->attributes['user_lockout_within'] : null;
    }
    /**
     * Enable User Requests feature
     *
     * @return bool|null
     */
    public function getUserRequestsEnabled()
    {
        return isset($this->attributes['user_requests_enabled']) ? $this->attributes['user_requests_enabled'] : null;
    }
    /**
     * Send email to site admins when a user request is received?
     *
     * @return bool|null
     */
    public function getUserRequestsNotifyAdmins()
    {
        return isset($this->attributes['user_requests_notify_admins']) ? $this->attributes['user_requests_notify_admins'] : null;
    }
    /**
     * Allow users to create their own API keys?
     *
     * @return bool|null
     */
    public function getUsersCanCreateApiKeys()
    {
        return isset($this->attributes['users_can_create_api_keys']) ? $this->attributes['users_can_create_api_keys'] : null;
    }
    /**
     * Allow users to create their own SSH keys?
     *
     * @return bool|null
     */
    public function getUsersCanCreateSshKeys()
    {
        return isset($this->attributes['users_can_create_ssh_keys']) ? $this->attributes['users_can_create_ssh_keys'] : null;
    }
    /**
     * How usernames are displayed in the web UI. Can be `username_only`, `full_name_only`, `full_name_username`, `full_name_company`, or `full_name_username_company`.
     *
     * @return string|null
     */
    public function getUsernameDisplay()
    {
        return isset($this->attributes['username_display']) ? $this->attributes['username_display'] : null;
    }
    /**
     * Custom text send in user welcome email
     *
     * @return string|null
     */
    public function getWelcomeCustomText()
    {
        return isset($this->attributes['welcome_custom_text']) ? $this->attributes['welcome_custom_text'] : null;
    }
    /**
     * Custom footer text for system-generated emails (as Markdown). Supports standard strftime date/time patterns like %Y (4-digit year), %m (month), %d (day).
     *
     * @return string|null
     */
    public function getEmailFooterCustomText()
    {
        return isset($this->attributes['email_footer_custom_text']) ? $this->attributes['email_footer_custom_text'] : null;
    }
    /**
     * Include this email in welcome emails if enabled
     *
     * @return string|null
     */
    public function getWelcomeEmailCc()
    {
        return isset($this->attributes['welcome_email_cc']) ? $this->attributes['welcome_email_cc'] : null;
    }
    /**
     * Include this email subject in welcome emails if enabled
     *
     * @return string|null
     */
    public function getWelcomeEmailSubject()
    {
        return isset($this->attributes['welcome_email_subject']) ? $this->attributes['welcome_email_subject'] : null;
    }
    /**
     * Will the welcome email be sent to new users?
     *
     * @return bool|null
     */
    public function getWelcomeEmailEnabled()
    {
        return isset($this->attributes['welcome_email_enabled']) ? $this->attributes['welcome_email_enabled'] : null;
    }
    /**
     * Does the welcome screen appear?
     *
     * @return string|null
     */
    public function getWelcomeScreen()
    {
        return isset($this->attributes['welcome_screen']) ? $this->attributes['welcome_screen'] : null;
    }
    /**
     * Does FTP user Windows emulation mode?
     *
     * @return bool|null
     */
    public function getWindowsModeFtp()
    {
        return isset($this->attributes['windows_mode_ftp']) ? $this->attributes['windows_mode_ftp'] : null;
    }

    /**
     * Show Site Settings
     *
     * GET /site
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Site
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($params = [], $options = [])
    {
        $response = Api::sendRequest('/site', 'GET', null, $options);

        return new Site(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Get the most recent usage snapshot (usage data for billing purposes) for a Site
     *
     * GET /site/usage
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UsageSnapshot
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function getUsage($params = [], $options = [])
    {
        $response = Api::sendRequest('/site/usage', 'GET', null, $options);

        return new UsageSnapshot(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Extend the Automations conversion window by up to six weeks beyond its originally announced end date
     *
     * POST /site/extend_automation_migration_window
     *
     * @param array $params Request parameters.
     *     - `end_date` (string, required): New conversion-window end date in YYYY-MM-DD format, in Eastern Time (EST/EDT). Must be a Tuesday, Wednesday, or Thursday. Can only extend the current end date, within six weeks of the originally announced end date.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function extendAutomationMigrationWindow($params = [], $options = [])
    {
        if (empty($params['end_date'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: end_date');
        }

        if (isset($params['end_date']) && !is_string($params['end_date'])) {
            throw new \Files\Exception\InvalidParameterException('$end_date must be of type string; received ' . gettype(@$params['end_date']));
        }

        $response = Api::sendRequest('/site/extend_automation_migration_window', 'POST', $params, $options);

        return;
    }

    /**
     * Update Site Settings
     *
     * PATCH /site
     *
     * @param array $params Request parameters.
     *     - `name` (string, optional): Site name
     *     - `subdomain` (string, optional): Site subdomain
     *     - `domain` (string, optional): Custom domain
     *     - `domain_hsts_header` (bool, optional): Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
     *     - `domain_letsencrypt_chain` (string, optional): Letsencrypt chain to use when registering SSL Certificate for domain. No longer used as of 2026.
     *     - `email` (string, optional): Main email for this site
     *     - `reply_to_email` (string, optional): Reply-to email for this site
     *     - `allow_bundle_names` (bool, optional): Are manual Bundle names allowed?
     *     - `bundle_expiration` (int, optional): Site-wide Bundle expiration in days
     *     - `welcome_email_enabled` (bool, optional): Will the welcome email be sent to new users?
     *     - `ask_about_overwrites` (bool, optional): If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
     *     - `show_request_access_link` (bool, optional): Show request access link for users without access?  Currently unused.
     *     - `always_mkdir_parents` (bool, optional): Create parent directories if they do not exist during uploads?  This is primarily used to work around broken upload clients that assume servers will perform this step.
     *     - `welcome_email_cc` (string, optional): Include this email in welcome emails if enabled
     *     - `welcome_email_subject` (string, optional): Include this email subject in welcome emails if enabled
     *     - `welcome_custom_text` (string, optional): Custom text send in user welcome email
     *     - `language` (string, optional): Site default language
     *     - `windows_mode_ftp` (bool, optional): Does FTP user Windows emulation mode?
     *     - `default_time_zone` (string, optional): Site default time zone
     *     - `desktop_app` (bool, optional): Is the desktop app enabled?
     *     - `desktop_app_session_ip_pinning` (bool, optional): Is desktop app session IP pinning enabled?
     *     - `desktop_app_session_lifetime` (int, optional): Desktop app session lifetime (in hours)
     *     - `mobile_app` (bool, optional): Is the mobile app enabled?
     *     - `mobile_app_session_ip_pinning` (bool, optional): Is mobile app session IP pinning enabled?
     *     - `mobile_app_session_lifetime` (int, optional): Mobile app session lifetime (in hours)
     *     - `folder_permissions_groups_only` (bool, optional): If true, permissions for this site must be bound to a group (not a user).
     *     - `welcome_screen` (string, optional): Does the welcome screen appear?
     *     - `office_integration_available` (bool, optional): If true, allows users to use a document editing integration.
     *     - `office_integration_type` (string, optional): Which document editing integration to support. Files.com Editor or Microsoft Office for the Web.
     *     - `pin_all_remote_servers_to_site_region` (bool, optional): If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
     *     - `motd_text` (string, optional): A message to show users when they connect via FTP or SFTP.
     *     - `motd_use_for_ftp` (bool, optional): Show message to users connecting via FTP
     *     - `motd_use_for_sftp` (bool, optional): Show message to users connecting via SFTP
     *     - `left_navigation_visibility` (array|object, optional): Visibility settings for account navigation
     *     - `disable_all_ai_features` (bool, optional): If true, all AI features are disabled for this site.
     *     - `ai_feature_availability` (array|object, optional): Availability settings for AI features. Each feature requires the site_admins, workspace_admins, folder_admins, and all_users keys. Optional selected_group_members defaults to false; when true, members of at least one group in group_ids get access regardless of the other options. All availability options are additive: any enabled option matching the user grants access. Optional group_ids is an array of integer IDs of active groups on this site, from any workspace. Omitted or empty group_ids grants no access through selected_group_members and does not affect other options. Disabling all AI features overrides these settings.
     *     - `mcp_dcr_enabled` (bool, optional): Is OAuth DCR (dynamic client registration) for MCP enabled?
     *     - `additional_text_file_types` (string[], optional): Additional extensions that are considered text files
     *     - `bundle_require_note` (bool, optional): Do Bundles require internal notes?
     *     - `bundle_send_shared_receipts` (bool, optional): Do Bundle creators receive receipts of invitations?
     *     - `bundles_default_owned_by_primary_group` (bool, optional): If true, new Share Links created by a user with a primary group will default to that group as owner.
     *     - `calculate_file_checksums_crc32` (bool, optional): Calculate CRC32 checksums for files?
     *     - `calculate_file_checksums_md5` (bool, optional): Calculate MD5 checksums for files?
     *     - `calculate_file_checksums_sha1` (bool, optional): Calculate SHA1 checksums for files?
     *     - `calculate_file_checksums_sha256` (bool, optional): Calculate SHA256 checksums for files?
     *     - `legacy_checksums_mode` (bool, optional): Use legacy checksums mode?
     *     - `migrate_remote_server_sync_to_sync` (bool, optional): If true, we will migrate all remote server syncs to the new Sync model.
     *     - `as2_message_retention_days` (int, optional): Number of days to retain AS2 messages (incoming and outgoing).
     *     - `file_activity_events_enabled` (bool, optional): If true, file operations produce file_action.activity.v1 EventChannel events in all workspaces. Defaults to false; subscription filters and workspace access still control delivery.
     *     - `username_display` (string, optional): How usernames are displayed in the web UI. Can be `username_only`, `full_name_only`, `full_name_username`, `full_name_company`, or `full_name_username_company`.
     *     - `integration_centric_remote_server_admin_access` (string, optional): Administrator access to user-owned Remote Servers in every Workspace: always_visible (default), never_visible, user_choice_default_visible, or user_choice_default_personal. Forced modes override owner preferences; choice modes use the preference or their default.
     *     - `session_expiry_minutes` (int, optional): Session expiry in minutes
     *     - `legacy_pairing_enabled` (bool, optional): Allow legacy app pairing in all workspaces? Disabled for new sites; existing sites remain enabled until changed. Disabling blocks new pairing exchanges without revoking existing app credentials or affecting OAuth sign-in. Site administrators can re-enable it.
     *     - `ssl_required` (bool, optional): Is SSL required?  Disabling this is insecure.
     *     - `ftp_tls_session_reuse_required` (bool, optional): Require TLS-protected FTP data connections to reuse the control connection TLS session? Applies to all workspaces.
     *     - `sftp_insecure_ciphers` (bool, optional): If true, we will allow weak and known insecure ciphers to be used for SFTP connections.  Enabling this setting severely weakens the security of your site and it is not recommend, except as a last resort for compatibility.
     *     - `sftp_insecure_diffie_hellman` (bool, optional): If true, we will allow weak Diffie Hellman parameters to be used within ciphers for SFTP that are otherwise on our secure list.  This has the effect of making the cipher weaker than our normal threshold for security, but is required to support certain legacy or broken SSH and MFT clients.  Enabling this weakens security, but not nearly as much as enabling the full `sftp_insecure_ciphers` option.
     *     - `disable_files_certificate_generation` (bool, optional): If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
     *     - `fedramp` (bool, optional): Are FedRAMP security restrictions enabled for this site?
     *     - `user_lockout` (bool, optional): Will users be locked out after incorrect login attempts?
     *     - `user_lockout_tries` (int, optional): Number of login tries within `user_lockout_within` hours before users are locked out
     *     - `user_lockout_within` (int, optional): Number of hours for user lockout window
     *     - `user_lockout_lock_period` (int, optional): How many hours to lock user out for failed password?
     *     - `include_password_in_welcome_email` (bool, optional): Include password in emails to new users?
     *     - `allowed_countries` (string, optional): Comma separated list of allowed Country codes
     *     - `allowed_ips` (string, optional): List of allowed IP addresses
     *     - `allow_user_level_2fa_override` (bool, optional): Allow the site-wide two-factor authentication requirement to be overriden on a per-user-basis?
     *     - `allow_user_level_allowed_ip_override` (bool, optional): Allow the site-wide allowed IP restriction to be overriden on a per-user-basis?
     *     - `allow_user_level_ssl_override` (bool, optional): Allow the site-wide FTP SSL requirement to be overriden on a per-user-basis?
     *     - `allow_user_level_ftp_tls_session_reuse_override` (bool, optional): Allow per-user overrides of the FTP TLS session reuse requirement?
     *     - `enforce_parent_site_user_ip_restrictions` (bool, optional): Require Parent Site users entering this Child Site to match its allowed IP addresses in addition to their existing IP restrictions. Applies to all Workspaces. Defaults to false.
     *     - `disallowed_countries` (string, optional): Comma separated list of disallowed Country codes
     *     - `days_to_retain_backups` (int, optional): Number of days to keep deleted files
     *     - `max_prior_passwords` (int, optional): Number of prior passwords to disallow
     *     - `password_validity_days` (int, optional): Number of days password is valid
     *     - `password_min_length` (int, optional): Shortest password length for users
     *     - `password_require_letter` (bool, optional): Require a letter in passwords?
     *     - `password_require_mixed` (bool, optional): Require lower and upper case letters in passwords?
     *     - `password_require_special` (bool, optional): Require special characters in password?
     *     - `password_require_number` (bool, optional): Require a number in passwords?
     *     - `password_require_unbreached` (bool, optional): Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
     *     - `require_logout_from_bundles_and_inboxes` (bool, optional): If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
     *     - `dav_user_root_enabled` (bool, optional): Use user FTP roots also for WebDAV?
     *     - `sftp_user_root_enabled` (bool, optional): Use user FTP roots also for SFTP?
     *     - `disable_password_reset` (bool, optional): Is password reset disabled?
     *     - `immutable_files` (bool, optional): Are files protected from modification?
     *     - `bundle_not_found_message` (string, optional): Custom error message to show when bundle is not found.
     *     - `bundle_password_required` (bool, optional): Do Bundles require password protection?
     *     - `bundle_require_registration` (bool, optional): Do Bundles require registration?
     *     - `bundle_require_share_recipient` (bool, optional): Do Bundles require recipients for sharing?
     *     - `bundle_send_one_time_password_to_recipient_at_registration` (bool, optional): If true, new Share Links must send a one-time password to the recipient when they register. Requires bundle_require_share_recipient and cannot be enabled with bundle_password_required.
     *     - `bundle_registration_notifications` (string, optional): Do Bundle owners receive registration notification?
     *     - `bundle_activity_notifications` (string, optional): Do Bundle owners receive activity notifications?
     *     - `bundle_upload_receipt_notifications` (string, optional): Do Bundle uploaders receive upload confirmation notifications?
     *     - `document_edits_in_bundle_allowed` (bool, optional): If true, allow public viewers of Bundles with full permissions to use document editing integrations.
     *     - `password_requirements_apply_to_bundles` (bool, optional): Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
     *     - `prevent_root_permissions_for_non_site_admins` (bool, optional): If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
     *     - `restrict_root_folder_behaviors_to_site_admins` (bool, optional): If true, only site admins may create, modify, or delete any behavior at the site root, or a skip that would disable one.
     *     - `root_folder_behaviors_apply_to_workspaces` (bool, optional): If true, supported protective behaviors at the site root also apply within named workspaces. Requires restrict_root_folder_behaviors_to_site_admins to be enabled.
     *     - `opt_out_global` (bool, optional): Use servers in the USA only?
     *     - `use_provided_modified_at` (bool, optional): Allow uploaders to set `provided_modified_at` for uploaded files?
     *     - `custom_namespace` (bool, optional): Is this site using a custom namespace for users?
     *     - `non_sso_groups_allowed` (bool, optional): If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
     *     - `non_sso_users_allowed` (bool, optional): If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
     *     - `sharing_enabled` (bool, optional): Allow bundle creation
     *     - `snapshot_sharing_enabled` (bool, optional): Allow snapshot share links creation
     *     - `user_requests_enabled` (bool, optional): Enable User Requests feature
     *     - `user_requests_notify_admins` (bool, optional): Send email to site admins when a user request is received?
     *     - `dav_enabled` (bool, optional): Is WebDAV enabled?
     *     - `files_com_remote_server_enabled` (bool, optional): Allow other Files.com sites to use this site's API keys for native Files.com Remote Server connections? Defaults to true and applies to all keys and workspaces on this site. When false, new pairings and access through existing connections are rejected without revoking keys or deleting connections. Re-enabling permits access again with usable keys. Does not disable this site's connections to other sites, ordinary API access, or Connected Sites.
     *     - `ftp_enabled` (bool, optional): Is FTP enabled?
     *     - `s3_compatible_endpoint_enabled` (bool, optional): Is the S3-compatible endpoint enabled for all users and workspaces on this site? Defaults to true. When false, user and group S3 permissions do not allow access.
     *     - `sftp_enabled` (bool, optional): Is SFTP enabled?
     *     - `sftp_finalize_partial_uploads` (bool, optional): Finalize partial SFTP uploads from interrupted connections? Default: true.
     *     - `users_can_create_api_keys` (bool, optional): Allow users to create their own API keys?
     *     - `users_can_create_ssh_keys` (bool, optional): Allow users to create their own SSH keys?
     *     - `show_user_notifications_log_in_link` (bool, optional): Show log in link in user notifications?
     *     - `sftp_host_key_type` (string, optional): Sftp Host Key Type
     *     - `active_sftp_host_key_id` (int, optional): Id of the currently selected custom SFTP Host Key
     *     - `active_sftp_host_key_ids` (int[], optional): Ids of the selected custom SFTP Host Keys
     *     - `protocol_access_groups_only` (bool, optional): If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
     *     - `revoke_bundle_access_on_disable_or_delete` (bool, optional): Auto-removes bundles for disabled/deleted users and enforces bundle expiry within user access period.
     *     - `group_admins_can_add_users` (bool, optional): Allow group admins to create users in their groups
     *     - `group_admins_can_manage_group_memberships` (bool, optional): Allow group admins to add or remove existing users in their groups
     *     - `group_admins_can_delete_users` (bool, optional): Allow group admins to delete users in their groups
     *     - `group_admins_can_enable_disable_users` (bool, optional): Allow group admins to enable or disable users in their groups
     *     - `group_admins_can_modify_users` (bool, optional): Allow group admins to modify users in their groups
     *     - `group_admins_can_bypass_user_lifecycle_rules` (bool, optional): Allow group admins to exempt users in their groups from lifecycle rules
     *     - `group_admins_can_reset_passwords` (bool, optional): Allow group admins to reset passwords for users in their groups
     *     - `group_admins_can_set_user_password` (bool, optional): Allow group admins to set password authentication method
     *     - `child_site_folder_admins_can_see_parent_site_directory` (bool, optional): If true, administrators on this site's Child Sites, including Folder Admins, can list this site's Default Workspace users and groups and grant them folder permissions and notifications on Child Site paths.
     *     - `workspace_folder_admins_can_see_default_workspace_directory` (bool, optional): If true, Workspace Administrators and Folder Admins in a Custom Workspace can list the Default Workspace users and groups that have permissions in that Workspace and choose them as Email Notification recipients.
     *     - `bundle_recipient_blacklist_free_email_domains` (bool, optional): Disallow free email domains for Bundle/Inbox recipients?
     *     - `bundle_recipient_blacklist_domains` (string[], optional): List of email domains to disallow when entering a Bundle/Inbox recipients
     *     - `admins_bypass_locked_subfolders` (bool, optional): Allow admins to bypass the locked subfolders setting.
     *     - `allowed_2fa_method_sms` (bool, optional): Is SMS two factor authentication allowed?
     *     - `allowed_2fa_method_totp` (bool, optional): Is TOTP two factor authentication allowed?
     *     - `allowed_2fa_method_webauthn` (bool, optional): Is WebAuthn two factor authentication allowed?
     *     - `allowed_2fa_method_yubi` (bool, optional): Is yubikey two factor authentication allowed?
     *     - `allowed_2fa_method_email` (bool, optional): Is OTP via email two factor authentication allowed?
     *     - `allowed_2fa_method_static` (bool, optional): Is OTP via static codes for two factor authentication allowed?
     *     - `allowed_2fa_method_bypass_for_ftp_sftp_dav` (bool, optional): Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
     *     - `require_2fa` (bool, optional): Require two-factor authentication for all users?
     *     - `require_2fa_exempt_all_sso_users` (bool, optional): If true, SSO users using the default user-level two-factor authentication setting are exempt from the site-wide two-factor authentication requirement.
     *     - `require_2fa_user_type` (string, optional): What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
     *     - `color2_top` (string, optional): Top bar background color
     *     - `color2_left` (string, optional): Page link and button color
     *     - `color2_link` (string, optional): Top bar link color
     *     - `color2_text` (string, optional): Page link and button color
     *     - `color2_top_text` (string, optional): Top bar text color
     *     - `site_header` (string, optional): Custom site header text for authenticated pages
     *     - `site_footer` (string, optional): Custom site footer text for authenticated pages
     *     - `site_public_header` (string, optional): Custom site header text for public pages
     *     - `site_public_footer` (string, optional): Custom site footer text for public pages
     *     - `login_help_text` (string, optional): Login help text
     *     - `use_dedicated_ips_for_smtp` (bool, optional): If using custom SMTP, should we use dedicated IPs to deliver emails?
     *     - `email_footer_custom_text` (string, optional): Custom footer text for system-generated emails (as Markdown). Supports standard strftime date/time patterns like %Y (4-digit year), %m (month), %d (day).
     *     - `smtp_address` (string, optional): SMTP server hostname or IP
     *     - `smtp_authentication` (string, optional): SMTP server authentication type
     *     - `smtp_from` (string, optional): From address to use when mailing through custom SMTP
     *     - `smtp_username` (string, optional): SMTP server username
     *     - `smtp_port` (int, optional): SMTP server port
     *     - `smtp_ssl` (string, optional): Custom SMTP encryption mode: if_available (default) uses STARTTLS when offered and otherwise sends credentials and messages unencrypted. A certificate-verified STARTTLS connection automatically changes if_available to require unless smtp_ssl is managed by a parent policy. require requires STARTTLS before authentication; require_implicit uses TLS from connection start; never disables TLS. TLS verifies the server certificate against smtp_address. Encryption is never automatically downgraded.
     *     - `uploads_via_email_authentication` (bool, optional): Require email authentication, virus, and spam checks for incoming emails to Inboxes and Incoming Email Automations in every Workspace on this site?
     *     - `bundle_watermark_value` (array|object, optional): Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     *     - `icon16_file` (mixed, optional):
     *     - `icon16_delete` (bool, optional): If true, will delete the file stored in icon16
     *     - `icon32_file` (mixed, optional):
     *     - `icon32_delete` (bool, optional): If true, will delete the file stored in icon32
     *     - `icon48_file` (mixed, optional):
     *     - `icon48_delete` (bool, optional): If true, will delete the file stored in icon48
     *     - `icon128_file` (mixed, optional):
     *     - `icon128_delete` (bool, optional): If true, will delete the file stored in icon128
     *     - `logo_file` (mixed, optional):
     *     - `logo_delete` (bool, optional): If true, will delete the file stored in logo
     *     - `bundle_watermark_attachment_file` (mixed, optional):
     *     - `bundle_watermark_attachment_delete` (bool, optional): If true, will delete the file stored in bundle_watermark_attachment
     *     - `login_page_background_image_file` (mixed, optional):
     *     - `login_page_background_image_delete` (bool, optional): If true, will delete the file stored in login_page_background_image
     *     - `disable_2fa_with_delay` (bool, optional): If set to true, we will begin the process of disabling 2FA on this site.
     *     - `redirect_old_subdomain` (bool, optional): If true, and if changing the site subdomain, then create a redirect from the previous Files.com subdomain to the new Files.com subdomain.
     *     - `smtp_password` (string, optional): Password for SMTP server.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Site
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function update($params = [], $options = [])
    {
        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['subdomain']) && !is_string($params['subdomain'])) {
            throw new \Files\Exception\InvalidParameterException('$subdomain must be of type string; received ' . gettype(@$params['subdomain']));
        }

        if (isset($params['domain']) && !is_string($params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        if (isset($params['domain_hsts_header']) && !is_scalar($params['domain_hsts_header'])) {
            throw new \Files\Exception\InvalidParameterException('$domain_hsts_header must be of type boolean; received ' . gettype(@$params['domain_hsts_header']));
        }

        if (isset($params['domain_letsencrypt_chain']) && !is_string($params['domain_letsencrypt_chain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain_letsencrypt_chain must be of type string; received ' . gettype(@$params['domain_letsencrypt_chain']));
        }

        if (isset($params['email']) && !is_string($params['email'])) {
            throw new \Files\Exception\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
        }

        if (isset($params['reply_to_email']) && !is_string($params['reply_to_email'])) {
            throw new \Files\Exception\InvalidParameterException('$reply_to_email must be of type string; received ' . gettype(@$params['reply_to_email']));
        }

        if (isset($params['allow_bundle_names']) && !is_scalar($params['allow_bundle_names'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_bundle_names must be of type boolean; received ' . gettype(@$params['allow_bundle_names']));
        }

        if (isset($params['bundle_expiration']) && !is_int($params['bundle_expiration'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_expiration must be of type int; received ' . gettype(@$params['bundle_expiration']));
        }

        if (isset($params['welcome_email_enabled']) && !is_scalar($params['welcome_email_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_email_enabled must be of type boolean; received ' . gettype(@$params['welcome_email_enabled']));
        }

        if (isset($params['ask_about_overwrites']) && !is_scalar($params['ask_about_overwrites'])) {
            throw new \Files\Exception\InvalidParameterException('$ask_about_overwrites must be of type boolean; received ' . gettype(@$params['ask_about_overwrites']));
        }

        if (isset($params['show_request_access_link']) && !is_scalar($params['show_request_access_link'])) {
            throw new \Files\Exception\InvalidParameterException('$show_request_access_link must be of type boolean; received ' . gettype(@$params['show_request_access_link']));
        }

        if (isset($params['always_mkdir_parents']) && !is_scalar($params['always_mkdir_parents'])) {
            throw new \Files\Exception\InvalidParameterException('$always_mkdir_parents must be of type boolean; received ' . gettype(@$params['always_mkdir_parents']));
        }

        if (isset($params['welcome_email_cc']) && !is_string($params['welcome_email_cc'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_email_cc must be of type string; received ' . gettype(@$params['welcome_email_cc']));
        }

        if (isset($params['welcome_email_subject']) && !is_string($params['welcome_email_subject'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_email_subject must be of type string; received ' . gettype(@$params['welcome_email_subject']));
        }

        if (isset($params['welcome_custom_text']) && !is_string($params['welcome_custom_text'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_custom_text must be of type string; received ' . gettype(@$params['welcome_custom_text']));
        }

        if (isset($params['language']) && !is_string($params['language'])) {
            throw new \Files\Exception\InvalidParameterException('$language must be of type string; received ' . gettype(@$params['language']));
        }

        if (isset($params['windows_mode_ftp']) && !is_scalar($params['windows_mode_ftp'])) {
            throw new \Files\Exception\InvalidParameterException('$windows_mode_ftp must be of type boolean; received ' . gettype(@$params['windows_mode_ftp']));
        }

        if (isset($params['default_time_zone']) && !is_string($params['default_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$default_time_zone must be of type string; received ' . gettype(@$params['default_time_zone']));
        }

        if (isset($params['desktop_app']) && !is_scalar($params['desktop_app'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_app must be of type boolean; received ' . gettype(@$params['desktop_app']));
        }

        if (isset($params['desktop_app_session_ip_pinning']) && !is_scalar($params['desktop_app_session_ip_pinning'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_app_session_ip_pinning must be of type boolean; received ' . gettype(@$params['desktop_app_session_ip_pinning']));
        }

        if (isset($params['desktop_app_session_lifetime']) && !is_int($params['desktop_app_session_lifetime'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_app_session_lifetime must be of type int; received ' . gettype(@$params['desktop_app_session_lifetime']));
        }

        if (isset($params['mobile_app']) && !is_scalar($params['mobile_app'])) {
            throw new \Files\Exception\InvalidParameterException('$mobile_app must be of type boolean; received ' . gettype(@$params['mobile_app']));
        }

        if (isset($params['mobile_app_session_ip_pinning']) && !is_scalar($params['mobile_app_session_ip_pinning'])) {
            throw new \Files\Exception\InvalidParameterException('$mobile_app_session_ip_pinning must be of type boolean; received ' . gettype(@$params['mobile_app_session_ip_pinning']));
        }

        if (isset($params['mobile_app_session_lifetime']) && !is_int($params['mobile_app_session_lifetime'])) {
            throw new \Files\Exception\InvalidParameterException('$mobile_app_session_lifetime must be of type int; received ' . gettype(@$params['mobile_app_session_lifetime']));
        }

        if (isset($params['folder_permissions_groups_only']) && !is_scalar($params['folder_permissions_groups_only'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_permissions_groups_only must be of type boolean; received ' . gettype(@$params['folder_permissions_groups_only']));
        }

        if (isset($params['welcome_screen']) && !is_string($params['welcome_screen'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_screen must be of type string; received ' . gettype(@$params['welcome_screen']));
        }

        if (isset($params['office_integration_available']) && !is_scalar($params['office_integration_available'])) {
            throw new \Files\Exception\InvalidParameterException('$office_integration_available must be of type boolean; received ' . gettype(@$params['office_integration_available']));
        }

        if (isset($params['office_integration_type']) && !is_string($params['office_integration_type'])) {
            throw new \Files\Exception\InvalidParameterException('$office_integration_type must be of type string; received ' . gettype(@$params['office_integration_type']));
        }

        if (isset($params['pin_all_remote_servers_to_site_region']) && !is_scalar($params['pin_all_remote_servers_to_site_region'])) {
            throw new \Files\Exception\InvalidParameterException('$pin_all_remote_servers_to_site_region must be of type boolean; received ' . gettype(@$params['pin_all_remote_servers_to_site_region']));
        }

        if (isset($params['motd_text']) && !is_string($params['motd_text'])) {
            throw new \Files\Exception\InvalidParameterException('$motd_text must be of type string; received ' . gettype(@$params['motd_text']));
        }

        if (isset($params['motd_use_for_ftp']) && !is_scalar($params['motd_use_for_ftp'])) {
            throw new \Files\Exception\InvalidParameterException('$motd_use_for_ftp must be of type boolean; received ' . gettype(@$params['motd_use_for_ftp']));
        }

        if (isset($params['motd_use_for_sftp']) && !is_scalar($params['motd_use_for_sftp'])) {
            throw new \Files\Exception\InvalidParameterException('$motd_use_for_sftp must be of type boolean; received ' . gettype(@$params['motd_use_for_sftp']));
        }

        if (isset($params['disable_all_ai_features']) && !is_scalar($params['disable_all_ai_features'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_all_ai_features must be of type boolean; received ' . gettype(@$params['disable_all_ai_features']));
        }

        if (isset($params['mcp_dcr_enabled']) && !is_scalar($params['mcp_dcr_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$mcp_dcr_enabled must be of type boolean; received ' . gettype(@$params['mcp_dcr_enabled']));
        }

        if (isset($params['additional_text_file_types']) && !is_array($params['additional_text_file_types'])) {
            throw new \Files\Exception\InvalidParameterException('$additional_text_file_types must be of type array; received ' . gettype(@$params['additional_text_file_types']));
        }

        if (isset($params['bundle_require_note']) && !is_scalar($params['bundle_require_note'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_require_note must be of type boolean; received ' . gettype(@$params['bundle_require_note']));
        }

        if (isset($params['bundle_send_shared_receipts']) && !is_scalar($params['bundle_send_shared_receipts'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_send_shared_receipts must be of type boolean; received ' . gettype(@$params['bundle_send_shared_receipts']));
        }

        if (isset($params['bundles_default_owned_by_primary_group']) && !is_scalar($params['bundles_default_owned_by_primary_group'])) {
            throw new \Files\Exception\InvalidParameterException('$bundles_default_owned_by_primary_group must be of type boolean; received ' . gettype(@$params['bundles_default_owned_by_primary_group']));
        }

        if (isset($params['calculate_file_checksums_crc32']) && !is_scalar($params['calculate_file_checksums_crc32'])) {
            throw new \Files\Exception\InvalidParameterException('$calculate_file_checksums_crc32 must be of type boolean; received ' . gettype(@$params['calculate_file_checksums_crc32']));
        }

        if (isset($params['calculate_file_checksums_md5']) && !is_scalar($params['calculate_file_checksums_md5'])) {
            throw new \Files\Exception\InvalidParameterException('$calculate_file_checksums_md5 must be of type boolean; received ' . gettype(@$params['calculate_file_checksums_md5']));
        }

        if (isset($params['calculate_file_checksums_sha1']) && !is_scalar($params['calculate_file_checksums_sha1'])) {
            throw new \Files\Exception\InvalidParameterException('$calculate_file_checksums_sha1 must be of type boolean; received ' . gettype(@$params['calculate_file_checksums_sha1']));
        }

        if (isset($params['calculate_file_checksums_sha256']) && !is_scalar($params['calculate_file_checksums_sha256'])) {
            throw new \Files\Exception\InvalidParameterException('$calculate_file_checksums_sha256 must be of type boolean; received ' . gettype(@$params['calculate_file_checksums_sha256']));
        }

        if (isset($params['legacy_checksums_mode']) && !is_scalar($params['legacy_checksums_mode'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_checksums_mode must be of type boolean; received ' . gettype(@$params['legacy_checksums_mode']));
        }

        if (isset($params['migrate_remote_server_sync_to_sync']) && !is_scalar($params['migrate_remote_server_sync_to_sync'])) {
            throw new \Files\Exception\InvalidParameterException('$migrate_remote_server_sync_to_sync must be of type boolean; received ' . gettype(@$params['migrate_remote_server_sync_to_sync']));
        }

        if (isset($params['as2_message_retention_days']) && !is_int($params['as2_message_retention_days'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_message_retention_days must be of type int; received ' . gettype(@$params['as2_message_retention_days']));
        }

        if (isset($params['file_activity_events_enabled']) && !is_scalar($params['file_activity_events_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$file_activity_events_enabled must be of type boolean; received ' . gettype(@$params['file_activity_events_enabled']));
        }

        if (isset($params['username_display']) && !is_string($params['username_display'])) {
            throw new \Files\Exception\InvalidParameterException('$username_display must be of type string; received ' . gettype(@$params['username_display']));
        }

        if (isset($params['integration_centric_remote_server_admin_access']) && !is_string($params['integration_centric_remote_server_admin_access'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_remote_server_admin_access must be of type string; received ' . gettype(@$params['integration_centric_remote_server_admin_access']));
        }

        if (isset($params['session_expiry_minutes']) && !is_int($params['session_expiry_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$session_expiry_minutes must be of type int; received ' . gettype(@$params['session_expiry_minutes']));
        }

        if (isset($params['legacy_pairing_enabled']) && !is_scalar($params['legacy_pairing_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_pairing_enabled must be of type boolean; received ' . gettype(@$params['legacy_pairing_enabled']));
        }

        if (isset($params['ssl_required']) && !is_scalar($params['ssl_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_required must be of type boolean; received ' . gettype(@$params['ssl_required']));
        }

        if (isset($params['ftp_tls_session_reuse_required']) && !is_scalar($params['ftp_tls_session_reuse_required'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_tls_session_reuse_required must be of type boolean; received ' . gettype(@$params['ftp_tls_session_reuse_required']));
        }

        if (isset($params['sftp_insecure_ciphers']) && !is_scalar($params['sftp_insecure_ciphers'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_insecure_ciphers must be of type boolean; received ' . gettype(@$params['sftp_insecure_ciphers']));
        }

        if (isset($params['sftp_insecure_diffie_hellman']) && !is_scalar($params['sftp_insecure_diffie_hellman'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_insecure_diffie_hellman must be of type boolean; received ' . gettype(@$params['sftp_insecure_diffie_hellman']));
        }

        if (isset($params['disable_files_certificate_generation']) && !is_scalar($params['disable_files_certificate_generation'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_files_certificate_generation must be of type boolean; received ' . gettype(@$params['disable_files_certificate_generation']));
        }

        if (isset($params['fedramp']) && !is_scalar($params['fedramp'])) {
            throw new \Files\Exception\InvalidParameterException('$fedramp must be of type boolean; received ' . gettype(@$params['fedramp']));
        }

        if (isset($params['user_lockout']) && !is_scalar($params['user_lockout'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout must be of type boolean; received ' . gettype(@$params['user_lockout']));
        }

        if (isset($params['user_lockout_tries']) && !is_int($params['user_lockout_tries'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_tries must be of type int; received ' . gettype(@$params['user_lockout_tries']));
        }

        if (isset($params['user_lockout_within']) && !is_int($params['user_lockout_within'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_within must be of type int; received ' . gettype(@$params['user_lockout_within']));
        }

        if (isset($params['user_lockout_lock_period']) && !is_int($params['user_lockout_lock_period'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_lock_period must be of type int; received ' . gettype(@$params['user_lockout_lock_period']));
        }

        if (isset($params['include_password_in_welcome_email']) && !is_scalar($params['include_password_in_welcome_email'])) {
            throw new \Files\Exception\InvalidParameterException('$include_password_in_welcome_email must be of type boolean; received ' . gettype(@$params['include_password_in_welcome_email']));
        }

        if (isset($params['allowed_countries']) && !is_string($params['allowed_countries'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_countries must be of type string; received ' . gettype(@$params['allowed_countries']));
        }

        if (isset($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (isset($params['allow_user_level_2fa_override']) && !is_scalar($params['allow_user_level_2fa_override'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_level_2fa_override must be of type boolean; received ' . gettype(@$params['allow_user_level_2fa_override']));
        }

        if (isset($params['allow_user_level_allowed_ip_override']) && !is_scalar($params['allow_user_level_allowed_ip_override'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_level_allowed_ip_override must be of type boolean; received ' . gettype(@$params['allow_user_level_allowed_ip_override']));
        }

        if (isset($params['allow_user_level_ssl_override']) && !is_scalar($params['allow_user_level_ssl_override'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_level_ssl_override must be of type boolean; received ' . gettype(@$params['allow_user_level_ssl_override']));
        }

        if (isset($params['allow_user_level_ftp_tls_session_reuse_override']) && !is_scalar($params['allow_user_level_ftp_tls_session_reuse_override'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_user_level_ftp_tls_session_reuse_override must be of type boolean; received ' . gettype(@$params['allow_user_level_ftp_tls_session_reuse_override']));
        }

        if (isset($params['enforce_parent_site_user_ip_restrictions']) && !is_scalar($params['enforce_parent_site_user_ip_restrictions'])) {
            throw new \Files\Exception\InvalidParameterException('$enforce_parent_site_user_ip_restrictions must be of type boolean; received ' . gettype(@$params['enforce_parent_site_user_ip_restrictions']));
        }

        if (isset($params['disallowed_countries']) && !is_string($params['disallowed_countries'])) {
            throw new \Files\Exception\InvalidParameterException('$disallowed_countries must be of type string; received ' . gettype(@$params['disallowed_countries']));
        }

        if (isset($params['days_to_retain_backups']) && !is_int($params['days_to_retain_backups'])) {
            throw new \Files\Exception\InvalidParameterException('$days_to_retain_backups must be of type int; received ' . gettype(@$params['days_to_retain_backups']));
        }

        if (isset($params['max_prior_passwords']) && !is_int($params['max_prior_passwords'])) {
            throw new \Files\Exception\InvalidParameterException('$max_prior_passwords must be of type int; received ' . gettype(@$params['max_prior_passwords']));
        }

        if (isset($params['password_validity_days']) && !is_int($params['password_validity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype(@$params['password_validity_days']));
        }

        if (isset($params['password_min_length']) && !is_int($params['password_min_length'])) {
            throw new \Files\Exception\InvalidParameterException('$password_min_length must be of type int; received ' . gettype(@$params['password_min_length']));
        }

        if (isset($params['password_require_letter']) && !is_scalar($params['password_require_letter'])) {
            throw new \Files\Exception\InvalidParameterException('$password_require_letter must be of type boolean; received ' . gettype(@$params['password_require_letter']));
        }

        if (isset($params['password_require_mixed']) && !is_scalar($params['password_require_mixed'])) {
            throw new \Files\Exception\InvalidParameterException('$password_require_mixed must be of type boolean; received ' . gettype(@$params['password_require_mixed']));
        }

        if (isset($params['password_require_special']) && !is_scalar($params['password_require_special'])) {
            throw new \Files\Exception\InvalidParameterException('$password_require_special must be of type boolean; received ' . gettype(@$params['password_require_special']));
        }

        if (isset($params['password_require_number']) && !is_scalar($params['password_require_number'])) {
            throw new \Files\Exception\InvalidParameterException('$password_require_number must be of type boolean; received ' . gettype(@$params['password_require_number']));
        }

        if (isset($params['password_require_unbreached']) && !is_scalar($params['password_require_unbreached'])) {
            throw new \Files\Exception\InvalidParameterException('$password_require_unbreached must be of type boolean; received ' . gettype(@$params['password_require_unbreached']));
        }

        if (isset($params['require_logout_from_bundles_and_inboxes']) && !is_scalar($params['require_logout_from_bundles_and_inboxes'])) {
            throw new \Files\Exception\InvalidParameterException('$require_logout_from_bundles_and_inboxes must be of type boolean; received ' . gettype(@$params['require_logout_from_bundles_and_inboxes']));
        }

        if (isset($params['dav_user_root_enabled']) && !is_scalar($params['dav_user_root_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$dav_user_root_enabled must be of type boolean; received ' . gettype(@$params['dav_user_root_enabled']));
        }

        if (isset($params['sftp_user_root_enabled']) && !is_scalar($params['sftp_user_root_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_user_root_enabled must be of type boolean; received ' . gettype(@$params['sftp_user_root_enabled']));
        }

        if (isset($params['disable_password_reset']) && !is_scalar($params['disable_password_reset'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_password_reset must be of type boolean; received ' . gettype(@$params['disable_password_reset']));
        }

        if (isset($params['immutable_files']) && !is_scalar($params['immutable_files'])) {
            throw new \Files\Exception\InvalidParameterException('$immutable_files must be of type boolean; received ' . gettype(@$params['immutable_files']));
        }

        if (isset($params['bundle_not_found_message']) && !is_string($params['bundle_not_found_message'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_not_found_message must be of type string; received ' . gettype(@$params['bundle_not_found_message']));
        }

        if (isset($params['bundle_password_required']) && !is_scalar($params['bundle_password_required'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_password_required must be of type boolean; received ' . gettype(@$params['bundle_password_required']));
        }

        if (isset($params['bundle_require_registration']) && !is_scalar($params['bundle_require_registration'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_require_registration must be of type boolean; received ' . gettype(@$params['bundle_require_registration']));
        }

        if (isset($params['bundle_require_share_recipient']) && !is_scalar($params['bundle_require_share_recipient'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_require_share_recipient must be of type boolean; received ' . gettype(@$params['bundle_require_share_recipient']));
        }

        if (isset($params['bundle_send_one_time_password_to_recipient_at_registration']) && !is_scalar($params['bundle_send_one_time_password_to_recipient_at_registration'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_send_one_time_password_to_recipient_at_registration must be of type boolean; received ' . gettype(@$params['bundle_send_one_time_password_to_recipient_at_registration']));
        }

        if (isset($params['bundle_registration_notifications']) && !is_string($params['bundle_registration_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_notifications must be of type string; received ' . gettype(@$params['bundle_registration_notifications']));
        }

        if (isset($params['bundle_activity_notifications']) && !is_string($params['bundle_activity_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_activity_notifications must be of type string; received ' . gettype(@$params['bundle_activity_notifications']));
        }

        if (isset($params['bundle_upload_receipt_notifications']) && !is_string($params['bundle_upload_receipt_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_upload_receipt_notifications must be of type string; received ' . gettype(@$params['bundle_upload_receipt_notifications']));
        }

        if (isset($params['document_edits_in_bundle_allowed']) && !is_scalar($params['document_edits_in_bundle_allowed'])) {
            throw new \Files\Exception\InvalidParameterException('$document_edits_in_bundle_allowed must be of type boolean; received ' . gettype(@$params['document_edits_in_bundle_allowed']));
        }

        if (isset($params['password_requirements_apply_to_bundles']) && !is_scalar($params['password_requirements_apply_to_bundles'])) {
            throw new \Files\Exception\InvalidParameterException('$password_requirements_apply_to_bundles must be of type boolean; received ' . gettype(@$params['password_requirements_apply_to_bundles']));
        }

        if (isset($params['prevent_root_permissions_for_non_site_admins']) && !is_scalar($params['prevent_root_permissions_for_non_site_admins'])) {
            throw new \Files\Exception\InvalidParameterException('$prevent_root_permissions_for_non_site_admins must be of type boolean; received ' . gettype(@$params['prevent_root_permissions_for_non_site_admins']));
        }

        if (isset($params['restrict_root_folder_behaviors_to_site_admins']) && !is_scalar($params['restrict_root_folder_behaviors_to_site_admins'])) {
            throw new \Files\Exception\InvalidParameterException('$restrict_root_folder_behaviors_to_site_admins must be of type boolean; received ' . gettype(@$params['restrict_root_folder_behaviors_to_site_admins']));
        }

        if (isset($params['root_folder_behaviors_apply_to_workspaces']) && !is_scalar($params['root_folder_behaviors_apply_to_workspaces'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder_behaviors_apply_to_workspaces must be of type boolean; received ' . gettype(@$params['root_folder_behaviors_apply_to_workspaces']));
        }

        if (isset($params['opt_out_global']) && !is_scalar($params['opt_out_global'])) {
            throw new \Files\Exception\InvalidParameterException('$opt_out_global must be of type boolean; received ' . gettype(@$params['opt_out_global']));
        }

        if (isset($params['use_provided_modified_at']) && !is_scalar($params['use_provided_modified_at'])) {
            throw new \Files\Exception\InvalidParameterException('$use_provided_modified_at must be of type boolean; received ' . gettype(@$params['use_provided_modified_at']));
        }

        if (isset($params['custom_namespace']) && !is_scalar($params['custom_namespace'])) {
            throw new \Files\Exception\InvalidParameterException('$custom_namespace must be of type boolean; received ' . gettype(@$params['custom_namespace']));
        }

        if (isset($params['non_sso_groups_allowed']) && !is_scalar($params['non_sso_groups_allowed'])) {
            throw new \Files\Exception\InvalidParameterException('$non_sso_groups_allowed must be of type boolean; received ' . gettype(@$params['non_sso_groups_allowed']));
        }

        if (isset($params['non_sso_users_allowed']) && !is_scalar($params['non_sso_users_allowed'])) {
            throw new \Files\Exception\InvalidParameterException('$non_sso_users_allowed must be of type boolean; received ' . gettype(@$params['non_sso_users_allowed']));
        }

        if (isset($params['sharing_enabled']) && !is_scalar($params['sharing_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sharing_enabled must be of type boolean; received ' . gettype(@$params['sharing_enabled']));
        }

        if (isset($params['snapshot_sharing_enabled']) && !is_scalar($params['snapshot_sharing_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$snapshot_sharing_enabled must be of type boolean; received ' . gettype(@$params['snapshot_sharing_enabled']));
        }

        if (isset($params['user_requests_enabled']) && !is_scalar($params['user_requests_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$user_requests_enabled must be of type boolean; received ' . gettype(@$params['user_requests_enabled']));
        }

        if (isset($params['user_requests_notify_admins']) && !is_scalar($params['user_requests_notify_admins'])) {
            throw new \Files\Exception\InvalidParameterException('$user_requests_notify_admins must be of type boolean; received ' . gettype(@$params['user_requests_notify_admins']));
        }

        if (isset($params['dav_enabled']) && !is_scalar($params['dav_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$dav_enabled must be of type boolean; received ' . gettype(@$params['dav_enabled']));
        }

        if (isset($params['files_com_remote_server_enabled']) && !is_scalar($params['files_com_remote_server_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$files_com_remote_server_enabled must be of type boolean; received ' . gettype(@$params['files_com_remote_server_enabled']));
        }

        if (isset($params['ftp_enabled']) && !is_scalar($params['ftp_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_enabled must be of type boolean; received ' . gettype(@$params['ftp_enabled']));
        }

        if (isset($params['s3_compatible_endpoint_enabled']) && !is_scalar($params['s3_compatible_endpoint_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint_enabled must be of type boolean; received ' . gettype(@$params['s3_compatible_endpoint_enabled']));
        }

        if (isset($params['sftp_enabled']) && !is_scalar($params['sftp_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_enabled must be of type boolean; received ' . gettype(@$params['sftp_enabled']));
        }

        if (isset($params['sftp_finalize_partial_uploads']) && !is_scalar($params['sftp_finalize_partial_uploads'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_finalize_partial_uploads must be of type boolean; received ' . gettype(@$params['sftp_finalize_partial_uploads']));
        }

        if (isset($params['users_can_create_api_keys']) && !is_scalar($params['users_can_create_api_keys'])) {
            throw new \Files\Exception\InvalidParameterException('$users_can_create_api_keys must be of type boolean; received ' . gettype(@$params['users_can_create_api_keys']));
        }

        if (isset($params['users_can_create_ssh_keys']) && !is_scalar($params['users_can_create_ssh_keys'])) {
            throw new \Files\Exception\InvalidParameterException('$users_can_create_ssh_keys must be of type boolean; received ' . gettype(@$params['users_can_create_ssh_keys']));
        }

        if (isset($params['show_user_notifications_log_in_link']) && !is_scalar($params['show_user_notifications_log_in_link'])) {
            throw new \Files\Exception\InvalidParameterException('$show_user_notifications_log_in_link must be of type boolean; received ' . gettype(@$params['show_user_notifications_log_in_link']));
        }

        if (isset($params['sftp_host_key_type']) && !is_string($params['sftp_host_key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_host_key_type must be of type string; received ' . gettype(@$params['sftp_host_key_type']));
        }

        if (isset($params['active_sftp_host_key_id']) && !is_int($params['active_sftp_host_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$active_sftp_host_key_id must be of type int; received ' . gettype(@$params['active_sftp_host_key_id']));
        }

        if (isset($params['active_sftp_host_key_ids']) && !is_array($params['active_sftp_host_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$active_sftp_host_key_ids must be of type array; received ' . gettype(@$params['active_sftp_host_key_ids']));
        }

        if (isset($params['protocol_access_groups_only']) && !is_scalar($params['protocol_access_groups_only'])) {
            throw new \Files\Exception\InvalidParameterException('$protocol_access_groups_only must be of type boolean; received ' . gettype(@$params['protocol_access_groups_only']));
        }

        if (isset($params['revoke_bundle_access_on_disable_or_delete']) && !is_scalar($params['revoke_bundle_access_on_disable_or_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$revoke_bundle_access_on_disable_or_delete must be of type boolean; received ' . gettype(@$params['revoke_bundle_access_on_disable_or_delete']));
        }

        if (isset($params['group_admins_can_add_users']) && !is_scalar($params['group_admins_can_add_users'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_add_users must be of type boolean; received ' . gettype(@$params['group_admins_can_add_users']));
        }

        if (isset($params['group_admins_can_manage_group_memberships']) && !is_scalar($params['group_admins_can_manage_group_memberships'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_manage_group_memberships must be of type boolean; received ' . gettype(@$params['group_admins_can_manage_group_memberships']));
        }

        if (isset($params['group_admins_can_delete_users']) && !is_scalar($params['group_admins_can_delete_users'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_delete_users must be of type boolean; received ' . gettype(@$params['group_admins_can_delete_users']));
        }

        if (isset($params['group_admins_can_enable_disable_users']) && !is_scalar($params['group_admins_can_enable_disable_users'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_enable_disable_users must be of type boolean; received ' . gettype(@$params['group_admins_can_enable_disable_users']));
        }

        if (isset($params['group_admins_can_modify_users']) && !is_scalar($params['group_admins_can_modify_users'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_modify_users must be of type boolean; received ' . gettype(@$params['group_admins_can_modify_users']));
        }

        if (isset($params['group_admins_can_bypass_user_lifecycle_rules']) && !is_scalar($params['group_admins_can_bypass_user_lifecycle_rules'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_bypass_user_lifecycle_rules must be of type boolean; received ' . gettype(@$params['group_admins_can_bypass_user_lifecycle_rules']));
        }

        if (isset($params['group_admins_can_reset_passwords']) && !is_scalar($params['group_admins_can_reset_passwords'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_reset_passwords must be of type boolean; received ' . gettype(@$params['group_admins_can_reset_passwords']));
        }

        if (isset($params['group_admins_can_set_user_password']) && !is_scalar($params['group_admins_can_set_user_password'])) {
            throw new \Files\Exception\InvalidParameterException('$group_admins_can_set_user_password must be of type boolean; received ' . gettype(@$params['group_admins_can_set_user_password']));
        }

        if (isset($params['child_site_folder_admins_can_see_parent_site_directory']) && !is_scalar($params['child_site_folder_admins_can_see_parent_site_directory'])) {
            throw new \Files\Exception\InvalidParameterException('$child_site_folder_admins_can_see_parent_site_directory must be of type boolean; received ' . gettype(@$params['child_site_folder_admins_can_see_parent_site_directory']));
        }

        if (isset($params['workspace_folder_admins_can_see_default_workspace_directory']) && !is_scalar($params['workspace_folder_admins_can_see_default_workspace_directory'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_folder_admins_can_see_default_workspace_directory must be of type boolean; received ' . gettype(@$params['workspace_folder_admins_can_see_default_workspace_directory']));
        }

        if (isset($params['bundle_recipient_blacklist_free_email_domains']) && !is_scalar($params['bundle_recipient_blacklist_free_email_domains'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_recipient_blacklist_free_email_domains must be of type boolean; received ' . gettype(@$params['bundle_recipient_blacklist_free_email_domains']));
        }

        if (isset($params['bundle_recipient_blacklist_domains']) && !is_array($params['bundle_recipient_blacklist_domains'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_recipient_blacklist_domains must be of type array; received ' . gettype(@$params['bundle_recipient_blacklist_domains']));
        }

        if (isset($params['admins_bypass_locked_subfolders']) && !is_scalar($params['admins_bypass_locked_subfolders'])) {
            throw new \Files\Exception\InvalidParameterException('$admins_bypass_locked_subfolders must be of type boolean; received ' . gettype(@$params['admins_bypass_locked_subfolders']));
        }

        if (isset($params['allowed_2fa_method_sms']) && !is_scalar($params['allowed_2fa_method_sms'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_sms must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_sms']));
        }

        if (isset($params['allowed_2fa_method_totp']) && !is_scalar($params['allowed_2fa_method_totp'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_totp must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_totp']));
        }

        if (isset($params['allowed_2fa_method_webauthn']) && !is_scalar($params['allowed_2fa_method_webauthn'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_webauthn must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_webauthn']));
        }

        if (isset($params['allowed_2fa_method_yubi']) && !is_scalar($params['allowed_2fa_method_yubi'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_yubi must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_yubi']));
        }

        if (isset($params['allowed_2fa_method_email']) && !is_scalar($params['allowed_2fa_method_email'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_email must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_email']));
        }

        if (isset($params['allowed_2fa_method_static']) && !is_scalar($params['allowed_2fa_method_static'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_static must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_static']));
        }

        if (isset($params['allowed_2fa_method_bypass_for_ftp_sftp_dav']) && !is_scalar($params['allowed_2fa_method_bypass_for_ftp_sftp_dav'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_2fa_method_bypass_for_ftp_sftp_dav must be of type boolean; received ' . gettype(@$params['allowed_2fa_method_bypass_for_ftp_sftp_dav']));
        }

        if (isset($params['require_2fa']) && !is_scalar($params['require_2fa'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa must be of type boolean; received ' . gettype(@$params['require_2fa']));
        }

        if (isset($params['require_2fa_exempt_all_sso_users']) && !is_scalar($params['require_2fa_exempt_all_sso_users'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa_exempt_all_sso_users must be of type boolean; received ' . gettype(@$params['require_2fa_exempt_all_sso_users']));
        }

        if (isset($params['require_2fa_user_type']) && !is_string($params['require_2fa_user_type'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa_user_type must be of type string; received ' . gettype(@$params['require_2fa_user_type']));
        }

        if (isset($params['color2_top']) && !is_string($params['color2_top'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_top must be of type string; received ' . gettype(@$params['color2_top']));
        }

        if (isset($params['color2_left']) && !is_string($params['color2_left'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_left must be of type string; received ' . gettype(@$params['color2_left']));
        }

        if (isset($params['color2_link']) && !is_string($params['color2_link'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_link must be of type string; received ' . gettype(@$params['color2_link']));
        }

        if (isset($params['color2_text']) && !is_string($params['color2_text'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_text must be of type string; received ' . gettype(@$params['color2_text']));
        }

        if (isset($params['color2_top_text']) && !is_string($params['color2_top_text'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_top_text must be of type string; received ' . gettype(@$params['color2_top_text']));
        }

        if (isset($params['site_header']) && !is_string($params['site_header'])) {
            throw new \Files\Exception\InvalidParameterException('$site_header must be of type string; received ' . gettype(@$params['site_header']));
        }

        if (isset($params['site_footer']) && !is_string($params['site_footer'])) {
            throw new \Files\Exception\InvalidParameterException('$site_footer must be of type string; received ' . gettype(@$params['site_footer']));
        }

        if (isset($params['site_public_header']) && !is_string($params['site_public_header'])) {
            throw new \Files\Exception\InvalidParameterException('$site_public_header must be of type string; received ' . gettype(@$params['site_public_header']));
        }

        if (isset($params['site_public_footer']) && !is_string($params['site_public_footer'])) {
            throw new \Files\Exception\InvalidParameterException('$site_public_footer must be of type string; received ' . gettype(@$params['site_public_footer']));
        }

        if (isset($params['login_help_text']) && !is_string($params['login_help_text'])) {
            throw new \Files\Exception\InvalidParameterException('$login_help_text must be of type string; received ' . gettype(@$params['login_help_text']));
        }

        if (isset($params['use_dedicated_ips_for_smtp']) && !is_scalar($params['use_dedicated_ips_for_smtp'])) {
            throw new \Files\Exception\InvalidParameterException('$use_dedicated_ips_for_smtp must be of type boolean; received ' . gettype(@$params['use_dedicated_ips_for_smtp']));
        }

        if (isset($params['email_footer_custom_text']) && !is_string($params['email_footer_custom_text'])) {
            throw new \Files\Exception\InvalidParameterException('$email_footer_custom_text must be of type string; received ' . gettype(@$params['email_footer_custom_text']));
        }

        if (isset($params['smtp_address']) && !is_string($params['smtp_address'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_address must be of type string; received ' . gettype(@$params['smtp_address']));
        }

        if (isset($params['smtp_authentication']) && !is_string($params['smtp_authentication'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_authentication must be of type string; received ' . gettype(@$params['smtp_authentication']));
        }

        if (isset($params['smtp_from']) && !is_string($params['smtp_from'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_from must be of type string; received ' . gettype(@$params['smtp_from']));
        }

        if (isset($params['smtp_username']) && !is_string($params['smtp_username'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_username must be of type string; received ' . gettype(@$params['smtp_username']));
        }

        if (isset($params['smtp_port']) && !is_int($params['smtp_port'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_port must be of type int; received ' . gettype(@$params['smtp_port']));
        }

        if (isset($params['smtp_ssl']) && !is_string($params['smtp_ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_ssl must be of type string; received ' . gettype(@$params['smtp_ssl']));
        }

        if (isset($params['uploads_via_email_authentication']) && !is_scalar($params['uploads_via_email_authentication'])) {
            throw new \Files\Exception\InvalidParameterException('$uploads_via_email_authentication must be of type boolean; received ' . gettype(@$params['uploads_via_email_authentication']));
        }

        if (isset($params['icon16_delete']) && !is_scalar($params['icon16_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$icon16_delete must be of type boolean; received ' . gettype(@$params['icon16_delete']));
        }

        if (isset($params['icon32_delete']) && !is_scalar($params['icon32_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$icon32_delete must be of type boolean; received ' . gettype(@$params['icon32_delete']));
        }

        if (isset($params['icon48_delete']) && !is_scalar($params['icon48_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$icon48_delete must be of type boolean; received ' . gettype(@$params['icon48_delete']));
        }

        if (isset($params['icon128_delete']) && !is_scalar($params['icon128_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$icon128_delete must be of type boolean; received ' . gettype(@$params['icon128_delete']));
        }

        if (isset($params['logo_delete']) && !is_scalar($params['logo_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$logo_delete must be of type boolean; received ' . gettype(@$params['logo_delete']));
        }

        if (isset($params['bundle_watermark_attachment_delete']) && !is_scalar($params['bundle_watermark_attachment_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_watermark_attachment_delete must be of type boolean; received ' . gettype(@$params['bundle_watermark_attachment_delete']));
        }

        if (isset($params['login_page_background_image_delete']) && !is_scalar($params['login_page_background_image_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$login_page_background_image_delete must be of type boolean; received ' . gettype(@$params['login_page_background_image_delete']));
        }

        if (isset($params['disable_2fa_with_delay']) && !is_scalar($params['disable_2fa_with_delay'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_2fa_with_delay must be of type boolean; received ' . gettype(@$params['disable_2fa_with_delay']));
        }

        if (isset($params['redirect_old_subdomain']) && !is_scalar($params['redirect_old_subdomain'])) {
            throw new \Files\Exception\InvalidParameterException('$redirect_old_subdomain must be of type boolean; received ' . gettype(@$params['redirect_old_subdomain']));
        }

        if (isset($params['smtp_password']) && !is_string($params['smtp_password'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_password must be of type string; received ' . gettype(@$params['smtp_password']));
        }

        $response = Api::sendRequest('/site', 'PATCH', $params, $options);

        return new Site(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
