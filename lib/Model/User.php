<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class User
 *
 * @package Files
 */
class User {
  private $attributes = [];
  private $options = [];

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

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # User ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # User's username
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // array # List of group IDs of which this user is an administrator
  public function getAdminGroupIds() {
    return @$this->attributes['admin_group_ids'];
  }

  public function setAdminGroupIds($value) {
    return $this->attributes['admin_group_ids'] = $value;
  }

  // string # A list of allowed IPs if applicable.  Newline delimited
  public function getAllowedIps() {
    return @$this->attributes['allowed_ips'];
  }

  public function setAllowedIps($value) {
    return $this->attributes['allowed_ips'] = $value;
  }

  // boolean # DEPRECATED: Can the user create Bundles (aka Share Links)? Use the bundle permission instead.
  public function getAttachmentsPermission() {
    return @$this->attributes['attachments_permission'];
  }

  public function setAttachmentsPermission($value) {
    return $this->attributes['attachments_permission'] = $value;
  }

  // int64 # Number of api keys associated with this user
  public function getApiKeysCount() {
    return @$this->attributes['api_keys_count'];
  }

  public function setApiKeysCount($value) {
    return $this->attributes['api_keys_count'] = $value;
  }

  // date-time # Scheduled Date/Time at which user will be deactivated
  public function getAuthenticateUntil() {
    return @$this->attributes['authenticate_until'];
  }

  public function setAuthenticateUntil($value) {
    return $this->attributes['authenticate_until'] = $value;
  }

  // string # How is this user authenticated?
  public function getAuthenticationMethod() {
    return @$this->attributes['authentication_method'];
  }

  public function setAuthenticationMethod($value) {
    return $this->attributes['authentication_method'] = $value;
  }

  // string # URL holding the user's avatar
  public function getAvatarUrl() {
    return @$this->attributes['avatar_url'];
  }

  public function setAvatarUrl($value) {
    return $this->attributes['avatar_url'] = $value;
  }

  // boolean # Allow this user to perform operations on the account, payments, and invoices?
  public function getBillingPermission() {
    return @$this->attributes['billing_permission'];
  }

  public function setBillingPermission($value) {
    return $this->attributes['billing_permission'] = $value;
  }

  // boolean # Allow this user to skip site-wide IP blacklists?
  public function getBypassSiteAllowedIps() {
    return @$this->attributes['bypass_site_allowed_ips'];
  }

  public function setBypassSiteAllowedIps($value) {
    return $this->attributes['bypass_site_allowed_ips'] = $value;
  }

  // boolean # Exempt this user from being disabled based on inactivity?
  public function getBypassInactiveDisable() {
    return @$this->attributes['bypass_inactive_disable'];
  }

  public function setBypassInactiveDisable($value) {
    return $this->attributes['bypass_inactive_disable'] = $value;
  }

  // date-time # When this user was created
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // boolean # Can the user connect with WebDAV?
  public function getDavPermission() {
    return @$this->attributes['dav_permission'];
  }

  public function setDavPermission($value) {
    return $this->attributes['dav_permission'] = $value;
  }

