<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class GpgKey
 *
 * @package Files
 */
class GpgKey
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
    // int64 # Your GPG key ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // date-time # Your GPG key expiration date.
    public function getExpiresAt()
    {
        return @$this->attributes['expires_at'];
    }

    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    // string # Your GPG key name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // int64 # GPG owner's user id
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Your GPG public key
    public function getPublicKey()
    {
        return @$this->attributes['public_key'];
    }

    public function setPublicKey($value)
    {
        return $this->attributes['public_key'] = $value;
    }
    // string # Your GPG private key.
    public function getPrivateKey()
    {
        return @$this->attributes['private_key'];
    }

    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    // string # Your GPG private key password. Only required for password protected keys.
    public function getPrivateKeyPassword()
    {
        return @$this->attributes['private_key_password'];
    }

    public function setPrivateKeyPassword($value)
    {
        return $this->attributes['private_key_password'] = $value;
    }

    // Parameters:
    //   public_key - string - Your GPG public key
    //   private_key - string - Your GPG private key.
    //   private_key_password - string - Your GPG private key password. Only required for password protected keys.
    //   name - string - Your GPG key name.
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

        if (@$params['public_key'] && !is_string(@$params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_password'] && !is_string(@$params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/gpg_keys/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new GpgKey((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/gpg_keys/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name` and `expires_at`.
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

        $response = Api::sendRequest('/gpg_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new GpgKey((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Gpg Key ID.
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

        $response = Api::sendRequest('/gpg_keys/' . @$params['id'] . '', 'GET', $params, $options);

        return new GpgKey((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   public_key - string - Your GPG public key
    //   private_key - string - Your GPG private key.
    //   private_key_password - string - Your GPG private key password. Only required for password protected keys.
    //   name (required) - string - Your GPG key name.
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['public_key'] && !is_string(@$params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_password'] && !is_string(@$params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/gpg_keys', 'POST', $params, $options);

        return new GpgKey((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name` and `expires_at`.
    public static function createExport($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/gpg_keys/create_export', 'POST', $params, $options);

        return new Export((array) (@$response->data ?: []), $options);
    }
}
