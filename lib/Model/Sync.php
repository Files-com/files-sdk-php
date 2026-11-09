<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Sync represents a file synchronization job between two locations (local-remote, remote-remote, local-child_site, etc).
 *
 * @property int|null $id Sync ID
 * @property string|null $name Name for this sync job
 * @property string|null $description Description for this sync job
 * @property int|null $site_id Site ID this sync belongs to
 * @property int|null $workspace_id Workspace ID this sync belongs to
 * @property int|null $user_id User who created or owns this sync
 * @property string|null $src_path Absolute source path for the sync
 * @property string|null $dest_path Absolute destination path for the sync
 * @property int|null $src_remote_server_id Remote server ID for the source (if remote)
 * @property int|null $dest_remote_server_id Remote server ID for the destination (if remote)
 * @property int|null $src_site_id Source site ID if syncing from a child or partner site
 * @property int|null $dest_site_id Destination site ID if syncing to a child or partner site
 * @property bool|null $two_way Is this a two-way sync?
 * @property bool|null $keep_after_copy Keep files after copying?
 * @property bool|null $delete_empty_folders Delete empty folders after sync?
 * @property bool|null $disabled Is this sync disabled?
 * @property string|null $trigger Trigger type: daily, custom_schedule, or manual
 * @property string|null $trigger_file Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
 * @property bool|null $always_write_trigger_file If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
 * @property string[]|null $include_patterns Array of glob patterns to include
 * @property string[]|null $exclude_patterns Array of glob patterns to exclude
 * @property string|null $created_at When this sync was created
 * @property string|null $updated_at When this sync was last updated
 * @property int|null $sync_interval_minutes Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
 * @property string|null $interval If trigger is `daily`, this specifies how often to run this sync. One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
 * @property int|null $recurring_day If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
 * @property int[]|null $recurring_days If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
 * @property int|null $schedule_id If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
 * @property int[]|null $schedule_days_of_week If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
 * @property string[]|null $schedule_times_of_day Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
 * @property string|null $schedule_time_zone Time zone for the schedule. If not set, times are interpreted as UTC.
 * @property string|null $holiday_region Skip the sync if there is a formal, observed holiday for this region.
 * @property \stdClass|null $latest_sync_run The latest run of this sync
 * @package Files
 */
