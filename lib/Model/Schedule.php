<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Schedule is a named, reusable weekday-and-time schedule shared by scheduled resources across a Site.
 *
 * @property int|null $id Schedule ID.
 * @property string|null $name Schedule name.
 * @property int[]|null $schedule_days_of_week 0-based weekdays used by the Schedule. 0 is Sunday.
 * @property string[]|null $schedule_times_of_day Times of day in HH:MM format (24-hour).
 * @property string|null $schedule_time_zone Time zone for scheduled times. If not set, times are interpreted as UTC.
 * @property string|null $holiday_region Optional holiday region on which linked resources do not run.
 * @property string|null $human_readable_schedule Human-readable Schedule description.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class Schedule
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
     * Schedule ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Schedule ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Schedule name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Schedule name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * 0-based weekdays used by the Schedule. 0 is Sunday.
     *
     * @return int[]|null
     */
    public function getScheduleDaysOfWeek()
    {
        return isset($this->attributes['schedule_days_of_week']) ? $this->attributes['schedule_days_of_week'] : null;
    }

    /**
     * 0-based weekdays used by the Schedule. 0 is Sunday.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setScheduleDaysOfWeek($value)
    {
        return $this->attributes['schedule_days_of_week'] = $value;
    }
    /**
     * Times of day in HH:MM format (24-hour).
     *
     * @return string[]|null
     */
    public function getScheduleTimesOfDay()
    {
        return isset($this->attributes['schedule_times_of_day']) ? $this->attributes['schedule_times_of_day'] : null;
    }

    /**
     * Times of day in HH:MM format (24-hour).
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setScheduleTimesOfDay($value)
    {
        return $this->attributes['schedule_times_of_day'] = $value;
    }
    /**
     * Time zone for scheduled times. If not set, times are interpreted as UTC.
     *
     * @return string|null
     */
    public function getScheduleTimeZone()
    {
        return isset($this->attributes['schedule_time_zone']) ? $this->attributes['schedule_time_zone'] : null;
    }

    /**
     * Time zone for scheduled times. If not set, times are interpreted as UTC.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setScheduleTimeZone($value)
    {
        return $this->attributes['schedule_time_zone'] = $value;
    }
    /**
     * Optional holiday region on which linked resources do not run.
     *
     * @return string|null
     */
    public function getHolidayRegion()
    {
        return isset($this->attributes['holiday_region']) ? $this->attributes['holiday_region'] : null;
    }

    /**
     * Optional holiday region on which linked resources do not run.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHolidayRegion($value)
    {
        return $this->attributes['holiday_region'] = $value;
    }
    /**
     * Human-readable Schedule description.
     *
     * @return string|null
     */
    public function getHumanReadableSchedule()
    {
        return isset($this->attributes['human_readable_schedule']) ? $this->attributes['human_readable_schedule'] : null;
    }

    /**
     * Human-readable Schedule description.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHumanReadableSchedule($value)
    {
        return $this->attributes['human_readable_schedule'] = $value;
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
     * Update Schedule
     *
     * PATCH /schedules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Schedule ID.
     *     - `name` (string, optional): Schedule name.
     *     - `schedule_days_of_week` (int[], optional): 0-based weekdays used by the Schedule. 0 is Sunday.
     *     - `schedule_times_of_day` (string[], optional): Times of day in HH:MM format (24-hour).
     *     - `schedule_time_zone` (string, optional): Time zone for scheduled times. If not set, times are interpreted as UTC.
     *     - `holiday_region` (string, optional): Optional holiday region on which linked resources do not run.
     * @return \Files\Model\Schedule
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

        $response = Api::sendRequest('/schedules/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Schedule((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Schedule
     *
     * DELETE /schedules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Schedule ID.
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

        $response = Api::sendRequest('/schedules/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Schedule
     *
     * DELETE /schedules/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Schedule ID.
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
     * List Schedules
     *
     * GET /schedules
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Schedule[]
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

        $response = Api::sendRequest('/schedules', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Schedule((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Schedule
     *
     * GET /schedules/{id}
     *
     * @param int $id Schedule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Schedule
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

        $response = Api::sendRequest('/schedules/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Schedule((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Schedule
     *
     * GET /schedules/{id}
     *
     * Alias of find().
     *
     * @param int $id Schedule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Schedule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Schedule
     *
     * POST /schedules
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Schedule name.
     *     - `schedule_days_of_week` (int[], required): 0-based weekdays used by the Schedule. 0 is Sunday.
     *     - `schedule_times_of_day` (string[], required): Times of day in HH:MM format (24-hour).
     *     - `schedule_time_zone` (string, optional): Time zone for scheduled times. If not set, times are interpreted as UTC.
     *     - `holiday_region` (string, optional): Optional holiday region on which linked resources do not run.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Schedule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['schedule_days_of_week'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: schedule_days_of_week');
        }

        if (empty($params['schedule_times_of_day'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: schedule_times_of_day');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
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

        $response = Api::sendRequest('/schedules', 'POST', $params, $options);

        return new Schedule((array) (@$response->data ?: []), $options);
    }
}
