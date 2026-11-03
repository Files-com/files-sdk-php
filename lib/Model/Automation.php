<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An Automation is an automated process of controlling workflows on your Files.com site.
 *
 * @property int|null $id Automation ID
 * @property int|null $workspace_id Workspace ID
 * @property bool|null $always_serialize_jobs Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
 * @property bool|null $always_overwrite_size_matching_files Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer. If this flag is `true` we will overwrite the destination file always. Note that this may cause large amounts of wasted transfer usage. This setting has no effect unless `overwrite_files` is also set to `true`.
 * @property string|null $automation Automation type
 * @property int|null $converted_automation_id ID of this v1 Automation's converted v2 copy, when present.
 * @property bool|null $deleted Indicates if the automation has been deleted.
 * @property string|null $description Description for the this Automation.
 * @property object|null $definition Automation v2 graph definition.
 * @property string|null $destination_replace_from If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
 * @property string|null $destination_replace_to If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
 * @property string[]|null $destinations Destination Paths
 * @property bool|null $disabled If true, this automation will not run.
 * @property string|null $exclude_pattern If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
 * @property object[]|null $import_urls List of URLs to be imported and names to be used.
 * @property string|null $inbound_email_address If trigger is `email`, this is the address that triggers the Automation.
 * @property bool|null $flatten_destination_structure Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination. If this flag is `true`, the source folder structure will be flattened in the destination. This is useful for copying or moving files from multiple folders into a single destination folder.
 * @property int[]|null $group_ids IDs of Groups for the Automation (i.e. who to Request File from)
 * @property bool|null $ignore_locked_folders If true, the Lock Folders behavior will be disregarded for automated actions.
 * @property string|null $interval If trigger is `daily`, this specifies how often to run this automation. One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
 * @property string|null $last_modified_at Time when automation was last modified. Does not change for name or description updates.
 * @property bool|null $legacy_folder_matching If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files. This behavior no longer works and should not be used.
 * @property string|null $name Name for this automation.
 * @property int|null $original_automation_id ID of the retained v1 Automation for a converted Automation. Re-enable that v1 Automation to pause this copy and revert the conversion.
 * @property bool|null $overwrite_files If true, existing files will be overwritten with new files on Move/Copy automations. Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file. Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
 * @property string|null $path Path on which this Automation runs. Supports globs, except on remote mounts. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $path_time_zone Timezone to use when rendering timestamps in paths.
 * @property int|null $version Current Automation v2 definition version.
 * @property int|null $recurring_day If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
 * @property int[]|null $recurring_days If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
 * @property int|null $schedule_id If trigger is `custom_schedule`, the reusable Schedule used instead of the automation's schedule fields.
 * @property int|null $retry_on_failure_interval_in_minutes If the Automation fails, retry at this interval (in minutes). Acceptable values are 5 through 1440 (one day). Set to null to disable.
 * @property int|null $retry_on_failure_number_of_attempts If the Automation fails, retry at most this many times. Maximum allowed value: 10. Set to null to disable.
 * @property object|null $schedule If trigger is `custom_schedule`, Custom schedule description for when the automation should be run in json format.
 * @property string|null $human_readable_schedule If trigger is `custom_schedule` or `daily` with times, Human readable schedule description for when the automation should be run.
 * @property int[]|null $schedule_days_of_week If trigger is `custom_schedule`, Custom schedule description for when the automation should be run. 0 is Sunday, 1 is Monday, etc.
 * @property string[]|null $schedule_times_of_day Times of day to run in HH:MM format (24-hour). For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
 * @property string|null $schedule_time_zone Time zone for the schedule. If not set, times are interpreted as UTC.
 * @property string|null $source Source path/glob. See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
 * @property int[]|null $sync_ids IDs of Syncs to run by this Automation.
 * @property string[]|null $trigger_actions If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, copy, move, archived_delete, update, read, destroy
 * @property string|null $trigger How this automation is triggered to run.
 * @property int|null $user_id User ID of the Automation's creator.
 * @property int[]|null $user_ids IDs of Users for the Automation (i.e. who to Request File from)
 * @property object|null $value A Hash of attributes specific to the automation type.
 * @property string|null $webhook_url If trigger is `webhook`, this is the URL of the webhook to trigger the Automation.
 * @property string|null $holiday_region Skip the automation if there is a formal, observed holiday for this region.
 * @package Files
 */