  // boolean # Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  public function getDisabled() {
    return @$this->attributes['disabled'];
  }

  public function setDisabled($value) {
    return $this->attributes['disabled'] = $value;
  }

  // email # User email address
  public function getEmail() {
    return @$this->attributes['email'];
  }

  public function setEmail($value) {
    return $this->attributes['email'] = $value;
  }

  // boolean # Can the user access with FTP/FTPS?
  public function getFtpPermission() {
    return @$this->attributes['ftp_permission'];
  }

  public function setFtpPermission($value) {
    return $this->attributes['ftp_permission'] = $value;
  }

  // string # Comma-separated list of group IDs of which this user is a member
  public function getGroupIds() {
    return @$this->attributes['group_ids'];
  }

  public function setGroupIds($value) {
    return $this->attributes['group_ids'] = $value;
  }

  // string # Text to display to the user in the header of the UI
  public function getHeaderText() {
    return @$this->attributes['header_text'];
  }

  public function setHeaderText($value) {
    return $this->attributes['header_text'] = $value;
  }

  // string # Preferred language
  public function getLanguage() {
    return @$this->attributes['language'];
  }

  public function setLanguage($value) {
    return $this->attributes['language'] = $value;
  }

  // date-time # User's last login time
  public function getLastLoginAt() {
    return @$this->attributes['last_login_at'];
  }

  public function setLastLoginAt($value) {
    return $this->attributes['last_login_at'] = $value;
  }

  // string # The last protocol and cipher used
  public function getLastProtocolCipher() {
    return @$this->attributes['last_protocol_cipher'];
  }

  public function setLastProtocolCipher($value) {
    return $this->attributes['last_protocol_cipher'] = $value;
  }

  // date-time # Time in the future that the user will no longer be locked out if applicable
  public function getLockoutExpires() {
    return @$this->attributes['lockout_expires'];
  }

  public function setLockoutExpires($value) {
    return $this->attributes['lockout_expires'] = $value;
  }

  // string # User's full name
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # User's company
  public function getCompany() {
    return @$this->attributes['company'];
  }

  public function setCompany($value) {
    return $this->attributes['company'] = $value;
  }

  // string # Any internal notes on the user
  public function getNotes() {
    return @$this->attributes['notes'];
  }

  public function setNotes($value) {
    return $this->attributes['notes'] = $value;
  }

  // int64 # Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  public function getNotificationDailySendTime() {
    return @$this->attributes['notification_daily_send_time'];
  }

  public function setNotificationDailySendTime($value) {
    return $this->attributes['notification_daily_send_time'] = $value;
  }

  // boolean # Enable integration with Office for the web?
  public function getOfficeIntegrationEnabled() {
    return @$this->attributes['office_integration_enabled'];
  }

  public function setOfficeIntegrationEnabled($value) {
    return $this->attributes['office_integration_enabled'] = $value;
  }

  // date-time # Last time the user's password was set
  public function getPasswordSetAt() {
    return @$this->attributes['password_set_at'];
  }

  public function setPasswordSetAt($value) {
    return $this->attributes['password_set_at'] = $value;
  }

  // int64 # Number of days to allow user to use the same password
  public function getPasswordValidityDays() {
    return @$this->attributes['password_validity_days'];
  }

  public function setPasswordValidityDays($value) {
    return $this->attributes['password_validity_days'] = $value;
  }

  // int64 # Number of public keys associated with this user
  public function getPublicKeysCount() {
    return @$this->attributes['public_keys_count'];
  }

  public function setPublicKeysCount($value) {
    return $this->attributes['public_keys_count'] = $value;
  }

  // boolean # Should the user receive admin alerts such a certificate expiration notifications and overages?
  public function getReceiveAdminAlerts() {
    return @$this->attributes['receive_admin_alerts'];
  }

  public function setReceiveAdminAlerts($value) {
    return $this->attributes['receive_admin_alerts'] = $value;
  }

  // string # 2FA required setting
  public function getRequire2fa() {
    return @$this->attributes['require_2fa'];
  }

  public function setRequire2fa($value) {
    return $this->attributes['require_2fa'] = $value;
  }

  // boolean # Is 2fa active for the user?
  public function getActive2fa() {
    return @$this->attributes['active_2fa'];
  }

  public function setActive2fa($value) {
    return $this->attributes['active_2fa'] = $value;
  }

  // boolean # Is a password change required upon next user login?
  public function getRequirePasswordChange() {
    return @$this->attributes['require_password_change'];
  }

  public function setRequirePasswordChange($value) {
    return $this->attributes['require_password_change'] = $value;
  }

  // boolean # Is user's password expired?
  public function getPasswordExpired() {
    return @$this->attributes['password_expired'];
  }

  public function setPasswordExpired($value) {
    return $this->attributes['password_expired'] = $value;
  }

  // boolean # Can this user access the REST API?
  public function getRestapiPermission() {
    return @$this->attributes['restapi_permission'];
  }

  public function setRestapiPermission($value) {
    return $this->attributes['restapi_permission'] = $value;
  }

  // boolean # Does this user manage it's own credentials or is it a shared/bot user?
  public function getSelfManaged() {
    return @$this->attributes['self_managed'];
  }

  public function setSelfManaged($value) {
    return $this->attributes['self_managed'] = $value;
  }

  // boolean # Can the user access with SFTP?
  public function getSftpPermission() {
    return @$this->attributes['sftp_permission'];
  }

  public function setSftpPermission($value) {
    return $this->attributes['sftp_permission'] = $value;
  }

  // boolean # Is the user an administrator for this site?
  public function getSiteAdmin() {
    return @$this->attributes['site_admin'];
  }

  public function setSiteAdmin($value) {
    return $this->attributes['site_admin'] = $value;
  }

  // boolean # Skip Welcome page in the UI?
  public function getSkipWelcomeScreen() {
    return @$this->attributes['skip_welcome_screen'];
  }

  public function setSkipWelcomeScreen($value) {
    return $this->attributes['skip_welcome_screen'] = $value;
  }

  // string # SSL required setting
  public function getSslRequired() {
    return @$this->attributes['ssl_required'];
  }

  public function setSslRequired($value) {
    return $this->attributes['ssl_required'] = $value;
  }

  // int64 # SSO (Single Sign On) strategy ID for the user, if applicable.
  public function getSsoStrategyId() {
    return @$this->attributes['sso_strategy_id'];
  }

  public function setSsoStrategyId($value) {
    return $this->attributes['sso_strategy_id'] = $value;
  }

  // boolean # Is the user subscribed to the newsletter?
  public function getSubscribeToNewsletter() {
    return @$this->attributes['subscribe_to_newsletter'];
  }

  public function setSubscribeToNewsletter($value) {
    return $this->attributes['subscribe_to_newsletter'] = $value;
  }

  // boolean # Is this user managed by a SsoStrategy?
  public function getExternallyManaged() {
    return @$this->attributes['externally_managed'];
  }

  public function setExternallyManaged($value) {
    return $this->attributes['externally_managed'] = $value;
  }

  // string # User time zone
  public function getTimeZone() {
    return @$this->attributes['time_zone'];
  }

  public function setTimeZone($value) {
    return $this->attributes['time_zone'] = $value;
  }

  // string # Type(s) of 2FA methods in use.  Will be either `sms`, `totp`, `u2f`, `yubi`, or multiple values sorted alphabetically and joined by an underscore.
  public function getTypeOf2fa() {
    return @$this->attributes['type_of_2fa'];
  }

  public function setTypeOf2fa($value) {
    return $this->attributes['type_of_2fa'] = $value;
  }

  // string # Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  public function getUserRoot() {
    return @$this->attributes['user_root'];
  }

  public function setUserRoot($value) {
    return $this->attributes['user_root'] = $value;
  }

  // file # An image file for your user avatar.
  public function getAvatarFile() {
    return @$this->attributes['avatar_file'];
  }

  public function setAvatarFile($value) {
    return $this->attributes['avatar_file'] = $value;
  }

  // boolean # If true, the avatar will be deleted.
  public function getAvatarDelete() {
    return @$this->attributes['avatar_delete'];
  }

  public function setAvatarDelete($value) {
    return $this->attributes['avatar_delete'] = $value;
  }

  // string # Used for changing a password on an existing user.
  public function getChangePassword() {
    return @$this->attributes['change_password'];
  }

  public function setChangePassword($value) {
    return $this->attributes['change_password'] = $value;
  }

  // string # Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
  public function getChangePasswordConfirmation() {
    return @$this->attributes['change_password_confirmation'];
  }

  public function setChangePasswordConfirmation($value) {
    return $this->attributes['change_password_confirmation'] = $value;
  }

  // string # Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `list`, or `history`.
  public function getGrantPermission() {
    return @$this->attributes['grant_permission'];
  }

  public function setGrantPermission($value) {
    return $this->attributes['grant_permission'] = $value;
  }

  // int64 # Group ID to associate this user with.
  public function getGroupId() {
    return @$this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // string # Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash menthods are MD5, SHA1, and SHA256.
  public function getImportedPasswordHash() {
    return @$this->attributes['imported_password_hash'];
  }

  public function setImportedPasswordHash($value) {
    return $this->attributes['imported_password_hash'] = $value;
  }

  // string # User password.
  public function getPassword() {
    return @$this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # Optional, but if provided, we will ensure that it matches the value sent in `password`.
  public function getPasswordConfirmation() {
    return @$this->attributes['password_confirmation'];
  }

  public function setPasswordConfirmation($value) {
    return $this->attributes['password_confirmation'] = $value;
  }

  // boolean # Signifies that the user has read all the announcements in the UI.
  public function getAnnouncementsRead() {
    return @$this->attributes['announcements_read'];
  }

  public function setAnnouncementsRead($value) {
    return $this->attributes['announcements_read'] = $value;
  }

  // Unlock user who has been locked out due to failed logins
  public function unlock($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '/unlock', 'POST', $params, $this->options);
    return $response->data;
  }

  // Resend user welcome email
  public function resendWelcomeEmail($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '/resend_welcome_email', 'POST', $params, $this->options);
    return $response->data;
  }

  // Trigger 2FA Reset process for user who has lost access to their existing 2FA methods
  public function user2faReset($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '/2fa/reset', 'POST', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   avatar_file - file - An image file for your user avatar.
  //   avatar_delete - boolean - If true, the avatar will be deleted.
  //   change_password - string - Used for changing a password on an existing user.
  //   change_password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
  //   email - string - User's email.
  //   grant_permission - string - Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `list`, or `history`.
  //   group_id - int64 - Group ID to associate this user with.
  //   group_ids - string - A list of group ids to associate this user with.  Comma delimited.
  //   imported_password_hash - string - Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash menthods are MD5, SHA1, and SHA256.
  //   password - string - User password.
  //   password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `password`.
  //   announcements_read - boolean - Signifies that the user has read all the announcements in the UI.
  //   allowed_ips - string - A list of allowed IPs if applicable.  Newline delimited
  //   attachments_permission - boolean - DEPRECATED: Can the user create Bundles (aka Share Links)? Use the bundle permission instead.
  //   authenticate_until - string - Scheduled Date/Time at which user will be deactivated
  //   authentication_method - string - How is this user authenticated?
  //   billing_permission - boolean - Allow this user to perform operations on the account, payments, and invoices?
  //   bypass_inactive_disable - boolean - Exempt this user from being disabled based on inactivity?
  //   bypass_site_allowed_ips - boolean - Allow this user to skip site-wide IP blacklists?
  //   dav_permission - boolean - Can the user connect with WebDAV?
  //   disabled - boolean - Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  //   ftp_permission - boolean - Can the user access with FTP/FTPS?
  //   header_text - string - Text to display to the user in the header of the UI
  //   language - string - Preferred language
  //   notification_daily_send_time - int64 - Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  //   name - string - User's full name
  //   company - string - User's company
  //   notes - string - Any internal notes on the user
  //   office_integration_enabled - boolean - Enable integration with Office for the web?
  //   password_validity_days - int64 - Number of days to allow user to use the same password
  //   receive_admin_alerts - boolean - Should the user receive admin alerts such a certificate expiration notifications and overages?
  //   require_password_change - boolean - Is a password change required upon next user login?
  //   restapi_permission - boolean - Can this user access the REST API?
  //   self_managed - boolean - Does this user manage it's own credentials or is it a shared/bot user?
  //   sftp_permission - boolean - Can the user access with SFTP?
  //   site_admin - boolean - Is the user an administrator for this site?
  //   skip_welcome_screen - boolean - Skip Welcome page in the UI?
  //   ssl_required - string - SSL required setting
  //   sso_strategy_id - int64 - SSO (Single Sign On) strategy ID for the user, if applicable.
  //   subscribe_to_newsletter - boolean - Is the user subscribed to the newsletter?
  //   require_2fa - string - 2FA required setting
  //   time_zone - string - User time zone
  //   user_root - string - Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  //   username - string - User's username
  public function update($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['change_password'] && !is_string(@$params['change_password'])) {
      throw new \Files\InvalidParameterException('$change_password must be of type string; received ' . gettype($change_password));
    }

    if (@$params['change_password_confirmation'] && !is_string(@$params['change_password_confirmation'])) {
      throw new \Files\InvalidParameterException('$change_password_confirmation must be of type string; received ' . gettype($change_password_confirmation));
    }

    if (@$params['email'] && !is_string(@$params['email'])) {
      throw new \Files\InvalidParameterException('$email must be of type string; received ' . gettype($email));
    }

    if (@$params['grant_permission'] && !is_string(@$params['grant_permission'])) {
      throw new \Files\InvalidParameterException('$grant_permission must be of type string; received ' . gettype($grant_permission));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \Files\InvalidParameterException('$group_ids must be of type string; received ' . gettype($group_ids));
    }

    if (@$params['imported_password_hash'] && !is_string(@$params['imported_password_hash'])) {
      throw new \Files\InvalidParameterException('$imported_password_hash must be of type string; received ' . gettype($imported_password_hash));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype($password));
    }

    if (@$params['password_confirmation'] && !is_string(@$params['password_confirmation'])) {
      throw new \Files\InvalidParameterException('$password_confirmation must be of type string; received ' . gettype($password_confirmation));
    }

    if (@$params['allowed_ips'] && !is_string(@$params['allowed_ips'])) {
      throw new \Files\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype($allowed_ips));
    }

    if (@$params['authenticate_until'] && !is_string(@$params['authenticate_until'])) {
      throw new \Files\InvalidParameterException('$authenticate_until must be of type string; received ' . gettype($authenticate_until));
    }

    if (@$params['authentication_method'] && !is_string(@$params['authentication_method'])) {
      throw new \Files\InvalidParameterException('$authentication_method must be of type string; received ' . gettype($authentication_method));
    }

    if (@$params['header_text'] && !is_string(@$params['header_text'])) {
      throw new \Files\InvalidParameterException('$header_text must be of type string; received ' . gettype($header_text));
    }

    if (@$params['language'] && !is_string(@$params['language'])) {
      throw new \Files\InvalidParameterException('$language must be of type string; received ' . gettype($language));
    }

    if (@$params['notification_daily_send_time'] && !is_int(@$params['notification_daily_send_time'])) {
      throw new \Files\InvalidParameterException('$notification_daily_send_time must be of type int; received ' . gettype($notification_daily_send_time));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['company'] && !is_string(@$params['company'])) {
      throw new \Files\InvalidParameterException('$company must be of type string; received ' . gettype($company));
    }

    if (@$params['notes'] && !is_string(@$params['notes'])) {
      throw new \Files\InvalidParameterException('$notes must be of type string; received ' . gettype($notes));
    }

    if (@$params['password_validity_days'] && !is_int(@$params['password_validity_days'])) {
      throw new \Files\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype($password_validity_days));
    }

    if (@$params['ssl_required'] && !is_string(@$params['ssl_required'])) {
      throw new \Files\InvalidParameterException('$ssl_required must be of type string; received ' . gettype($ssl_required));
    }

    if (@$params['sso_strategy_id'] && !is_int(@$params['sso_strategy_id'])) {
      throw new \Files\InvalidParameterException('$sso_strategy_id must be of type int; received ' . gettype($sso_strategy_id));
    }

    if (@$params['require_2fa'] && !is_string(@$params['require_2fa'])) {
      throw new \Files\InvalidParameterException('$require_2fa must be of type string; received ' . gettype($require_2fa));
    }

    if (@$params['time_zone'] && !is_string(@$params['time_zone'])) {
      throw new \Files\InvalidParameterException('$time_zone must be of type string; received ' . gettype($time_zone));
    }

    if (@$params['user_root'] && !is_string(@$params['user_root'])) {
      throw new \Files\InvalidParameterException('$user_root must be of type string; received ' . gettype($user_root));
    }

    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  public function delete($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `authenticate_until`, `email`, `last_desktop_login_at`, `last_login_at`, `username`, `company`, `name`, `site_admin`, `receive_admin_alerts`, `password_validity_days`, `ssl_required` or `not_site_admin`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal to the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   filter_like - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal to the supplied value. Valid fields are `username`, `email`, `company`, `site_admin`, `password_validity_days`, `ssl_required`, `last_login_at`, `authenticate_until` or `not_site_admin`.
  //   ids - string - comma-separated list of User IDs
  //   q[username] - string - List users matching username.
  //   q[email] - string - List users matching email.
  //   q[notes] - string - List users matching notes field.
  //   q[admin] - string - If `true`, list only admin users.
  //   q[allowed_ips] - string - If set, list only users with overridden allowed IP setting.
  //   q[password_validity_days] - string - If set, list only users with overridden password validity days setting.
  //   q[ssl_required] - string - If set, list only users with overridden SSL required setting.
  //   search - string - Searches for partial matches of name, username, or email.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['ids'] && !is_string(@$params['ids'])) {
      throw new \Files\InvalidParameterException('$ids must be of type string; received ' . gettype($ids));
    }

    if (@$params['search'] && !is_string(@$params['search'])) {
      throw new \Files\InvalidParameterException('$search must be of type string; received ' . gettype($search));
    }

    $response = Api::sendRequest('/users', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new User((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - User ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/users/' . @$params['id'] . '', 'GET', $params, $options);

    return new User((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   avatar_file - file - An image file for your user avatar.
  //   avatar_delete - boolean - If true, the avatar will be deleted.
  //   change_password - string - Used for changing a password on an existing user.
  //   change_password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
  //   email - string - User's email.
  //   grant_permission - string - Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `list`, or `history`.
  //   group_id - int64 - Group ID to associate this user with.
  //   group_ids - string - A list of group ids to associate this user with.  Comma delimited.
  //   imported_password_hash - string - Pre-calculated hash of the user's password. If supplied, this will be used to authenticate the user on first login. Supported hash menthods are MD5, SHA1, and SHA256.
  //   password - string - User password.
  //   password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `password`.
  //   announcements_read - boolean - Signifies that the user has read all the announcements in the UI.
  //   allowed_ips - string - A list of allowed IPs if applicable.  Newline delimited
  //   attachments_permission - boolean - DEPRECATED: Can the user create Bundles (aka Share Links)? Use the bundle permission instead.
  //   authenticate_until - string - Scheduled Date/Time at which user will be deactivated
  //   authentication_method - string - How is this user authenticated?
  //   billing_permission - boolean - Allow this user to perform operations on the account, payments, and invoices?
  //   bypass_inactive_disable - boolean - Exempt this user from being disabled based on inactivity?
  //   bypass_site_allowed_ips - boolean - Allow this user to skip site-wide IP blacklists?
  //   dav_permission - boolean - Can the user connect with WebDAV?
  //   disabled - boolean - Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  //   ftp_permission - boolean - Can the user access with FTP/FTPS?
  //   header_text - string - Text to display to the user in the header of the UI
  //   language - string - Preferred language
  //   notification_daily_send_time - int64 - Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  //   name - string - User's full name
  //   company - string - User's company
  //   notes - string - Any internal notes on the user
  //   office_integration_enabled - boolean - Enable integration with Office for the web?
  //   password_validity_days - int64 - Number of days to allow user to use the same password
  //   receive_admin_alerts - boolean - Should the user receive admin alerts such a certificate expiration notifications and overages?
  //   require_password_change - boolean - Is a password change required upon next user login?
  //   restapi_permission - boolean - Can this user access the REST API?
  //   self_managed - boolean - Does this user manage it's own credentials or is it a shared/bot user?
  //   sftp_permission - boolean - Can the user access with SFTP?
  //   site_admin - boolean - Is the user an administrator for this site?
  //   skip_welcome_screen - boolean - Skip Welcome page in the UI?
  //   ssl_required - string - SSL required setting
  //   sso_strategy_id - int64 - SSO (Single Sign On) strategy ID for the user, if applicable.
  //   subscribe_to_newsletter - boolean - Is the user subscribed to the newsletter?
  //   require_2fa - string - 2FA required setting
  //   time_zone - string - User time zone
  //   user_root - string - Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  //   username - string - User's username
  public static function create($params = [], $options = []) {
    if (@$params['change_password'] && !is_string(@$params['change_password'])) {
      throw new \Files\InvalidParameterException('$change_password must be of type string; received ' . gettype($change_password));
    }

    if (@$params['change_password_confirmation'] && !is_string(@$params['change_password_confirmation'])) {
      throw new \Files\InvalidParameterException('$change_password_confirmation must be of type string; received ' . gettype($change_password_confirmation));
    }

    if (@$params['email'] && !is_string(@$params['email'])) {
      throw new \Files\InvalidParameterException('$email must be of type string; received ' . gettype($email));
    }

    if (@$params['grant_permission'] && !is_string(@$params['grant_permission'])) {
      throw new \Files\InvalidParameterException('$grant_permission must be of type string; received ' . gettype($grant_permission));
    }

    if (@$params['group_id'] && !is_int(@$params['group_id'])) {
      throw new \Files\InvalidParameterException('$group_id must be of type int; received ' . gettype($group_id));
    }

    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \Files\InvalidParameterException('$group_ids must be of type string; received ' . gettype($group_ids));
    }

    if (@$params['imported_password_hash'] && !is_string(@$params['imported_password_hash'])) {
      throw new \Files\InvalidParameterException('$imported_password_hash must be of type string; received ' . gettype($imported_password_hash));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype($password));
    }

    if (@$params['password_confirmation'] && !is_string(@$params['password_confirmation'])) {
      throw new \Files\InvalidParameterException('$password_confirmation must be of type string; received ' . gettype($password_confirmation));
    }

    if (@$params['allowed_ips'] && !is_string(@$params['allowed_ips'])) {
      throw new \Files\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype($allowed_ips));
    }

    if (@$params['authenticate_until'] && !is_string(@$params['authenticate_until'])) {
      throw new \Files\InvalidParameterException('$authenticate_until must be of type string; received ' . gettype($authenticate_until));
    }

    if (@$params['authentication_method'] && !is_string(@$params['authentication_method'])) {
      throw new \Files\InvalidParameterException('$authentication_method must be of type string; received ' . gettype($authentication_method));
    }

    if (@$params['header_text'] && !is_string(@$params['header_text'])) {
      throw new \Files\InvalidParameterException('$header_text must be of type string; received ' . gettype($header_text));
    }

    if (@$params['language'] && !is_string(@$params['language'])) {
      throw new \Files\InvalidParameterException('$language must be of type string; received ' . gettype($language));
    }

    if (@$params['notification_daily_send_time'] && !is_int(@$params['notification_daily_send_time'])) {
      throw new \Files\InvalidParameterException('$notification_daily_send_time must be of type int; received ' . gettype($notification_daily_send_time));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['company'] && !is_string(@$params['company'])) {
      throw new \Files\InvalidParameterException('$company must be of type string; received ' . gettype($company));
    }

    if (@$params['notes'] && !is_string(@$params['notes'])) {
      throw new \Files\InvalidParameterException('$notes must be of type string; received ' . gettype($notes));
    }

    if (@$params['password_validity_days'] && !is_int(@$params['password_validity_days'])) {
      throw new \Files\InvalidParameterException('$password_validity_days must be of type int; received ' . gettype($password_validity_days));
    }

    if (@$params['ssl_required'] && !is_string(@$params['ssl_required'])) {
      throw new \Files\InvalidParameterException('$ssl_required must be of type string; received ' . gettype($ssl_required));
    }

    if (@$params['sso_strategy_id'] && !is_int(@$params['sso_strategy_id'])) {
      throw new \Files\InvalidParameterException('$sso_strategy_id must be of type int; received ' . gettype($sso_strategy_id));
    }

    if (@$params['require_2fa'] && !is_string(@$params['require_2fa'])) {
      throw new \Files\InvalidParameterException('$require_2fa must be of type string; received ' . gettype($require_2fa));
    }

    if (@$params['time_zone'] && !is_string(@$params['time_zone'])) {
      throw new \Files\InvalidParameterException('$time_zone must be of type string; received ' . gettype($time_zone));
    }

    if (@$params['user_root'] && !is_string(@$params['user_root'])) {
      throw new \Files\InvalidParameterException('$user_root must be of type string; received ' . gettype($user_root));
    }

    if (@$params['username'] && !is_string(@$params['username'])) {
      throw new \Files\InvalidParameterException('$username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/users', 'POST', $params, $options);

    return new User((array)(@$response->data ?: []), $options);
  }
}
