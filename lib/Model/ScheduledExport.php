<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Scheduled Export defines a recurring schedule for generating one of the built-in CSV exports and e-mailing it to a Site Admin recipient.
 *
 * @property int|null $id Scheduled Export ID
 * @property string|null $name Name for this scheduled export.
 * @property string|null $export_type Export report type. Valid values: folder_size_audit, group_membership_audit, permission_audit, share_link_audit
 * @property string|null $report_name Human-readable report name.
 * @property object|null $export_options Options for the report selected by export_type.
 * @property int|null $user_id Site Admin user who receives the completed export e-mail.
 * @property bool|null $disabled If true, this scheduled export will not run.
 * @property string|null $trigger Schedule trigger type: `daily` or `custom_schedule`.
 * @property string|null $interval If trigger is `daily`, this specifies how often to run the scheduled export.
 * @property int|null $recurring_day If trigger is `daily`, this selects the day number inside the chosen interval.
 * @property int[]|null $recurring_days If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
 * @property int|null $schedule_id If trigger is `custom_schedule`, the reusable Schedule used instead of the scheduled export's schedule fields.
 * @property int[]|null $schedule_days_of_week If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
 * @property string[]|null $schedule_times_of_day Times of day in HH:MM format for the scheduled export schedule.
 * @property string|null $schedule_time_zone Time zone used by the scheduled export schedule.
 * @property string|null $holiday_region Optional holiday region used by the scheduled export schedule.
 * @property string|null $human_readable_schedule Human-readable schedule description.
 * @property string|null $last_run_at Most recent scheduled attempt time, including attempts that failed validation.
 * @property int|null $last_export_id Most recent Export ID created by this schedule.
 * @property string|null $last_error Validation error from the most recent scheduled attempt. The schedule remains enabled and retries at its next scheduled time. Cleared when an export is successfully created; does not describe errors during export generation.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class ScheduledExport
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
     * Scheduled Export ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Scheduled Export ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Name for this scheduled export.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this scheduled export.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Export report type. Valid values: folder_size_audit, group_membership_audit, permission_audit, share_link_audit
     *
     * @return string|null
     */
    public function getExportType()
    {
        return isset($this->attributes['export_type']) ? $this->attributes['export_type'] : null;
    }

    /**
     * Export report type. Valid values: folder_size_audit, group_membership_audit, permission_audit, share_link_audit
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExportType($value)
    {
        return $this->attributes['export_type'] = $value;
    }
    /**
     * Human-readable report name.
     *
     * @return string|null
     */
    public function getReportName()
    {
        return isset($this->attributes['report_name']) ? $this->attributes['report_name'] : null;
    }

    /**
     * Human-readable report name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setReportName($value)
    {
        return $this->attributes['report_name'] = $value;
    }
    /**
     * Options for the report selected by export_type.
     *
     * @return object|null
     */
    public function getExportOptions()
    {
        return isset($this->attributes['export_options']) ? $this->attributes['export_options'] : null;
    }

    /**
     * Options for the report selected by export_type.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setExportOptions($value)
    {
        return $this->attributes['export_options'] = $value;
    }
    /**
     * Site Admin user who receives the completed export e-mail.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * Site Admin user who receives the completed export e-mail.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * If true, this scheduled export will not run.
     *
     * @return bool|null
     */
    public function getDisabled()
    {
        return isset($this->attributes['disabled']) ? $this->attributes['disabled'] : null;
    }

    /**
     * If true, this scheduled export will not run.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisabled($value)
    {
        return $this->attributes['disabled'] = $value;
    }
    /**
     * Schedule trigger type: `daily` or `custom_schedule`.
     *
     * @return string|null
     */
    public function getTrigger()
    {
        return isset($this->attributes['trigger']) ? $this->attributes['trigger'] : null;
    }

    /**
     * Schedule trigger type: `daily` or `custom_schedule`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTrigger($value)
    {
        return $this->attributes['trigger'] = $value;
    }
    /**
     * If trigger is `daily`, this specifies how often to run the scheduled export.
     *
     * @return string|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * If trigger is `daily`, this specifies how often to run the scheduled export.
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
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the scheduled export's schedule fields.
     *
     * @return int|null
     */
    public function getScheduleId()
    {
        return isset($this->attributes['schedule_id']) ? $this->attributes['schedule_id'] : null;
    }

    /**
     * If trigger is `custom_schedule`, the reusable Schedule used instead of the scheduled export's schedule fields.
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
     * Times of day in HH:MM format for the scheduled export schedule.
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day in HH:MM format for the scheduled export schedule.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    /**
     * Time zone used by the scheduled export schedule.
     *
     * @return string|null
     */
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    /**
     * Time zone used by the scheduled export schedule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    /**
     * Optional holiday region used by the scheduled export schedule.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Optional holiday region used by the scheduled export schedule.
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
     * Most recent scheduled attempt time, including attempts that failed validation.
     *
     * @return string|null
     */
    public function getLastRunAt()
    {
        return isset($this->attributes['last_run_at']) ? $this->attributes['last_run_at'] : null;
    }

    /**
     * Most recent scheduled attempt time, including attempts that failed validation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastRunAt($value)
    {
        return $this->attributes['last_run_at'] = $value;
    }
    /**
     * Most recent Export ID created by this schedule.
     *
     * @return int|null
     */
    public function getLastExportId()
    {
        return isset($this->attributes['last_export_id']) ? $this->attributes['last_export_id'] : null;
    }

    /**
     * Most recent Export ID created by this schedule.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastExportId($value)
    {
        return $this->attributes['last_export_id'] = $value;
    }
    /**
     * Validation error from the most recent scheduled attempt. The schedule remains enabled and retries at its next scheduled time. Cleared when an export is successfully created; does not describe errors during export generation.
     *
     * @return string|null
     */
    public function getLastError()
    {
        return isset($this->attributes['last_error']) ? $this->attributes['last_error'] : null;
    }

    /**
     * Validation error from the most recent scheduled attempt. The schedule remains enabled and retries at its next scheduled time. Cleared when an export is successfully created; does not describe errors during export generation.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastError($value)
    {
        return $this->attributes['last_error'] = $value;
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
     * Update Scheduled Export
     *
     * PATCH /scheduled_exports/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Scheduled Export ID.
     *     - `name` (string, optional): Name for this scheduled export.
     *     - `export_type` (string, optional): Export report type. Valid values: folder_size_audit, group_membership_audit, permission_audit, share_link_audit
     *     - `export_options` (array|object, optional): Options for the report selected by export_type.
     *     - `user_id` (int, optional): Site Admin user who receives the completed export e-mail.
     *     - `disabled` (bool, optional): If true, this scheduled export will not run.
     *     - `trigger` (string, optional): Schedule trigger type: `daily` or `custom_schedule`. Allowed values: daily, custom_schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the scheduled export.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the scheduled export's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the scheduled export schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the scheduled export schedule.
     *     - `holiday_region` (string, optional): Optional holiday region used by the scheduled export schedule.
     * @return \Files\Model\ScheduledExport
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

        if (!empty($params['export_type']) && !is_string($params['export_type'])) {
            throw new \Files\Exception\InvalidParameterException('$export_type must be of type string; received ' . gettype(@$params['export_type']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
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

        $response = Api::sendRequest('/scheduled_exports/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new ScheduledExport((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Scheduled Export
     *
     * DELETE /scheduled_exports/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Scheduled Export ID.
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

        $response = Api::sendRequest('/scheduled_exports/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Scheduled Export
     *
     * DELETE /scheduled_exports/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Scheduled Export ID.
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
     * List Scheduled Exports
     *
     * GET /scheduled_exports
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `export_type` or `disabled`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled` and `export_type`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `export_type`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ScheduledExport[]
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

        $response = Api::sendRequest('/scheduled_exports', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ScheduledExport((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Scheduled Export
     *
     * GET /scheduled_exports/{id}
     *
     * @param int $id Scheduled Export ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ScheduledExport
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

        $response = Api::sendRequest('/scheduled_exports/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ScheduledExport((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Scheduled Export
     *
     * GET /scheduled_exports/{id}
     *
     * Alias of find().
     *
     * @param int $id Scheduled Export ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ScheduledExport
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Scheduled Export
     *
     * POST /scheduled_exports
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Name for this scheduled export.
     *     - `export_type` (string, required): Export report type. Valid values: folder_size_audit, group_membership_audit, permission_audit, share_link_audit
     *     - `export_options` (array|object, optional): Options for the report selected by export_type.
     *     - `user_id` (int, optional): Site Admin user who receives the completed export e-mail.
     *     - `disabled` (bool, optional): If true, this scheduled export will not run.
     *     - `trigger` (string, optional): Schedule trigger type: `daily` or `custom_schedule`. Allowed values: daily, custom_schedule.
     *     - `interval` (string, optional): If trigger is `daily`, this specifies how often to run the scheduled export.
     *     - `recurring_day` (int, optional): If trigger is `daily`, this selects the day number inside the chosen interval.
     *     - `recurring_days` (int[], optional): If trigger is `daily`, this selects one or more day numbers inside a `week`, `month`, `quarter`, or `year` interval.
     *     - `schedule_id` (int, optional): If trigger is `custom_schedule`, the reusable Schedule used instead of the scheduled export's schedule fields.
     *     - `schedule_days_of_week` (int[], optional): If trigger is `custom_schedule`, the 0-based weekdays used by the schedule.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format for the scheduled export schedule.
     *     - `schedule_time_zone` (string, optional): Time zone used by the scheduled export schedule.
     *     - `holiday_region` (string, optional): Optional holiday region used by the scheduled export schedule.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ScheduledExport
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['export_type'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: export_type');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['export_type']) && !is_string($params['export_type'])) {
            throw new \Files\Exception\InvalidParameterException('$export_type must be of type string; received ' . gettype(@$params['export_type']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
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

        $response = Api::sendRequest('/scheduled_exports', 'POST', $params, $options);

        return new ScheduledExport((array) (@$response->data ?: []), $options);
    }
}
