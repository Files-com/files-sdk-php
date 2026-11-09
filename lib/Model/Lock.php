<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Lock can be used by your custom-developed applications to implement file locking and concurrency features.
 *
 * @property string|null $path Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $timeout Lock timeout in seconds
 * @property string|null $depth
 * @property bool|null $recursive Does lock apply to subfolders?
 * @property string|null $owner Arbitrary descriptive label for the lock. Does not change the lock creator or permissions.
 * @property string|null $scope
 * @property bool|null $exclusive Is lock exclusive?
 * @property string|null $token Lock token. Use to release lock.
 * @property string|null $type
 * @property bool|null $allow_access_by_any_user Can lock be modified by users other than its creator?
 * @property int|null $user_id Lock creator user ID
 * @property string|null $username Lock creator username
 * @property string|null $expected_token Require this existing, unexpired token before refreshing or replacing a lock. Set token to the same value to refresh, or a different value to replace.
 * @package Files
 */
class Lock
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
     * Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Lock timeout in seconds
     *
     * @return int|null
     */
    public function getTimeout()
    {
        return isset($this->attributes['timeout']) ? $this->attributes['timeout'] : null;
    }

    /**
     * Lock timeout in seconds
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setTimeout($value)
    {
        return $this->attributes['timeout'] = $value;
    }
    /**
     * Read depth.
     *
     * @return string|null
     */
    public function getDepth()
    {
        return isset($this->attributes['depth']) ? $this->attributes['depth'] : null;
    }

    /**
     * Set depth locally.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDepth($value)
    {
        return $this->attributes['depth'] = $value;
    }
    /**
     * Does lock apply to subfolders?
     *
     * @return bool|null
     */
    public function getRecursive()
    {
        return isset($this->attributes['recursive']) ? $this->attributes['recursive'] : null;
    }

    /**
     * Does lock apply to subfolders?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    /**
     * Arbitrary descriptive label for the lock. Does not change the lock creator or permissions.
     *
     * @return string|null
     */
    public function getOwner()
    {
        return isset($this->attributes['owner']) ? $this->attributes['owner'] : null;
    }

    /**
     * Arbitrary descriptive label for the lock. Does not change the lock creator or permissions.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setOwner($value)
    {
        return $this->attributes['owner'] = $value;
    }
    /**
     * Read scope.
     *
     * @return string|null
     */
    public function getScope()
    {
        return isset($this->attributes['scope']) ? $this->attributes['scope'] : null;
    }

    /**
     * Set scope locally.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScope($value)
    {
        return $this->attributes['scope'] = $value;
    }
    /**
     * Is lock exclusive?
     *
     * @return bool|null
     */
    public function getExclusive()
    {
        return isset($this->attributes['exclusive']) ? $this->attributes['exclusive'] : null;
    }

    /**
     * Is lock exclusive?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setExclusive($value)
    {
        return $this->attributes['exclusive'] = $value;
    }
    /**
     * Lock token.  Use to release lock.
     *
     * @return string|null
     */
    public function getToken()
    {
        return isset($this->attributes['token']) ? $this->attributes['token'] : null;
    }

    /**
     * Lock token.  Use to release lock.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setToken($value)
    {
        return $this->attributes['token'] = $value;
    }
    /**
     * Read type.
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }

    /**
     * Set type locally.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setType($value)
    {
        return $this->attributes['type'] = $value;
    }
    /**
     * Can lock be modified by users other than its creator?
     *
     * @return bool|null
     */
    public function getAllowAccessByAnyUser()
    {
        return isset($this->attributes['allow_access_by_any_user']) ? $this->attributes['allow_access_by_any_user'] : null;
    }

    /**
     * Can lock be modified by users other than its creator?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAllowAccessByAnyUser($value)
    {
        return $this->attributes['allow_access_by_any_user'] = $value;
    }
    /**
     * Lock creator user ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * Lock creator user ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Lock creator username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Lock creator username
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * Require this existing, unexpired token before refreshing or replacing a lock. Set token to the same value to refresh, or a different value to replace.
     *
     * @return string|null
     */
    public function getExpectedToken()
    {
        return isset($this->attributes['expected_token']) ? $this->attributes['expected_token'] : null;
    }

    /**
     * Require this existing, unexpired token before refreshing or replacing a lock. Set token to the same value to refresh, or a different value to replace.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpectedToken($value)
    {
        return $this->attributes['expected_token'] = $value;
    }

    /**
     * Delete Lock
     *
     * DELETE /locks/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path
     *     - `token` (string, required): Lock token
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['token'])) {
            if (@$this->token) {
                $params['token'] = $this->token;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: token');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['token']) && !is_string($params['token'])) {
            throw new \Files\Exception\InvalidParameterException('$token must be of type string; received ' . gettype(@$params['token']));
        }

        $response = Api::sendRequest('/locks/' . rawurlencode(strval(@$params['path'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Lock
     *
     * DELETE /locks/{path}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path
     *     - `token` (string, required): Lock token
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
        $new_obj = self::create($this->path, $this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
    }


    /**
     * List Locks by Path
     *
     * GET /locks/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `include_children` (bool, optional): Include locks from children objects?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Lock[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listFor($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['include_children']) && !is_scalar($params['include_children'])) {
            throw new \Files\Exception\InvalidParameterException('$include_children must be of type boolean; received ' . gettype(@$params['include_children']));
        }

        $response = Api::sendRequest('/locks/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Lock(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of listFor()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listForPage($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['include_children']) && !is_scalar($params['include_children'])) {
            throw new \Files\Exception\InvalidParameterException('$include_children must be of type boolean; received ' . gettype(@$params['include_children']));
        }

        return \Files\Page::fetch('/locks/' . rawurlencode(strval(@$params['path'])) . '', $params, $options, Lock::class);
    }

    /**
     * Create Lock
     *
     * POST /locks/{path}
     *
     * @param string $path Path
     * @param array $params Request parameters.
     *     - `token` (string, optional): Lock token. With expected_token, use the same value to refresh or a different value to replace the existing token.
     *     - `expected_token` (string, optional): Require this existing, unexpired token before refreshing or replacing a lock. Set token to the same value to refresh, or a different value to replace.
     *     - `allow_access_by_any_user` (bool, optional): Can lock be modified by users other than its creator?
     *     - `exclusive` (bool, optional): Is lock exclusive?
     *     - `recursive` (bool, optional): Does lock apply to subfolders?
     *     - `owner` (string, optional): Arbitrary descriptive label for the lock. Does not change the lock creator or permissions.
     *     - `timeout` (int, optional): Lock timeout in seconds
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Lock
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['token']) && !is_string($params['token'])) {
            throw new \Files\Exception\InvalidParameterException('$token must be of type string; received ' . gettype(@$params['token']));
        }

        if (isset($params['expected_token']) && !is_string($params['expected_token'])) {
            throw new \Files\Exception\InvalidParameterException('$expected_token must be of type string; received ' . gettype(@$params['expected_token']));
        }

        if (isset($params['allow_access_by_any_user']) && !is_scalar($params['allow_access_by_any_user'])) {
            throw new \Files\Exception\InvalidParameterException('$allow_access_by_any_user must be of type boolean; received ' . gettype(@$params['allow_access_by_any_user']));
        }

        if (isset($params['exclusive']) && !is_scalar($params['exclusive'])) {
            throw new \Files\Exception\InvalidParameterException('$exclusive must be of type boolean; received ' . gettype(@$params['exclusive']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        if (isset($params['owner']) && !is_string($params['owner'])) {
            throw new \Files\Exception\InvalidParameterException('$owner must be of type string; received ' . gettype(@$params['owner']));
        }

        if (isset($params['timeout']) && !is_int($params['timeout'])) {
            throw new \Files\Exception\InvalidParameterException('$timeout must be of type int; received ' . gettype(@$params['timeout']));
        }

        $response = Api::sendRequest('/locks/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $options);

        return new Lock(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
