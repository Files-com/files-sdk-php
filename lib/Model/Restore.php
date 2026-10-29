<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Restore
 *
 * @package Files
 */
class Restore
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
    // date-time # Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
    public function getEarliestDate()
    {
        return isset($this->attributes['earliest_date']) ? $this->attributes['earliest_date'] : null;
    }

    public function setEarliestDate($value)
    {
        return $this->attributes['earliest_date'] = $value;
    }
    // int64 # Restore Record ID.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Number of directories that were successfully restored.
    public function getDirsRestored()
    {
        return isset($this->attributes['dirs_restored']) ? $this->attributes['dirs_restored'] : null;
    }

    public function setDirsRestored($value)
    {
        return $this->attributes['dirs_restored'] = $value;
    }
    // int64 # Number of directories that were not able to be restored.
    public function getDirsErrored()
    {
        return isset($this->attributes['dirs_errored']) ? $this->attributes['dirs_errored'] : null;
    }

    public function setDirsErrored($value)
    {
        return $this->attributes['dirs_errored'] = $value;
    }
    // int64 # Total number of directories processed.
    public function getDirsTotal()
    {
        return isset($this->attributes['dirs_total']) ? $this->attributes['dirs_total'] : null;
    }

    public function setDirsTotal($value)
    {
        return $this->attributes['dirs_total'] = $value;
    }
    // int64 # Number of files successfully restored.
    public function getFilesRestored()
    {
        return isset($this->attributes['files_restored']) ? $this->attributes['files_restored'] : null;
    }

    public function setFilesRestored($value)
    {
        return $this->attributes['files_restored'] = $value;
    }
    // int64 # Number of files that were not able to be restored.
    public function getFilesErrored()
    {
        return isset($this->attributes['files_errored']) ? $this->attributes['files_errored'] : null;
    }

    public function setFilesErrored($value)
    {
        return $this->attributes['files_errored'] = $value;
    }
    // int64 # Total number of files processed.
    public function getFilesTotal()
    {
        return isset($this->attributes['files_total']) ? $this->attributes['files_total'] : null;
    }

    public function setFilesTotal($value)
    {
        return $this->attributes['files_total'] = $value;
    }
    // string # Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
    public function getPrefix()
    {
        return isset($this->attributes['prefix']) ? $this->attributes['prefix'] : null;
    }

    public function setPrefix($value)
    {
        return $this->attributes['prefix'] = $value;
    }
    // string # Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted.
    public function getRestorationType()
    {
        return isset($this->attributes['restoration_type']) ? $this->attributes['restoration_type'] : null;
    }

    public function setRestorationType($value)
    {
        return $this->attributes['restoration_type'] = $value;
    }
    // boolean # If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
    public function getRestoreInPlace()
    {
        return isset($this->attributes['restore_in_place']) ? $this->attributes['restore_in_place'] : null;
    }

    public function setRestoreInPlace($value)
    {
        return $this->attributes['restore_in_place'] = $value;
    }
    // boolean # If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
    public function getRestoreDeletedPermissions()
    {
        return isset($this->attributes['restore_deleted_permissions']) ? $this->attributes['restore_deleted_permissions'] : null;
    }

    public function setRestoreDeletedPermissions($value)
    {
        return $this->attributes['restore_deleted_permissions'] = $value;
    }
    // int64 # Number of users successfully restored (only present for `restoration_type=users`).
    public function getUsersRestored()
    {
        return isset($this->attributes['users_restored']) ? $this->attributes['users_restored'] : null;
    }

    public function setUsersRestored($value)
    {
        return $this->attributes['users_restored'] = $value;
    }
    // int64 # Number of users that failed to restore (only present for `restoration_type=users`).
    public function getUsersErrored()
    {
        return isset($this->attributes['users_errored']) ? $this->attributes['users_errored'] : null;
    }

    public function setUsersErrored($value)
    {
        return $this->attributes['users_errored'] = $value;
    }
    // int64 # Total number of users processed (only present for `restoration_type=users`).
    public function getUsersTotal()
    {
        return isset($this->attributes['users_total']) ? $this->attributes['users_total'] : null;
    }

    public function setUsersTotal($value)
    {
        return $this->attributes['users_total'] = $value;
    }
    // int64 # Number of API keys restored (only present for `restoration_type=users`).
    public function getApiKeysRestored()
    {
        return isset($this->attributes['api_keys_restored']) ? $this->attributes['api_keys_restored'] : null;
    }

    public function setApiKeysRestored($value)
    {
        return $this->attributes['api_keys_restored'] = $value;
    }
    // int64 # Number of public keys restored (only present for `restoration_type=users`).
    public function getPublicKeysRestored()
    {
        return isset($this->attributes['public_keys_restored']) ? $this->attributes['public_keys_restored'] : null;
    }

    public function setPublicKeysRestored($value)
    {
        return $this->attributes['public_keys_restored'] = $value;
    }
    // int64 # Number of two factor authentication methods restored (only present for `restoration_type=users`).
    public function getTwoFactorAuthenticationMethodsRestored()
    {
        return isset($this->attributes['two_factor_authentication_methods_restored']) ? $this->attributes['two_factor_authentication_methods_restored'] : null;
    }

    public function setTwoFactorAuthenticationMethodsRestored($value)
    {
        return $this->attributes['two_factor_authentication_methods_restored'] = $value;
    }
    // string # Status of the restoration process.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // boolean # If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
    public function getUpdateTimestamps()
    {
        return isset($this->attributes['update_timestamps']) ? $this->attributes['update_timestamps'] : null;
    }

    public function setUpdateTimestamps($value)
    {
        return $this->attributes['update_timestamps'] = $value;
    }
    // int64 # Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // array(string) # Error messages received while restoring files and/or directories. Only present if there were errors.
    public function getErrorMessages()
    {
        return isset($this->attributes['error_messages']) ? $this->attributes['error_messages'] : null;
    }

    public function setErrorMessages($value)
    {
        return $this->attributes['error_messages'] = $value;
    }

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


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `restoration_type`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/restores', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Restore((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   earliest_date (required) - string - Restore files or users deleted on or after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
    //   prefix - string - Prefix of the files/folders to restore, or a case-insensitive username prefix for a user restore. A trailing slash selects that folder and its contents only; without it, the prefix matches any path beginning with that text. Do not use a leading slash. To restore all deleted items of the selected restoration type within the selected site or workspace scope, specify an empty string (`''`) or omit the field.
    //   restoration_type - string - Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated authentication records, plus permissions when `restore_deleted_permissions=true`. A 5-minute grace period allows for batch deletion processes: associated records deleted within the 5 minutes before the user are included, even if removed independently. Records deleted earlier, or before `earliest_date`, remain deleted.
    //   restore_deleted_permissions - boolean - If true, a user restore restores permissions deleted along with the selected users, including permissions removed independently within the 5 minutes before user deletion. Permissions deleted earlier, or before `earliest_date`, remain deleted. An in-place file restore restores user and group permissions on returned folders and their subfolders that were deleted from the time of the folder's deletion through 24 hours afterward, including permissions removed independently within that window. File restores into a new folder do not restore permissions.
    //   restore_in_place - boolean - If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
    //   update_timestamps - boolean - If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
    //   workspace_id - int64 - Workspace ID for a workspace-scoped restore. `0` means the default site-wide scope.
    public static function create($params = [], $options = [])
    {
        if (empty($params['earliest_date'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: earliest_date');
        }

        if (!empty($params['earliest_date']) && !is_string($params['earliest_date'])) {
            throw new \Files\Exception\InvalidParameterException('$earliest_date must be of type string; received ' . gettype(@$params['earliest_date']));
        }

        if (!empty($params['prefix']) && !is_string($params['prefix'])) {
            throw new \Files\Exception\InvalidParameterException('$prefix must be of type string; received ' . gettype(@$params['prefix']));
        }

        if (!empty($params['restoration_type']) && !is_string($params['restoration_type'])) {
            throw new \Files\Exception\InvalidParameterException('$restoration_type must be of type string; received ' . gettype(@$params['restoration_type']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/restores', 'POST', $params, $options);

        return new Restore((array) (@$response->data ?: []), $options);
    }
}
