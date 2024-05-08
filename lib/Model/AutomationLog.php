<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AutomationLog
 *
 * @package Files
 */
class AutomationLog
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
    // int64 # Automation ID
    public function getAutomationId()
    {
        return @$this->attributes['automation_id'];
    }
    // int64 # Automation Run ID
    public function getAutomationRunId()
    {
        return @$this->attributes['automation_run_id'];
    }
    // string # Destination path, for moves and copies
    public function getDestPath()
    {
        return @$this->attributes['dest_path'];
    }
    // string # Error type, if applicable
    public function getErrorType()
    {
        return @$this->attributes['error_type'];
    }
    // string # Message
    public function getMessage()
    {
        return @$this->attributes['message'];
    }
    // string # Operation type
    public function getOperation()
    {
        return @$this->attributes['operation'];
    }
    // string # File path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // string # Status
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `automation_id`, `automation_run_id`, `operation`, `path` or `status`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ automation_id ]`, `[ automation_run_id ]`, `[ operation ]`, `[ path ]`, `[ status ]`, `[ start_date, end_date ]`, `[ start_date, automation_id ]`, `[ start_date, automation_run_id ]`, `[ start_date, operation ]`, `[ start_date, path ]`, `[ start_date, status ]`, `[ end_date, automation_id ]`, `[ end_date, automation_run_id ]`, `[ end_date, operation ]`, `[ end_date, path ]`, `[ end_date, status ]`, `[ automation_id, automation_run_id ]`, `[ automation_id, operation ]`, `[ automation_id, path ]`, `[ automation_id, status ]`, `[ automation_run_id, operation ]`, `[ automation_run_id, path ]`, `[ automation_run_id, status ]`, `[ operation, path ]`, `[ operation, status ]`, `[ path, status ]`, `[ start_date, end_date, automation_id ]`, `[ start_date, end_date, automation_run_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, status ]`, `[ start_date, automation_id, automation_run_id ]`, `[ start_date, automation_id, operation ]`, `[ start_date, automation_id, path ]`, `[ start_date, automation_id, status ]`, `[ start_date, automation_run_id, operation ]`, `[ start_date, automation_run_id, path ]`, `[ start_date, automation_run_id, status ]`, `[ start_date, operation, path ]`, `[ start_date, operation, status ]`, `[ start_date, path, status ]`, `[ end_date, automation_id, automation_run_id ]`, `[ end_date, automation_id, operation ]`, `[ end_date, automation_id, path ]`, `[ end_date, automation_id, status ]`, `[ end_date, automation_run_id, operation ]`, `[ end_date, automation_run_id, path ]`, `[ end_date, automation_run_id, status ]`, `[ end_date, operation, path ]`, `[ end_date, operation, status ]`, `[ end_date, path, status ]`, `[ automation_id, automation_run_id, operation ]`, `[ automation_id, automation_run_id, path ]`, `[ automation_id, automation_run_id, status ]`, `[ automation_id, operation, path ]`, `[ automation_id, operation, status ]`, `[ automation_id, path, status ]`, `[ automation_run_id, operation, path ]`, `[ automation_run_id, operation, status ]`, `[ automation_run_id, path, status ]`, `[ operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id ]`, `[ start_date, end_date, automation_id, operation ]`, `[ start_date, end_date, automation_id, path ]`, `[ start_date, end_date, automation_id, status ]`, `[ start_date, end_date, automation_run_id, operation ]`, `[ start_date, end_date, automation_run_id, path ]`, `[ start_date, end_date, automation_run_id, status ]`, `[ start_date, end_date, operation, path ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, path, status ]`, `[ start_date, automation_id, automation_run_id, operation ]`, `[ start_date, automation_id, automation_run_id, path ]`, `[ start_date, automation_id, automation_run_id, status ]`, `[ start_date, automation_id, operation, path ]`, `[ start_date, automation_id, operation, status ]`, `[ start_date, automation_id, path, status ]`, `[ start_date, automation_run_id, operation, path ]`, `[ start_date, automation_run_id, operation, status ]`, `[ start_date, automation_run_id, path, status ]`, `[ start_date, operation, path, status ]`, `[ end_date, automation_id, automation_run_id, operation ]`, `[ end_date, automation_id, automation_run_id, path ]`, `[ end_date, automation_id, automation_run_id, status ]`, `[ end_date, automation_id, operation, path ]`, `[ end_date, automation_id, operation, status ]`, `[ end_date, automation_id, path, status ]`, `[ end_date, automation_run_id, operation, path ]`, `[ end_date, automation_run_id, operation, status ]`, `[ end_date, automation_run_id, path, status ]`, `[ end_date, operation, path, status ]`, `[ automation_id, automation_run_id, operation, path ]`, `[ automation_id, automation_run_id, operation, status ]`, `[ automation_id, automation_run_id, path, status ]`, `[ automation_id, operation, path, status ]`, `[ automation_run_id, operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id, operation ]`, `[ start_date, end_date, automation_id, automation_run_id, path ]`, `[ start_date, end_date, automation_id, automation_run_id, status ]`, `[ start_date, end_date, automation_id, operation, path ]`, `[ start_date, end_date, automation_id, operation, status ]`, `[ start_date, end_date, automation_id, path, status ]`, `[ start_date, end_date, automation_run_id, operation, path ]`, `[ start_date, end_date, automation_run_id, operation, status ]`, `[ start_date, end_date, automation_run_id, path, status ]`, `[ start_date, end_date, operation, path, status ]`, `[ start_date, automation_id, automation_run_id, operation, path ]`, `[ start_date, automation_id, automation_run_id, operation, status ]`, `[ start_date, automation_id, automation_run_id, path, status ]`, `[ start_date, automation_id, operation, path, status ]`, `[ start_date, automation_run_id, operation, path, status ]`, `[ end_date, automation_id, automation_run_id, operation, path ]`, `[ end_date, automation_id, automation_run_id, operation, status ]`, `[ end_date, automation_id, automation_run_id, path, status ]`, `[ end_date, automation_id, operation, path, status ]`, `[ end_date, automation_run_id, operation, path, status ]`, `[ automation_id, automation_run_id, operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id, operation, path ]`, `[ start_date, end_date, automation_id, automation_run_id, operation, status ]`, `[ start_date, end_date, automation_id, automation_run_id, path, status ]`, `[ start_date, end_date, automation_id, operation, path, status ]`, `[ start_date, end_date, automation_run_id, operation, path, status ]`, `[ start_date, automation_id, automation_run_id, operation, path, status ]` or `[ end_date, automation_id, automation_run_id, operation, path, status ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `operation`, `path` or `status`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ automation_id ]`, `[ automation_run_id ]`, `[ operation ]`, `[ path ]`, `[ status ]`, `[ start_date, end_date ]`, `[ start_date, automation_id ]`, `[ start_date, automation_run_id ]`, `[ start_date, operation ]`, `[ start_date, path ]`, `[ start_date, status ]`, `[ end_date, automation_id ]`, `[ end_date, automation_run_id ]`, `[ end_date, operation ]`, `[ end_date, path ]`, `[ end_date, status ]`, `[ automation_id, automation_run_id ]`, `[ automation_id, operation ]`, `[ automation_id, path ]`, `[ automation_id, status ]`, `[ automation_run_id, operation ]`, `[ automation_run_id, path ]`, `[ automation_run_id, status ]`, `[ operation, path ]`, `[ operation, status ]`, `[ path, status ]`, `[ start_date, end_date, automation_id ]`, `[ start_date, end_date, automation_run_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, status ]`, `[ start_date, automation_id, automation_run_id ]`, `[ start_date, automation_id, operation ]`, `[ start_date, automation_id, path ]`, `[ start_date, automation_id, status ]`, `[ start_date, automation_run_id, operation ]`, `[ start_date, automation_run_id, path ]`, `[ start_date, automation_run_id, status ]`, `[ start_date, operation, path ]`, `[ start_date, operation, status ]`, `[ start_date, path, status ]`, `[ end_date, automation_id, automation_run_id ]`, `[ end_date, automation_id, operation ]`, `[ end_date, automation_id, path ]`, `[ end_date, automation_id, status ]`, `[ end_date, automation_run_id, operation ]`, `[ end_date, automation_run_id, path ]`, `[ end_date, automation_run_id, status ]`, `[ end_date, operation, path ]`, `[ end_date, operation, status ]`, `[ end_date, path, status ]`, `[ automation_id, automation_run_id, operation ]`, `[ automation_id, automation_run_id, path ]`, `[ automation_id, automation_run_id, status ]`, `[ automation_id, operation, path ]`, `[ automation_id, operation, status ]`, `[ automation_id, path, status ]`, `[ automation_run_id, operation, path ]`, `[ automation_run_id, operation, status ]`, `[ automation_run_id, path, status ]`, `[ operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id ]`, `[ start_date, end_date, automation_id, operation ]`, `[ start_date, end_date, automation_id, path ]`, `[ start_date, end_date, automation_id, status ]`, `[ start_date, end_date, automation_run_id, operation ]`, `[ start_date, end_date, automation_run_id, path ]`, `[ start_date, end_date, automation_run_id, status ]`, `[ start_date, end_date, operation, path ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, path, status ]`, `[ start_date, automation_id, automation_run_id, operation ]`, `[ start_date, automation_id, automation_run_id, path ]`, `[ start_date, automation_id, automation_run_id, status ]`, `[ start_date, automation_id, operation, path ]`, `[ start_date, automation_id, operation, status ]`, `[ start_date, automation_id, path, status ]`, `[ start_date, automation_run_id, operation, path ]`, `[ start_date, automation_run_id, operation, status ]`, `[ start_date, automation_run_id, path, status ]`, `[ start_date, operation, path, status ]`, `[ end_date, automation_id, automation_run_id, operation ]`, `[ end_date, automation_id, automation_run_id, path ]`, `[ end_date, automation_id, automation_run_id, status ]`, `[ end_date, automation_id, operation, path ]`, `[ end_date, automation_id, operation, status ]`, `[ end_date, automation_id, path, status ]`, `[ end_date, automation_run_id, operation, path ]`, `[ end_date, automation_run_id, operation, status ]`, `[ end_date, automation_run_id, path, status ]`, `[ end_date, operation, path, status ]`, `[ automation_id, automation_run_id, operation, path ]`, `[ automation_id, automation_run_id, operation, status ]`, `[ automation_id, automation_run_id, path, status ]`, `[ automation_id, operation, path, status ]`, `[ automation_run_id, operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id, operation ]`, `[ start_date, end_date, automation_id, automation_run_id, path ]`, `[ start_date, end_date, automation_id, automation_run_id, status ]`, `[ start_date, end_date, automation_id, operation, path ]`, `[ start_date, end_date, automation_id, operation, status ]`, `[ start_date, end_date, automation_id, path, status ]`, `[ start_date, end_date, automation_run_id, operation, path ]`, `[ start_date, end_date, automation_run_id, operation, status ]`, `[ start_date, end_date, automation_run_id, path, status ]`, `[ start_date, end_date, operation, path, status ]`, `[ start_date, automation_id, automation_run_id, operation, path ]`, `[ start_date, automation_id, automation_run_id, operation, status ]`, `[ start_date, automation_id, automation_run_id, path, status ]`, `[ start_date, automation_id, operation, path, status ]`, `[ start_date, automation_run_id, operation, path, status ]`, `[ end_date, automation_id, automation_run_id, operation, path ]`, `[ end_date, automation_id, automation_run_id, operation, status ]`, `[ end_date, automation_id, automation_run_id, path, status ]`, `[ end_date, automation_id, operation, path, status ]`, `[ end_date, automation_run_id, operation, path, status ]`, `[ automation_id, automation_run_id, operation, path, status ]`, `[ start_date, end_date, automation_id, automation_run_id, operation, path ]`, `[ start_date, end_date, automation_id, automation_run_id, operation, status ]`, `[ start_date, end_date, automation_id, automation_run_id, path, status ]`, `[ start_date, end_date, automation_id, operation, path, status ]`, `[ start_date, end_date, automation_run_id, operation, path, status ]`, `[ start_date, automation_id, automation_run_id, operation, path, status ]` or `[ end_date, automation_id, automation_run_id, operation, path, status ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/automation_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AutomationLog((array) $obj, $options);
        }

        return $return_array;
    }
}
