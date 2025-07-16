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
    // int64 # ID of the Sync this run belongs to
    public function getSyncId()
    {
        return @$this->attributes['sync_id'];
    }
    // int64 # Site ID
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }
    // string # Status of the sync run (success, failure, partial_failure, in_progress, skipped)
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Source remote server type, if any
    public function getSrcRemoteServerType()
    {
        return @$this->attributes['src_remote_server_type'];
    }
    // string # Destination remote server type, if any
    public function getDestRemoteServerType()
    {
        return @$this->attributes['dest_remote_server_type'];
    }
    // string # Log or summary body for this run
    public function getBody()
    {
        return @$this->attributes['body'];
    }
    // array(string) # Array of errors encountered during the run
    public function getEventErrors()
    {
        return @$this->attributes['event_errors'];
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
    // int64 # Number of files that errored
    public function getErroredFiles()
    {
        return @$this->attributes['errored_files'];
    }
    // int64 # Number of files successfully synced
    public function getSuccessfulFiles()
    {
        return @$this->attributes['successful_files'];
    }
    // double # Total runtime in seconds
    public function getRuntime()
    {
        return @$this->attributes['runtime'];
    }
    // string # Link to external log file.
    public function getLogUrl()
    {
        return @$this->attributes['log_url'];
    }
    // date-time # When this run was completed
    public function getCompletedAt()
    {
        return @$this->attributes['completed_at'];
    }
    // boolean # Whether notifications were sent for this run
    public function getNotified()
    {
        return @$this->attributes['notified'];
    }
    // date-time # When this run was created
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `sync_id`, `created_at` or `status`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `status` and `sync_id`. Valid field combinations are `[ sync_id, status ]`.
    //   sync_id (required) - int64 - ID of the Sync this run belongs to
    public static function all($params = [], $options = [])
    {
        if (!@$params['sync_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: sync_id');
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

        if (@$params['sync_id'] && !is_int(@$params['sync_id'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_id must be of type int; received ' . gettype(@$params['sync_id']));
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
