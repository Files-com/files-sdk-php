<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Export
 *
 * @package Files
 */
class Export
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
    // int64 # ID for this Export
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Status of the Export
    public function getExportStatus()
    {
        return @$this->attributes['export_status'];
    }

    public function setExportStatus($value)
    {
        return $this->attributes['export_status'] = $value;
    }
    // string # Type of data being exported
    public function getExportType()
    {
        return @$this->attributes['export_type'];
    }

    public function setExportType($value)
    {
        return $this->attributes['export_type'] = $value;
    }
    // int64 # Number of rows exported
    public function getExportRows()
    {
        return @$this->attributes['export_rows'];
    }

    public function setExportRows($value)
    {
        return $this->attributes['export_rows'] = $value;
    }
    // string # Link to download Export file.
    public function getDownloadUri()
    {
        return @$this->attributes['download_uri'];
    }

    public function setDownloadUri($value)
    {
        return $this->attributes['download_uri'] = $value;
    }
    // string # Export message
    public function getMessage()
    {
        return @$this->attributes['message'];
    }

    public function setMessage($value)
    {
        return $this->attributes['message'] = $value;
    }
    // int64 # User ID.  Provide a value of `0` to operate the current session's user.
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // object # If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `export_status` and `export_type`.
    public function getSortBy()
    {
        return @$this->attributes['sort_by'];
    }

    public function setSortBy($value)
    {
        return $this->attributes['sort_by'] = $value;
    }
    // object # If set, return records where the specified field is equal to the supplied value. Valid fields are `export_status` and `export_type`.
    public function getFilter()
    {
        return @$this->attributes['filter'];
    }

    public function setFilter($value)
    {
        return $this->attributes['filter'] = $value;
    }
    // object # If set, return records where the specified field is prefixed by the supplied value. Valid fields are `export_type`.
    public function getFilterPrefix()
    {
        return @$this->attributes['filter_prefix'];
    }

    public function setFilterPrefix($value)
    {
        return $this->attributes['filter_prefix'] = $value;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The Export object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `export_status` and `export_type`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `export_status` and `export_type`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `export_type`.
    public static function all($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/exports', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Export ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/exports/' . @$params['id'] . '', 'GET', $params, $options);

        return new Export((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `export_status` and `export_type`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `export_status` and `export_type`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `export_type`.
    public static function create($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/exports/create_export', 'POST', $params, $options);

        return new Export((array) (@$response->data ?: []), $options);
    }
}
