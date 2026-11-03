<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AS2Partner is a counterparty of the Files.com site's AS2 connectivity.
 *
 * @property int|null $id ID of the AS2 Partner.
 * @property int|null $workspace_id ID of the Workspace associated with this AS2 Partner.
 * @property int|null $as2_station_id ID of the AS2 Station associated with this partner.
 * @property string|null $name The partner's formal AS2 name.
 * @property string|null $uri Public URI where we will send the AS2 messages (via HTTP/HTTPS).
 * @property string|null $server_certificate Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS? (This only applies to Outgoing AS2 message from Files.com to a Partner.)
 * @property string|null $http_auth_username Username to send to server for HTTP Authentication.
 * @property object|null $additional_http_headers Additional HTTP Headers for outgoing message sent to this partner.
 * @property string|null $default_mime_type Default mime type of the file attached to the encrypted message
 * @property string|null $mdn_validation_level How should Files.com evaluate message transfer success based on a partner's MDN response? This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates. `auto`: Automatically set the correct value for this setting based on next mdn received.
 * @property string|null $signature_validation_level Should Files.com require signatures on incoming AS2 messages? `normal`: require that incoming messages are signed with a valid matching signature. `none`: Unsigned incoming messages are allowed. `auto`: A one-time starting state. The next message received is always accepted and switches this to `normal` if it carries a valid signature, or `none` if it does not.
 * @property bool|null $enable_dedicated_ips If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
 * @property string|null $hex_public_certificate_serial Serial of public certificate used for message security in hex format.
 * @property string|null $public_certificate Public certificate used for message security.
 * @property string|null $public_certificate_md5 MD5 hash of public certificate used for message security.
 * @property string|null $public_certificate_subject Subject of public certificate used for message security.
 * @property string|null $public_certificate_issuer Issuer of public certificate used for message security.
 * @property string|null $public_certificate_serial Serial of public certificate used for message security.
 * @property string|null $public_certificate_not_before Not before value of public certificate used for message security.
 * @property string|null $public_certificate_not_after Not after value of public certificate used for message security.
 * @property string|null $http_auth_password Password to send to server for HTTP Authentication.
 * @property string|null $pkcs12 Base64-encoded PKCS#12 (.pfx or .p12) file containing the partner's public certificate. Provide this instead of public_certificate. Any private key in the file is discarded. A public-only file must contain exactly one certificate.
 * @property string|null $pkcs12_password Password for pkcs12. The file and password are used only for import; the extracted public certificate is stored as PEM.
 * @package Files
 */
