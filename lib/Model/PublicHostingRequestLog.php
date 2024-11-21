<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PublicHostingRequestLog
 *
 * @package Files
 */
class PublicHostingRequestLog
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
    // string # IP Address of Public Hosting Client
    public function getRemoteIp()
    {
        return @$this->attributes['remote_ip'];
    }
    // string # IP Address of Public Hosting Server
    public function getServerIp()
    {
        return @$this->attributes['server_ip'];
    }
    // string # HTTP Request Hostname
    public function getHostname()
    {
        return @$this->attributes['hostname'];
    }
    // string # HTTP Request Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // int64 # HTTP Response Code
    public function getResponseCode()
    {
        return @$this->attributes['responseCode'];
    }
    // boolean # Whether SFTP Action was successful.
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
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `path`, `remote_ip` or `success`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, path ]`, `[ start_date, remote_ip ]`, `[ start_date, success ]`, `[ end_date, path ]`, `[ end_date, remote_ip ]`, `[ end_date, success ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ remote_ip, success ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, remote_ip ]`, `[ start_date, end_date, success ]`, `[ start_date, path, remote_ip ]`, `[ start_date, path, success ]`, `[ start_date, remote_ip, success ]`, `[ end_date, path, remote_ip ]`, `[ end_date, path, success ]`, `[ end_date, remote_ip, success ]`, `[ path, remote_ip, success ]`, `[ start_date, end_date, path, remote_ip ]`, `[ start_date, end_date, path, success ]`, `[ start_date, end_date, remote_ip, success ]`, `[ start_date, path, remote_ip, success ]` or `[ end_date, path, remote_ip, success ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, path ]`, `[ start_date, remote_ip ]`, `[ start_date, success ]`, `[ end_date, path ]`, `[ end_date, remote_ip ]`, `[ end_date, success ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ remote_ip, success ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, remote_ip ]`, `[ start_date, end_date, success ]`, `[ start_date, path, remote_ip ]`, `[ start_date, path, success ]`, `[ start_date, remote_ip, success ]`, `[ end_date, path, remote_ip ]`, `[ end_date, path, success ]`, `[ end_date, remote_ip, success ]`, `[ path, remote_ip, success ]`, `[ start_date, end_date, path, remote_ip ]`, `[ start_date, end_date, path, success ]`, `[ start_date, end_date, remote_ip, success ]`, `[ start_date, path, remote_ip, success ]` or `[ end_date, path, remote_ip, success ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/public_hosting_request_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PublicHostingRequestLog((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `path`, `remote_ip` or `success`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, path ]`, `[ start_date, remote_ip ]`, `[ start_date, success ]`, `[ end_date, path ]`, `[ end_date, remote_ip ]`, `[ end_date, success ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ remote_ip, success ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, remote_ip ]`, `[ start_date, end_date, success ]`, `[ start_date, path, remote_ip ]`, `[ start_date, path, success ]`, `[ start_date, remote_ip, success ]`, `[ end_date, path, remote_ip ]`, `[ end_date, path, success ]`, `[ end_date, remote_ip, success ]`, `[ path, remote_ip, success ]`, `[ start_date, end_date, path, remote_ip ]`, `[ start_date, end_date, path, success ]`, `[ start_date, end_date, remote_ip, success ]`, `[ start_date, path, remote_ip, success ]` or `[ end_date, path, remote_ip, success ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ start_date, end_date ]`, `[ start_date, path ]`, `[ start_date, remote_ip ]`, `[ start_date, success ]`, `[ end_date, path ]`, `[ end_date, remote_ip ]`, `[ end_date, success ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ remote_ip, success ]`, `[ start_date, end_date, path ]`, `[ start_date, end_date, remote_ip ]`, `[ start_date, end_date, success ]`, `[ start_date, path, remote_ip ]`, `[ start_date, path, success ]`, `[ start_date, remote_ip, success ]`, `[ end_date, path, remote_ip ]`, `[ end_date, path, success ]`, `[ end_date, remote_ip, success ]`, `[ path, remote_ip, success ]`, `[ start_date, end_date, path, remote_ip ]`, `[ start_date, end_date, path, success ]`, `[ start_date, end_date, remote_ip, success ]`, `[ start_date, path, remote_ip, success ]` or `[ end_date, path, remote_ip, success ]`.
    public static function createExport($params = [], $options = [])
    {
        $response = Api::sendRequest('/public_hosting_request_logs/create_export', 'POST', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }
}
