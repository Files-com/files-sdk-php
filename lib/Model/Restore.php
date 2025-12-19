<?php

declare(strict_types=1);

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
        return @$this->attributes[$name];
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
    // date-time # Restore all files deleted after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
    public function getEarliestDate()
    {
        return @$this->attributes['earliest_date'];
    }

    public function setEarliestDate($value)
    {
        return $this->attributes['earliest_date'] = $value;
    }
    // int64 # Restore Record ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Number of directories that were successfully restored.
    public function getDirsRestored()
    {
        return @$this->attributes['dirs_restored'];
    }

    public function setDirsRestored($value)
    {
        return $this->attributes['dirs_restored'] = $value;
    }
    // int64 # Number of directories that were not able to be restored.
    public function getDirsErrored()
    {
        return @$this->attributes['dirs_errored'];
    }

    public function setDirsErrored($value)
    {
        return $this->attributes['dirs_errored'] = $value;
    }
    // int64 # Total number of directories processed.
    public function getDirsTotal()
    {
        return @$this->attributes['dirs_total'];
    }

    public function setDirsTotal($value)
    {
        return $this->attributes['dirs_total'] = $value;
    }
    // int64 # Number of files successfully restored.
    public function getFilesRestored()
    {
        return @$this->attributes['files_restored'];
    }

    public function setFilesRestored($value)
    {
        return $this->attributes['files_restored'] = $value;
    }
    // int64 # Number of files that were not able to be restored.
    public function getFilesErrored()
    {
        return @$this->attributes['files_errored'];
    }

    public function setFilesErrored($value)
    {
        return $this->attributes['files_errored'] = $value;
    }
    // int64 # Total number of files processed.
    public function getFilesTotal()
    {
        return @$this->attributes['files_total'];
    }

    public function setFilesTotal($value)
    {
        return $this->attributes['files_total'] = $value;
    }
    // string # Prefix of the files/folders to restore. To restore a folder, add a trailing slash to the folder name. Do not use a leading slash. To restore all deleted items, specify an empty string (`''`) in the prefix field or omit the field from the request.
    public function getPrefix()
    {
        return @$this->attributes['prefix'];
    }

    public function setPrefix($value)
    {
        return $this->attributes['prefix'] = $value;
    }
    // string # Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated access/authentication records.
    public function getRestorationType()
    {
        return @$this->attributes['restoration_type'];
    }

    public function setRestorationType($value)
    {
        return $this->attributes['restoration_type'] = $value;
    }
    // boolean # If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
    public function getRestoreInPlace()
    {
        return @$this->attributes['restore_in_place'];
    }

    public function setRestoreInPlace($value)
    {
        return $this->attributes['restore_in_place'] = $value;
    }
    // boolean # If true, we will also restore any Permissions that match the same path prefix from the same dates.
    public function getRestoreDeletedPermissions()
    {
        return @$this->attributes['restore_deleted_permissions'];
    }

    public function setRestoreDeletedPermissions($value)
    {
        return $this->attributes['restore_deleted_permissions'] = $value;
    }
    // int64 # Number of users successfully restored (only present for `restoration_type=users`).
    public function getUsersRestored()
    {
        return @$this->attributes['users_restored'];
    }

    public function setUsersRestored($value)
    {
        return $this->attributes['users_restored'] = $value;
    }
    // int64 # Number of users that failed to restore (only present for `restoration_type=users`).
    public function getUsersErrored()
    {
        return @$this->attributes['users_errored'];
    }

    public function setUsersErrored($value)
    {
        return $this->attributes['users_errored'] = $value;
    }
    // int64 # Total number of users processed (only present for `restoration_type=users`).
    public function getUsersTotal()
    {
        return @$this->attributes['users_total'];
    }

    public function setUsersTotal($value)
    {
        return $this->attributes['users_total'] = $value;
    }
    // int64 # Number of API keys restored (only present for `restoration_type=users`).
    public function getApiKeysRestored()
    {
        return @$this->attributes['api_keys_restored'];
    }

    public function setApiKeysRestored($value)
    {
        return $this->attributes['api_keys_restored'] = $value;
    }
    // int64 # Number of public keys restored (only present for `restoration_type=users`).
    public function getPublicKeysRestored()
    {
        return @$this->attributes['public_keys_restored'];
    }

    public function setPublicKeysRestored($value)
    {
        return $this->attributes['public_keys_restored'] = $value;
    }
    // int64 # Number of two factor authentication methods restored (only present for `restoration_type=users`).
    public function getTwoFactorAuthenticationMethodsRestored()
    {
        return @$this->attributes['two_factor_authentication_methods_restored'];
    }

    public function setTwoFactorAuthenticationMethodsRestored($value)
    {
        return $this->attributes['two_factor_authentication_methods_restored'] = $value;
    }
    // string # Status of the restoration process.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // boolean # If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
    public function getUpdateTimestamps()
    {
        return @$this->attributes['update_timestamps'];
    }

    public function setUpdateTimestamps($value)
    {
        return $this->attributes['update_timestamps'] = $value;
    }
    // array(string) # Error messages received while restoring files and/or directories. Only present if there were errors.
    public function getErrorMessages()
    {
        return @$this->attributes['error_messages'];
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
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
    //   earliest_date (required) - string - Restore all files deleted after this date/time. Don't set this earlier than you need. Can not be greater than 365 days prior to the restore request.
    //   prefix - string - Prefix of the files/folders to restore. To restore a folder, add a trailing slash to the folder name. Do not use a leading slash. To restore all deleted items, specify an empty string (`''`) in the prefix field or omit the field from the request.
    //   restoration_type - string - Type of restoration to perform. `files` restores deleted filesystem items. `users` restores deleted users and associated access/authentication records.
    //   restore_deleted_permissions - boolean - If true, we will also restore any Permissions that match the same path prefix from the same dates.
    //   restore_in_place - boolean - If true, we will restore the files in place (into their original paths). If false, we will create a new restoration folder in the root and restore files there.
    //   update_timestamps - boolean - If true, we will update the last modified timestamp of restored files to today's date. If false, we might trigger File Expiration to delete the file again.
    public static function create($params = [], $options = [])
    {
        if (!@$params['earliest_date']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: earliest_date');
        }

        if (@$params['earliest_date'] && !is_string(@$params['earliest_date'])) {
            throw new \Files\Exception\InvalidParameterException('$earliest_date must be of type string; received ' . gettype(@$params['earliest_date']));
        }

        if (@$params['prefix'] && !is_string(@$params['prefix'])) {
            throw new \Files\Exception\InvalidParameterException('$prefix must be of type string; received ' . gettype(@$params['prefix']));
        }

        if (@$params['restoration_type'] && !is_string(@$params['restoration_type'])) {
            throw new \Files\Exception\InvalidParameterException('$restoration_type must be of type string; received ' . gettype(@$params['restoration_type']));
        }

        $response = Api::sendRequest('/restores', 'POST', $params, $options);

        return new Restore((array) (@$response->data ?: []), $options);
    }
}
