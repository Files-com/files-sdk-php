<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An ExavaultApiRequestLog is an audit log for monitoring and troubleshooting requests to the ExaVault API.
 *
 * @property string|null $timestamp Start Time of Action. Deprecrated: Use created_at.
 * @property string|null $endpoint Name of API Endpoint
 * @property float|null $version Exavault API Version
 * @property string|null $request_ip IP of requesting client
 * @property string|null $request_method HTTP Method
 * @property string|null $error_type Error type, if applicable
 * @property string|null $error_message Error message, if applicable
 * @property string|null $user_agent User-Agent
 * @property int|null $response_code HTTP Response Code
 * @property bool|null $success `false` if HTTP Response Code is 4xx or 5xx
 * @property int|null $duration_ms Duration (in milliseconds)
 * @property string|null $created_at Start Time of Action
 * @package Files
 */
class ExavaultApiRequestLog
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * Start Time of Action. Deprecrated: Use created_at.
     *
     * @return string|null
     */
    public function getTimestamp()
    {
        return isset($this->attributes['timestamp']) ? $this->attributes['timestamp'] : null;
    }
    /**
     * Name of API Endpoint
     *
     * @return string|null
     */
    public function getEndpoint()
    {
        return isset($this->attributes['endpoint']) ? $this->attributes['endpoint'] : null;
    }
    /**
     * Exavault API Version
     *
     * @return float|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    /**
     * IP of requesting client
     *
     * @return string|null
     */
    public function getRequestIp()
    {
        return isset($this->attributes['request_ip']) ? $this->attributes['request_ip'] : null;
    }
    /**
     * HTTP Method
     *
     * @return string|null
     */
    public function getRequestMethod()
    {
        return isset($this->attributes['request_method']) ? $this->attributes['request_method'] : null;
    }
    /**
     * Error type, if applicable
     *
     * @return string|null
     */
    public function getErrorType()
    {
        return isset($this->attributes['error_type']) ? $this->attributes['error_type'] : null;
    }
    /**
     * Error message, if applicable
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return isset($this->attributes['error_message']) ? $this->attributes['error_message'] : null;
    }
    /**
     * User-Agent
     *
     * @return string|null
     */
    public function getUserAgent()
    {
        return isset($this->attributes['user_agent']) ? $this->attributes['user_agent'] : null;
    }
    /**
     * HTTP Response Code
     *
     * @return int|null
     */
    public function getResponseCode()
    {
        return isset($this->attributes['response_code']) ? $this->attributes['response_code'] : null;
    }
    /**
     * `false` if HTTP Response Code is 4xx or 5xx
     *
     * @return bool|null
     */
    public function getSuccess()
    {
        return isset($this->attributes['success']) ? $this->attributes['success'] : null;
    }
    /**
     * Duration (in milliseconds)
     *
     * @return int|null
     */
    public function getDurationMs()
    {
        return isset($this->attributes['duration_ms']) ? $this->attributes['duration_ms'] : null;
    }
    /**
     * Start Time of Action
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Exavault API Request Logs
     *
     * GET /exavault_api_request_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `request_ip`, `request_method`, `success` or `created_at`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `request_ip` and `request_method`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ request_ip ]`, `[ request_method ]`, `[ success ]`, `[ created_at ]`, `[ request_ip, request_method ]`, `[ request_ip, success ]`, `[ request_ip, created_at ]`, `[ request_method, success ]`, `[ request_method, created_at ]`, `[ success, created_at ]`, `[ request_ip, request_method, success ]`, `[ request_ip, request_method, created_at ]`, `[ request_ip, success, created_at ]`, `[ request_method, success, created_at ]` or `[ request_ip, request_method, success, created_at ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ExavaultApiRequestLog[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/exavault_api_request_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExavaultApiRequestLog((array) $obj, $options);
        }

        return $return_array;
    }
}
