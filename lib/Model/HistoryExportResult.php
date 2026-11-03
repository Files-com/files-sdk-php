<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A HistoryExportResult is a single record with historical information about actions that happened in your site.
 *
 * @property int|null $id Action ID
 * @property int|null $created_at When the action happened
 * @property string|null $created_at_iso8601 When the action happened, in ISO8601 format.
 * @property int|null $user_id User ID
 * @property int|null $file_id File ID related to the action
 * @property int|null $parent_id ID of the parent folder
 * @property string|null $path Path of the related action. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $folder Folder in which the action occurred
 * @property string|null $src File move originated from this path
 * @property string|null $destination File moved to this destination folder
 * @property string|null $ip Client IP that performed the action
 * @property string|null $username Username of the user that performed the action
 * @property bool|null $user_is_from_parent_site true if this change was performed by a user on a parent site.
 * @property string|null $action What action was taken. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`, `archived_delete`
 * @property string|null $failure_type The type of login failure, if applicable. Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`, `country_mismatch`, `insecure_ftp`, `insecure_cipher`, `rate_limited`, `no_s3_compatible_endpoint_permission`, `mobile_ip_mismatch`, `mobile_api_key_not_used_quickly_enough`, `desktop_app_disabled`, `mobile_app_disabled`
 * @property string|null $interface Interface through which the action was taken. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`, `office`, `mobile`, `as2`, `inbound_email`, `remote`, `inbound_s3`
 * @property int|null $target_id ID of the object (such as Users, or API Keys) on which the action was taken
 * @property string|null $target_name Name of the User, Group or other object with a name related to this action
 * @property string|null $target_permission Permission level of the action
 * @property bool|null $target_recursive Whether or not the action was recursive
 * @property int|null $target_expires_at If searching for Histories about API keys, this is when the API key will expire. Represented as a Unix timestamp.
 * @property string|null $target_expires_at_iso8601 If searching for Histories about API keys, this is when the API key will expire. Represented in ISO8601 format.
 * @property string|null $target_permission_set If searching for Histories about API keys, this represents the permission set of the associated API key
 * @property string|null $target_platform If searching for Histories about API keys, this is the platform on which the action was taken
 * @property string|null $target_username If searching for Histories about API keys, this is the username on which the action was taken
 * @property int|null $target_user_id If searching for Histories about API keys, this is the User ID on which the action was taken
 * @package Files
 */
class HistoryExportResult
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
     * Action ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * When the action happened
     *
     * @return int|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * When the action happened, in ISO8601 format.
     *
     * @return string|null
     */
    public function getCreatedAtIso8601()
    {
        return isset($this->attributes['created_at_iso8601']) ? $this->attributes['created_at_iso8601'] : null;
    }
    /**
     * User ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * File ID related to the action
     *
     * @return int|null
     */
    public function getFileId()
    {
        return isset($this->attributes['file_id']) ? $this->attributes['file_id'] : null;
    }
    /**
     * ID of the parent folder
     *
     * @return int|null
     */
    public function getParentId()
    {
        return isset($this->attributes['parent_id']) ? $this->attributes['parent_id'] : null;
    }
    /**
     * Path of the related action. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Folder in which the action occurred
     *
     * @return string|null
     */
    public function getFolder()
    {
        return isset($this->attributes['folder']) ? $this->attributes['folder'] : null;
    }
    /**
     * File move originated from this path
     *
     * @return string|null
     */
    public function getSrc()
    {
        return isset($this->attributes['src']) ? $this->attributes['src'] : null;
    }
    /**
     * File moved to this destination folder
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    /**
     * Client IP that performed the action
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * Username of the user that performed the action
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    /**
     * true if this change was performed by a user on a parent site.
     *
     * @return bool|null
     */
    public function getUserIsFromParentSite()
    {
        return isset($this->attributes['user_is_from_parent_site']) ? $this->attributes['user_is_from_parent_site'] : null;
    }
    /**
     * What action was taken. Valid values: `create`, `read`, `update`, `destroy`, `move`, `login`, `failedlogin`, `copy`, `user_create`, `user_update`, `user_destroy`, `group_create`, `group_update`, `group_destroy`, `permission_create`, `permission_destroy`, `api_key_create`, `api_key_update`, `api_key_destroy`, `archived_delete`
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * The type of login failure, if applicable.  Valid values: `expired_trial`, `account_overdue`, `locked_out`, `ip_mismatch`, `password_mismatch`, `site_mismatch`, `username_not_found`, `none`, `no_ftp_permission`, `no_web_permission`, `no_directory`, `errno_enoent`, `no_sftp_permission`, `no_dav_permission`, `no_restapi_permission`, `key_mismatch`, `region_mismatch`, `expired_access`, `desktop_ip_mismatch`, `desktop_api_key_not_used_quickly_enough`, `disabled`, `country_mismatch`, `insecure_ftp`, `insecure_cipher`, `rate_limited`, `no_s3_compatible_endpoint_permission`, `mobile_ip_mismatch`, `mobile_api_key_not_used_quickly_enough`, `desktop_app_disabled`, `mobile_app_disabled`
     *
     * @return string|null
     */
    public function getFailureType()
    {
        return isset($this->attributes['failure_type']) ? $this->attributes['failure_type'] : null;
    }
    /**
     * Interface through which the action was taken. Valid values: `web`, `ftp`, `robot`, `jsapi`, `webdesktopapi`, `sftp`, `dav`, `desktop`, `restapi`, `scim`, `office`, `mobile`, `as2`, `inbound_email`, `remote`, `inbound_s3`
     *
     * @return string|null
     */
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }
    /**
     * ID of the object (such as Users, or API Keys) on which the action was taken
     *
     * @return int|null
     */
    public function getTargetId()
    {
        return isset($this->attributes['target_id']) ? $this->attributes['target_id'] : null;
    }
    /**
     * Name of the User, Group or other object with a name related to this action
     *
     * @return string|null
     */
    public function getTargetName()
    {
        return isset($this->attributes['target_name']) ? $this->attributes['target_name'] : null;
    }
    /**
     * Permission level of the action
     *
     * @return string|null
     */
    public function getTargetPermission()
    {
        return isset($this->attributes['target_permission']) ? $this->attributes['target_permission'] : null;
    }
    /**
     * Whether or not the action was recursive
     *
     * @return bool|null
     */
    public function getTargetRecursive()
    {
        return isset($this->attributes['target_recursive']) ? $this->attributes['target_recursive'] : null;
    }
    /**
     * If searching for Histories about API keys, this is when the API key will expire. Represented as a Unix timestamp.
     *
     * @return int|null
     */
    public function getTargetExpiresAt()
    {
        return isset($this->attributes['target_expires_at']) ? $this->attributes['target_expires_at'] : null;
    }
    /**
     * If searching for Histories about API keys, this is when the API key will expire. Represented in ISO8601 format.
     *
     * @return string|null
     */
    public function getTargetExpiresAtIso8601()
    {
        return isset($this->attributes['target_expires_at_iso8601']) ? $this->attributes['target_expires_at_iso8601'] : null;
    }
    /**
     * If searching for Histories about API keys, this represents the permission set of the associated  API key
     *
     * @return string|null
     */
    public function getTargetPermissionSet()
    {
        return isset($this->attributes['target_permission_set']) ? $this->attributes['target_permission_set'] : null;
    }
    /**
     * If searching for Histories about API keys, this is the platform on which the action was taken
     *
     * @return string|null
     */
    public function getTargetPlatform()
    {
        return isset($this->attributes['target_platform']) ? $this->attributes['target_platform'] : null;
    }
    /**
     * If searching for Histories about API keys, this is the username on which the action was taken
     *
     * @return string|null
     */
    public function getTargetUsername()
    {
        return isset($this->attributes['target_username']) ? $this->attributes['target_username'] : null;
    }
    /**
     * If searching for Histories about API keys, this is the User ID on which the action was taken
     *
     * @return int|null
     */
    public function getTargetUserId()
    {
        return isset($this->attributes['target_user_id']) ? $this->attributes['target_user_id'] : null;
    }

    /**
     * List History Export Results
     *
     * GET /history_export_results
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `history_export_id` (int, required): ID of the associated history export.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\HistoryExportResult[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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
