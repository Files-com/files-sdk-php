<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A PublicHostingRequestLog is an audit log for monitoring requests we received to access your publicly served folders.
 *
 * @property string|null $timestamp Start Time of Action. Deprecrated: Use created_at.
 * @property string|null $remote_ip IP Address of Public Hosting Client.
 * @property string|null $server_ip IP Address of Public Hosting Server.
 * @property string|null $hostname HTTP Request Hostname.
 * @property string|null $path HTTP Request Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $responseCode HTTP Response Code.
 * @property bool|null $success Whether SFTP Action was successful.
 * @property int|null $duration_ms Duration (in milliseconds).
 * @property string|null $created_at Start Time of Action.
 * @property int|null $bytes_transferred The number of bytes transferred for file downloads.
 * @property string|null $http_method Method of the HTTP call.
 * @package Files
 */
class PublicHostingRequestLog
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
     * IP Address of Public Hosting Client.
     *
     * @return string|null
     */
    public function getRemoteIp()
    {
        return isset($this->attributes['remote_ip']) ? $this->attributes['remote_ip'] : null;
    }
    /**
     * IP Address of Public Hosting Server.
     *
     * @return string|null
     */
    public function getServerIp()
    {
        return isset($this->attributes['server_ip']) ? $this->attributes['server_ip'] : null;
    }
    /**
     * HTTP Request Hostname.
     *
     * @return string|null
     */
    public function getHostname()
    {
        return isset($this->attributes['hostname']) ? $this->attributes['hostname'] : null;
    }
    /**
     * HTTP Request Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * HTTP Response Code.
     *
     * @return int|null
     */
    public function getResponseCode()
    {
        return isset($this->attributes['responseCode']) ? $this->attributes['responseCode'] : null;
    }
    /**
     * Whether SFTP Action was successful.
     *
     * @return bool|null
     */
    public function getSuccess()
    {
        return isset($this->attributes['success']) ? $this->attributes['success'] : null;
    }
    /**
     * Duration (in milliseconds).
     *
     * @return int|null
     */
    public function getDurationMs()
    {
        return isset($this->attributes['duration_ms']) ? $this->attributes['duration_ms'] : null;
    }
    /**
     * Start Time of Action.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * The number of bytes transferred for file downloads.
     *
     * @return int|null
     */
    public function getBytesTransferred()
    {
        return isset($this->attributes['bytes_transferred']) ? $this->attributes['bytes_transferred'] : null;
    }
    /**
     * Method of the HTTP call.
     *
     * @return string|null
     */
    public function getHttpMethod()
    {
        return isset($this->attributes['http_method']) ? $this->attributes['http_method'] : null;
    }

    /**
     * List Public Hosting Request Logs
     *
     * GET /public_hosting_request_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `remote_ip`, `success` or `created_at`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ path ]`, `[ remote_ip ]`, `[ success ]`, `[ created_at ]`, `[ path, remote_ip ]`, `[ path, success ]`, `[ path, created_at ]`, `[ remote_ip, success ]`, `[ remote_ip, created_at ]`, `[ success, created_at ]`, `[ path, remote_ip, success ]`, `[ path, remote_ip, created_at ]`, `[ path, success, created_at ]`, `[ remote_ip, success, created_at ]` or `[ path, remote_ip, success, created_at ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PublicHostingRequestLog[]
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

        $response = Api::sendRequest('/public_hosting_request_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PublicHostingRequestLog((array) $obj, $options);
        }

        return $return_array;
    }
}
