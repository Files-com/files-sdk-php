<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Certificate is a way to store your custom SSL certificate for your Files.com site.
 *
 * @property int|null $id Certificate ID
 * @property string|null $name Descriptive name of certificate
 * @property int|null $custom_domain_id Custom Domain that owns this certificate, including while the certificate is inactive.
 * @property string|null $certificate Full text of SSL certificate
 * @property string|null $created_at Certificate created at
 * @property string|null $display_status Certificate status. (One of `Request Generated`, `New`, `Active`, `Active/Expired`, `Expired`, or `Available`)
 * @property string[]|null $domains Domains on this certificate other than files.com domains
 * @property string|null $expires_at Certificate expiration date/time
 * @property bool|null $brick_managed Is this certificate automatically managed and renewed by Files.com?
 * @property string|null $intermediates Intermediate certificates
 * @property string[]|null $ip_addresses A list of IP addresses associated with this SSL Certificate
 * @property string|null $issuer X509 issuer
 * @property string|null $key_type Type of key
 * @property string|null $request Certificate signing request text
 * @property string|null $status Certificate status (Request Generated, New, Active, Active/Expired, Expired, or Available)
 * @property string|null $subject X509 Subject name
 * @property string|null $certificate_domain Domain for certificate.
 * @property string[]|null $certificate_extra_domains Additional domains for the certificate CSR.
 * @property string|null $certificate_country Country.
 * @property string|null $certificate_state_or_province State or province.
 * @property string|null $certificate_city_or_locale City or locale.
 * @property string|null $certificate_company_name Company name.
 * @property string|null $csr_ou1 Department name or organization unit 1
 * @property string|null $csr_ou2 Department name or organization unit 2
 * @property string|null $csr_ou3 Department name or organization unit 3
 * @property string|null $certificate_email_address Email address for the certificate owner.
 * @property string|null $private_key Certificate private key.
 * @property string|null $password Certificate password.
 * @package Files
 */
