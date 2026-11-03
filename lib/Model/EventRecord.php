<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EventRecord is a durable event emitted by Files.com for routing through Event Channels.
 *
 * @property int|null $id Event Record ID
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace or site-wide.
 * @property string|null $event_uuid Stable event UUID.
 * @property string|null $event_type Versioned event type string. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
 * @property string|null $severity Event severity.
 * @property string|null $source_type Source record type.
 * @property int|null $source_id Source record ID.
 * @property string|null $occurred_at Event occurrence date/time.
 * @property string|null $human_title Human-readable event title.
 * @property string|null $human_summary Human-readable event summary.
 * @property object[]|null $human_fields Human-readable event detail fields.
 * @property object|null $actor Actor associated with the event.
 * @property object[]|null $resources Resources associated with the event.
 * @property object|null $payload Event payload.
 * @property string|null $created_at Event Record create date/time.
 * @package Files
 */
class EventRecord
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
     * Event Record ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Workspace ID. 0 means the default workspace or site-wide.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Stable event UUID.
     *
     * @return string|null
     */
    public function getEventUuid()
    {
        return isset($this->attributes['event_uuid']) ? $this->attributes['event_uuid'] : null;
    }
    /**
     * Versioned event type string. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
     *
     * @return string|null
     */
    public function getEventType()
    {
        return isset($this->attributes['event_type']) ? $this->attributes['event_type'] : null;
    }
    /**
     * Event severity.
     *
     * @return string|null
     */
    public function getSeverity()
    {
        return isset($this->attributes['severity']) ? $this->attributes['severity'] : null;
    }
    /**
     * Source record type.
     *
     * @return string|null
     */
    public function getSourceType()
    {
        return isset($this->attributes['source_type']) ? $this->attributes['source_type'] : null;
    }
    /**
     * Source record ID.
     *
     * @return int|null
     */
    public function getSourceId()
    {
        return isset($this->attributes['source_id']) ? $this->attributes['source_id'] : null;
    }
    /**
     * Event occurrence date/time.
     *
     * @return string|null
     */
    public function getOccurredAt()
    {
        return isset($this->attributes['occurred_at']) ? $this->attributes['occurred_at'] : null;
    }
    /**
     * Human-readable event title.
     *
     * @return string|null
     */
    public function getHumanTitle()
    {
        return isset($this->attributes['human_title']) ? $this->attributes['human_title'] : null;
    }
    /**
     * Human-readable event summary.
     *
     * @return string|null
     */
    public function getHumanSummary()
    {
        return isset($this->attributes['human_summary']) ? $this->attributes['human_summary'] : null;
    }
    /**
     * Human-readable event detail fields.
     *
     * @return object[]|null
     */
    public function getHumanFields()
    {
        return isset($this->attributes['human_fields']) ? $this->attributes['human_fields'] : null;
    }
    /**
     * Actor associated with the event.
     *
     * @return object|null
     */
    public function getActor()
    {
        return isset($this->attributes['actor']) ? $this->attributes['actor'] : null;
    }
    /**
     * Resources associated with the event.
     *
     * @return object[]|null
     */
    public function getResources()
    {
        return isset($this->attributes['resources']) ? $this->attributes['resources'] : null;
    }
    /**
     * Event payload.
     *
     * @return object|null
     */
    public function getPayload()
    {
        return isset($this->attributes['payload']) ? $this->attributes['payload'] : null;
    }
    /**
     * Event Record create date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Event Records
     *
     * GET /event_records
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `event_type`, `created_at` or `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `event_type` or `workspace_id`. Valid field combinations are `[ event_type, created_at ]`, `[ workspace_id, created_at ]`, `[ workspace_id, event_type ]` or `[ workspace_id, event_type, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `event_type`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventRecord[]
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

        $response = Api::sendRequest('/event_records', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new EventRecord(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/event_records', $params, $options, EventRecord::class);
    }

    /**
     * Show Event Record
     *
     * GET /event_records/{id}
     *
     * @param int $id Event Record ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventRecord
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

        $response = Api::sendRequest('/event_records/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventRecord(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Event Record
     *
     * GET /event_records/{id}
     *
     * Alias of find().
     *
     * @param int $id Event Record ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventRecord
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
