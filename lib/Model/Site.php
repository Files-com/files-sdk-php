<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Site
 *
 * @package Files
 */
class Site
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
    // int64 # Site Id
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # Site name
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    // array(string) # Additional extensions that are considered text files
    public function getAdditionalTextFileTypes()
    {
        return isset($this->attributes['additional_text_file_types']) ? $this->attributes['additional_text_file_types'] : null;
    }
    // object # Availability settings for AI features. Each feature requires the site_admins, workspace_admins, folder_admins, and all_users keys. Optional selected_group_members defaults to false; when true, members of at least one group in group_ids get access regardless of the other options. All availability options are additive: any enabled option matching the user grants access. Optional group_ids is an array of integer IDs of active groups on this site, from any workspace. Omitted or empty group_ids grants no access through selected_group_members and does not affect other options. Disabling all AI features overrides these settings.
    public function getAiFeatureAvailability()
    {
        return isset($this->attributes['ai_feature_availability']) ? $this->attributes['ai_feature_availability'] : null;
    }
    // boolean # Is SMS two factor authentication allowed?
    public function getAllowed2faMethodSms()
    {
        return isset($this->attributes['allowed_2fa_method_sms']) ? $this->attributes['allowed_2fa_method_sms'] : null;
    }
    // boolean # Is TOTP two factor authentication allowed?
    public function getAllowed2faMethodTotp()
    {
        return isset($this->attributes['allowed_2fa_method_totp']) ? $this->attributes['allowed_2fa_method_totp'] : null;
    }
    // boolean # Is WebAuthn two factor authentication allowed?
    public function getAllowed2faMethodWebauthn()
    {
        return isset($this->attributes['allowed_2fa_method_webauthn']) ? $this->attributes['allowed_2fa_method_webauthn'] : null;
    }
    // boolean # Is yubikey two factor authentication allowed?
    public function getAllowed2faMethodYubi()
    {
        return isset($this->attributes['allowed_2fa_method_yubi']) ? $this->attributes['allowed_2fa_method_yubi'] : null;
    }
    // boolean # Is OTP via email two factor authentication allowed?
    public function getAllowed2faMethodEmail()
    {
        return isset($this->attributes['allowed_2fa_method_email']) ? $this->attributes['allowed_2fa_method_email'] : null;
    }
    // boolean # Is OTP via static codes for two factor authentication allowed?
    public function getAllowed2faMethodStatic()
    {
        return isset($this->attributes['allowed_2fa_method_static']) ? $this->attributes['allowed_2fa_method_static'] : null;
    }
    // boolean # Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
    public function getAllowed2faMethodBypassForFtpSftpDav()
    {
        return isset($this->attributes['allowed_2fa_method_bypass_for_ftp_sftp_dav']) ? $this->attributes['allowed_2fa_method_bypass_for_ftp_sftp_dav'] : null;
    }
    // int64 # User ID for the main site administrator
    public function getAdminUserId()
    {
        return isset($this->attributes['admin_user_id']) ? $this->attributes['admin_user_id'] : null;
    }
    // boolean # Allow admins to bypass the locked subfolders setting.
    public function getAdminsBypassLockedSubfolders()
    {
        return isset($this->attributes['admins_bypass_locked_subfolders']) ? $this->attributes['admins_bypass_locked_subfolders'] : null;
    }
    // boolean # Are manual Bundle names allowed?
    public function getAllowBundleNames()
    {
        return isset($this->attributes['allow_bundle_names']) ? $this->attributes['allow_bundle_names'] : null;
    }
    // boolean # Allow the site-wide two-factor authentication requirement to be overriden on a per-user-basis?
    public function getAllowUserLevel2faOverride()
    {
        return isset($this->attributes['allow_user_level_2fa_override']) ? $this->attributes['allow_user_level_2fa_override'] : null;
    }
    // boolean # Allow the site-wide allowed IP restriction to be overriden on a per-user-basis?
    public function getAllowUserLevelAllowedIpOverride()
    {
        return isset($this->attributes['allow_user_level_allowed_ip_override']) ? $this->attributes['allow_user_level_allowed_ip_override'] : null;
    }
    // boolean # Allow the site-wide FTP SSL requirement to be overriden on a per-user-basis?
    public function getAllowUserLevelSslOverride()
    {
        return isset($this->attributes['allow_user_level_ssl_override']) ? $this->attributes['allow_user_level_ssl_override'] : null;
    }
    // string # Comma separated list of allowed Country codes
    public function getAllowedCountries()
    {
        return isset($this->attributes['allowed_countries']) ? $this->attributes['allowed_countries'] : null;
    }
    // string # List of allowed IP addresses
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }
    // boolean # Require Parent Site users entering this Child Site to match its allowed IP addresses in addition to their existing IP restrictions. Applies to all Workspaces. Defaults to false.
    public function getEnforceParentSiteUserIpRestrictions()
    {
        return isset($this->attributes['enforce_parent_site_user_ip_restrictions']) ? $this->attributes['enforce_parent_site_user_ip_restrictions'] : null;
    }
    // boolean # Create parent directories if they do not exist during uploads?  This is primarily used to work around broken upload clients that assume servers will perform this step.
    public function getAlwaysMkdirParents()
    {
        return isset($this->attributes['always_mkdir_parents']) ? $this->attributes['always_mkdir_parents'] : null;
    }
    // int64 # Number of days to retain AS2 messages (incoming and outgoing).
    public function getAs2MessageRetentionDays()
    {
        return isset($this->attributes['as2_message_retention_days']) ? $this->attributes['as2_message_retention_days'] : null;
    }
    // boolean # If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
    public function getAskAboutOverwrites()
    {
        return isset($this->attributes['ask_about_overwrites']) ? $this->attributes['ask_about_overwrites'] : null;
    }
    // string # Do Bundle owners receive activity notifications?
    public function getBundleActivityNotifications()
    {
        return isset($this->attributes['bundle_activity_notifications']) ? $this->attributes['bundle_activity_notifications'] : null;
    }
    // int64 # Site-wide Bundle expiration in days
    public function getBundleExpiration()
    {
        return isset($this->attributes['bundle_expiration']) ? $this->attributes['bundle_expiration'] : null;
    }
    // string # Custom error message to show when bundle is not found.
    public function getBundleNotFoundMessage()
    {
        return isset($this->attributes['bundle_not_found_message']) ? $this->attributes['bundle_not_found_message'] : null;
    }
    // boolean # Do Bundles require password protection?
    public function getBundlePasswordRequired()
    {
        return isset($this->attributes['bundle_password_required']) ? $this->attributes['bundle_password_required'] : null;
    }
    // boolean # If true, new Share Links created by a user with a primary group will default to that group as owner.
    public function getBundlesDefaultOwnedByPrimaryGroup()
    {
        return isset($this->attributes['bundles_default_owned_by_primary_group']) ? $this->attributes['bundles_default_owned_by_primary_group'] : null;
    }
    // array(string) # List of email domains to disallow when entering a Bundle/Inbox recipients
    public function getBundleRecipientBlacklistDomains()
    {
        return isset($this->attributes['bundle_recipient_blacklist_domains']) ? $this->attributes['bundle_recipient_blacklist_domains'] : null;
    }
    // boolean # Disallow free email domains for Bundle/Inbox recipients?
    public function getBundleRecipientBlacklistFreeEmailDomains()
    {
        return isset($this->attributes['bundle_recipient_blacklist_free_email_domains']) ? $this->attributes['bundle_recipient_blacklist_free_email_domains'] : null;
    }
    // string # Do Bundle owners receive registration notification?
    public function getBundleRegistrationNotifications()
    {
        return isset($this->attributes['bundle_registration_notifications']) ? $this->attributes['bundle_registration_notifications'] : null;
    }
    // boolean # Do Bundles require registration?
    public function getBundleRequireRegistration()
    {
        return isset($this->attributes['bundle_require_registration']) ? $this->attributes['bundle_require_registration'] : null;
    }
    // boolean # Do Bundles require recipients for sharing?
    public function getBundleRequireShareRecipient()
    {
        return isset($this->attributes['bundle_require_share_recipient']) ? $this->attributes['bundle_require_share_recipient'] : null;
    }
    // boolean # If true, new Share Links must send a one-time password to the recipient when they register. Requires bundle_require_share_recipient and cannot be enabled with bundle_password_required.
    public function getBundleSendOneTimePasswordToRecipientAtRegistration()
    {
        return isset($this->attributes['bundle_send_one_time_password_to_recipient_at_registration']) ? $this->attributes['bundle_send_one_time_password_to_recipient_at_registration'] : null;
    }
    // boolean # Do Bundles require internal notes?
    public function getBundleRequireNote()
    {
        return isset($this->attributes['bundle_require_note']) ? $this->attributes['bundle_require_note'] : null;
    }
    // boolean # Do Bundle creators receive receipts of invitations?
    public function getBundleSendSharedReceipts()
    {
        return isset($this->attributes['bundle_send_shared_receipts']) ? $this->attributes['bundle_send_shared_receipts'] : null;
    }
    // string # Do Bundle uploaders receive upload confirmation notifications?
    public function getBundleUploadReceiptNotifications()
    {
        return isset($this->attributes['bundle_upload_receipt_notifications']) ? $this->attributes['bundle_upload_receipt_notifications'] : null;
    }
    // Image # Preview watermark image applied to all bundle items.
    public function getBundleWatermarkAttachment()
    {
        return isset($this->attributes['bundle_watermark_attachment']) ? $this->attributes['bundle_watermark_attachment'] : null;
    }
    // object # Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
    public function getBundleWatermarkValue()
    {
        return isset($this->attributes['bundle_watermark_value']) ? $this->attributes['bundle_watermark_value'] : null;
    }
    // boolean # Calculate CRC32 checksums for files?
    public function getCalculateFileChecksumsCrc32()
    {
        return isset($this->attributes['calculate_file_checksums_crc32']) ? $this->attributes['calculate_file_checksums_crc32'] : null;
    }
    // boolean # Calculate MD5 checksums for files?
    public function getCalculateFileChecksumsMd5()
    {
        return isset($this->attributes['calculate_file_checksums_md5']) ? $this->attributes['calculate_file_checksums_md5'] : null;
    }
    // boolean # Calculate SHA1 checksums for files?
    public function getCalculateFileChecksumsSha1()
    {
        return isset($this->attributes['calculate_file_checksums_sha1']) ? $this->attributes['calculate_file_checksums_sha1'] : null;
    }
    // boolean # Calculate SHA256 checksums for files?
    public function getCalculateFileChecksumsSha256()
    {
        return isset($this->attributes['calculate_file_checksums_sha256']) ? $this->attributes['calculate_file_checksums_sha256'] : null;
    }
    // boolean # If true, administrators on this site's Child Sites, including Folder Admins, can list this site's Default Workspace users and groups and grant them folder permissions and notifications on Child Site paths.
    public function getChildSiteFolderAdminsCanSeeParentSiteDirectory()
    {
        return isset($this->attributes['child_site_folder_admins_can_see_parent_site_directory']) ? $this->attributes['child_site_folder_admins_can_see_parent_site_directory'] : null;
    }
    // boolean # If true, Workspace Administrators and Folder Admins in a Custom Workspace can list the Default Workspace users and groups that have permissions in that Workspace and choose them as Email Notification recipients.
    public function getWorkspaceFolderAdminsCanSeeDefaultWorkspaceDirectory()
    {
        return isset($this->attributes['workspace_folder_admins_can_see_default_workspace_directory']) ? $this->attributes['workspace_folder_admins_can_see_default_workspace_directory'] : null;
    }
    // boolean # Require email authentication, virus, and spam checks for incoming emails to Inboxes and Incoming Email Automations in every Workspace on this site?
    public function getUploadsViaEmailAuthentication()
    {
        return isset($this->attributes['uploads_via_email_authentication']) ? $this->attributes['uploads_via_email_authentication'] : null;
    }
    // string # Page link and button color
    public function getColor2Left()
    {
        return isset($this->attributes['color2_left']) ? $this->attributes['color2_left'] : null;
    }
    // string # Top bar link color
    public function getColor2Link()
    {
        return isset($this->attributes['color2_link']) ? $this->attributes['color2_link'] : null;
    }
    // string # Page link and button color
    public function getColor2Text()
    {
        return isset($this->attributes['color2_text']) ? $this->attributes['color2_text'] : null;
    }
    // string # Top bar background color
    public function getColor2Top()
    {
        return isset($this->attributes['color2_top']) ? $this->attributes['color2_top'] : null;
    }
    // string # Top bar text color
    public function getColor2TopText()
    {
        return isset($this->attributes['color2_top_text']) ? $this->attributes['color2_top_text'] : null;
    }
    // string # Site main contact name
    public function getContactName()
    {
        return isset($this->attributes['contact_name']) ? $this->attributes['contact_name'] : null;
    }
    // date-time # Time this site was created
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // string # Preferred currency
    public function getCurrency()
    {
        return isset($this->attributes['currency']) ? $this->attributes['currency'] : null;
    }
    // boolean # Is this site using a custom namespace for users?
    public function getCustomNamespace()
    {
        return isset($this->attributes['custom_namespace']) ? $this->attributes['custom_namespace'] : null;
    }
    // boolean # Is WebDAV enabled?
    public function getDavEnabled()
    {
        return isset($this->attributes['dav_enabled']) ? $this->attributes['dav_enabled'] : null;
    }
    // boolean # Use user FTP roots also for WebDAV?
    public function getDavUserRootEnabled()
    {
        return isset($this->attributes['dav_user_root_enabled']) ? $this->attributes['dav_user_root_enabled'] : null;
    }
    // int64 # Number of days to keep deleted files
    public function getDaysToRetainBackups()
    {
        return isset($this->attributes['days_to_retain_backups']) ? $this->attributes['days_to_retain_backups'] : null;
    }
    // boolean # If true, allow public viewers of Bundles with full permissions to use document editing integrations.
    public function getDocumentEditsInBundleAllowed()
    {
        return isset($this->attributes['document_edits_in_bundle_allowed']) ? $this->attributes['document_edits_in_bundle_allowed'] : null;
    }
    // string # Site default time zone
    public function getDefaultTimeZone()
    {
        return isset($this->attributes['default_time_zone']) ? $this->attributes['default_time_zone'] : null;
    }
    // boolean # Is the desktop app enabled?
    public function getDesktopApp()
    {
        return isset($this->attributes['desktop_app']) ? $this->attributes['desktop_app'] : null;
    }
    // boolean # Is desktop app session IP pinning enabled?
    public function getDesktopAppSessionIpPinning()
    {
        return isset($this->attributes['desktop_app_session_ip_pinning']) ? $this->attributes['desktop_app_session_ip_pinning'] : null;
    }
    // int64 # Desktop app session lifetime (in hours)
    public function getDesktopAppSessionLifetime()
    {
        return isset($this->attributes['desktop_app_session_lifetime']) ? $this->attributes['desktop_app_session_lifetime'] : null;
    }
    // boolean # Use legacy checksums mode?
    public function getLegacyChecksumsMode()
    {
        return isset($this->attributes['legacy_checksums_mode']) ? $this->attributes['legacy_checksums_mode'] : null;
    }
    // boolean # If true, we will migrate all remote server syncs to the new Sync model.
    public function getMigrateRemoteServerSyncToSync()
    {
        return isset($this->attributes['migrate_remote_server_sync_to_sync']) ? $this->attributes['migrate_remote_server_sync_to_sync'] : null;
    }
    // boolean # Is OAuth DCR (dynamic client registration) for MCP enabled?
    public function getMcpDcrEnabled()
    {
        return isset($this->attributes['mcp_dcr_enabled']) ? $this->attributes['mcp_dcr_enabled'] : null;
    }
    // boolean # Is the mobile app enabled?
    public function getMobileApp()
    {
        return isset($this->attributes['mobile_app']) ? $this->attributes['mobile_app'] : null;
    }
    // boolean # Is mobile app session IP pinning enabled?
    public function getMobileAppSessionIpPinning()
    {
        return isset($this->attributes['mobile_app_session_ip_pinning']) ? $this->attributes['mobile_app_session_ip_pinning'] : null;
    }
    // int64 # Mobile app session lifetime (in hours)
    public function getMobileAppSessionLifetime()
    {
        return isset($this->attributes['mobile_app_session_lifetime']) ? $this->attributes['mobile_app_session_lifetime'] : null;
    }
    // string # Comma separated list of disallowed Country codes
    public function getDisallowedCountries()
    {
        return isset($this->attributes['disallowed_countries']) ? $this->attributes['disallowed_countries'] : null;
    }
    // boolean # If true, all AI features are disabled for this site.
    public function getDisableAllAiFeatures()
    {
        return isset($this->attributes['disable_all_ai_features']) ? $this->attributes['disable_all_ai_features'] : null;
    }
    // boolean # If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
    public function getDisableFilesCertificateGeneration()
    {
        return isset($this->attributes['disable_files_certificate_generation']) ? $this->attributes['disable_files_certificate_generation'] : null;
    }
    // boolean # Are notifications disabled?
    public function getDisableNotifications()
    {
        return isset($this->attributes['disable_notifications']) ? $this->attributes['disable_notifications'] : null;
    }
    // boolean # If true, file operations produce file_action.activity.v1 EventChannel events in all workspaces. Defaults to false; subscription filters and workspace access still control delivery.
    public function getFileActivityEventsEnabled()
    {
        return isset($this->attributes['file_activity_events_enabled']) ? $this->attributes['file_activity_events_enabled'] : null;
    }
    // boolean # Is password reset disabled?
    public function getDisablePasswordReset()
    {
        return isset($this->attributes['disable_password_reset']) ? $this->attributes['disable_password_reset'] : null;
    }
    // string # Custom domain
    public function getDomain()
    {
        return isset($this->attributes['domain']) ? $this->attributes['domain'] : null;
    }
    // boolean # Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
    public function getDomainHstsHeader()
    {
        return isset($this->attributes['domain_hsts_header']) ? $this->attributes['domain_hsts_header'] : null;
    }
    // string # Letsencrypt chain to use when registering SSL Certificate for domain. No longer used as of 2026.
    public function getDomainLetsencryptChain()
    {
        return isset($this->attributes['domain_letsencrypt_chain']) ? $this->attributes['domain_letsencrypt_chain'] : null;
    }
    // email # Main email for this site
    public function getEmail()
    {
        return isset($this->attributes['email']) ? $this->attributes['email'] : null;
    }
    // boolean # Are FedRAMP security restrictions enabled for this site?
    public function getFedramp()
    {
        return isset($this->attributes['fedramp']) ? $this->attributes['fedramp'] : null;
    }
    // boolean # Allow other Files.com sites to use this site's API keys for native Files.com Remote Server connections? Defaults to true and applies to all keys and workspaces on this site. When false, new pairings and access through existing connections are rejected without revoking keys or deleting connections. Re-enabling permits access again with usable keys. Does not disable this site's connections to other sites, ordinary API access, or Connected Sites.
    public function getFilesComRemoteServerEnabled()
    {
        return isset($this->attributes['files_com_remote_server_enabled']) ? $this->attributes['files_com_remote_server_enabled'] : null;
    }
    // boolean # Is FTP enabled?
    public function getFtpEnabled()
    {
        return isset($this->attributes['ftp_enabled']) ? $this->attributes['ftp_enabled'] : null;
    }
    // email # Reply-to email for this site
    public function getReplyToEmail()
    {
        return isset($this->attributes['reply_to_email']) ? $this->attributes['reply_to_email'] : null;
    }
    // boolean # If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
    public function getNonSsoGroupsAllowed()
    {
        return isset($this->attributes['non_sso_groups_allowed']) ? $this->attributes['non_sso_groups_allowed'] : null;
    }
    // boolean # If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
    public function getNonSsoUsersAllowed()
    {
        return isset($this->attributes['non_sso_users_allowed']) ? $this->attributes['non_sso_users_allowed'] : null;
    }
    // boolean # If true, permissions for this site must be bound to a group (not a user).
    public function getFolderPermissionsGroupsOnly()
    {
        return isset($this->attributes['folder_permissions_groups_only']) ? $this->attributes['folder_permissions_groups_only'] : null;
    }
    // boolean # Allow group admins to create users in their groups
    public function getGroupAdminsCanAddUsers()
    {
        return isset($this->attributes['group_admins_can_add_users']) ? $this->attributes['group_admins_can_add_users'] : null;
    }
    // boolean # Allow group admins to add or remove existing users in their groups
    public function getGroupAdminsCanManageGroupMemberships()
    {
        return isset($this->attributes['group_admins_can_manage_group_memberships']) ? $this->attributes['group_admins_can_manage_group_memberships'] : null;
    }
    // boolean # Allow group admins to delete users in their groups
    public function getGroupAdminsCanDeleteUsers()
    {
        return isset($this->attributes['group_admins_can_delete_users']) ? $this->attributes['group_admins_can_delete_users'] : null;
    }
    // boolean # Allow group admins to enable or disable users in their groups
    public function getGroupAdminsCanEnableDisableUsers()
    {
        return isset($this->attributes['group_admins_can_enable_disable_users']) ? $this->attributes['group_admins_can_enable_disable_users'] : null;
    }
    // boolean # Allow group admins to modify users in their groups
    public function getGroupAdminsCanModifyUsers()
    {
        return isset($this->attributes['group_admins_can_modify_users']) ? $this->attributes['group_admins_can_modify_users'] : null;
    }
    // boolean # Allow group admins to exempt users in their groups from lifecycle rules
    public function getGroupAdminsCanBypassUserLifecycleRules()
    {
        return isset($this->attributes['group_admins_can_bypass_user_lifecycle_rules']) ? $this->attributes['group_admins_can_bypass_user_lifecycle_rules'] : null;
    }
    // boolean # Allow group admins to reset passwords for users in their groups
    public function getGroupAdminsCanResetPasswords()
    {
        return isset($this->attributes['group_admins_can_reset_passwords']) ? $this->attributes['group_admins_can_reset_passwords'] : null;
    }
    // boolean # Allow group admins to set password authentication method
    public function getGroupAdminsCanSetUserPassword()
    {
        return isset($this->attributes['group_admins_can_set_user_password']) ? $this->attributes['group_admins_can_set_user_password'] : null;
    }
    // boolean # Is there a signed HIPAA BAA between Files.com and this site?
    public function getHipaa()
    {
        return isset($this->attributes['hipaa']) ? $this->attributes['hipaa'] : null;
    }
    // Image # Branded icon 128x128
    public function getIcon128()
    {
        return isset($this->attributes['icon128']) ? $this->attributes['icon128'] : null;
    }
    // Image # Branded icon 16x16
    public function getIcon16()
    {
        return isset($this->attributes['icon16']) ? $this->attributes['icon16'] : null;
    }
    // Image # Branded icon 32x32
    public function getIcon32()
    {
        return isset($this->attributes['icon32']) ? $this->attributes['icon32'] : null;
    }
    // Image # Branded icon 48x48
    public function getIcon48()
    {
        return isset($this->attributes['icon48']) ? $this->attributes['icon48'] : null;
    }
    // date-time # Can files be modified?
    public function getImmutableFilesSetAt()
    {
        return isset($this->attributes['immutable_files_set_at']) ? $this->attributes['immutable_files_set_at'] : null;
    }
    // string # Administrator access to user-owned Remote Servers in every Workspace: always_visible (default), never_visible, user_choice_default_visible, or user_choice_default_personal. Forced modes override owner preferences; choice modes use the preference or their default.
    public function getIntegrationCentricRemoteServerAdminAccess()
    {
        return isset($this->attributes['integration_centric_remote_server_admin_access']) ? $this->attributes['integration_centric_remote_server_admin_access'] : null;
    }
    // boolean # Include password in emails to new users?
    public function getIncludePasswordInWelcomeEmail()
    {
        return isset($this->attributes['include_password_in_welcome_email']) ? $this->attributes['include_password_in_welcome_email'] : null;
    }
    // string # Site default language
    public function getLanguage()
    {
        return isset($this->attributes['language']) ? $this->attributes['language'] : null;
    }
    // string # Base DN for looking up users in LDAP server
    public function getLdapBaseDn()
    {
        return isset($this->attributes['ldap_base_dn']) ? $this->attributes['ldap_base_dn'] : null;
    }
    // string # Domain name that will be appended to usernames
    public function getLdapDomain()
    {
        return isset($this->attributes['ldap_domain']) ? $this->attributes['ldap_domain'] : null;
    }
    // boolean # Main LDAP setting: is LDAP enabled?
    public function getLdapEnabled()
    {
        return isset($this->attributes['ldap_enabled']) ? $this->attributes['ldap_enabled'] : null;
    }
    // string # Should we sync groups from LDAP server?
    public function getLdapGroupAction()
    {
        return isset($this->attributes['ldap_group_action']) ? $this->attributes['ldap_group_action'] : null;
    }
    // string # Comma or newline separated list of group names (with optional wildcards) to exclude when syncing.
    public function getLdapGroupExclusion()
    {
        return isset($this->attributes['ldap_group_exclusion']) ? $this->attributes['ldap_group_exclusion'] : null;
    }
    // string # Comma or newline separated list of group names (with optional wildcards) to include when syncing.
    public function getLdapGroupInclusion()
    {
        return isset($this->attributes['ldap_group_inclusion']) ? $this->attributes['ldap_group_inclusion'] : null;
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
    // string # LDAP type
    public function getLdapType()
    {
        return isset($this->attributes['ldap_type']) ? $this->attributes['ldap_type'] : null;
    }
    // string # Should we sync users from LDAP server?
    public function getLdapUserAction()
    {
        return isset($this->attributes['ldap_user_action']) ? $this->attributes['ldap_user_action'] : null;
    }
    // string # Comma or newline separated list of group names (with optional wildcards) - if provided, only users in these groups will be added or synced.
    public function getLdapUserIncludeGroups()
    {
        return isset($this->attributes['ldap_user_include_groups']) ? $this->attributes['ldap_user_include_groups'] : null;
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
    // string # Login help text
    public function getLoginHelpText()
    {
        return isset($this->attributes['login_help_text']) ? $this->attributes['login_help_text'] : null;
    }
    // Image # Branded logo
    public function getLogo()
    {
        return isset($this->attributes['logo']) ? $this->attributes['logo'] : null;
    }
    // Image # Branded login page background
    public function getLoginPageBackgroundImage()
    {
        return isset($this->attributes['login_page_background_image']) ? $this->attributes['login_page_background_image'] : null;
    }
    // int64 # Number of prior passwords to disallow
    public function getMaxPriorPasswords()
    {
        return isset($this->attributes['max_prior_passwords']) ? $this->attributes['max_prior_passwords'] : null;
    }
    // object # Site settings managed by the parent site. Each key identifies a managed setting, with a null value. Non-secret effective values are returned in their own Site settings fields.
    public function getManagedSiteSettings()
    {
        return isset($this->attributes['managed_site_settings']) ? $this->attributes['managed_site_settings'] : null;
    }
    // string # A message to show users when they connect via FTP or SFTP.
    public function getMotdText()
    {
        return isset($this->attributes['motd_text']) ? $this->attributes['motd_text'] : null;
    }
    // boolean # Show message to users connecting via FTP
    public function getMotdUseForFtp()
    {
        return isset($this->attributes['motd_use_for_ftp']) ? $this->attributes['motd_use_for_ftp'] : null;
    }
    // boolean # Show message to users connecting via SFTP
    public function getMotdUseForSftp()
    {
        return isset($this->attributes['motd_use_for_sftp']) ? $this->attributes['motd_use_for_sftp'] : null;
    }
    // decimal # Next billing amount
    public function getNextBillingAmount()
    {
        return isset($this->attributes['next_billing_amount']) ? $this->attributes['next_billing_amount'] : null;
    }
    // string # Next billing date
    public function getNextBillingDate()
    {
        return isset($this->attributes['next_billing_date']) ? $this->attributes['next_billing_date'] : null;
    }
    // boolean # If true, allows users to use a document editing integration.
    public function getOfficeIntegrationAvailable()
    {
        return isset($this->attributes['office_integration_available']) ? $this->attributes['office_integration_available'] : null;
    }
    // string # Which document editing integration to support. Files.com Editor or Microsoft Office for the Web.
    public function getOfficeIntegrationType()
    {
        return isset($this->attributes['office_integration_type']) ? $this->attributes['office_integration_type'] : null;
    }
    // string # Link to scheduling a meeting with our Sales team
    public function getOncehubLink()
    {
        return isset($this->attributes['oncehub_link']) ? $this->attributes['oncehub_link'] : null;
    }
    // boolean # Use servers in the USA only?
    public function getOptOutGlobal()
    {
        return isset($this->attributes['opt_out_global']) ? $this->attributes['opt_out_global'] : null;
    }
    // boolean # Is this site's billing overdue?
    public function getOverdue()
    {
        return isset($this->attributes['overdue']) ? $this->attributes['overdue'] : null;
    }
    // int64 # Shortest password length for users
    public function getPasswordMinLength()
    {
        return isset($this->attributes['password_min_length']) ? $this->attributes['password_min_length'] : null;
    }
    // boolean # Require a letter in passwords?
    public function getPasswordRequireLetter()
    {
        return isset($this->attributes['password_require_letter']) ? $this->attributes['password_require_letter'] : null;
    }
    // boolean # Require lower and upper case letters in passwords?
    public function getPasswordRequireMixed()
    {
        return isset($this->attributes['password_require_mixed']) ? $this->attributes['password_require_mixed'] : null;
    }
    // boolean # Require a number in passwords?
    public function getPasswordRequireNumber()
    {
        return isset($this->attributes['password_require_number']) ? $this->attributes['password_require_number'] : null;
    }
    // boolean # Require special characters in password?
    public function getPasswordRequireSpecial()
    {
        return isset($this->attributes['password_require_special']) ? $this->attributes['password_require_special'] : null;
    }
    // boolean # Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
    public function getPasswordRequireUnbreached()
    {
        return isset($this->attributes['password_require_unbreached']) ? $this->attributes['password_require_unbreached'] : null;
    }
    // boolean # Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
    public function getPasswordRequirementsApplyToBundles()
    {
        return isset($this->attributes['password_requirements_apply_to_bundles']) ? $this->attributes['password_requirements_apply_to_bundles'] : null;
    }
    // int64 # Number of days password is valid
    public function getPasswordValidityDays()
    {
        return isset($this->attributes['password_validity_days']) ? $this->attributes['password_validity_days'] : null;
    }
    // string # Site phone number
    public function getPhone()
    {
        return isset($this->attributes['phone']) ? $this->attributes['phone'] : null;
    }
    // boolean # If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
    public function getPinAllRemoteServersToSiteRegion()
    {
        return isset($this->attributes['pin_all_remote_servers_to_site_region']) ? $this->attributes['pin_all_remote_servers_to_site_region'] : null;
    }
    // boolean # If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
    public function getPreventRootPermissionsForNonSiteAdmins()
    {
        return isset($this->attributes['prevent_root_permissions_for_non_site_admins']) ? $this->attributes['prevent_root_permissions_for_non_site_admins'] : null;
    }
    // boolean # If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
    public function getProtocolAccessGroupsOnly()
    {
        return isset($this->attributes['protocol_access_groups_only']) ? $this->attributes['protocol_access_groups_only'] : null;
    }
    // boolean # Require two-factor authentication for all users?
    public function getRequire2fa()
    {
        return isset($this->attributes['require_2fa']) ? $this->attributes['require_2fa'] : null;
    }
    // boolean # If true, only site admins may create, modify, or delete any behavior at the site root, or a skip that would disable one.
    public function getRestrictRootFolderBehaviorsToSiteAdmins()
    {
        return isset($this->attributes['restrict_root_folder_behaviors_to_site_admins']) ? $this->attributes['restrict_root_folder_behaviors_to_site_admins'] : null;
    }
    // boolean # If true, supported protective behaviors at the site root also apply within named workspaces. Requires restrict_root_folder_behaviors_to_site_admins to be enabled.
    public function getRootFolderBehaviorsApplyToWorkspaces()
    {
        return isset($this->attributes['root_folder_behaviors_apply_to_workspaces']) ? $this->attributes['root_folder_behaviors_apply_to_workspaces'] : null;
    }
    // boolean # If true, SSO users using the default user-level two-factor authentication setting are exempt from the site-wide two-factor authentication requirement.
    public function getRequire2faExemptAllSsoUsers()
    {
        return isset($this->attributes['require_2fa_exempt_all_sso_users']) ? $this->attributes['require_2fa_exempt_all_sso_users'] : null;
    }
    // date-time # If set, requirement for two-factor authentication has been scheduled to end on this date-time.
    public function getRequire2faStopTime()
    {
        return isset($this->attributes['require_2fa_stop_time']) ? $this->attributes['require_2fa_stop_time'] : null;
    }
    // boolean # Auto-removes bundles for disabled/deleted users and enforces bundle expiry within user access period.
    public function getRevokeBundleAccessOnDisableOrDelete()
    {
        return isset($this->attributes['revoke_bundle_access_on_disable_or_delete']) ? $this->attributes['revoke_bundle_access_on_disable_or_delete'] : null;
    }
    // string # What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
    public function getRequire2faUserType()
    {
        return isset($this->attributes['require_2fa_user_type']) ? $this->attributes['require_2fa_user_type'] : null;
    }
    // boolean # If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
    public function getRequireLogoutFromBundlesAndInboxes()
    {
        return isset($this->attributes['require_logout_from_bundles_and_inboxes']) ? $this->attributes['require_logout_from_bundles_and_inboxes'] : null;
    }
    // Session # Current session
    public function getSession()
    {
        return isset($this->attributes['session']) ? $this->attributes['session'] : null;
    }
    // boolean # Is the S3-compatible endpoint enabled for all users and workspaces on this site? Defaults to true. When false, user and group S3 permissions do not allow access.
    public function getS3CompatibleEndpointEnabled()
    {
        return isset($this->attributes['s3_compatible_endpoint_enabled']) ? $this->attributes['s3_compatible_endpoint_enabled'] : null;
    }
    // boolean # Is SFTP enabled?
    public function getSftpEnabled()
    {
        return isset($this->attributes['sftp_enabled']) ? $this->attributes['sftp_enabled'] : null;
    }
    // boolean # Finalize partial SFTP uploads from interrupted connections? Default: true.
    public function getSftpFinalizePartialUploads()
    {
        return isset($this->attributes['sftp_finalize_partial_uploads']) ? $this->attributes['sftp_finalize_partial_uploads'] : null;
    }
    // string # Sftp Host Key Type
    public function getSftpHostKeyType()
    {
        return isset($this->attributes['sftp_host_key_type']) ? $this->attributes['sftp_host_key_type'] : null;
    }
    // int64 # Id of the currently selected custom SFTP Host Key
    public function getActiveSftpHostKeyId()
    {
        return isset($this->attributes['active_sftp_host_key_id']) ? $this->attributes['active_sftp_host_key_id'] : null;
    }
    // array(int64) # Ids of the selected custom SFTP Host Keys
    public function getActiveSftpHostKeyIds()
    {
        return isset($this->attributes['active_sftp_host_key_ids']) ? $this->attributes['active_sftp_host_key_ids'] : null;
    }
    // boolean # If true, we will allow weak and known insecure ciphers to be used for SFTP connections.  Enabling this setting severely weakens the security of your site and it is not recommend, except as a last resort for compatibility.
    public function getSftpInsecureCiphers()
    {
        return isset($this->attributes['sftp_insecure_ciphers']) ? $this->attributes['sftp_insecure_ciphers'] : null;
    }
    // boolean # If true, we will allow weak Diffie Hellman parameters to be used within ciphers for SFTP that are otherwise on our secure list.  This has the effect of making the cipher weaker than our normal threshold for security, but is required to support certain legacy or broken SSH and MFT clients.  Enabling this weakens security, but not nearly as much as enabling the full `sftp_insecure_ciphers` option.
    public function getSftpInsecureDiffieHellman()
    {
        return isset($this->attributes['sftp_insecure_diffie_hellman']) ? $this->attributes['sftp_insecure_diffie_hellman'] : null;
    }
    // boolean # Use user FTP roots also for SFTP?
    public function getSftpUserRootEnabled()
    {
        return isset($this->attributes['sftp_user_root_enabled']) ? $this->attributes['sftp_user_root_enabled'] : null;
    }
    // boolean # Allow bundle creation
    public function getSharingEnabled()
    {
        return isset($this->attributes['sharing_enabled']) ? $this->attributes['sharing_enabled'] : null;
    }
    // boolean # Show log in link in user notifications?
    public function getShowUserNotificationsLogInLink()
    {
        return isset($this->attributes['show_user_notifications_log_in_link']) ? $this->attributes['show_user_notifications_log_in_link'] : null;
    }
    // boolean # Show request access link for users without access?  Currently unused.
    public function getShowRequestAccessLink()
    {
        return isset($this->attributes['show_request_access_link']) ? $this->attributes['show_request_access_link'] : null;
    }
    // string # Custom site footer text for authenticated pages
    public function getSiteFooter()
    {
        return isset($this->attributes['site_footer']) ? $this->attributes['site_footer'] : null;
    }
    // string # Custom site header text for authenticated pages
    public function getSiteHeader()
    {
        return isset($this->attributes['site_header']) ? $this->attributes['site_header'] : null;
    }
    // string # Custom site footer text for public pages
    public function getSitePublicFooter()
    {
        return isset($this->attributes['site_public_footer']) ? $this->attributes['site_public_footer'] : null;
    }
    // string # Custom site header text for public pages
    public function getSitePublicHeader()
    {
        return isset($this->attributes['site_public_header']) ? $this->attributes['site_public_header'] : null;
    }
    // string # SMTP server hostname or IP
    public function getSmtpAddress()
    {
        return isset($this->attributes['smtp_address']) ? $this->attributes['smtp_address'] : null;
    }
    // string # SMTP server authentication type
    public function getSmtpAuthentication()
    {
        return isset($this->attributes['smtp_authentication']) ? $this->attributes['smtp_authentication'] : null;
    }
    // string # From address to use when mailing through custom SMTP
    public function getSmtpFrom()
    {
        return isset($this->attributes['smtp_from']) ? $this->attributes['smtp_from'] : null;
    }
    // int64 # SMTP server port
    public function getSmtpPort()
    {
        return isset($this->attributes['smtp_port']) ? $this->attributes['smtp_port'] : null;
    }
    // string # Custom SMTP encryption mode: if_available (default) uses STARTTLS when offered and otherwise sends credentials and messages unencrypted. A certificate-verified STARTTLS connection automatically changes if_available to require unless smtp_ssl is managed by a parent policy. require requires STARTTLS before authentication; require_implicit uses TLS from connection start; never disables TLS. TLS verifies the server certificate against smtp_address. Encryption is never automatically downgraded.
    public function getSmtpSsl()
    {
        return isset($this->attributes['smtp_ssl']) ? $this->attributes['smtp_ssl'] : null;
    }
    // string # SMTP server username
    public function getSmtpUsername()
    {
        return isset($this->attributes['smtp_username']) ? $this->attributes['smtp_username'] : null;
    }
    // int64 # Session expiry in minutes
    public function getSessionExpiryMinutes()
    {
        return isset($this->attributes['session_expiry_minutes']) ? $this->attributes['session_expiry_minutes'] : null;
    }
    // boolean # Allow snapshot share links creation
    public function getSnapshotSharingEnabled()
    {
        return isset($this->attributes['snapshot_sharing_enabled']) ? $this->attributes['snapshot_sharing_enabled'] : null;
    }
    // boolean # Is SSL required?  Disabling this is insecure.
    public function getSslRequired()
    {
        return isset($this->attributes['ssl_required']) ? $this->attributes['ssl_required'] : null;
    }
    // string # Site subdomain
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    // date-time # If switching plans, when does the new plan take effect?
    public function getSwitchToPlanDate()
    {
        return isset($this->attributes['switch_to_plan_date']) ? $this->attributes['switch_to_plan_date'] : null;
    }
    // int64 # Number of days left in trial
    public function getTrialDaysLeft()
    {
        return isset($this->attributes['trial_days_left']) ? $this->attributes['trial_days_left'] : null;
    }
    // date-time # When does this Site trial expire?
    public function getTrialUntil()
    {
        return isset($this->attributes['trial_until']) ? $this->attributes['trial_until'] : null;
    }
    // boolean # If using custom SMTP, should we use dedicated IPs to deliver emails?
    public function getUseDedicatedIpsForSmtp()
    {
        return isset($this->attributes['use_dedicated_ips_for_smtp']) ? $this->attributes['use_dedicated_ips_for_smtp'] : null;
    }
    // boolean # Allow uploaders to set `provided_modified_at` for uploaded files?
    public function getUseProvidedModifiedAt()
    {
        return isset($this->attributes['use_provided_modified_at']) ? $this->attributes['use_provided_modified_at'] : null;
    }
    // User # User of current session
    public function getUser()
    {
        return isset($this->attributes['user']) ? $this->attributes['user'] : null;
    }
    // boolean # Will users be locked out after incorrect login attempts?
    public function getUserLockout()
    {
        return isset($this->attributes['user_lockout']) ? $this->attributes['user_lockout'] : null;
    }
    // int64 # How many hours to lock user out for failed password?
    public function getUserLockoutLockPeriod()
    {
        return isset($this->attributes['user_lockout_lock_period']) ? $this->attributes['user_lockout_lock_period'] : null;
    }
    // int64 # Number of login tries within `user_lockout_within` hours before users are locked out
    public function getUserLockoutTries()
    {
        return isset($this->attributes['user_lockout_tries']) ? $this->attributes['user_lockout_tries'] : null;
    }
    // int64 # Number of hours for user lockout window
    public function getUserLockoutWithin()
    {
        return isset($this->attributes['user_lockout_within']) ? $this->attributes['user_lockout_within'] : null;
    }
    // boolean # Enable User Requests feature
    public function getUserRequestsEnabled()
    {
        return isset($this->attributes['user_requests_enabled']) ? $this->attributes['user_requests_enabled'] : null;
    }
    // boolean # Send email to site admins when a user request is received?
    public function getUserRequestsNotifyAdmins()
    {
        return isset($this->attributes['user_requests_notify_admins']) ? $this->attributes['user_requests_notify_admins'] : null;
    }
    // boolean # Allow users to create their own API keys?
    public function getUsersCanCreateApiKeys()
    {
        return isset($this->attributes['users_can_create_api_keys']) ? $this->attributes['users_can_create_api_keys'] : null;
    }
    // boolean # Allow users to create their own SSH keys?
    public function getUsersCanCreateSshKeys()
    {
        return isset($this->attributes['users_can_create_ssh_keys']) ? $this->attributes['users_can_create_ssh_keys'] : null;
    }
    // string # How usernames are displayed in the web UI. Can be `username_only`, `full_name_only`, `full_name_username`, `full_name_company`, or `full_name_username_company`.
    public function getUsernameDisplay()
    {
        return isset($this->attributes['username_display']) ? $this->attributes['username_display'] : null;
    }
    // string # Custom text send in user welcome email
    public function getWelcomeCustomText()
    {
        return isset($this->attributes['welcome_custom_text']) ? $this->attributes['welcome_custom_text'] : null;
    }
    // string # Custom footer text for system-generated emails (as Markdown). Supports standard strftime date/time patterns like %Y (4-digit year), %m (month), %d (day).
    public function getEmailFooterCustomText()
    {
        return isset($this->attributes['email_footer_custom_text']) ? $this->attributes['email_footer_custom_text'] : null;
    }
    // email # Include this email in welcome emails if enabled
    public function getWelcomeEmailCc()
    {
        return isset($this->attributes['welcome_email_cc']) ? $this->attributes['welcome_email_cc'] : null;
    }
    // string # Include this email subject in welcome emails if enabled
    public function getWelcomeEmailSubject()
    {
        return isset($this->attributes['welcome_email_subject']) ? $this->attributes['welcome_email_subject'] : null;
    }
    // boolean # Will the welcome email be sent to new users?
    public function getWelcomeEmailEnabled()
    {
        return isset($this->attributes['welcome_email_enabled']) ? $this->attributes['welcome_email_enabled'] : null;
    }
    // string # Does the welcome screen appear?
    public function getWelcomeScreen()
    {
        return isset($this->attributes['welcome_screen']) ? $this->attributes['welcome_screen'] : null;
    }
    // boolean # Does FTP user Windows emulation mode?
    public function getWindowsModeFtp()
    {
        return isset($this->attributes['windows_mode_ftp']) ? $this->attributes['windows_mode_ftp'] : null;
    }

    public static function get($params = [], $options = [])
    {
        $response = Api::sendRequest('/site', 'GET', $options);

        return new Site((array) (@$response->data ?: []), $options);
    }

    public static function getUsage($params = [], $options = [])
    {
        $response = Api::sendRequest('/site/usage', 'GET', $options);

        return new UsageSnapshot((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   name - string - Site name
    //   subdomain - string - Site subdomain
    //   domain - string - Custom domain
    //   domain_hsts_header - boolean - Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
    //   domain_letsencrypt_chain - string - Letsencrypt chain to use when registering SSL Certificate for domain. No longer used as of 2026.
    //   email - string - Main email for this site
    //   reply_to_email - string - Reply-to email for this site
    //   allow_bundle_names - boolean - Are manual Bundle names allowed?
    //   bundle_expiration - int64 - Site-wide Bundle expiration in days
    //   welcome_email_enabled - boolean - Will the welcome email be sent to new users?
    //   ask_about_overwrites - boolean - If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
    //   show_request_access_link - boolean - Show request access link for users without access?  Currently unused.
    //   always_mkdir_parents - boolean - Create parent directories if they do not exist during uploads?  This is primarily used to work around broken upload clients that assume servers will perform this step.
    //   welcome_email_cc - string - Include this email in welcome emails if enabled
    //   welcome_email_subject - string - Include this email subject in welcome emails if enabled
    //   welcome_custom_text - string - Custom text send in user welcome email
    //   language - string - Site default language
    //   windows_mode_ftp - boolean - Does FTP user Windows emulation mode?
    //   default_time_zone - string - Site default time zone
    //   desktop_app - boolean - Is the desktop app enabled?
    //   desktop_app_session_ip_pinning - boolean - Is desktop app session IP pinning enabled?
    //   desktop_app_session_lifetime - int64 - Desktop app session lifetime (in hours)
    //   mobile_app - boolean - Is the mobile app enabled?
    //   mobile_app_session_ip_pinning - boolean - Is mobile app session IP pinning enabled?
    //   mobile_app_session_lifetime - int64 - Mobile app session lifetime (in hours)
    //   folder_permissions_groups_only - boolean - If true, permissions for this site must be bound to a group (not a user).
    //   welcome_screen - string - Does the welcome screen appear?
    //   office_integration_available - boolean - If true, allows users to use a document editing integration.
    //   office_integration_type - string - Which document editing integration to support. Files.com Editor or Microsoft Office for the Web.
    //   pin_all_remote_servers_to_site_region - boolean - If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
    //   motd_text - string - A message to show users when they connect via FTP or SFTP.
    //   motd_use_for_ftp - boolean - Show message to users connecting via FTP
    //   motd_use_for_sftp - boolean - Show message to users connecting via SFTP
    //   left_navigation_visibility - object - Visibility settings for account navigation
    //   disable_all_ai_features - boolean - If true, all AI features are disabled for this site.
    //   ai_feature_availability - object - Availability settings for AI features. Each feature requires the site_admins, workspace_admins, folder_admins, and all_users keys. Optional selected_group_members defaults to false; when true, members of at least one group in group_ids get access regardless of the other options. All availability options are additive: any enabled option matching the user grants access. Optional group_ids is an array of integer IDs of active groups on this site, from any workspace. Omitted or empty group_ids grants no access through selected_group_members and does not affect other options. Disabling all AI features overrides these settings.
    //   mcp_dcr_enabled - boolean - Is OAuth DCR (dynamic client registration) for MCP enabled?
    //   additional_text_file_types - array(string) - Additional extensions that are considered text files
    //   bundle_require_note - boolean - Do Bundles require internal notes?
    //   bundle_send_shared_receipts - boolean - Do Bundle creators receive receipts of invitations?
    //   bundles_default_owned_by_primary_group - boolean - If true, new Share Links created by a user with a primary group will default to that group as owner.
    //   calculate_file_checksums_crc32 - boolean - Calculate CRC32 checksums for files?
    //   calculate_file_checksums_md5 - boolean - Calculate MD5 checksums for files?
    //   calculate_file_checksums_sha1 - boolean - Calculate SHA1 checksums for files?
    //   calculate_file_checksums_sha256 - boolean - Calculate SHA256 checksums for files?
    //   legacy_checksums_mode - boolean - Use legacy checksums mode?
    //   migrate_remote_server_sync_to_sync - boolean - If true, we will migrate all remote server syncs to the new Sync model.
    //   as2_message_retention_days - int64 - Number of days to retain AS2 messages (incoming and outgoing).
    //   file_activity_events_enabled - boolean - If true, file operations produce file_action.activity.v1 EventChannel events in all workspaces. Defaults to false; subscription filters and workspace access still control delivery.
    //   username_display - string - How usernames are displayed in the web UI. Can be `username_only`, `full_name_only`, `full_name_username`, `full_name_company`, or `full_name_username_company`.
    //   integration_centric_remote_server_admin_access - string - Administrator access to user-owned Remote Servers in every Workspace: always_visible (default), never_visible, user_choice_default_visible, or user_choice_default_personal. Forced modes override owner preferences; choice modes use the preference or their default.
    //   session_expiry_minutes - int64 - Session expiry in minutes
    //   ssl_required - boolean - Is SSL required?  Disabling this is insecure.
    //   sftp_insecure_ciphers - boolean - If true, we will allow weak and known insecure ciphers to be used for SFTP connections.  Enabling this setting severely weakens the security of your site and it is not recommend, except as a last resort for compatibility.
    //   sftp_insecure_diffie_hellman - boolean - If true, we will allow weak Diffie Hellman parameters to be used within ciphers for SFTP that are otherwise on our secure list.  This has the effect of making the cipher weaker than our normal threshold for security, but is required to support certain legacy or broken SSH and MFT clients.  Enabling this weakens security, but not nearly as much as enabling the full `sftp_insecure_ciphers` option.
    //   disable_files_certificate_generation - boolean - If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
    //   fedramp - boolean - Are FedRAMP security restrictions enabled for this site?
    //   user_lockout - boolean - Will users be locked out after incorrect login attempts?
    //   user_lockout_tries - int64 - Number of login tries within `user_lockout_within` hours before users are locked out
    //   user_lockout_within - int64 - Number of hours for user lockout window
    //   user_lockout_lock_period - int64 - How many hours to lock user out for failed password?
    //   include_password_in_welcome_email - boolean - Include password in emails to new users?
    //   allowed_countries - string - Comma separated list of allowed Country codes
    //   allowed_ips - string - List of allowed IP addresses
    //   allow_user_level_2fa_override - boolean - Allow the site-wide two-factor authentication requirement to be overriden on a per-user-basis?
    //   allow_user_level_allowed_ip_override - boolean - Allow the site-wide allowed IP restriction to be overriden on a per-user-basis?
    //   allow_user_level_ssl_override - boolean - Allow the site-wide FTP SSL requirement to be overriden on a per-user-basis?
    //   enforce_parent_site_user_ip_restrictions - boolean - Require Parent Site users entering this Child Site to match its allowed IP addresses in addition to their existing IP restrictions. Applies to all Workspaces. Defaults to false.
    //   disallowed_countries - string - Comma separated list of disallowed Country codes
    //   days_to_retain_backups - int64 - Number of days to keep deleted files
    //   max_prior_passwords - int64 - Number of prior passwords to disallow
    //   password_validity_days - int64 - Number of days password is valid
    //   password_min_length - int64 - Shortest password length for users
    //   password_require_letter - boolean - Require a letter in passwords?
    //   password_require_mixed - boolean - Require lower and upper case letters in passwords?
    //   password_require_special - boolean - Require special characters in password?
    //   password_require_number - boolean - Require a number in passwords?
    //   password_require_unbreached - boolean - Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
    //   require_logout_from_bundles_and_inboxes - boolean - If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
    //   dav_user_root_enabled - boolean - Use user FTP roots also for WebDAV?
    //   sftp_user_root_enabled - boolean - Use user FTP roots also for SFTP?
    //   disable_password_reset - boolean - Is password reset disabled?
    //   immutable_files - boolean - Are files protected from modification?
    //   bundle_not_found_message - string - Custom error message to show when bundle is not found.
    //   bundle_password_required - boolean - Do Bundles require password protection?
    //   bundle_require_registration - boolean - Do Bundles require registration?
    //   bundle_require_share_recipient - boolean - Do Bundles require recipients for sharing?
    //   bundle_send_one_time_password_to_recipient_at_registration - boolean - If true, new Share Links must send a one-time password to the recipient when they register. Requires bundle_require_share_recipient and cannot be enabled with bundle_password_required.
    //   bundle_registration_notifications - string - Do Bundle owners receive registration notification?
    //   bundle_activity_notifications - string - Do Bundle owners receive activity notifications?
    //   bundle_upload_receipt_notifications - string - Do Bundle uploaders receive upload confirmation notifications?
    //   document_edits_in_bundle_allowed - boolean - If true, allow public viewers of Bundles with full permissions to use document editing integrations.
    //   password_requirements_apply_to_bundles - boolean - Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
    //   prevent_root_permissions_for_non_site_admins - boolean - If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
    //   restrict_root_folder_behaviors_to_site_admins - boolean - If true, only site admins may create, modify, or delete any behavior at the site root, or a skip that would disable one.
    //   root_folder_behaviors_apply_to_workspaces - boolean - If true, supported protective behaviors at the site root also apply within named workspaces. Requires restrict_root_folder_behaviors_to_site_admins to be enabled.
    //   opt_out_global - boolean - Use servers in the USA only?
    //   use_provided_modified_at - boolean - Allow uploaders to set `provided_modified_at` for uploaded files?
    //   custom_namespace - boolean - Is this site using a custom namespace for users?
    //   non_sso_groups_allowed - boolean - If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
    //   non_sso_users_allowed - boolean - If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
    //   sharing_enabled - boolean - Allow bundle creation
    //   snapshot_sharing_enabled - boolean - Allow snapshot share links creation
    //   user_requests_enabled - boolean - Enable User Requests feature
    //   user_requests_notify_admins - boolean - Send email to site admins when a user request is received?
    //   dav_enabled - boolean - Is WebDAV enabled?
    //   files_com_remote_server_enabled - boolean - Allow other Files.com sites to use this site's API keys for native Files.com Remote Server connections? Defaults to true and applies to all keys and workspaces on this site. When false, new pairings and access through existing connections are rejected without revoking keys or deleting connections. Re-enabling permits access again with usable keys. Does not disable this site's connections to other sites, ordinary API access, or Connected Sites.
    //   ftp_enabled - boolean - Is FTP enabled?
    //   s3_compatible_endpoint_enabled - boolean - Is the S3-compatible endpoint enabled for all users and workspaces on this site? Defaults to true. When false, user and group S3 permissions do not allow access.
    //   sftp_enabled - boolean - Is SFTP enabled?
    //   sftp_finalize_partial_uploads - boolean - Finalize partial SFTP uploads from interrupted connections? Default: true.
    //   users_can_create_api_keys - boolean - Allow users to create their own API keys?
    //   users_can_create_ssh_keys - boolean - Allow users to create their own SSH keys?
    //   show_user_notifications_log_in_link - boolean - Show log in link in user notifications?
    //   sftp_host_key_type - string - Sftp Host Key Type
    //   active_sftp_host_key_id - int64 - Id of the currently selected custom SFTP Host Key
    //   active_sftp_host_key_ids - array(int64) - Ids of the selected custom SFTP Host Keys
    //   protocol_access_groups_only - boolean - If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
    //   revoke_bundle_access_on_disable_or_delete - boolean - Auto-removes bundles for disabled/deleted users and enforces bundle expiry within user access period.
    //   group_admins_can_add_users - boolean - Allow group admins to create users in their groups
    //   group_admins_can_manage_group_memberships - boolean - Allow group admins to add or remove existing users in their groups
    //   group_admins_can_delete_users - boolean - Allow group admins to delete users in their groups
    //   group_admins_can_enable_disable_users - boolean - Allow group admins to enable or disable users in their groups
    //   group_admins_can_modify_users - boolean - Allow group admins to modify users in their groups
    //   group_admins_can_bypass_user_lifecycle_rules - boolean - Allow group admins to exempt users in their groups from lifecycle rules
    //   group_admins_can_reset_passwords - boolean - Allow group admins to reset passwords for users in their groups
    //   group_admins_can_set_user_password - boolean - Allow group admins to set password authentication method
    //   child_site_folder_admins_can_see_parent_site_directory - boolean - If true, administrators on this site's Child Sites, including Folder Admins, can list this site's Default Workspace users and groups and grant them folder permissions and notifications on Child Site paths.
    //   workspace_folder_admins_can_see_default_workspace_directory - boolean - If true, Workspace Administrators and Folder Admins in a Custom Workspace can list the Default Workspace users and groups that have permissions in that Workspace and choose them as Email Notification recipients.
    //   bundle_recipient_blacklist_free_email_domains - boolean - Disallow free email domains for Bundle/Inbox recipients?
    //   bundle_recipient_blacklist_domains - array(string) - List of email domains to disallow when entering a Bundle/Inbox recipients
    //   admins_bypass_locked_subfolders - boolean - Allow admins to bypass the locked subfolders setting.
    //   allowed_2fa_method_sms - boolean - Is SMS two factor authentication allowed?
    //   allowed_2fa_method_totp - boolean - Is TOTP two factor authentication allowed?
    //   allowed_2fa_method_webauthn - boolean - Is WebAuthn two factor authentication allowed?
    //   allowed_2fa_method_yubi - boolean - Is yubikey two factor authentication allowed?
    //   allowed_2fa_method_email - boolean - Is OTP via email two factor authentication allowed?
    //   allowed_2fa_method_static - boolean - Is OTP via static codes for two factor authentication allowed?
    //   allowed_2fa_method_bypass_for_ftp_sftp_dav - boolean - Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
    //   require_2fa - boolean - Require two-factor authentication for all users?
    //   require_2fa_exempt_all_sso_users - boolean - If true, SSO users using the default user-level two-factor authentication setting are exempt from the site-wide two-factor authentication requirement.
    //   require_2fa_user_type - string - What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
    //   color2_top - string - Top bar background color
    //   color2_left - string - Page link and button color
    //   color2_link - string - Top bar link color
    //   color2_text - string - Page link and button color
    //   color2_top_text - string - Top bar text color
    //   site_header - string - Custom site header text for authenticated pages
    //   site_footer - string - Custom site footer text for authenticated pages
    //   site_public_header - string - Custom site header text for public pages
    //   site_public_footer - string - Custom site footer text for public pages
    //   login_help_text - string - Login help text
    //   use_dedicated_ips_for_smtp - boolean - If using custom SMTP, should we use dedicated IPs to deliver emails?
    //   email_footer_custom_text - string - Custom footer text for system-generated emails (as Markdown). Supports standard strftime date/time patterns like %Y (4-digit year), %m (month), %d (day).
    //   smtp_address - string - SMTP server hostname or IP
    //   smtp_authentication - string - SMTP server authentication type
    //   smtp_from - string - From address to use when mailing through custom SMTP
    //   smtp_username - string - SMTP server username
    //   smtp_port - int64 - SMTP server port
    //   smtp_ssl - string - Custom SMTP encryption mode: if_available (default) uses STARTTLS when offered and otherwise sends credentials and messages unencrypted. A certificate-verified STARTTLS connection automatically changes if_available to require unless smtp_ssl is managed by a parent policy. require requires STARTTLS before authentication; require_implicit uses TLS from connection start; never disables TLS. TLS verifies the server certificate against smtp_address. Encryption is never automatically downgraded.
    //   ldap_enabled - boolean - Main LDAP setting: is LDAP enabled?
    //   ldap_type - string - LDAP type
    //   ldap_host - string - LDAP host
    //   ldap_host_2 - string - LDAP backup host
    //   ldap_host_3 - string - LDAP backup host
    //   ldap_port - int64 - LDAP port
    //   ldap_secure - boolean - Use secure LDAP?
    //   ldap_server_certificate - string - How to validate the LDAP server certificate. `require_match` validates the certificate chain and hostname; `allow_any` disables certificate validation.
    //   ldap_username - string - Username for signing in to LDAP server.
    //   ldap_username_field - string - LDAP username field
    //   ldap_domain - string - Domain name that will be appended to usernames
    //   ldap_user_action - string - Should we sync users from LDAP server?
    //   ldap_group_action - string - Should we sync groups from LDAP server?
    //   ldap_user_include_groups - string - Comma or newline separated list of group names (with optional wildcards) - if provided, only users in these groups will be added or synced.
    //   ldap_group_exclusion - string - Comma or newline separated list of group names (with optional wildcards) to exclude when syncing.
    //   ldap_group_inclusion - string - Comma or newline separated list of group names (with optional wildcards) to include when syncing.
    //   ldap_base_dn - string - Base DN for looking up users in LDAP server
    //   uploads_via_email_authentication - boolean - Require email authentication, virus, and spam checks for incoming emails to Inboxes and Incoming Email Automations in every Workspace on this site?
    //   bundle_watermark_value - object - Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
    //   icon16_file - file
    //   icon16_delete - boolean - If true, will delete the file stored in icon16
    //   icon32_file - file
    //   icon32_delete - boolean - If true, will delete the file stored in icon32
    //   icon48_file - file
    //   icon48_delete - boolean - If true, will delete the file stored in icon48
    //   icon128_file - file
    //   icon128_delete - boolean - If true, will delete the file stored in icon128
    //   logo_file - file
    //   logo_delete - boolean - If true, will delete the file stored in logo
    //   bundle_watermark_attachment_file - file
    //   bundle_watermark_attachment_delete - boolean - If true, will delete the file stored in bundle_watermark_attachment
    //   login_page_background_image_file - file
    //   login_page_background_image_delete - boolean - If true, will delete the file stored in login_page_background_image
    //   disable_2fa_with_delay - boolean - If set to true, we will begin the process of disabling 2FA on this site.
    //   ldap_password_change - string - New LDAP password.
    //   ldap_password_change_confirmation - string - Confirm new LDAP password.
    //   redirect_old_subdomain - boolean - If true, and if changing the site subdomain, then create a redirect from the previous Files.com subdomain to the new Files.com subdomain.
    //   smtp_password - string - Password for SMTP server.
    public static function update($params = [], $options = [])
    {
        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['subdomain']) && !is_string($params['subdomain'])) {
            throw new \Files\Exception\InvalidParameterException('$subdomain must be of type string; received ' . gettype(@$params['subdomain']));
        }

        if (!empty($params['domain']) && !is_string($params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        if (!empty($params['domain_letsencrypt_chain']) && !is_string($params['domain_letsencrypt_chain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain_letsencrypt_chain must be of type string; received ' . gettype(@$params['domain_letsencrypt_chain']));
        }

        if (!empty($params['email']) && !is_string($params['email'])) {
            throw new \Files\Exception\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
        }

        if (!empty($params['reply_to_email']) && !is_string($params['reply_to_email'])) {
            throw new \Files\Exception\InvalidParameterException('$reply_to_email must be of type string; received ' . gettype(@$params['reply_to_email']));
        }

        if (!empty($params['bundle_expiration']) && !is_int($params['bundle_expiration'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_expiration must be of type int; received ' . gettype(@$params['bundle_expiration']));
        }

        if (!empty($params['welcome_email_cc']) && !is_string($params['welcome_email_cc'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_email_cc must be of type string; received ' . gettype(@$params['welcome_email_cc']));
        }

        if (!empty($params['welcome_email_subject']) && !is_string($params['welcome_email_subject'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_email_subject must be of type string; received ' . gettype(@$params['welcome_email_subject']));
        }

        if (!empty($params['welcome_custom_text']) && !is_string($params['welcome_custom_text'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_custom_text must be of type string; received ' . gettype(@$params['welcome_custom_text']));
        }

        if (!empty($params['language']) && !is_string($params['language'])) {
            throw new \Files\Exception\InvalidParameterException('$language must be of type string; received ' . gettype(@$params['language']));
        }

        if (!empty($params['default_time_zone']) && !is_string($params['default_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$default_time_zone must be of type string; received ' . gettype(@$params['default_time_zone']));
        }

        if (!empty($params['desktop_app_session_lifetime']) && !is_int($params['desktop_app_session_lifetime'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_app_session_lifetime must be of type int; received ' . gettype(@$params['desktop_app_session_lifetime']));
        }

        if (!empty($params['mobile_app_session_lifetime']) && !is_int($params['mobile_app_session_lifetime'])) {
            throw new \Files\Exception\InvalidParameterException('$mobile_app_session_lifetime must be of type int; received ' . gettype(@$params['mobile_app_session_lifetime']));
        }

        if (!empty($params['welcome_screen']) && !is_string($params['welcome_screen'])) {
            throw new \Files\Exception\InvalidParameterException('$welcome_screen must be of type string; received ' . gettype(@$params['welcome_screen']));
        }

        if (!empty($params['office_integration_type']) && !is_string($params['office_integration_type'])) {
            throw new \Files\Exception\InvalidParameterException('$office_integration_type must be of type string; received ' . gettype(@$params['office_integration_type']));
        }

        if (!empty($params['motd_text']) && !is_string($params['motd_text'])) {
            throw new \Files\Exception\InvalidParameterException('$motd_text must be of type string; received ' . gettype(@$params['motd_text']));
        }

        if (!empty($params['additional_text_file_types']) && !is_array($params['additional_text_file_types'])) {
            throw new \Files\Exception\InvalidParameterException('$additional_text_file_types must be of type array; received ' . gettype(@$params['additional_text_file_types']));
        }

        if (!empty($params['as2_message_retention_days']) && !is_int($params['as2_message_retention_days'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_message_retention_days must be of type int; received ' . gettype(@$params['as2_message_retention_days']));
        }

        if (!empty($params['username_display']) && !is_string($params['username_display'])) {
            throw new \Files\Exception\InvalidParameterException('$username_display must be of type string; received ' . gettype(@$params['username_display']));
        }

        if (!empty($params['integration_centric_remote_server_admin_access']) && !is_string($params['integration_centric_remote_server_admin_access'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_remote_server_admin_access must be of type string; received ' . gettype(@$params['integration_centric_remote_server_admin_access']));
        }

        if (!empty($params['session_expiry_minutes']) && !is_int($params['session_expiry_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$session_expiry_minutes must be of type int; received ' . gettype(@$params['session_expiry_minutes']));
        }

        if (!empty($params['user_lockout_tries']) && !is_int($params['user_lockout_tries'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_tries must be of type int; received ' . gettype(@$params['user_lockout_tries']));
        }

        if (!empty($params['user_lockout_within']) && !is_int($params['user_lockout_within'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_within must be of type int; received ' . gettype(@$params['user_lockout_within']));
        }

        if (!empty($params['user_lockout_lock_period']) && !is_int($params['user_lockout_lock_period'])) {
            throw new \Files\Exception\InvalidParameterException('$user_lockout_lock_period must be of type int; received ' . gettype(@$params['user_lockout_lock_period']));
        }

        if (!empty($params['allowed_countries']) && !is_string($params['allowed_countries'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_countries must be of type string; received ' . gettype(@$params['allowed_countries']));
        }

        if (!empty($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (!empty($params['disallowed_countries']) && !is_string($params['disallowed_countries'])) {
            throw new \Files\Exception\InvalidParameterException('$disallowed_countries must be of type string; received ' . gettype(@$params['disallowed_countries']));
        }

        if (!empty($params['days_to_retain_backups']) && !is_int($params['days_to_retain_backups'])) {
            throw new \Files\Exception\InvalidParameterException('$days_to_retain_backups must be of type int; received ' . gettype(@$params['days_to_retain_backups']));
        }

        if (!empty($params['max_prior_passwords']) && !is_int($params['max_prior_passwords'])) {
            throw new \Files\Exception\InvalidParameterException('$max_prior_passwords must be of type int; received ' . gettype(@$params['max_prior_passwords']));
        }

        if (!empty($params['password_validity_days']) && !is_int($params['password_validity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype(@$params['password_validity_days']));
        }

        if (!empty($params['password_min_length']) && !is_int($params['password_min_length'])) {
            throw new \Files\Exception\InvalidParameterException('$password_min_length must be of type int; received ' . gettype(@$params['password_min_length']));
        }

        if (!empty($params['bundle_not_found_message']) && !is_string($params['bundle_not_found_message'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_not_found_message must be of type string; received ' . gettype(@$params['bundle_not_found_message']));
        }

        if (!empty($params['bundle_registration_notifications']) && !is_string($params['bundle_registration_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_notifications must be of type string; received ' . gettype(@$params['bundle_registration_notifications']));
        }

        if (!empty($params['bundle_activity_notifications']) && !is_string($params['bundle_activity_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_activity_notifications must be of type string; received ' . gettype(@$params['bundle_activity_notifications']));
        }

        if (!empty($params['bundle_upload_receipt_notifications']) && !is_string($params['bundle_upload_receipt_notifications'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_upload_receipt_notifications must be of type string; received ' . gettype(@$params['bundle_upload_receipt_notifications']));
        }

        if (!empty($params['sftp_host_key_type']) && !is_string($params['sftp_host_key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_host_key_type must be of type string; received ' . gettype(@$params['sftp_host_key_type']));
        }

        if (!empty($params['active_sftp_host_key_id']) && !is_int($params['active_sftp_host_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$active_sftp_host_key_id must be of type int; received ' . gettype(@$params['active_sftp_host_key_id']));
        }

        if (!empty($params['active_sftp_host_key_ids']) && !is_array($params['active_sftp_host_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$active_sftp_host_key_ids must be of type array; received ' . gettype(@$params['active_sftp_host_key_ids']));
        }

        if (!empty($params['bundle_recipient_blacklist_domains']) && !is_array($params['bundle_recipient_blacklist_domains'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_recipient_blacklist_domains must be of type array; received ' . gettype(@$params['bundle_recipient_blacklist_domains']));
        }

        if (!empty($params['require_2fa_user_type']) && !is_string($params['require_2fa_user_type'])) {
            throw new \Files\Exception\InvalidParameterException('$require_2fa_user_type must be of type string; received ' . gettype(@$params['require_2fa_user_type']));
        }

        if (!empty($params['color2_top']) && !is_string($params['color2_top'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_top must be of type string; received ' . gettype(@$params['color2_top']));
        }

        if (!empty($params['color2_left']) && !is_string($params['color2_left'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_left must be of type string; received ' . gettype(@$params['color2_left']));
        }

        if (!empty($params['color2_link']) && !is_string($params['color2_link'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_link must be of type string; received ' . gettype(@$params['color2_link']));
        }

        if (!empty($params['color2_text']) && !is_string($params['color2_text'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_text must be of type string; received ' . gettype(@$params['color2_text']));
        }

        if (!empty($params['color2_top_text']) && !is_string($params['color2_top_text'])) {
            throw new \Files\Exception\InvalidParameterException('$color2_top_text must be of type string; received ' . gettype(@$params['color2_top_text']));
        }

        if (!empty($params['site_header']) && !is_string($params['site_header'])) {
            throw new \Files\Exception\InvalidParameterException('$site_header must be of type string; received ' . gettype(@$params['site_header']));
        }

        if (!empty($params['site_footer']) && !is_string($params['site_footer'])) {
            throw new \Files\Exception\InvalidParameterException('$site_footer must be of type string; received ' . gettype(@$params['site_footer']));
        }

        if (!empty($params['site_public_header']) && !is_string($params['site_public_header'])) {
            throw new \Files\Exception\InvalidParameterException('$site_public_header must be of type string; received ' . gettype(@$params['site_public_header']));
        }

        if (!empty($params['site_public_footer']) && !is_string($params['site_public_footer'])) {
            throw new \Files\Exception\InvalidParameterException('$site_public_footer must be of type string; received ' . gettype(@$params['site_public_footer']));
        }

        if (!empty($params['login_help_text']) && !is_string($params['login_help_text'])) {
            throw new \Files\Exception\InvalidParameterException('$login_help_text must be of type string; received ' . gettype(@$params['login_help_text']));
        }

        if (!empty($params['email_footer_custom_text']) && !is_string($params['email_footer_custom_text'])) {
            throw new \Files\Exception\InvalidParameterException('$email_footer_custom_text must be of type string; received ' . gettype(@$params['email_footer_custom_text']));
        }

        if (!empty($params['smtp_address']) && !is_string($params['smtp_address'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_address must be of type string; received ' . gettype(@$params['smtp_address']));
        }

        if (!empty($params['smtp_authentication']) && !is_string($params['smtp_authentication'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_authentication must be of type string; received ' . gettype(@$params['smtp_authentication']));
        }

        if (!empty($params['smtp_from']) && !is_string($params['smtp_from'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_from must be of type string; received ' . gettype(@$params['smtp_from']));
        }

        if (!empty($params['smtp_username']) && !is_string($params['smtp_username'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_username must be of type string; received ' . gettype(@$params['smtp_username']));
        }

        if (!empty($params['smtp_port']) && !is_int($params['smtp_port'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_port must be of type int; received ' . gettype(@$params['smtp_port']));
        }

        if (!empty($params['smtp_ssl']) && !is_string($params['smtp_ssl'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_ssl must be of type string; received ' . gettype(@$params['smtp_ssl']));
        }

        if (!empty($params['ldap_type']) && !is_string($params['ldap_type'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_type must be of type string; received ' . gettype(@$params['ldap_type']));
        }

        if (!empty($params['ldap_host']) && !is_string($params['ldap_host'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_host must be of type string; received ' . gettype(@$params['ldap_host']));
        }

        if (!empty($params['ldap_host_2']) && !is_string($params['ldap_host_2'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_host_2 must be of type string; received ' . gettype(@$params['ldap_host_2']));
        }

        if (!empty($params['ldap_host_3']) && !is_string($params['ldap_host_3'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_host_3 must be of type string; received ' . gettype(@$params['ldap_host_3']));
        }

        if (!empty($params['ldap_port']) && !is_int($params['ldap_port'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_port must be of type int; received ' . gettype(@$params['ldap_port']));
        }

        if (!empty($params['ldap_server_certificate']) && !is_string($params['ldap_server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_server_certificate must be of type string; received ' . gettype(@$params['ldap_server_certificate']));
        }

        if (!empty($params['ldap_username']) && !is_string($params['ldap_username'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_username must be of type string; received ' . gettype(@$params['ldap_username']));
        }

        if (!empty($params['ldap_username_field']) && !is_string($params['ldap_username_field'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_username_field must be of type string; received ' . gettype(@$params['ldap_username_field']));
        }

        if (!empty($params['ldap_domain']) && !is_string($params['ldap_domain'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_domain must be of type string; received ' . gettype(@$params['ldap_domain']));
        }

        if (!empty($params['ldap_user_action']) && !is_string($params['ldap_user_action'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_user_action must be of type string; received ' . gettype(@$params['ldap_user_action']));
        }

        if (!empty($params['ldap_group_action']) && !is_string($params['ldap_group_action'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_group_action must be of type string; received ' . gettype(@$params['ldap_group_action']));
        }

        if (!empty($params['ldap_user_include_groups']) && !is_string($params['ldap_user_include_groups'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_user_include_groups must be of type string; received ' . gettype(@$params['ldap_user_include_groups']));
        }

        if (!empty($params['ldap_group_exclusion']) && !is_string($params['ldap_group_exclusion'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_group_exclusion must be of type string; received ' . gettype(@$params['ldap_group_exclusion']));
        }

        if (!empty($params['ldap_group_inclusion']) && !is_string($params['ldap_group_inclusion'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_group_inclusion must be of type string; received ' . gettype(@$params['ldap_group_inclusion']));
        }

        if (!empty($params['ldap_base_dn']) && !is_string($params['ldap_base_dn'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_base_dn must be of type string; received ' . gettype(@$params['ldap_base_dn']));
        }

        if (!empty($params['ldap_password_change']) && !is_string($params['ldap_password_change'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_password_change must be of type string; received ' . gettype(@$params['ldap_password_change']));
        }

        if (!empty($params['ldap_password_change_confirmation']) && !is_string($params['ldap_password_change_confirmation'])) {
            throw new \Files\Exception\InvalidParameterException('$ldap_password_change_confirmation must be of type string; received ' . gettype(@$params['ldap_password_change_confirmation']));
        }

        if (!empty($params['smtp_password']) && !is_string($params['smtp_password'])) {
            throw new \Files\Exception\InvalidParameterException('$smtp_password must be of type string; received ' . gettype(@$params['smtp_password']));
        }

        $response = Api::sendRequest('/site', 'PATCH', $params, $options);

        return new Site((array) (@$response->data ?: []), $options);
    }
}