class As2Partner
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
     * ID of the AS2 Partner.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * ID of the AS2 Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * ID of the Workspace associated with this AS2 Partner.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * ID of the Workspace associated with this AS2 Partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * ID of the AS2 Station associated with this partner.
     *
     * @return int|null
     */
    public function getAs2StationId()
    {
        return isset($this->attributes['as2_station_id']) ? $this->attributes['as2_station_id'] : null;
    }

    /**
     * ID of the AS2 Station associated with this partner.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAs2StationId($value)
    {
        return $this->attributes['as2_station_id'] = $value;
    }
    /**
     * The partner's formal AS2 name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The partner's formal AS2 name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Public URI where we will send the AS2 messages (via HTTP/HTTPS).
     *
     * @return string|null
     */
    public function getUri()
    {
        return isset($this->attributes['uri']) ? $this->attributes['uri'] : null;
    }

    /**
     * Public URI where we will send the AS2 messages (via HTTP/HTTPS).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUri($value)
    {
        return $this->attributes['uri'] = $value;
    }
    /**
     * Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS? (This only applies to Outgoing AS2 message from Files.com to a Partner.)
     *
     * @return string|null
     */
    public function getServerCertificate()
    {
        return isset($this->attributes['server_certificate']) ? $this->attributes['server_certificate'] : null;
    }

    /**
     * Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS? (This only applies to Outgoing AS2 message from Files.com to a Partner.)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setServerCertificate($value)
    {
        return $this->attributes['server_certificate'] = $value;
    }
    /**
     * Username to send to server for HTTP Authentication.
     *
     * @return string|null
     */
    public function getHttpAuthUsername()
    {
        return isset($this->attributes['http_auth_username']) ? $this->attributes['http_auth_username'] : null;
    }

    /**
     * Username to send to server for HTTP Authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHttpAuthUsername($value)
    {
        return $this->attributes['http_auth_username'] = $value;
    }
    /**
     * Additional HTTP Headers for outgoing message sent to this partner.
     *
     * @return object|null
     */
    public function getAdditionalHttpHeaders()
    {
        return isset($this->attributes['additional_http_headers']) ? $this->attributes['additional_http_headers'] : null;
    }

    /**
     * Additional HTTP Headers for outgoing message sent to this partner.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setAdditionalHttpHeaders($value)
    {
        return $this->attributes['additional_http_headers'] = $value;
    }
    /**
     * Default mime type of the file attached to the encrypted message
     *
     * @return string|null
     */
    public function getDefaultMimeType()
    {
        return isset($this->attributes['default_mime_type']) ? $this->attributes['default_mime_type'] : null;
    }

    /**
     * Default mime type of the file attached to the encrypted message
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDefaultMimeType($value)
    {
        return $this->attributes['default_mime_type'] = $value;
    }
    /**
     * How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates. `auto`: Automatically set the correct value for this setting based on next mdn received.
     *
     * @return string|null
     */
    public function getMdnValidationLevel()
    {
        return isset($this->attributes['mdn_validation_level']) ? $this->attributes['mdn_validation_level'] : null;
    }

    /**
     * How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates. `auto`: Automatically set the correct value for this setting based on next mdn received.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMdnValidationLevel($value)
    {
        return $this->attributes['mdn_validation_level'] = $value;
    }
    /**
     * Should Files.com require signatures on incoming AS2 messages?  `normal`: require that incoming messages are signed with a valid matching signature. `none`: Unsigned incoming messages are allowed. `auto`: A one-time starting state. The next message received is always accepted and switches this to `normal` if it carries a valid signature, or `none` if it does not.
     *
     * @return string|null
     */
    public function getSignatureValidationLevel()
    {
        return isset($this->attributes['signature_validation_level']) ? $this->attributes['signature_validation_level'] : null;
    }

    /**
     * Should Files.com require signatures on incoming AS2 messages?  `normal`: require that incoming messages are signed with a valid matching signature. `none`: Unsigned incoming messages are allowed. `auto`: A one-time starting state. The next message received is always accepted and switches this to `normal` if it carries a valid signature, or `none` if it does not.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSignatureValidationLevel($value)
    {
        return $this->attributes['signature_validation_level'] = $value;
    }
    /**
     * If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
     *
     * @return bool|null
     */
    public function getEnableDedicatedIps()
    {
        return isset($this->attributes['enable_dedicated_ips']) ? $this->attributes['enable_dedicated_ips'] : null;
    }

    /**
     * If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEnableDedicatedIps($value)
    {
        return $this->attributes['enable_dedicated_ips'] = $value;
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
     * Password to send to server for HTTP Authentication.
     *
     * @return string|null
     */
    public function getHttpAuthPassword()
    {
        return isset($this->attributes['http_auth_password']) ? $this->attributes['http_auth_password'] : null;
    }

    /**
     * Password to send to server for HTTP Authentication.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHttpAuthPassword($value)
    {
        return $this->attributes['http_auth_password'] = $value;
    }
    /**
     * Base64-encoded PKCS#12 (.pfx or .p12) file containing the partner's public certificate. Provide this instead of public_certificate. Any private key in the file is discarded. A public-only file must contain exactly one certificate.
     *
     * @return string|null
     */
    public function getPkcs12()
    {
        return isset($this->attributes['pkcs12']) ? $this->attributes['pkcs12'] : null;
    }

    /**
     * Base64-encoded PKCS#12 (.pfx or .p12) file containing the partner's public certificate. Provide this instead of public_certificate. Any private key in the file is discarded. A public-only file must contain exactly one certificate.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPkcs12($value)
    {
        return $this->attributes['pkcs12'] = $value;
    }
    /**
     * Password for pkcs12. The file and password are used only for import; the extracted public certificate is stored as PEM.
     *
     * @return string|null
     */
    public function getPkcs12Password()
    {
        return isset($this->attributes['pkcs12_password']) ? $this->attributes['pkcs12_password'] : null;
    }

    /**
     * Password for pkcs12. The file and password are used only for import; the extracted public certificate is stored as PEM.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPkcs12Password($value)
    {
        return $this->attributes['pkcs12_password'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // As2Partner with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update AS2 Partner
     *
     * PATCH /as2_partners/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Partner ID.
     *     - `enable_dedicated_ips` (bool, optional): If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
     *     - `http_auth_username` (string, optional): Username to send to server for HTTP Authentication.
     *     - `http_auth_password` (string, optional): Password to send to server for HTTP Authentication.
     *     - `mdn_validation_level` (string, optional): How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates. `auto`: Automatically set the correct value for this setting based on next mdn received. Allowed values: none, weak, normal, strict, auto.
     *     - `signature_validation_level` (string, optional): Should Files.com require signatures on incoming AS2 messages?  `normal`: require that incoming messages are signed with a valid matching signature. `none`: Unsigned incoming messages are allowed. `auto`: A one-time starting state. The next message received is always accepted and switches this to `normal` if it carries a valid signature, or `none` if it does not. Allowed values: normal, none, auto.
     *     - `server_certificate` (string, optional): Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS? (This only applies to Outgoing AS2 message from Files.com to a Partner.) Allowed values: require_match, allow_any.
     *     - `default_mime_type` (string, optional): Default mime type of the file attached to the encrypted message
     *     - `additional_http_headers` (array|object, optional): Additional HTTP Headers for outgoing message sent to this partner.
     *     - `name` (string, optional): The partner's formal AS2 name.
     *     - `uri` (string, optional): Public URI where we will send the AS2 messages (via HTTP/HTTPS).
     *     - `public_certificate` (string, optional): Public certificate for AS2 Partner.  Note: This is the certificate for AS2 message security, not a certificate used for HTTPS authentication.
     *     - `pkcs12` (string, optional): Base64-encoded PKCS#12 (.pfx or .p12) file containing the partner's public certificate. Provide this instead of public_certificate. Any private key in the file is discarded. A public-only file must contain exactly one certificate.
     *     - `pkcs12_password` (string, optional): Password for pkcs12. The file and password are used only for import; the extracted public certificate is stored as PEM.
     * @return \Files\Model\As2Partner
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['enable_dedicated_ips']) && !is_scalar($params['enable_dedicated_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$enable_dedicated_ips must be of type boolean; received ' . gettype(@$params['enable_dedicated_ips']));
        }

        if (isset($params['http_auth_username']) && !is_string($params['http_auth_username'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_username must be of type string; received ' . gettype(@$params['http_auth_username']));
        }

        if (isset($params['http_auth_password']) && !is_string($params['http_auth_password'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_password must be of type string; received ' . gettype(@$params['http_auth_password']));
        }

        if (isset($params['mdn_validation_level']) && !is_string($params['mdn_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$mdn_validation_level must be of type string; received ' . gettype(@$params['mdn_validation_level']));
        }

        if (isset($params['signature_validation_level']) && !is_string($params['signature_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$signature_validation_level must be of type string; received ' . gettype(@$params['signature_validation_level']));
        }

        if (isset($params['server_certificate']) && !is_string($params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (isset($params['default_mime_type']) && !is_string($params['default_mime_type'])) {
            throw new \Files\Exception\InvalidParameterException('$default_mime_type must be of type string; received ' . gettype(@$params['default_mime_type']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['uri']) && !is_string($params['uri'])) {
            throw new \Files\Exception\InvalidParameterException('$uri must be of type string; received ' . gettype(@$params['uri']));
        }

        if (isset($params['public_certificate']) && !is_string($params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (isset($params['pkcs12']) && !is_string($params['pkcs12'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12 must be of type string; received ' . gettype(@$params['pkcs12']));
        }

        if (isset($params['pkcs12_password']) && !is_string($params['pkcs12_password'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12_password must be of type string; received ' . gettype(@$params['pkcs12_password']));
        }

        $response = Api::sendRequest('/as2_partners/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new As2Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete AS2 Partner
     *
     * DELETE /as2_partners/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Partner ID.
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/as2_partners/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete AS2 Partner
     *
     * DELETE /as2_partners/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Partner ID.
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
     * List AS2 Partners
     *
     * GET /as2_partners
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `as2_station_id` or `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `as2_station_id` and `workspace_id`. Valid field combinations are `[ workspace_id, as2_station_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Partner[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/as2_partners', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new As2Partner(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/as2_partners', $params, $options, As2Partner::class);
    }

    /**
     * Show AS2 Partner
     *
     * GET /as2_partners/{id}
     *
     * @param int $id As2 Partner ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Partner
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/as2_partners/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new As2Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show AS2 Partner
     *
     * GET /as2_partners/{id}
     *
     * Alias of find().
     *
     * @param int $id As2 Partner ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Partner
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create AS2 Partner
     *
     * POST /as2_partners
     *
     * @param array $params Request parameters.
     *     - `enable_dedicated_ips` (bool, optional): If `true`, we will use your site's dedicated IPs for all outbound connections to this AS2 Partner.
     *     - `http_auth_username` (string, optional): Username to send to server for HTTP Authentication.
     *     - `http_auth_password` (string, optional): Password to send to server for HTTP Authentication.
     *     - `mdn_validation_level` (string, optional): How should Files.com evaluate message transfer success based on a partner's MDN response?  This setting does not affect MDN storage; all MDNs received from a partner are always stored. `none`: MDN is stored for informational purposes only, a successful HTTPS transfer is a successful AS2 transfer. `weak`: Inspect the MDN for MIC and Disposition only. `normal`: `weak` plus validate MDN signature matches body, `strict`: `normal` but do not allow signatures from self-signed or incorrectly purposed certificates. `auto`: Automatically set the correct value for this setting based on next mdn received. Allowed values: none, weak, normal, strict, auto.
     *     - `signature_validation_level` (string, optional): Should Files.com require signatures on incoming AS2 messages?  `normal`: require that incoming messages are signed with a valid matching signature. `none`: Unsigned incoming messages are allowed. `auto`: A one-time starting state. The next message received is always accepted and switches this to `normal` if it carries a valid signature, or `none` if it does not. Allowed values: normal, none, auto.
     *     - `server_certificate` (string, optional): Should we require that the remote HTTP server have a valid SSL Certificate for HTTPS? (This only applies to Outgoing AS2 message from Files.com to a Partner.) Allowed values: require_match, allow_any.
     *     - `default_mime_type` (string, optional): Default mime type of the file attached to the encrypted message
     *     - `additional_http_headers` (array|object, optional): Additional HTTP Headers for outgoing message sent to this partner.
     *     - `as2_station_id` (int, required): ID of the AS2 Station associated with this partner.
     *     - `name` (string, required): The partner's formal AS2 name.
     *     - `uri` (string, required): Public URI where we will send the AS2 messages (via HTTP/HTTPS).
     *     - `public_certificate` (string, optional): Public certificate for AS2 Partner.  Note: This is the certificate for AS2 message security, not a certificate used for HTTPS authentication.
     *     - `pkcs12` (string, optional): Base64-encoded PKCS#12 (.pfx or .p12) file containing the partner's public certificate. Provide this instead of public_certificate. Any private key in the file is discarded. A public-only file must contain exactly one certificate.
     *     - `pkcs12_password` (string, optional): Password for pkcs12. The file and password are used only for import; the extracted public certificate is stored as PEM.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2Partner
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['as2_station_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: as2_station_id');
        }

        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['uri'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: uri');
        }

        if (isset($params['enable_dedicated_ips']) && !is_scalar($params['enable_dedicated_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$enable_dedicated_ips must be of type boolean; received ' . gettype(@$params['enable_dedicated_ips']));
        }

        if (isset($params['http_auth_username']) && !is_string($params['http_auth_username'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_username must be of type string; received ' . gettype(@$params['http_auth_username']));
        }

        if (isset($params['http_auth_password']) && !is_string($params['http_auth_password'])) {
            throw new \Files\Exception\InvalidParameterException('$http_auth_password must be of type string; received ' . gettype(@$params['http_auth_password']));
        }

        if (isset($params['mdn_validation_level']) && !is_string($params['mdn_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$mdn_validation_level must be of type string; received ' . gettype(@$params['mdn_validation_level']));
        }

        if (isset($params['signature_validation_level']) && !is_string($params['signature_validation_level'])) {
            throw new \Files\Exception\InvalidParameterException('$signature_validation_level must be of type string; received ' . gettype(@$params['signature_validation_level']));
        }

        if (isset($params['server_certificate']) && !is_string($params['server_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$server_certificate must be of type string; received ' . gettype(@$params['server_certificate']));
        }

        if (isset($params['default_mime_type']) && !is_string($params['default_mime_type'])) {
            throw new \Files\Exception\InvalidParameterException('$default_mime_type must be of type string; received ' . gettype(@$params['default_mime_type']));
        }

        if (isset($params['as2_station_id']) && !is_int($params['as2_station_id'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_station_id must be of type int; received ' . gettype(@$params['as2_station_id']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['uri']) && !is_string($params['uri'])) {
            throw new \Files\Exception\InvalidParameterException('$uri must be of type string; received ' . gettype(@$params['uri']));
        }

        if (isset($params['public_certificate']) && !is_string($params['public_certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$public_certificate must be of type string; received ' . gettype(@$params['public_certificate']));
        }

        if (isset($params['pkcs12']) && !is_string($params['pkcs12'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12 must be of type string; received ' . gettype(@$params['pkcs12']));
        }

        if (isset($params['pkcs12_password']) && !is_string($params['pkcs12_password'])) {
            throw new \Files\Exception\InvalidParameterException('$pkcs12_password must be of type string; received ' . gettype(@$params['pkcs12_password']));
        }

        $response = Api::sendRequest('/as2_partners', 'POST', $params, $options);

        return new As2Partner(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
