<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A UsageSnapshot is a site-wide utilization report of storage, bandwidth, APIs, and user accounts of your Files.com site for the given time period.
 *
 * @property int|null $id Usage snapshot ID
 * @property string|null $start_at Usage snapshot start date/time
 * @property string|null $end_at Usage snapshot end date/time
 * @property int|null $high_water_user_count Highest user count number in time period
 * @property int|null $transformation_credits_usage Total Transformation and AI credits used in this usage period. Updated daily. Does not include credits consumed today.
 * @property int|null $current_storage Current total Storage Usage GB as of end date (not necessarily high water mark, which is used for billing)
 * @property int|null $high_water_storage Highest Storage Usage GB recorded in time period (used for billing)
 * @property int|null $root_storage Storage Usage for root folder as of end date (not necessarily high water mark, which is used for billing)
 * @property int|null $deleted_files_counted_in_minimum Storage Usage for files that are deleted but uploaded within last 30 days as of end date (not necessarily high water mark, which is used for billing)
 * @property int|null $deleted_files_storage Storage Usage for files that are deleted but retained as backups as of end date (not necessarily high water mark, which is used for billing)
 * @property int|null $total_billable_usage Storage + Transfer Usage - Total Billable amount
 * @property int|null $total_billable_transfer_usage Transfer usage for period - Total Billable amount
 * @property int|null $bytes_sent Transfer Usage for period - Outbound GB from Files Native Storage
 * @property int|null $sync_bytes_received Transfer Usage for period - Inbound GB to Remote Servers (Sync/Mount)
 * @property int|null $sync_bytes_sent Transfer Usage for period - Outbound GB from Remote Servers (Sync/Mount)
 * @property object[]|null $usage_by_top_level_dir Storage Usage - map of root folders to their usage as of end date (not necessarily high water mark, which is used for billing)
 * @package Files
 */
class UsageSnapshot
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
     * Usage snapshot ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Usage snapshot start date/time
     *
     * @return string|null
     */
    public function getStartAt()
    {
        return isset($this->attributes['start_at']) ? $this->attributes['start_at'] : null;
    }
    /**
     * Usage snapshot end date/time
     *
     * @return string|null
     */
    public function getEndAt()
    {
        return isset($this->attributes['end_at']) ? $this->attributes['end_at'] : null;
    }
    /**
     * Highest user count number in time period
     *
     * @return int|null
     */
    public function getHighWaterUserCount()
    {
        return isset($this->attributes['high_water_user_count']) ? $this->attributes['high_water_user_count'] : null;
    }
    /**
     * Total Transformation and AI credits used in this usage period. Updated daily. Does not include credits consumed today.
     *
     * @return int|null
     */
    public function getTransformationCreditsUsage()
    {
        return isset($this->attributes['transformation_credits_usage']) ? $this->attributes['transformation_credits_usage'] : null;
    }
    /**
     * Current total Storage Usage GB as of end date (not necessarily high water mark, which is used for billing)
     *
     * @return int|null
     */
    public function getCurrentStorage()
    {
        return isset($this->attributes['current_storage']) ? $this->attributes['current_storage'] : null;
    }
    /**
     * Highest Storage Usage GB recorded in time period (used for billing)
     *
     * @return int|null
     */
    public function getHighWaterStorage()
    {
        return isset($this->attributes['high_water_storage']) ? $this->attributes['high_water_storage'] : null;
    }
    /**
     * Storage Usage for root folder as of end date (not necessarily high water mark, which is used for billing)
     *
     * @return int|null
     */
    public function getRootStorage()
    {
        return isset($this->attributes['root_storage']) ? $this->attributes['root_storage'] : null;
    }
    /**
     * Storage Usage for files that are deleted but uploaded within last 30 days as of end date (not necessarily high water mark, which is used for billing)
     *
     * @return int|null
     */
    public function getDeletedFilesCountedInMinimum()
    {
        return isset($this->attributes['deleted_files_counted_in_minimum']) ? $this->attributes['deleted_files_counted_in_minimum'] : null;
    }
    /**
     * Storage Usage for files that are deleted but retained as backups as of end date (not necessarily high water mark, which is used for billing)
     *
     * @return int|null
     */
    public function getDeletedFilesStorage()
    {
        return isset($this->attributes['deleted_files_storage']) ? $this->attributes['deleted_files_storage'] : null;
    }
    /**
     * Storage + Transfer Usage - Total Billable amount
     *
     * @return int|null
     */
    public function getTotalBillableUsage()
    {
        return isset($this->attributes['total_billable_usage']) ? $this->attributes['total_billable_usage'] : null;
    }
    /**
     * Transfer usage for period - Total Billable amount
     *
     * @return int|null
     */
    public function getTotalBillableTransferUsage()
    {
        return isset($this->attributes['total_billable_transfer_usage']) ? $this->attributes['total_billable_transfer_usage'] : null;
    }
    /**
     * Transfer Usage for period - Outbound GB from Files Native Storage
     *
     * @return int|null
     */
    public function getBytesSent()
    {
        return isset($this->attributes['bytes_sent']) ? $this->attributes['bytes_sent'] : null;
    }
    /**
     * Transfer Usage for period - Inbound GB to Remote Servers (Sync/Mount)
     *
     * @return int|null
     */
    public function getSyncBytesReceived()
    {
        return isset($this->attributes['sync_bytes_received']) ? $this->attributes['sync_bytes_received'] : null;
    }
    /**
     * Transfer Usage for period - Outbound GB from Remote Servers (Sync/Mount)
     *
     * @return int|null
     */
    public function getSyncBytesSent()
    {
        return isset($this->attributes['sync_bytes_sent']) ? $this->attributes['sync_bytes_sent'] : null;
    }
    /**
     * Storage Usage - map of root folders to their usage as of end date (not necessarily high water mark, which is used for billing)
     *
     * @return object[]|null
     */
    public function getUsageByTopLevelDir()
    {
        return isset($this->attributes['usage_by_top_level_dir']) ? $this->attributes['usage_by_top_level_dir'] : null;
    }

    /**
     * List Usage Snapshots
     *
     * GET /usage_snapshots
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UsageSnapshot[]
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

        $response = Api::sendRequest('/usage_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new UsageSnapshot(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/usage_snapshots', $params, $options, UsageSnapshot::class);
    }
}
