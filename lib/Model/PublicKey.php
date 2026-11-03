<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A PublicKey is used to authenticate to Files.com via SFTP (SSH File Transfer Protocol).
 *
 * @property int|null $id Public key ID
 * @property int|null $workspace_id Workspace ID (0 for default workspace).
 * @property string|null $title Public key title
 * @property string|null $created_at Public key created at date/time
 * @property string|null $expires_at Public key expiration date/time
 * @property bool|null $expired Is this public key expired?
 * @property string|null $fingerprint Public key fingerprint (MD5)
 * @property string|null $fingerprint_sha256 Public key fingerprint (SHA256)
 * @property string|null $status Only returned when generating keys. Can be invalid, not_generated, generating, complete
 * @property string|null $last_login_at Key's most recent login time via SFTP
 * @property string|null $generated_private_key Only returned when generating keys. Private key generated for the user.
 * @property string|null $generated_public_key Only returned when generating keys. Public key generated for the user.
 * @property string|null $username Username of the user this public key is associated with
 * @property int|null $user_id User ID this public key is associated with
 * @property string|null $public_key Actual contents of SSH key.
 * @property bool|null $generate_keypair If true, generate a new SSH key pair. Can not be used with `public_key`
 * @property string|null $generate_private_key_password Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @property string|null $generate_algorithm Type of key to generate. One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @property int|null $generate_length Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @package Files
 */
