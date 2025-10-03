<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ScimLog
 *
 * @package Files
 */
class ScimLog
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
    // int64 # The unique ID of this SCIM request.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # The date and time when this SCIM request occurred.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # The path portion of the URL requested.
    public function getRequestPath()
    {
        return @$this->attributes['request_path'];
    }
    // string # The HTTP method used for this request.
    public function getRequestMethod()
    {
        return @$this->attributes['request_method'];
    }
    // string # The HTTP response code returned for this request.
    public function getHttpResponseCode()
    {
        return @$this->attributes['http_response_code'];
    }
    // string # The User-Agent header sent with the request.
    public function getUserAgent()
    {
        return @$this->attributes['user_agent'];
    }
    // string # The JSON payload sent with the request.
    public function getRequestJson()
    {
        return @$this->attributes['request_json'];
    }
    // string # The JSON payload returned in the response.
    public function getResponseJson()
    {
        return @$this->attributes['response_json'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/scim_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ScimLog((array) $obj, $options);
        }

        return $return_array;
    }
}
