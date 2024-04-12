<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Site
 *
 * @package Files
 */
class Site {
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

  // string # Site name
  public function getName() {
    return @$this->attributes['name'];
  }

  // boolean # Is SMS two factor authentication allowed?
  public function getAllowed2faMethodSms() {
    return @$this->attributes['allowed_2fa_method_sms'];
  }

  // boolean # Is TOTP two factor authentication allowed?
  public function getAllowed2faMethodTotp() {
    return @$this->attributes['allowed_2fa_method_totp'];
  }

  // boolean # Is U2F two factor authentication allowed?
  public function getAllowed2faMethodU2f() {
    return @$this->attributes['allowed_2fa_method_u2f'];
  }

  // boolean # Is WebAuthn two factor authentication allowed?
  public function getAllowed2faMethodWebauthn() {
    return @$this->attributes['allowed_2fa_method_webauthn'];
  }

  // boolean # Is yubikey two factor authentication allowed?
  public function getAllowed2faMethodYubi() {
    return @$this->attributes['allowed_2fa_method_yubi'];
  }

  // boolean # Is OTP via email two factor authentication allowed?
  public function getAllowed2faMethodEmail() {
    return @$this->attributes['allowed_2fa_method_email'];
  }

  // boolean # Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
  public function getAllowed2faMethodBypassForFtpSftpDav() {
    return @$this->attributes['allowed_2fa_method_bypass_for_ftp_sftp_dav'];
  }

  // int64 # User ID for the main site administrator
  public function getAdminUserId() {
    return @$this->attributes['admin_user_id'];
  }

  // boolean # Allow admins to bypass the locked subfolders setting.
  public function getAdminsBypassLockedSubfolders() {
    return @$this->attributes['admins_bypass_locked_subfolders'];
  }

  // boolean # Are manual Bundle names allowed?
  public function getAllowBundleNames() {
    return @$this->attributes['allow_bundle_names'];
  }

  // string # Comma seperated list of allowed Country codes
  public function getAllowedCountries() {
    return @$this->attributes['allowed_countries'];
  }

  // string # List of allowed IP addresses
  public function getAllowedIps() {
    return @$this->attributes['allowed_ips'];
  }

  // boolean # If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
  public function getAskAboutOverwrites() {
    return @$this->attributes['ask_about_overwrites'];
  }

  // string # Do Bundle owners receive activity notifications?
  public function getBundleActivityNotifications() {
    return @$this->attributes['bundle_activity_notifications'];
  }

  // int64 # Site-wide Bundle expiration in days
  public function getBundleExpiration() {
    return @$this->attributes['bundle_expiration'];
  }

  // string # Custom error message to show when bundle is not found.
  public function getBundleNotFoundMessage() {
    return @$this->attributes['bundle_not_found_message'];
  }

  // boolean # Do Bundles require password protection?
  public function getBundlePasswordRequired() {
    return @$this->attributes['bundle_password_required'];
  }

  // array # List of email domains to disallow when entering a Bundle/Inbox recipients
  public function getBundleRecipientBlacklistDomains() {
    return @$this->attributes['bundle_recipient_blacklist_domains'];
  }

  // boolean # Disallow free email domains for Bundle/Inbox recipients?
  public function getBundleRecipientBlacklistFreeEmailDomains() {
    return @$this->attributes['bundle_recipient_blacklist_free_email_domains'];
  }

  // string # Do Bundle owners receive registration notification?
  public function getBundleRegistrationNotifications() {
    return @$this->attributes['bundle_registration_notifications'];
  }

  // boolean # Do Bundles require registration?
  public function getBundleRequireRegistration() {
    return @$this->attributes['bundle_require_registration'];
  }

  // boolean # Do Bundles require recipients for sharing?
  public function getBundleRequireShareRecipient() {
    return @$this->attributes['bundle_require_share_recipient'];
  }

  // string # Do Bundle uploaders receive upload confirmation notifications?
  public function getBundleUploadReceiptNotifications() {
    return @$this->attributes['bundle_upload_receipt_notifications'];
  }

  // Image # Preview watermark image applied to all bundle items.
  public function getBundleWatermarkAttachment() {
    return @$this->attributes['bundle_watermark_attachment'];
  }

  // object # Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
  public function getBundleWatermarkValue() {
    return @$this->attributes['bundle_watermark_value'];
  }

  // boolean # Do incoming emails in the Inboxes require checking for SPF/DKIM/DMARC?
  public function getUploadsViaEmailAuthentication() {
    return @$this->attributes['uploads_via_email_authentication'];
  }

  // string # Page link and button color
  public function getColor2Left() {
    return @$this->attributes['color2_left'];
  }

  // string # Top bar link color
  public function getColor2Link() {
    return @$this->attributes['color2_link'];
  }

  // string # Page link and button color
  public function getColor2Text() {
    return @$this->attributes['color2_text'];
  }

  // string # Top bar background color
  public function getColor2Top() {
    return @$this->attributes['color2_top'];
  }

  // string # Top bar text color
  public function getColor2TopText() {
    return @$this->attributes['color2_top_text'];
  }

  // string # Site main contact name
  public function getContactName() {
    return @$this->attributes['contact_name'];
  }

  // date-time # Time this site was created
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // string # Preferred currency
  public function getCurrency() {
    return @$this->attributes['currency'];
  }

  // boolean # Is this site using a custom namespace for users?
  public function getCustomNamespace() {
    return @$this->attributes['custom_namespace'];
  }

  // boolean # Is WebDAV enabled?
  public function getDavEnabled() {
    return @$this->attributes['dav_enabled'];
  }

  // boolean # Use user FTP roots also for WebDAV?
  public function getDavUserRootEnabled() {
    return @$this->attributes['dav_user_root_enabled'];
  }

