<?php

declare(strict_types=1);

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
    // int64 # Expectation ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # Expectation name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Expectation description.
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // string # Path scope for the expectation. Supports workspace-relative presentation. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Source glob used to select candidate files.
    public function getSource()
    {
        return @$this->attributes['source'];
    }

    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    // string # Optional source exclusion glob.
    public function getExcludePattern()
    {
        return @$this->attributes['exclude_pattern'];
    }

    public function setExcludePattern($value)
    {
        return $this->attributes['exclude_pattern'] = $value;
    }
    // boolean # If true, the expectation is disabled.
    public function getDisabled()
    {
        return @$this->attributes['disabled'];
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // int64 # Criteria schema version for this expectation.
    public function getExpectationsVersion()
    {
        return @$this->attributes['expectations_version'];
    }

    public function setExpectationsVersion($value)
    {
        return $this->attributes['expectations_version'] = $value;
    }
    // string # How this expectation opens windows.
    public function getTrigger()
    {
        return @$this->attributes['trigger'];
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run the expectation.
    public function getInterval()
    {
        return @$this->attributes['interval'];
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // int64 # If trigger is `daily`, this selects the day number inside the chosen interval.
    public function getRecurringDay()
    {
        return @$this->attributes['recurring_day'];
    }

    public function setRecurringDay($value)
    {
        return $this->attributes['recurring_day'] = $value;
    }
    // array(int64) # If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    public function getScheduleDaysOfWeek()
    {
        return @$this->attributes['schedule_days_of_week'];
    }

    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    // array(string) # Times of day in HH:MM format for schedule-driven expectations.
    public function getScheduleTimesOfDay()
    {
        return @$this->attributes['schedule_times_of_day'];
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # Time zone used by the expectation schedule.
    public function getScheduleTimeZone()
    {
        return @$this->attributes['schedule_time_zone'];
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # Optional holiday region used by schedule-driven expectations.
    public function getHolidayRegion()
    {
        return @$this->attributes['holiday_region'];
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    // int64 # How many seconds before the due boundary the window starts.
    public function getLookbackInterval()
    {
        return @$this->attributes['lookback_interval'];
    }

    public function setLookbackInterval($value)
    {
        return $this->attributes['lookback_interval'] = $value;
    }
    // int64 # How many seconds a schedule-driven window may remain eligible to close as late.
    public function getLateAcceptanceInterval()
    {
        return @$this->attributes['late_acceptance_interval'];
    }

    public function setLateAcceptanceInterval($value)
    {
        return $this->attributes['late_acceptance_interval'] = $value;
    }
    // int64 # How many quiet seconds are required before final closure.
    public function getInactivityInterval()
    {
        return @$this->attributes['inactivity_interval'];
    }

    public function setInactivityInterval($value)
    {
        return $this->attributes['inactivity_interval'] = $value;
    }
    // int64 # Hard-stop duration in seconds for unscheduled expectations.
    public function getMaxOpenInterval()
    {
        return @$this->attributes['max_open_interval'];
    }

    public function setMaxOpenInterval($value)
    {
        return $this->attributes['max_open_interval'] = $value;
    }
    // object # Structured criteria v1 definition for the expectation.
    public function getCriteria()
    {
        return @$this->attributes['criteria'];
    }

    public function setCriteria($value)
    {
        return $this->attributes['criteria'] = $value;
    }
    // date-time # Last time this expectation was evaluated.
    public function getLastEvaluatedAt()
    {
        return @$this->attributes['last_evaluated_at'];
    }

    public function setLastEvaluatedAt($value)
    {
        return $this->attributes['last_evaluated_at'] = $value;
    }
    // date-time # Last time this expectation closed successfully.
    public function getLastSuccessAt()
    {
        return @$this->attributes['last_success_at'];
    }

    public function setLastSuccessAt($value)
    {
        return $this->attributes['last_success_at'] = $value;
    }
    // date-time # Last time this expectation closed with a failure result.
    public function getLastFailureAt()
    {
        return @$this->attributes['last_failure_at'];
    }

    public function setLastFailureAt($value)
    {
        return $this->attributes['last_failure_at'] = $value;
    }
    // string # Most recent terminal result for this expectation.
    public function getLastResult()
    {
        return @$this->attributes['last_result'];
    }

    public function setLastResult($value)
    {
        return $this->attributes['last_result'] = $value;
    }
    // date-time # Creation time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # Last update time.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Manually open an Expectation window
    public function trigger($params = [])
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

        $response = Api::sendRequest('/expectations/' . @$params['id'] . '/trigger', 'POST', $params, $this->options);
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
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for schedule-driven expectations.
    //   schedule_time_zone - string - Time zone used by the expectation schedule.
    //   holiday_region - string - Optional holiday region used by schedule-driven expectations.
    //   lookback_interval - int64 - How many seconds before the due boundary the window starts.
    //   late_acceptance_interval - int64 - How many seconds a schedule-driven window may remain eligible to close as late.
    //   inactivity_interval - int64 - How many quiet seconds are required before final closure.
    //   max_open_interval - int64 - Hard-stop duration in seconds for unscheduled expectations.
    //   criteria - object - Structured criteria v1 definition for the expectation.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
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

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['exclude_pattern'] && !is_string(@$params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
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

        if (@$params['lookback_interval'] && !is_int(@$params['lookback_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$lookback_interval must be of type int; received ' . gettype(@$params['lookback_interval']));
        }

        if (@$params['late_acceptance_interval'] && !is_int(@$params['late_acceptance_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$late_acceptance_interval must be of type int; received ' . gettype(@$params['late_acceptance_interval']));
        }

        if (@$params['inactivity_interval'] && !is_int(@$params['inactivity_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_interval must be of type int; received ' . gettype(@$params['inactivity_interval']));
        }

        if (@$params['max_open_interval'] && !is_int(@$params['max_open_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$max_open_interval must be of type int; received ' . gettype(@$params['max_open_interval']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/expectations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Expectation((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/expectations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `disabled`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled` and `workspace_id`. Valid field combinations are `[ workspace_id, disabled ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
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

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/expectations/' . @$params['id'] . '', 'GET', $params, $options);

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
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for schedule-driven expectations.
    //   schedule_time_zone - string - Time zone used by the expectation schedule.
    //   holiday_region - string - Optional holiday region used by schedule-driven expectations.
    //   lookback_interval - int64 - How many seconds before the due boundary the window starts.
    //   late_acceptance_interval - int64 - How many seconds a schedule-driven window may remain eligible to close as late.
    //   inactivity_interval - int64 - How many quiet seconds are required before final closure.
    //   max_open_interval - int64 - Hard-stop duration in seconds for unscheduled expectations.
    //   criteria - object - Structured criteria v1 definition for the expectation.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
    public static function create($params = [], $options = [])
    {
        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['exclude_pattern'] && !is_string(@$params['exclude_pattern'])) {
            throw new \Files\Exception\InvalidParameterException('$exclude_pattern must be of type string; received ' . gettype(@$params['exclude_pattern']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
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

        if (@$params['lookback_interval'] && !is_int(@$params['lookback_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$lookback_interval must be of type int; received ' . gettype(@$params['lookback_interval']));
        }

        if (@$params['late_acceptance_interval'] && !is_int(@$params['late_acceptance_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$late_acceptance_interval must be of type int; received ' . gettype(@$params['late_acceptance_interval']));
        }

        if (@$params['inactivity_interval'] && !is_int(@$params['inactivity_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_interval must be of type int; received ' . gettype(@$params['inactivity_interval']));
        }

        if (@$params['max_open_interval'] && !is_int(@$params['max_open_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$max_open_interval must be of type int; received ' . gettype(@$params['max_open_interval']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/expectations', 'POST', $params, $options);

        return new Expectation((array) (@$response->data ?: []), $options);
    }
}
