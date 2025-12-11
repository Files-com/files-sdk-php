<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Sync
 *
 * @package Files
 */
class Sync
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
    // int64 # Sync ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Name for this sync job
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Description for this sync job
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // int64 # Site ID this sync belongs to
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }

    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    // int64 # User who created or owns this sync
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Absolute source path for the sync
    public function getSrcPath()
    {
        return @$this->attributes['src_path'];
    }

    public function setSrcPath($value)
    {
        return $this->attributes['src_path'] = $value;
    }
    // string # Absolute destination path for the sync
    public function getDestPath()
    {
        return @$this->attributes['dest_path'];
    }

    public function setDestPath($value)
    {
        return $this->attributes['dest_path'] = $value;
    }
    // int64 # Remote server ID for the source (if remote)
    public function getSrcRemoteServerId()
    {
        return @$this->attributes['src_remote_server_id'];
    }

    public function setSrcRemoteServerId($value)
    {
        return $this->attributes['src_remote_server_id'] = $value;
    }
    // int64 # Remote server ID for the destination (if remote)
    public function getDestRemoteServerId()
    {
        return @$this->attributes['dest_remote_server_id'];
    }

    public function setDestRemoteServerId($value)
    {
        return $this->attributes['dest_remote_server_id'] = $value;
    }
    // boolean # Is this a two-way sync?
    public function getTwoWay()
    {
        return @$this->attributes['two_way'];
    }

    public function setTwoWay($value)
    {
        return $this->attributes['two_way'] = $value;
    }
    // boolean # Keep files after copying?
    public function getKeepAfterCopy()
    {
        return @$this->attributes['keep_after_copy'];
    }

    public function setKeepAfterCopy($value)
    {
        return $this->attributes['keep_after_copy'] = $value;
    }
    // boolean # Delete empty folders after sync?
    public function getDeleteEmptyFolders()
    {
        return @$this->attributes['delete_empty_folders'];
    }

    public function setDeleteEmptyFolders($value)
    {
        return $this->attributes['delete_empty_folders'] = $value;
    }
    // boolean # Is this sync disabled?
    public function getDisabled()
    {
        return @$this->attributes['disabled'];
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // string # Trigger type: daily, custom_schedule, or manual
    public function getTrigger()
    {
        return @$this->attributes['trigger'];
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // string # Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    public function getTriggerFile()
    {
        return @$this->attributes['trigger_file'];
    }

    public function setTriggerFile($value)
    {
        return $this->attributes['trigger_file'] = $value;
    }
    // array(string) # Array of glob patterns to include
    public function getIncludePatterns()
    {
        return @$this->attributes['include_patterns'];
    }

    public function setIncludePatterns($value)
    {
        return $this->attributes['include_patterns'] = $value;
    }
    // array(string) # Array of glob patterns to exclude
    public function getExcludePatterns()
    {
        return @$this->attributes['exclude_patterns'];
    }

    public function setExcludePatterns($value)
    {
        return $this->attributes['exclude_patterns'] = $value;
    }
    // date-time # When this sync was created
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # When this sync was last updated
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }
    // int64 # Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    public function getSyncIntervalMinutes()
    {
        return @$this->attributes['sync_interval_minutes'];
    }

    public function setSyncIntervalMinutes($value)
    {
        return $this->attributes['sync_interval_minutes'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    public function getInterval()
    {
        return @$this->attributes['interval'];
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // int64 # If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    public function getRecurringDay()
    {
        return @$this->attributes['recurring_day'];
    }

    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    // array(int64) # If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    public function getScheduleDaysOfWeek()
    {
        return @$this->attributes['schedule_days_of_week'];
    }

    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    // array(string) # If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. Times of day in HH:MM format.
    public function getScheduleTimesOfDay()
    {
        return @$this->attributes['schedule_times_of_day'];
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # If trigger is `custom_schedule`, Custom schedule Time Zone for when the sync should be run.
    public function getScheduleTimeZone()
    {
        return @$this->attributes['schedule_time_zone'];
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # If trigger is `custom_schedule`, the sync will check if there is a formal, observed holiday for the region, and if so, it will not run.
    public function getHolidayRegion()
    {
        return @$this->attributes['holiday_region'];
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    // SyncRun # The latest run of this sync
    public function getLatestSyncRun()
    {
        return @$this->attributes['latest_sync_run'];
    }

    public function setLatestSyncRun($value)
    {
        return $this->attributes['latest_sync_run'] = $value;
    }

    // Dry Run Sync
    public function dryRun($params = [])
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

        $response = Api::sendRequest('/syncs/' . @$params['id'] . '/dry_run', 'POST', $params, $this->options);
        return;
    }

    // Manually Run Sync
    public function manualRun($params = [])
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

        $response = Api::sendRequest('/syncs/' . @$params['id'] . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   name - string - Name for this sync job
    //   description - string - Description for this sync job
    //   src_path - string - Absolute source path
    //   dest_path - string - Absolute destination path
    //   src_remote_server_id - int64 - Remote server ID for the source
    //   dest_remote_server_id - int64 - Remote server ID for the destination
    //   keep_after_copy - boolean - Keep files after copying?
    //   delete_empty_folders - boolean - Delete empty folders after sync?
    //   disabled - boolean - Is this sync disabled?
    //   interval - string - If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   trigger - string - Trigger type: daily, custom_schedule, or manual
    //   trigger_file - string - Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    //   holiday_region - string - If trigger is `custom_schedule`, the sync will check if there is a formal, observed holiday for the region, and if so, it will not run.
    //   sync_interval_minutes - int64 - Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   schedule_time_zone - string - If trigger is `custom_schedule`, Custom schedule Time Zone for when the sync should be run.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    //   schedule_times_of_day - array(string) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. Times of day in HH:MM format.
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

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['src_path'] && !is_string(@$params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (@$params['dest_path'] && !is_string(@$params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (@$params['src_remote_server_id'] && !is_int(@$params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (@$params['dest_remote_server_id'] && !is_int(@$params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_file'] && !is_string(@$params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['sync_interval_minutes'] && !is_int(@$params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        $response = Api::sendRequest('/syncs/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Sync((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/syncs/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `src_remote_server_id` and `dest_remote_server_id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/syncs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Sync((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Sync ID.
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

        $response = Api::sendRequest('/syncs/' . @$params['id'] . '', 'GET', $params, $options);

        return new Sync((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name - string - Name for this sync job
    //   description - string - Description for this sync job
    //   src_path - string - Absolute source path
    //   dest_path - string - Absolute destination path
    //   src_remote_server_id - int64 - Remote server ID for the source
    //   dest_remote_server_id - int64 - Remote server ID for the destination
    //   keep_after_copy - boolean - Keep files after copying?
    //   delete_empty_folders - boolean - Delete empty folders after sync?
    //   disabled - boolean - Is this sync disabled?
    //   interval - string - If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   trigger - string - Trigger type: daily, custom_schedule, or manual
    //   trigger_file - string - Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    //   holiday_region - string - If trigger is `custom_schedule`, the sync will check if there is a formal, observed holiday for the region, and if so, it will not run.
    //   sync_interval_minutes - int64 - Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   schedule_time_zone - string - If trigger is `custom_schedule`, Custom schedule Time Zone for when the sync should be run.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    //   schedule_times_of_day - array(string) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. Times of day in HH:MM format.
    public static function create($params = [], $options = [])
    {
        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['src_path'] && !is_string(@$params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (@$params['dest_path'] && !is_string(@$params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (@$params['src_remote_server_id'] && !is_int(@$params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (@$params['dest_remote_server_id'] && !is_int(@$params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_file'] && !is_string(@$params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['sync_interval_minutes'] && !is_int(@$params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        $response = Api::sendRequest('/syncs', 'POST', $params, $options);

        return new Sync((array) (@$response->data ?: []), $options);
    }
}
