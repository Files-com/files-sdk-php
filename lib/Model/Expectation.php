<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Expectations let your Files.com site define what “correct” file delivery looks like, continuously evaluate whether it happened, and keep history when it did not.
 *
 * @property int|null $id Expectation ID
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $name Expectation name.
 * @property string|null $description Expectation description.
 * @property string|null $path Path scope for the expectation. Supports workspace-relative presentation. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $source Source glob used to select candidate files.
 * @property string|null $exclude_pattern Optional source exclusion glob.
 * @property bool|null $disabled If true, the expectation is disabled.
 * @property int|null $expectations_version Criteria schema version for this expectation.
 * @property string|null $trigger How this expectation opens windows.
 * @property string|null $interval If trigger is `daily`, this specifies how often to run the expectation.
 * @property int|null $recurring_day If trigger is `daily`, this selects the day number inside the chosen interval.
 * @property int[]|null $recurring_days If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
 * @property int|null $schedule_id If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
 * @property int[]|null $schedule_days_of_week If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
 * @property string[]|null $schedule_times_of_day Times of day in HH:MM format for the Expectation schedule.
 * @property string|null $schedule_time_zone Time zone used by the Expectation schedule.
 * @property string|null $holiday_region Optional holiday region used by the Expectation schedule.
 * @property int|null $lookback_interval How many seconds before the due boundary the window starts.
 * @property int|null $late_acceptance_interval How many seconds a schedule-driven window may remain eligible to close as late.
 * @property int|null $inactivity_interval How many quiet seconds are required before final closure.
 * @property int|null $max_open_interval Hard-stop duration in seconds for unscheduled expectations.
 * @property object|null $criteria Versioned success criteria definition for the expectation. Criteria v2 supports optional FTS content validation.
 * @property string|null $last_evaluated_at Last time this expectation was evaluated.
 * @property string|null $last_success_at Last time this expectation closed successfully.
 * @property string|null $last_failure_at Last time this expectation closed with a failure result.
 * @property string|null $last_result Most recent terminal result for this expectation.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class Expectation
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
     * Expectation ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Expectation ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Expectation name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Expectation name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Expectation description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Expectation description.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Path scope for the expectation. Supports workspace-relative presentation. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Path scope for the expectation. Supports workspace-relative presentation. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Source glob used to select candidate files.
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }

    /**
     * Source glob used to select candidate files.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    /**
     * Optional source exclusion glob.
     *
     * @return string|null
     */
    public function getExcludePattern()
    {
        return isset($this->attributes['exclude_pattern']) ? $this->attributes['exclude_pattern'] : null;
    }

    /**
     * Optional source exclusion glob.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExcludePattern($value)
    {
        return $this->attributes['exclude_pattern'] = $value;
    }
    /**
     * If true, the expectation is disabled.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * If true, the expectation is disabled.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * Criteria schema version for this expectation.
     *
     * @return int|null
     */
    public function getExpectationsVersion()
    {
        return isset($this->attributes['expectations_version']) ? $this->attributes['expectations_version'] : null;
    }

    /**
     * Criteria schema version for this expectation.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setExpectationsVersion($value)
    {
        return $this->attributes['expectations_version'] = $value;
    }
    /**
     * How this expectation opens windows.
     *
     * @return string|null
     */
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    /**
     * How this expectation opens windows.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    /**
     * If trigger is `daily`, this specifies how often to run the expectation.
     *
     * @return string|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * If trigger is `daily`, this specifies how often to run the expectation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    /**
     * If trigger is `daily`, this selects the day number inside the chosen interval.
     *
     * @return int|null
     */
    public function getRecurringDay()
    {
        return isset($this->attributes['recurring_day']) ? $this->attributes['recurring_day'] : null;
    }

    /**
     * If trigger is `daily`, this selects the day number inside the chosen interval.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    /**
     * If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *
     * @return int[]|null
     */
    public function getRecurringDays()
    {
        return isset($this->attributes['recurring_days']) ? $this->attributes['recurring_days'] : null;
    }

    /**
     * If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setRecurringDays($value)
    {
        return $this->attributes['recurring_days'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
     *
     * @return int|null
     */
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setScheduleId($value)
    {
        return $this->attributes['schedule_id'] = $value;
    }
    /**
     * If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *
     * @return int[]|null
     */
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    /**
     * Times of day in HH:MM format for the Expectation schedule.
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day in HH:MM format for the Expectation schedule.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    /**
     * Time zone used by the Expectation schedule.
     *
     * @return string|null
     */
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    /**
     * Time zone used by the Expectation schedule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    /**
     * Optional holiday region used by the Expectation schedule.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Optional holiday region used by the Expectation schedule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    /**
     * How many seconds before the due boundary the window starts.
     *
     * @return int|null
     */
    public function getLookbackInterval()
    {
        return isset($this->attributes['lookback_interval']) ? $this->attributes['lookback_interval'] : null;
    }

    /**
     * How many seconds before the due boundary the window starts.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLookbackInterval($value)
    {
        return $this->attributes['lookback_interval'] = $value;
    }
    /**
     * How many seconds a schedule-driven window may remain eligible to close as late.
     *
     * @return int|null
     */
    public function getLateAcceptanceInterval()
    {
        return isset($this->attributes['late_acceptance_interval']) ? $this->attributes['late_acceptance_interval'] : null;
    }

    /**
     * How many seconds a schedule-driven window may remain eligible to close as late.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLateAcceptanceInterval($value)
    {
        return $this->attributes['late_acceptance_interval'] = $value;
    }
    /**
     * How many quiet seconds are required before final closure.
     *
     * @return int|null
     */
    public function getInactivityInterval()
    {
        return isset($this->attributes['inactivity_interval']) ? $this->attributes['inactivity_interval'] : null;
    }

    /**
     * How many quiet seconds are required before final closure.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInactivityInterval($value)
    {
        return $this->attributes['inactivity_interval'] = $value;
    }
    /**
     * Hard-stop duration in seconds for unscheduled expectations.
     *
     * @return int|null
     */
    public function getMaxOpenInterval()
    {
        return isset($this->attributes['max_open_interval']) ? $this->attributes['max_open_interval'] : null;
    }

    /**
     * Hard-stop duration in seconds for unscheduled expectations.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setMaxOpenInterval($value)
    {
        return $this->attributes['max_open_interval'] = $value;
    }
    /**
     * Versioned success criteria definition for the expectation. Criteria v2 supports optional FTS content validation.
     *
     * @return object|null
     */
    public function getCriteria()
    {
        return isset($this->attributes['criteria']) ? $this->attributes['criteria'] : null;
    }

    /**
     * Versioned success criteria definition for the expectation. Criteria v2 supports optional FTS content validation.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setCriteria($value)
    {
        return $this->attributes['criteria'] = $value;
    }
    /**
     * Last time this expectation was evaluated.
     *
     * @return string|null
     */
    public function getLastEvaluatedAt()
    {
        return isset($this->attributes['last_evaluated_at']) ? $this->attributes['last_evaluated_at'] : null;
    }

    /**
     * Last time this expectation was evaluated.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastEvaluatedAt($value)
    {
        return $this->attributes['last_evaluated_at'] = $value;
    }
    /**
     * Last time this expectation closed successfully.
     *
     * @return string|null
     */
    public function getLastSuccessAt()
    {
        return isset($this->attributes['last_success_at']) ? $this->attributes['last_success_at'] : null;
    }

    /**
     * Last time this expectation closed successfully.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastSuccessAt($value)
    {
        return $this->attributes['last_success_at'] = $value;
    }
    /**
     * Last time this expectation closed with a failure result.
     *
     * @return string|null
     */
    public function getLastFailureAt()
    {
        return isset($this->attributes['last_failure_at']) ? $this->attributes['last_failure_at'] : null;
    }

    /**
     * Last time this expectation closed with a failure result.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastFailureAt($value)
    {
        return $this->attributes['last_failure_at'] = $value;
    }
    /**
     * Most recent terminal result for this expectation.
     *
     * @return string|null
     */
    public function getLastResult()
    {
        return isset($this->attributes['last_result']) ? $this->attributes['last_result'] : null;
    }

    /**
     * Most recent terminal result for this expectation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastResult($value)
    {
        return $this->attributes['last_result'] = $value;
    }
    /**
     * Creation time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Last update time.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Manually open an Expectation window
     *
     * POST /expectations/{id}/trigger_evaluation
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation ID.
     * @return \Files\Model\ExpectationEvaluation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function triggerEvaluation($params = [])
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

        $response = Api::sendRequest('/expectations/' . rawurlencode(strval(@$params['id'])) . '/trigger_evaluation', 'POST', $params, $this->options);
        return new ExpectationEvaluation((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Update Expectation
     *
     * PATCH /expectations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation ID.
     *     - `name` (string, optional): Expectation name.
     *     - `description` (string, optional): Expectation description.
     *     - `path` (string, optional): Path scope for the expectation. Supports workspace-relative presentation.
     *     - `source` (string, optional): Source glob used to select candidate files.
     *     - `exclude_pattern` (string, optional): Optional source exclusion glob.
     *     - `disabled` (bool, optional): If true, the expectation is disabled.
     *     - `trigger` (string, optional): How this expectation opens windows. Allowed values: manual, upload, daily, custom_schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the expectation.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the Expectation schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the Expectation schedule.
     *     - `holiday_region` (string, optional): Optional holiday region used by the Expectation schedule.
     *     - `lookback_interval` (int, optional): How many seconds before the due boundary the window starts.
     *     - `late_acceptance_interval` (int, optional): How many seconds a schedule-driven window may remain eligible to close as late.
     *     - `inactivity_interval` (int, optional): How many quiet seconds are required before final closure.
     *     - `max_open_interval` (int, optional): Hard-stop duration in seconds for unscheduled expectations.
     *     - `criteria` (array|object, optional): Versioned success criteria definition for the expectation, including optional Files Transform Script content validation in criteria v2.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\Expectation
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (!empty($params['exclude_pattern']) && !is_string($params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
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

        if (!empty($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (!empty($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['lookback_interval']) && !is_int($params['lookback_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$lookback_interval must be of type int; received ' . gettype(@$params['lookback_interval']));
        }

        if (!empty($params['late_acceptance_interval']) && !is_int($params['late_acceptance_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$late_acceptance_interval must be of type int; received ' . gettype(@$params['late_acceptance_interval']));
        }

        if (!empty($params['inactivity_interval']) && !is_int($params['inactivity_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_interval must be of type int; received ' . gettype(@$params['inactivity_interval']));
        }

        if (!empty($params['max_open_interval']) && !is_int($params['max_open_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$max_open_interval must be of type int; received ' . gettype(@$params['max_open_interval']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/expectations/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Expectation((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Expectation
     *
     * DELETE /expectations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation ID.
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/expectations/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Expectation
     *
     * DELETE /expectations/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation ID.
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
     * List Expectations
     *
     * GET /expectations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `disabled`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled` and `workspace_id`. Valid field combinations are `[ workspace_id, disabled ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Expectation[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/expectations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Expectation((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Expectation
     *
     * GET /expectations/{id}
     *
     * @param int $id Expectation ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Expectation
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/expectations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Expectation((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Expectation
     *
     * GET /expectations/{id}
     *
     * Alias of find().
     *
     * @param int $id Expectation ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Expectation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Expectation
     *
     * POST /expectations
     *
     * @param array $params Request parameters.
     *     - `name` (string, optional): Expectation name.
     *     - `description` (string, optional): Expectation description.
     *     - `path` (string, optional): Path scope for the expectation. Supports workspace-relative presentation.
     *     - `source` (string, optional): Source glob used to select candidate files.
     *     - `exclude_pattern` (string, optional): Optional source exclusion glob.
     *     - `disabled` (bool, optional): If true, the expectation is disabled.
     *     - `trigger` (string, optional): How this expectation opens windows. Allowed values: manual, upload, daily, custom_schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the expectation.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the Expectation schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the Expectation schedule.
     *     - `holiday_region` (string, optional): Optional holiday region used by the Expectation schedule.
     *     - `lookback_interval` (int, optional): How many seconds before the due boundary the window starts.
     *     - `late_acceptance_interval` (int, optional): How many seconds a schedule-driven window may remain eligible to close as late.
     *     - `inactivity_interval` (int, optional): How many quiet seconds are required before final closure.
     *     - `max_open_interval` (int, optional): Hard-stop duration in seconds for unscheduled expectations.
     *     - `criteria` (array|object, optional): Versioned success criteria definition for the expectation, including optional Files Transform Script content validation in criteria v2.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Expectation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (!empty($params['exclude_pattern']) && !is_string($params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
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

        if (!empty($params['schedule_times_of_day']) && !is_array($params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (!empty($params['schedule_time_zone']) && !is_string($params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['lookback_interval']) && !is_int($params['lookback_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$lookback_interval must be of type int; received ' . gettype(@$params['lookback_interval']));
        }

        if (!empty($params['late_acceptance_interval']) && !is_int($params['late_acceptance_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$late_acceptance_interval must be of type int; received ' . gettype(@$params['late_acceptance_interval']));
        }

        if (!empty($params['inactivity_interval']) && !is_int($params['inactivity_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_interval must be of type int; received ' . gettype(@$params['inactivity_interval']));
        }

        if (!empty($params['max_open_interval']) && !is_int($params['max_open_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$max_open_interval must be of type int; received ' . gettype(@$params['max_open_interval']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/expectations', 'POST', $params, $options);

        return new Expectation((array) (@$response->data ?: []), $options);
    }
}
