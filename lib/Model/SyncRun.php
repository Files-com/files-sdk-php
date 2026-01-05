<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class SyncRun
 *
 * @package Files
 */
class SyncRun
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
    // int64 # SyncRun ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Log or summary body for this run
    public function getBody()
    {
        return @$this->attributes['body'];
    }
    // int64 # Total bytes synced in this run
    public function getBytesSynced()
    {
        return @$this->attributes['bytes_synced'];
    }
    // int64 # Number of files compared
    public function getComparedFiles()
    {
        return @$this->attributes['compared_files'];
    }
    // int64 # Number of folders compared
    public function getComparedFolders()
    {
        return @$this->attributes['compared_folders'];
    }
    // date-time # When this run was completed
    public function getCompletedAt()
    {
        return @$this->attributes['completed_at'];
    }
    // date-time # When this run was created
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # Destination remote server type, if any
    public function getDestRemoteServerType()
    {
        return @$this->attributes['dest_remote_server_type'];
    }
    // boolean # Whether this run was a dry run (no actual changes made)
    public function getDryRun()
    {
        return @$this->attributes['dry_run'];
    }
    // int64 # Number of files that errored
    public function getErroredFiles()
    {
        return @$this->attributes['errored_files'];
    }
    // int64 # Estimated bytes count for this run
    public function getEstimatedBytesCount()
    {
        return @$this->attributes['estimated_bytes_count'];
    }
    // array(string) # Array of errors encountered during the run
    public function getEventErrors()
    {
        return @$this->attributes['event_errors'];
    }
    // string # Link to external log file.
    public function getLogUrl()
    {
        return @$this->attributes['log_url'];
    }
    // double # Total runtime in seconds
    public function getRuntime()
    {
        return @$this->attributes['runtime'];
    }
    // int64 # Site ID
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }
    // int64 # Workspace ID
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }
    // string # Source remote server type, if any
    public function getSrcRemoteServerType()
    {
        return @$this->attributes['src_remote_server_type'];
    }
    // string # Status of the sync run (success, failure, partial_failure, in_progress, skipped)
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // int64 # Number of files successfully synced
    public function getSuccessfulFiles()
    {
        return @$this->attributes['successful_files'];
    }
    // int64 # ID of the Sync this run belongs to
    public function getSyncId()
    {
        return @$this->attributes['sync_id'];
    }
    // string # Name of the Sync this run belongs to
    public function getSyncName()
    {
        return @$this->attributes['sync_name'];
    }
    // date-time # When this run was last updated
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id`, `sync_id`, `created_at` or `status`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `status`, `dry_run`, `workspace_id`, `src_remote_server_type`, `dest_remote_server_type` or `sync_id`. Valid field combinations are `[ status, created_at ]`, `[ workspace_id, created_at ]`, `[ src_remote_server_type, created_at ]`, `[ dest_remote_server_type, created_at ]`, `[ sync_id, created_at ]`, `[ workspace_id, status ]`, `[ src_remote_server_type, status ]`, `[ dest_remote_server_type, status ]`, `[ sync_id, status ]`, `[ workspace_id, src_remote_server_type ]`, `[ workspace_id, dest_remote_server_type ]`, `[ workspace_id, sync_id ]`, `[ workspace_id, status, created_at ]`, `[ src_remote_server_type, status, created_at ]`, `[ dest_remote_server_type, status, created_at ]`, `[ sync_id, status, created_at ]`, `[ workspace_id, src_remote_server_type, created_at ]`, `[ workspace_id, dest_remote_server_type, created_at ]`, `[ workspace_id, sync_id, created_at ]`, `[ workspace_id, src_remote_server_type, status ]`, `[ workspace_id, dest_remote_server_type, status ]`, `[ workspace_id, sync_id, status ]`, `[ workspace_id, src_remote_server_type, status, created_at ]`, `[ workspace_id, dest_remote_server_type, status, created_at ]` or `[ workspace_id, sync_id, status, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/sync_runs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new SyncRun((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Sync Run ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/sync_runs/' . @$params['id'] . '', 'GET', $params, $options);

        return new SyncRun((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
