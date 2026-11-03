<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AS2Station is a remote AS2 server that can send data into Files.com and receive data from Files.com.
 *
 * @property int|null $id Id of the AS2 Station.
 * @property int|null $workspace_id ID of the Workspace associated with this AS2 Station.
 * @property string|null $name The station's formal AS2 name, unique across all Workspaces on the Site.
 * @property string|null $uri Public URI for sending AS2 message to.
 * @property string|null $domain The station's AS2 domain name.
 * @property string|null $hex_public_certificate_serial Serial of public certificate used for message security in hex format.
 * @property string|null $public_certificate_md5 MD5 hash of public certificate used for message security.
 * @property string|null $public_certificate Public certificate used for message security.
 * @property string|null $private_key_md5 MD5 hash of private key used for message security.
 * @property string|null $public_certificate_subject Subject of public certificate used for message security.
 * @property string|null $public_certificate_issuer Issuer of public certificate used for message security.
 * @property string|null $public_certificate_serial Serial of public certificate used for message security.
 * @property string|null $public_certificate_not_before Not before value of public certificate used for message security.
 * @property string|null $public_certificate_not_after Not after value of public certificate used for message security.
 * @property string|null $private_key_password_md5 MD5 hash of private key password used for message security.
 * @property string|null $private_key PEM-encoded private key matching public_certificate.
 * @property string|null $private_key_password Password for the PEM-encoded private key.
 * @property string|null $pkcs12 Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
 * @property string|null $pkcs12_password Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
 * @package Files
 */
