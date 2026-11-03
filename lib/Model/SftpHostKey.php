<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An SFTP Host Key is a cryptographic key used to verify the identity of the server during an SFTP connection.
 *
 * @property bool|null $active If true, use this SFTP Host Key.
 * @property int|null $custom_domain_id Custom Domain ID. If set, this key is used only for that Custom Domain.
 * @property int|null $id SFTP Host Key ID
 * @property string|null $name The friendly name of this SFTP Host Key.
 * @property string|null $key_type SSH key type
 * @property string|null $fingerprint_md5 MD5 Fingerprint of the public key
 * @property string|null $fingerprint_sha256 SHA256 Fingerprint of the public key
 * @property string|null $private_key The private key data.
 * @package Files
 */
class SftpHostKey
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
     * If true, use this SFTP Host Key.
     *
     * @return bool|null
     */
    public function getActive()
    {
        return isset($this->attributes['active']) ? $this->attributes['active'] : null;
    }

    /**
     * If true, use this SFTP Host Key.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setActive($value)
    {
        return $this->attributes['active'] = $value;
    }
    /**
     * Custom Domain ID. If set, this key is used only for that Custom Domain.
     *
     * @return int|null
     */
    public function getCustomDomainId()
    {
        return isset($this->attributes['custom_domain_id']) ? $this->attributes['custom_domain_id'] : null;
    }

    /**
     * Custom Domain ID. If set, this key is used only for that Custom Domain.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCustomDomainId($value)
    {
        return $this->attributes['custom_domain_id'] = $value;
    }
    /**
     * SFTP Host Key ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * SFTP Host Key ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * The friendly name of this SFTP Host Key.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The friendly name of this SFTP Host Key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * SSH key type
     *
     * @return string|null
     */
    public function getKeyType()
    {
        return isset($this->attributes['key_type']) ? $this->attributes['key_type'] : null;
    }

    /**
     * SSH key type
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setKeyType($value)
    {
        return $this->attributes['key_type'] = $value;
    }
    /**
     * MD5 Fingerprint of the public key
     *
     * @return string|null
     */
    public function getFingerprintMd5()
    {
        return isset($this->attributes['fingerprint_md5']) ? $this->attributes['fingerprint_md5'] : null;
    }

    /**
     * MD5 Fingerprint of the public key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFingerprintMd5($value)
    {
        return $this->attributes['fingerprint_md5'] = $value;
    }
    /**
     * SHA256 Fingerprint of the public key
     *
     * @return string|null
     */
    public function getFingerprintSha256()
    {
        return isset($this->attributes['fingerprint_sha256']) ? $this->attributes['fingerprint_sha256'] : null;
    }

    /**
     * SHA256 Fingerprint of the public key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFingerprintSha256($value)
    {
        return $this->attributes['fingerprint_sha256'] = $value;
    }
    /**
     * The private key data.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    /**
     * The private key data.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }

    /**
     * Update SFTP Host Key
     *
     * PATCH /sftp_host_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sftp Host Key ID.
     *     - `active` (bool, optional): If true, use this SFTP Host Key.
     *     - `custom_domain_id` (int, optional): Custom Domain ID. If set, this key is used only for that Custom Domain.
     *     - `name` (string, optional): The friendly name of this SFTP Host Key.
     *     - `private_key` (string, optional): The private key data.
     * @return \Files\Model\SftpHostKey
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

        if (!empty($params['custom_domain_id']) && !is_int($params['custom_domain_id'])) {
            throw new \Files\Exception\InvalidParameterException('$custom_domain_id must be of type int; received ' . gettype(@$params['custom_domain_id']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        $response = Api::sendRequest('/sftp_host_keys/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new SftpHostKey((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete SFTP Host Key
     *
     * DELETE /sftp_host_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sftp Host Key ID.
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

        $response = Api::sendRequest('/sftp_host_keys/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete SFTP Host Key
     *
     * DELETE /sftp_host_keys/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sftp Host Key ID.
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
     * List SFTP Host Keys
     *
     * GET /sftp_host_keys
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SftpHostKey[]
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

        $response = Api::sendRequest('/sftp_host_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new SftpHostKey((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show SFTP Host Key
     *
     * GET /sftp_host_keys/{id}
     *
     * @param int $id Sftp Host Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SftpHostKey
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

        $response = Api::sendRequest('/sftp_host_keys/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SftpHostKey((array) (@$response->data ?: []), $options);
    }
    /**
     * Show SFTP Host Key
     *
     * GET /sftp_host_keys/{id}
     *
     * Alias of find().
     *
     * @param int $id Sftp Host Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SftpHostKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create SFTP Host Key
     *
     * POST /sftp_host_keys
     *
     * @param array $params Request parameters.
     *     - `active` (bool, optional): If true, use this SFTP Host Key.
     *     - `custom_domain_id` (int, optional): Custom Domain ID. If set, this key is used only for that Custom Domain.
     *     - `name` (string, optional): The friendly name of this SFTP Host Key.
     *     - `private_key` (string, optional): The private key data.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SftpHostKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (!empty($params['custom_domain_id']) && !is_int($params['custom_domain_id'])) {
            throw new \Files\Exception\InvalidParameterException('$custom_domain_id must be of type int; received ' . gettype(@$params['custom_domain_id']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        $response = Api::sendRequest('/sftp_host_keys', 'POST', $params, $options);

        return new SftpHostKey((array) (@$response->data ?: []), $options);
    }
}
