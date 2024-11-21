<?php

declare(strict_types=1);

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
    // int64 # Id of the AS2 Station.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # The station's formal AS2 name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Public URI for sending AS2 message to.
    public function getUri()
    {
        return @$this->attributes['uri'];
    }

    public function setUri($value)
    {
        return $this->attributes['uri'] = $value;
    }
    // string # The station's AS2 domain name.
    public function getDomain()
    {
        return @$this->attributes['domain'];
    }

    public function setDomain($value)
    {
        return $this->attributes['domain'] = $value;
    }
    // string # Serial of public certificate used for message security in hex format.
    public function getHexPublicCertificateSerial()
    {
        return @$this->attributes['hex_public_certificate_serial'];
    }

    public function setHexPublicCertificateSerial($value)
    {
        return $this->attributes['hex_public_certificate_serial'] = $value;
    }
    // string # MD5 hash of public certificate used for message security.
    public function getPublicCertificateMd5()
    {
        return @$this->attributes['public_certificate_md5'];
    }

    public function setPublicCertificateMd5($value)
    {
        return $this->attributes['public_certificate_md5'] = $value;
    }
    // string # MD5 hash of private key used for message security.
    public function getPrivateKeyMd5()
    {
        return @$this->attributes['private_key_md5'];
    }

    public function setPrivateKeyMd5($value)
    {
        return $this->attributes['private_key_md5'] = $value;
    }
    // string # Subject of public certificate used for message security.
    public function getPublicCertificateSubject()
    {
        return @$this->attributes['public_certificate_subject'];
    }

    public function setPublicCertificateSubject($value)
    {
        return $this->attributes['public_certificate_subject'] = $value;
    }
    // string # Issuer of public certificate used for message security.
    public function getPublicCertificateIssuer()
    {
        return @$this->attributes['public_certificate_issuer'];
    }

    public function setPublicCertificateIssuer($value)
    {
        return $this->attributes['public_certificate_issuer'] = $value;
    }
    // string # Serial of public certificate used for message security.
    public function getPublicCertificateSerial()
    {
        return @$this->attributes['public_certificate_serial'];
    }

    public function setPublicCertificateSerial($value)
    {
        return $this->attributes['public_certificate_serial'] = $value;
    }
    // string # Not before value of public certificate used for message security.
    public function getPublicCertificateNotBefore()
    {
        return @$this->attributes['public_certificate_not_before'];
    }

    public function setPublicCertificateNotBefore($value)
    {
        return $this->attributes['public_certificate_not_before'] = $value;
    }
    // string # Not after value of public certificate used for message security.
    public function getPublicCertificateNotAfter()
    {
        return @$this->attributes['public_certificate_not_after'];
    }

    public function setPublicCertificateNotAfter($value)
    {
        return $this->attributes['public_certificate_not_after'] = $value;
    }
    // string # MD5 hash of private key password used for message security.
    public function getPrivateKeyPasswordMd5()
    {
        return @$this->attributes['private_key_password_md5'];
    }

    public function setPrivateKeyPasswordMd5($value)
    {
        return $this->attributes['private_key_password_md5'] = $value;
    }
    // string
    public function getPublicCertificate()
    {
        return @$this->attributes['public_certificate'];
    }

    public function setPublicCertificate($value)
    {
        return $this->attributes['public_certificate'] = $value;
    }
    // string
    public function getPrivateKey()
    {
        return @$this->attributes['private_key'];
    }

    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    // string
    public function getPrivateKeyPassword()
    {
        return @$this->attributes['private_key_password'];
    }

    public function setPrivateKeyPassword($value)
    {
        return $this->attributes['private_key_password'] = $value;
    }

    // Parameters:
    //   name - string - AS2 Name
    //   public_certificate - string
    //   private_key - string
    //   private_key_password - string
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

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_password'] && !is_string(@$params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        $response = Api::sendRequest('/as2_stations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new As2Station((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/as2_stations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
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

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/as2_stations/' . @$params['id'] . '', 'GET', $params, $options);

        return new As2Station((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name (required) - string - AS2 Name
    //   public_certificate (required) - string
    //   private_key (required) - string
    //   private_key_password - string
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!@$params['public_certificate']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: public_certificate');
        }

        if (!@$params['private_key']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: private_key');
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (@$params['private_key'] && !is_string(@$params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (@$params['private_key_password'] && !is_string(@$params['private_key_password'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key_password must be of type string; received ' . gettype(@$params['private_key_password']));
        }

        $response = Api::sendRequest('/as2_stations', 'POST', $params, $options);

        return new As2Station((array) (@$response->data ?: []), $options);
    }

    public static function createExport($params = [], $options = [])
    {
        $response = Api::sendRequest('/as2_stations/create_export', 'POST', $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }
}
