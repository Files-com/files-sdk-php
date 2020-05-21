<?php

declare(strict_types=1);

namespace Files;

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

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // int64 # User ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # User's username
  public function getUsername() {
    return $this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // array # List of group IDs of which this user is an administrator
  public function getAdminGroupIds() {
    return $this->attributes['admin_group_ids'];
  }

  public function setAdminGroupIds($value) {
    return $this->attributes['admin_group_ids'] = $value;
  }

  // string # A list of allowed IPs if applicable.  Newline delimited
  public function getAllowedIps() {
    return $this->attributes['allowed_ips'];
  }

  public function setAllowedIps($value) {
    return $this->attributes['allowed_ips'] = $value;
  }

  // boolean # Can the user create Bundles (aka Share Links)?  This field will be aliased or renamed in the future to `bundles_permission`.
  public function getAttachmentsPermission() {
    return $this->attributes['attachments_permission'];
  }

  public function setAttachmentsPermission($value) {
    return $this->attributes['attachments_permission'] = $value;
  }

  // int64 # Number of api keys associated with this user
  public function getApiKeysCount() {
    return $this->attributes['api_keys_count'];
  }

  public function setApiKeysCount($value) {
    return $this->attributes['api_keys_count'] = $value;
  }

  // date-time # Scheduled Date/Time at which user will be deactivated
  public function getAuthenticateUntil() {
    return $this->attributes['authenticate_until'];
  }

  public function setAuthenticateUntil($value) {
    return $this->attributes['authenticate_until'] = $value;
  }

  // string # How is this user authenticated?
  public function getAuthenticationMethod() {
    return $this->attributes['authentication_method'];
  }

  public function setAuthenticationMethod($value) {
    return $this->attributes['authentication_method'] = $value;
  }

  // string # URL holding the user's avatar
  public function getAvatarUrl() {
    return $this->attributes['avatar_url'];
  }

  public function setAvatarUrl($value) {
    return $this->attributes['avatar_url'] = $value;
  }

  // boolean # Allow this user to perform operations on the account, payments, and invoices?
  public function getBillingPermission() {
    return $this->attributes['billing_permission'];
  }

  public function setBillingPermission($value) {
    return $this->attributes['billing_permission'] = $value;
  }

  // boolean # Allow this user to skip site-wide IP blacklists?
  public function getBypassSiteAllowedIps() {
    return $this->attributes['bypass_site_allowed_ips'];
  }

  public function setBypassSiteAllowedIps($value) {
    return $this->attributes['bypass_site_allowed_ips'] = $value;
  }

  // boolean # Exempt this user from being disabled based on inactivity?
  public function getBypassInactiveDisable() {
    return $this->attributes['bypass_inactive_disable'];
  }

  public function setBypassInactiveDisable($value) {
    return $this->attributes['bypass_inactive_disable'] = $value;
  }

  // date-time # When this user was created
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // boolean # Can the user connect with WebDAV?
  public function getDavPermission() {
    return $this->attributes['dav_permission'];
  }

  public function setDavPermission($value) {
    return $this->attributes['dav_permission'] = $value;
  }

  // boolean # Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  public function getDisabled() {
    return $this->attributes['disabled'];
  }

  public function setDisabled($value) {
    return $this->attributes['disabled'] = $value;
  }

  // email # User email address
  public function getEmail() {
    return $this->attributes['email'];
  }

  public function setEmail($value) {
    return $this->attributes['email'] = $value;
  }

  // boolean # Can the user access with FTP/FTPS?
  public function getFtpPermission() {
    return $this->attributes['ftp_permission'];
  }

  public function setFtpPermission($value) {
    return $this->attributes['ftp_permission'] = $value;
  }

  // array # Comma-separated list of group IDs of which this user is a member
  public function getGroupIds() {
    return $this->attributes['group_ids'];
  }

  public function setGroupIds($value) {
    return $this->attributes['group_ids'] = $value;
  }

  // string # Preferred language
  public function getLanguage() {
    return $this->attributes['language'];
  }

  public function setLanguage($value) {
    return $this->attributes['language'] = $value;
  }

  // date-time # User's last login time
  public function getLastLoginAt() {
    return $this->attributes['last_login_at'];
  }

  public function setLastLoginAt($value) {
    return $this->attributes['last_login_at'] = $value;
  }

  // string # The last protocol and cipher used
  public function getLastProtocolCipher() {
    return $this->attributes['last_protocol_cipher'];
  }

  public function setLastProtocolCipher($value) {
    return $this->attributes['last_protocol_cipher'] = $value;
  }

  // date-time # Time in the future that the user will no longer be locked out if applicable
  public function getLockoutExpires() {
    return $this->attributes['lockout_expires'];
  }

  public function setLockoutExpires($value) {
    return $this->attributes['lockout_expires'] = $value;
  }

  // string # User's full name
  public function getName() {
    return $this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # Any internal notes on the user
  public function getNotes() {
    return $this->attributes['notes'];
  }

  public function setNotes($value) {
    return $this->attributes['notes'] = $value;
  }

  // int64 # Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  public function getNotificationDailySendTime() {
    return $this->attributes['notification_daily_send_time'];
  }

  public function setNotificationDailySendTime($value) {
    return $this->attributes['notification_daily_send_time'] = $value;
  }

  // date-time # Last time the user's password was set
  public function getPasswordSetAt() {
    return $this->attributes['password_set_at'];
  }

  public function setPasswordSetAt($value) {
    return $this->attributes['password_set_at'] = $value;
  }

  // int64 # Number of days to allow user to use the same password
  public function getPasswordValidityDays() {
    return $this->attributes['password_validity_days'];
  }

  public function setPasswordValidityDays($value) {
    return $this->attributes['password_validity_days'] = $value;
  }

  // int64 # Number of public keys associated with this user
  public function getPublicKeysCount() {
    return $this->attributes['public_keys_count'];
  }

  public function setPublicKeysCount($value) {
    return $this->attributes['public_keys_count'] = $value;
  }

  // boolean # Should the user receive admin alerts such a certificate expiration notifications and overages?
  public function getReceiveAdminAlerts() {
    return $this->attributes['receive_admin_alerts'];
  }

  public function setReceiveAdminAlerts($value) {
    return $this->attributes['receive_admin_alerts'] = $value;
  }

  // boolean # Is 2fa required to sign in?
  public function getRequire2fa() {
    return $this->attributes['require_2fa'];
  }

  public function setRequire2fa($value) {
    return $this->attributes['require_2fa'] = $value;
  }

  // boolean # Is a password change required upon next user login?
  public function getRequirePasswordChange() {
    return $this->attributes['require_password_change'];
  }

  public function setRequirePasswordChange($value) {
    return $this->attributes['require_password_change'] = $value;
  }

  // boolean # Can this user access the REST API?
  public function getRestapiPermission() {
    return $this->attributes['restapi_permission'];
  }

  public function setRestapiPermission($value) {
    return $this->attributes['restapi_permission'] = $value;
  }

  // boolean # Does this user manage it's own credentials or is it a shared/bot user?
  public function getSelfManaged() {
    return $this->attributes['self_managed'];
  }

  public function setSelfManaged($value) {
    return $this->attributes['self_managed'] = $value;
  }

  // boolean # Can the user access with SFTP?
  public function getSftpPermission() {
    return $this->attributes['sftp_permission'];
  }

  public function setSftpPermission($value) {
    return $this->attributes['sftp_permission'] = $value;
  }

  // boolean # Is the user an administrator for this site?
  public function getSiteAdmin() {
    return $this->attributes['site_admin'];
  }

  public function setSiteAdmin($value) {
    return $this->attributes['site_admin'] = $value;
  }

  // boolean # Skip Welcome page in the UI?
  public function getSkipWelcomeScreen() {
    return $this->attributes['skip_welcome_screen'];
  }

  public function setSkipWelcomeScreen($value) {
    return $this->attributes['skip_welcome_screen'] = $value;
  }

  // string # SSL required setting
  public function getSslRequired() {
    return $this->attributes['ssl_required'];
  }

  public function setSslRequired($value) {
    return $this->attributes['ssl_required'] = $value;
  }

  // int64 # SSO (Single Sign On) strategy ID for the user, if applicable.
  public function getSsoStrategyId() {
    return $this->attributes['sso_strategy_id'];
  }

  public function setSsoStrategyId($value) {
    return $this->attributes['sso_strategy_id'] = $value;
  }

  // boolean # Is the user subscribed to the newsletter?
  public function getSubscribeToNewsletter() {
    return $this->attributes['subscribe_to_newsletter'];
  }

  public function setSubscribeToNewsletter($value) {
    return $this->attributes['subscribe_to_newsletter'] = $value;
  }

  // boolean # Is this user managed by an external source (such as LDAP)?
  public function getExternallyManaged() {
    return $this->attributes['externally_managed'];
  }

  public function setExternallyManaged($value) {
    return $this->attributes['externally_managed'] = $value;
  }

  // string # User time zone
  public function getTimeZone() {
    return $this->attributes['time_zone'];
  }

  public function setTimeZone($value) {
    return $this->attributes['time_zone'] = $value;
  }

  // string # Type(s) of 2FA methods in use.  Will be either `sms`, `totp`, `u2f`, `yubi`, or multiple values sorted alphabetically and joined by an underscore.
  public function getTypeOf2fa() {
    return $this->attributes['type_of_2fa'];
  }

  public function setTypeOf2fa($value) {
    return $this->attributes['type_of_2fa'] = $value;
  }

  // string # Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  public function getUserRoot() {
    return $this->attributes['user_root'];
  }

  public function setUserRoot($value) {
    return $this->attributes['user_root'] = $value;
  }

  // file # An image file for your user avatar.
  public function getAvatarFile() {
    return $this->attributes['avatar_file'];
  }

  public function setAvatarFile($value) {
    return $this->attributes['avatar_file'] = $value;
  }

  // boolean # If true, the avatar will be deleted.
  public function getAvatarDelete() {
    return $this->attributes['avatar_delete'];
  }

  public function setAvatarDelete($value) {
    return $this->attributes['avatar_delete'] = $value;
  }

  // string # Used for changing a password on an existing user.
  public function getChangePassword() {
    return $this->attributes['change_password'];
  }

  public function setChangePassword($value) {
    return $this->attributes['change_password'] = $value;
  }

  // string # Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
  public function getChangePasswordConfirmation() {
    return $this->attributes['change_password_confirmation'];
  }

  public function setChangePasswordConfirmation($value) {
    return $this->attributes['change_password_confirmation'] = $value;
  }

  // string # Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `preview`, or `history`.
  public function getGrantPermission() {
    return $this->attributes['grant_permission'];
  }

  public function setGrantPermission($value) {
    return $this->attributes['grant_permission'] = $value;
  }

  // int64 # Group ID to associate this user with.
  public function getGroupId() {
    return $this->attributes['group_id'];
  }

  public function setGroupId($value) {
    return $this->attributes['group_id'] = $value;
  }

  // string # User password.
  public function getPassword() {
    return $this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // string # Optional, but if provided, we will ensure that it matches the value sent in `password`.
  public function getPasswordConfirmation() {
    return $this->attributes['password_confirmation'];
  }

  public function setPasswordConfirmation($value) {
    return $this->attributes['password_confirmation'] = $value;
  }

  // boolean # Signifies that the user has read all the announcements in the UI.
  public function getAnnouncementsRead() {
    return $this->attributes['announcements_read'];
  }

  public function setAnnouncementsRead($value) {
    return $this->attributes['announcements_read'] = $value;
  }

  // Unlock user who has been locked out due to failed logins
  public function unlock($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/users/' . $params['id'] . '/unlock', 'POST', $params);
  }

  // Resend user welcome email
  public function resendWelcomeEmail($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/users/' . $params['id'] . '/resend_welcome_email', 'POST', $params);
  }

  // Trigger 2FA Reset process for user who has lost access to their existing 2FA methods
  public function user2faReset($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/users/' . $params['id'] . '/2fa/reset', 'POST', $params);
  }

  // Parameters:
  //   avatar_file - file - An image file for your user avatar.
  //   avatar_delete - boolean - If true, the avatar will be deleted.
  //   change_password - string - Used for changing a password on an existing user.
  //   change_password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `change_password`.
  //   email - string - User's email.
  //   grant_permission - string - Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `preview`, or `history`.
  //   group_id - integer - Group ID to associate this user with.
  //   group_ids - string - A list of group ids to associate this user with.  Comma delimited.
  //   password - string - User password.
  //   password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `password`.
  //   announcements_read - boolean - Signifies that the user has read all the announcements in the UI.
  //   allowed_ips - string - A list of allowed IPs if applicable.  Newline delimited
  //   attachments_permission - boolean - Can the user create Bundles (aka Share Links)?  This field will be aliased or renamed in the future to `bundles_permission`.
  //   authenticate_until - string - Scheduled Date/Time at which user will be deactivated
  //   authentication_method - string - How is this user authenticated?
  //   billing_permission - boolean - Allow this user to perform operations on the account, payments, and invoices?
  //   bypass_inactive_disable - boolean - Exempt this user from being disabled based on inactivity?
  //   bypass_site_allowed_ips - boolean - Allow this user to skip site-wide IP blacklists?
  //   dav_permission - boolean - Can the user connect with WebDAV?
  //   disabled - boolean - Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  //   ftp_permission - boolean - Can the user access with FTP/FTPS?
  //   language - string - Preferred language
  //   notification_daily_send_time - integer - Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  //   name - string - User's full name
  //   notes - string - Any internal notes on the user
  //   password_validity_days - integer - Number of days to allow user to use the same password
  //   receive_admin_alerts - boolean - Should the user receive admin alerts such a certificate expiration notifications and overages?
  //   require_password_change - boolean - Is a password change required upon next user login?
  //   restapi_permission - boolean - Can this user access the REST API?
  //   self_managed - boolean - Does this user manage it's own credentials or is it a shared/bot user?
  //   sftp_permission - boolean - Can the user access with SFTP?
  //   site_admin - boolean - Is the user an administrator for this site?
  //   skip_welcome_screen - boolean - Skip Welcome page in the UI?
  //   ssl_required - string - SSL required setting
  //   sso_strategy_id - integer - SSO (Single Sign On) strategy ID for the user, if applicable.
  //   subscribe_to_newsletter - boolean - Is the user subscribed to the newsletter?
  //   time_zone - string - User time zone
  //   user_root - string - Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  //   username - string - User's username
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if ($params['change_password'] && !is_string($params['change_password'])) {
      throw new \InvalidArgumentException('Bad parameter: $change_password must be of type string; received ' . gettype($change_password));
    }
    if ($params['change_password_confirmation'] && !is_string($params['change_password_confirmation'])) {
      throw new \InvalidArgumentException('Bad parameter: $change_password_confirmation must be of type string; received ' . gettype($change_password_confirmation));
    }
    if ($params['email'] && !is_string($params['email'])) {
      throw new \InvalidArgumentException('Bad parameter: $email must be of type string; received ' . gettype($email));
    }
    if ($params['grant_permission'] && !is_string($params['grant_permission'])) {
      throw new \InvalidArgumentException('Bad parameter: $grant_permission must be of type string; received ' . gettype($grant_permission));
    }
    if ($params['group_id'] && !is_int($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type int; received ' . gettype($group_id));
    }
    if ($params['group_ids'] && !is_string($params['group_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_ids must be of type string; received ' . gettype($group_ids));
    }
    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }
    if ($params['password_confirmation'] && !is_string($params['password_confirmation'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_confirmation must be of type string; received ' . gettype($password_confirmation));
    }
    if ($params['allowed_ips'] && !is_string($params['allowed_ips'])) {
      throw new \InvalidArgumentException('Bad parameter: $allowed_ips must be of type string; received ' . gettype($allowed_ips));
    }
    if ($params['authenticate_until'] && !is_string($params['authenticate_until'])) {
      throw new \InvalidArgumentException('Bad parameter: $authenticate_until must be of type string; received ' . gettype($authenticate_until));
    }
    if ($params['authentication_method'] && !is_string($params['authentication_method'])) {
      throw new \InvalidArgumentException('Bad parameter: $authentication_method must be of type string; received ' . gettype($authentication_method));
    }
    if ($params['language'] && !is_string($params['language'])) {
      throw new \InvalidArgumentException('Bad parameter: $language must be of type string; received ' . gettype($language));
    }
    if ($params['notification_daily_send_time'] && !is_int($params['notification_daily_send_time'])) {
      throw new \InvalidArgumentException('Bad parameter: $notification_daily_send_time must be of type int; received ' . gettype($notification_daily_send_time));
    }
    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }
    if ($params['notes'] && !is_string($params['notes'])) {
      throw new \InvalidArgumentException('Bad parameter: $notes must be of type string; received ' . gettype($notes));
    }
    if ($params['password_validity_days'] && !is_int($params['password_validity_days'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_validity_days must be of type int; received ' . gettype($password_validity_days));
    }
    if ($params['ssl_required'] && !is_string($params['ssl_required'])) {
      throw new \InvalidArgumentException('Bad parameter: $ssl_required must be of type string; received ' . gettype($ssl_required));
    }
    if ($params['sso_strategy_id'] && !is_int($params['sso_strategy_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $sso_strategy_id must be of type int; received ' . gettype($sso_strategy_id));
    }
    if ($params['time_zone'] && !is_string($params['time_zone'])) {
      throw new \InvalidArgumentException('Bad parameter: $time_zone must be of type string; received ' . gettype($time_zone));
    }
    if ($params['user_root'] && !is_string($params['user_root'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_root must be of type string; received ' . gettype($user_root));
    }
    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/users/' . $params['id'] . '', 'PATCH', $params);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/users/' . $params['id'] . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      return $this->update($this->attributes);
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   page - integer - Current page number.
  //   per_page - integer - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   q[username] - string - List users matching username.
  //   q[email] - string - List users matching email.
  //   q[notes] - string - List users matching notes field.
  //   q[admin] - string - If `true`, list only admin users.
  //   q[allowed_ips] - string - If set, list only users with overridden allowed IP setting.
  //   q[password_validity_days] - string - If set, list only users with overridden password validity days setting.
  //   q[ssl_required] - string - If set, list only users with overridden SSL required setting.
  //   search - string - Searches for partial matches of name, username, or email.
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

    if ($params['search'] && !is_string($params['search'])) {
      throw new \InvalidArgumentException('Bad parameter: $search must be of type string; received ' . gettype($search));
    }

    $response = Api::sendRequest('/users', 'GET', $params);

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
  //   id (required) - integer - User ID.
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

    $response = Api::sendRequest('/users/' . $params['id'] . '', 'GET', $params);

    return new User((array)$response->data, $options);
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
  //   grant_permission - string - Permission to grant on the user root.  Can be blank or `full`, `read`, `write`, `preview`, or `history`.
  //   group_id - integer - Group ID to associate this user with.
  //   group_ids - string - A list of group ids to associate this user with.  Comma delimited.
  //   password - string - User password.
  //   password_confirmation - string - Optional, but if provided, we will ensure that it matches the value sent in `password`.
  //   announcements_read - boolean - Signifies that the user has read all the announcements in the UI.
  //   allowed_ips - string - A list of allowed IPs if applicable.  Newline delimited
  //   attachments_permission - boolean - Can the user create Bundles (aka Share Links)?  This field will be aliased or renamed in the future to `bundles_permission`.
  //   authenticate_until - string - Scheduled Date/Time at which user will be deactivated
  //   authentication_method - string - How is this user authenticated?
  //   billing_permission - boolean - Allow this user to perform operations on the account, payments, and invoices?
  //   bypass_inactive_disable - boolean - Exempt this user from being disabled based on inactivity?
  //   bypass_site_allowed_ips - boolean - Allow this user to skip site-wide IP blacklists?
  //   dav_permission - boolean - Can the user connect with WebDAV?
  //   disabled - boolean - Is user disabled? Disabled users cannot log in, and do not count for billing purposes.  Users can be automatically disabled after an inactivity period via a Site setting.
  //   ftp_permission - boolean - Can the user access with FTP/FTPS?
  //   language - string - Preferred language
  //   notification_daily_send_time - integer - Hour of the day at which daily notifications should be sent. Can be in range 0 to 23
  //   name - string - User's full name
  //   notes - string - Any internal notes on the user
  //   password_validity_days - integer - Number of days to allow user to use the same password
  //   receive_admin_alerts - boolean - Should the user receive admin alerts such a certificate expiration notifications and overages?
  //   require_password_change - boolean - Is a password change required upon next user login?
  //   restapi_permission - boolean - Can this user access the REST API?
  //   self_managed - boolean - Does this user manage it's own credentials or is it a shared/bot user?
  //   sftp_permission - boolean - Can the user access with SFTP?
  //   site_admin - boolean - Is the user an administrator for this site?
  //   skip_welcome_screen - boolean - Skip Welcome page in the UI?
  //   ssl_required - string - SSL required setting
  //   sso_strategy_id - integer - SSO (Single Sign On) strategy ID for the user, if applicable.
  //   subscribe_to_newsletter - boolean - Is the user subscribed to the newsletter?
  //   time_zone - string - User time zone
  //   user_root - string - Root folder for FTP (and optionally SFTP if the appropriate site-wide setting is set.)  Note that this is not used for API, Desktop, or Web interface.
  //   username - string - User's username
  public static function create($params = [], $options = []) {
    if ($params['change_password'] && !is_string($params['change_password'])) {
      throw new \InvalidArgumentException('Bad parameter: $change_password must be of type string; received ' . gettype($change_password));
    }

    if ($params['change_password_confirmation'] && !is_string($params['change_password_confirmation'])) {
      throw new \InvalidArgumentException('Bad parameter: $change_password_confirmation must be of type string; received ' . gettype($change_password_confirmation));
    }

    if ($params['email'] && !is_string($params['email'])) {
      throw new \InvalidArgumentException('Bad parameter: $email must be of type string; received ' . gettype($email));
    }

    if ($params['grant_permission'] && !is_string($params['grant_permission'])) {
      throw new \InvalidArgumentException('Bad parameter: $grant_permission must be of type string; received ' . gettype($grant_permission));
    }

    if ($params['group_id'] && !is_int($params['group_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_id must be of type int; received ' . gettype($group_id));
    }

    if ($params['group_ids'] && !is_string($params['group_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_ids must be of type string; received ' . gettype($group_ids));
    }

    if ($params['password'] && !is_string($params['password'])) {
      throw new \InvalidArgumentException('Bad parameter: $password must be of type string; received ' . gettype($password));
    }

    if ($params['password_confirmation'] && !is_string($params['password_confirmation'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_confirmation must be of type string; received ' . gettype($password_confirmation));
    }

    if ($params['allowed_ips'] && !is_string($params['allowed_ips'])) {
      throw new \InvalidArgumentException('Bad parameter: $allowed_ips must be of type string; received ' . gettype($allowed_ips));
    }

    if ($params['authenticate_until'] && !is_string($params['authenticate_until'])) {
      throw new \InvalidArgumentException('Bad parameter: $authenticate_until must be of type string; received ' . gettype($authenticate_until));
    }

    if ($params['authentication_method'] && !is_string($params['authentication_method'])) {
      throw new \InvalidArgumentException('Bad parameter: $authentication_method must be of type string; received ' . gettype($authentication_method));
    }

    if ($params['language'] && !is_string($params['language'])) {
      throw new \InvalidArgumentException('Bad parameter: $language must be of type string; received ' . gettype($language));
    }

    if ($params['notification_daily_send_time'] && !is_int($params['notification_daily_send_time'])) {
      throw new \InvalidArgumentException('Bad parameter: $notification_daily_send_time must be of type int; received ' . gettype($notification_daily_send_time));
    }

    if ($params['name'] && !is_string($params['name'])) {
      throw new \InvalidArgumentException('Bad parameter: $name must be of type string; received ' . gettype($name));
    }

    if ($params['notes'] && !is_string($params['notes'])) {
      throw new \InvalidArgumentException('Bad parameter: $notes must be of type string; received ' . gettype($notes));
    }

    if ($params['password_validity_days'] && !is_int($params['password_validity_days'])) {
      throw new \InvalidArgumentException('Bad parameter: $password_validity_days must be of type int; received ' . gettype($password_validity_days));
    }

    if ($params['ssl_required'] && !is_string($params['ssl_required'])) {
      throw new \InvalidArgumentException('Bad parameter: $ssl_required must be of type string; received ' . gettype($ssl_required));
    }

    if ($params['sso_strategy_id'] && !is_int($params['sso_strategy_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $sso_strategy_id must be of type int; received ' . gettype($sso_strategy_id));
    }

    if ($params['time_zone'] && !is_string($params['time_zone'])) {
      throw new \InvalidArgumentException('Bad parameter: $time_zone must be of type string; received ' . gettype($time_zone));
    }

    if ($params['user_root'] && !is_string($params['user_root'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_root must be of type string; received ' . gettype($user_root));
    }

    if ($params['username'] && !is_string($params['username'])) {
      throw new \InvalidArgumentException('Bad parameter: $username must be of type string; received ' . gettype($username));
    }

    $response = Api::sendRequest('/users', 'POST', $params);

    return new User((array)$response->data, $options);
  }
}
