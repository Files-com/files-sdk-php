<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Permission
 *
 * @package Files
 */
class Permission
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
    // int64 # Permission ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // int64 # User ID
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Username (if applicable)
    public function getUsername()
    {
        return @$this->attributes['username'];
    }

    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    // int64 # Group ID
    public function getGroupId()
    {
        return @$this->attributes['group_id'];
    }

    public function setGroupId($value)
    {
        return $this->attributes['group_id'] = $value;
    }
    // string # Group name (if applicable)
    public function getGroupName()
    {
        return @$this->attributes['group_name'];
    }

    public function setGroupName($value)
    {
        return $this->attributes['group_name'] = $value;
    }
    // string # Permission type.  See the table referenced in the documentation for an explanation of each permission.
    public function getPermission()
    {
        return @$this->attributes['permission'];
    }

    public function setPermission($value)
    {
        return $this->attributes['permission'] = $value;
    }
    // boolean # Recursive: does this permission apply to subfolders?
    public function getRecursive()
    {
        return @$this->attributes['recursive'];
    }

    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    // int64 # Site ID
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }

    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/permissions/' . @$params['id'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

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


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `group_id`, `path` or `user_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `group_id` or `user_id`. Valid field combinations are `[ group_id, path ]`, `[ user_id, path ]` or `[ user_id, group_id ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
    //   path - string - Permission path.  If provided, will scope all permissions(including upward) to this path.
    //   include_groups - boolean - If searching by user or group, also include user's permissions that are inherited from its groups?
    //   group_id - string
    //   user_id - string
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['group_id'] && !is_string(@$params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type string; received ' . gettype(@$params['group_id']));
        }

        if (@$params['user_id'] && !is_string(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type string; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/permissions', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Permission((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   path (required) - string - Folder path
    //   group_id - int64 - Group ID. Provide `group_name` or `group_id`
    //   permission - string - Permission type.  Can be `admin`, `full`, `readonly`, `writeonly`, `list`, or `history`
    //   recursive - boolean - Apply to subfolders recursively?
    //   user_id - int64 - User ID.  Provide `username` or `user_id`
    //   username - string - User username.  Provide `username` or `user_id`
    //   group_name - string - Group name.  Provide `group_name` or `group_id`
    //   site_id - int64 - Site ID. If not provided, will default to current site. Used when creating a permission for a child site.
    public static function create($params = [], $options = [])
    {
        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['group_id'] && !is_int(@$params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (@$params['permission'] && !is_string(@$params['permission'])) {
            throw new \Files\Exception\InvalidParameterException('$permission must be of type string; received ' . gettype(@$params['permission']));
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['username'] && !is_string(@$params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (@$params['group_name'] && !is_string(@$params['group_name'])) {
            throw new \Files\Exception\InvalidParameterException('$group_name must be of type string; received ' . gettype(@$params['group_name']));
        }

        if (@$params['site_id'] && !is_int(@$params['site_id'])) {
            throw new \Files\Exception\InvalidParameterException('$site_id must be of type int; received ' . gettype(@$params['site_id']));
        }

        $response = Api::sendRequest('/permissions', 'POST', $params, $options);

        return new Permission((array) (@$response->data ?: []), $options);
    }
}
