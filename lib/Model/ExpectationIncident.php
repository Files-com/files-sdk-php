<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ExpectationIncident
 *
 * @package Files
 */
class ExpectationIncident
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
    // int64 # Expectation Incident ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }
    // int64 # Expectation ID.
    public function getExpectationId()
    {
        return @$this->attributes['expectation_id'];
    }
    // string # Incident status.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // date-time # When the incident was opened.
    public function getOpenedAt()
    {
        return @$this->attributes['opened_at'];
    }
    // date-time # When the most recent failing evaluation contributing to the incident occurred.
    public function getLastFailedAt()
    {
        return @$this->attributes['last_failed_at'];
    }
    // date-time # When the incident was acknowledged.
    public function getAcknowledgedAt()
    {
        return @$this->attributes['acknowledged_at'];
    }
    // date-time # When the current snooze expires.
    public function getSnoozedUntil()
    {
        return @$this->attributes['snoozed_until'];
    }
    // date-time # When the incident was resolved.
    public function getResolvedAt()
    {
        return @$this->attributes['resolved_at'];
    }
    // int64 # Evaluation that first opened the incident.
    public function getOpenedByEvaluationId()
    {
        return @$this->attributes['opened_by_evaluation_id'];
    }
    // int64 # Most recent evaluation linked to the incident.
    public function getLastEvaluationId()
    {
        return @$this->attributes['last_evaluation_id'];
    }
    // int64 # Evaluation that resolved the incident.
    public function getResolvedByEvaluationId()
    {
        return @$this->attributes['resolved_by_evaluation_id'];
    }
    // object # Compact incident summary payload.
    public function getSummary()
    {
        return @$this->attributes['summary'];
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

    // Resolve an expectation incident
    public function resolve($params = [])
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

        $response = Api::sendRequest('/expectation_incidents/' . @$params['id'] . '/resolve', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    // Snooze an expectation incident until a specified time
    //
    // Parameters:
    //   snoozed_until (required) - string - Time until which the incident should remain snoozed.
    public function snooze($params = [])
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

        if (!@$params['snoozed_until']) {
            if (@$this->snoozed_until) {
                $params['snoozed_until'] = $this->snoozed_until;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: snoozed_until');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['snoozed_until'] && !is_string(@$params['snoozed_until'])) {
            throw new \Files\Exception\InvalidParameterException('$snoozed_until must be of type string; received ' . gettype(@$params['snoozed_until']));
        }

        $response = Api::sendRequest('/expectation_incidents/' . @$params['id'] . '/snooze', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    // Acknowledge an expectation incident
    public function acknowledge($params = [])
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

        $response = Api::sendRequest('/expectation_incidents/' . @$params['id'] . '/acknowledge', 'POST', $params, $this->options);
        return new ExpectationIncident((array) (@$response->data ?: []), $this->options);
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `expectation_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `expectation_id` and `workspace_id`. Valid field combinations are `[ workspace_id, expectation_id ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/expectation_incidents', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExpectationIncident((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Expectation Incident ID.
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

        $response = Api::sendRequest('/expectation_incidents/' . @$params['id'] . '', 'GET', $params, $options);

        return new ExpectationIncident((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
