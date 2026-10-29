<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Expectation
 *
 * @package Files
 */
class Expectation
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
    // int64 # Expectation ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # Expectation name.
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Expectation description.
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // string # Path scope for the expectation. Supports workspace-relative presentation. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Source glob used to select candidate files.
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }

    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    // string # Optional source exclusion glob.
    public function getExcludePattern()
    {
        return isset($this->attributes['exclude_pattern']) ? $this->attributes['exclude_pattern'] : null;
    }

    public function setExcludePattern($value)
    {
        return $this->attributes['exclude_pattern'] = $value;
    }
    // boolean # If true, the expectation is disabled.
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // int64 # Criteria schema version for this expectation.
    public function getExpectationsVersion()
    {
        return isset($this->attributes['expectations_version']) ? $this->attributes['expectations_version'] : null;
    }

    public function setExpectationsVersion($value)
    {
        return $this->attributes['expectations_version'] = $value;
    }
    // string # How this expectation opens windows.
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run the expectation.
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // int64 # If trigger is `daily`, this selects the day number inside the chosen interval.
    public function getRecurringDay()
    {
        return isset($this->attributes['recurring_day']) ? $this->attributes['recurring_day'] : null;
    }

    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    // array(int64) # If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
    public function getRecurringDays()
    {
        return isset($this->attributes['recurring_days']) ? $this->attributes['recurring_days'] : null;
    }

    public function setRecurringDays($value)
    {
        return $this->attributes['recurring_days'] = $value;
    }
    // int64 # If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    public function setScheduleId($value)
    {
        return $this->attributes['schedule_id'] = $value;
    }
    // array(int64) # If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    // array(string) # Times of day in HH:MM format for the Expectation schedule.
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # Time zone used by the Expectation schedule.
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # Optional holiday region used by the Expectation schedule.
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    // int64 # How many seconds before the due boundary the window starts.
    public function getLookbackInterval()
    {
        return isset($this->attributes['lookback_interval']) ? $this->attributes['lookback_interval'] : null;
    }

    public function setLookbackInterval($value)
    {
        return $this->attributes['lookback_interval'] = $value;
    }
    // int64 # How many seconds a schedule-driven window may remain eligible to close as late.
    public function getLateAcceptanceInterval()
    {
        return isset($this->attributes['late_acceptance_interval']) ? $this->attributes['late_acceptance_interval'] : null;
    }

    public function setLateAcceptanceInterval($value)
    {
        return $this->attributes['late_acceptance_interval'] = $value;
    }
    // int64 # How many quiet seconds are required before final closure.
    public function getInactivityInterval()
    {
        return isset($this->attributes['inactivity_interval']) ? $this->attributes['inactivity_interval'] : null;
    }

    public function setInactivityInterval($value)
    {
        return $this->attributes['inactivity_interval'] = $value;
    }
    // int64 # Hard-stop duration in seconds for unscheduled expectations.
    public function getMaxOpenInterval()
    {
        return isset($this->attributes['max_open_interval']) ? $this->attributes['max_open_interval'] : null;
    }

    public function setMaxOpenInterval($value)
    {
        return $this->attributes['max_open_interval'] = $value;
    }
    // object # Versioned success criteria definition for the expectation. Criteria v2 supports optional FTS content validation.
    public function getCriteria()
    {
        return isset($this->attributes['criteria']) ? $this->attributes['criteria'] : null;
    }

    public function setCriteria($value)
    {
        return $this->attributes['criteria'] = $value;
    }
    // date-time # Last time this expectation was evaluated.
    public function getLastEvaluatedAt()
    {
        return isset($this->attributes['last_evaluated_at']) ? $this->attributes['last_evaluated_at'] : null;
    }

    public function setLastEvaluatedAt($value)
    {
        return $this->attributes['last_evaluated_at'] = $value;
    }
    // date-time # Last time this expectation closed successfully.
    public function getLastSuccessAt()
    {
        return isset($this->attributes['last_success_at']) ? $this->attributes['last_success_at'] : null;
    }

    public function setLastSuccessAt($value)
    {
        return $this->attributes['last_success_at'] = $value;
    }
    // date-time # Last time this expectation closed with a failure result.
    public function getLastFailureAt()
    {
        return isset($this->attributes['last_failure_at']) ? $this->attributes['last_failure_at'] : null;
    }

    public function setLastFailureAt($value)
    {
        return $this->attributes['last_failure_at'] = $value;
    }
    // string # Most recent terminal result for this expectation.
    public function getLastResult()
    {
        return isset($this->attributes['last_result']) ? $this->attributes['last_result'] : null;
    }

    public function setLastResult($value)
    {
        return $this->attributes['last_result'] = $value;
    }
    // date-time # Creation time.
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // date-time # Last update time.
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    // Manually open an Expectation window
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

    // Parameters:
    //   name - string - Expectation name.
    //   description - string - Expectation description.
    //   path - string - Path scope for the expectation. Supports workspace-relative presentation.
    //   source - string - Source glob used to select candidate files.
    //   exclude_pattern - string - Optional source exclusion glob.
    //   disabled - boolean - If true, the expectation is disabled.
    //   trigger - string - How this expectation opens windows.
    //   interval - string - If trigger is `daily`, this specifies how often to run the expectation.
    //   recurring_day - int64 - If trigger is `daily`, this selects the day number inside the chosen interval.
    //   recurring_days - array(int64) - If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
    //   schedule_id - int64 - If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for the Expectation schedule.
    //   schedule_time_zone - string - Time zone used by the Expectation schedule.
    //   holiday_region - string - Optional holiday region used by the Expectation schedule.
    //   lookback_interval - int64 - How many seconds before the due boundary the window starts.
    //   late_acceptance_interval - int64 - How many seconds a schedule-driven window may remain eligible to close as late.
    //   inactivity_interval - int64 - How many quiet seconds are required before final closure.
    //   max_open_interval - int64 - Hard-stop duration in seconds for unscheduled expectations.
    //   criteria - object - Versioned success criteria definition for the expectation, including optional Files Transform Script content validation in criteria v2.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `disabled`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled` and `workspace_id`. Valid field combinations are `[ workspace_id, disabled ]`.
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

    // Parameters:
    //   id (required) - int64 - Expectation ID.
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
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name - string - Expectation name.
    //   description - string - Expectation description.
    //   path - string - Path scope for the expectation. Supports workspace-relative presentation.
    //   source - string - Source glob used to select candidate files.
    //   exclude_pattern - string - Optional source exclusion glob.
    //   disabled - boolean - If true, the expectation is disabled.
    //   trigger - string - How this expectation opens windows.
    //   interval - string - If trigger is `daily`, this specifies how often to run the expectation.
    //   recurring_day - int64 - If trigger is `daily`, this selects the day number inside the chosen interval.
    //   recurring_days - array(int64) - If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
    //   schedule_id - int64 - If trigger is `custom_schedule`, the reusable Schedule used instead of the Expectation's schedule fields.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for the Expectation schedule.
    //   schedule_time_zone - string - Time zone used by the Expectation schedule.
    //   holiday_region - string - Optional holiday region used by the Expectation schedule.
    //   lookback_interval - int64 - How many seconds before the due boundary the window starts.
    //   late_acceptance_interval - int64 - How many seconds a schedule-driven window may remain eligible to close as late.
    //   inactivity_interval - int64 - How many quiet seconds are required before final closure.
    //   max_open_interval - int64 - Hard-stop duration in seconds for unscheduled expectations.
    //   criteria - object - Versioned success criteria definition for the expectation, including optional Files Transform Script content validation in criteria v2.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
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
