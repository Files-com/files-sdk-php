<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2Partner
 *
 * @package Files
 */
class As2Partner
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
    // int64 # ID of the AS2 Partner.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # ID of the AS2 Station associated with this partner.
    public function getAs2StationId()
    {
        return @$this->attributes['as2_station_id'];
    }

    public function setAs2StationId($value)
    {
        return $this->attributes['as2_station_id'] = $value;
    }
    // string # The partner's formal AS2 name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Public URI where we will send the AS2 messages (via HTTP/HTTPS).
    public function getUri()
    {
        return @$this->attributes['uri'];
    }

    public function setUri($value)
    {
        return $this->attributes['uri'] = $value;
    }
    // string # Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS?
    public function getServerCertificate()
    {
        return @$this->attributes['server_certificate'];
    }

    public function setServerCertificate($value)
    {
        return $this->attributes['server_certificate'] = $value;
    }
    // string # Username to send to server for HTTP Authentication.
    public function getHttpAuthUsername()
    {
        return @$this->attributes['http_auth_username'];
    }

    public function setHttpAuthUsername($value)
    {
        return $this->attributes['http_auth_username'] = $value;
    }
    // object # Additional HTTP Headers for outgoing message sent to this partner.
    public function getAdditionalHttpHeaders()
    {
        return @$this->attributes['additional_http_headers'];
    }

    public function setAdditionalHttpHeaders($value)
    {
        return $this->attributes['additional_http_headers'] = $value;
    }
    // string # Default mime type of the file attached to the encrypted message
    public function getDefaultMimeType()
    {
        return @$this->attributes['default_mime_type'];
    }

    public function setDefaultMimeType($value)
    {
        return $this->attributes['default_mime_type'] = $value;
    }
    // string # How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates.
    public function getMdnValidationLevel()
    {
        return @$this->attributes['mdn_validation_level'];
    }

    public function setMdnValidationLevel($value)
    {
        return $this->attributes['mdn_validation_level'] = $value;
    }
    // boolean # If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
    public function getEnableDedicatedIps()
    {
        return @$this->attributes['enable_dedicated_ips'];
    }

    public function setEnableDedicatedIps($value)
    {
        return $this->attributes['enable_dedicated_ips'] = $value;
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
    // string # Password to send to server for HTTP Authentication.
    public function getHttpAuthPassword()
    {
        return @$this->attributes['http_auth_password'];
    }

    public function setHttpAuthPassword($value)
    {
        return $this->attributes['http_auth_password'] = $value;
    }
    // string # Public certificate for AS2 Partner.  Note: This is the certificate for AS2 message security, not a certificate used for HTTPS authentication.
    public function getPublicCertificate()
    {
        return @$this->attributes['public_certificate'];
    }

    public function setPublicCertificate($value)
    {
        return $this->attributes['public_certificate'] = $value;
    }

    // Parameters:
    //   enable_dedicated_ips - boolean - If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
    //   http_auth_username - string - Username to send to server for HTTP Authentication.
    //   http_auth_password - string - Password to send to server for HTTP Authentication.
    //   mdn_validation_level - string - How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates.
    //   server_certificate - string - Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS?
    //   default_mime_type - string - Default mime type of the file attached to the encrypted message
    //   additional_http_headers - object - Additional HTTP Headers for outgoing message sent to this partner.
    //   name - string - The partner's formal AS2 name.
    //   uri - string - Public URI where we will send the AS2 messages (via HTTP/HTTPS).
    //   public_certificate - string - Public certificate for AS2 Partner.  Note: This is the certificate for AS2 message security, not a certificate used for HTTPS authentication.
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

        if (@$params['http_auth_username'] && !is_string(@$params['http_auth_username'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_username must be of type string; received ' . gettype(@$params['http_auth_username']));
        }

        if (@$params['http_auth_password'] && !is_string(@$params['http_auth_password'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_password must be of type string; received ' . gettype(@$params['http_auth_password']));
        }

        if (@$params['mdn_validation_level'] && !is_string(@$params['mdn_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$mdn_validation_level must be of type string; received ' . gettype(@$params['mdn_validation_level']));
        }

        if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (@$params['default_mime_type'] && !is_string(@$params['default_mime_type'])) {
            throw new \Files\Exception\InvalidParameterException('$default_mime_type must be of type string; received ' . gettype(@$params['default_mime_type']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['uri'] && !is_string(@$params['uri'])) {
            throw new \Files\Exception\InvalidParameterException('$uri must be of type string; received ' . gettype(@$params['uri']));
        }

        if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new As2Partner((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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

        $response = Api::sendRequest('/as2_partners', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2Partner((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - As2 Partner ID.
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

        $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'GET', $params, $options);

        return new As2Partner((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   enable_dedicated_ips - boolean - If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
    //   http_auth_username - string - Username to send to server for HTTP Authentication.
    //   http_auth_password - string - Password to send to server for HTTP Authentication.
    //   mdn_validation_level - string - How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates.
    //   server_certificate - string - Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS?
    //   default_mime_type - string - Default mime type of the file attached to the encrypted message
    //   additional_http_headers - object - Additional HTTP Headers for outgoing message sent to this partner.
    //   as2_station_id (required) - int64 - ID of the AS2 Station associated with this partner.
    //   name (required) - string - The partner's formal AS2 name.
    //   uri (required) - string - Public URI where we will send the AS2 messages (via HTTP/HTTPS).
    //   public_certificate (required) - string - Public certificate for AS2 Partner.  Note: This is the certificate for AS2 message security, not a certificate used for HTTPS authentication.
    public static function create($params = [], $options = [])
    {
        if (!@$params['as2_station_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: as2_station_id');
        }

        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!@$params['uri']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: uri');
        }

        if (!@$params['public_certificate']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: public_certificate');
        }

        if (@$params['http_auth_username'] && !is_string(@$params['http_auth_username'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_username must be of type string; received ' . gettype(@$params['http_auth_username']));
        }

        if (@$params['http_auth_password'] && !is_string(@$params['http_auth_password'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_password must be of type string; received ' . gettype(@$params['http_auth_password']));
        }

        if (@$params['mdn_validation_level'] && !is_string(@$params['mdn_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$mdn_validation_level must be of type string; received ' . gettype(@$params['mdn_validation_level']));
        }

        if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (@$params['default_mime_type'] && !is_string(@$params['default_mime_type'])) {
            throw new \Files\Exception\InvalidParameterException('$default_mime_type must be of type string; received ' . gettype(@$params['default_mime_type']));
        }

        if (@$params['as2_station_id'] && !is_int(@$params['as2_station_id'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_station_id must be of type int; received ' . gettype(@$params['as2_station_id']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['uri'] && !is_string(@$params['uri'])) {
            throw new \Files\Exception\InvalidParameterException('$uri must be of type string; received ' . gettype(@$params['uri']));
        }

        if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        $response = Api::sendRequest('/as2_partners', 'POST', $params, $options);

        return new As2Partner((array) (@$response->data ?: []), $options);
    }

    public static function createExport($params = [], $options = [])
    {
        $response = Api::sendRequest('/as2_partners/create_export', 'POST', $options);

        return new Export((array) (@$response->data ?: []), $options);
    }
}