class Certificate
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
     * Certificate ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Certificate ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Descriptive name of certificate
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Descriptive name of certificate
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Custom Domain that owns this certificate, including while the certificate is inactive.
     *
     * @return int|null
     */
    public function getCustomDomainId()
    {
        return isset($this->attributes['custom_domain_id']) ? $this->attributes['custom_domain_id'] : null;
    }

    /**
     * Custom Domain that owns this certificate, including while the certificate is inactive.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCustomDomainId($value)
    {
        return $this->attributes['custom_domain_id'] = $value;
    }
    /**
     * Full text of SSL certificate
     *
     * @return string|null
     */
    public function getCertificate()
    {
        return isset($this->attributes['certificate']) ? $this->attributes['certificate'] : null;
    }

    /**
     * Full text of SSL certificate
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificate($value)
    {
        return $this->attributes['certificate'] = $value;
    }
    /**
     * Certificate created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Certificate status. (One of `Request Generated`, `New`, `Active`, `Active/Expired`, `Expired`, or `Available`)
     *
     * @return string|null
     */
    public function getDisplayStatus()
    {
        return isset($this->attributes['display_status']) ? $this->attributes['display_status'] : null;
    }

    /**
     * Certificate status. (One of `Request Generated`, `New`, `Active`, `Active/Expired`, `Expired`, or `Available`)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDisplayStatus($value)
    {
        return $this->attributes['display_status'] = $value;
    }
    /**
     * Domains on this certificate other than files.com domains
     *
     * @return string[]|null
     */
    public function getDomains()
    {
        return isset($this->attributes['domains']) ? $this->attributes['domains'] : null;
    }

    /**
     * Domains on this certificate other than files.com domains
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setDomains($value)
    {
        return $this->attributes['domains'] = $value;
    }
    /**
     * Certificate expiration date/time
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * Certificate expiration date/time
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * Is this certificate automatically managed and renewed by Files.com?
     *
     * @return bool|null
     */
    public function getBrickManaged()
    {
        return isset($this->attributes['brick_managed']) ? $this->attributes['brick_managed'] : null;
    }

    /**
     * Is this certificate automatically managed and renewed by Files.com?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBrickManaged($value)
    {
        return $this->attributes['brick_managed'] = $value;
    }
    /**
     * Intermediate certificates
     *
     * @return string|null
     */
    public function getIntermediates()
    {
        return isset($this->attributes['intermediates']) ? $this->attributes['intermediates'] : null;
    }

    /**
     * Intermediate certificates
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setIntermediates($value)
    {
        return $this->attributes['intermediates'] = $value;
    }
    /**
     * A list of IP addresses associated with this SSL Certificate
     *
     * @return string[]|null
     */
    public function getIpAddresses()
    {
        return isset($this->attributes['ip_addresses']) ? $this->attributes['ip_addresses'] : null;
    }

    /**
     * A list of IP addresses associated with this SSL Certificate
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setIpAddresses($value)
    {
        return $this->attributes['ip_addresses'] = $value;
    }
    /**
     * X509 issuer
     *
     * @return string|null
     */
    public function getIssuer()
    {
        return isset($this->attributes['issuer']) ? $this->attributes['issuer'] : null;
    }

    /**
     * X509 issuer
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setIssuer($value)
    {
        return $this->attributes['issuer'] = $value;
    }
    /**
     * Type of key
     *
     * @return string|null
     */
    public function getKeyType()
    {
        return isset($this->attributes['key_type']) ? $this->attributes['key_type'] : null;
    }

    /**
     * Type of key
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setKeyType($value)
    {
        return $this->attributes['key_type'] = $value;
    }
    /**
     * Certificate signing request text
     *
     * @return string|null
     */
    public function getRequest()
    {
        return isset($this->attributes['request']) ? $this->attributes['request'] : null;
    }

    /**
     * Certificate signing request text
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRequest($value)
    {
        return $this->attributes['request'] = $value;
    }
    /**
     * Certificate status (Request Generated, New, Active, Active/Expired, Expired, or Available)
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Certificate status (Request Generated, New, Active, Active/Expired, Expired, or Available)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * X509 Subject name
     *
     * @return string|null
     */
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }

    /**
     * X509 Subject name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSubject($value)
    {
        return $this->attributes['subject'] = $value;
    }
    /**
     * Domain for certificate.
     *
     * @return string|null
     */
    public function getCertificateDomain()
    {
        return isset($this->attributes['certificate_domain']) ? $this->attributes['certificate_domain'] : null;
    }

    /**
     * Domain for certificate.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateDomain($value)
    {
        return $this->attributes['certificate_domain'] = $value;
    }
    /**
     * Additional domains for the certificate CSR.
     *
     * @return string[]|null
     */
    public function getCertificateExtraDomains()
    {
        return isset($this->attributes['certificate_extra_domains']) ? $this->attributes['certificate_extra_domains'] : null;
    }

    /**
     * Additional domains for the certificate CSR.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setCertificateExtraDomains($value)
    {
        return $this->attributes['certificate_extra_domains'] = $value;
    }
    /**
     * Country.
     *
     * @return string|null
     */
    public function getCertificateCountry()
    {
        return isset($this->attributes['certificate_country']) ? $this->attributes['certificate_country'] : null;
    }

    /**
     * Country.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateCountry($value)
    {
        return $this->attributes['certificate_country'] = $value;
    }
    /**
     * State or province.
     *
     * @return string|null
     */
    public function getCertificateStateOrProvince()
    {
        return isset($this->attributes['certificate_state_or_province']) ? $this->attributes['certificate_state_or_province'] : null;
    }

    /**
     * State or province.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateStateOrProvince($value)
    {
        return $this->attributes['certificate_state_or_province'] = $value;
    }
    /**
     * City or locale.
     *
     * @return string|null
     */
    public function getCertificateCityOrLocale()
    {
        return isset($this->attributes['certificate_city_or_locale']) ? $this->attributes['certificate_city_or_locale'] : null;
    }

    /**
     * City or locale.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateCityOrLocale($value)
    {
        return $this->attributes['certificate_city_or_locale'] = $value;
    }
    /**
     * Company name.
     *
     * @return string|null
     */
    public function getCertificateCompanyName()
    {
        return isset($this->attributes['certificate_company_name']) ? $this->attributes['certificate_company_name'] : null;
    }

    /**
     * Company name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateCompanyName($value)
    {
        return $this->attributes['certificate_company_name'] = $value;
    }
    /**
     * Department name or organization unit 1
     *
     * @return string|null
     */
    public function getCsrOu1()
    {
        return isset($this->attributes['csr_ou1']) ? $this->attributes['csr_ou1'] : null;
    }

    /**
     * Department name or organization unit 1
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCsrOu1($value)
    {
        return $this->attributes['csr_ou1'] = $value;
    }
    /**
     * Department name or organization unit 2
     *
     * @return string|null
     */
    public function getCsrOu2()
    {
        return isset($this->attributes['csr_ou2']) ? $this->attributes['csr_ou2'] : null;
    }

    /**
     * Department name or organization unit 2
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCsrOu2($value)
    {
        return $this->attributes['csr_ou2'] = $value;
    }
    /**
     * Department name or organization unit 3
     *
     * @return string|null
     */
    public function getCsrOu3()
    {
        return isset($this->attributes['csr_ou3']) ? $this->attributes['csr_ou3'] : null;
    }

    /**
     * Department name or organization unit 3
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCsrOu3($value)
    {
        return $this->attributes['csr_ou3'] = $value;
    }
    /**
     * Email address for the certificate owner.
     *
     * @return string|null
     */
    public function getCertificateEmailAddress()
    {
        return isset($this->attributes['certificate_email_address']) ? $this->attributes['certificate_email_address'] : null;
    }

    /**
     * Email address for the certificate owner.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCertificateEmailAddress($value)
    {
        return $this->attributes['certificate_email_address'] = $value;
    }
    /**
     * Certificate private key.
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }

    /**
     * Certificate private key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrivateKey($value)
    {
        return $this->attributes['private_key'] = $value;
    }
    /**
     * Certificate password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return isset($this->attributes['password']) ? $this->attributes['password'] : null;
    }

    /**
     * Certificate password.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }

    /**
     * Activate SSL Certificate
     *
     * POST /certificates/{id}/activate
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
     *     - `replace_cert` (string, optional): Leave blank or set to `any` to replace any currently active certificate, if applicable.  Set to `new_ips` to activate this certificate as an additional certificate on your Site by allocating a new set of Dedicated IPs (may require a Plan upgrade).  Set to the ID of a currently active certificate to replace that certificate on its set of dedicated IPs.  A certificate belonging to a Custom Domain activates only for that domain: leave blank, use `any`, or specify that domain's current certificate ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function activate($params = [])
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

        if (isset($params['replace_cert']) && !is_string($params['replace_cert'])) {
            throw new \Files\Exception\InvalidParameterException('$replace_cert must be of type string; received ' . gettype(@$params['replace_cert']));
        }

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '/activate', 'POST', $params, $this->options);
        return;
    }

    /**
     * Deactivate SSL Certificate
     *
     * POST /certificates/{id}/deactivate
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function deactivate($params = [])
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

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '/deactivate', 'POST', $params, $this->options);
        return;
    }

    /**
     * Replace this active custom SSL Certificate for the Primary Custom Domain with a Files.com SSL Certificate.  The Primary Custom Domain's CNAME record must point at the Site's subdomain.  The Files.com certificate is issued and activated in the background
     *
     * POST /certificates/{id}/replace_with_files_certificate
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function replaceWithFiles($params = [])
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

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '/replace_with_files_certificate', 'POST', $params, $this->options);
        return;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Certificate with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Certificate
     *
     * PATCH /certificates/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
     *     - `name` (string, optional): Internal certificate name.
     *     - `intermediates` (string, optional): Any intermediate certificates.  PEM or PKCS7 formats accepted.
     *     - `certificate` (string, optional): The certificate.  PEM or PKCS7 formats accepted.
     * @return \Files\Model\Certificate
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

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['intermediates']) && !is_string($params['intermediates'])) {
            throw new \Files\Exception\InvalidParameterException('$intermediates must be of type string; received ' . gettype(@$params['intermediates']));
        }

        if (isset($params['certificate']) && !is_string($params['certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate must be of type string; received ' . gettype(@$params['certificate']));
        }

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Certificate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Certificate
     *
     * DELETE /certificates/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
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

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Certificate
     *
     * DELETE /certificates/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Certificate ID.
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
     * List Certificates
     *
     * GET /certificates
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Certificate[]
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

        $response = Api::sendRequest('/certificates', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Certificate(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/certificates', $params, $options, Certificate::class);
    }

    /**
     * Show Certificate
     *
     * GET /certificates/{id}
     *
     * @param int $id Certificate ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Certificate
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

        $response = Api::sendRequest('/certificates/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Certificate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Certificate
     *
     * GET /certificates/{id}
     *
     * Alias of find().
     *
     * @param int $id Certificate ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Certificate
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create CSR or import existing SSL Certificate
     *
     * POST /certificates
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Internal name of the SSL certificate.
     *     - `certificate_domain` (string, optional): Domain for certificate.
     *     - `certificate_extra_domains` (string[], optional): Additional domains for the certificate CSR.
     *     - `certificate_country` (string, optional): Country.
     *     - `certificate_state_or_province` (string, optional): State or province.
     *     - `certificate_city_or_locale` (string, optional): City or locale.
     *     - `certificate_company_name` (string, optional): Company name.
     *     - `csr_ou1` (string, optional): Department name or organization unit 1
     *     - `csr_ou2` (string, optional): Department name or organization unit 2
     *     - `csr_ou3` (string, optional): Department name or organization unit 3
     *     - `certificate_email_address` (string, optional): Email address for the certificate owner.
     *     - `key_type` (string, optional): Any supported key type.  Defaults to `RSA-4096`.
     *     - `certificate` (string, optional): The certificate.  PEM or PKCS7 formats accepted.
     *     - `private_key` (string, optional): Certificate private key.
     *     - `password` (string, optional): Certificate password.
     *     - `intermediates` (string, optional): Intermediate certificates.  PEM or PKCS7 formats accepted.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Certificate
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['certificate_domain']) && !is_string($params['certificate_domain'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_domain must be of type string; received ' . gettype(@$params['certificate_domain']));
        }

        if (isset($params['certificate_extra_domains']) && !is_array($params['certificate_extra_domains'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_extra_domains must be of type array; received ' . gettype(@$params['certificate_extra_domains']));
        }

        if (isset($params['certificate_country']) && !is_string($params['certificate_country'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_country must be of type string; received ' . gettype(@$params['certificate_country']));
        }

        if (isset($params['certificate_state_or_province']) && !is_string($params['certificate_state_or_province'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_state_or_province must be of type string; received ' . gettype(@$params['certificate_state_or_province']));
        }

        if (isset($params['certificate_city_or_locale']) && !is_string($params['certificate_city_or_locale'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_city_or_locale must be of type string; received ' . gettype(@$params['certificate_city_or_locale']));
        }

        if (isset($params['certificate_company_name']) && !is_string($params['certificate_company_name'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_company_name must be of type string; received ' . gettype(@$params['certificate_company_name']));
        }

        if (isset($params['csr_ou1']) && !is_string($params['csr_ou1'])) {
            throw new \Files\Exception\InvalidParameterException('$csr_ou1 must be of type string; received ' . gettype(@$params['csr_ou1']));
        }

        if (isset($params['csr_ou2']) && !is_string($params['csr_ou2'])) {
            throw new \Files\Exception\InvalidParameterException('$csr_ou2 must be of type string; received ' . gettype(@$params['csr_ou2']));
        }

        if (isset($params['csr_ou3']) && !is_string($params['csr_ou3'])) {
            throw new \Files\Exception\InvalidParameterException('$csr_ou3 must be of type string; received ' . gettype(@$params['csr_ou3']));
        }

        if (isset($params['certificate_email_address']) && !is_string($params['certificate_email_address'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate_email_address must be of type string; received ' . gettype(@$params['certificate_email_address']));
        }

        if (isset($params['key_type']) && !is_string($params['key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$key_type must be of type string; received ' . gettype(@$params['key_type']));
        }

        if (isset($params['certificate']) && !is_string($params['certificate'])) {
            throw new \Files\Exception\InvalidParameterException('$certificate must be of type string; received ' . gettype(@$params['certificate']));
        }

        if (isset($params['private_key']) && !is_string($params['private_key'])) {
            throw new \Files\Exception\InvalidParameterException('$private_key must be of type string; received ' . gettype(@$params['private_key']));
        }

        if (isset($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (isset($params['intermediates']) && !is_string($params['intermediates'])) {
            throw new \Files\Exception\InvalidParameterException('$intermediates must be of type string; received ' . gettype(@$params['intermediates']));
        }

        $response = Api::sendRequest('/certificates', 'POST', $params, $options);

        return new Certificate(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