  // int64 # Number of days to keep deleted files
  public function getDaysToRetainBackups() {
    return @$this->attributes['days_to_retain_backups'];
  }

  // string # Site default time zone
  public function getDefaultTimeZone() {
    return @$this->attributes['default_time_zone'];
  }

  // boolean # Is the desktop app enabled?
  public function getDesktopApp() {
    return @$this->attributes['desktop_app'];
  }

  // boolean # Is desktop app session IP pinning enabled?
  public function getDesktopAppSessionIpPinning() {
    return @$this->attributes['desktop_app_session_ip_pinning'];
  }

  // int64 # Desktop app session lifetime (in hours)
  public function getDesktopAppSessionLifetime() {
    return @$this->attributes['desktop_app_session_lifetime'];
  }

  // boolean # Is the mobile app enabled?
  public function getMobileApp() {
    return @$this->attributes['mobile_app'];
  }

  // boolean # Is mobile app session IP pinning enabled?
  public function getMobileAppSessionIpPinning() {
    return @$this->attributes['mobile_app_session_ip_pinning'];
  }

  // int64 # Mobile app session lifetime (in hours)
  public function getMobileAppSessionLifetime() {
    return @$this->attributes['mobile_app_session_lifetime'];
  }

  // string # Comma seperated list of disallowed Country codes
  public function getDisallowedCountries() {
    return @$this->attributes['disallowed_countries'];
  }

  // boolean # If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
  public function getDisableFilesCertificateGeneration() {
    return @$this->attributes['disable_files_certificate_generation'];
  }

  // boolean # Are notifications disabled?
  public function getDisableNotifications() {
    return @$this->attributes['disable_notifications'];
  }

  // boolean # Is password reset disabled?
  public function getDisablePasswordReset() {
    return @$this->attributes['disable_password_reset'];
  }

  // string # Custom domain
  public function getDomain() {
    return @$this->attributes['domain'];
  }

  // boolean # Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
  public function getDomainHstsHeader() {
    return @$this->attributes['domain_hsts_header'];
  }

  // string # Letsencrypt chain to use when registering SSL Certificate for domain.
  public function getDomainLetsencryptChain() {
    return @$this->attributes['domain_letsencrypt_chain'];
  }

  // email # Main email for this site
  public function getEmail() {
    return @$this->attributes['email'];
  }

  // boolean # Is FTP enabled?
  public function getFtpEnabled() {
    return @$this->attributes['ftp_enabled'];
  }

  // email # Reply-to email for this site
  public function getReplyToEmail() {
    return @$this->attributes['reply_to_email'];
  }

  // boolean # If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
  public function getNonSsoGroupsAllowed() {
    return @$this->attributes['non_sso_groups_allowed'];
  }

  // boolean # If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
  public function getNonSsoUsersAllowed() {
    return @$this->attributes['non_sso_users_allowed'];
  }

  // boolean # If true, permissions for this site must be bound to a group (not a user). Otherwise, permissions must be bound to a user.
  public function getFolderPermissionsGroupsOnly() {
    return @$this->attributes['folder_permissions_groups_only'];
  }

  // boolean # Is there a signed HIPAA BAA between Files.com and this site?
  public function getHipaa() {
    return @$this->attributes['hipaa'];
  }

  // Image # Branded icon 128x128
  public function getIcon128() {
    return @$this->attributes['icon128'];
  }

  // Image # Branded icon 16x16
  public function getIcon16() {
    return @$this->attributes['icon16'];
  }

  // Image # Branded icon 32x32
  public function getIcon32() {
    return @$this->attributes['icon32'];
  }

  // Image # Branded icon 48x48
  public function getIcon48() {
    return @$this->attributes['icon48'];
  }

  // date-time # Can files be modified?
  public function getImmutableFilesSetAt() {
    return @$this->attributes['immutable_files_set_at'];
  }

  // boolean # Include password in emails to new users?
  public function getIncludePasswordInWelcomeEmail() {
    return @$this->attributes['include_password_in_welcome_email'];
  }

  // string # Site default language
  public function getLanguage() {
    return @$this->attributes['language'];
  }

  // string # Base DN for looking up users in LDAP server
  public function getLdapBaseDn() {
    return @$this->attributes['ldap_base_dn'];
  }

  // string # Domain name that will be appended to usernames
  public function getLdapDomain() {
    return @$this->attributes['ldap_domain'];
  }

  // boolean # Main LDAP setting: is LDAP enabled?
  public function getLdapEnabled() {
    return @$this->attributes['ldap_enabled'];
  }