class Automation
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
     * Automation ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Automation ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace ID
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
     *
     * @return bool|null
     */
    public function getAlwaysSerializeJobs()
    {
        return isset($this->attributes['always_serialize_jobs']) ? $this->attributes['always_serialize_jobs'] : null;
    }

    /**
     * Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAlwaysSerializeJobs($value)
    {
        return $this->attributes['always_serialize_jobs'] = $value;
    }
    /**
     * Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
     *
     * @return bool|null
     */
    public function getAlwaysOverwriteSizeMatchingFiles()
    {
        return isset($this->attributes['always_overwrite_size_matching_files']) ? $this->attributes['always_overwrite_size_matching_files'] : null;
    }

    /**
     * Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAlwaysOverwriteSizeMatchingFiles($value)
    {
        return $this->attributes['always_overwrite_size_matching_files'] = $value;
    }
    /**
     * Automation type
     *
     * @return string|null
     */
    public function getAutomation()
    {
        return isset($this->attributes['automation']) ? $this->attributes['automation'] : null;
    }

    /**
     * Automation type
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAutomation($value)
    {
        return $this->attributes['automation'] = $value;
    }
    /**
     * ID of this v1 Automation's converted v2 copy, when present.
     *
     * @return int|null
     */
    public function getConvertedAutomationId()
    {
        return isset($this->attributes['converted_automation_id']) ? $this->attributes['converted_automation_id'] : null;
    }

    /**
     * ID of this v1 Automation's converted v2 copy, when present.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setConvertedAutomationId($value)
    {
        return $this->attributes['converted_automation_id'] = $value;
    }
    /**
     * Indicates if the automation has been deleted.
     *
     * @return bool|null
     */
    public function getDeleted()
    {
        return isset($this->attributes['deleted']) ? $this->attributes['deleted'] : null;
    }

    /**
     * Indicates if the automation has been deleted.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDeleted($value)
    {
        return $this->attributes['deleted'] = $value;
    }
    /**
     * Description for the this Automation.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Description for the this Automation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Automation v2 graph definition.
     *
     * @return object|null
     */
    public function getDefinition()
    {
        return isset($this->attributes['definition']) ? $this->attributes['definition'] : null;
    }

    /**
     * Automation v2 graph definition.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setDefinition($value)
    {
        return $this->attributes['definition'] = $value;
    }
    /**
     * If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
     *
     * @return string|null
     */
    public function getDestinationReplaceFrom()
    {
        return isset($this->attributes['destination_replace_from']) ? $this->attributes['destination_replace_from'] : null;
    }

    /**
     * If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestinationReplaceFrom($value)
    {
        return $this->attributes['destination_replace_from'] = $value;
    }
    /**
     * If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
     *
     * @return string|null
     */
    public function getDestinationReplaceTo()
    {
        return isset($this->attributes['destination_replace_to']) ? $this->attributes['destination_replace_to'] : null;
    }

    /**
     * If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestinationReplaceTo($value)
    {
        return $this->attributes['destination_replace_to'] = $value;
    }
    /**
     * Destination Paths
     *
     * @return string[]|null
     */
    public function getDestinations()
    {
        return isset($this->attributes['destinations']) ? $this->attributes['destinations'] : null;
    }

    /**
     * Destination Paths
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setDestinations($value)
    {
        return $this->attributes['destinations'] = $value;
    }
    /**
     * If true, this automation will not run.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * If true, this automation will not run.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
     *
     * @return string|null
     */
    public function getExcludePattern()
    {
        return isset($this->attributes['exclude_pattern']) ? $this->attributes['exclude_pattern'] : null;
    }

    /**
     * If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExcludePattern($value)
    {
        return $this->attributes['exclude_pattern'] = $value;
    }
    /**
     * List of URLs to be imported and names to be used.
     *
     * @return object[]|null
     */
    public function getImportUrls()
    {
        return isset($this->attributes['import_urls']) ? $this->attributes['import_urls'] : null;
    }

    /**
     * List of URLs to be imported and names to be used.
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setImportUrls($value)
    {
        return $this->attributes['import_urls'] = $value;
    }
    /**
     * If trigger is `email`, this is the address that triggers the Automation.
     *
     * @return string|null
     */
    public function getInboundEmailAddress()
    {
        return isset($this->attributes['inbound_email_address']) ? $this->attributes['inbound_email_address'] : null;
    }

    /**
     * If trigger is `email`, this is the address that triggers the Automation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setInboundEmailAddress($value)
    {
        return $this->attributes['inbound_email_address'] = $value;
    }
    /**
     * Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
     *
     * @return bool|null
     */
    public function getFlattenDestinationStructure()
    {
        return isset($this->attributes['flatten_destination_structure']) ? $this->attributes['flatten_destination_structure'] : null;
    }

    /**
     * Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFlattenDestinationStructure($value)
    {
        return $this->attributes['flatten_destination_structure'] = $value;
    }
    /**
     * IDs of Groups for the Automation (i.e. who to Request File from)
     *
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * IDs of Groups for the Automation (i.e. who to Request File from)
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    /**
     * If true, the Lock Folders behavior will be disregarded for automated actions.
     *
     * @return bool|null
     */
    public function getIgnoreLockedFolders()
    {
        return isset($this->attributes['ignore_locked_folders']) ? $this->attributes['ignore_locked_folders'] : null;
    }

    /**
     * If true, the Lock Folders behavior will be disregarded for automated actions.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setIgnoreLockedFolders($value)
    {
        return $this->attributes['ignore_locked_folders'] = $value;
    }
    /**
     * If trigger is `daily`, this specifies how often to run this automation.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *
     * @return string|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * If trigger is `daily`, this specifies how often to run this automation.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    /**
     * Time when automation was last modified. Does not change for name or description updates.
     *
     * @return string|null
     */
    public function getLastModifiedAt()
    {
        return isset($this->attributes['last_modified_at']) ? $this->attributes['last_modified_at'] : null;
    }

    /**
     * Time when automation was last modified. Does not change for name or description updates.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastModifiedAt($value)
    {
        return $this->attributes['last_modified_at'] = $value;
    }
    /**
     * If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
     *
     * @return bool|null
     */
    public function getLegacyFolderMatching()
    {
        return isset($this->attributes['legacy_folder_matching']) ? $this->attributes['legacy_folder_matching'] : null;
    }

    /**
     * If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setLegacyFolderMatching($value)
    {
        return $this->attributes['legacy_folder_matching'] = $value;
    }
    /**
     * Name for this automation.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this automation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * ID of the retained v1 Automation for a converted Automation. Re-enable that v1 Automation to pause this copy and revert the conversion.
     *
     * @return int|null
     */
    public function getOriginalAutomationId()
    {
        return isset($this->attributes['original_automation_id']) ? $this->attributes['original_automation_id'] : null;
    }

    /**
     * ID of the retained v1 Automation for a converted Automation. Re-enable that v1 Automation to pause this copy and revert the conversion.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setOriginalAutomationId($value)
    {
        return $this->attributes['original_automation_id'] = $value;
    }
    /**
     * If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
     *
     * @return bool|null
     */
    public function getOverwriteFiles()
    {
        return isset($this->attributes['overwrite_files']) ? $this->attributes['overwrite_files'] : null;
    }

    /**
     * If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setOverwriteFiles($value)
    {
        return $this->attributes['overwrite_files'] = $value;
    }
    /**
     * Path on which this Automation runs.  Supports globs, except on remote mounts. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Path on which this Automation runs.  Supports globs, except on remote mounts. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Timezone to use when rendering timestamps in paths.
     *
     * @return string|null
     */
    public function getPathTimeZone()
    {
        return isset($this->attributes['path_time_zone']) ? $this->attributes['path_time_zone'] : null;
    }

    /**
     * Timezone to use when rendering timestamps in paths.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPathTimeZone($value)
    {
        return $this->attributes['path_time_zone'] = $value;
    }
    /**
     * Current Automation v2 definition version.
     *
     * @return int|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }

    /**
     * Current Automation v2 definition version.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setVersion($value)
    {
        return $this->attributes['version'] = $value;
    }
    /**
     * If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *
     * @return int|null
     */
    public function getRecurringDay()
    {
        return isset($this->attributes['recurring_day']) ? $this->attributes['recurring_day'] : null;
    }

    /**
     * If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    /**
     * If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *
     * @return int[]|null
     */
    public function getRecurringDays()
    {
        return isset($this->attributes['recurring_days']) ? $this->attributes['recurring_days'] : null;
    }

    /**
     * If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setRecurringDays($value)
    {
        return $this->attributes['recurring_days'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the automation's schedule fields.
     *
     * @return int|null
     */
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the automation's schedule fields.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setScheduleId($value)
    {
        return $this->attributes['schedule_id'] = $value;
    }
    /**
     * If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
     *
     * @return int|null
     */
    public function getRetryOnFailureIntervalInMinutes()
    {
        return isset($this->attributes['retry_on_failure_interval_in_minutes']) ? $this->attributes['retry_on_failure_interval_in_minutes'] : null;
    }

    /**
     * If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRetryOnFailureIntervalInMinutes($value)
    {
        return $this->attributes['retry_on_failure_interval_in_minutes'] = $value;
    }
    /**
     * If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
     *
     * @return int|null
     */
    public function getRetryOnFailureNumberOfAttempts()
    {
        return isset($this->attributes['retry_on_failure_number_of_attempts']) ? $this->attributes['retry_on_failure_number_of_attempts'] : null;
    }

    /**
     * If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRetryOnFailureNumberOfAttempts($value)
    {
        return $this->attributes['retry_on_failure_number_of_attempts'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the automation should be run in json format.
     *
     * @return object|null
     */
    public function getSchedule()
    {
        return isset($this->attributes['schedule']) ? $this->attributes['schedule'] : null;
    }

    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the automation should be run in json format.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setSchedule($value)
    {
        return $this->attributes['schedule'] = $value;
    }
    /**
     * If trigger is `custom_schedule` or `daily` with times, Human readable schedule description for when the automation should be run.
     *
     * @return string|null
     */
    public function getHumanReadableSchedule()
    {
        return isset($this->attributes['human_readable_schedule']) ? $this->attributes['human_readable_schedule'] : null;
    }

    /**
     * If trigger is `custom_schedule` or `daily` with times, Human readable schedule description for when the automation should be run.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHumanReadableSchedule($value)
    {
        return $this->attributes['human_readable_schedule'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the automation should be run. 0 is Sunday, 1 is Monday, etc.
     *
     * @return int[]|null
     */
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the automation should be run. 0 is Sunday, 1 is Monday, etc.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    /**
     * Times of day to run in HH:MM format (24-hour). For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day to run in HH:MM format (24-hour). For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    /**
     * Time zone for the schedule. If not set, times are interpreted as UTC.
     *
     * @return string|null
     */
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    /**
     * Time zone for the schedule. If not set, times are interpreted as UTC.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    /**
     * Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }

    /**
     * Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    /**
     * IDs of Syncs to run by this Automation.
     *
     * @return int[]|null
     */
    public function getSyncIds()
    {
        return isset($this->attributes['sync_ids']) ? $this->attributes['sync_ids'] : null;
    }

    /**
     * IDs of Syncs to run by this Automation.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setSyncIds($value)
    {
        return $this->attributes['sync_ids'] = $value;
    }
    /**
     * If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, copy, move, archived_delete, update, read, destroy
     *
     * @return string[]|null
     */
    public function getTriggerActions()
    {
        return isset($this->attributes['trigger_actions']) ? $this->attributes['trigger_actions'] : null;
    }

    /**
     * If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, copy, move, archived_delete, update, read, destroy
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setTriggerActions($value)
    {
        return $this->attributes['trigger_actions'] = $value;
    }
    /**
     * How this automation is triggered to run.
     *
     * @return string|null
     */
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    /**
     * How this automation is triggered to run.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    /**
     * User ID of the Automation's creator.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID of the Automation's creator.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * IDs of Users for the Automation (i.e. who to Request File from)
     *
     * @return int[]|null
     */
    public function getUserIds()
    {
        return isset($this->attributes['user_ids']) ? $this->attributes['user_ids'] : null;
    }

    /**
     * IDs of Users for the Automation (i.e. who to Request File from)
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }
    /**
     * A Hash of attributes specific to the automation type.
     *
     * @return object|null
     */
    public function getValue()
    {
        return isset($this->attributes['value']) ? $this->attributes['value'] : null;
    }

    /**
     * A Hash of attributes specific to the automation type.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    /**
     * If trigger is `webhook`, this is the URL of the webhook to trigger the Automation.
     *
     * @return string|null
     */
    public function getWebhookUrl()
    {
        return isset($this->attributes['webhook_url']) ? $this->attributes['webhook_url'] : null;
    }

    /**
     * If trigger is `webhook`, this is the URL of the webhook to trigger the Automation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setWebhookUrl($value)
    {
        return $this->attributes['webhook_url'] = $value;
    }
    /**
     * Skip the automation if there is a formal, observed holiday for this region.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Skip the automation if there is a formal, observed holiday for this region.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }

    /**
     * Upgrade a legacy Automation to Automation v2
     *
     * POST /automations/{id}/upgrade
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation ID.
     * @return \Files\Model\Automation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function upgrade($params = [])
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

        $response = Api::sendRequest('/automations/' . rawurlencode(strval(@$params['id'])) . '/upgrade', 'POST', $params, $this->options);
        return new Automation(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Manually Run Automation
     *
     * POST /automations/{id}/manual_run
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation ID.
     *     - `items` ((array|object)[], optional): Initial items for a v2 manual trigger. Each item contains exactly one `file` path or `data` object.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['items']) && !is_array($params['items'])) {
            throw new \Files\Exception\InvalidParameterException('$items must be of type array; received ' . gettype(@$params['items']));
        }

        $response = Api::sendRequest('/automations/' . rawurlencode(strval(@$params['id'])) . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Automation with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Automation
     *
     * PATCH /automations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation ID.
     *     - `source` (string, optional): Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
     *     - `destinations` (string[], optional): A list of destination paths. Use a trailing slash for folder destinations and omit it for file destinations.
     *     - `destination_replace_from` (string, optional): If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
     *     - `destination_replace_to` (string, optional): If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
     *     - `interval` (string, optional): How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *     - `path` (string, optional): Path on which this Automation runs.  Supports globs, except on remote mounts.
     *     - `sync_ids` (string, optional): A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `user_ids` (string, optional): A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `group_ids` (string, optional): A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the automation's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule` without `schedule_id`, a list of days of the week to run this automation. 0 is Sunday, 1 is Monday, etc.
     *     - `schedule_times_of_day` (string[], optional): Times of day to run in HH:MM format (24-hour). Required for `custom_schedule` without `schedule_id`. Optional for `daily` triggers; if not set, runs at midnight UTC.
     *     - `schedule_time_zone` (string, optional): Time zone for schedule fields. Optional for `custom_schedule` without `schedule_id` and for `daily`. If not set, times are interpreted as UTC.
     *     - `holiday_region` (string, optional): Skip automation on holidays in this region. Optional for `custom_schedule` without `schedule_id` and for `daily`.
     *     - `always_overwrite_size_matching_files` (bool, optional): Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
     *     - `always_serialize_jobs` (bool, optional): Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
     *     - `description` (string, optional): Description for the this Automation.
     *     - `definition` (array|object, optional): Automation v2 graph definition.
     *     - `disabled` (bool, optional): If true, this automation will not run.
     *     - `exclude_pattern` (string, optional): If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
     *     - `import_urls` ((array|object)[], optional): List of URLs to be imported and names to be used.
     *     - `flatten_destination_structure` (bool, optional): Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
     *     - `ignore_locked_folders` (bool, optional): If true, the Lock Folders behavior will be disregarded for automated actions.
     *     - `legacy_folder_matching` (bool, optional): DEPRECATED: If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
     *     - `name` (string, optional): Name for this automation.
     *     - `overwrite_files` (bool, optional): If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
     *     - `path_time_zone` (string, optional): Timezone to use when rendering timestamps in paths.
     *     - `retry_on_failure_interval_in_minutes` (int, optional): If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
     *     - `retry_on_failure_number_of_attempts` (int, optional): If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
     *     - `trigger` (string, optional): How this automation is triggered to run. Allowed values: manual, daily, custom_schedule, webhook, email, action.
     *     - `trigger_actions` (string[], optional): If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, copy, move, archived_delete, update, read, destroy
     *     - `value` (array|object, optional): A Hash of attributes specific to the automation type.
     *     - `recurring_day` (int, optional): If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `recurring_days` (int[], optional): If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `automation` (string, optional): Automation type Allowed values: create_folder, delete_file, copy_file, move_file, as2_send, run_sync, import_file, v2.
     * @return \Files\Model\Automation
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

        if (isset($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (isset($params['destinations']) && !is_array($params['destinations'])) {
            throw new \Files\Exception\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
        }

        if (isset($params['destination_replace_from']) && !is_string($params['destination_replace_from'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
        }

        if (isset($params['destination_replace_to']) && !is_string($params['destination_replace_to'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
        }

        if (isset($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['sync_ids']) && !is_string($params['sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
        }

        if (isset($params['user_ids']) && !is_string($params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (isset($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (isset($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (isset($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (isset($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (isset($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (isset($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (isset($params['always_overwrite_size_matching_files']) && !is_scalar($params['always_overwrite_size_matching_files'])) {
            throw new \Files\Exception\InvalidParameterException('$always_overwrite_size_matching_files must be of type boolean; received ' . gettype(@$params['always_overwrite_size_matching_files']));
        }

        if (isset($params['always_serialize_jobs']) && !is_scalar($params['always_serialize_jobs'])) {
            throw new \Files\Exception\InvalidParameterException('$always_serialize_jobs must be of type boolean; received ' . gettype(@$params['always_serialize_jobs']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['disabled']) && !is_scalar($params['disabled'])) {
            throw new \Files\Exception\InvalidParameterException('$disabled must be of type boolean; received ' . gettype(@$params['disabled']));
        }

        if (isset($params['exclude_pattern']) && !is_string($params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (isset($params['import_urls']) && !is_array($params['import_urls'])) {
            throw new \Files\Exception\InvalidParameterException('$import_urls must be of type array; received ' . gettype(@$params['import_urls']));
        }

        if (isset($params['flatten_destination_structure']) && !is_scalar($params['flatten_destination_structure'])) {
            throw new \Files\Exception\InvalidParameterException('$flatten_destination_structure must be of type boolean; received ' . gettype(@$params['flatten_destination_structure']));
        }

        if (isset($params['ignore_locked_folders']) && !is_scalar($params['ignore_locked_folders'])) {
            throw new \Files\Exception\InvalidParameterException('$ignore_locked_folders must be of type boolean; received ' . gettype(@$params['ignore_locked_folders']));
        }

        if (isset($params['legacy_folder_matching']) && !is_scalar($params['legacy_folder_matching'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_folder_matching must be of type boolean; received ' . gettype(@$params['legacy_folder_matching']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['overwrite_files']) && !is_scalar($params['overwrite_files'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite_files must be of type boolean; received ' . gettype(@$params['overwrite_files']));
        }

        if (isset($params['path_time_zone']) && !is_string($params['path_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_time_zone must be of type string; received ' . gettype(@$params['path_time_zone']));
        }

        if (isset($params['retry_on_failure_interval_in_minutes']) && !is_int($params['retry_on_failure_interval_in_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_interval_in_minutes must be of type int; received ' . gettype(@$params['retry_on_failure_interval_in_minutes']));
        }

        if (isset($params['retry_on_failure_number_of_attempts']) && !is_int($params['retry_on_failure_number_of_attempts'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_number_of_attempts must be of type int; received ' . gettype(@$params['retry_on_failure_number_of_attempts']));
        }

        if (isset($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (isset($params['trigger_actions']) && !is_array($params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (isset($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (isset($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (isset($params['automation']) && !is_string($params['automation'])) {
            throw new \Files\Exception\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
        }

        $response = Api::sendRequest('/automations/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Automation(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Automation
     *
     * DELETE /automations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation ID.
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

        $response = Api::sendRequest('/automations/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Automation
     *
     * DELETE /automations/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation ID.
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
     * List Automations
     *
     * GET /automations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name`, `automation`, `last_modified_at` or `disabled`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled`, `last_modified_at`, `workspace_id` or `automation`. Valid field combinations are `[ disabled, last_modified_at ]`, `[ workspace_id, disabled ]`, `[ disabled, automation ]`, `[ workspace_id, last_modified_at ]`, `[ automation, last_modified_at ]`, `[ workspace_id, automation ]`, `[ workspace_id, disabled, last_modified_at ]`, `[ disabled, automation, last_modified_at ]`, `[ workspace_id, disabled, automation ]`, `[ workspace_id, automation, last_modified_at ]` or `[ workspace_id, disabled, automation, last_modified_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `last_modified_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `last_modified_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `last_modified_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `last_modified_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Automation[]
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

        $response = Api::sendRequest('/automations', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Automation(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/automations', $params, $options, Automation::class);
    }

    /**
     * Show Automation
     *
     * GET /automations/{id}
     *
     * @param int $id Automation ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Automation
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

        $response = Api::sendRequest('/automations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Automation(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Automation
     *
     * GET /automations/{id}
     *
     * Alias of find().
     *
     * @param int $id Automation ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Automation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Show the Automation v2 authoring schema and active node catalog
     *
     * GET /automations/authoring_schema
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationAuthoringSchema
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function getAuthoringSchema($params = [], $options = [])
    {
        $response = Api::sendRequest('/automations/authoring_schema', 'GET', null, $options);

        return new AutomationAuthoringSchema(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Create Automation
     *
     * POST /automations
     *
     * @param array $params Request parameters.
     *     - `source` (string, optional): Source path/glob.  See Automation docs for exact description, but this is used to filter for files in the `path` to find files to operate on. Supports globs, except on remote mounts.
     *     - `destinations` (string[], optional): A list of destination paths. Use a trailing slash for folder destinations and omit it for file destinations.
     *     - `destination_replace_from` (string, optional): If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
     *     - `destination_replace_to` (string, optional): If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
     *     - `interval` (string, optional): How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *     - `path` (string, optional): Path on which this Automation runs.  Supports globs, except on remote mounts.
     *     - `sync_ids` (string, optional): A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `user_ids` (string, optional): A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `group_ids` (string, optional): A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the automation's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule` without `schedule_id`, a list of days of the week to run this automation. 0 is Sunday, 1 is Monday, etc.
     *     - `schedule_times_of_day` (string[], optional): Times of day to run in HH:MM format (24-hour). Required for `custom_schedule` without `schedule_id`. Optional for `daily` triggers; if not set, runs at midnight UTC.
     *     - `schedule_time_zone` (string, optional): Time zone for schedule fields. Optional for `custom_schedule` without `schedule_id` and for `daily`. If not set, times are interpreted as UTC.
     *     - `holiday_region` (string, optional): Skip automation on holidays in this region. Optional for `custom_schedule` without `schedule_id` and for `daily`.
     *     - `always_overwrite_size_matching_files` (bool, optional): Ordinarily, files with identical size in the source and destination will be skipped from copy operations to prevent wasted transfer.  If this flag is `true` we will overwrite the destination file always.  Note that this may cause large amounts of wasted transfer usage.  This setting has no effect unless `overwrite_files` is also set to `true`.
     *     - `always_serialize_jobs` (bool, optional): Ordinarily, we will allow automation runs to run in parallel for non-scheduled automations. If this flag is `true` we will force automation runs to be serialized (run one at a time, one after another). This can resolve some issues with race conditions on remote systems at the cost of some performance.
     *     - `description` (string, optional): Description for the this Automation.
     *     - `definition` (array|object, optional): Automation v2 graph definition.
     *     - `disabled` (bool, optional): If true, this automation will not run.
     *     - `exclude_pattern` (string, optional): If set, this glob pattern will exclude files from the automation. Supports globs, except on remote mounts.
     *     - `import_urls` ((array|object)[], optional): List of URLs to be imported and names to be used.
     *     - `flatten_destination_structure` (bool, optional): Normally copy and move automations that use globs will implicitly preserve the source folder structure in the destination.  If this flag is `true`, the source folder structure will be flattened in the destination.  This is useful for copying or moving files from multiple folders into a single destination folder.
     *     - `ignore_locked_folders` (bool, optional): If true, the Lock Folders behavior will be disregarded for automated actions.
     *     - `legacy_folder_matching` (bool, optional): DEPRECATED: If `true`, use the legacy behavior for this automation, where it can operate on folders in addition to just files.  This behavior no longer works and should not be used.
     *     - `name` (string, optional): Name for this automation.
     *     - `overwrite_files` (bool, optional): If true, existing files will be overwritten with new files on Move/Copy automations.  Note: by default files will not be overwritten on Copy automations if they appear to be the same file size as the newly incoming file.  Use the `always_overwrite_size_matching_files` option in conjunction with `overwrite_files` to override this behavior and overwrite files no matter what.
     *     - `path_time_zone` (string, optional): Timezone to use when rendering timestamps in paths.
     *     - `retry_on_failure_interval_in_minutes` (int, optional): If the Automation fails, retry at this interval (in minutes).  Acceptable values are 5 through 1440 (one day).  Set to null to disable.
     *     - `retry_on_failure_number_of_attempts` (int, optional): If the Automation fails, retry at most this many times.  Maximum allowed value: 10.  Set to null to disable.
     *     - `trigger` (string, optional): How this automation is triggered to run. Allowed values: manual, daily, custom_schedule, webhook, email, action.
     *     - `trigger_actions` (string[], optional): If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, copy, move, archived_delete, update, read, destroy
     *     - `value` (array|object, optional): A Hash of attributes specific to the automation type.
     *     - `recurring_day` (int, optional): If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `recurring_days` (int[], optional): If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `automation` (string, required): Automation type Allowed values: create_folder, delete_file, copy_file, move_file, as2_send, run_sync, import_file, v2.
     *     - `workspace_id` (int, optional): Workspace ID
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Automation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['automation'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation');
        }

        if (isset($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (isset($params['destinations']) && !is_array($params['destinations'])) {
            throw new \Files\Exception\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
        }

        if (isset($params['destination_replace_from']) && !is_string($params['destination_replace_from'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
        }

        if (isset($params['destination_replace_to']) && !is_string($params['destination_replace_to'])) {
            throw new \Files\Exception\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
        }

        if (isset($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['sync_ids']) && !is_string($params['sync_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
        }

        if (isset($params['user_ids']) && !is_string($params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (isset($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (isset($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (isset($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (isset($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (isset($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (isset($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (isset($params['always_overwrite_size_matching_files']) && !is_scalar($params['always_overwrite_size_matching_files'])) {
            throw new \Files\Exception\InvalidParameterException('$always_overwrite_size_matching_files must be of type boolean; received ' . gettype(@$params['always_overwrite_size_matching_files']));
        }

        if (isset($params['always_serialize_jobs']) && !is_scalar($params['always_serialize_jobs'])) {
            throw new \Files\Exception\InvalidParameterException('$always_serialize_jobs must be of type boolean; received ' . gettype(@$params['always_serialize_jobs']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['disabled']) && !is_scalar($params['disabled'])) {
            throw new \Files\Exception\InvalidParameterException('$disabled must be of type boolean; received ' . gettype(@$params['disabled']));
        }

        if (isset($params['exclude_pattern']) && !is_string($params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (isset($params['import_urls']) && !is_array($params['import_urls'])) {
            throw new \Files\Exception\InvalidParameterException('$import_urls must be of type array; received ' . gettype(@$params['import_urls']));
        }

        if (isset($params['flatten_destination_structure']) && !is_scalar($params['flatten_destination_structure'])) {
            throw new \Files\Exception\InvalidParameterException('$flatten_destination_structure must be of type boolean; received ' . gettype(@$params['flatten_destination_structure']));
        }

        if (isset($params['ignore_locked_folders']) && !is_scalar($params['ignore_locked_folders'])) {
            throw new \Files\Exception\InvalidParameterException('$ignore_locked_folders must be of type boolean; received ' . gettype(@$params['ignore_locked_folders']));
        }

        if (isset($params['legacy_folder_matching']) && !is_scalar($params['legacy_folder_matching'])) {
            throw new \Files\Exception\InvalidParameterException('$legacy_folder_matching must be of type boolean; received ' . gettype(@$params['legacy_folder_matching']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['overwrite_files']) && !is_scalar($params['overwrite_files'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite_files must be of type boolean; received ' . gettype(@$params['overwrite_files']));
        }

        if (isset($params['path_time_zone']) && !is_string($params['path_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_time_zone must be of type string; received ' . gettype(@$params['path_time_zone']));
        }

        if (isset($params['retry_on_failure_interval_in_minutes']) && !is_int($params['retry_on_failure_interval_in_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_interval_in_minutes must be of type int; received ' . gettype(@$params['retry_on_failure_interval_in_minutes']));
        }

        if (isset($params['retry_on_failure_number_of_attempts']) && !is_int($params['retry_on_failure_number_of_attempts'])) {
            throw new \Files\Exception\InvalidParameterException('$retry_on_failure_number_of_attempts must be of type int; received ' . gettype(@$params['retry_on_failure_number_of_attempts']));
        }

        if (isset($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (isset($params['trigger_actions']) && !is_array($params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (isset($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (isset($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (isset($params['automation']) && !is_string($params['automation'])) {
            throw new \Files\Exception\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/automations', 'POST', $params, $options);

        return new Automation(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
