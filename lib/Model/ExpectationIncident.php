<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An ExpectationIncident groups ongoing failure behavior for an Expectation over time.
 *
 * @property int|null $id Expectation Incident ID
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property int|null $expectation_id Expectation ID.
 * @property string|null $status Incident status.
 * @property string|null $opened_at When the incident was opened.
 * @property string|null $last_failed_at When the most recent failing evaluation contributing to the incident occurred.
 * @property string|null $acknowledged_at When the incident was acknowledged.
 * @property string|null $snoozed_until When the current snooze expires.
 * @property string|null $resolved_at When the incident was resolved.
 * @property int|null $opened_by_evaluation_id Evaluation that first opened the incident.
 * @property int|null $last_evaluation_id Most recent evaluation linked to the incident.
 * @property int|null $resolved_by_evaluation_id Evaluation that resolved the incident.
 * @property object|null $summary Compact incident summary payload.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class ExpectationIncident
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
     * Expectation Incident ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
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
     * Expectation ID.
     *
     * @return int|null
     */
    public function getExpectationId()
    {
        return isset($this->attributes['expectation_id']) ? $this->attributes['expectation_id'] : null;
    }
    /**
     * Incident status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * When the incident was opened.
     *
     * @return string|null
     */
    public function getOpenedAt()
    {
        return isset($this->attributes['opened_at']) ? $this->attributes['opened_at'] : null;
    }
    /**
     * When the most recent failing evaluation contributing to the incident occurred.
     *
     * @return string|null
     */
    public function getLastFailedAt()
    {
        return isset($this->attributes['last_failed_at']) ? $this->attributes['last_failed_at'] : null;
    }
    /**
     * When the incident was acknowledged.
     *
     * @return string|null
     */
    public function getAcknowledgedAt()
    {
        return isset($this->attributes['acknowledged_at']) ? $this->attributes['acknowledged_at'] : null;
    }
    /**
     * When the current snooze expires.
     *
     * @return string|null
     */
    public function getSnoozedUntil()
    {
        return isset($this->attributes['snoozed_until']) ? $this->attributes['snoozed_until'] : null;
    }
    /**
     * When the incident was resolved.
     *
     * @return string|null
     */
    public function getResolvedAt()
    {
        return isset($this->attributes['resolved_at']) ? $this->attributes['resolved_at'] : null;
    }
    /**
     * Evaluation that first opened the incident.
     *
     * @return int|null
     */
    public function getOpenedByEvaluationId()
    {
        return isset($this->attributes['opened_by_evaluation_id']) ? $this->attributes['opened_by_evaluation_id'] : null;
    }
    /**
     * Most recent evaluation linked to the incident.
     *
     * @return int|null
     */
    public function getLastEvaluationId()
    {
        return isset($this->attributes['last_evaluation_id']) ? $this->attributes['last_evaluation_id'] : null;
    }
    /**
     * Evaluation that resolved the incident.
     *
     * @return int|null
     */
    public function getResolvedByEvaluationId()
    {
        return isset($this->attributes['resolved_by_evaluation_id']) ? $this->attributes['resolved_by_evaluation_id'] : null;
    }
    /**
     * Compact incident summary payload.
     *
     * @return object|null
     */
    public function getSummary()
    {
        return isset($this->attributes['summary']) ? $this->attributes['summary'] : null;
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
     * Resolve an expectation incident
     *
     * POST /expectation_incidents/{id}/resolve
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation Incident ID.
     * @return \Files\Model\ExpectationIncident
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function resolve($params = [])
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

        $response = Api::sendRequest('/expectation_incidents/' . rawurlencode(strval(@$params['id'])) . '/resolve', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Snooze an expectation incident until a specified time
     *
     * POST /expectation_incidents/{id}/snooze
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation Incident ID.
     *     - `snoozed_until` (string, required): Time until which the incident should remain snoozed.
     * @return \Files\Model\ExpectationIncident
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function snooze($params = [])
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

        if (empty($params['snoozed_until'])) {
            if (@$this->snoozed_until) {
                $params['snoozed_until'] = $this->snoozed_until;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: snoozed_until');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['snoozed_until']) && !is_string($params['snoozed_until'])) {
            throw new \Files\Exception\InvalidParameterException('$snoozed_until must be of type string; received ' . gettype(@$params['snoozed_until']));
        }

        $response = Api::sendRequest('/expectation_incidents/' . rawurlencode(strval(@$params['id'])) . '/snooze', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Acknowledge an expectation incident
     *
     * POST /expectation_incidents/{id}/acknowledge
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Expectation Incident ID.
     * @return \Files\Model\ExpectationIncident
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function acknowledge($params = [])
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

        $response = Api::sendRequest('/expectation_incidents/' . rawurlencode(strval(@$params['id'])) . '/acknowledge', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    /**
     * List Expectation Incidents
     *
     * GET /expectation_incidents
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `expectation_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `expectation_id` and `workspace_id`. Valid field combinations are `[ workspace_id, expectation_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ExpectationIncident[]
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

        $response = Api::sendRequest('/expectation_incidents', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExpectationIncident((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Expectation Incident
     *
     * GET /expectation_incidents/{id}
     *
     * @param int $id Expectation Incident ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ExpectationIncident
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

        $response = Api::sendRequest('/expectation_incidents/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ExpectationIncident((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Expectation Incident
     *
     * GET /expectation_incidents/{id}
     *
     * Alias of find().
     *
     * @param int $id Expectation Incident ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ExpectationIncident
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
