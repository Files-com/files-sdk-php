<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A GroupUser is a record about membership of a User within a Group.
 *
 * @property string|null $group_name Group name
 * @property int|null $group_id Group ID
 * @property int|null $user_id User ID
 * @property bool|null $admin Is this user an administrator of this group?
 * @property string|null $username Username of the user
 * @property int|null $id Group User ID.
 * @package Files
 */
class GroupUser
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
     * Group name
     *
     * @return string|null
     */
    public function getGroupName()
    {
        return isset($this->attributes['group_name']) ? $this->attributes['group_name'] : null;
    }

    /**
     * Group name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGroupName($value)
    {
        return $this->attributes['group_name'] = $value;
    }
    /**
     * Group ID
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return isset($this->attributes['group_id']) ? $this->attributes['group_id'] : null;
    }

    /**
     * Group ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setGroupId($value)
    {
        return $this->attributes['group_id'] = $value;
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
     * User ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Is this user an administrator of this group?
     *
     * @return bool|null
     */
    public function getAdmin()
    {
        return isset($this->attributes['admin']) ? $this->attributes['admin'] : null;
    }

    /**
     * Is this user an administrator of this group?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAdmin($value)
    {
        return $this->attributes['admin'] = $value;
    }
    /**
     * Username of the user
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Username of the user
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * Group User ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Group User ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }

    /**
     * Update Group User
     *
     * PATCH /group_users/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group User ID.
     *     - `group_id` (int, required): Group ID to add user to.
     *     - `user_id` (int, required): User ID to add to group.
     *     - `admin` (bool, optional): Is the user a group administrator?
     * @return \Files\Model\GroupUser
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
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

        if (empty($params['group_id'])) {
            if (@$this->group_id) {
                $params['group_id'] = $this->group_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: group_id');
            }
        }

        if (empty($params['user_id'])) {
            if (@$this->user_id) {
                $params['user_id'] = $this->user_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: user_id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/group_users/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new GroupUser((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Group User
     *
     * DELETE /group_users/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group User ID.
     *     - `group_id` (int, required): Group ID from which to remove user.
     *     - `user_id` (int, required): User ID to remove from group.
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

        if (empty($params['group_id'])) {
            if (@$this->group_id) {
                $params['group_id'] = $this->group_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: group_id');
            }
        }

        if (empty($params['user_id'])) {
            if (@$this->user_id) {
                $params['user_id'] = $this->user_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: user_id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/group_users/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Group User
     *
     * DELETE /group_users/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group User ID.
     *     - `group_id` (int, required): Group ID from which to remove user.
     *     - `user_id` (int, required): User ID to remove from group.
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
            $new_obj = $this->update($this->attributes);
            $this->attributes = $new_obj->attributes;
            return true;
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Group Users
     *
     * GET /group_users
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `group_id` (int, optional): Group ID. If provided, returns memberships of this group. Requires a Site Administrator, a Read-only Administrator, a Workspace Administrator for the group's workspace, or a Group Administrator of this group.
     *     - `user_id` (int, optional): User ID.  If provided, will return group_users of this user.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GroupUser[]
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

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/group_users', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new GroupUser((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Create Group User
     *
     * POST /group_users
     *
     * @param array $params Request parameters.
     *     - `group_id` (int, required): Group ID to add user to.
     *     - `user_id` (int, required): User ID to add to group.
     *     - `admin` (bool, optional): Is the user a group administrator?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GroupUser
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['group_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: group_id');
        }

        if (empty($params['user_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: user_id');
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/group_users', 'POST', $params, $options);

        return new GroupUser((array) (@$response->data ?: []), $options);
    }
}