  // string # Should we sync groups from LDAP server?
  public function getLdapGroupAction() {
    return @$this->attributes['ldap_group_action'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to exclude when syncing.
  public function getLdapGroupExclusion() {
    return @$this->attributes['ldap_group_exclusion'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to include when syncing.
  public function getLdapGroupInclusion() {
    return @$this->attributes['ldap_group_inclusion'];
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

  // string # LDAP type
  public function getLdapType() {
    return @$this->attributes['ldap_type'];
  }

  // string # Should we sync users from LDAP server?
  public function getLdapUserAction() {
    return @$this->attributes['ldap_user_action'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) - if provided, only users in these groups will be added or synced.
  public function getLdapUserIncludeGroups() {
    return @$this->attributes['ldap_user_include_groups'];
  }

  // string # Username for signing in to LDAP server.
  public function getLdapUsername() {
    return @$this->attributes['ldap_username'];
  }

  // string # LDAP username field
  public function getLdapUsernameField() {
    return @$this->attributes['ldap_username_field'];
  }

  // string # Login help text
  public function getLoginHelpText() {
    return @$this->attributes['login_help_text'];
  }

  // Image # Branded logo
  public function getLogo() {
    return @$this->attributes['logo'];
  }

  // Image # Branded login page background
  public function getLoginPageBackgroundImage() {
    return @$this->attributes['login_page_background_image'];
  }

  // int64 # Number of prior passwords to disallow
  public function getMaxPriorPasswords() {
    return @$this->attributes['max_prior_passwords'];
  }

  // string # A message to show users when they connect via FTP or SFTP.
  public function getMotdText() {
    return @$this->attributes['motd_text'];
  }

  // boolean # Show message to users connecting via FTP
  public function getMotdUseForFtp() {
    return @$this->attributes['motd_use_for_ftp'];
  }

  // boolean # Show message to users connecting via SFTP
  public function getMotdUseForSftp() {
    return @$this->attributes['motd_use_for_sftp'];
  }

  // double # Next billing amount
  public function getNextBillingAmount() {
    return @$this->attributes['next_billing_amount'];
  }

  // string # Next billing date
  public function getNextBillingDate() {
    return @$this->attributes['next_billing_date'];
  }

  // boolean # Allow users to use Office for the web?
  public function getOfficeIntegrationAvailable() {
    return @$this->attributes['office_integration_available'];
  }

  // string # Office integration application used to edit and view the MS Office documents
  public function getOfficeIntegrationType() {
    return @$this->attributes['office_integration_type'];
  }

  // string # Link to scheduling a meeting with our Sales team
  public function getOncehubLink() {
    return @$this->attributes['oncehub_link'];
  }

  // boolean # Use servers in the USA only?
  public function getOptOutGlobal() {
    return @$this->attributes['opt_out_global'];
  }

  // boolean # Is this site's billing overdue?
  public function getOverdue() {
    return @$this->attributes['overdue'];
  }

  // int64 # Shortest password length for users
  public function getPasswordMinLength() {
    return @$this->attributes['password_min_length'];
  }

  // boolean # Require a letter in passwords?
  public function getPasswordRequireLetter() {
    return @$this->attributes['password_require_letter'];
  }

  // boolean # Require lower and upper case letters in passwords?
  public function getPasswordRequireMixed() {
    return @$this->attributes['password_require_mixed'];
  }

  // boolean # Require a number in passwords?
  public function getPasswordRequireNumber() {
    return @$this->attributes['password_require_number'];
  }

  // boolean # Require special characters in password?
  public function getPasswordRequireSpecial() {
    return @$this->attributes['password_require_special'];
  }

  // boolean # Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
  public function getPasswordRequireUnbreached() {
    return @$this->attributes['password_require_unbreached'];
  }

  // boolean # Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
  public function getPasswordRequirementsApplyToBundles() {
    return @$this->attributes['password_requirements_apply_to_bundles'];
  }

  // int64 # Number of days password is valid
  public function getPasswordValidityDays() {
    return @$this->attributes['password_validity_days'];
  }

  // string # Site phone number
  public function getPhone() {
    return @$this->attributes['phone'];
  }

  // boolean # If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
  public function getPinAllRemoteServersToSiteRegion() {
    return @$this->attributes['pin_all_remote_servers_to_site_region'];
  }

  // boolean # If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
  public function getPreventRootPermissionsForNonSiteAdmins() {
    return @$this->attributes['prevent_root_permissions_for_non_site_admins'];
  }

  // boolean # If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
  public function getProtocolAccessGroupsOnly() {
    return @$this->attributes['protocol_access_groups_only'];
  }

  // boolean # Require two-factor authentication for all users?
  public function getRequire2fa() {
    return @$this->attributes['require_2fa'];
  }

  // date-time # If set, requirement for two-factor authentication has been scheduled to end on this date-time.
  public function getRequire2faStopTime() {
    return @$this->attributes['require_2fa_stop_time'];
  }

  // string # What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
  public function getRequire2faUserType() {
    return @$this->attributes['require_2fa_user_type'];
  }

  // boolean # If true, we will hide the 'Remember Me' box on Inbox and Bundle registration pages, requiring that the user logout and log back in every time they visit the page.
  public function getRequireLogoutFromBundlesAndInboxes() {
    return @$this->attributes['require_logout_from_bundles_and_inboxes'];
  }

  // Session # Current session
  public function getSession() {
    return @$this->attributes['session'];
  }

  // boolean # Are sessions locked to the same IP? (i.e. do users need to log in again if they change IPs?)
  public function getSessionPinnedByIp() {
    return @$this->attributes['session_pinned_by_ip'];
  }

  // boolean # Is SFTP enabled?
  public function getSftpEnabled() {
    return @$this->attributes['sftp_enabled'];
  }

  // string # Sftp Host Key Type
  public function getSftpHostKeyType() {
    return @$this->attributes['sftp_host_key_type'];
  }

  // int64 # Id of the currently selected custom SFTP Host Key
  public function getActiveSftpHostKeyId() {
    return @$this->attributes['active_sftp_host_key_id'];
  }

  // boolean # Are Insecure Ciphers allowed for SFTP?  Note:  Setting TLS Disabled -> True will always allow insecure ciphers for SFTP as well.  Enabling this is insecure.
  public function getSftpInsecureCiphers() {
    return @$this->attributes['sftp_insecure_ciphers'];
  }

  // boolean # Use user FTP roots also for SFTP?
  public function getSftpUserRootEnabled() {
    return @$this->attributes['sftp_user_root_enabled'];
  }

  // boolean # Allow bundle creation
  public function getSharingEnabled() {
    return @$this->attributes['sharing_enabled'];
  }

  // boolean # Show request access link for users without access?  Currently unused.
  public function getShowRequestAccessLink() {
    return @$this->attributes['show_request_access_link'];
  }

  // string # Custom site footer text
  public function getSiteFooter() {
    return @$this->attributes['site_footer'];
  }

  // string # Custom site header text
  public function getSiteHeader() {
    return @$this->attributes['site_header'];
  }

  // string # SMTP server hostname or IP
  public function getSmtpAddress() {
    return @$this->attributes['smtp_address'];
  }

  // string # SMTP server authentication type
  public function getSmtpAuthentication() {
    return @$this->attributes['smtp_authentication'];
  }

  // string # From address to use when mailing through custom SMTP
  public function getSmtpFrom() {
    return @$this->attributes['smtp_from'];
  }

  // int64 # SMTP server port
  public function getSmtpPort() {
    return @$this->attributes['smtp_port'];
  }

  // string # SMTP server username
  public function getSmtpUsername() {
    return @$this->attributes['smtp_username'];
  }

  // double # Session expiry in hours
  public function getSessionExpiry() {
    return @$this->attributes['session_expiry'];
  }

  // int64 # Session expiry in minutes
  public function getSessionExpiryMinutes() {
    return @$this->attributes['session_expiry_minutes'];
  }

  // boolean # Is SSL required?  Disabling this is insecure.
  public function getSslRequired() {
    return @$this->attributes['ssl_required'];
  }

  // string # Site subdomain
  public function getSubdomain() {
    return @$this->attributes['subdomain'];
  }

  // date-time # If switching plans, when does the new plan take effect?
  public function getSwitchToPlanDate() {
    return @$this->attributes['switch_to_plan_date'];
  }

  // boolean # Are Insecure TLS and SFTP Ciphers allowed?  Enabling this is insecure.
  public function getTlsDisabled() {
    return @$this->attributes['tls_disabled'];
  }

  // int64 # Number of days left in trial
  public function getTrialDaysLeft() {
    return @$this->attributes['trial_days_left'];
  }

  // date-time # When does this Site trial expire?
  public function getTrialUntil() {
    return @$this->attributes['trial_until'];
  }

  // boolean # Allow uploaders to set `provided_modified_at` for uploaded files?
  public function getUseProvidedModifiedAt() {
    return @$this->attributes['use_provided_modified_at'];
  }

  // User # User of current session
  public function getUser() {
    return @$this->attributes['user'];
  }

  // boolean # Will users be locked out after incorrect login attempts?
  public function getUserLockout() {
    return @$this->attributes['user_lockout'];
  }

  // int64 # How many hours to lock user out for failed password?
  public function getUserLockoutLockPeriod() {
    return @$this->attributes['user_lockout_lock_period'];
  }

  // int64 # Number of login tries within `user_lockout_within` hours before users are locked out
  public function getUserLockoutTries() {
    return @$this->attributes['user_lockout_tries'];
  }

  // int64 # Number of hours for user lockout window
  public function getUserLockoutWithin() {
    return @$this->attributes['user_lockout_within'];
  }

  // boolean # Enable User Requests feature
  public function getUserRequestsEnabled() {
    return @$this->attributes['user_requests_enabled'];
  }

  // boolean # Send email to site admins when a user request is received?
  public function getUserRequestsNotifyAdmins() {
    return @$this->attributes['user_requests_notify_admins'];
  }

  // string # Custom text send in user welcome email
  public function getWelcomeCustomText() {
    return @$this->attributes['welcome_custom_text'];
  }

  // email # Include this email in welcome emails if enabled
  public function getWelcomeEmailCc() {
    return @$this->attributes['welcome_email_cc'];
  }

  // string # Include this email subject in welcome emails if enabled
  public function getWelcomeEmailSubject() {
    return @$this->attributes['welcome_email_subject'];
  }

  // boolean # Will the welcome email be sent to new users?
  public function getWelcomeEmailEnabled() {
    return @$this->attributes['welcome_email_enabled'];
  }

  // string # Does the welcome screen appear?
  public function getWelcomeScreen() {
    return @$this->attributes['welcome_screen'];
  }

  // boolean # Does FTP user Windows emulation mode?
  public function getWindowsModeFtp() {
    return @$this->attributes['windows_mode_ftp'];
  }

  // int64 # If greater than zero, users will unable to login if they do not show activity within this number of days.
  public function getDisableUsersFromInactivityPeriodDays() {
    return @$this->attributes['disable_users_from_inactivity_period_days'];
  }

  // boolean # Allow group admins set password authentication method
  public function getGroupAdminsCanSetUserPassword() {
    return @$this->attributes['group_admins_can_set_user_password'];
  }

  public static function get($params = [], $options = []) {
    $response = Api::sendRequest('/site', 'GET', $options);

    return new Site((array)(@$response->data ?: []), $options);
  }


  public static function getUsage($params = [], $options = []) {
    $response = Api::sendRequest('/site/usage', 'GET', $options);

    return new UsageSnapshot((array)(@$response->data ?: []), $options);
  }


  // Parameters:
  //   name - string - Site name
  //   subdomain - string - Site subdomain
  //   domain - string - Custom domain
  //   domain_hsts_header - boolean - Send HSTS (HTTP Strict Transport Security) header when visitors access the site via a custom domain?
  //   domain_letsencrypt_chain - string - Letsencrypt chain to use when registering SSL Certificate for domain.
  //   email - string - Main email for this site
  //   reply_to_email - string - Reply-to email for this site
  //   allow_bundle_names - boolean - Are manual Bundle names allowed?
  //   bundle_expiration - int64 - Site-wide Bundle expiration in days
  //   welcome_email_enabled - boolean - Will the welcome email be sent to new users?
  //   ask_about_overwrites - boolean - If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
  //   show_request_access_link - boolean - Show request access link for users without access?  Currently unused.
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
  //   folder_permissions_groups_only - boolean - If true, permissions for this site must be bound to a group (not a user). Otherwise, permissions must be bound to a user.
  //   welcome_screen - string - Does the welcome screen appear?
  //   office_integration_available - boolean - Allow users to use Office for the web?
  //   office_integration_type - string - Office integration application used to edit and view the MS Office documents
  //   pin_all_remote_servers_to_site_region - boolean - If true, we will ensure that all internal communications with any remote server are made through the primary region of the site. This setting overrides individual remote server settings.
  //   motd_text - string - A message to show users when they connect via FTP or SFTP.
  //   motd_use_for_ftp - boolean - Show message to users connecting via FTP
  //   motd_use_for_sftp - boolean - Show message to users connecting via SFTP
  //   left_navigation_visibility - object - Visibility settings for account navigation
  //   session_expiry - double - Session expiry in hours
  //   ssl_required - boolean - Is SSL required?  Disabling this is insecure.
  //   tls_disabled - boolean - Are Insecure TLS and SFTP Ciphers allowed?  Enabling this is insecure.
  //   sftp_insecure_ciphers - boolean - Are Insecure Ciphers allowed for SFTP?  Note:  Setting TLS Disabled -> True will always allow insecure ciphers for SFTP as well.  Enabling this is insecure.
  //   disable_files_certificate_generation - boolean - If set, Files.com will not set the CAA records required to generate future SSL certificates for this domain.
  //   user_lockout - boolean - Will users be locked out after incorrect login attempts?
  //   user_lockout_tries - int64 - Number of login tries within `user_lockout_within` hours before users are locked out
  //   user_lockout_within - int64 - Number of hours for user lockout window
  //   user_lockout_lock_period - int64 - How many hours to lock user out for failed password?
  //   include_password_in_welcome_email - boolean - Include password in emails to new users?
  //   allowed_countries - string - Comma seperated list of allowed Country codes
  //   allowed_ips - string - List of allowed IP addresses
  //   disallowed_countries - string - Comma seperated list of disallowed Country codes
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
  //   session_pinned_by_ip - boolean - Are sessions locked to the same IP? (i.e. do users need to log in again if they change IPs?)
  //   bundle_not_found_message - string - Custom error message to show when bundle is not found.
  //   bundle_password_required - boolean - Do Bundles require password protection?
  //   bundle_require_registration - boolean - Do Bundles require registration?
  //   bundle_require_share_recipient - boolean - Do Bundles require recipients for sharing?
  //   bundle_registration_notifications - string - Do Bundle owners receive registration notification?
  //   bundle_activity_notifications - string - Do Bundle owners receive activity notifications?
  //   bundle_upload_receipt_notifications - string - Do Bundle uploaders receive upload confirmation notifications?
  //   password_requirements_apply_to_bundles - boolean - Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
  //   prevent_root_permissions_for_non_site_admins - boolean - If true, we will prevent non-administrators from receiving any permissions directly on the root folder.  This is commonly used to prevent the accidental application of permissions.
  //   opt_out_global - boolean - Use servers in the USA only?
  //   use_provided_modified_at - boolean - Allow uploaders to set `provided_modified_at` for uploaded files?
  //   custom_namespace - boolean - Is this site using a custom namespace for users?
  //   disable_users_from_inactivity_period_days - int64 - If greater than zero, users will unable to login if they do not show activity within this number of days.
  //   non_sso_groups_allowed - boolean - If true, groups can be manually created / modified / deleted by Site Admins. Otherwise, groups can only be managed via your SSO provider.
  //   non_sso_users_allowed - boolean - If true, users can be manually created / modified / deleted by Site Admins. Otherwise, users can only be managed via your SSO provider.
  //   sharing_enabled - boolean - Allow bundle creation
  //   user_requests_enabled - boolean - Enable User Requests feature
  //   user_requests_notify_admins - boolean - Send email to site admins when a user request is received?
  //   dav_enabled - boolean - Is WebDAV enabled?
  //   ftp_enabled - boolean - Is FTP enabled?
  //   sftp_enabled - boolean - Is SFTP enabled?
  //   sftp_host_key_type - string - Sftp Host Key Type
  //   active_sftp_host_key_id - int64 - Id of the currently selected custom SFTP Host Key
  //   protocol_access_groups_only - boolean - If true, protocol access permissions on users will be ignored, and only protocol access permissions set on Groups will be honored.  Make sure that your current user is a member of a group with API permission when changing this value to avoid locking yourself out of your site.
  //   bundle_watermark_value - object - Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
  //   group_admins_can_set_user_password - boolean - Allow group admins set password authentication method
  //   bundle_recipient_blacklist_free_email_domains - boolean - Disallow free email domains for Bundle/Inbox recipients?
  //   bundle_recipient_blacklist_domains - array(string) - List of email domains to disallow when entering a Bundle/Inbox recipients
  //   admins_bypass_locked_subfolders - boolean - Allow admins to bypass the locked subfolders setting.
  //   allowed_2fa_method_sms - boolean - Is SMS two factor authentication allowed?
  //   allowed_2fa_method_u2f - boolean - Is U2F two factor authentication allowed?
  //   allowed_2fa_method_totp - boolean - Is TOTP two factor authentication allowed?
  //   allowed_2fa_method_webauthn - boolean - Is WebAuthn two factor authentication allowed?
  //   allowed_2fa_method_yubi - boolean - Is yubikey two factor authentication allowed?
  //   allowed_2fa_method_email - boolean - Is OTP via email two factor authentication allowed?
  //   allowed_2fa_method_bypass_for_ftp_sftp_dav - boolean - Are users allowed to configure their two factor authentication to be bypassed for FTP/SFTP/WebDAV?
  //   require_2fa - boolean - Require two-factor authentication for all users?
  //   require_2fa_user_type - string - What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
  //   color2_top - string - Top bar background color
  //   color2_left - string - Page link and button color
  //   color2_link - string - Top bar link color
  //   color2_text - string - Page link and button color
  //   color2_top_text - string - Top bar text color
  //   site_header - string - Custom site header text
  //   site_footer - string - Custom site footer text
  //   login_help_text - string - Login help text
  //   smtp_address - string - SMTP server hostname or IP
  //   smtp_authentication - string - SMTP server authentication type
  //   smtp_from - string - From address to use when mailing through custom SMTP
  //   smtp_username - string - SMTP server username
  //   smtp_port - int64 - SMTP server port
  //   ldap_enabled - boolean - Main LDAP setting: is LDAP enabled?
  //   ldap_type - string - LDAP type
  //   ldap_host - string - LDAP host
  //   ldap_host_2 - string - LDAP backup host
  //   ldap_host_3 - string - LDAP backup host
  //   ldap_port - int64 - LDAP port
  //   ldap_secure - boolean - Use secure LDAP?
  //   ldap_username - string - Username for signing in to LDAP server.
  //   ldap_username_field - string - LDAP username field
  //   ldap_domain - string - Domain name that will be appended to usernames
  //   ldap_user_action - string - Should we sync users from LDAP server?
  //   ldap_group_action - string - Should we sync groups from LDAP server?
  //   ldap_user_include_groups - string - Comma or newline separated list of group names (with optional wildcards) - if provided, only users in these groups will be added or synced.
  //   ldap_group_exclusion - string - Comma or newline separated list of group names (with optional wildcards) to exclude when syncing.
  //   ldap_group_inclusion - string - Comma or newline separated list of group names (with optional wildcards) to include when syncing.
  //   ldap_base_dn - string - Base DN for looking up users in LDAP server
  //   uploads_via_email_authentication - boolean - Do incoming emails in the Inboxes require checking for SPF/DKIM/DMARC?
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
  //   smtp_password - string - Password for SMTP server.
  //   session_expiry_minutes - int64 - Session expiry in minutes
  public static function update($params = [], $options = []) {
    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['subdomain'] && !is_string(@$params['subdomain'])) {
      throw new \Files\InvalidParameterException('$subdomain must be of type string; received ' . gettype(@$params['subdomain']));
    }

    if (@$params['domain'] && !is_string(@$params['domain'])) {
      throw new \Files\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
    }

    if (@$params['domain_letsencrypt_chain'] && !is_string(@$params['domain_letsencrypt_chain'])) {
      throw new \Files\InvalidParameterException('$domain_letsencrypt_chain must be of type string; received ' . gettype(@$params['domain_letsencrypt_chain']));
    }

    if (@$params['email'] && !is_string(@$params['email'])) {
      throw new \Files\InvalidParameterException('$email must be of type string; received ' . gettype(@$params['email']));
    }

    if (@$params['reply_to_email'] && !is_string(@$params['reply_to_email'])) {
      throw new \Files\InvalidParameterException('$reply_to_email must be of type string; received ' . gettype(@$params['reply_to_email']));
    }

    if (@$params['bundle_expiration'] && !is_int(@$params['bundle_expiration'])) {
      throw new \Files\InvalidParameterException('$bundle_expiration must be of type int; received ' . gettype(@$params['bundle_expiration']));
    }

    if (@$params['welcome_email_cc'] && !is_string(@$params['welcome_email_cc'])) {
      throw new \Files\InvalidParameterException('$welcome_email_cc must be of type string; received ' . gettype(@$params['welcome_email_cc']));
    }

    if (@$params['welcome_email_subject'] && !is_string(@$params['welcome_email_subject'])) {
      throw new \Files\InvalidParameterException('$welcome_email_subject must be of type string; received ' . gettype(@$params['welcome_email_subject']));
    }

    if (@$params['welcome_custom_text'] && !is_string(@$params['welcome_custom_text'])) {
      throw new \Files\InvalidParameterException('$welcome_custom_text must be of type string; received ' . gettype(@$params['welcome_custom_text']));
    }

    if (@$params['language'] && !is_string(@$params['language'])) {
      throw new \Files\InvalidParameterException('$language must be of type string; received ' . gettype(@$params['language']));
    }

    if (@$params['default_time_zone'] && !is_string(@$params['default_time_zone'])) {
      throw new \Files\InvalidParameterException('$default_time_zone must be of type string; received ' . gettype(@$params['default_time_zone']));
    }

    if (@$params['desktop_app_session_lifetime'] && !is_int(@$params['desktop_app_session_lifetime'])) {
      throw new \Files\InvalidParameterException('$desktop_app_session_lifetime must be of type int; received ' . gettype(@$params['desktop_app_session_lifetime']));
    }

    if (@$params['mobile_app_session_lifetime'] && !is_int(@$params['mobile_app_session_lifetime'])) {
      throw new \Files\InvalidParameterException('$mobile_app_session_lifetime must be of type int; received ' . gettype(@$params['mobile_app_session_lifetime']));
    }

    if (@$params['welcome_screen'] && !is_string(@$params['welcome_screen'])) {
      throw new \Files\InvalidParameterException('$welcome_screen must be of type string; received ' . gettype(@$params['welcome_screen']));
    }

    if (@$params['office_integration_type'] && !is_string(@$params['office_integration_type'])) {
      throw new \Files\InvalidParameterException('$office_integration_type must be of type string; received ' . gettype(@$params['office_integration_type']));
    }

    if (@$params['motd_text'] && !is_string(@$params['motd_text'])) {
      throw new \Files\InvalidParameterException('$motd_text must be of type string; received ' . gettype(@$params['motd_text']));
    }

    if (@$params['user_lockout_tries'] && !is_int(@$params['user_lockout_tries'])) {
      throw new \Files\InvalidParameterException('$user_lockout_tries must be of type int; received ' . gettype(@$params['user_lockout_tries']));
    }

    if (@$params['user_lockout_within'] && !is_int(@$params['user_lockout_within'])) {
      throw new \Files\InvalidParameterException('$user_lockout_within must be of type int; received ' . gettype(@$params['user_lockout_within']));
    }

    if (@$params['user_lockout_lock_period'] && !is_int(@$params['user_lockout_lock_period'])) {
      throw new \Files\InvalidParameterException('$user_lockout_lock_period must be of type int; received ' . gettype(@$params['user_lockout_lock_period']));
    }

    if (@$params['allowed_countries'] && !is_string(@$params['allowed_countries'])) {
      throw new \Files\InvalidParameterException('$allowed_countries must be of type string; received ' . gettype(@$params['allowed_countries']));
    }

    if (@$params['allowed_ips'] && !is_string(@$params['allowed_ips'])) {
      throw new \Files\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
    }

    if (@$params['disallowed_countries'] && !is_string(@$params['disallowed_countries'])) {
      throw new \Files\InvalidParameterException('$disallowed_countries must be of type string; received ' . gettype(@$params['disallowed_countries']));
    }

    if (@$params['days_to_retain_backups'] && !is_int(@$params['days_to_retain_backups'])) {
      throw new \Files\InvalidParameterException('$days_to_retain_backups must be of type int; received ' . gettype(@$params['days_to_retain_backups']));
    }

    if (@$params['max_prior_passwords'] && !is_int(@$params['max_prior_passwords'])) {
      throw new \Files\InvalidParameterException('$max_prior_passwords must be of type int; received ' . gettype(@$params['max_prior_passwords']));
    }

    if (@$params['password_validity_days'] && !is_int(@$params['password_validity_days'])) {
      throw new \Files\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype(@$params['password_validity_days']));
    }

    if (@$params['password_min_length'] && !is_int(@$params['password_min_length'])) {
      throw new \Files\InvalidParameterException('$password_min_length must be of type int; received ' . gettype(@$params['password_min_length']));
    }

    if (@$params['bundle_not_found_message'] && !is_string(@$params['bundle_not_found_message'])) {
      throw new \Files\InvalidParameterException('$bundle_not_found_message must be of type string; received ' . gettype(@$params['bundle_not_found_message']));
    }

    if (@$params['bundle_registration_notifications'] && !is_string(@$params['bundle_registration_notifications'])) {
      throw new \Files\InvalidParameterException('$bundle_registration_notifications must be of type string; received ' . gettype(@$params['bundle_registration_notifications']));
    }

    if (@$params['bundle_activity_notifications'] && !is_string(@$params['bundle_activity_notifications'])) {
      throw new \Files\InvalidParameterException('$bundle_activity_notifications must be of type string; received ' . gettype(@$params['bundle_activity_notifications']));
    }

    if (@$params['bundle_upload_receipt_notifications'] && !is_string(@$params['bundle_upload_receipt_notifications'])) {
      throw new \Files\InvalidParameterException('$bundle_upload_receipt_notifications must be of type string; received ' . gettype(@$params['bundle_upload_receipt_notifications']));
    }

    if (@$params['disable_users_from_inactivity_period_days'] && !is_int(@$params['disable_users_from_inactivity_period_days'])) {
      throw new \Files\InvalidParameterException('$disable_users_from_inactivity_period_days must be of type int; received ' . gettype(@$params['disable_users_from_inactivity_period_days']));
    }

    if (@$params['sftp_host_key_type'] && !is_string(@$params['sftp_host_key_type'])) {
      throw new \Files\InvalidParameterException('$sftp_host_key_type must be of type string; received ' . gettype(@$params['sftp_host_key_type']));
    }

    if (@$params['active_sftp_host_key_id'] && !is_int(@$params['active_sftp_host_key_id'])) {
      throw new \Files\InvalidParameterException('$active_sftp_host_key_id must be of type int; received ' . gettype(@$params['active_sftp_host_key_id']));
    }

    if (@$params['bundle_recipient_blacklist_domains'] && !is_array(@$params['bundle_recipient_blacklist_domains'])) {
      throw new \Files\InvalidParameterException('$bundle_recipient_blacklist_domains must be of type array; received ' . gettype(@$params['bundle_recipient_blacklist_domains']));
    }

    if (@$params['require_2fa_user_type'] && !is_string(@$params['require_2fa_user_type'])) {
      throw new \Files\InvalidParameterException('$require_2fa_user_type must be of type string; received ' . gettype(@$params['require_2fa_user_type']));
    }

    if (@$params['color2_top'] && !is_string(@$params['color2_top'])) {
      throw new \Files\InvalidParameterException('$color2_top must be of type string; received ' . gettype(@$params['color2_top']));
    }

    if (@$params['color2_left'] && !is_string(@$params['color2_left'])) {
      throw new \Files\InvalidParameterException('$color2_left must be of type string; received ' . gettype(@$params['color2_left']));
    }

    if (@$params['color2_link'] && !is_string(@$params['color2_link'])) {
      throw new \Files\InvalidParameterException('$color2_link must be of type string; received ' . gettype(@$params['color2_link']));
    }

    if (@$params['color2_text'] && !is_string(@$params['color2_text'])) {
      throw new \Files\InvalidParameterException('$color2_text must be of type string; received ' . gettype(@$params['color2_text']));
    }

    if (@$params['color2_top_text'] && !is_string(@$params['color2_top_text'])) {
      throw new \Files\InvalidParameterException('$color2_top_text must be of type string; received ' . gettype(@$params['color2_top_text']));
    }

    if (@$params['site_header'] && !is_string(@$params['site_header'])) {
      throw new \Files\InvalidParameterException('$site_header must be of type string; received ' . gettype(@$params['site_header']));
    }

    if (@$params['site_footer'] && !is_string(@$params['site_footer'])) {
      throw new \Files\InvalidParameterException('$site_footer must be of type string; received ' . gettype(@$params['site_footer']));
    }

    if (@$params['login_help_text'] && !is_string(@$params['login_help_text'])) {
      throw new \Files\InvalidParameterException('$login_help_text must be of type string; received ' . gettype(@$params['login_help_text']));
    }

    if (@$params['smtp_address'] && !is_string(@$params['smtp_address'])) {
      throw new \Files\InvalidParameterException('$smtp_address must be of type string; received ' . gettype(@$params['smtp_address']));
    }

    if (@$params['smtp_authentication'] && !is_string(@$params['smtp_authentication'])) {
      throw new \Files\InvalidParameterException('$smtp_authentication must be of type string; received ' . gettype(@$params['smtp_authentication']));
    }

    if (@$params['smtp_from'] && !is_string(@$params['smtp_from'])) {
      throw new \Files\InvalidParameterException('$smtp_from must be of type string; received ' . gettype(@$params['smtp_from']));
    }

    if (@$params['smtp_username'] && !is_string(@$params['smtp_username'])) {
      throw new \Files\InvalidParameterException('$smtp_username must be of type string; received ' . gettype(@$params['smtp_username']));
    }

    if (@$params['smtp_port'] && !is_int(@$params['smtp_port'])) {
      throw new \Files\InvalidParameterException('$smtp_port must be of type int; received ' . gettype(@$params['smtp_port']));
    }

    if (@$params['ldap_type'] && !is_string(@$params['ldap_type'])) {
      throw new \Files\InvalidParameterException('$ldap_type must be of type string; received ' . gettype(@$params['ldap_type']));
    }

    if (@$params['ldap_host'] && !is_string(@$params['ldap_host'])) {
      throw new \Files\InvalidParameterException('$ldap_host must be of type string; received ' . gettype(@$params['ldap_host']));
    }

    if (@$params['ldap_host_2'] && !is_string(@$params['ldap_host_2'])) {
      throw new \Files\InvalidParameterException('$ldap_host_2 must be of type string; received ' . gettype(@$params['ldap_host_2']));
    }

    if (@$params['ldap_host_3'] && !is_string(@$params['ldap_host_3'])) {
      throw new \Files\InvalidParameterException('$ldap_host_3 must be of type string; received ' . gettype(@$params['ldap_host_3']));
    }

    if (@$params['ldap_port'] && !is_int(@$params['ldap_port'])) {
      throw new \Files\InvalidParameterException('$ldap_port must be of type int; received ' . gettype(@$params['ldap_port']));
    }

    if (@$params['ldap_username'] && !is_string(@$params['ldap_username'])) {
      throw new \Files\InvalidParameterException('$ldap_username must be of type string; received ' . gettype(@$params['ldap_username']));
    }

    if (@$params['ldap_username_field'] && !is_string(@$params['ldap_username_field'])) {
      throw new \Files\InvalidParameterException('$ldap_username_field must be of type string; received ' . gettype(@$params['ldap_username_field']));
    }

    if (@$params['ldap_domain'] && !is_string(@$params['ldap_domain'])) {
      throw new \Files\InvalidParameterException('$ldap_domain must be of type string; received ' . gettype(@$params['ldap_domain']));
    }

    if (@$params['ldap_user_action'] && !is_string(@$params['ldap_user_action'])) {
      throw new \Files\InvalidParameterException('$ldap_user_action must be of type string; received ' . gettype(@$params['ldap_user_action']));
    }

    if (@$params['ldap_group_action'] && !is_string(@$params['ldap_group_action'])) {
      throw new \Files\InvalidParameterException('$ldap_group_action must be of type string; received ' . gettype(@$params['ldap_group_action']));
    }

    if (@$params['ldap_user_include_groups'] && !is_string(@$params['ldap_user_include_groups'])) {
      throw new \Files\InvalidParameterException('$ldap_user_include_groups must be of type string; received ' . gettype(@$params['ldap_user_include_groups']));
    }

    if (@$params['ldap_group_exclusion'] && !is_string(@$params['ldap_group_exclusion'])) {
      throw new \Files\InvalidParameterException('$ldap_group_exclusion must be of type string; received ' . gettype(@$params['ldap_group_exclusion']));
    }

    if (@$params['ldap_group_inclusion'] && !is_string(@$params['ldap_group_inclusion'])) {
      throw new \Files\InvalidParameterException('$ldap_group_inclusion must be of type string; received ' . gettype(@$params['ldap_group_inclusion']));
    }

    if (@$params['ldap_base_dn'] && !is_string(@$params['ldap_base_dn'])) {
      throw new \Files\InvalidParameterException('$ldap_base_dn must be of type string; received ' . gettype(@$params['ldap_base_dn']));
    }

    if (@$params['ldap_password_change'] && !is_string(@$params['ldap_password_change'])) {
      throw new \Files\InvalidParameterException('$ldap_password_change must be of type string; received ' . gettype(@$params['ldap_password_change']));
    }

    if (@$params['ldap_password_change_confirmation'] && !is_string(@$params['ldap_password_change_confirmation'])) {
      throw new \Files\InvalidParameterException('$ldap_password_change_confirmation must be of type string; received ' . gettype(@$params['ldap_password_change_confirmation']));
    }

    if (@$params['smtp_password'] && !is_string(@$params['smtp_password'])) {
      throw new \Files\InvalidParameterException('$smtp_password must be of type string; received ' . gettype(@$params['smtp_password']));
    }

    if (@$params['session_expiry_minutes'] && !is_int(@$params['session_expiry_minutes'])) {
      throw new \Files\InvalidParameterException('$session_expiry_minutes must be of type int; received ' . gettype(@$params['session_expiry_minutes']));
    }

    $response = Api::sendRequest('/site', 'PATCH', $params, $options);

    return new Site((array)(@$response->data ?: []), $options);
  }

}
