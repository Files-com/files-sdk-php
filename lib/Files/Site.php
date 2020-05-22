<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Site
 *
 * @package Files
 */
class Site {
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

  // string # Site name
  public function getName() {
    return $this->attributes['name'];
  }

  // boolean # Is SMS two factor authentication allowed?
  public function getAllowed2faMethodSms() {
    return $this->attributes['allowed_2fa_method_sms'];
  }

  // boolean # Is TOTP two factor authentication allowed?
  public function getAllowed2faMethodTotp() {
    return $this->attributes['allowed_2fa_method_totp'];
  }

  // boolean # Is U2F two factor authentication allowed?
  public function getAllowed2faMethodU2f() {
    return $this->attributes['allowed_2fa_method_u2f'];
  }

  // boolean # Is yubikey two factor authentication allowed?
  public function getAllowed2faMethodYubi() {
    return $this->attributes['allowed_2fa_method_yubi'];
  }

  // int64 # User ID for the main site administrator
  public function getAdminUserId() {
    return $this->attributes['admin_user_id'];
  }

  // boolean # Are manual Bundle names allowed?
  public function getAllowBundleNames() {
    return $this->attributes['allow_bundle_names'];
  }

  // string # List of allowed IP addresses
  public function getAllowedIps() {
    return $this->attributes['allowed_ips'];
  }

  // boolean # If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
  public function getAskAboutOverwrites() {
    return $this->attributes['ask_about_overwrites'];
  }

  // int64 # Site-wide Bundle expiration in days
  public function getBundleExpiration() {
    return $this->attributes['bundle_expiration'];
  }

  // boolean # Do Bundles require password protection?
  public function getBundlePasswordRequired() {
    return $this->attributes['bundle_password_required'];
  }

  // string # Page link and button color
  public function getColor2Left() {
    return $this->attributes['color2_left'];
  }

  // string # Top bar link color
  public function getColor2Link() {
    return $this->attributes['color2_link'];
  }

  // string # Page link and button color
  public function getColor2Text() {
    return $this->attributes['color2_text'];
  }

  // string # Top bar background color
  public function getColor2Top() {
    return $this->attributes['color2_top'];
  }

  // string # Top bar text color
  public function getColor2TopText() {
    return $this->attributes['color2_top_text'];
  }

  // date-time # Time this site was created
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // string # Preferred currency
  public function getCurrency() {
    return $this->attributes['currency'];
  }

  // boolean # Is this site using a custom namespace for users?
  public function getCustomNamespace() {
    return $this->attributes['custom_namespace'];
  }

  // int64 # Number of days to keep deleted files
  public function getDaysToRetainBackups() {
    return $this->attributes['days_to_retain_backups'];
  }

  // string # Site default time zone
  public function getDefaultTimeZone() {
    return $this->attributes['default_time_zone'];
  }

  // boolean # Is the desktop app enabled?
  public function getDesktopApp() {
    return $this->attributes['desktop_app'];
  }

  // boolean # Is desktop app session IP pinning enabled?
  public function getDesktopAppSessionIpPinning() {
    return $this->attributes['desktop_app_session_ip_pinning'];
  }

  // int64 # Desktop app session lifetime (in hours)
  public function getDesktopAppSessionLifetime() {
    return $this->attributes['desktop_app_session_lifetime'];
  }

  // boolean # Are notifications disabled?
  public function getDisableNotifications() {
    return $this->attributes['disable_notifications'];
  }

  // boolean # Is password reset disabled?
  public function getDisablePasswordReset() {
    return $this->attributes['disable_password_reset'];
  }

  // string # Custom domain
  public function getDomain() {
    return $this->attributes['domain'];
  }

  // email # Main email for this site
  public function getEmail() {
    return $this->attributes['email'];
  }

  // boolean # If true, permissions for this site must be bound to a group (not a user). Otherwise, permissions must be bound to a user.
  public function getFolderPermissionsGroupsOnly() {
    return $this->attributes['folder_permissions_groups_only'];
  }

  // boolean # Is there a signed HIPAA BAA between Files.com and this site?
  public function getHipaa() {
    return $this->attributes['hipaa'];
  }

