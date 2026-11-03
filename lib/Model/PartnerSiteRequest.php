<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A PartnerSiteRequest represents a request for a Guest Partner to add their Files.com Site to their Partnership with the Host Partner.
 *
 * @property int|null $id Partner Site Request ID
 * @property int|null $host_partner_id Host Partner ID
 * @property string|null $guest_site_url Guest Site URL
 * @property string|null $status Request status (pending, approved, rejected)
 * @property string|null $host_site_name Host Site Name
 * @property string|null $pairing_key Pairing key used to approve this request on the Guest Site
 * @property string|null $created_at Request creation date/time
 * @property string|null $updated_at Request last updated date/time
 * @package Files
 */
class PartnerSiteRequest
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
     * Partner Site Request ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Partner Site Request ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Host Partner ID
     *
     * @return int|null
     */
    public function getHostPartnerId()
    {
        return isset($this->attributes['host_partner_id']) ? $this->attributes['host_partner_id'] : null;
    }

    /**
     * Host Partner ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setHostPartnerId($value)
    {
        return $this->attributes['host_partner_id'] = $value;
    }
    /**
     * Guest Site URL
     *
     * @return string|null
     */
    public function getGuestSiteUrl()
    {
        return isset($this->attributes['guest_site_url']) ? $this->attributes['guest_site_url'] : null;
    }

    /**
     * Guest Site URL
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGuestSiteUrl($value)
    {
        return $this->attributes['guest_site_url'] = $value;
    }
    /**
     * Request status (pending, approved, rejected)
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Request status (pending, approved, rejected)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * Host Site Name
     *
     * @return string|null
     */
    public function getHostSiteName()
    {
        return isset($this->attributes['host_site_name']) ? $this->attributes['host_site_name'] : null;
    }

    /**
     * Host Site Name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHostSiteName($value)
    {
        return $this->attributes['host_site_name'] = $value;
    }
    /**
     * Pairing key used to approve this request on the Guest Site
     *
     * @return string|null
     */
    public function getPairingKey()
    {
        return isset($this->attributes['pairing_key']) ? $this->attributes['pairing_key'] : null;
    }

    /**
     * Pairing key used to approve this request on the Guest Site
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPairingKey($value)
    {
        return $this->attributes['pairing_key'] = $value;
    }
    /**
     * Request creation date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Request last updated date/time
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Delete Partner Site Request
     *
     * DELETE /partner_site_requests/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner Site Request ID.
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

        $response = Api::sendRequest('/partner_site_requests/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Partner Site Request
     *
     * DELETE /partner_site_requests/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Partner Site Request ID.
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
            throw new \Files\Exception\NotImplementedException('The PartnerSiteRequest object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Partner Site Requests
     *
     * GET /partner_site_requests
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `host_partner_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `host_partner_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerSiteRequest[]
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

        $response = Api::sendRequest('/partner_site_requests', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PartnerSiteRequest((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Find partner site request by pairing key
     *
     * GET /partner_site_requests/find_by_pairing_key
     *
     * @param array $params Request parameters.
     *     - `pairing_key` (string, required): Pairing key for the partner site request
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function findByPairingKey($params = [], $options = [])
    {
        if (empty($params['pairing_key'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: pairing_key');
        }

        if (!empty($params['pairing_key']) && !is_string($params['pairing_key'])) {
            throw new \Files\Exception\InvalidParameterException('$pairing_key must be of type string; received ' . gettype(@$params['pairing_key']));
        }

        $response = Api::sendRequest('/partner_site_requests/find_by_pairing_key', 'GET', $params, $options);

        return;
    }

    /**
     * Create Partner Site Request
     *
     * POST /partner_site_requests
     *
     * @param array $params Request parameters.
     *     - `host_partner_id` (int, required): Host Partner ID to link with
     *     - `guest_site_url` (string, required): Guest Site URL to link to
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\PartnerSiteRequest
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['host_partner_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: host_partner_id');
        }

        if (empty($params['guest_site_url'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: guest_site_url');
        }

        if (!empty($params['host_partner_id']) && !is_int($params['host_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$host_partner_id must be of type int; received ' . gettype(@$params['host_partner_id']));
        }

        if (!empty($params['guest_site_url']) && !is_string($params['guest_site_url'])) {
            throw new \Files\Exception\InvalidParameterException('$guest_site_url must be of type string; received ' . gettype(@$params['guest_site_url']));
        }

        $response = Api::sendRequest('/partner_site_requests', 'POST', $params, $options);

        return new PartnerSiteRequest((array) (@$response->data ?: []), $options);
    }

    /**
     * Reject partner site request
     *
     * POST /partner_site_requests/reject
     *
     * @param array $params Request parameters.
     *     - `pairing_key` (string, required): Pairing key for the partner site request
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function reject($params = [], $options = [])
    {
        if (empty($params['pairing_key'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: pairing_key');
        }

        if (!empty($params['pairing_key']) && !is_string($params['pairing_key'])) {
            throw new \Files\Exception\InvalidParameterException('$pairing_key must be of type string; received ' . gettype(@$params['pairing_key']));
        }

        $response = Api::sendRequest('/partner_site_requests/reject', 'POST', $params, $options);

        return;
    }

    /**
     * Approve partner site request
     *
     * POST /partner_site_requests/approve
     *
     * @param array $params Request parameters.
     *     - `pairing_key` (string, required): Pairing key for the partner site request
     *     - `partner_id` (int, optional): ID of an existing Partner on this site, with the host role, that represents the requesting organization. The connection binds to that Partner and makes it host_and_guest. When omitted, a guest Partner named after the host site is created.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function approve($params = [], $options = [])
    {
        if (empty($params['pairing_key'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: pairing_key');
        }

        if (!empty($params['pairing_key']) && !is_string($params['pairing_key'])) {
            throw new \Files\Exception\InvalidParameterException('$pairing_key must be of type string; received ' . gettype(@$params['pairing_key']));
        }

        if (!empty($params['partner_id']) && !is_int($params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        $response = Api::sendRequest('/partner_site_requests/approve', 'POST', $params, $options);

        return;
    }
}
