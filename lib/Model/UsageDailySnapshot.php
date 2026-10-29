<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UsageDailySnapshot
 *
 * @package Files
 */
class UsageDailySnapshot
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
    // int64 # ID of the usage record
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // date # The date of this usage record
    public function getDate()
    {
        return isset($this->attributes['date']) ? $this->attributes['date'] : null;
    }
    // boolean # True if the API usage fields `read_api_usage` and `write_api_usage` can be relied upon.  If this is false, we suggest hiding that value from any UI.
    public function getApiUsageAvailable()
    {
        return isset($this->attributes['api_usage_available']) ? $this->attributes['api_usage_available'] : null;
    }
    // int64 # Read API Calls used on this day. Note: only updated for days before the current day.
    public function getReadApiUsage()
    {
        return isset($this->attributes['read_api_usage']) ? $this->attributes['read_api_usage'] : null;
    }
    // int64 # Write API Calls used on this day. Note: only updated for days before the current day.
    public function getWriteApiUsage()
    {
        return isset($this->attributes['write_api_usage']) ? $this->attributes['write_api_usage'] : null;
    }
    // int64 # Transformation and AI credits used on this day. Note: only updated for days before the current day.
    public function getTransformationCreditsUsage()
    {
        return isset($this->attributes['transformation_credits_usage']) ? $this->attributes['transformation_credits_usage'] : null;
    }
    // int64 # Number of billable users as of this day.
    public function getUserCount()
    {
        return isset($this->attributes['user_count']) ? $this->attributes['user_count'] : null;
    }
    // int64 # GB of Files Native Storage used on this day.
    public function getCurrentStorage()
    {
        return isset($this->attributes['current_storage']) ? $this->attributes['current_storage'] : null;
    }
    // int64 # GB of Files Native Storage used on this day for files that have been deleted and are stored as backups.
    public function getDeletedFilesStorage()
    {
        return isset($this->attributes['deleted_files_storage']) ? $this->attributes['deleted_files_storage'] : null;
    }
    // int64 # GB of Files Native Storage used on this day for files that have been permanently deleted but were uploaded less than 30 days ago, and are still billable.
    public function getDeletedFilesCountedInMinimum()
    {
        return isset($this->attributes['deleted_files_counted_in_minimum']) ? $this->attributes['deleted_files_counted_in_minimum'] : null;
    }
    // int64 # GB of Files Native Storage used for the root folder.  Included here because this value will not be part of `usage_by_top_level_dir`
    public function getRootStorage()
    {
        return isset($this->attributes['root_storage']) ? $this->attributes['root_storage'] : null;
    }
    // array(object) # Usage broken down by each top-level folder
    public function getUsageByTopLevelDir()
    {
        return isset($this->attributes['usage_by_top_level_dir']) ? $this->attributes['usage_by_top_level_dir'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `date`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `date` and `usage_snapshot_id`. Valid field combinations are `[ usage_snapshot_id, date ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `date`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `date`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `date`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `date`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/usage_daily_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new UsageDailySnapshot((array) $obj, $options);
        }

        return $return_array;
    }
}
