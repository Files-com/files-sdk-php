<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A BandwidthSnapshot is information about site bandwidth usage for the given date.
 *
 * @property int|null $id Site bandwidth ID
 * @property int|null $bytes_received Site bandwidth report bytes received
 * @property int|null $bytes_sent Site bandwidth report bytes sent
 * @property int|null $sync_bytes_received Site sync bandwidth report bytes received
 * @property int|null $sync_bytes_sent Site sync bandwidth report bytes sent
 * @property int|null $requests_get Site bandwidth report get requests
 * @property int|null $requests_put Site bandwidth report put requests
 * @property int|null $requests_other Site bandwidth report other requests
 * @property string|null $logged_at Time the site bandwidth report was logged
 * @package Files
 */
class BandwidthSnapshot
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
     * Site bandwidth ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Site bandwidth report bytes received
     *
     * @return int|null
     */
    public function getBytesReceived()
    {
        return isset($this->attributes['bytes_received']) ? $this->attributes['bytes_received'] : null;
    }
    /**
     * Site bandwidth report bytes sent
     *
     * @return int|null
     */
    public function getBytesSent()
    {
        return isset($this->attributes['bytes_sent']) ? $this->attributes['bytes_sent'] : null;
    }
    /**
     * Site sync bandwidth report bytes received
     *
     * @return int|null
     */
    public function getSyncBytesReceived()
    {
        return isset($this->attributes['sync_bytes_received']) ? $this->attributes['sync_bytes_received'] : null;
    }
    /**
     * Site sync bandwidth report bytes sent
     *
     * @return int|null
     */
    public function getSyncBytesSent()
    {
        return isset($this->attributes['sync_bytes_sent']) ? $this->attributes['sync_bytes_sent'] : null;
    }
    /**
     * Site bandwidth report get requests
     *
     * @return int|null
     */
    public function getRequestsGet()
    {
        return isset($this->attributes['requests_get']) ? $this->attributes['requests_get'] : null;
    }
    /**
     * Site bandwidth report put requests
     *
     * @return int|null
     */
    public function getRequestsPut()
    {
        return isset($this->attributes['requests_put']) ? $this->attributes['requests_put'] : null;
    }
    /**
     * Site bandwidth report other requests
     *
     * @return int|null
     */
    public function getRequestsOther()
    {
        return isset($this->attributes['requests_other']) ? $this->attributes['requests_other'] : null;
    }
    /**
     * Time the site bandwidth report was logged
     *
     * @return string|null
     */
    public function getLoggedAt()
    {
        return isset($this->attributes['logged_at']) ? $this->attributes['logged_at'] : null;
    }

    /**
     * List Bandwidth Snapshots
     *
     * GET /bandwidth_snapshots
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `logged_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `logged_at`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `logged_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `logged_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `logged_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `logged_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BandwidthSnapshot[]
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

        $response = Api::sendRequest('/bandwidth_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new BandwidthSnapshot(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/bandwidth_snapshots', $params, $options, BandwidthSnapshot::class);
    }
}
