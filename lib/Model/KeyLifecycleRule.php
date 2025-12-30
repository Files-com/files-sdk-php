<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class KeyLifecycleRule
 *
 * @package Files
 */
class KeyLifecycleRule
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
    // int64 # Key Lifecycle Rule ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Key type for which the rule will apply (gpg or ssh).
    public function getKeyType()
    {
        return @$this->attributes['key_type'];
    }

    public function setKeyType($value)
    {
        return $this->attributes['key_type'] = $value;
    }
    // int64 # Number of days of inactivity before the rule applies.
    public function getInactivityDays()
    {
        return @$this->attributes['inactivity_days'];
    }

    public function setInactivityDays($value)
    {
        return $this->attributes['inactivity_days'] = $value;
    }
    // string # Key Lifecycle Rule name
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }

    // Parameters:
    //   key_type - string - Key type for which the rule will apply (gpg or ssh).
    //   inactivity_days - int64 - Number of days of inactivity before the rule applies.
    //   name - string - Key Lifecycle Rule name
    public function update($params = [])
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

        if (@$params['key_type'] && !is_string(@$params['key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$key_type must be of type string; received ' . gettype(@$params['key_type']));
        }

        if (@$params['inactivity_days'] && !is_int(@$params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/key_lifecycle_rules/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new KeyLifecycleRule((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/key_lifecycle_rules/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
            $new_obj = $this->update($this->attributes);
            $this->attributes = $new_obj->attributes;
            return true;
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `key_type`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `key_type`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/key_lifecycle_rules', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new KeyLifecycleRule((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Key Lifecycle Rule ID.
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

        $response = Api::sendRequest('/key_lifecycle_rules/' . @$params['id'] . '', 'GET', $params, $options);

        return new KeyLifecycleRule((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   key_type - string - Key type for which the rule will apply (gpg or ssh).
    //   inactivity_days - int64 - Number of days of inactivity before the rule applies.
    //   name - string - Key Lifecycle Rule name
    public static function create($params = [], $options = [])
    {
        if (@$params['key_type'] && !is_string(@$params['key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$key_type must be of type string; received ' . gettype(@$params['key_type']));
        }

        if (@$params['inactivity_days'] && !is_int(@$params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/key_lifecycle_rules', 'POST', $params, $options);

        return new KeyLifecycleRule((array) (@$response->data ?: []), $options);
    }
}
