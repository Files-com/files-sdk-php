<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A UsageDailySnapshot is a detailed site-wide usage report for the day.
 *
 * @property int|null $id ID of the usage record
 * @property string|null $date The date of this usage record
 * @property bool|null $api_usage_available True if the API usage fields `read_api_usage` and `write_api_usage` can be relied upon. If this is false, we suggest hiding that value from any UI.
 * @property int|null $read_api_usage Read API Calls used on this day. Note: only updated for days before the current day.
 * @property int|null $write_api_usage Write API Calls used on this day. Note: only updated for days before the current day.
 * @property int|null $transformation_credits_usage Transformation and AI credits used on this day. Note: only updated for days before the current day.
 * @property int|null $user_count Number of billable users as of this day.
 * @property int|null $current_storage GB of Files Native Storage used on this day.
 * @property int|null $deleted_files_storage GB of Files Native Storage used on this day for files that have been deleted and are stored as backups.
 * @property int|null $deleted_files_counted_in_minimum GB of Files Native Storage used on this day for files that have been permanently deleted but were uploaded less than 30 days ago, and are still billable.
 * @property int|null $root_storage GB of Files Native Storage used for the root folder. Included here because this value will not be part of `usage_by_top_level_dir`
 * @property object[]|null $usage_by_top_level_dir Usage broken down by each top-level folder
 * @package Files
 */
class UsageDailySnapshot
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
     * ID of the usage record
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * The date of this usage record
     *
     * @return string|null
     */
    public function getDate()
    {
        return isset($this->attributes['date']) ? $this->attributes['date'] : null;
    }
    /**
     * True if the API usage fields `read_api_usage` and `write_api_usage` can be relied upon.  If this is false, we suggest hiding that value from any UI.
     *
     * @return bool|null
     */
    public function getApiUsageAvailable()
    {
        return isset($this->attributes['api_usage_available']) ? $this->attributes['api_usage_available'] : null;
    }
    /**
     * Read API Calls used on this day. Note: only updated for days before the current day.
     *
     * @return int|null
     */
    public function getReadApiUsage()
    {
        return isset($this->attributes['read_api_usage']) ? $this->attributes['read_api_usage'] : null;
    }
    /**
     * Write API Calls used on this day. Note: only updated for days before the current day.
     *
     * @return int|null
     */
    public function getWriteApiUsage()
    {
        return isset($this->attributes['write_api_usage']) ? $this->attributes['write_api_usage'] : null;
    }
    /**
     * Transformation and AI credits used on this day. Note: only updated for days before the current day.
     *
     * @return int|null
     */
    public function getTransformationCreditsUsage()
    {
        return isset($this->attributes['transformation_credits_usage']) ? $this->attributes['transformation_credits_usage'] : null;
    }
    /**
     * Number of billable users as of this day.
     *
     * @return int|null
     */
    public function getUserCount()
    {
        return isset($this->attributes['user_count']) ? $this->attributes['user_count'] : null;
    }
    /**
     * GB of Files Native Storage used on this day.
     *
     * @return int|null
     */
    public function getCurrentStorage()
    {
        return isset($this->attributes['current_storage']) ? $this->attributes['current_storage'] : null;
    }
    /**
     * GB of Files Native Storage used on this day for files that have been deleted and are stored as backups.
     *
     * @return int|null
     */
    public function getDeletedFilesStorage()
    {
        return isset($this->attributes['deleted_files_storage']) ? $this->attributes['deleted_files_storage'] : null;
    }
    /**
     * GB of Files Native Storage used on this day for files that have been permanently deleted but were uploaded less than 30 days ago, and are still billable.
     *
     * @return int|null
     */
    public function getDeletedFilesCountedInMinimum()
    {
        return isset($this->attributes['deleted_files_counted_in_minimum']) ? $this->attributes['deleted_files_counted_in_minimum'] : null;
    }
    /**
     * GB of Files Native Storage used for the root folder.  Included here because this value will not be part of `usage_by_top_level_dir`
     *
     * @return int|null
     */
    public function getRootStorage()
    {
        return isset($this->attributes['root_storage']) ? $this->attributes['root_storage'] : null;
    }
    /**
     * Usage broken down by each top-level folder
     *
     * @return object[]|null
     */
    public function getUsageByTopLevelDir()
    {
        return isset($this->attributes['usage_by_top_level_dir']) ? $this->attributes['usage_by_top_level_dir'] : null;
    }

    /**
     * List Usage Daily Snapshots
     *
     * GET /usage_daily_snapshots
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `date`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`. Valid field combinations are `[ usage_snapshot_id, date ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `date`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `date`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `date`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `date`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UsageDailySnapshot[]
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

        $response = Api::sendRequest('/usage_daily_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new UsageDailySnapshot(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/usage_daily_snapshots', $params, $options, UsageDailySnapshot::class);
    }
}
