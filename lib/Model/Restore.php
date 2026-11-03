<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Restore kicks off a process to restore deleted data for your Site.
 *
 * @property string|null $earliest_date Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
 * @property int|null $id Restore Record ID.
 * @property int|null $dirs_restored Number of directories that were successfully restored.
 * @property int|null $dirs_errored Number of directories that were not able to be restored.
 * @property int|null $dirs_total Total number of directories processed.
 * @property int|null $files_restored Number of files successfully restored.
 * @property int|null $files_errored Number of files that were not able to be restored.
 * @property int|null $files_total Total number of files processed.
 * @property string|null $prefix Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
 * @property string|null $restoration_type Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted.
 * @property bool|null $restore_in_place If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
 * @property bool|null $restore_deleted_permissions If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
 * @property int|null $users_restored Number of users successfully restored (only present for `restoration_type=users`).
 * @property int|null $users_errored Number of users that failed to restore (only present for `restoration_type=users`).
 * @property int|null $users_total Total number of users processed (only present for `restoration_type=users`).
 * @property int|null $api_keys_restored Number of API keys restored (only present for `restoration_type=users`).
 * @property int|null $public_keys_restored Number of public keys restored (only present for `restoration_type=users`).
 * @property int|null $two_factor_authentication_methods_restored Number of two factor authentication methods restored (only present for `restoration_type=users`).
 * @property string|null $status Status of the restoration process.
 * @property bool|null $update_timestamps If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
 * @property int|null $workspace_id Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
 * @property string[]|null $error_messages Error messages received while restoring files and/or directories. Only present if there were errors.
 * @package Files
 */
