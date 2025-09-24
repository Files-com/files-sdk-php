<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Automation
 *
 * @package Files
 */
class Automation
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
    // int64 # Automation ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // boolean # Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
    public function getAlwaysSerializeJobs()
    {
        return @$this->attributes['always_serialize_jobs'];
    }

    public function setAlwaysSerializeJobs($value)
    {
        return $this->attributes['always_serialize_jobs'] = $value;
    }
    // boolean # Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
    public function getAlwaysOverwriteSizeMatchingFiles()
    {
        return @$this->attributes['always_overwrite_size_matching_files'];
    }

    public function setAlwaysOverwriteSizeMatchingFiles($value)
    {
        return $this->attributes['always_overwrite_size_matching_files'] = $value;
    }
    // string # Automation type
    public function getAutomation()
    {
        return @$this->attributes['automation'];
    }

    public function setAutomation($value)
    {
        return $this->attributes['automation'] = $value;
    }
    // boolean # Indicates if the automation has been deleted.
    public function getDeleted()
    {
        return @$this->attributes['deleted'];
    }

    public function setDeleted($value)
    {
        return $this->attributes['deleted'] = $value;
    }
    // string # Description for the this Automation.
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // string # If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
    public function getDestinationReplaceFrom()
    {
        return @$this->attributes['destination_replace_from'];
    }

    public function setDestinationReplaceFrom($value)
    {
        return $this->attributes['destination_replace_from'] = $value;
    }
    // string # If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
    public function getDestinationReplaceTo()
    {
        return @$this->attributes['destination_replace_to'];
    }

    public function setDestinationReplaceTo($value)
    {
        return $this->attributes['destination_replace_to'] = $value;
    }
    // array(string) # Destination Paths
    public function getDestinations()
    {
        return @$this->attributes['destinations'];
    }

    public function setDestinations($value)
    {
        return $this->attributes['destinations'] = $value;
    }
    // boolean # If true, this automation will not run.
    public function getDisabled()
    {
        return @$this->attributes['disabled'];
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // string # If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
    public function getExcludePattern()
    {
        return @$this->attributes['exclude_pattern'];
    }

    public function setExcludePattern($value)
    {
        return $this->attributes['exclude_pattern'] = $value;
    }
    // array(object) # List of URLs to be imported and names to be used.
    public function getImportUrls()
    {
        return @$this->attributes['import_urls'];
    }

    public function setImportUrls($value)
    {
        return $this->attributes['import_urls'] = $value;
    }
    // boolean # Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
    public function getFlattenDestinationStructure()
    {
        return @$this->attributes['flatten_destination_structure'];
    }

    public function setFlattenDestinationStructure($value)
    {
        return $this->attributes['flatten_destination_structure'] = $value;
    }
    // array(int64) # IDs of Groups for the Automation (i.e. who to Request File from)
    public function getGroupIds()
    {
        return @$this->attributes['group_ids'];
    }

    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    // boolean # If true, the Lock Folders behavior will be disregarded for automated actions.
    public function getIgnoreLockedFolders()
    {
        return @$this->attributes['ignore_locked_folders'];
    }

    public function setIgnoreLockedFolders($value)
    {
        return $this->attributes['ignore_locked_folders'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run this automation.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    public function getInterval()
    {
        return @$this->attributes['interval'];
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // date-time # Time when automation was last modified. Does not change for name or description updates.
    public function getLastModifiedAt()
    {
        return @$this->attributes['last_modified_at'];
    }

    public function setLastModifiedAt($value)
    {
        return $this->attributes['last_modified_at'] = $value;
    }
    // boolean # If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
    public function getLegacyFolderMatching()
    {
        return @$this->attributes['legacy_folder_matching'];
    }

    public function setLegacyFolderMatching($value)
    {
        return $this->attributes['legacy_folder_matching'] = $value;
    }
    // string # Name for this automation.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // boolean # If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
    public function getOverwriteFiles()
    {
        return @$this->attributes['overwrite_files'];
    }

    public function setOverwriteFiles($value)
    {
        return $this->attributes['overwrite_files'] = $value;
    }
    // string # Path on which this Automation runs.  Supports globs, except on remote mounts. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Timezone to use when rendering timestamps in paths.
    public function getPathTimeZone()
    {
        return @$this->attributes['path_time_zone'];
    }

    public function setPathTimeZone($value)
    {
        return $this->attributes['path_time_zone'] = $value;
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
    // int64 # If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
    public function getRetryOnFailureIntervalInMinutes()
    {
        return @$this->attributes['retry_on_failure_interval_in_minutes'];
    }

    public function setRetryOnFailureIntervalInMinutes($value)
    {
        return $this->attributes['retry_on_failure_interval_in_minutes'] = $value;
    }
    // int64 # If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
    public function getRetryOnFailureNumberOfAttempts()
    {
        return @$this->attributes['retry_on_failure_number_of_attempts'];
    }

    public function setRetryOnFailureNumberOfAttempts($value)
    {
        return $this->attributes['retry_on_failure_number_of_attempts'] = $value;
    }
    // object # If trigger is `custom_schedule`, Custom schedule description for when the automation should be run in json format.
    public function getSchedule()
    {
        return @$this->attributes['schedule'];
    }

    public function setSchedule($value)
    {
        return $this->attributes['schedule'] = $value;
    }
    // string # If trigger is `custom_schedule`, Human readable Custom schedule description for when the automation should be run.
    public function getHumanReadableSchedule()
    {
        return @$this->attributes['human_readable_schedule'];
    }

    public function setHumanReadableSchedule($value)
    {
        return $this->attributes['human_readable_schedule'] = $value;
    }
    // array(int64) # If trigger is `custom_schedule`, Custom schedule description for when the automation should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
    public function getScheduleDaysOfWeek()
    {
        return @$this->attributes['schedule_days_of_week'];
    }

    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    // array(string) # If trigger is `custom_schedule`, Custom schedule description for when the automation should be run. Times of day in HH:MM format.
    public function getScheduleTimesOfDay()
    {
        return @$this->attributes['schedule_times_of_day'];
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # If trigger is `custom_schedule`, Custom schedule Time Zone for when the automation should be run.
    public function getScheduleTimeZone()
    {
        return @$this->attributes['schedule_time_zone'];
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
    public function getSource()
    {
        return @$this->attributes['source'];
    }

    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    // array(int64) # IDs of remote sync folder behaviors to run by this Automation
    public function getLegacySyncIds()
    {
        return @$this->attributes['legacy_sync_ids'];
    }

    public function setLegacySyncIds($value)
    {
        return $this->attributes['legacy_sync_ids'] = $value;
    }
    // array(int64) # IDs of syncs to run by this Automation. This is the new way to specify syncs, and it is recommended to use this instead of `legacy_sync_ids`.
    public function getSyncIds()
    {
        return @$this->attributes['sync_ids'];
    }

    public function setSyncIds($value)
    {
        return $this->attributes['sync_ids'] = $value;
    }
    // array(string) # If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, archived_delete, copy
    public function getTriggerActions()
    {
        return @$this->attributes['trigger_actions'];
    }

    public function setTriggerActions($value)
    {
        return $this->attributes['trigger_actions'] = $value;
    }
    // string # How this automation is triggered to run.
    public function getTrigger()
    {
        return @$this->attributes['trigger'];
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // int64 # User ID of the Automation's creator.
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // array(int64) # IDs of Users for the Automation (i.e. who to Request File from)
    public function getUserIds()
    {
        return @$this->attributes['user_ids'];
    }

    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }
    // object # A Hash of attributes specific to the automation type.
    public function getValue()
    {
        return @$this->attributes['value'];
    }

    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    // string # If trigger is `webhook`, this is the URL of the webhook to trigger the Automation.
    public function getWebhookUrl()
    {
        return @$this->attributes['webhook_url'];
    }

    public function setWebhookUrl($value)
    {
        return $this->attributes['webhook_url'] = $value;
    }
    // string # If trigger is `custom_schedule`, the Automation will check if there is a formal, observed holiday for the region, and if so, it will not run.
    public function getHolidayRegion()
    {
        return @$this->attributes['holiday_region'];
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }

    // Manually Run Automation
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

        $response = Api::sendRequest('/automations/' . @$params['id'] . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   source - string - Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
    //   destinations - array(string) - A list of String destination paths or Hash of folder_path and optional file_path.
    //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
    //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
    //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   path - string - Path on which this Automation runs.  Supports globs, except on remote mounts.
    //   legacy_sync_ids - string - A list of legacy sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   sync_ids - string - A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`. A list of days of the week to run this automation. 0 is Sunday, 1 is Monday, etc.
    //   schedule_times_of_day - array(string) - If trigger is `custom_schedule`. A list of times of day to run this automation. 24-hour time format.
    //   schedule_time_zone - string - If trigger is `custom_schedule`. Time zone for the schedule.
    //   holiday_region - string - If trigger is `custom_schedule`, the Automation will check if there is a formal, observed holiday for the region, and if so, it will not run.
    //   always_overwrite_size_matching_files - boolean - Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
    //   always_serialize_jobs - boolean - Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
    //   description - string - Description for the this Automation.
    //   disabled - boolean - If true, this automation will not run.
    //   exclude_pattern - string - If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
    //   import_urls - array(object) - List of URLs to be imported and names to be used.
    //   flatten_destination_structure - boolean - Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
    //   ignore_locked_folders - boolean - If true, the Lock Folders behavior will be disregarded for automated actions.
    //   legacy_folder_matching - boolean - DEPRECATED: If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
    //   name - string - Name for this automation.
    //   overwrite_files - boolean - If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
    //   path_time_zone - string - Timezone to use when rendering timestamps in paths.
    //   retry_on_failure_interval_in_minutes - int64 - If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
    //   retry_on_failure_number_of_attempts - int64 - If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
    //   trigger - string - How this automation is triggered to run.
    //   trigger_actions - array(string) - If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, archived_delete, copy
    //   value - object - A Hash of attributes specific to the automation type.
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   automation - string - Automation type
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

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['destinations'] && !is_array(@$params['destinations'])) {
            throw new \Files\Exception\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
        }

        if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
        }

        if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['legacy_sync_ids'] && !is_string(@$params['legacy_sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_sync_ids must be of type string; received ' . gettype(@$params['legacy_sync_ids']));
        }

        if (@$params['sync_ids'] && !is_string(@$params['sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
        }

        if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['exclude_pattern'] && !is_string(@$params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (@$params['import_urls'] && !is_array(@$params['import_urls'])) {
            throw new \Files\Exception\InvalidParameterException('$import_urls must be of type array; received ' . gettype(@$params['import_urls']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['path_time_zone'] && !is_string(@$params['path_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_time_zone must be of type string; received ' . gettype(@$params['path_time_zone']));
        }

        if (@$params['retry_on_failure_interval_in_minutes'] && !is_int(@$params['retry_on_failure_interval_in_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_interval_in_minutes must be of type int; received ' . gettype(@$params['retry_on_failure_interval_in_minutes']));
        }

        if (@$params['retry_on_failure_number_of_attempts'] && !is_int(@$params['retry_on_failure_number_of_attempts'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_number_of_attempts must be of type int; received ' . gettype(@$params['retry_on_failure_number_of_attempts']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['automation'] && !is_string(@$params['automation'])) {
            throw new \Files\Exception\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
        }

        $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Automation((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `automation`, `disabled`, `last_modified_at` or `name`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled`, `last_modified_at` or `automation`. Valid field combinations are `[ disabled, last_modified_at ]`, `[ automation, disabled ]`, `[ automation, last_modified_at ]` or `[ automation, disabled, last_modified_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `last_modified_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `last_modified_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `last_modified_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `last_modified_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/automations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Automation((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Automation ID.
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

        $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'GET', $params, $options);

        return new Automation((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   source - string - Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
    //   destinations - array(string) - A list of String destination paths or Hash of folder_path and optional file_path.
    //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
    //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
    //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
    //   path - string - Path on which this Automation runs.  Supports globs, except on remote mounts.
    //   legacy_sync_ids - string - A list of legacy sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   sync_ids - string - A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`. A list of days of the week to run this automation. 0 is Sunday, 1 is Monday, etc.
    //   schedule_times_of_day - array(string) - If trigger is `custom_schedule`. A list of times of day to run this automation. 24-hour time format.
    //   schedule_time_zone - string - If trigger is `custom_schedule`. Time zone for the schedule.
    //   holiday_region - string - If trigger is `custom_schedule`, the Automation will check if there is a formal, observed holiday for the region, and if so, it will not run.
    //   always_overwrite_size_matching_files - boolean - Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
    //   always_serialize_jobs - boolean - Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
    //   description - string - Description for the this Automation.
    //   disabled - boolean - If true, this automation will not run.
    //   exclude_pattern - string - If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
    //   import_urls - array(object) - List of URLs to be imported and names to be used.
    //   flatten_destination_structure - boolean - Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
    //   ignore_locked_folders - boolean - If true, the Lock Folders behavior will be disregarded for automated actions.
    //   legacy_folder_matching - boolean - DEPRECATED: If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
    //   name - string - Name for this automation.
    //   overwrite_files - boolean - If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
    //   path_time_zone - string - Timezone to use when rendering timestamps in paths.
    //   retry_on_failure_interval_in_minutes - int64 - If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
    //   retry_on_failure_number_of_attempts - int64 - If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
    //   trigger - string - How this automation is triggered to run.
    //   trigger_actions - array(string) - If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, archived_delete, copy
    //   value - object - A Hash of attributes specific to the automation type.
    //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
    //   automation (required) - string - Automation type
    public static function create($params = [], $options = [])
    {
        if (!@$params['automation']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation');
        }

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['destinations'] && !is_array(@$params['destinations'])) {
            throw new \Files\Exception\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
        }

        if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
        }

        if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['legacy_sync_ids'] && !is_string(@$params['legacy_sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_sync_ids must be of type string; received ' . gettype(@$params['legacy_sync_ids']));
        }

        if (@$params['sync_ids'] && !is_string(@$params['sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
        }

        if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['exclude_pattern'] && !is_string(@$params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (@$params['import_urls'] && !is_array(@$params['import_urls'])) {
            throw new \Files\Exception\InvalidParameterException('$import_urls must be of type array; received ' . gettype(@$params['import_urls']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['path_time_zone'] && !is_string(@$params['path_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_time_zone must be of type string; received ' . gettype(@$params['path_time_zone']));
        }

        if (@$params['retry_on_failure_interval_in_minutes'] && !is_int(@$params['retry_on_failure_interval_in_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_interval_in_minutes must be of type int; received ' . gettype(@$params['retry_on_failure_interval_in_minutes']));
        }

        if (@$params['retry_on_failure_number_of_attempts'] && !is_int(@$params['retry_on_failure_number_of_attempts'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_number_of_attempts must be of type int; received ' . gettype(@$params['retry_on_failure_number_of_attempts']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['automation'] && !is_string(@$params['automation'])) {
            throw new \Files\Exception\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
        }

        $response = Api::sendRequest('/automations', 'POST', $params, $options);

        return new Automation((array) (@$response->data ?: []), $options);
    }
}
