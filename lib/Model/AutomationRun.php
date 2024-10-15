<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AutomationRun
 *
 * @package Files
 */
class AutomationRun
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
    // int64 # ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # ID of the associated Automation.
    public function getAutomationId()
    {
        return @$this->attributes['automation_id'];
    }
    // date-time # Automation run completion/failure date/time.
    public function getCompletedAt()
    {
        return @$this->attributes['completed_at'];
    }
    // date-time # Automation run start date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // double # Automation run runtime.
    public function getRuntime()
    {
        return @$this->attributes['runtime'];
    }
    // string # The success status of the AutomationRun. One of `running`, `success`, `partial_failure`, or `failure`.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // int64 # Count of successful operations.
    public function getSuccessfulOperations()
    {
        return @$this->attributes['successful_operations'];
    }
    // int64 # Count of failed operations.
    public function getFailedOperations()
    {
        return @$this->attributes['failed_operations'];
    }
    // string # Link to status messages log file.
    public function getStatusMessagesUrl()
    {
        return @$this->attributes['status_messages_url'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `automation_id`, `created_at` or `status`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `status` and `automation_id`. Valid field combinations are `[ status, automation_id ]`.
    //   automation_id (required) - int64 - ID of the associated Automation.
    public static function all($params = [], $options = [])
    {
        if (!@$params['automation_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['automation_id'] && !is_int(@$params['automation_id'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_id must be of type int; received ' . gettype(@$params['automation_id']));
        }

        $response = Api::sendRequest('/automation_runs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AutomationRun((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Automation Run ID.
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

        $response = Api::sendRequest('/automation_runs/' . @$params['id'] . '', 'GET', $params, $options);

        return new AutomationRun((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