class Restore
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
     * Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
     *
     * @return string|null
     */
    public function getEarliestDate()
    {
        return isset($this->attributes['earliest_date']) ? $this->attributes['earliest_date'] : null;
    }

    /**
     * Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEarliestDate($value)
    {
        return $this->attributes['earliest_date'] = $value;
    }
    /**
     * Restore Record ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Restore Record ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Number of directories that were successfully restored.
     *
     * @return int|null
     */
    public function getDirsRestored()
    {
        return isset($this->attributes['dirs_restored']) ? $this->attributes['dirs_restored'] : null;
    }

    /**
     * Number of directories that were successfully restored.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDirsRestored($value)
    {
        return $this->attributes['dirs_restored'] = $value;
    }
    /**
     * Number of directories that were not able to be restored.
     *
     * @return int|null
     */
    public function getDirsErrored()
    {
        return isset($this->attributes['dirs_errored']) ? $this->attributes['dirs_errored'] : null;
    }

    /**
     * Number of directories that were not able to be restored.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDirsErrored($value)
    {
        return $this->attributes['dirs_errored'] = $value;
    }
    /**
     * Total number of directories processed.
     *
     * @return int|null
     */
    public function getDirsTotal()
    {
        return isset($this->attributes['dirs_total']) ? $this->attributes['dirs_total'] : null;
    }

    /**
     * Total number of directories processed.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDirsTotal($value)
    {
        return $this->attributes['dirs_total'] = $value;
    }
    /**
     * Number of files successfully restored.
     *
     * @return int|null
     */
    public function getFilesRestored()
    {
        return isset($this->attributes['files_restored']) ? $this->attributes['files_restored'] : null;
    }

    /**
     * Number of files successfully restored.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFilesRestored($value)
    {
        return $this->attributes['files_restored'] = $value;
    }
    /**
     * Number of files that were not able to be restored.
     *
     * @return int|null
     */
    public function getFilesErrored()
    {
        return isset($this->attributes['files_errored']) ? $this->attributes['files_errored'] : null;
    }

    /**
     * Number of files that were not able to be restored.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFilesErrored($value)
    {
        return $this->attributes['files_errored'] = $value;
    }
    /**
     * Total number of files processed.
     *
     * @return int|null
     */
    public function getFilesTotal()
    {
        return isset($this->attributes['files_total']) ? $this->attributes['files_total'] : null;
    }

    /**
     * Total number of files processed.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFilesTotal($value)
    {
        return $this->attributes['files_total'] = $value;
    }
    /**
     * Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return isset($this->attributes['prefix']) ? $this->attributes['prefix'] : null;
    }

    /**
     * Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrefix($value)
    {
        return $this->attributes['prefix'] = $value;
    }
    /**
     * Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted.
     *
     * @return string|null
     */
    public function getRestorationType()
    {
        return isset($this->attributes['restoration_type']) ? $this->attributes['restoration_type'] : null;
    }

    /**
     * Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRestorationType($value)
    {
        return $this->attributes['restoration_type'] = $value;
    }
    /**
     * If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
     *
     * @return bool|null
     */
    public function getRestoreInPlace()
    {
        return isset($this->attributes['restore_in_place']) ? $this->attributes['restore_in_place'] : null;
    }

    /**
     * If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRestoreInPlace($value)
    {
        return $this->attributes['restore_in_place'] = $value;
    }
    /**
     * If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
     *
     * @return bool|null
     */
    public function getRestoreDeletedPermissions()
    {
        return isset($this->attributes['restore_deleted_permissions']) ? $this->attributes['restore_deleted_permissions'] : null;
    }

    /**
     * If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRestoreDeletedPermissions($value)
    {
        return $this->attributes['restore_deleted_permissions'] = $value;
    }
    /**
     * Number of users successfully restored (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getUsersRestored()
    {
        return isset($this->attributes['users_restored']) ? $this->attributes['users_restored'] : null;
    }

    /**
     * Number of users successfully restored (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUsersRestored($value)
    {
        return $this->attributes['users_restored'] = $value;
    }
    /**
     * Number of users that failed to restore (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getUsersErrored()
    {
        return isset($this->attributes['users_errored']) ? $this->attributes['users_errored'] : null;
    }

    /**
     * Number of users that failed to restore (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUsersErrored($value)
    {
        return $this->attributes['users_errored'] = $value;
    }
    /**
     * Total number of users processed (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getUsersTotal()
    {
        return isset($this->attributes['users_total']) ? $this->attributes['users_total'] : null;
    }

    /**
     * Total number of users processed (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUsersTotal($value)
    {
        return $this->attributes['users_total'] = $value;
    }
    /**
     * Number of API keys restored (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getApiKeysRestored()
    {
        return isset($this->attributes['api_keys_restored']) ? $this->attributes['api_keys_restored'] : null;
    }

    /**
     * Number of API keys restored (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setApiKeysRestored($value)
    {
        return $this->attributes['api_keys_restored'] = $value;
    }
    /**
     * Number of public keys restored (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getPublicKeysRestored()
    {
        return isset($this->attributes['public_keys_restored']) ? $this->attributes['public_keys_restored'] : null;
    }

    /**
     * Number of public keys restored (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPublicKeysRestored($value)
    {
        return $this->attributes['public_keys_restored'] = $value;
    }
    /**
     * Number of two factor authentication methods restored (only present for `restoration_type=users`).
     *
     * @return int|null
     */
    public function getTwoFactorAuthenticationMethodsRestored()
    {
        return isset($this->attributes['two_factor_authentication_methods_restored']) ? $this->attributes['two_factor_authentication_methods_restored'] : null;
    }

    /**
     * Number of two factor authentication methods restored (only present for `restoration_type=users`).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setTwoFactorAuthenticationMethodsRestored($value)
    {
        return $this->attributes['two_factor_authentication_methods_restored'] = $value;
    }
    /**
     * Status of the restoration process.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Status of the restoration process.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
     *
     * @return bool|null
     */
    public function getUpdateTimestamps()
    {
        return isset($this->attributes['update_timestamps']) ? $this->attributes['update_timestamps'] : null;
    }

    /**
     * If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUpdateTimestamps($value)
    {
        return $this->attributes['update_timestamps'] = $value;
    }
    /**
     * Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Error messages received while restoring files and/or directories. Only present if there were errors.
     *
     * @return string[]|null
     */
    public function getErrorMessages()
    {
        return isset($this->attributes['error_messages']) ? $this->attributes['error_messages'] : null;
    }

    /**
     * Error messages received while restoring files and/or directories. Only present if there were errors.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setErrorMessages($value)
    {
        return $this->attributes['error_messages'] = $value;
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
            throw new \Files\Exception\NotImplementedException('The Restore object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Restores
     *
     * GET /restores
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `restoration_type`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Restore[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/restores', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Restore(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/restores', $params, $options, Restore::class);
    }

    /**
     * Create Restore
     *
     * POST /restores
     *
     * @param array $params Request parameters.
     *     - `earliest_date` (string, required): Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
     *     - `prefix` (string, optional): Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
     *     - `restoration_type` (string, optional): Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted. Allowed values: files, users.
     *     - `restore_deleted_permissions` (bool, optional): If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
     *     - `restore_in_place` (bool, optional): If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
     *     - `update_timestamps` (bool, optional): If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
     *     - `workspace_id` (int, optional): Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Restore
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['earliest_date'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: earliest_date');
        }

        if (isset($params['earliest_date']) && !is_string($params['earliest_date'])) {
            throw new \Files\Exception\InvalidParameterException('$earliest_date must be of type string; received ' . gettype(@$params['earliest_date']));
        }

        if (isset($params['prefix']) && !is_string($params['prefix'])) {
            throw new \Files\Exception\InvalidParameterException('$prefix must be of type string; received ' . gettype(@$params['prefix']));
        }

        if (isset($params['restoration_type']) && !is_string($params['restoration_type'])) {
            throw new \Files\Exception\InvalidParameterException('$restoration_type must be of type string; received ' . gettype(@$params['restoration_type']));
        }

        if (isset($params['restore_deleted_permissions']) && !is_scalar($params['restore_deleted_permissions'])) {
            throw new \Files\Exception\InvalidParameterException('$restore_deleted_permissions must be of type boolean; received ' . gettype(@$params['restore_deleted_permissions']));
        }

        if (isset($params['restore_in_place']) && !is_scalar($params['restore_in_place'])) {
            throw new \Files\Exception\InvalidParameterException('$restore_in_place must be of type boolean; received ' . gettype(@$params['restore_in_place']));
        }

        if (isset($params['update_timestamps']) && !is_scalar($params['update_timestamps'])) {
            throw new \Files\Exception\InvalidParameterException('$update_timestamps must be of type boolean; received ' . gettype(@$params['update_timestamps']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/restores', 'POST', $params, $options);

        return new Restore(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
