<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class OutboundConnectionLog
 *
 * @package Files
 */
class OutboundConnectionLog
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
    // date-time # Start Time of Action
    public function getTimestamp()
    {
        return @$this->attributes['timestamp'];
    }
    // string # Remote Path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // string # End User IP
    public function getClientIp()
    {
        return @$this->attributes['client_ip'];
    }
    // string # Source Remote Server ID
    public function getSrcRemoteServerId()
    {
        return @$this->attributes['src_remote_server_id'];
    }
    // string # Destination Remote Server ID
    public function getDestRemoteServerId()
    {
        return @$this->attributes['dest_remote_server_id'];
    }
    // string # Operation Type
    public function getOperation()
    {
        return @$this->attributes['operation'];
    }
    // string # Error message, if applicable
    public function getErrorMessage()
    {
        return @$this->attributes['error_message'];
    }
    // string # Error operation, if applicable
    public function getErrorOperation()
    {
        return @$this->attributes['error_operation'];
    }
    // string # Error type, if applicable
    public function getErrorType()
    {
        return @$this->attributes['error_type'];
    }
    // string # Status
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // int64 # Duration (in milliseconds)
    public function getDurationMs()
    {
        return @$this->attributes['duration_ms'];
    }
    // int64 # Data Length in Bytes. Present for upload actions that transfer data.
    public function getBytesUploaded()
    {
        return @$this->attributes['bytes_uploaded'];
    }
    // int64 # Data Length in Bytes. Present for download actions that transfer data.
    public function getBytesDownloaded()
    {
        return @$this->attributes['bytes_downloaded'];
    }
    // int64 # Number of entries returned for a folder list action.
    public function getListCount()
    {
        return @$this->attributes['list_count'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   action - string
    //   page - int64
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `operation`, `status`, `src_remote_server_id`, `dest_remote_server_id`, `path` or `client_ip`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ start_date, end_date ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, src_remote_server_id ]`, `[ start_date, dest_remote_server_id ]`, `[ start_date, path ]`, `[ start_date, client_ip ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, src_remote_server_id ]`, `[ end_date, dest_remote_server_id ]`, `[ end_date, path ]`, `[ end_date, client_ip ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ path, client_ip ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, src_remote_server_id ]`, `[ start_date, end_date, dest_remote_server_id ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, client_ip ]`, `[ start_date, operation, status ]`, `[ start_date, operation, src_remote_server_id ]`, `[ start_date, operation, dest_remote_server_id ]`, `[ start_date, operation, path ]`, `[ start_date, operation, client_ip ]`, `[ start_date, status, src_remote_server_id ]`, `[ start_date, status, dest_remote_server_id ]`, `[ start_date, status, path ]`, `[ start_date, status, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, src_remote_server_id, path ]`, `[ start_date, src_remote_server_id, client_ip ]`, `[ start_date, dest_remote_server_id, path ]`, `[ start_date, dest_remote_server_id, client_ip ]`, `[ start_date, path, client_ip ]`, `[ end_date, operation, status ]`, `[ end_date, operation, src_remote_server_id ]`, `[ end_date, operation, dest_remote_server_id ]`, `[ end_date, operation, path ]`, `[ end_date, operation, client_ip ]`, `[ end_date, status, src_remote_server_id ]`, `[ end_date, status, dest_remote_server_id ]`, `[ end_date, status, path ]`, `[ end_date, status, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, src_remote_server_id, path ]`, `[ end_date, src_remote_server_id, client_ip ]`, `[ end_date, dest_remote_server_id, path ]`, `[ end_date, dest_remote_server_id, client_ip ]`, `[ end_date, path, client_ip ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, src_remote_server_id ]`, `[ start_date, end_date, operation, dest_remote_server_id ]`, `[ start_date, end_date, operation, path ]`, `[ start_date, end_date, operation, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id ]`, `[ start_date, end_date, status, dest_remote_server_id ]`, `[ start_date, end_date, status, path ]`, `[ start_date, end_date, status, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, src_remote_server_id, path ]`, `[ start_date, end_date, src_remote_server_id, client_ip ]`, `[ start_date, end_date, dest_remote_server_id, path ]`, `[ start_date, end_date, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id ]`, `[ start_date, operation, status, dest_remote_server_id ]`, `[ start_date, operation, status, path ]`, `[ start_date, operation, status, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, operation, src_remote_server_id, path ]`, `[ start_date, operation, src_remote_server_id, client_ip ]`, `[ start_date, operation, dest_remote_server_id, path ]`, `[ start_date, operation, dest_remote_server_id, client_ip ]`, `[ start_date, operation, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, status, src_remote_server_id, path ]`, `[ start_date, status, src_remote_server_id, client_ip ]`, `[ start_date, status, dest_remote_server_id, path ]`, `[ start_date, status, dest_remote_server_id, client_ip ]`, `[ start_date, status, path, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, src_remote_server_id, path, client_ip ]`, `[ start_date, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id ]`, `[ end_date, operation, status, dest_remote_server_id ]`, `[ end_date, operation, status, path ]`, `[ end_date, operation, status, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, operation, src_remote_server_id, path ]`, `[ end_date, operation, src_remote_server_id, client_ip ]`, `[ end_date, operation, dest_remote_server_id, path ]`, `[ end_date, operation, dest_remote_server_id, client_ip ]`, `[ end_date, operation, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, status, src_remote_server_id, path ]`, `[ end_date, status, src_remote_server_id, client_ip ]`, `[ end_date, status, dest_remote_server_id, path ]`, `[ end_date, status, dest_remote_server_id, client_ip ]`, `[ end_date, status, path, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, src_remote_server_id, path, client_ip ]`, `[ end_date, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id ]`, `[ start_date, end_date, operation, status, dest_remote_server_id ]`, `[ start_date, end_date, operation, status, path ]`, `[ start_date, end_date, operation, status, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, operation, src_remote_server_id, path ]`, `[ start_date, end_date, operation, src_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, status, src_remote_server_id, path ]`, `[ start_date, end_date, status, src_remote_server_id, client_ip ]`, `[ start_date, end_date, status, dest_remote_server_id, path ]`, `[ start_date, end_date, status, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, status, path, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, operation, status, src_remote_server_id, path ]`, `[ start_date, operation, status, src_remote_server_id, client_ip ]`, `[ start_date, operation, status, dest_remote_server_id, path ]`, `[ start_date, operation, status, dest_remote_server_id, client_ip ]`, `[ start_date, operation, status, path, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, operation, src_remote_server_id, path, client_ip ]`, `[ start_date, operation, dest_remote_server_id, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, status, src_remote_server_id, path, client_ip ]`, `[ start_date, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, operation, status, src_remote_server_id, path ]`, `[ end_date, operation, status, src_remote_server_id, client_ip ]`, `[ end_date, operation, status, dest_remote_server_id, path ]`, `[ end_date, operation, status, dest_remote_server_id, client_ip ]`, `[ end_date, operation, status, path, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, operation, src_remote_server_id, path, client_ip ]`, `[ end_date, operation, dest_remote_server_id, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, status, src_remote_server_id, path, client_ip ]`, `[ end_date, status, dest_remote_server_id, path, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, operation, status, src_remote_server_id, path ]`, `[ start_date, end_date, operation, status, src_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, path, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]` or `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `operation`, `status`, `src_remote_server_id`, `dest_remote_server_id` or `path`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ start_date, end_date ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, src_remote_server_id ]`, `[ start_date, dest_remote_server_id ]`, `[ start_date, path ]`, `[ start_date, client_ip ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, src_remote_server_id ]`, `[ end_date, dest_remote_server_id ]`, `[ end_date, path ]`, `[ end_date, client_ip ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ path, client_ip ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, src_remote_server_id ]`, `[ start_date, end_date, dest_remote_server_id ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, client_ip ]`, `[ start_date, operation, status ]`, `[ start_date, operation, src_remote_server_id ]`, `[ start_date, operation, dest_remote_server_id ]`, `[ start_date, operation, path ]`, `[ start_date, operation, client_ip ]`, `[ start_date, status, src_remote_server_id ]`, `[ start_date, status, dest_remote_server_id ]`, `[ start_date, status, path ]`, `[ start_date, status, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, src_remote_server_id, path ]`, `[ start_date, src_remote_server_id, client_ip ]`, `[ start_date, dest_remote_server_id, path ]`, `[ start_date, dest_remote_server_id, client_ip ]`, `[ start_date, path, client_ip ]`, `[ end_date, operation, status ]`, `[ end_date, operation, src_remote_server_id ]`, `[ end_date, operation, dest_remote_server_id ]`, `[ end_date, operation, path ]`, `[ end_date, operation, client_ip ]`, `[ end_date, status, src_remote_server_id ]`, `[ end_date, status, dest_remote_server_id ]`, `[ end_date, status, path ]`, `[ end_date, status, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, src_remote_server_id, path ]`, `[ end_date, src_remote_server_id, client_ip ]`, `[ end_date, dest_remote_server_id, path ]`, `[ end_date, dest_remote_server_id, client_ip ]`, `[ end_date, path, client_ip ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, src_remote_server_id ]`, `[ start_date, end_date, operation, dest_remote_server_id ]`, `[ start_date, end_date, operation, path ]`, `[ start_date, end_date, operation, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id ]`, `[ start_date, end_date, status, dest_remote_server_id ]`, `[ start_date, end_date, status, path ]`, `[ start_date, end_date, status, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, src_remote_server_id, path ]`, `[ start_date, end_date, src_remote_server_id, client_ip ]`, `[ start_date, end_date, dest_remote_server_id, path ]`, `[ start_date, end_date, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id ]`, `[ start_date, operation, status, dest_remote_server_id ]`, `[ start_date, operation, status, path ]`, `[ start_date, operation, status, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, operation, src_remote_server_id, path ]`, `[ start_date, operation, src_remote_server_id, client_ip ]`, `[ start_date, operation, dest_remote_server_id, path ]`, `[ start_date, operation, dest_remote_server_id, client_ip ]`, `[ start_date, operation, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, status, src_remote_server_id, path ]`, `[ start_date, status, src_remote_server_id, client_ip ]`, `[ start_date, status, dest_remote_server_id, path ]`, `[ start_date, status, dest_remote_server_id, client_ip ]`, `[ start_date, status, path, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, src_remote_server_id, path, client_ip ]`, `[ start_date, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id ]`, `[ end_date, operation, status, dest_remote_server_id ]`, `[ end_date, operation, status, path ]`, `[ end_date, operation, status, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, operation, src_remote_server_id, path ]`, `[ end_date, operation, src_remote_server_id, client_ip ]`, `[ end_date, operation, dest_remote_server_id, path ]`, `[ end_date, operation, dest_remote_server_id, client_ip ]`, `[ end_date, operation, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, status, src_remote_server_id, path ]`, `[ end_date, status, src_remote_server_id, client_ip ]`, `[ end_date, status, dest_remote_server_id, path ]`, `[ end_date, status, dest_remote_server_id, client_ip ]`, `[ end_date, status, path, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, src_remote_server_id, path, client_ip ]`, `[ end_date, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id ]`, `[ start_date, end_date, operation, status, dest_remote_server_id ]`, `[ start_date, end_date, operation, status, path ]`, `[ start_date, end_date, operation, status, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, operation, src_remote_server_id, path ]`, `[ start_date, end_date, operation, src_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, status, src_remote_server_id, path ]`, `[ start_date, end_date, status, src_remote_server_id, client_ip ]`, `[ start_date, end_date, status, dest_remote_server_id, path ]`, `[ start_date, end_date, status, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, status, path, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, operation, status, src_remote_server_id, path ]`, `[ start_date, operation, status, src_remote_server_id, client_ip ]`, `[ start_date, operation, status, dest_remote_server_id, path ]`, `[ start_date, operation, status, dest_remote_server_id, client_ip ]`, `[ start_date, operation, status, path, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, operation, src_remote_server_id, path, client_ip ]`, `[ start_date, operation, dest_remote_server_id, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, status, src_remote_server_id, path, client_ip ]`, `[ start_date, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ end_date, operation, status, src_remote_server_id, path ]`, `[ end_date, operation, status, src_remote_server_id, client_ip ]`, `[ end_date, operation, status, dest_remote_server_id, path ]`, `[ end_date, operation, status, dest_remote_server_id, client_ip ]`, `[ end_date, operation, status, path, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, operation, src_remote_server_id, path, client_ip ]`, `[ end_date, operation, dest_remote_server_id, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, status, src_remote_server_id, path, client_ip ]`, `[ end_date, status, dest_remote_server_id, path, client_ip ]`, `[ end_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ start_date, end_date, operation, status, src_remote_server_id, path ]`, `[ start_date, end_date, operation, status, src_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, path, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ end_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ end_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ end_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ end_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ start_date, end_date, operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ start_date, end_date, operation, status, src_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, status, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, end_date, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ start_date, operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]` or `[ end_date, operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`.
    public static function all($params = [], $options = [])
    {
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

        $response = Api::sendRequest('/outbound_connection_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new OutboundConnectionLog((array) $obj, $options);
        }

        return $return_array;
    }
}
