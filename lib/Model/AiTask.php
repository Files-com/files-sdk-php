<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AI Task defines a Files.com AI prompt that can run on a schedule or in response to file actions.
 *
 * @property int|null $id AI Task ID.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $name AI Task name.
 * @property string|null $description AI Task description.
 * @property string|null $prompt Prompt sent when this AI Task is invoked.
 * @property string|null $permission_set Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
 * @property string|null $path Path scope used for action-triggered AI Tasks. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $source Source glob used with `path` for action-triggered AI Tasks.
 * @property bool|null $disabled If true, this AI Task will not run.
 * @property string|null $trigger How this AI Task is triggered.
 * @property string[]|null $trigger_actions If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
 * @property string|null $interval If trigger is `daily`, this specifies how often to run the AI Task.
 * @property int|null $recurring_day If trigger is `daily`, this selects the day number inside the chosen interval.
 * @property int[]|null $recurring_days If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
 * @property int|null $schedule_id If trigger is `custom_schedule`, the reusable Schedule used instead of the AI Task's schedule fields.
 * @property int[]|null $schedule_days_of_week If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
 * @property string[]|null $schedule_times_of_day Times of day in HH:MM format for the AI Task schedule.
 * @property string|null $schedule_time_zone Time zone used by the AI Task schedule.
 * @property string|null $holiday_region Optional holiday region used by the AI Task schedule.
 * @property string|null $human_readable_schedule Human-readable schedule description.
 * @property string|null $last_run_at Most recent successful invocation time.
 * @property int|null $master_admin_user_id Master User ID used for AI Task invocations.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class AiTask
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
     * AI Task ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * AI Task ID.
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
     * AI Task name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * AI Task name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * AI Task description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * AI Task description.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Prompt sent when this AI Task is invoked.
     *
     * @return string|null
     */
    public function getPrompt()
    {
        return isset($this->attributes['prompt']) ? $this->attributes['prompt'] : null;
    }

    /**
     * Prompt sent when this AI Task is invoked.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPrompt($value)
    {
        return $this->attributes['prompt'] = $value;
    }
    /**
     * Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
     *
     * @return string|null
     */
    public function getPermissionSet()
    {
        return isset($this->attributes['permission_set']) ? $this->attributes['permission_set'] : null;
    }

    /**
     * Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPermissionSet($value)
    {
        return $this->attributes['permission_set'] = $value;
    }
    /**
     * Path scope used for action-triggered AI Tasks. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Path scope used for action-triggered AI Tasks. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Source glob used with `path` for action-triggered AI Tasks.
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }

    /**
     * Source glob used with `path` for action-triggered AI Tasks.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSource($value)
    {
        return $this->attributes['source'] = $value;
    }
    /**
     * If true, this AI Task will not run.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * If true, this AI Task will not run.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * How this AI Task is triggered.
     *
     * @return string|null
     */
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    /**
     * How this AI Task is triggered.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    /**
     * If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
     *
     * @return string[]|null
     */
    public function getTriggerActions()
    {
        return isset($this->attributes['trigger_actions']) ? $this->attributes['trigger_actions'] : null;
    }

    /**
     * If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setTriggerActions($value)
    {
        return $this->attributes['trigger_actions'] = $value;
    }
    /**
     * If trigger is `daily`, this specifies how often to run the AI Task.
     *
     * @return string|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * If trigger is `daily`, this specifies how often to run the AI Task.
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
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the AI Task's schedule fields.
     *
     * @return int|null
     */
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the AI Task's schedule fields.
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
     * Times of day in HH:MM format for the AI Task schedule.
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day in HH:MM format for the AI Task schedule.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    /**
     * Time zone used by the AI Task schedule.
     *
     * @return string|null
     */
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    /**
     * Time zone used by the AI Task schedule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    /**
     * Optional holiday region used by the AI Task schedule.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Optional holiday region used by the AI Task schedule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    /**
     * Human-readable schedule description.
     *
     * @return string|null
     */
    public function getHumanReadableSchedule()
    {
        return isset($this->attributes['human_readable_schedule']) ? $this->attributes['human_readable_schedule'] : null;
    }

    /**
     * Human-readable schedule description.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHumanReadableSchedule($value)
    {
        return $this->attributes['human_readable_schedule'] = $value;
    }
    /**
     * Most recent successful invocation time.
     *
     * @return string|null
     */
    public function getLastRunAt()
    {
        return isset($this->attributes['last_run_at']) ? $this->attributes['last_run_at'] : null;
    }

    /**
     * Most recent successful invocation time.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastRunAt($value)
    {
        return $this->attributes['last_run_at'] = $value;
    }
    /**
     * Master User ID used for AI Task invocations.
     *
     * @return int|null
     */
    public function getMasterAdminUserId()
    {
        return isset($this->attributes['master_admin_user_id']) ? $this->attributes['master_admin_user_id'] : null;
    }

    /**
     * Master User ID used for AI Task invocations.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setMasterAdminUserId($value)
    {
        return $this->attributes['master_admin_user_id'] = $value;
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
     * Manually Run AI Task
     *
     * POST /ai_tasks/{id}/manual_run
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Task ID.
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/ai_tasks/' . rawurlencode(strval(@$params['id'])) . '/manual_run', 'POST', $params, $this->options);
        return;
    }

    /**
     * Update Ai Task
     *
     * PATCH /ai_tasks/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Task ID.
     *     - `description` (string, optional): AI Task description.
     *     - `disabled` (bool, optional): If true, this AI Task will not run.
     *     - `holiday_region` (string, optional): Optional holiday region used by the AI Task schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the AI Task.
     *     - `name` (string, optional): AI Task name.
     *     - `path` (string, optional): Path scope used for action-triggered AI Tasks.
     *     - `permission_set` (string, optional): Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`. Allowed values: full, files_only.
     *     - `prompt` (string, optional): Prompt sent when this AI Task is invoked.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the AI Task's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the AI Task schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the AI Task schedule.
     *     - `source` (string, optional): Source glob used with `path` for action-triggered AI Tasks.
     *     - `trigger` (string, optional): How this AI Task is triggered. Allowed values: manual, daily, custom_schedule, action.
     *     - `trigger_actions` (string[], optional): If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\AiTask
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

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['permission_set']) && !is_string($params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (!empty($params['prompt']) && !is_string($params['prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$prompt must be of type string; received ' . gettype(@$params['prompt']));
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

        if (!empty($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['trigger_actions']) && !is_array($params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_tasks/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new AiTask((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Ai Task
     *
     * DELETE /ai_tasks/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Task ID.
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

        $response = Api::sendRequest('/ai_tasks/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Ai Task
     *
     * DELETE /ai_tasks/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Task ID.
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
     * List Ai Tasks
     *
     * GET /ai_tasks
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name`, `disabled` or `updated_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled`, `trigger` or `workspace_id`. Valid field combinations are `[ workspace_id, disabled ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiTask[]
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

        $response = Api::sendRequest('/ai_tasks', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AiTask((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Ai Task
     *
     * GET /ai_tasks/{id}
     *
     * @param int $id Ai Task ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiTask
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

        $response = Api::sendRequest('/ai_tasks/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AiTask((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Ai Task
     *
     * GET /ai_tasks/{id}
     *
     * Alias of find().
     *
     * @param int $id Ai Task ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiTask
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Ai Task
     *
     * POST /ai_tasks
     *
     * @param array $params Request parameters.
     *     - `description` (string, optional): AI Task description.
     *     - `disabled` (bool, optional): If true, this AI Task will not run.
     *     - `holiday_region` (string, optional): Optional holiday region used by the AI Task schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the AI Task.
     *     - `name` (string, required): AI Task name.
     *     - `path` (string, optional): Path scope used for action-triggered AI Tasks.
     *     - `permission_set` (string, optional): Permissions used by the internal API key for this AI Task. Valid values are `full` and `files_only`. Allowed values: full, files_only.
     *     - `prompt` (string, required): Prompt sent when this AI Task is invoked.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the AI Task's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the AI Task schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the AI Task schedule.
     *     - `source` (string, optional): Source glob used with `path` for action-triggered AI Tasks.
     *     - `trigger` (string, optional): How this AI Task is triggered. Allowed values: manual, daily, custom_schedule, action.
     *     - `trigger_actions` (string[], optional): If trigger is `action`, the file action types that invoke this AI Task. Valid actions are create, copy, move, archived_delete, update, read, destroy.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiTask
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['prompt'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: prompt');
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['holiday_region']) && !is_string($params['holiday_region'])) {
            throw new \Files\Exception\InvalidParameterException('$holiday_region must be of type string; received ' . gettype(@$params['holiday_region']));
        }

        if (!empty($params['interval']) && !is_string($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['permission_set']) && !is_string($params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (!empty($params['prompt']) && !is_string($params['prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$prompt must be of type string; received ' . gettype(@$params['prompt']));
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

        if (!empty($params['source']) && !is_string($params['source'])) {
            throw new \Files\Exception\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
        }

        if (!empty($params['trigger']) && !is_string($params['trigger'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
        }

        if (!empty($params['trigger_actions']) && !is_array($params['trigger_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_tasks', 'POST', $params, $options);

        return new AiTask((array) (@$response->data ?: []), $options);
    }
}
