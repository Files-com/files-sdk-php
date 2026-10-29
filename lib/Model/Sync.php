<?php

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
    // int64 # Sync ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Name for this sync job
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Description for this sync job
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // int64 # Site ID this sync belongs to
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }

    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    // int64 # Workspace ID this sync belongs to
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // int64 # User who created or owns this sync
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Absolute source path for the sync
    public function getSrcPath()
    {
        return isset($this->attributes['src_path']) ? $this->attributes['src_path'] : null;
    }

    public function setSrcPath($value)
    {
        return $this->attributes['src_path'] = $value;
    }
    // string # Absolute destination path for the sync
    public function getDestPath()
    {
        return isset($this->attributes['dest_path']) ? $this->attributes['dest_path'] : null;
    }

    public function setDestPath($value)
    {
        return $this->attributes['dest_path'] = $value;
    }
    // int64 # Remote server ID for the source (if remote)
    public function getSrcRemoteServerId()
    {
        return isset($this->attributes['src_remote_server_id']) ? $this->attributes['src_remote_server_id'] : null;
    }

    public function setSrcRemoteServerId($value)
    {
        return $this->attributes['src_remote_server_id'] = $value;
    }
    // int64 # Remote server ID for the destination (if remote)
    public function getDestRemoteServerId()
    {
        return isset($this->attributes['dest_remote_server_id']) ? $this->attributes['dest_remote_server_id'] : null;
    }

    public function setDestRemoteServerId($value)
    {
        return $this->attributes['dest_remote_server_id'] = $value;
    }
    // int64 # Source site ID if syncing from a child or partner site
    public function getSrcSiteId()
    {
        return isset($this->attributes['src_site_id']) ? $this->attributes['src_site_id'] : null;
    }

    public function setSrcSiteId($value)
    {
        return $this->attributes['src_site_id'] = $value;
    }
    // int64 # Destination site ID if syncing to a child or partner site
    public function getDestSiteId()
    {
        return isset($this->attributes['dest_site_id']) ? $this->attributes['dest_site_id'] : null;
    }

    public function setDestSiteId($value)
    {
        return $this->attributes['dest_site_id'] = $value;
    }
    // boolean # Is this a two-way sync?
    public function getTwoWay()
    {
        return isset($this->attributes['two_way']) ? $this->attributes['two_way'] : null;
    }

    public function setTwoWay($value)
    {
        return $this->attributes['two_way'] = $value;
    }
    // boolean # Keep files after copying?
    public function getKeepAfterCopy()
    {
        return isset($this->attributes['keep_after_copy']) ? $this->attributes['keep_after_copy'] : null;
    }

    public function setKeepAfterCopy($value)
    {
        return $this->attributes['keep_after_copy'] = $value;
    }
    // boolean # Delete empty folders after sync?
    public function getDeleteEmptyFolders()
    {
        return isset($this->attributes['delete_empty_folders']) ? $this->attributes['delete_empty_folders'] : null;
    }

    public function setDeleteEmptyFolders($value)
    {
        return $this->attributes['delete_empty_folders'] = $value;
    }
    // boolean # Is this sync disabled?
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // string # Trigger type: daily, custom_schedule, or manual
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // string # Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    public function getTriggerFile()
    {
        return isset($this->attributes['trigger_file']) ? $this->attributes['trigger_file'] : null;
    }

    public function setTriggerFile($value)
    {
        return $this->attributes['trigger_file'] = $value;
    }
    // boolean # If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
    public function getAlwaysWriteTriggerFile()
    {
        return isset($this->attributes['always_write_trigger_file']) ? $this->attributes['always_write_trigger_file'] : null;
    }

    public function setAlwaysWriteTriggerFile($value)
    {
        return $this->attributes['always_write_trigger_file'] = $value;
    }
    // array(string) # Array of glob patterns to include
    public function getIncludePatterns()
    {
        return isset($this->attributes['include_patterns']) ? $this->attributes['include_patterns'] : null;
    }

    public function setIncludePatterns($value)
    {
        return $this->attributes['include_patterns'] = $value;
    }
    // array(string) # Array of glob patterns to exclude
    public function getExcludePatterns()
    {
        return isset($this->attributes['exclude_patterns']) ? $this->attributes['exclude_patterns'] : null;
    }

    public function setExcludePatterns($value)
    {
        return $this->attributes['exclude_patterns'] = $value;
    }
    // date-time # When this sync was created
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // date-time # When this sync was last updated
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }
    // int64 # Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    public function getSyncIntervalMinutes()
    {
        return isset($this->attributes['sync_interval_minutes']) ? $this->attributes['sync_interval_minutes'] : null;
    }

    public function setSyncIntervalMinutes($value)
    {
        return $this->attributes['sync_interval_minutes'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // int64 # If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    public function getRecurringDay()
    {
        return isset($this->attributes['recurring_day']) ? $this->attributes['recurring_day'] : null;
    }

    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    // array(int64) # If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    public function getRecurringDays()
    {
        return isset($this->attributes['recurring_days']) ? $this->attributes['recurring_days'] : null;
    }

    public function setRecurringDays($value)
    {
        return $this->attributes['recurring_days'] = $value;
    }
    // int64 # If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    public function setScheduleId($value)
    {
        return $this->attributes['schedule_id'] = $value;
    }
    // array(int64) # If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    // array(string) # Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # Time zone for the schedule. If not set, times are interpreted as UTC.
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # Skip the sync if there is a formal, observed holiday for this region.
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    // SyncRun # The latest run of this sync
    public function getLatestSyncRun()
    {
        return isset($this->attributes['latest_sync_run']) ? $this->attributes['latest_sync_run'] : null;
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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '/dry_run', 'POST', $params, $this->options);
        return;
    }

    // Manually Run Sync
    public function manualRun($params = [])
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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   delete_empty_folders - boolean - Delete empty folders after sync?
    //   description - string - Description for this sync job
    //   dest_path - string - Absolute destination path for the sync
    //   dest_remote_server_id - int64 - Remote server ID for the destination (if remote)
    //   disabled - boolean - Is this sync disabled?
    //   exclude_patterns - array(string) - Array of glob patterns to exclude
    //   holiday_region - string - Skip the sync if there is a formal, observed holiday for this region.
    //   include_patterns - array(string) - Array of glob patterns to include
    //   interval - string - If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   keep_after_copy - boolean - Keep files after copying?
    //   name - string - Name for this sync job
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   recurring_days - array(int64) - If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   schedule_id - int64 - If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    //   schedule_time_zone - string - Time zone for the schedule. If not set, times are interpreted as UTC.
    //   schedule_times_of_day - array(string) - Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
    //   src_path - string - Absolute source path for the sync
    //   src_remote_server_id - int64 - Remote server ID for the source (if remote)
    //   sync_interval_minutes - int64 - Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    //   trigger - string - Trigger type: daily, custom_schedule, or manual
    //   trigger_file - string - Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    //   always_write_trigger_file - boolean - If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['dest_path']) && !is_string($params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (!empty($params['dest_remote_server_id']) && !is_int($params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (!empty($params['exclude_patterns']) && !is_array($params['exclude_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_patterns must be of type array; received ' . gettype(@$params['exclude_patterns']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['include_patterns']) && !is_array($params['include_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$include_patterns must be of type array; received ' . gettype(@$params['include_patterns']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (!empty($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (!empty($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (!empty($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (!empty($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (!empty($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (!empty($params['src_path']) && !is_string($params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (!empty($params['src_remote_server_id']) && !is_int($params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (!empty($params['sync_interval_minutes']) && !is_int($params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['trigger_file']) && !is_string($params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Sync((array) (@$response->data ?: []), $this->options);
    }

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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id` or `name`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`, `disabled`, `src_remote_server_id` or `dest_remote_server_id`. Valid field combinations are `[ workspace_id, disabled ]`, `[ workspace_id, src_remote_server_id ]`, `[ workspace_id, dest_remote_server_id ]`, `[ disabled, src_remote_server_id ]`, `[ disabled, dest_remote_server_id ]`, `[ workspace_id, disabled, src_remote_server_id ]` or `[ workspace_id, disabled, dest_remote_server_id ]`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
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

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Sync((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   delete_empty_folders - boolean - Delete empty folders after sync?
    //   description - string - Description for this sync job
    //   dest_path - string - Absolute destination path for the sync
    //   dest_remote_server_id - int64 - Remote server ID for the destination (if remote)
    //   disabled - boolean - Is this sync disabled?
    //   exclude_patterns - array(string) - Array of glob patterns to exclude
    //   holiday_region - string - Skip the sync if there is a formal, observed holiday for this region.
    //   include_patterns - array(string) - Array of glob patterns to include
    //   interval - string - If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   keep_after_copy - boolean - Keep files after copying?
    //   name - string - Name for this sync job
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   recurring_days - array(int64) - If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   schedule_id - int64 - If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    //   schedule_time_zone - string - Time zone for the schedule. If not set, times are interpreted as UTC.
    //   schedule_times_of_day - array(string) - Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
    //   src_path - string - Absolute source path for the sync
    //   src_remote_server_id - int64 - Remote server ID for the source (if remote)
    //   sync_interval_minutes - int64 - Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
    //   trigger - string - Trigger type: daily, custom_schedule, or manual
    //   trigger_file - string - Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
    //   always_write_trigger_file - boolean - If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
    //   workspace_id - int64 - Workspace ID this sync belongs to
    public static function create($params = [], $options = [])
    {
        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['dest_path']) && !is_string($params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (!empty($params['dest_remote_server_id']) && !is_int($params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (!empty($params['exclude_patterns']) && !is_array($params['exclude_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_patterns must be of type array; received ' . gettype(@$params['exclude_patterns']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['include_patterns']) && !is_array($params['include_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$include_patterns must be of type array; received ' . gettype(@$params['include_patterns']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (!empty($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (!empty($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (!empty($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (!empty($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (!empty($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (!empty($params['src_path']) && !is_string($params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (!empty($params['src_remote_server_id']) && !is_int($params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (!empty($params['sync_interval_minutes']) && !is_int($params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['trigger_file']) && !is_string($params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/syncs', 'POST', $params, $options);

        return new Sync((array) (@$response->data ?: []), $options);
    }
}
