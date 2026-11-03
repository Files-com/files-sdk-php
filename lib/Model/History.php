<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An Action is a single record in our history log.
 *
 * @property int|null $id Action ID
 * @property string|null $path Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $when Action occurrence date/time
 * @property string|null $destination The destination path for this action, if applicable
 * @property string|null $display Friendly displayed output
 * @property string|null $ip IP Address that performed this action
 * @property string|null $source The source path for this action, if applicable
 * @property object|null $targets Targets
 * @property int|null $user_id User ID
 * @property string|null $username Username
 * @property bool|null $user_is_from_parent_site true if this change was performed by a user on a parent site.
 * @property string|null $action Type of action
 * @property string|null $failure_type Failure type. If action was a user login or session failure, why did it fail?
 * @property string|null $interface Interface on which this action occurred.
 * @package Files
 */
class History
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
        return !!@$this->attributes['path'];
    }
    /**
     * Action ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Action occurrence date/time
     *
     * @return string|null
     */
    public function getWhen()
    {
        return isset($this->attributes['when']) ? $this->attributes['when'] : null;
    }
    /**
     * The destination path for this action, if applicable
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    /**
     * Friendly displayed output
     *
     * @return string|null
     */
    public function getDisplay()
    {
        return isset($this->attributes['display']) ? $this->attributes['display'] : null;
    }
    /**
     * IP Address that performed this action
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * The source path for this action, if applicable
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }
    /**
     * Targets
     *
     * @return object|null
     */
    public function getTargets()
    {
        return isset($this->attributes['targets']) ? $this->attributes['targets'] : null;
    }
    /**
     * User ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * Username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    /**
     * true if this change was performed by a user on a parent site.
     *
     * @return bool|null
     */
    public function getUserIsFromParentSite()
    {
        return isset($this->attributes['user_is_from_parent_site']) ? $this->attributes['user_is_from_parent_site'] : null;
    }
    /**
     * Type of action
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * Failure type.  If action was a user login or session failure, why did it fail?
     *
     * @return string|null
     */
    public function getFailureType()
    {
        return isset($this->attributes['failure_type']) ? $this->attributes['failure_type'] : null;
    }
    /**
     * Interface on which this action occurred.
     *
     * @return string|null
     */
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }

    /**
     * List history for specific file
     *
     * GET /history/files/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `start_at` (string, optional): Leave blank or set to a date/time to filter earlier entries.
     *     - `end_at` (string, optional): Leave blank or set to a date/time to filter later entries.
     *     - `display` (string, optional): Display format. Leave blank or set to `full` or `parent`.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Action[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listForFile($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['display']) && !is_string($params['display'])) {
            throw new \Files\Exception\InvalidParameterException('$display must be of type string; received ' . gettype(@$params['display']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/history/files/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Action((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * List history for specific folder
     *
     * GET /history/folders/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `start_at` (string, optional): Leave blank or set to a date/time to filter earlier entries.
     *     - `end_at` (string, optional): Leave blank or set to a date/time to filter later entries.
     *     - `display` (string, optional): Display format. Leave blank or set to `full` or `parent`.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Action[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listForFolder($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['display']) && !is_string($params['display'])) {
            throw new \Files\Exception\InvalidParameterException('$display must be of type string; received ' . gettype(@$params['display']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/history/folders/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Action((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * List history for specific user
     *
     * GET /history/users/{user_id}
     *
     * @param int $user_id User ID.
     * @param array $params Request parameters.
     *     - `start_at` (string, optional): Leave blank or set to a date/time to filter earlier entries.
     *     - `end_at` (string, optional): Leave blank or set to a date/time to filter later entries.
     *     - `display` (string, optional): Display format. Leave blank or set to `full` or `parent`.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Action[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listForUser($user_id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['user_id'] = $user_id;

        if (empty($params['user_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: user_id');
        }

        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['display']) && !is_string($params['display'])) {
            throw new \Files\Exception\InvalidParameterException('$display must be of type string; received ' . gettype(@$params['display']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/history/users/' . rawurlencode(strval(@$params['user_id'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Action((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * List site login history
     *
     * GET /history/login
     *
     * @param array $params Request parameters.
     *     - `start_at` (string, optional): Leave blank or set to a date/time to filter earlier entries.
     *     - `end_at` (string, optional): Leave blank or set to a date/time to filter later entries.
     *     - `display` (string, optional): Display format. Leave blank or set to `full` or `parent`.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Action[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listLogins($params = [], $options = [])
    {
        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['display']) && !is_string($params['display'])) {
            throw new \Files\Exception\InvalidParameterException('$display must be of type string; received ' . gettype(@$params['display']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/history/login', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Action((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * List site full action history
     *
     * GET /history
     *
     * @param array $params Request parameters.
     *     - `start_at` (string, optional): Leave blank or set to a date/time to filter earlier entries.
     *     - `end_at` (string, optional): Leave blank or set to a date/time to filter later entries.
     *     - `display` (string, optional): Display format. Leave blank or set to `full` or `parent`.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `user_id`, `folder` or `path`. Valid field combinations are `[  ]`, `[ path ]`, `[ path ]` or `[ path ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Action[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['display']) && !is_string($params['display'])) {
            throw new \Files\Exception\InvalidParameterException('$display must be of type string; received ' . gettype(@$params['display']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/history', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Action((array) $obj, $options);
        }

        return $return_array;
    }
}
