<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Request
 *
 * @package Files
 */
class Request
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
                return @self::$method($arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // int64 # Request ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Source filename, if applicable
    public function getSource()
    {
        return @$this->attributes['source'];
    }

    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    // string # Destination filename
    public function getDestination()
    {
        return @$this->attributes['destination'];
    }

    public function setDestination($value)
    {
        return $this->attributes['destination'] = $value;
    }
    // string # ID of automation that created request
    public function getAutomationId()
    {
        return @$this->attributes['automation_id'];
    }

    public function setAutomationId($value)
    {
        return $this->attributes['automation_id'] = $value;
    }
    // string # User making the request (if applicable)
    public function getUserDisplayName()
    {
        return @$this->attributes['user_display_name'];
    }

    public function setUserDisplayName($value)
    {
        return $this->attributes['user_display_name'] = $value;
    }
    // string # A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
    public function getUserIds()
    {
        return @$this->attributes['user_ids'];
    }

    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }
    // string # A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
    public function getGroupIds()
    {
        return @$this->attributes['group_ids'];
    }

    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
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

        $response = Api::sendRequest('/requests/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
            throw new \Files\Exception\NotImplementedException('The Request object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[destination]=desc`). Valid fields are `destination`.
    //   mine - boolean - Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
    //   path - string - Path to show requests for.  If omitted, shows all paths. Send `/` to represent the root directory.
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

        $response = Api::sendRequest('/requests', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Request((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[destination]=desc`). Valid fields are `destination`.
    //   mine - boolean - Only show requests of the current user?  (Defaults to true if current user is not a site admin.)
    //   path (required) - string - Path to show requests for.  If omitted, shows all paths. Send `/` to represent the root directory.
    public static function getFolder($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/requests/folders/' . @$params['path'] . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Request((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   path (required) - string - Folder path on which to request the file.
    //   destination (required) - string - Destination filename (without extension) to request.
    //   user_ids - string - A list of user IDs to request the file from. If sent as a string, it should be comma-delimited.
    //   group_ids - string - A list of group IDs to request the file from. If sent as a string, it should be comma-delimited.
    public static function create($params = [], $options = [])
    {
        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!@$params['destination']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        $response = Api::sendRequest('/requests', 'POST', $params, $options);

        return new Request((array) (@$response->data ?: []), $options);
    }
}