class As2Station
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
     * Id of the AS2 Station.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Id of the AS2 Station.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * ID of the Workspace associated with this AS2 Station.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * ID of the Workspace associated with this AS2 Station.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * The station's formal AS2 name, unique across all Workspaces on the Site.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The station's formal AS2 name, unique across all Workspaces on the Site.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Public URI for sending AS2 message to.
     *
     * @return string|null
     */
    public function getUri()
    {
        return isset($this->attributes['uri']) ? $this->attributes['uri'] : null;
    }

    /**
     * Public URI for sending AS2 message to.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUri($value)
    {
        return $this->attributes['uri'] = $value;
    }
    /**
     * The station's AS2 domain name.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->attributes['domain']) ? $this->attributes['domain'] : null;
    }

    /**
     * The station's AS2 domain name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDomain($value)
    {
        return $this->attributes['domain'] = $value;
    }
    /**
     * Serial of public certificate used for message security in hex format.
     *
     * @return string|null
     */
    public function getHexPublicCertificateSerial()
    {
        return isset($this->attributes['hex_public_certificate_serial']) ? $this->attributes['hex_public_certificate_serial'] : null;
    }

    /**
     * Serial of public certificate used for message security in hex format.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHexPublicCertificateSerial($value)
    {
        return $this->attributes['hex_public_certificate_serial'] = $value;
    }
    /**
     * MD5 hash of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateMd5()
    {
        return isset($this->attributes['public_certificate_md5']) ? $this->attributes['public_certificate_md5'] : null;
    }

    /**
     * MD5 hash of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateMd5($value)
    {
        return $this->attributes['public_certificate_md5'] = $value;
    }
    /**
     * Public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificate()
    {
        return isset($this->attributes['public_certificate']) ? $this->attributes['public_certificate'] : null;
    }

    /**
     * Public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificate($value)
    {
        return $this->attributes['public_certificate'] = $value;
    }
    /**
     * MD5 hash of private key used for message security.
     *
     * @return string|null
     */
    public function getPrivateKeyMd5()
    {
        return isset($this->attributes['private_key_md5']) ? $this->attributes['private_key_md5'] : null;
    }

    /**
     * MD5 hash of private key used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyMd5($value)
    {
        return $this->attributes['private_key_md5'] = $value;
    }
    /**
     * Subject of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateSubject()
    {
        return isset($this->attributes['public_certificate_subject']) ? $this->attributes['public_certificate_subject'] : null;
    }

    /**
     * Subject of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateSubject($value)
    {
        return $this->attributes['public_certificate_subject'] = $value;
    }
    /**
     * Issuer of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateIssuer()
    {
        return isset($this->attributes['public_certificate_issuer']) ? $this->attributes['public_certificate_issuer'] : null;
    }

    /**
     * Issuer of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateIssuer($value)
    {
        return $this->attributes['public_certificate_issuer'] = $value;
    }
    /**
     * Serial of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateSerial()
    {
        return isset($this->attributes['public_certificate_serial']) ? $this->attributes['public_certificate_serial'] : null;
    }

    /**
     * Serial of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateSerial($value)
    {
        return $this->attributes['public_certificate_serial'] = $value;
    }
    /**
     * Not before value of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateNotBefore()
    {
        return isset($this->attributes['public_certificate_not_before']) ? $this->attributes['public_certificate_not_before'] : null;
    }

    /**
     * Not before value of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateNotBefore($value)
    {
        return $this->attributes['public_certificate_not_before'] = $value;
    }
    /**
     * Not after value of public certificate used for message security.
     *
     * @return string|null
     */
    public function getPublicCertificateNotAfter()
    {
        return isset($this->attributes['public_certificate_not_after']) ? $this->attributes['public_certificate_not_after'] : null;
    }

    /**
     * Not after value of public certificate used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicCertificateNotAfter($value)
    {
        return $this->attributes['public_certificate_not_after'] = $value;
    }
    /**
     * MD5 hash of private key password used for message security.
     *
     * @return string|null
     */
    public function getPrivateKeyPasswordMd5()
    {
        return isset($this->attributes['private_key_password_md5']) ? $this->attributes['private_key_password_md5'] : null;
    }

    /**
     * MD5 hash of private key password used for message security.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyPasswordMd5($value)
    {
        return $this->attributes['private_key_password_md5'] = $value;
    }
    /**
     * PEM-encoded private key matching public_certificate.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    /**
     * PEM-encoded private key matching public_certificate.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    /**
     * Password for the PEM-encoded private key.
     *
     * @return string|null
     */
    public function getPrivateKeyPassword()
    {
        return isset($this->attributes['private_key_password']) ? $this->attributes['private_key_password'] : null;
    }

    /**
     * Password for the PEM-encoded private key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKeyPassword($value)
    {
        return $this->attributes['private_key_password'] = $value;
    }
    /**
     * Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
     *
     * @return string|null
     */
    public function getPkcs12()
    {
        return isset($this->attributes['pkcs12']) ? $this->attributes['pkcs12'] : null;
    }

    /**
     * Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPkcs12($value)
    {
        return $this->attributes['pkcs12'] = $value;
    }
    /**
     * Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
     *
     * @return string|null
     */
    public function getPkcs12Password()
    {
        return isset($this->attributes['pkcs12_password']) ? $this->attributes['pkcs12_password'] : null;
    }

    /**
     * Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPkcs12Password($value)
    {
        return $this->attributes['pkcs12_password'] = $value;
    }

    /**
     * Update AS2 Station
     *
     * PATCH /as2_stations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Station ID.
     *     - `name` (string, optional): The station's formal AS2 name, unique across all Workspaces on the Site.
     *     - `public_certificate` (string, optional): Public certificate used for message security.
     *     - `private_key` (string, optional): PEM-encoded private key matching public_certificate.
     *     - `private_key_password` (string, optional): Password for the PEM-encoded private key.
     *     - `pkcs12` (string, optional): Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's replacement certificate and private key. Provide this instead of public_certificate and private_key.
     *     - `pkcs12_password` (string, optional): Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
     * @return \Files\Model\As2Station
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

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['public_certificate']) && !is_string($params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_password']) && !is_string($params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (!empty($params['pkcs12']) && !is_string($params['pkcs12'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12 must be of type string; received ' . gettype(@$params['pkcs12']));
        }

        if (!empty($params['pkcs12_password']) && !is_string($params['pkcs12_password'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12_password must be of type string; received ' . gettype(@$params['pkcs12_password']));
        }

        $response = Api::sendRequest('/as2_stations/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new As2Station((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete AS2 Station
     *
     * DELETE /as2_stations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Station ID.
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

        $response = Api::sendRequest('/as2_stations/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete AS2 Station
     *
     * DELETE /as2_stations/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Station ID.
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
     * List AS2 Stations
     *
     * GET /as2_stations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Station[]
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

        $response = Api::sendRequest('/as2_stations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2Station((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show AS2 Station
     *
     * GET /as2_stations/{id}
     *
     * @param int $id As2 Station ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Station
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

        $response = Api::sendRequest('/as2_stations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new As2Station((array) (@$response->data ?: []), $options);
    }
    /**
     * Show AS2 Station
     *
     * GET /as2_stations/{id}
     *
     * Alias of find().
     *
     * @param int $id As2 Station ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Station
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create AS2 Station
     *
     * POST /as2_stations
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): The station's formal AS2 name, unique across all Workspaces on the Site.
     *     - `workspace_id` (int, optional): ID of the Workspace associated with this AS2 Station.
     *     - `public_certificate` (string, optional): Public certificate used for message security.
     *     - `private_key` (string, optional): PEM-encoded private key matching public_certificate.
     *     - `private_key_password` (string, optional): Password for the PEM-encoded private key.
     *     - `pkcs12` (string, optional): Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
     *     - `pkcs12_password` (string, optional): Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Station
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['public_certificate']) && !is_string($params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (!empty($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (!empty($params['private_key_password']) && !is_string($params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        if (!empty($params['pkcs12']) && !is_string($params['pkcs12'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12 must be of type string; received ' . gettype(@$params['pkcs12']));
        }

        if (!empty($params['pkcs12_password']) && !is_string($params['pkcs12_password'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12_password must be of type string; received ' . gettype(@$params['pkcs12_password']));
        }

        $response = Api::sendRequest('/as2_stations', 'POST', $params, $options);

        return new As2Station((array) (@$response->data ?: []), $options);
    }
}
