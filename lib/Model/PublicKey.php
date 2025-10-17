<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PublicKey
 *
 * @package Files
 */
class PublicKey
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
    // int64 # Public key ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Public key title
    public function getTitle()
    {
        return @$this->attributes['title'];
    }

    public function setTitle($value)
    {
        return $this->attributes['title'] = $value;
    }
    // date-time # Public key created at date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # Public key fingerprint (MD5)
    public function getFingerprint()
    {
        return @$this->attributes['fingerprint'];
    }

    public function setFingerprint($value)
    {
        return $this->attributes['fingerprint'] = $value;
    }
    // string # Public key fingerprint (SHA256)
    public function getFingerprintSha256()
    {
        return @$this->attributes['fingerprint_sha256'];
    }

    public function setFingerprintSha256($value)
    {
        return $this->attributes['fingerprint_sha256'] = $value;
    }
    // string # Only returned when generating keys. Can be invalid, not_generated, generating, complete
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // date-time # Key's most recent login time via SFTP
    public function getLastLoginAt()
    {
        return @$this->attributes['last_login_at'];
    }

    public function setLastLoginAt($value)
    {
        return $this->attributes['last_login_at'] = $value;
    }
    // string # Only returned when generating keys. Private key generated for the user.
    public function getGeneratedPrivateKey()
    {
        return @$this->attributes['generated_private_key'];
    }

    public function setGeneratedPrivateKey($value)
    {
        return $this->attributes['generated_private_key'] = $value;
    }
    // string # Only returned when generating keys. Public key generated for the user.
    public function getGeneratedPublicKey()
    {
        return @$this->attributes['generated_public_key'];
    }

    public function setGeneratedPublicKey($value)
    {
        return $this->attributes['generated_public_key'] = $value;
    }
    // string # Username of the user this public key is associated with
    public function getUsername()
    {
        return @$this->attributes['username'];
    }

    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    // int64 # User ID this public key is associated with
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Actual contents of SSH key.
    public function getPublicKey()
    {
        return @$this->attributes['public_key'];
    }

    public function setPublicKey($value)
    {
        return $this->attributes['public_key'] = $value;
    }
    // boolean # If true, generate a new SSH key pair. Can not be used with `public_key`
    public function getGenerateKeypair()
    {
        return @$this->attributes['generate_keypair'];
    }

    public function setGenerateKeypair($value)
    {
        return $this->attributes['generate_keypair'] = $value;
    }
    // string # Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    public function getGeneratePrivateKeyPassword()
    {
        return @$this->attributes['generate_private_key_password'];
    }

    public function setGeneratePrivateKeyPassword($value)
    {
        return $this->attributes['generate_private_key_password'] = $value;
    }
    // string # Type of key to generate.  One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    public function getGenerateAlgorithm()
    {
        return @$this->attributes['generate_algorithm'];
    }

    public function setGenerateAlgorithm($value)
    {
        return $this->attributes['generate_algorithm'] = $value;
    }
    // int64 # Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    public function getGenerateLength()
    {
        return @$this->attributes['generate_length'];
    }

    public function setGenerateLength($value)
    {
        return $this->attributes['generate_length'] = $value;
    }

    // Parameters:
    //   title (required) - string - Internal reference for key.
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

        if (!@$params['title']) {
            if (@$this->title) {
                $params['title'] = $this->title;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: title');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['title'] && !is_string(@$params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new PublicKey((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `title`, `created_at` or `user_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
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

        $response = Api::sendRequest('/public_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PublicKey((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Public Key ID.
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

        $response = Api::sendRequest('/public_keys/' . @$params['id'] . '', 'GET', $params, $options);

        return new PublicKey((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   title (required) - string - Internal reference for key.
    //   public_key - string - Actual contents of SSH key.
    //   generate_keypair - boolean - If true, generate a new SSH key pair. Can not be used with `public_key`
    //   generate_private_key_password - string - Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    //   generate_algorithm - string - Type of key to generate.  One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    //   generate_length - int64 - Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
    public static function create($params = [], $options = [])
    {
        if (!@$params['title']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: title');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['title'] && !is_string(@$params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (@$params['public_key'] && !is_string(@$params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (@$params['generate_private_key_password'] && !is_string(@$params['generate_private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_private_key_password must be of type string; received ' . gettype(@$params['generate_private_key_password']));
        }

        if (@$params['generate_algorithm'] && !is_string(@$params['generate_algorithm'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_algorithm must be of type string; received ' . gettype(@$params['generate_algorithm']));
        }

        if (@$params['generate_length'] && !is_int(@$params['generate_length'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_length must be of type int; received ' . gettype(@$params['generate_length']));
        }

        $response = Api::sendRequest('/public_keys', 'POST', $params, $options);

        return new PublicKey((array) (@$response->data ?: []), $options);
    }
}
