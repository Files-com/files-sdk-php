<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AiTask
 *
 * @package Files
 */
class AiTask
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
    // int64 # AI Task ID.
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
    // string # AI Task name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # AI Task description.
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // string # Prompt sent when this AI Task is invoked.
    public function getPrompt()
    {
        return @$this->attributes['prompt'];
    }

    public function setPrompt($value)
    {
        return $this->attributes['prompt'] = $value;
    }
    // string # Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
    public function getPermissionSet()
    {
        return @$this->attributes['permission_set'];
    }

    public function setPermissionSet($value)
    {
        return $this->attributes['permission_set'] = $value;
    }
    // string # Path scope used for action-triggered AI Tasks. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Source glob used with `path` for action-triggered AI Tasks.
    public function getSource()
    {
        return @$this->attributes['source'];
    }

    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    // boolean # If true, this AI Task will not run.
    public function getDisabled()
    {
        return @$this->attributes['disabled'];
    }

    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    // string # How this AI Task is triggered.
    public function getTrigger()
    {
        return @$this->attributes['trigger'];
    }

    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    // array(string) # If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
    public function getTriggerActions()
    {
        return @$this->attributes['trigger_actions'];
    }

    public function setTriggerActions($value)
    {
        return $this->attributes['trigger_actions'] = $value;
    }
    // string # If trigger is `daily`, this specifies how often to run the AI Task.
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
    // array(string) # Times of day in HH:MM format for scheduled AI Tasks.
    public function getScheduleTimesOfDay()
    {
        return @$this->attributes['schedule_times_of_day'];
    }

    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    // string # Time zone used by the AI Task schedule.
    public function getScheduleTimeZone()
    {
        return @$this->attributes['schedule_time_zone'];
    }

    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    // string # Optional holiday region used by scheduled AI Tasks.
    public function getHolidayRegion()
    {
        return @$this->attributes['holiday_region'];
    }

    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    // string # Human-readable schedule description.
    public function getHumanReadableSchedule()
    {
        return @$this->attributes['human_readable_schedule'];
    }

    public function setHumanReadableSchedule($value)
    {
        return $this->attributes['human_readable_schedule'] = $value;
    }
    // date-time # Most recent successful invocation time.
    public function getLastRunAt()
    {
        return @$this->attributes['last_run_at'];
    }

    public function setLastRunAt($value)
    {
        return $this->attributes['last_run_at'] = $value;
    }
    // int64 # Master User ID used for AI Task invocations.
    public function getMasterAdminUserId()
    {
        return @$this->attributes['master_admin_user_id'];
    }

    public function setMasterAdminUserId($value)
    {
        return $this->attributes['master_admin_user_id'] = $value;
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

    // Manually Run AI Task
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

        $response = Api::sendRequest('/ai_tasks/' . @$params['id'] . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   description - string - AI Task description.
    //   disabled - boolean - If true, this AI Task will not run.
    //   holiday_region - string - Optional holiday region used by scheduled AI Tasks.
    //   interval - string - If trigger is `daily`, this specifies how often to run the AI Task.
    //   name - string - AI Task name.
    //   path - string - Path scope used for action-triggered AI Tasks.
    //   permission_set - string - Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
    //   prompt - string - Prompt sent when this AI Task is invoked.
    //   recurring_day - int64 - If trigger is `daily`, this selects the day number inside the chosen interval.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_time_zone - string - Time zone used by the AI Task schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for scheduled AI Tasks.
    //   source - string - Source glob used with `path` for action-triggered AI Tasks.
    //   trigger - string - How this AI Task is triggered.
    //   trigger_actions - array(string) - If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
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

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (@$params['prompt'] && !is_string(@$params['prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$prompt must be of type string; received ' . gettype(@$params['prompt']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_tasks/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new AiTask((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/ai_tasks/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `id`, `disabled` or `updated_at`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled`, `trigger` or `workspace_id`. Valid field combinations are `[ workspace_id, disabled ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/ai_tasks', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AiTask((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Ai Task ID.
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

        $response = Api::sendRequest('/ai_tasks/' . @$params['id'] . '', 'GET', $params, $options);

        return new AiTask((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   description - string - AI Task description.
    //   disabled - boolean - If true, this AI Task will not run.
    //   holiday_region - string - Optional holiday region used by scheduled AI Tasks.
    //   interval - string - If trigger is `daily`, this specifies how often to run the AI Task.
    //   name (required) - string - AI Task name.
    //   path - string - Path scope used for action-triggered AI Tasks.
    //   permission_set - string - Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
    //   prompt (required) - string - Prompt sent when this AI Task is invoked.
    //   recurring_day - int64 - If trigger is `daily`, this selects the day number inside the chosen interval.
    //   schedule_days_of_week - array(int64) - If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
    //   schedule_time_zone - string - Time zone used by the AI Task schedule.
    //   schedule_times_of_day - array(string) - Times of day in HH:MM format for scheduled AI Tasks.
    //   source - string - Source glob used with `path` for action-triggered AI Tasks.
    //   trigger - string - How this AI Task is triggered.
    //   trigger_actions - array(string) - If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!@$params['prompt']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: prompt');
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['holiday_region'] && !is_string(@$params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (@$params['interval'] && !is_string(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['permission_set'] && !is_string(@$params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (@$params['prompt'] && !is_string(@$params['prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$prompt must be of type string; received ' . gettype(@$params['prompt']));
        }

        if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
            throw new \Files\Exception\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
        }

        if (@$params['schedule_days_of_week'] && !is_array(@$params['schedule_days_of_week'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_days_of_week must be of type array; received ' . gettype(@$params['schedule_days_of_week']));
        }

        if (@$params['schedule_time_zone'] && !is_string(@$params['schedule_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_time_zone must be of type string; received ' . gettype(@$params['schedule_time_zone']));
        }

        if (@$params['schedule_times_of_day'] && !is_array(@$params['schedule_times_of_day'])) {
            throw new \Files\Exception\InvalidParameterException('$schedule_times_of_day must be of type array; received ' . gettype(@$params['schedule_times_of_day']));
        }

        if (@$params['source'] && !is_string(@$params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (@$params['trigger'] && !is_string(@$params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_tasks', 'POST', $params, $options);

        return new AiTask((array) (@$response->data ?: []), $options);
    }
}
