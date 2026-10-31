<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2Station
 *
 * @package Files
 */
class As2Station
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // int64 # Id of the AS2 Station.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # ID of the Workspace associated with this AS2 Station.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # The station's formal AS2 name, unique across all Workspaces on the Site.
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Public URI for sending AS2 message to.
    public function getUri()
    {
        return isset($this->attributes['uri']) ? $this->attributes['uri'] : null;
    }

    public function setUri($value)
    {
        return $this->attributes['uri'] = $value;
    }
    // string # The station's AS2 domain name.
    public function getDomain()
    {
        return isset($this->attributes['domain']) ? $this->attributes['domain'] : null;
    }

    public function setDomain($value)
    {
        return $this->attributes['domain'] = $value;
    }
    // string # Serial of public certificate used for message security in hex format.
    public function getHexPublicCertificateSerial()
    {
        return isset($this->attributes['hex_public_certificate_serial']) ? $this->attributes['hex_public_certificate_serial'] : null;
    }

    public function setHexPublicCertificateSerial($value)
    {
        return $this->attributes['hex_public_certificate_serial'] = $value;
    }
    // string # MD5 hash of public certificate used for message security.
    public function getPublicCertificateMd5()
    {
        return isset($this->attributes['public_certificate_md5']) ? $this->attributes['public_certificate_md5'] : null;
    }

    public function setPublicCertificateMd5($value)
    {
        return $this->attributes['public_certificate_md5'] = $value;
    }
    // string # Public certificate used for message security.
    public function getPublicCertificate()
    {
        return isset($this->attributes['public_certificate']) ? $this->attributes['public_certificate'] : null;
    }

    public function setPublicCertificate($value)
    {
        return $this->attributes['public_certificate'] = $value;
    }
    // string # MD5 hash of private key used for message security.
    public function getPrivateKeyMd5()
    {
        return isset($this->attributes['private_key_md5']) ? $this->attributes['private_key_md5'] : null;
    }

    public function setPrivateKeyMd5($value)
    {
        return $this->attributes['private_key_md5'] = $value;
    }
    // string # Subject of public certificate used for message security.
    public function getPublicCertificateSubject()
    {
        return isset($this->attributes['public_certificate_subject']) ? $this->attributes['public_certificate_subject'] : null;
    }

    public function setPublicCertificateSubject($value)
    {
        return $this->attributes['public_certificate_subject'] = $value;
    }
    // string # Issuer of public certificate used for message security.
    public function getPublicCertificateIssuer()
    {
        return isset($this->attributes['public_certificate_issuer']) ? $this->attributes['public_certificate_issuer'] : null;
    }

    public function setPublicCertificateIssuer($value)
    {
        return $this->attributes['public_certificate_issuer'] = $value;
    }
    // string # Serial of public certificate used for message security.
    public function getPublicCertificateSerial()
    {
        return isset($this->attributes['public_certificate_serial']) ? $this->attributes['public_certificate_serial'] : null;
    }

    public function setPublicCertificateSerial($value)
    {
        return $this->attributes['public_certificate_serial'] = $value;
    }
    // string # Not before value of public certificate used for message security.
    public function getPublicCertificateNotBefore()
    {
        return isset($this->attributes['public_certificate_not_before']) ? $this->attributes['public_certificate_not_before'] : null;
    }

    public function setPublicCertificateNotBefore($value)
    {
        return $this->attributes['public_certificate_not_before'] = $value;
    }
    // string # Not after value of public certificate used for message security.
    public function getPublicCertificateNotAfter()
    {
        return isset($this->attributes['public_certificate_not_after']) ? $this->attributes['public_certificate_not_after'] : null;
    }

    public function setPublicCertificateNotAfter($value)
    {
        return $this->attributes['public_certificate_not_after'] = $value;
    }
    // string # MD5 hash of private key password used for message security.
    public function getPrivateKeyPasswordMd5()
    {
        return isset($this->attributes['private_key_password_md5']) ? $this->attributes['private_key_password_md5'] : null;
    }

    public function setPrivateKeyPasswordMd5($value)
    {
        return $this->attributes['private_key_password_md5'] = $value;
    }
    // string # PEM-encoded private key matching public_certificate.
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    // string # Password for the PEM-encoded private key.
    public function getPrivateKeyPassword()
    {
        return isset($this->attributes['private_key_password']) ? $this->attributes['private_key_password'] : null;
    }

    public function setPrivateKeyPassword($value)
    {
        return $this->attributes['private_key_password'] = $value;
    }
    // string # Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
    public function getPkcs12()
    {
        return isset($this->attributes['pkcs12']) ? $this->attributes['pkcs12'] : null;
    }

    public function setPkcs12($value)
    {
        return $this->attributes['pkcs12'] = $value;
    }
    // string # Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
    public function getPkcs12Password()
    {
        return isset($this->attributes['pkcs12_password']) ? $this->attributes['pkcs12_password'] : null;
    }

    public function setPkcs12Password($value)
    {
        return $this->attributes['pkcs12_password'] = $value;
    }

    // Parameters:
    //   name - string - The station's formal AS2 name, unique across all Workspaces on the Site.
    //   public_certificate - string - Public certificate used for message security.
    //   private_key - string - PEM-encoded private key matching public_certificate.
    //   private_key_password - string - Password for the PEM-encoded private key.
    //   pkcs12 - string - Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's replacement certificate and private key. Provide this instead of public_certificate and private_key.
    //   pkcs12_password - string - Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
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

    // Parameters:
    //   id (required) - int64 - As2 Station ID.
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
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name (required) - string - The station's formal AS2 name, unique across all Workspaces on the Site.
    //   workspace_id - int64 - ID of the Workspace associated with this AS2 Station.
    //   public_certificate - string - Public certificate used for message security.
    //   private_key - string - PEM-encoded private key matching public_certificate.
    //   private_key_password - string - Password for the PEM-encoded private key.
    //   pkcs12 - string - Base64-encoded PKCS#12 (.pfx or .p12) file containing the identity's certificate and private key. Provide this instead of public_certificate and private_key.
    //   pkcs12_password - string - Password for pkcs12. The file and password are used only for import; the extracted certificate and private key are stored as PEM.
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
