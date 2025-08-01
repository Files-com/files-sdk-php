<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UserCipherUse
 *
 * @package Files
 */
class UserCipherUse
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
    // int64 # UserCipherUse ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # ID of the user who performed this access
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }
    // string # Username of the user who performed this access
    public function getUsername()
    {
        return @$this->attributes['username'];
    }
    // string # The protocol and cipher employed
    public function getProtocolCipher()
    {
        return @$this->attributes['protocol_cipher'];
    }
    // date-time # The earliest recorded use of this combination of interface and protocol and cipher (for this user)
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // boolean # Is this cipher considered insecure?
    public function getInsecure()
    {
        return @$this->attributes['insecure'];
    }
    // string # The interface accessed
    public function getInterface()
    {
        return @$this->attributes['interface'];
    }
    // date-time # The most recent use of this combination of interface and protocol and cipher (for this user)
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   user_id - int64 - User ID. If provided, will return uses for this user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `updated_at`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `insecure` and `updated_at`. Valid field combinations are `[ insecure, updated_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `updated_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `updated_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `updated_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `updated_at`.
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

        $response = Api::sendRequest('/user_cipher_uses', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new UserCipherUse((array) $obj, $options);
        }

        return $return_array;
    }
}
