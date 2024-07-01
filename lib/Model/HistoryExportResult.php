<?php

declare(strict_types=1);

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
        return @$this->attributes[$name];
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method($arguments);
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
        return @$this->attributes['id'];
    }
    // int64 # When the action happened
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # When the action happened, in ISO8601 format.
    public function getCreatedAtIso8601()
    {
        return @$this->attributes['created_at_iso8601'];
    }
    // int64 # User ID
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }
    // int64 # File ID related to the action
    public function getFileId()
    {
        return @$this->attributes['file_id'];
    }
    // int64 # ID of the parent folder
    public function getParentId()
    {
        return @$this->attributes['parent_id'];
    }
    // string # Path of the related action This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // string # Folder in which the action occurred
    public function getFolder()
    {
        return @$this->attributes['folder'];
    }
    // string # File move originated from this path
    public function getSrc()
    {
        return @$this->attributes['src'];
    }
    // string # File moved to this destination folder
    public function getDestination()
    {
        return @$this->attributes['destination'];
    }
    // string # Client IP that performed the action
    public function getIp()
    {
        return @$this->attributes['ip'];
    }
    // string # Username of the user that performed the action
    public function getUsername()
    {
        return @$this->attributes['username'];
    }
    // string # What action was taken. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`
    public function getAction()
    {
        return @$this->attributes['action'];
    }
    // string # The type of login failure, if applicable.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`, `country_mismatch`, `insecure_ftp`, `insecure_cipher`, `rate_limited`
    public function getFailureType()
    {
        return @$this->attributes['failure_type'];
    }
    // string # Inteface through which the action was taken. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`, `office`, `mobile`, `as2`, `inbound_email`, `remote`
    public function getInterface()
    {
        return @$this->attributes['interface'];
    }
    // int64 # ID of the object (such as Users, or API Keys) on which the action was taken
    public function getTargetId()
    {
        return @$this->attributes['target_id'];
    }
    // string # Name of the User, Group or other object with a name related to this action
    public function getTargetName()
    {
        return @$this->attributes['target_name'];
    }
    // string # Permission level of the action
    public function getTargetPermission()
    {
        return @$this->attributes['target_permission'];
    }
    // boolean # Whether or not the action was recursive
    public function getTargetRecursive()
    {
        return @$this->attributes['target_recursive'];
    }
    // int64 # If searching for Histories about API keys, this is when the API key will expire. Represented as a Unix timestamp.
    public function getTargetExpiresAt()
    {
        return @$this->attributes['target_expires_at'];
    }
    // string # If searching for Histories about API keys, this is when the API key will expire. Represented in ISO8601 format.
    public function getTargetExpiresAtIso8601()
    {
        return @$this->attributes['target_expires_at_iso8601'];
    }
    // string # If searching for Histories about API keys, this represents the permission set of the associated  API key
    public function getTargetPermissionSet()
    {
        return @$this->attributes['target_permission_set'];
    }
    // string # If searching for Histories about API keys, this is the platform on which the action was taken
    public function getTargetPlatform()
    {
        return @$this->attributes['target_platform'];
    }
    // string # If searching for Histories about API keys, this is the username on which the action was taken
    public function getTargetUsername()
    {
        return @$this->attributes['target_username'];
    }
    // int64 # If searching for Histories about API keys, this is the User ID on which the action was taken
    public function getTargetUserId()
    {
        return @$this->attributes['target_user_id'];
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   action - string
    //   page - int64
    //   history_export_id (required) - int64 - ID of the associated history export.
    public static function all($params = [], $options = [])
    {
        if (!@$params['history_export_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: history_export_id');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (@$params['page'] && !is_int(@$params['page'])) {
            throw new \Files\Exception\InvalidParameterException('$page must be of type int; received ' . gettype(@$params['page']));
        }

        if (@$params['history_export_id'] && !is_int(@$params['history_export_id'])) {
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