class Sync
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
     * Sync ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Sync ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Name for this sync job
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this sync job
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Description for this sync job
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Description for this sync job
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Site ID this sync belongs to
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }

    /**
     * Site ID this sync belongs to
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    /**
     * Workspace ID this sync belongs to
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID this sync belongs to
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * User who created or owns this sync
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User who created or owns this sync
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Absolute source path for the sync
     *
     * @return string|null
     */
    public function getSrcPath()
    {
        return isset($this->attributes['src_path']) ? $this->attributes['src_path'] : null;
    }

    /**
     * Absolute source path for the sync
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSrcPath($value)
    {
        return $this->attributes['src_path'] = $value;
    }
    /**
     * Absolute destination path for the sync
     *
     * @return string|null
     */
    public function getDestPath()
    {
        return isset($this->attributes['dest_path']) ? $this->attributes['dest_path'] : null;
    }

    /**
     * Absolute destination path for the sync
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDestPath($value)
    {
        return $this->attributes['dest_path'] = $value;
    }
    /**
     * Remote server ID for the source (if remote)
     *
     * @return int|null
     */
    public function getSrcRemoteServerId()
    {
        return isset($this->attributes['src_remote_server_id']) ? $this->attributes['src_remote_server_id'] : null;
    }

    /**
     * Remote server ID for the source (if remote)
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSrcRemoteServerId($value)
    {
        return $this->attributes['src_remote_server_id'] = $value;
    }
    /**
     * Remote server ID for the destination (if remote)
     *
     * @return int|null
     */
    public function getDestRemoteServerId()
    {
        return isset($this->attributes['dest_remote_server_id']) ? $this->attributes['dest_remote_server_id'] : null;
    }

    /**
     * Remote server ID for the destination (if remote)
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDestRemoteServerId($value)
    {
        return $this->attributes['dest_remote_server_id'] = $value;
    }
    /**
     * Source site ID if syncing from a child or partner site
     *
     * @return int|null
     */
    public function getSrcSiteId()
    {
        return isset($this->attributes['src_site_id']) ? $this->attributes['src_site_id'] : null;
    }

    /**
     * Source site ID if syncing from a child or partner site
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSrcSiteId($value)
    {
        return $this->attributes['src_site_id'] = $value;
    }
    /**
     * Destination site ID if syncing to a child or partner site
     *
     * @return int|null
     */
    public function getDestSiteId()
    {
        return isset($this->attributes['dest_site_id']) ? $this->attributes['dest_site_id'] : null;
    }

    /**
     * Destination site ID if syncing to a child or partner site
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDestSiteId($value)
    {
        return $this->attributes['dest_site_id'] = $value;
    }
    /**
     * Is this a two-way sync?
     *
     * @return bool|null
     */
    public function getTwoWay()
    {
        return isset($this->attributes['two_way']) ? $this->attributes['two_way'] : null;
    }

    /**
     * Is this a two-way sync?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setTwoWay($value)
    {
        return $this->attributes['two_way'] = $value;
    }
    /**
     * Keep files after copying?
     *
     * @return bool|null
     */
    public function getKeepAfterCopy()
    {
        return isset($this->attributes['keep_after_copy']) ? $this->attributes['keep_after_copy'] : null;
    }

    /**
     * Keep files after copying?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setKeepAfterCopy($value)
    {
        return $this->attributes['keep_after_copy'] = $value;
    }
    /**
     * Delete empty folders after sync?
     *
     * @return bool|null
     */
    public function getDeleteEmptyFolders()
    {
        return isset($this->attributes['delete_empty_folders']) ? $this->attributes['delete_empty_folders'] : null;
    }

    /**
     * Delete empty folders after sync?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDeleteEmptyFolders($value)
    {
        return $this->attributes['delete_empty_folders'] = $value;
    }
    /**
     * Is this sync disabled?
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * Is this sync disabled?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * Trigger type: daily, custom_schedule, or manual
     *
     * @return string|null
     */
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    /**
     * Trigger type: daily, custom_schedule, or manual
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    /**
     * Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
     *
     * @return string|null
     */
    public function getTriggerFile()
    {
        return isset($this->attributes['trigger_file']) ? $this->attributes['trigger_file'] : null;
    }

    /**
     * Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTriggerFile($value)
    {
        return $this->attributes['trigger_file'] = $value;
    }
    /**
     * If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
     *
     * @return bool|null
     */
    public function getAlwaysWriteTriggerFile()
    {
        return isset($this->attributes['always_write_trigger_file']) ? $this->attributes['always_write_trigger_file'] : null;
    }

    /**
     * If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAlwaysWriteTriggerFile($value)
    {
        return $this->attributes['always_write_trigger_file'] = $value;
    }
    /**
     * Array of glob patterns to include
     *
     * @return string[]|null
     */
    public function getIncludePatterns()
    {
        return isset($this->attributes['include_patterns']) ? $this->attributes['include_patterns'] : null;
    }

    /**
     * Array of glob patterns to include
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setIncludePatterns($value)
    {
        return $this->attributes['include_patterns'] = $value;
    }
    /**
     * Array of glob patterns to exclude
     *
     * @return string[]|null
     */
    public function getExcludePatterns()
    {
        return isset($this->attributes['exclude_patterns']) ? $this->attributes['exclude_patterns'] : null;
    }

    /**
     * Array of glob patterns to exclude
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setExcludePatterns($value)
    {
        return $this->attributes['exclude_patterns'] = $value;
    }
    /**
     * When this sync was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * When this sync was last updated
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }
    /**
     * Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
     *
     * @return int|null
     */
    public function getSyncIntervalMinutes()
    {
        return isset($this->attributes['sync_interval_minutes']) ? $this->attributes['sync_interval_minutes'] : null;
    }

    /**
     * Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSyncIntervalMinutes($value)
    {
        return $this->attributes['sync_interval_minutes'] = $value;
    }
    /**
     * If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *
     * @return string|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
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
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
     *
     * @return int|null
     */
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setScheduleId($value)
    {
        return $this->attributes['schedule_id'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
     *
     * @return int[]|null
     */
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    /**
     * If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    /**
     * Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
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
     * Skip the sync if there is a formal, observed holiday for this region.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Skip the sync if there is a formal, observed holiday for this region.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    /**
     * The latest run of this sync
     *
     * @return \stdClass|null
     */
    public function getLatestSyncRun()
    {
        return isset($this->attributes['latest_sync_run']) ? $this->attributes['latest_sync_run'] : null;
    }

    /**
     * The latest run of this sync
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setLatestSyncRun($value)
    {
        return $this->attributes['latest_sync_run'] = $value;
    }

    /**
     * Manually Run Sync
     *
     * POST /syncs/{id}/manual_run
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sync ID.
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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    /**
     * Dry Run Sync
     *
     * POST /syncs/{id}/dry_run
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sync ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '/dry_run', 'POST', $params, $this->options);
        return;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Sync with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Sync
     *
     * PATCH /syncs/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sync ID.
     *     - `delete_empty_folders` (bool, optional): Delete empty folders after sync?
     *     - `description` (string, optional): Description for this sync job
     *     - `dest_path` (string, optional): Absolute destination path for the sync
     *     - `dest_remote_server_id` (int, optional): Remote server ID for the destination (if remote)
     *     - `disabled` (bool, optional): Is this sync disabled?
     *     - `exclude_patterns` (string[], optional): Array of glob patterns to exclude
     *     - `holiday_region` (string, optional): Skip the sync if there is a formal, observed holiday for this region.
     *     - `include_patterns` (string[], optional): Array of glob patterns to include
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *     - `keep_after_copy` (bool, optional): Keep files after copying?
     *     - `name` (string, optional): Name for this sync job
     *     - `recurring_day` (int, optional): If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `recurring_days` (int[], optional): If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
     *     - `schedule_time_zone` (string, optional): Time zone for the schedule. If not set, times are interpreted as UTC.
     *     - `schedule_times_of_day` (string[], optional): Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
     *     - `src_path` (string, optional): Absolute source path for the sync
     *     - `src_remote_server_id` (int, optional): Remote server ID for the source (if remote)
     *     - `sync_interval_minutes` (int, optional): Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
     *     - `trigger` (string, optional): Trigger type: daily, custom_schedule, or manual Allowed values: daily, custom_schedule, manual.
     *     - `trigger_file` (string, optional): Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
     *     - `always_write_trigger_file` (bool, optional): If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
     * @return \Files\Model\Sync
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

        if (isset($params['delete_empty_folders']) && !is_scalar($params['delete_empty_folders'])) {
            throw new \Files\Exception\InvalidParameterException('$delete_empty_folders must be of type boolean; received ' . gettype(@$params['delete_empty_folders']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['dest_path']) && !is_string($params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (isset($params['dest_remote_server_id']) && !is_int($params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (isset($params['disabled']) && !is_scalar($params['disabled'])) {
            throw new \Files\Exception\InvalidParameterException('$disabled must be of type boolean; received ' . gettype(@$params['disabled']));
        }

        if (isset($params['exclude_patterns']) && !is_array($params['exclude_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_patterns must be of type array; received ' . gettype(@$params['exclude_patterns']));
        }

        if (isset($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (isset($params['include_patterns']) && !is_array($params['include_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$include_patterns must be of type array; received ' . gettype(@$params['include_patterns']));
        }

        if (isset($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (isset($params['keep_after_copy']) && !is_scalar($params['keep_after_copy'])) {
            throw new \Files\Exception\InvalidParameterException('$keep_after_copy must be of type boolean; received ' . gettype(@$params['keep_after_copy']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (isset($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (isset($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (isset($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (isset($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (isset($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (isset($params['src_path']) && !is_string($params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (isset($params['src_remote_server_id']) && !is_int($params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (isset($params['sync_interval_minutes']) && !is_int($params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (isset($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (isset($params['trigger_file']) && !is_string($params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        if (isset($params['always_write_trigger_file']) && !is_scalar($params['always_write_trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$always_write_trigger_file must be of type boolean; received ' . gettype(@$params['always_write_trigger_file']));
        }

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Sync(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Sync
     *
     * DELETE /syncs/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sync ID.
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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Sync
     *
     * DELETE /syncs/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Sync ID.
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
     * List Syncs
     *
     * GET /syncs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id` or `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`, `disabled`, `src_remote_server_id` or `dest_remote_server_id`. Valid field combinations are `[ workspace_id, disabled ]`, `[ workspace_id, src_remote_server_id ]`, `[ workspace_id, dest_remote_server_id ]`, `[ disabled, src_remote_server_id ]`, `[ disabled, dest_remote_server_id ]`, `[ workspace_id, disabled, src_remote_server_id ]` or `[ workspace_id, disabled, dest_remote_server_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Sync[]
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

        $response = Api::sendRequest('/syncs', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Sync(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/syncs', $params, $options, Sync::class);
    }

    /**
     * Show Sync
     *
     * GET /syncs/{id}
     *
     * @param int $id Sync ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Sync
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

        $response = Api::sendRequest('/syncs/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Sync(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Sync
     *
     * GET /syncs/{id}
     *
     * Alias of find().
     *
     * @param int $id Sync ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Sync
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Sync
     *
     * POST /syncs
     *
     * @param array $params Request parameters.
     *     - `delete_empty_folders` (bool, optional): Delete empty folders after sync?
     *     - `description` (string, optional): Description for this sync job
     *     - `dest_path` (string, optional): Absolute destination path for the sync
     *     - `dest_remote_server_id` (int, optional): Remote server ID for the destination (if remote)
     *     - `disabled` (bool, optional): Is this sync disabled?
     *     - `exclude_patterns` (string[], optional): Array of glob patterns to exclude
     *     - `holiday_region` (string, optional): Skip the sync if there is a formal, observed holiday for this region.
     *     - `include_patterns` (string[], optional): Array of glob patterns to include
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run this sync.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
     *     - `keep_after_copy` (bool, optional): Keep files after copying?
     *     - `name` (string, optional): Name for this sync job
     *     - `recurring_day` (int, optional): If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `recurring_days` (int[], optional): If trigger type is `daily`, this specifies one or more day numbers to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the sync's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, Custom schedule description for when the sync should be run. 0-based days of the week. 0 is Sunday, 1 is Monday, etc.
     *     - `schedule_time_zone` (string, optional): Time zone for the schedule. If not set, times are interpreted as UTC.
     *     - `schedule_times_of_day` (string[], optional): Times of day to run in HH:MM format. For `custom_schedule`, run at these times on specified days of week. For `daily`, run at these times on the scheduled interval date.
     *     - `src_path` (string, optional): Absolute source path for the sync
     *     - `src_remote_server_id` (int, optional): Remote server ID for the source (if remote)
     *     - `sync_interval_minutes` (int, optional): Frequency in minutes between syncs. If set, this value must be greater than or equal to the `remote_sync_interval` value for the site's plan. If left blank, the plan's `remote_sync_interval` will be used. This setting is only used if `trigger` is empty.
     *     - `trigger` (string, optional): Trigger type: daily, custom_schedule, or manual Allowed values: daily, custom_schedule, manual.
     *     - `trigger_file` (string, optional): Some MFT services request an empty file (known as a trigger file) to signal the sync is complete and they can begin further processing. If trigger_file is set, a zero-byte file will be sent at the end of the sync.
     *     - `always_write_trigger_file` (bool, optional): If true, the trigger file will be sent at the end of a successful sync even when no files were transferred.
     *     - `workspace_id` (int, optional): Workspace ID this sync belongs to
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Sync
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['delete_empty_folders']) && !is_scalar($params['delete_empty_folders'])) {
            throw new \Files\Exception\InvalidParameterException('$delete_empty_folders must be of type boolean; received ' . gettype(@$params['delete_empty_folders']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['dest_path']) && !is_string($params['dest_path'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_path must be of type string; received ' . gettype(@$params['dest_path']));
        }

        if (isset($params['dest_remote_server_id']) && !is_int($params['dest_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$dest_remote_server_id must be of type int; received ' . gettype(@$params['dest_remote_server_id']));
        }

        if (isset($params['disabled']) && !is_scalar($params['disabled'])) {
            throw new \Files\Exception\InvalidParameterException('$disabled must be of type boolean; received ' . gettype(@$params['disabled']));
        }

        if (isset($params['exclude_patterns']) && !is_array($params['exclude_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_patterns must be of type array; received ' . gettype(@$params['exclude_patterns']));
        }

        if (isset($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (isset($params['include_patterns']) && !is_array($params['include_patterns'])) {
            throw new \Files\Exception\InvalidParameterException('$include_patterns must be of type array; received ' . gettype(@$params['include_patterns']));
        }

        if (isset($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (isset($params['keep_after_copy']) && !is_scalar($params['keep_after_copy'])) {
            throw new \Files\Exception\InvalidParameterException('$keep_after_copy must be of type boolean; received ' . gettype(@$params['keep_after_copy']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['recurring_day']) && !is_int($params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (isset($params['recurring_days']) && !is_array($params['recurring_days'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_days must be of type array; received ' . gettype(@$params['recurring_days']));
        }

        if (isset($params['schedule_id']) && !is_int($params['schedule_id'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_id must be of type int; received ' . gettype(@$params['schedule_id']));
        }

        if (isset($params['schedule_days_of_week']) && !is_array($params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (isset($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (isset($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (isset($params['src_path']) && !is_string($params['src_path'])) {
            throw new \Files\Exception\InvalidParameterException('$src_path must be of type string; received ' . gettype(@$params['src_path']));
        }

        if (isset($params['src_remote_server_id']) && !is_int($params['src_remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$src_remote_server_id must be of type int; received ' . gettype(@$params['src_remote_server_id']));
        }

        if (isset($params['sync_interval_minutes']) && !is_int($params['sync_interval_minutes'])) {
            throw new \Files\Exception\InvalidParameterException('$sync_interval_minutes must be of type int; received ' . gettype(@$params['sync_interval_minutes']));
        }

        if (isset($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (isset($params['trigger_file']) && !is_string($params['trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_file must be of type string; received ' . gettype(@$params['trigger_file']));
        }

        if (isset($params['always_write_trigger_file']) && !is_scalar($params['always_write_trigger_file'])) {
            throw new \Files\Exception\InvalidParameterException('$always_write_trigger_file must be of type boolean; received ' . gettype(@$params['always_write_trigger_file']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/syncs', 'POST', $params, $options);

        return new Sync(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
