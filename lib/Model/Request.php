<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Request is a file that *should* be uploaded by a specific user or group.
 *
 * @property int|null $id Request ID
 * @property string|null $path Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $source Source filename, if applicable
 * @property string|null $destination Destination filename
 * @property int|null $automation_id ID of automation that created request
 * @property string|null $user_display_name User making the request (if applicable)
 * @property string|null $user_ids A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
 * @property string|null $group_ids A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
 * @package Files
 */
class Request
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
     * Request ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Request ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Source filename, if applicable
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }

    /**
     * Source filename, if applicable
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    /**
     * Destination filename
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }

    /**
     * Destination filename
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestination($value)
    {
        return $this->attributes['destination'] = $value;
    }
    /**
     * ID of automation that created request
     *
     * @return int|null
     */
    public function getAutomationId()
    {
        return isset($this->attributes['automation_id']) ? $this->attributes['automation_id'] : null;
    }

    /**
     * ID of automation that created request
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAutomationId($value)
    {
        return $this->attributes['automation_id'] = $value;
    }
    /**
     * User making the request (if applicable)
     *
     * @return string|null
     */
    public function getUserDisplayName()
    {
        return isset($this->attributes['user_display_name']) ? $this->attributes['user_display_name'] : null;
    }

    /**
     * User making the request (if applicable)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserDisplayName($value)
    {
        return $this->attributes['user_display_name'] = $value;
    }
    /**
     * A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
     *
     * @return string|null
     */
    public function getUserIds()
    {
        return isset($this->attributes['user_ids']) ? $this->attributes['user_ids'] : null;
    }

    /**
     * A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }
    /**
     * A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
     *
     * @return string|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }

    /**
     * Delete Request
     *
     * DELETE /requests/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Request ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/requests/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Request
     *
     * DELETE /requests/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Request ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The Request object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Requests
     *
     * GET /requests
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     *     - `mine` (bool, optional): Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
     *     - `path` (string, optional): Path to show requests for.  If omitted, shows all paths. Send `/` to represent the root directory.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Request[]
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

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/requests', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Request((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * List Requests
     *
     * GET /requests/folders/{path}
     *
     * @param string $path Path to show requests for.  If omitted, shows all paths. Send `/` to represent the root directory.
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     *     - `mine` (bool, optional): Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Request[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function getFolder($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
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

        $response = Api::sendRequest('/requests/folders/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Request((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Create Request
     *
     * POST /requests
     *
     * @param array $params Request parameters.
     *     - `path` (string, required): Folder path on which to request the file.
     *     - `destination` (string, required): Destination filename (without extension) to request.
     *     - `user_ids` (string, optional): A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
     *     - `group_ids` (string, optional): A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Request
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (empty($params['destination'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (!empty($params['user_ids']) && !is_string($params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (!empty($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        $response = Api::sendRequest('/requests', 'POST', $params, $options);

        return new Request((array) (@$response->data ?: []), $options);
    }
}
