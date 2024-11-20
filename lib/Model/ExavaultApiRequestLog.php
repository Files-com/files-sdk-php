<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ExavaultApiRequestLog
 *
 * @package Files
 */
class ExavaultApiRequestLog
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
    // date-time # Start Time of Action
    public function getTimestamp()
    {
        return @$this->attributes['timestamp'];
    }
    // string # Name of API Endpoint
    public function getEndpoint()
    {
        return @$this->attributes['endpoint'];
    }
    // string # Exavault API Version
    public function getVersion()
    {
        return @$this->attributes['version'];
    }
    // string # IP of requesting client
    public function getRequestIp()
    {
        return @$this->attributes['request_ip'];
    }
    // string # HTTP Method
    public function getRequestMethod()
    {
        return @$this->attributes['request_method'];
    }
    // string # Error type, if applicable
    public function getErrorType()
    {
        return @$this->attributes['error_type'];
    }
    // string # Error message, if applicable
    public function getErrorMessage()
    {
        return @$this->attributes['error_message'];
    }
    // string # User-Agent
    public function getUserAgent()
    {
        return @$this->attributes['user_agent'];
    }
    // int64 # HTTP Response Code
    public function getResponseCode()
    {
        return @$this->attributes['response_code'];
    }
    // boolean # `false` if HTTP Response Code is 4xx or 5xx
    public function getSuccess()
    {
        return @$this->attributes['success'];
    }
    // int64 # Duration (in milliseconds)
    public function getDurationMs()
    {
        return @$this->attributes['duration_ms'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `request_ip`, `request_method` or `success`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, request_ip ]`, `[ start_date, request_method ]`, `[ start_date, success ]`, `[ end_date, request_ip ]`, `[ end_date, request_method ]`, `[ end_date, success ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_method, success ]`, `[ start_date, end_date, request_ip ]`, `[ start_date, end_date, request_method ]`, `[ start_date, end_date, success ]`, `[ start_date, request_ip, request_method ]`, `[ start_date, request_ip, success ]`, `[ start_date, request_method, success ]`, `[ end_date, request_ip, request_method ]`, `[ end_date, request_ip, success ]`, `[ end_date, request_method, success ]`, `[ request_ip, request_method, success ]`, `[ start_date, end_date, request_ip, request_method ]`, `[ start_date, end_date, request_ip, success ]`, `[ start_date, end_date, request_method, success ]`, `[ start_date, request_ip, request_method, success ]` or `[ end_date, request_ip, request_method, success ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `request_ip` and `request_method`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, request_ip ]`, `[ start_date, request_method ]`, `[ start_date, success ]`, `[ end_date, request_ip ]`, `[ end_date, request_method ]`, `[ end_date, success ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_method, success ]`, `[ start_date, end_date, request_ip ]`, `[ start_date, end_date, request_method ]`, `[ start_date, end_date, success ]`, `[ start_date, request_ip, request_method ]`, `[ start_date, request_ip, success ]`, `[ start_date, request_method, success ]`, `[ end_date, request_ip, request_method ]`, `[ end_date, request_ip, success ]`, `[ end_date, request_method, success ]`, `[ request_ip, request_method, success ]`, `[ start_date, end_date, request_ip, request_method ]`, `[ start_date, end_date, request_ip, success ]`, `[ start_date, end_date, request_method, success ]`, `[ start_date, request_ip, request_method, success ]` or `[ end_date, request_ip, request_method, success ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/exavault_api_request_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExavaultApiRequestLog((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `request_ip`, `request_method` or `success`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, request_ip ]`, `[ start_date, request_method ]`, `[ start_date, success ]`, `[ end_date, request_ip ]`, `[ end_date, request_method ]`, `[ end_date, success ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_method, success ]`, `[ start_date, end_date, request_ip ]`, `[ start_date, end_date, request_method ]`, `[ start_date, end_date, success ]`, `[ start_date, request_ip, request_method ]`, `[ start_date, request_ip, success ]`, `[ start_date, request_method, success ]`, `[ end_date, request_ip, request_method ]`, `[ end_date, request_ip, success ]`, `[ end_date, request_method, success ]`, `[ request_ip, request_method, success ]`, `[ start_date, end_date, request_ip, request_method ]`, `[ start_date, end_date, request_ip, success ]`, `[ start_date, end_date, request_method, success ]`, `[ start_date, request_ip, request_method, success ]` or `[ end_date, request_ip, request_method, success ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `request_ip` and `request_method`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, request_ip ]`, `[ start_date, request_method ]`, `[ start_date, success ]`, `[ end_date, request_ip ]`, `[ end_date, request_method ]`, `[ end_date, success ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_method, success ]`, `[ start_date, end_date, request_ip ]`, `[ start_date, end_date, request_method ]`, `[ start_date, end_date, success ]`, `[ start_date, request_ip, request_method ]`, `[ start_date, request_ip, success ]`, `[ start_date, request_method, success ]`, `[ end_date, request_ip, request_method ]`, `[ end_date, request_ip, success ]`, `[ end_date, request_method, success ]`, `[ request_ip, request_method, success ]`, `[ start_date, end_date, request_ip, request_method ]`, `[ start_date, end_date, request_ip, success ]`, `[ start_date, end_date, request_method, success ]`, `[ start_date, request_ip, request_method, success ]` or `[ end_date, request_ip, request_method, success ]`.
    public static function createExport($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/exavault_api_request_logs/create_export', 'POST', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }
}
