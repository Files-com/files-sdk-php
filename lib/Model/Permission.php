<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Permission object represents a grant of access permission on a specific Path to a User or Group.
 *
 * @property int|null $id Permission ID
 * @property string|null $path Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $user_id User ID
 * @property string|null $username Username (if applicable)
 * @property int|null $group_id Group ID
 * @property string|null $group_name Group name (if applicable)
 * @property int[]|null $group_ids Group IDs when this Permission requires membership in every listed group.
 * @property string[]|null $group_names Group names when this permission requires multiple groups
 * @property int|null $partner_id Partner ID (if applicable)
 * @property string|null $partner_name Partner name (if applicable)
 * @property string|null $permission Permission type. See the table referenced in the documentation for an explanation of each permission.
 * @property bool|null $recursive Whether this Permission applies to subfolders. Must be true for `admin` Permissions.
 * @property int|null $site_id Site ID
 * @package Files
 */
class Permission
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
     * Permission ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Permission ID
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
     * Username (if applicable)
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Username (if applicable)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
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
     * Group name (if applicable)
     *
     * @return string|null
     */
    public function getGroupName()
    {
        return isset($this->attributes['group_name']) ? $this->attributes['group_name'] : null;
    }

    /**
     * Group name (if applicable)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGroupName($value)
    {
        return $this->attributes['group_name'] = $value;
    }
    /**
     * Group IDs when this Permission requires membership in every listed group.
     *
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * Group IDs when this Permission requires membership in every listed group.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    /**
     * Group names when this permission requires multiple groups
     *
     * @return string[]|null
     */
    public function getGroupNames()
    {
        return isset($this->attributes['group_names']) ? $this->attributes['group_names'] : null;
    }

    /**
     * Group names when this permission requires multiple groups
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setGroupNames($value)
    {
        return $this->attributes['group_names'] = $value;
    }
    /**
     * Partner ID (if applicable)
     *
     * @return int|null
     */
    public function getPartnerId()
    {
        return isset($this->attributes['partner_id']) ? $this->attributes['partner_id'] : null;
    }

    /**
     * Partner ID (if applicable)
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPartnerId($value)
    {
        return $this->attributes['partner_id'] = $value;
    }
    /**
     * Partner name (if applicable)
     *
     * @return string|null
     */
    public function getPartnerName()
    {
        return isset($this->attributes['partner_name']) ? $this->attributes['partner_name'] : null;
    }

    /**
     * Partner name (if applicable)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartnerName($value)
    {
        return $this->attributes['partner_name'] = $value;
    }
    /**
     * Permission type.  See the table referenced in the documentation for an explanation of each permission.
     *
     * @return string|null
     */
    public function getPermission()
    {
        return isset($this->attributes['permission']) ? $this->attributes['permission'] : null;
    }

    /**
     * Permission type.  See the table referenced in the documentation for an explanation of each permission.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPermission($value)
    {
        return $this->attributes['permission'] = $value;
    }
    /**
     * Whether this Permission applies to subfolders. Must be true for `admin` Permissions.
     *
     * @return bool|null
     */
    public function getRecursive()
    {
        return isset($this->attributes['recursive']) ? $this->attributes['recursive'] : null;
    }

    /**
     * Whether this Permission applies to subfolders. Must be true for `admin` Permissions.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    /**
     * Site ID
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }

    /**
     * Site ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }

    /**
     * Delete Permission
     *
     * DELETE /permissions/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Permission ID.
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

        $response = Api::sendRequest('/permissions/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Permission
     *
     * DELETE /permissions/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Permission ID.
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
            throw new \Files\Exception\NotImplementedException('The Permission object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Permissions
     *
     * GET /permissions
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `group_id`, `path`, `user_id`, `partner_id` or `id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `group_id`, `partner_id` or `user_id`. Valid field combinations are `[ group_id, path ]`, `[ partner_id, path ]`, `[ user_id, path ]`, `[ user_id, group_id ]`, `[ user_id, group_id, path ]`, `[ user_id, group_id, partner_id ]` or `[ user_id, group_id, partner_id, path ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
     *     - `path` (string, optional): Permission path.  If provided, will scope all permissions(including upward) to this path.
     *     - `include_groups` (bool, optional): If searching by user or group, also include user's permissions that are inherited from its groups?
     *     - `group_id` (string, optional):
     *     - `partner_id` (string, optional):
     *     - `user_id` (string, optional):
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Permission[]
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

        if (!empty($params['group_id']) && !is_string($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type string; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['partner_id']) && !is_string($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type string; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['user_id']) && !is_string($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type string; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/permissions', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Permission((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Create Permission
     *
     * POST /permissions
     *
     * @param array $params Request parameters.
     *     - `path` (string, required): Folder path
     *     - `group_id` (int, optional): Group ID. Provide `group_name` or `group_id`
     *     - `group_ids` (string, optional): Group IDs when the Permission requires membership in every listed group. If sent as a string, it should be comma-delimited.
     *     - `permission` (string, optional): Permission type.  Can be `admin`, `full`, `readonly`, `writeonly`, `list`, or `history`
     *     - `recursive` (bool, optional): Apply to subfolders recursively? Must be true for `admin` Permissions.
     *     - `partner_id` (int, optional): Partner ID if this Permission belongs to a partner.
     *     - `user_id` (int, optional): User ID.  Provide `username` or `user_id`
     *     - `username` (string, optional): User username.  Provide `username` or `user_id`
     *     - `group_name` (string, optional): Group name.  Provide `group_name` or `group_id`
     *     - `site_id` (int, optional): Site ID. If not provided, will default to current site. Used when creating a permission for a child site.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Permission
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (!empty($params['permission']) && !is_string($params['permission'])) {
            throw new \Files\Exception\InvalidParameterException('$permission must be of type string; received ' . gettype(@$params['permission']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (!empty($params['group_name']) && !is_string($params['group_name'])) {
            throw new \Files\Exception\InvalidParameterException('$group_name must be of type string; received ' . gettype(@$params['group_name']));
        }

        if (!empty($params['site_id']) && !is_int($params['site_id'])) {
            throw new \Files\Exception\InvalidParameterException('$site_id must be of type int; received ' . gettype(@$params['site_id']));
        }

        $response = Api::sendRequest('/permissions', 'POST', $params, $options);

        return new Permission((array) (@$response->data ?: []), $options);
    }
}