class PublicKey
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
     * Public key ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Public key ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace ID (0 for default workspace).
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID (0 for default workspace).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Public key title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return isset($this->attributes['title']) ? $this->attributes['title'] : null;
    }

    /**
     * Public key title
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTitle($value)
    {
        return $this->attributes['title'] = $value;
    }
    /**
     * Public key created at date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Public key expiration date/time
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * Public key expiration date/time
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * Is this public key expired?
     *
     * @return bool|null
     */
    public function getExpired()
    {
        return isset($this->attributes['expired']) ? $this->attributes['expired'] : null;
    }

    /**
     * Is this public key expired?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setExpired($value)
    {
        return $this->attributes['expired'] = $value;
    }
    /**
     * Public key fingerprint (MD5)
     *
     * @return string|null
     */
    public function getFingerprint()
    {
        return isset($this->attributes['fingerprint']) ? $this->attributes['fingerprint'] : null;
    }

    /**
     * Public key fingerprint (MD5)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFingerprint($value)
    {
        return $this->attributes['fingerprint'] = $value;
    }
    /**
     * Public key fingerprint (SHA256)
     *
     * @return string|null
     */
    public function getFingerprintSha256()
    {
        return isset($this->attributes['fingerprint_sha256']) ? $this->attributes['fingerprint_sha256'] : null;
    }

    /**
     * Public key fingerprint (SHA256)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFingerprintSha256($value)
    {
        return $this->attributes['fingerprint_sha256'] = $value;
    }
    /**
     * Only returned when generating keys. Can be invalid, not_generated, generating, complete
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Only returned when generating keys. Can be invalid, not_generated, generating, complete
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * Key's most recent login time via SFTP
     *
     * @return string|null
     */
    public function getLastLoginAt()
    {
        return isset($this->attributes['last_login_at']) ? $this->attributes['last_login_at'] : null;
    }

    /**
     * Key's most recent login time via SFTP
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastLoginAt($value)
    {
        return $this->attributes['last_login_at'] = $value;
    }
    /**
     * Only returned when generating keys. Private key generated for the user.
     *
     * @return string|null
     */
    public function getGeneratedPrivateKey()
    {
        return isset($this->attributes['generated_private_key']) ? $this->attributes['generated_private_key'] : null;
    }

    /**
     * Only returned when generating keys. Private key generated for the user.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGeneratedPrivateKey($value)
    {
        return $this->attributes['generated_private_key'] = $value;
    }
    /**
     * Only returned when generating keys. Public key generated for the user.
     *
     * @return string|null
     */
    public function getGeneratedPublicKey()
    {
        return isset($this->attributes['generated_public_key']) ? $this->attributes['generated_public_key'] : null;
    }

    /**
     * Only returned when generating keys. Public key generated for the user.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGeneratedPublicKey($value)
    {
        return $this->attributes['generated_public_key'] = $value;
    }
    /**
     * Username of the user this public key is associated with
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Username of the user this public key is associated with
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * User ID this public key is associated with
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID this public key is associated with
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Actual contents of SSH key.
     *
     * @return string|null
     */
    public function getPublicKey()
    {
        return isset($this->attributes['public_key']) ? $this->attributes['public_key'] : null;
    }

    /**
     * Actual contents of SSH key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicKey($value)
    {
        return $this->attributes['public_key'] = $value;
    }
    /**
     * If true, generate a new SSH key pair. Can not be used with `public_key`
     *
     * @return bool|null
     */
    public function getGenerateKeypair()
    {
        return isset($this->attributes['generate_keypair']) ? $this->attributes['generate_keypair'] : null;
    }

    /**
     * If true, generate a new SSH key pair. Can not be used with `public_key`
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setGenerateKeypair($value)
    {
        return $this->attributes['generate_keypair'] = $value;
    }
    /**
     * Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return string|null
     */
    public function getGeneratePrivateKeyPassword()
    {
        return isset($this->attributes['generate_private_key_password']) ? $this->attributes['generate_private_key_password'] : null;
    }

    /**
     * Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGeneratePrivateKeyPassword($value)
    {
        return $this->attributes['generate_private_key_password'] = $value;
    }
    /**
     * Type of key to generate.  One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return string|null
     */
    public function getGenerateAlgorithm()
    {
        return isset($this->attributes['generate_algorithm']) ? $this->attributes['generate_algorithm'] : null;
    }

    /**
     * Type of key to generate.  One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGenerateAlgorithm($value)
    {
        return $this->attributes['generate_algorithm'] = $value;
    }
    /**
     * Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return int|null
     */
    public function getGenerateLength()
    {
        return isset($this->attributes['generate_length']) ? $this->attributes['generate_length'] : null;
    }

    /**
     * Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setGenerateLength($value)
    {
        return $this->attributes['generate_length'] = $value;
    }

    /**
     * Update Public Key
     *
     * PATCH /public_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Public Key ID.
     *     - `title` (string, required): Internal reference for key.
     * @return \Files\Model\PublicKey
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

        if (empty($params['title'])) {
            if (@$this->title) {
                $params['title'] = $this->title;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: title');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        $response = Api::sendRequest('/public_keys/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new PublicKey((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Public Key
     *
     * DELETE /public_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Public Key ID.
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

        $response = Api::sendRequest('/public_keys/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Public Key
     *
     * DELETE /public_keys/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Public Key ID.
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
     * List Public Keys
     *
     * GET /public_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `user_id`, `title` or `created_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at` and `workspace_id`. Valid field combinations are `[ workspace_id, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PublicKey[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/public_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PublicKey((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Public Key
     *
     * GET /public_keys/{id}
     *
     * @param int $id Public Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PublicKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/public_keys/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new PublicKey((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Public Key
     *
     * GET /public_keys/{id}
     *
     * Alias of find().
     *
     * @param int $id Public Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PublicKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Public Key
     *
     * POST /public_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `title` (string, required): Internal reference for key.
     *     - `public_key` (string, optional): Actual contents of SSH key.
     *     - `generate_keypair` (bool, optional): If true, generate a new SSH key pair. Can not be used with `public_key`
     *     - `generate_private_key_password` (string, optional): Password for the private key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *     - `generate_algorithm` (string, optional): Type of key to generate.  One of rsa, dsa, ecdsa, ed25519. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *     - `generate_length` (int, optional): Length of key to generate. If algorithm is ecdsa, this is the signature size. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PublicKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['title'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: title');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (!empty($params['public_key']) && !is_string($params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (!empty($params['generate_private_key_password']) && !is_string($params['generate_private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_private_key_password must be of type string; received ' . gettype(@$params['generate_private_key_password']));
        }

        if (!empty($params['generate_algorithm']) && !is_string($params['generate_algorithm'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_algorithm must be of type string; received ' . gettype(@$params['generate_algorithm']));
        }

        if (!empty($params['generate_length']) && !is_int($params['generate_length'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_length must be of type int; received ' . gettype(@$params['generate_length']));
        }

        $response = Api::sendRequest('/public_keys', 'POST', $params, $options);

        return new PublicKey((array) (@$response->data ?: []), $options);
    }
}
