<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A CustomDomain object represents an additional customer-owned domain that routes to a Files.com site.
 *
 * @property int|null $id Custom Domain ID.
 * @property int|null $workspace_id Workspace ID (0 for the default workspace).
 * @property bool|null $available_to_all_workspaces Allow all workspaces to use this default-workspace Custom Domain.
 * @property string[]|null $outbound_ip_addresses Eligible public IP addresses for Remote Server outbound connections. Empty when this Custom Domain is not eligible for outbound selection.
 * @property string|null $domain Customer-owned domain name.
 * @property string|null $destination Where this custom domain routes. Can be `site_alias`, `public_hosting`, `s3_endpoint`, or `unassigned` (not routing traffic). Set to `unassigned` automatically when a bound `public_hosting` folder behavior is deleted, and can be set manually via the API for any reason.
 * @property string|null $dns_status Current DNS verification status.
 * @property int|null $ssl_certificate_id Current SSL certificate ID.
 * @property bool|null $brick_managed Is this domain's SSL certificate automatically managed and renewed by Files.com?
 * @property int|null $folder_behavior_id Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior. Preserved as historical context when `destination` becomes `unassigned`.
 * @property string[]|null $ip_addresses Dedicated public IP addresses allocated to this Custom Domain.
 * @property string|null $created_at When this Custom Domain was created.
 * @property string|null $updated_at When this Custom Domain was last updated.
 * @package Files
 */
