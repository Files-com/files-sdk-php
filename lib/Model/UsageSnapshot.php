<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UsageSnapshot
 *
 * @package Files
 */
class UsageSnapshot
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
    // int64 # Usage snapshot ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // date-time # Usage snapshot start date/time
    public function getStartAt()
    {
        return isset($this->attributes['start_at']) ? $this->attributes['start_at'] : null;
    }
    // date-time # Usage snapshot end date/time
    public function getEndAt()
    {
        return isset($this->attributes['end_at']) ? $this->attributes['end_at'] : null;
    }
    // int64 # Highest user count number in time period
    public function getHighWaterUserCount()
    {
        return isset($this->attributes['high_water_user_count']) ? $this->attributes['high_water_user_count'] : null;
    }
    // int64 # Total Transformation and AI credits used in this usage period. Updated daily. Does not include credits consumed today.
    public function getTransformationCreditsUsage()
    {
        return isset($this->attributes['transformation_credits_usage']) ? $this->attributes['transformation_credits_usage'] : null;
    }
    // int64 # Current total Storage Usage GB as of end date (not necessarily high water mark, which is used for billing)
    public function getCurrentStorage()
    {
        return isset($this->attributes['current_storage']) ? $this->attributes['current_storage'] : null;
    }
    // int64 # Highest Storage Usage GB recorded in time period (used for billing)
    public function getHighWaterStorage()
    {
        return isset($this->attributes['high_water_storage']) ? $this->attributes['high_water_storage'] : null;
    }
    // int64 # Storage Usage for root folder as of end date (not necessarily high water mark, which is used for billing)
    public function getRootStorage()
    {
        return isset($this->attributes['root_storage']) ? $this->attributes['root_storage'] : null;
    }
    // int64 # Storage Usage for files that are deleted but uploaded within last 30 days as of end date (not necessarily high water mark, which is used for billing)
    public function getDeletedFilesCountedInMinimum()
    {
        return isset($this->attributes['deleted_files_counted_in_minimum']) ? $this->attributes['deleted_files_counted_in_minimum'] : null;
    }
    // int64 # Storage Usage for files that are deleted but retained as backups as of end date (not necessarily high water mark, which is used for billing)
    public function getDeletedFilesStorage()
    {
        return isset($this->attributes['deleted_files_storage']) ? $this->attributes['deleted_files_storage'] : null;
    }
    // int64 # Storage + Transfer Usage - Total Billable amount
    public function getTotalBillableUsage()
    {
        return isset($this->attributes['total_billable_usage']) ? $this->attributes['total_billable_usage'] : null;
    }
    // int64 # Transfer usage for period - Total Billable amount
    public function getTotalBillableTransferUsage()
    {
        return isset($this->attributes['total_billable_transfer_usage']) ? $this->attributes['total_billable_transfer_usage'] : null;
    }
    // int64 # Transfer Usage for period - Outbound GB from Files Native Storage
    public function getBytesSent()
    {
        return isset($this->attributes['bytes_sent']) ? $this->attributes['bytes_sent'] : null;
    }
    // int64 # Transfer Usage for period - Inbound GB to Remote Servers (Sync/Mount)
    public function getSyncBytesReceived()
    {
        return isset($this->attributes['sync_bytes_received']) ? $this->attributes['sync_bytes_received'] : null;
    }
    // int64 # Transfer Usage for period - Outbound GB from Remote Servers (Sync/Mount)
    public function getSyncBytesSent()
    {
        return isset($this->attributes['sync_bytes_sent']) ? $this->attributes['sync_bytes_sent'] : null;
    }
    // array(object) # Storage Usage - map of root folders to their usage as of end date (not necessarily high water mark, which is used for billing)
    public function getUsageByTopLevelDir()
    {
        return isset($this->attributes['usage_by_top_level_dir']) ? $this->attributes['usage_by_top_level_dir'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/usage_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new UsageSnapshot((array) $obj, $options);
        }

        return $return_array;
    }
}