  // Branded icon 128x128
  public function getIcon128() {
    return $this->attributes['icon128'];
  }

  // Branded icon 16x16
  public function getIcon16() {
    return $this->attributes['icon16'];
  }

  // Branded icon 32x32
  public function getIcon32() {
    return $this->attributes['icon32'];
  }

  // Branded icon 48x48
  public function getIcon48() {
    return $this->attributes['icon48'];
  }

  // date-time # Can files be modified?
  public function getImmutableFilesSetAt() {
    return $this->attributes['immutable_files_set_at'];
  }

  // boolean # Include password in emails to new users?
  public function getIncludePasswordInWelcomeEmail() {
    return $this->attributes['include_password_in_welcome_email'];
  }

  // string # Site default language
  public function getLanguage() {
    return $this->attributes['language'];
  }

  // string # Base DN for looking up users in LDAP server
  public function getLdapBaseDn() {
    return $this->attributes['ldap_base_dn'];
  }

  // string # Domain name that will be appended to usernames
  public function getLdapDomain() {
    return $this->attributes['ldap_domain'];
  }

  // boolean # Main LDAP setting: is LDAP enabled?
  public function getLdapEnabled() {
    return $this->attributes['ldap_enabled'];
  }

  // string # Should we sync groups from LDAP server?
  public function getLdapGroupAction() {
    return $this->attributes['ldap_group_action'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to exclude when syncing.
  public function getLdapGroupExclusion() {
    return $this->attributes['ldap_group_exclusion'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) to include when syncing.
  public function getLdapGroupInclusion() {
    return $this->attributes['ldap_group_inclusion'];
  }

  // string # LDAP host
  public function getLdapHost() {
    return $this->attributes['ldap_host'];
  }

  // string # LDAP backup host
  public function getLdapHost2() {
    return $this->attributes['ldap_host_2'];
  }

  // string # LDAP backup host
  public function getLdapHost3() {
    return $this->attributes['ldap_host_3'];
  }

  // int64 # LDAP port
  public function getLdapPort() {
    return $this->attributes['ldap_port'];
  }

  // boolean # Use secure LDAP?
  public function getLdapSecure() {
    return $this->attributes['ldap_secure'];
  }

  // string # LDAP type
  public function getLdapType() {
    return $this->attributes['ldap_type'];
  }

  // string # Should we sync users from LDAP server?
  public function getLdapUserAction() {
    return $this->attributes['ldap_user_action'];
  }

  // string # Comma or newline separated list of group names (with optional wildcards) - if provided, only users in these groups will be added or synced.
  public function getLdapUserIncludeGroups() {
    return $this->attributes['ldap_user_include_groups'];
  }

  // string # Username for signing in to LDAP server.
  public function getLdapUsername() {
    return $this->attributes['ldap_username'];
  }

  // string # LDAP username field
  public function getLdapUsernameField() {
    return $this->attributes['ldap_username_field'];
  }

  // string # Login help text
  public function getLoginHelpText() {
    return $this->attributes['login_help_text'];
  }

  // Branded logo
  public function getLogo() {
    return $this->attributes['logo'];
  }

  // int64 # Number of prior passwords to disallow
  public function getMaxPriorPasswords() {
    return $this->attributes['max_prior_passwords'];
  }

  // double # Next billing amount
  public function getNextBillingAmount() {
    return $this->attributes['next_billing_amount'];
  }

  // string # Next billing date
  public function getNextBillingDate() {
    return $this->attributes['next_billing_date'];
  }

  // boolean # Use servers in the USA only?
  public function getOptOutGlobal() {
    return $this->attributes['opt_out_global'];
  }

  // date-time # Last time the site was notified about an overage
  public function getOverageNotifiedAt() {
    return $this->attributes['overage_notified_at'];
  }

  // boolean # Notify site email of overages?
  public function getOverageNotify() {
    return $this->attributes['overage_notify'];
  }

  // boolean # Is this site's billing overdue?
  public function getOverdue() {
    return $this->attributes['overdue'];
  }

  // int64 # Shortest password length for users
  public function getPasswordMinLength() {
    return $this->attributes['password_min_length'];
  }

  // boolean # Require a letter in passwords?
  public function getPasswordRequireLetter() {
    return $this->attributes['password_require_letter'];
  }

  // boolean # Require lower and upper case letters in passwords?
  public function getPasswordRequireMixed() {
    return $this->attributes['password_require_mixed'];
  }

  // boolean # Require a number in passwords?
  public function getPasswordRequireNumber() {
    return $this->attributes['password_require_number'];
  }

  // boolean # Require special characters in password?
  public function getPasswordRequireSpecial() {
    return $this->attributes['password_require_special'];
  }

  // boolean # Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
  public function getPasswordRequireUnbreached() {
    return $this->attributes['password_require_unbreached'];
  }

  // boolean # Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
  public function getPasswordRequirementsApplyToBundles() {
    return $this->attributes['password_requirements_apply_to_bundles'];
  }

  // int64 # Number of days password is valid
  public function getPasswordValidityDays() {
    return $this->attributes['password_validity_days'];
  }

  // string # Site phone number
  public function getPhone() {
    return $this->attributes['phone'];
  }

  // boolean # Require two-factor authentication for all users?
  public function getRequire2fa() {
    return $this->attributes['require_2fa'];
  }

  // date-time # If set, requirement for two-factor authentication has been scheduled to end on this date-time.
  public function getRequire2faStopTime() {
    return $this->attributes['require_2fa_stop_time'];
  }

  // string # What type of user is required to use two-factor authentication (when require_2fa is set to `true` for this site)?
  public function getRequire2faUserType() {
    return $this->attributes['require_2fa_user_type'];
  }

  // Current session
  public function getSession() {
    return $this->attributes['session'];
  }

  // boolean # Are sessions locked to the same IP? (i.e. do users need to log in again if they change IPs?)
  public function getSessionPinnedByIp() {
    return $this->attributes['session_pinned_by_ip'];
  }

  // boolean # Use user FTP roots also for SFTP?
  public function getSftpUserRootEnabled() {
    return $this->attributes['sftp_user_root_enabled'];
  }

  // boolean # Show request access link for users without access?  Currently unused.
  public function getShowRequestAccessLink() {
    return $this->attributes['show_request_access_link'];
  }

  // string # Custom site footer text
  public function getSiteFooter() {
    return $this->attributes['site_footer'];
  }

  // string # Custom site header text
  public function getSiteHeader() {
    return $this->attributes['site_header'];
  }

  // string # SMTP server hostname or IP
  public function getSmtpAddress() {
    return $this->attributes['smtp_address'];
  }

  // string # SMTP server authentication type
  public function getSmtpAuthentication() {
    return $this->attributes['smtp_authentication'];
  }

  // string # From address to use when mailing through custom SMTP
  public function getSmtpFrom() {
    return $this->attributes['smtp_from'];
  }

  // int64 # SMTP server port
  public function getSmtpPort() {
    return $this->attributes['smtp_port'];
  }

  // string # SMTP server username
  public function getSmtpUsername() {
    return $this->attributes['smtp_username'];
  }

  // double # Session expiry in hours
  public function getSessionExpiry() {
    return $this->attributes['session_expiry'];
  }

  // boolean # Is SSL required?  Disabling this is insecure.
  public function getSslRequired() {
    return $this->attributes['ssl_required'];
  }

  // string # Site subdomain
  public function getSubdomain() {
    return $this->attributes['subdomain'];
  }

  // date-time # If switching plans, when does the new plan take effect?
  public function getSwitchToPlanDate() {
    return $this->attributes['switch_to_plan_date'];
  }

  // boolean # Is TLS disabled(site setting)?
  public function getTlsDisabled() {
    return $this->attributes['tls_disabled'];
  }

  // int64 # Number of days left in trial
  public function getTrialDaysLeft() {
    return $this->attributes['trial_days_left'];
  }

  // date-time # When does this Site trial expire?
  public function getTrialUntil() {
    return $this->attributes['trial_until'];
  }

  // date-time # Last time this Site was updated
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }

  // boolean # Allow uploaders to set `provided_modified_at` for uploaded files?
  public function getUseProvidedModifiedAt() {
    return $this->attributes['use_provided_modified_at'];
  }

  // User of current session
  public function getUser() {
    return $this->attributes['user'];
  }

  // boolean # Will users be locked out after incorrect login attempts?
  public function getUserLockout() {
    return $this->attributes['user_lockout'];
  }

  // int64 # How many hours to lock user out for failed password?
  public function getUserLockoutLockPeriod() {
    return $this->attributes['user_lockout_lock_period'];
  }

  // int64 # Number of login tries within `user_lockout_within` hours before users are locked out
  public function getUserLockoutTries() {
    return $this->attributes['user_lockout_tries'];
  }

  // int64 # Number of hours for user lockout window
  public function getUserLockoutWithin() {
    return $this->attributes['user_lockout_within'];
  }

  // string # Custom text send in user welcome email
  public function getWelcomeCustomText() {
    return $this->attributes['welcome_custom_text'];
  }

  // email # Include this email in welcome emails if enabled
  public function getWelcomeEmailCc() {
    return $this->attributes['welcome_email_cc'];
  }

  // boolean # Will the welcome email be sent to new users?
  public function getWelcomeEmailEnabled() {
    return $this->attributes['welcome_email_enabled'];
  }

  // string # Does the welcome screen appear?
  public function getWelcomeScreen() {
    return $this->attributes['welcome_screen'];
  }

  // boolean # Does FTP user Windows emulation mode?
  public function getWindowsModeFtp() {
    return $this->attributes['windows_mode_ftp'];
  }

  // int64 # If greater than zero, users will unable to login if they do not show activity within this number of days.
  public function getDisableUsersFromInactivityPeriodDays() {
    return $this->attributes['disable_users_from_inactivity_period_days'];
  }

  public static function get($params = [], $options = []) {
    $response = Api::sendRequest('/site', 'GET');

    return new Site((array)$response->data, $options);
  }

  public static function getUsage($params = [], $options = []) {
    $response = Api::sendRequest('/site/usage', 'GET');

    return new UsageSnapshot((array)$response->data, $options);
  }

  // Parameters:
  //   name - string - Site name
  //   subdomain - string - Site subdomain
  //   domain - string - Custom domain
  //   email - string - Main email for this site
  //   allow_bundle_names - boolean - Are manual Bundle names allowed?
  //   bundle_expiration - integer - Site-wide Bundle expiration in days
  //   overage_notify - boolean - Notify site email of overages?
  //   welcome_email_enabled - boolean - Will the welcome email be sent to new users?
  //   ask_about_overwrites - boolean - If false, rename conflicting files instead of asking for overwrite confirmation.  Only applies to web interface.
  //   show_request_access_link - boolean - Show request access link for users without access?  Currently unused.
  //   welcome_email_cc - string - Include this email in welcome emails if enabled
  //   welcome_custom_text - string - Custom text send in user welcome email
  //   language - string - Site default language
  //   windows_mode_ftp - boolean - Does FTP user Windows emulation mode?
  //   default_time_zone - string - Site default time zone
  //   desktop_app - boolean - Is the desktop app enabled?
  //   desktop_app_session_ip_pinning - boolean - Is desktop app session IP pinning enabled?
  //   desktop_app_session_lifetime - integer - Desktop app session lifetime (in hours)
  //   folder_permissions_groups_only - boolean - If true, permissions for this site must be bound to a group (not a user). Otherwise, permissions must be bound to a user.
  //   welcome_screen - string - Does the welcome screen appear?
  //   session_expiry - number - Session expiry in hours
  //   ssl_required - boolean - Is SSL required?  Disabling this is insecure.
  //   tls_disabled - boolean - Is TLS disabled(site setting)?
  //   user_lockout - boolean - Will users be locked out after incorrect login attempts?
  //   user_lockout_tries - integer - Number of login tries within `user_lockout_within` hours before users are locked out
  //   user_lockout_within - integer - Number of hours for user lockout window
  //   user_lockout_lock_period - integer - How many hours to lock user out for failed password?
  //   include_password_in_welcome_email - boolean - Include password in emails to new users?
  //   allowed_ips - string - List of allowed IP addresses
  //   days_to_retain_backups - integer - Number of days to keep deleted files
  //   max_prior_passwords - integer - Number of prior passwords to disallow
  //   password_validity_days - integer - Number of days password is valid
  //   password_min_length - integer - Shortest password length for users
  //   password_require_letter - boolean - Require a letter in passwords?
  //   password_require_mixed - boolean - Require lower and upper case letters in passwords?
  //   password_require_special - boolean - Require special characters in password?
  //   password_require_number - boolean - Require a number in passwords?
  //   password_require_unbreached - boolean - Require passwords that have not been previously breached? (see https://haveibeenpwned.com/)
  //   sftp_user_root_enabled - boolean - Use user FTP roots also for SFTP?
  //   disable_password_reset - boolean - Is password reset disabled?
  //   immutable_files - boolean - Are files protected from modification?
  //   session_pinned_by_ip - boolean - Are sessions locked to the same IP? (i.e. do users need to log in again if they change IPs?)
  //   bundle_password_required - boolean - Do Bundles require password protection?
  //   password_requirements_apply_to_bundles - boolean - Require bundles' passwords, and passwords for other items (inboxes, public shares, etc.) to conform to the same requirements as users' passwords?
  //   opt_out_global - boolean - Use servers in the USA only?
  //   use_provided_modified_at - boolean - Allow uploaders to set `provided_modified_at` for uploaded files?
  //   custom_namespace - boolean - Is this site using a custom namespace for users?
  //   disable_users_from_inactivity_period_days - integer - If greater than zero, users will unable to login if they do not show activity within this number of days.
  //   allowed_2fa_method_sms - boolean - Is SMS two factor authentication allowed?
  //   allowed_2fa_method_u2f - boolean - Is U2F two factor authentication allowed?
  //   allowed_2fa_method_totp - boolean - Is TOTP two factor authentication allowed?
  //   allowed_2fa_method_yubi - boolean - Is yubikey two factor authentication allowed?
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
  //   smtp_port - integer - SMTP server port
  //   ldap_enabled - boolean - Main LDAP setting: is LDAP enabled?
  //   ldap_type - string - LDAP type
  //   ldap_host - string - LDAP host
  //   ldap_host_2 - string - LDAP backup host
  //   ldap_host_3 - string - LDAP backup host
  //   ldap_port - integer - LDAP port
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
  //   disable_2fa_with_delay - boolean - If set to true, we will begin the process of disabling 2FA on this site.
  //   ldap_password_change - string - New LDAP password.
  //   ldap_password_change_confirmation - string - Confirm new LDAP password.
  //   smtp_password - string - Password for SMTP server.
  public static function update($params = [], $options = []) {
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }

    if ($params['subdomain'] && !is_string($params['subdomain'])) {
      throw new \InvalidArgumentException('Bad parameter: $subdomain must be of type string; received ' . gettype($subdomain));
    }

    if ($params['domain'] && !is_string($params['domain'])) {
      throw new \InvalidArgumentException('Bad parameter: $domain must be of type string; received ' . gettype($domain));
    }

    if ($params['email'] && !is_string($params['email'])) {
      throw new \InvalidArgumentException('Bad parameter: $email must be of type string; received ' . gettype($email));
    }

    if ($params['bundle_expiration'] && !is_int($params['bundle_expiration'])) {
      throw new \InvalidArgumentException('Bad parameter: $bundle_expiration must be of type int; received ' . gettype($bundle_expiration));
    }

    if ($params['welcome_email_cc'] && !is_string($params['welcome_email_cc'])) {
      throw new \InvalidArgumentException('Bad parameter: $welcome_email_cc must be of type string; received ' . gettype($welcome_email_cc));
    }

    if ($params['welcome_custom_text'] && !is_string($params['welcome_custom_text'])) {
      throw new \InvalidArgumentException('Bad parameter: $welcome_custom_text must be of type string; received ' . gettype($welcome_custom_text));
    }

    if ($params['language'] && !is_string($params['language'])) {
      throw new \InvalidArgumentException('Bad parameter: $language must be of type string; received ' . gettype($language));
    }

    if ($params['default_time_zone'] && !is_string($params['default_time_zone'])) {
      throw new \InvalidArgumentException('Bad parameter: $default_time_zone must be of type string; received ' . gettype($default_time_zone));
    }

    if ($params['desktop_app_session_lifetime'] && !is_int($params['desktop_app_session_lifetime'])) {
      throw new \InvalidArgumentException('Bad parameter: $desktop_app_session_lifetime must be of type int; received ' . gettype($desktop_app_session_lifetime));
    }

    if ($params['welcome_screen'] && !is_string($params['welcome_screen'])) {
      throw new \InvalidArgumentException('Bad parameter: $welcome_screen must be of type string; received ' . gettype($welcome_screen));
    }

    if ($params['user_lockout_tries'] && !is_int($params['user_lockout_tries'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_lockout_tries must be of type int; received ' . gettype($user_lockout_tries));
    }

    if ($params['user_lockout_within'] && !is_int($params['user_lockout_within'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_lockout_within must be of type int; received ' . gettype($user_lockout_within));
    }

    if ($params['user_lockout_lock_period'] && !is_int($params['user_lockout_lock_period'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_lockout_lock_period must be of type int; received ' . gettype($user_lockout_lock_period));
    }

    if ($params['allowed_ips'] && !is_string($params['allowed_ips'])) {
      throw new \InvalidArgumentException('Bad parameter: $allowed_ips must be of type string; received ' . gettype($allowed_ips));
    }

    if ($params['days_to_retain_backups'] && !is_int($params['days_to_retain_backups'])) {
      throw new \InvalidArgumentException('Bad parameter: $days_to_retain_backups must be of type int; received ' . gettype($days_to_retain_backups));
    }

    if ($params['max_prior_passwords'] && !is_int($params['max_prior_passwords'])) {
      throw new \InvalidArgumentException('Bad parameter: $max_prior_passwords must be of type int; received ' . gettype($max_prior_passwords));
    }

    if ($params['password_validity_days'] && !is_int($params['password_validity_days'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_validity_days must be of type int; received ' . gettype($password_validity_days));
    }

    if ($params['password_min_length'] && !is_int($params['password_min_length'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_min_length must be of type int; received ' . gettype($password_min_length));
    }

    if ($params['disable_users_from_inactivity_period_days'] && !is_int($params['disable_users_from_inactivity_period_days'])) {
      throw new \InvalidArgumentException('Bad parameter: $disable_users_from_inactivity_period_days must be of type int; received ' . gettype($disable_users_from_inactivity_period_days));
    }

    if ($params['require_2fa_user_type'] && !is_string($params['require_2fa_user_type'])) {
      throw new \InvalidArgumentException('Bad parameter: $require_2fa_user_type must be of type string; received ' . gettype($require_2fa_user_type));
    }

    if ($params['color2_top'] && !is_string($params['color2_top'])) {
      throw new \InvalidArgumentException('Bad parameter: $color2_top must be of type string; received ' . gettype($color2_top));
    }

    if ($params['color2_left'] && !is_string($params['color2_left'])) {
      throw new \InvalidArgumentException('Bad parameter: $color2_left must be of type string; received ' . gettype($color2_left));
    }

    if ($params['color2_link'] && !is_string($params['color2_link'])) {
      throw new \InvalidArgumentException('Bad parameter: $color2_link must be of type string; received ' . gettype($color2_link));
    }

    if ($params['color2_text'] && !is_string($params['color2_text'])) {
      throw new \InvalidArgumentException('Bad parameter: $color2_text must be of type string; received ' . gettype($color2_text));
    }

    if ($params['color2_top_text'] && !is_string($params['color2_top_text'])) {
      throw new \InvalidArgumentException('Bad parameter: $color2_top_text must be of type string; received ' . gettype($color2_top_text));
    }

    if ($params['site_header'] && !is_string($params['site_header'])) {
      throw new \InvalidArgumentException('Bad parameter: $site_header must be of type string; received ' . gettype($site_header));
    }

    if ($params['site_footer'] && !is_string($params['site_footer'])) {
      throw new \InvalidArgumentException('Bad parameter: $site_footer must be of type string; received ' . gettype($site_footer));
    }

    if ($params['login_help_text'] && !is_string($params['login_help_text'])) {
      throw new \InvalidArgumentException('Bad parameter: $login_help_text must be of type string; received ' . gettype($login_help_text));
    }

    if ($params['smtp_address'] && !is_string($params['smtp_address'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_address must be of type string; received ' . gettype($smtp_address));
    }

    if ($params['smtp_authentication'] && !is_string($params['smtp_authentication'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_authentication must be of type string; received ' . gettype($smtp_authentication));
    }

    if ($params['smtp_from'] && !is_string($params['smtp_from'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_from must be of type string; received ' . gettype($smtp_from));
    }

    if ($params['smtp_username'] && !is_string($params['smtp_username'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_username must be of type string; received ' . gettype($smtp_username));
    }

    if ($params['smtp_port'] && !is_int($params['smtp_port'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_port must be of type int; received ' . gettype($smtp_port));
    }

    if ($params['ldap_type'] && !is_string($params['ldap_type'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_type must be of type string; received ' . gettype($ldap_type));
    }

    if ($params['ldap_host'] && !is_string($params['ldap_host'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_host must be of type string; received ' . gettype($ldap_host));
    }

    if ($params['ldap_host_2'] && !is_string($params['ldap_host_2'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_host_2 must be of type string; received ' . gettype($ldap_host_2));
    }

    if ($params['ldap_host_3'] && !is_string($params['ldap_host_3'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_host_3 must be of type string; received ' . gettype($ldap_host_3));
    }

    if ($params['ldap_port'] && !is_int($params['ldap_port'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_port must be of type int; received ' . gettype($ldap_port));
    }

    if ($params['ldap_username'] && !is_string($params['ldap_username'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_username must be of type string; received ' . gettype($ldap_username));
    }

    if ($params['ldap_username_field'] && !is_string($params['ldap_username_field'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_username_field must be of type string; received ' . gettype($ldap_username_field));
    }

    if ($params['ldap_domain'] && !is_string($params['ldap_domain'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_domain must be of type string; received ' . gettype($ldap_domain));
    }

    if ($params['ldap_user_action'] && !is_string($params['ldap_user_action'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_user_action must be of type string; received ' . gettype($ldap_user_action));
    }

    if ($params['ldap_group_action'] && !is_string($params['ldap_group_action'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_group_action must be of type string; received ' . gettype($ldap_group_action));
    }

    if ($params['ldap_user_include_groups'] && !is_string($params['ldap_user_include_groups'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_user_include_groups must be of type string; received ' . gettype($ldap_user_include_groups));
    }

    if ($params['ldap_group_exclusion'] && !is_string($params['ldap_group_exclusion'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_group_exclusion must be of type string; received ' . gettype($ldap_group_exclusion));
    }

    if ($params['ldap_group_inclusion'] && !is_string($params['ldap_group_inclusion'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_group_inclusion must be of type string; received ' . gettype($ldap_group_inclusion));
    }

    if ($params['ldap_base_dn'] && !is_string($params['ldap_base_dn'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_base_dn must be of type string; received ' . gettype($ldap_base_dn));
    }

    if ($params['ldap_password_change'] && !is_string($params['ldap_password_change'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_password_change must be of type string; received ' . gettype($ldap_password_change));
    }

    if ($params['ldap_password_change_confirmation'] && !is_string($params['ldap_password_change_confirmation'])) {
      throw new \InvalidArgumentException('Bad parameter: $ldap_password_change_confirmation must be of type string; received ' . gettype($ldap_password_change_confirmation));
    }

    if ($params['smtp_password'] && !is_string($params['smtp_password'])) {
      throw new \InvalidArgumentException('Bad parameter: $smtp_password must be of type string; received ' . gettype($smtp_password));
    }

    $response = Api::sendRequest('/site', 'PATCH', $params);

    return new Site((array)$response->data, $options);
  }
}
