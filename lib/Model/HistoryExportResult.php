<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class HistoryExportResult
 *
 * @package Files
 */
class HistoryExportResult
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
    // int64 # Action ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # When the action happened
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // string # When the action happened, in ISO8601 format.
    public function getCreatedAtIso8601()
    {
        return isset($this->attributes['created_at_iso8601']) ? $this->attributes['created_at_iso8601'] : null;
    }
    // int64 # User ID
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    // int64 # File ID related to the action
    public function getFileId()
    {
        return isset($this->attributes['file_id']) ? $this->attributes['file_id'] : null;
    }
    // int64 # ID of the parent folder
    public function getParentId()
    {
        return isset($this->attributes['parent_id']) ? $this->attributes['parent_id'] : null;
    }
    // string # Path of the related action. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    // string # Folder in which the action occurred
    public function getFolder()
    {
        return isset($this->attributes['folder']) ? $this->attributes['folder'] : null;
    }
    // string # File move originated from this path
    public function getSrc()
    {
        return isset($this->attributes['src']) ? $this->attributes['src'] : null;
    }
    // string # File moved to this destination folder
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    // string # Client IP that performed the action
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    // string # Username of the user that performed the action
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    // boolean # true if this change was performed by a user on a parent site.
    public function getUserIsFromParentSite()
    {
        return isset($this->attributes['user_is_from_parent_site']) ? $this->attributes['user_is_from_parent_site'] : null;
    }
    // string # What action was taken. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`, `archived_delete`
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    // string # The type of login failure, if applicable.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`, `country_mismatch`, `insecure_ftp`, `insecure_cipher`, `rate_limited`, `no_s3_compatible_endpoint_permission`, `mobile_ip_mismatch`, `mobile_api_key_not_used_quickly_enough`, `desktop_app_disabled`, `mobile_app_disabled`
    public function getFailureType()
    {
        return isset($this->attributes['failure_type']) ? $this->attributes['failure_type'] : null;
    }
    // string # Interface through which the action was taken. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`, `office`, `mobile`, `as2`, `inbound_email`, `remote`, `inbound_s3`
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }
    // int64 # ID of the object (such as Users, or API Keys) on which the action was taken
    public function getTargetId()
    {
        return isset($this->attributes['target_id']) ? $this->attributes['target_id'] : null;
    }
    // string # Name of the User, Group or other object with a name related to this action
    public function getTargetName()
    {
        return isset($this->attributes['target_name']) ? $this->attributes['target_name'] : null;
    }
    // string # Permission level of the action
    public function getTargetPermission()
    {
        return isset($this->attributes['target_permission']) ? $this->attributes['target_permission'] : null;
    }
    // boolean # Whether or not the action was recursive
    public function getTargetRecursive()
    {
        return isset($this->attributes['target_recursive']) ? $this->attributes['target_recursive'] : null;
    }
    // int64 # If searching for Histories about API keys, this is when the API key will expire. Represented as a Unix timestamp.
    public function getTargetExpiresAt()
    {
        return isset($this->attributes['target_expires_at']) ? $this->attributes['target_expires_at'] : null;
    }
    // string # If searching for Histories about API keys, this is when the API key will expire. Represented in ISO8601 format.
    public function getTargetExpiresAtIso8601()
    {
        return isset($this->attributes['target_expires_at_iso8601']) ? $this->attributes['target_expires_at_iso8601'] : null;
    }
    // string # If searching for Histories about API keys, this represents the permission set of the associated  API key
    public function getTargetPermissionSet()
    {
        return isset($this->attributes['target_permission_set']) ? $this->attributes['target_permission_set'] : null;
    }
    // string # If searching for Histories about API keys, this is the platform on which the action was taken
    public function getTargetPlatform()
    {
        return isset($this->attributes['target_platform']) ? $this->attributes['target_platform'] : null;
    }
    // string # If searching for Histories about API keys, this is the username on which the action was taken
    public function getTargetUsername()
    {
        return isset($this->attributes['target_username']) ? $this->attributes['target_username'] : null;
    }
    // int64 # If searching for Histories about API keys, this is the User ID on which the action was taken
    public function getTargetUserId()
    {
        return isset($this->attributes['target_user_id']) ? $this->attributes['target_user_id'] : null;
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   history_export_id (required) - int64 - ID of the associated history export.
    public static function all($params = [], $options = [])
    {
        if (empty($params['history_export_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: history_export_id');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['history_export_id']) && !is_int($params['history_export_id'])) {
            throw new \Files\Exception\InvalidParameterException('$history_export_id must be of type int; received ' . gettype(@$params['history_export_id']));
        }

        $response = Api::sendRequest('/history_export_results', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new HistoryExportResult((array) $obj, $options);
        }

        return $return_array;
    }
}
