<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A GPGKey object on Files.com is used to securely store both the private and public keys associated with a GPG (GNU Privacy Guard) encryption key pair.
 *
 * @property int|null $id GPG key ID.
 * @property int|null $workspace_id Workspace ID (0 for default workspace).
 * @property string|null $expires_at GPG key expiration date.
 * @property string|null $name GPG key name.
 * @property int|null $partner_id Partner ID who owns this GPG Key, if applicable.
 * @property string|null $partner_name Name of the Partner who owns this GPG Key, if applicable.
 * @property int|null $user_id User ID who owns this GPG Key, if applicable.
 * @property string|null $public_key_md5 MD5 hash of the GPG public key
 * @property string|null $private_key_md5 MD5 hash of the GPG private key.
 * @property string|null $generated_public_key GPG public key
 * @property string|null $generated_private_key GPG private key.
 * @property string|null $private_key_password_md5 GPG private key password. Only required for password protected keys.
 * @property string|null $public_key The GPG public key
 * @property string|null $private_key The GPG private key
 * @property string|null $private_key_password The GPG private key password
 * @property string|null $generate_expires_at Expiration date of the key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @property bool|null $generate_keypair If true, generate a new GPG key pair. Can not be used with `public_key`/`private_key`
 * @property string|null $generate_full_name Full name of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @property string|null $generate_email Email address of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
 * @package Files
 */