class CustomDomain
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
     * Custom Domain ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Custom Domain ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace ID (0 for the default workspace).
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID (0 for the default workspace).
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Allow all workspaces to use this default-workspace Custom Domain.
     *
     * @return bool|null
     */
    public function getAvailableToAllWorkspaces()
    {
        return isset($this->attributes['available_to_all_workspaces']) ? $this->attributes['available_to_all_workspaces'] : null;
    }

    /**
     * Allow all workspaces to use this default-workspace Custom Domain.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAvailableToAllWorkspaces($value)
    {
        return $this->attributes['available_to_all_workspaces'] = $value;
    }
    /**
     * Eligible public IP addresses for Remote Server outbound connections. Empty when this Custom Domain is not eligible for outbound selection.
     *
     * @return string[]|null
     */
    public function getOutboundIpAddresses()
    {
        return isset($this->attributes['outbound_ip_addresses']) ? $this->attributes['outbound_ip_addresses'] : null;
    }

    /**
     * Eligible public IP addresses for Remote Server outbound connections. Empty when this Custom Domain is not eligible for outbound selection.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setOutboundIpAddresses($value)
    {
        return $this->attributes['outbound_ip_addresses'] = $value;
    }
    /**
     * Customer-owned domain name.
     *
     * @return string|null
     */
    public function getDomain()
    {
        return isset($this->attributes['domain']) ? $this->attributes['domain'] : null;
    }

    /**
     * Customer-owned domain name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDomain($value)
    {
        return $this->attributes['domain'] = $value;
    }
    /**
     * Where this custom domain routes. Can be `site_alias`, `public_hosting`, `s3_endpoint`, or `unassigned` (not routing traffic). Set to `unassigned` automatically when a bound `public_hosting` folder behavior is deleted, and can be set manually via the API for any reason.
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }

    /**
     * Where this custom domain routes. Can be `site_alias`, `public_hosting`, `s3_endpoint`, or `unassigned` (not routing traffic). Set to `unassigned` automatically when a bound `public_hosting` folder behavior is deleted, and can be set manually via the API for any reason.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestination($value)
    {
        return $this->attributes['destination'] = $value;
    }
    /**
     * Current DNS verification status.
     *
     * @return string|null
     */
    public function getDnsStatus()
    {
        return isset($this->attributes['dns_status']) ? $this->attributes['dns_status'] : null;
    }

    /**
     * Current DNS verification status.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDnsStatus($value)
    {
        return $this->attributes['dns_status'] = $value;
    }
    /**
     * Current SSL certificate ID.
     *
     * @return int|null
     */
    public function getSslCertificateId()
    {
        return isset($this->attributes['ssl_certificate_id']) ? $this->attributes['ssl_certificate_id'] : null;
    }

    /**
     * Current SSL certificate ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSslCertificateId($value)
    {
        return $this->attributes['ssl_certificate_id'] = $value;
    }
    /**
     * Is this domain's SSL certificate automatically managed and renewed by Files.com?
     *
     * @return bool|null
     */
    public function getBrickManaged()
    {
        return isset($this->attributes['brick_managed']) ? $this->attributes['brick_managed'] : null;
    }

    /**
     * Is this domain's SSL certificate automatically managed and renewed by Files.com?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBrickManaged($value)
    {
        return $this->attributes['brick_managed'] = $value;
    }
    /**
     * Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.  Preserved as historical context when `destination` becomes `unassigned`.
     *
     * @return int|null
     */
    public function getFolderBehaviorId()
    {
        return isset($this->attributes['folder_behavior_id']) ? $this->attributes['folder_behavior_id'] : null;
    }

    /**
     * Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.  Preserved as historical context when `destination` becomes `unassigned`.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFolderBehaviorId($value)
    {
        return $this->attributes['folder_behavior_id'] = $value;
    }
    /**
     * Dedicated public IP addresses allocated to this Custom Domain.
     *
     * @return string[]|null
     */
    public function getIpAddresses()
    {
        return isset($this->attributes['ip_addresses']) ? $this->attributes['ip_addresses'] : null;
    }

    /**
     * Dedicated public IP addresses allocated to this Custom Domain.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setIpAddresses($value)
    {
        return $this->attributes['ip_addresses'] = $value;
    }
    /**
     * When this Custom Domain was created.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * When this Custom Domain was last updated.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // CustomDomain with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Custom Domain
     *
     * PATCH /custom_domains/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Custom Domain ID.
     *     - `available_to_all_workspaces` (bool, optional): Allow all workspaces to use this default-workspace Custom Domain.
     *     - `workspace_id` (int, optional): Workspace ID (0 for the default workspace).
     *     - `destination` (string, optional): Where this custom domain routes. Can be `site_alias`, `public_hosting`, `s3_endpoint`, or `unassigned` (not routing traffic). Set to `unassigned` automatically when a bound `public_hosting` folder behavior is deleted, and can be set manually via the API for any reason. Allowed values: site_alias, public_hosting, s3_endpoint, unassigned.
     *     - `folder_behavior_id` (int, optional): Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.  Preserved as historical context when `destination` becomes `unassigned`.
     *     - `ssl_certificate_id` (int, optional): Current SSL certificate ID.
     *     - `domain` (string, optional): Customer-owned domain name.
     * @return \Files\Model\CustomDomain
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

        if (isset($params['available_to_all_workspaces']) && !is_scalar($params['available_to_all_workspaces'])) {
            throw new \Files\Exception\InvalidParameterException('$available_to_all_workspaces must be of type boolean; received ' . gettype(@$params['available_to_all_workspaces']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['folder_behavior_id']) && !is_int($params['folder_behavior_id'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype(@$params['folder_behavior_id']));
        }

        if (isset($params['ssl_certificate_id']) && !is_int($params['ssl_certificate_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate_id must be of type int; received ' . gettype(@$params['ssl_certificate_id']));
        }

        if (isset($params['domain']) && !is_string($params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new CustomDomain(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Custom Domain
     *
     * DELETE /custom_domains/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Custom Domain ID.
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

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Custom Domain
     *
     * DELETE /custom_domains/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Custom Domain ID.
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
     * List Custom Domains
     *
     * GET /custom_domains
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `id` or `available_to_all_workspaces`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\CustomDomain[]
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

        $response = Api::sendRequest('/custom_domains', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new CustomDomain(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/custom_domains', $params, $options, CustomDomain::class);
    }

    /**
     * Show Custom Domain
     *
     * GET /custom_domains/{id}
     *
     * @param int $id Custom Domain ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\CustomDomain
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

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new CustomDomain(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Custom Domain
     *
     * GET /custom_domains/{id}
     *
     * Alias of find().
     *
     * @param int $id Custom Domain ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\CustomDomain
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Allocate dedicated IP addresses to this Custom Domain
     *
     * POST /custom_domains/{id}/allocate_ips
     *
     * @param int $id Custom Domain ID.
     * @param array $params Request parameters.
     *     - `count` (int, required): Number of dedicated IP addresses to allocate. Allowed values: 1, 2.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\CustomDomain
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function createAllocateIp($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (empty($params['count'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: count');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['count']) && !is_int($params['count'])) {
            throw new \Files\Exception\InvalidParameterException('$count must be of type int; received ' . gettype(@$params['count']));
        }

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '/allocate_ips', 'POST', $params, $options);

        return new CustomDomain(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Create Custom Domain
     *
     * POST /custom_domains
     *
     * @param array $params Request parameters.
     *     - `available_to_all_workspaces` (bool, optional): Allow all workspaces to use this default-workspace Custom Domain.
     *     - `workspace_id` (int, optional): Workspace ID (0 for the default workspace).
     *     - `destination` (string, optional): Where this custom domain routes. Can be `site_alias`, `public_hosting`, `s3_endpoint`, or `unassigned` (not routing traffic). Set to `unassigned` automatically when a bound `public_hosting` folder behavior is deleted, and can be set manually via the API for any reason. Allowed values: site_alias, public_hosting, s3_endpoint, unassigned.
     *     - `folder_behavior_id` (int, optional): Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.  Preserved as historical context when `destination` becomes `unassigned`.
     *     - `ssl_certificate_id` (int, optional): Current SSL certificate ID.
     *     - `domain` (string, required): Customer-owned domain name.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\CustomDomain
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['domain'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: domain');
        }

        if (isset($params['available_to_all_workspaces']) && !is_scalar($params['available_to_all_workspaces'])) {
            throw new \Files\Exception\InvalidParameterException('$available_to_all_workspaces must be of type boolean; received ' . gettype(@$params['available_to_all_workspaces']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['folder_behavior_id']) && !is_int($params['folder_behavior_id'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype(@$params['folder_behavior_id']));
        }

        if (isset($params['ssl_certificate_id']) && !is_int($params['ssl_certificate_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate_id must be of type int; received ' . gettype(@$params['ssl_certificate_id']));
        }

        if (isset($params['domain']) && !is_string($params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        $response = Api::sendRequest('/custom_domains', 'POST', $params, $options);

        return new CustomDomain(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