class GpgKey
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
     * GPG key ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * GPG key ID.
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
     * GPG key expiration date.
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * GPG key expiration date.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * GPG key name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * GPG key name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Partner ID who owns this GPG Key, if applicable.
     *
     * @return int|null
     */
    public function getPartnerId()
    {
        return isset($this->attributes['partner_id']) ? $this->attributes['partner_id'] : null;
    }

    /**
     * Partner ID who owns this GPG Key, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPartnerId($value)
    {
        return $this->attributes['partner_id'] = $value;
    }
    /**
     * Name of the Partner who owns this GPG Key, if applicable.
     *
     * @return string|null
     */
    public function getPartnerName()
    {
        return isset($this->attributes['partner_name']) ? $this->attributes['partner_name'] : null;
    }

    /**
     * Name of the Partner who owns this GPG Key, if applicable.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartnerName($value)
    {
        return $this->attributes['partner_name'] = $value;
    }
    /**
     * User ID who owns this GPG Key, if applicable.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID who owns this GPG Key, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * MD5 hash of the GPG public key
     *
     * @return string|null
     */
    public function getPublicKeyMd5()
    {
        return isset($this->attributes['public_key_md5']) ? $this->attributes['public_key_md5'] : null;
    }

    /**
     * MD5 hash of the GPG public key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicKeyMd5($value)
    {
        return $this->attributes['public_key_md5'] = $value;
    }
    /**
     * MD5 hash of the GPG private key.
     *
     * @return string|null
     */
    public function getPrivateKeyMd5()
    {
        return isset($this->attributes['private_key_md5']) ? $this->attributes['private_key_md5'] : null;
    }

    /**
     * MD5 hash of the GPG private key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyMd5($value)
    {
        return $this->attributes['private_key_md5'] = $value;
    }
    /**
     * GPG public key
     *
     * @return string|null
     */
    public function getGeneratedPublicKey()
    {
        return isset($this->attributes['generated_public_key']) ? $this->attributes['generated_public_key'] : null;
    }

    /**
     * GPG public key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGeneratedPublicKey($value)
    {
        return $this->attributes['generated_public_key'] = $value;
    }
    /**
     * GPG private key.
     *
     * @return string|null
     */
    public function getGeneratedPrivateKey()
    {
        return isset($this->attributes['generated_private_key']) ? $this->attributes['generated_private_key'] : null;
    }

    /**
     * GPG private key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGeneratedPrivateKey($value)
    {
        return $this->attributes['generated_private_key'] = $value;
    }
    /**
     * GPG private key password. Only required for password protected keys.
     *
     * @return string|null
     */
    public function getPrivateKeyPasswordMd5()
    {
        return isset($this->attributes['private_key_password_md5']) ? $this->attributes['private_key_password_md5'] : null;
    }

    /**
     * GPG private key password. Only required for password protected keys.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyPasswordMd5($value)
    {
        return $this->attributes['private_key_password_md5'] = $value;
    }
    /**
     * The GPG public key
     *
     * @return string|null
     */
    public function getPublicKey()
    {
        return isset($this->attributes['public_key']) ? $this->attributes['public_key'] : null;
    }

    /**
     * The GPG public key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicKey($value)
    {
        return $this->attributes['public_key'] = $value;
    }
    /**
     * The GPG private key
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    /**
     * The GPG private key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    /**
     * The GPG private key password
     *
     * @return string|null
     */
    public function getPrivateKeyPassword()
    {
        return isset($this->attributes['private_key_password']) ? $this->attributes['private_key_password'] : null;
    }

    /**
     * The GPG private key password
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyPassword($value)
    {
        return $this->attributes['private_key_password'] = $value;
    }
    /**
     * Expiration date of the key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return string|null
     */
    public function getGenerateExpiresAt()
    {
        return isset($this->attributes['generate_expires_at']) ? $this->attributes['generate_expires_at'] : null;
    }

    /**
     * Expiration date of the key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGenerateExpiresAt($value)
    {
        return $this->attributes['generate_expires_at'] = $value;
    }
    /**
     * If true, generate a new GPG key pair. Can not be used with `public_key`/`private_key`
     *
     * @return bool|null
     */
    public function getGenerateKeypair()
    {
        return isset($this->attributes['generate_keypair']) ? $this->attributes['generate_keypair'] : null;
    }

    /**
     * If true, generate a new GPG key pair. Can not be used with `public_key`/`private_key`
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setGenerateKeypair($value)
    {
        return $this->attributes['generate_keypair'] = $value;
    }
    /**
     * Full name of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return string|null
     */
    public function getGenerateFullName()
    {
        return isset($this->attributes['generate_full_name']) ? $this->attributes['generate_full_name'] : null;
    }

    /**
     * Full name of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGenerateFullName($value)
    {
        return $this->attributes['generate_full_name'] = $value;
    }
    /**
     * Email address of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @return string|null
     */
    public function getGenerateEmail()
    {
        return isset($this->attributes['generate_email']) ? $this->attributes['generate_email'] : null;
    }

    /**
     * Email address of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGenerateEmail($value)
    {
        return $this->attributes['generate_email'] = $value;
    }

    /**
     * Update GPG Key
     *
     * PATCH /gpg_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Gpg Key ID.
     *     - `partner_id` (int, optional): Partner ID who owns this GPG Key, if applicable.
     *     - `public_key` (string, optional): The GPG public key
     *     - `private_key` (string, optional): The GPG private key
     *     - `private_key_password` (string, optional): The GPG private key password
     *     - `name` (string, optional): GPG key name.
     * @return \Files\Model\GpgKey
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['public_key']) && !is_string($params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_password']) && !is_string($params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/gpg_keys/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new GpgKey((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete GPG Key
     *
     * DELETE /gpg_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Gpg Key ID.
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

        $response = Api::sendRequest('/gpg_keys/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete GPG Key
     *
     * DELETE /gpg_keys/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Gpg Key ID.
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
     * List GPG Keys
     *
     * GET /gpg_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `expires_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`, `partner_id` or `expires_at`. Valid field combinations are `[ workspace_id, partner_id ]`, `[ workspace_id, expires_at ]`, `[ partner_id, expires_at ]` or `[ workspace_id, partner_id, expires_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `expires_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `expires_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `expires_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `expires_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GpgKey[]
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

        $response = Api::sendRequest('/gpg_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new GpgKey((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show GPG Key
     *
     * GET /gpg_keys/{id}
     *
     * @param int $id Gpg Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GpgKey
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

        $response = Api::sendRequest('/gpg_keys/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new GpgKey((array) (@$response->data ?: []), $options);
    }
    /**
     * Show GPG Key
     *
     * GET /gpg_keys/{id}
     *
     * Alias of find().
     *
     * @param int $id Gpg Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GpgKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create GPG Key
     *
     * POST /gpg_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `partner_id` (int, optional): Partner ID who owns this GPG Key, if applicable.
     *     - `public_key` (string, optional): The GPG public key
     *     - `private_key` (string, optional): The GPG private key
     *     - `private_key_password` (string, optional): The GPG private key password
     *     - `name` (string, required): GPG key name.
     *     - `workspace_id` (int, optional): Workspace ID (0 for default workspace).
     *     - `generate_expires_at` (string, optional): Expiration date of the key. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *     - `generate_keypair` (bool, optional): If true, generate a new GPG key pair. Can not be used with `public_key`/`private_key`
     *     - `generate_full_name` (string, optional): Full name of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     *     - `generate_email` (string, optional): Email address of the key owner. Used for the generation of the key. Will be ignored if `generate_keypair` is false.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\GpgKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (!empty($params['public_key']) && !is_string($params['public_key'])) {
            throw new \Files\Exception\InvalidParameterException('$public_key must be of type string; received ' . gettype(@$params['public_key']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_password']) && !is_string($params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['generate_expires_at']) && !is_string($params['generate_expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_expires_at must be of type string; received ' . gettype(@$params['generate_expires_at']));
        }

        if (!empty($params['generate_full_name']) && !is_string($params['generate_full_name'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_full_name must be of type string; received ' . gettype(@$params['generate_full_name']));
        }

        if (!empty($params['generate_email']) && !is_string($params['generate_email'])) {
            throw new \Files\Exception\InvalidParameterException('$generate_email must be of type string; received ' . gettype(@$params['generate_email']));
        }

        $response = Api::sendRequest('/gpg_keys', 'POST', $params, $options);

        return new GpgKey((array) (@$response->data ?: []), $options);
    }
}
