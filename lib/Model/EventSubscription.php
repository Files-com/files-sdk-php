<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EventSubscription selects EventRecords for an EventChannel and sends them to one or more EventTargets.
 *
 * @property int|null $id Event Subscription ID
 * @property int|null $event_channel_id Event Channel ID
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace or site-wide.
 * @property bool|null $apply_to_all_workspaces If true, this default-workspace subscription applies to events from all workspaces.
 * @property string|null $name Event Subscription name.
 * @property string|null $subject Custom subject line to use for notification emails.
 * @property string|null $message Custom message to include in notification emails.
 * @property bool|null $message_only If true, notification email bodies contain only the custom message, omitting event details and the review button. Requires a custom message, defaults to false, and does not affect non-email targets.
 * @property bool|null $enabled Whether this Event Subscription can dispatch events.
 * @property string[]|null $event_types Event type strings matched by this subscription. Blank means all event types. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
 * @property object|null $filter Conditions on event fields that an event must meet to be delivered, keyed by dot-notation field path. All conditions must match.
 * @property object|null $delivery_policy Event Subscription delivery policy. Overrides retry settings from each Event Target's delivery policy.
 * @property int[]|null $event_target_ids Event Target IDs this subscription sends to.
 * @property string|null $created_at Event Subscription create date/time.
 * @property string|null $updated_at Event Subscription update date/time.
 * @package Files
 */
class EventSubscription
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
     * Event Subscription ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Event Subscription ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Event Channel ID
     *
     * @return int|null
     */
    public function getEventChannelId()
    {
        return isset($this->attributes['event_channel_id']) ? $this->attributes['event_channel_id'] : null;
    }

    /**
     * Event Channel ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setEventChannelId($value)
    {
        return $this->attributes['event_channel_id'] = $value;
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
     * Workspace ID. 0 means the default workspace or site-wide.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * If true, this default-workspace subscription applies to events from all workspaces.
     *
     * @return bool|null
     */
    public function getApplyToAllWorkspaces()
    {
        return isset($this->attributes['apply_to_all_workspaces']) ? $this->attributes['apply_to_all_workspaces'] : null;
    }

    /**
     * If true, this default-workspace subscription applies to events from all workspaces.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    /**
     * Event Subscription name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Event Subscription name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Custom subject line to use for notification emails.
     *
     * @return string|null
     */
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }

    /**
     * Custom subject line to use for notification emails.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSubject($value)
    {
        return $this->attributes['subject'] = $value;
    }
    /**
     * Custom message to include in notification emails.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }

    /**
     * Custom message to include in notification emails.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMessage($value)
    {
        return $this->attributes['message'] = $value;
    }
    /**
     * If true, notification email bodies contain only the custom message, omitting event details and the review button. Requires a custom message, defaults to false, and does not affect non-email targets.
     *
     * @return bool|null
     */
    public function getMessageOnly()
    {
        return isset($this->attributes['message_only']) ? $this->attributes['message_only'] : null;
    }

    /**
     * If true, notification email bodies contain only the custom message, omitting event details and the review button. Requires a custom message, defaults to false, and does not affect non-email targets.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setMessageOnly($value)
    {
        return $this->attributes['message_only'] = $value;
    }
    /**
     * Whether this Event Subscription can dispatch events.
     *
     * @return bool|null
     */
    public function getEnabled()
    {
        return isset($this->attributes['enabled']) ? $this->attributes['enabled'] : null;
    }

    /**
     * Whether this Event Subscription can dispatch events.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    /**
     * Event type strings matched by this subscription. Blank means all event types. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
     *
     * @return string[]|null
     */
    public function getEventTypes()
    {
        return isset($this->attributes['event_types']) ? $this->attributes['event_types'] : null;
    }

    /**
     * Event type strings matched by this subscription. Blank means all event types. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setEventTypes($value)
    {
        return $this->attributes['event_types'] = $value;
    }
    /**
     * Conditions on event fields that an event must meet to be delivered, keyed by dot-notation field path. All conditions must match.
     *
     * @return object|null
     */
    public function getFilter()
    {
        return isset($this->attributes['filter']) ? $this->attributes['filter'] : null;
    }

    /**
     * Conditions on event fields that an event must meet to be delivered, keyed by dot-notation field path. All conditions must match.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setFilter($value)
    {
        return $this->attributes['filter'] = $value;
    }
    /**
     * Event Subscription delivery policy. Overrides retry settings from each Event Target's delivery policy.
     *
     * @return object|null
     */
    public function getDeliveryPolicy()
    {
        return isset($this->attributes['delivery_policy']) ? $this->attributes['delivery_policy'] : null;
    }

    /**
     * Event Subscription delivery policy. Overrides retry settings from each Event Target's delivery policy.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setDeliveryPolicy($value)
    {
        return $this->attributes['delivery_policy'] = $value;
    }
    /**
     * Event Target IDs this subscription sends to.
     *
     * @return int[]|null
     */
    public function getEventTargetIds()
    {
        return isset($this->attributes['event_target_ids']) ? $this->attributes['event_target_ids'] : null;
    }

    /**
     * Event Target IDs this subscription sends to.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setEventTargetIds($value)
    {
        return $this->attributes['event_target_ids'] = $value;
    }
    /**
     * Event Subscription create date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Event Subscription update date/time.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Update Event Subscription
     *
     * PATCH /event_subscriptions/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Subscription ID.
     *     - `event_channel_id` (int, optional): Event Channel ID
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace or site-wide.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace subscription applies to events from all workspaces.
     *     - `name` (string, optional): Event Subscription name.
     *     - `subject` (string, optional): Custom subject line to use for notification emails.
     *     - `message` (string, optional): Custom message to include in notification emails.
     *     - `message_only` (bool, optional): If true, notification email bodies contain only the custom message, omitting event details and the review button. Requires a custom message, defaults to false, and does not affect non-email targets.
     *     - `enabled` (bool, optional): Whether this Event Subscription can dispatch events.
     *     - `event_types` (string[], optional): Event type strings matched by this subscription. Blank means all event types. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
     *     - `filter` (array|object, optional): Conditions on event fields that an event must meet to be delivered, keyed by dot-notation field path. All conditions must match.
     *     - `delivery_policy` (array|object, optional): Event Subscription delivery policy. Overrides retry settings from each Event Target's delivery policy.
     *     - `event_target_ids` (int[], optional): Event Target IDs this subscription sends to.
     * @return \Files\Model\EventSubscription
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

        if (!empty($params['event_channel_id']) && !is_int($params['event_channel_id'])) {
            throw new \Files\Exception\InvalidParameterException('$event_channel_id must be of type int; received ' . gettype(@$params['event_channel_id']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['subject']) && !is_string($params['subject'])) {
            throw new \Files\Exception\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
        }

        if (!empty($params['message']) && !is_string($params['message'])) {
            throw new \Files\Exception\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
        }

        if (!empty($params['event_types']) && !is_array($params['event_types'])) {
            throw new \Files\Exception\InvalidParameterException('$event_types must be of type array; received ' . gettype(@$params['event_types']));
        }

        if (!empty($params['event_target_ids']) && !is_array($params['event_target_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$event_target_ids must be of type array; received ' . gettype(@$params['event_target_ids']));
        }

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new EventSubscription((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Event Subscription
     *
     * DELETE /event_subscriptions/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Subscription ID.
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

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Event Subscription
     *
     * DELETE /event_subscriptions/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Subscription ID.
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
     * List Event Subscriptions
     *
     * GET /event_subscriptions
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `enabled`, `event_channel_id` or `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `enabled`, `event_channel_id` or `workspace_id`. Valid field combinations are `[ enabled, event_channel_id ]`, `[ workspace_id, enabled ]`, `[ workspace_id, event_channel_id ]` or `[ workspace_id, enabled, event_channel_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventSubscription[]
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

        $response = Api::sendRequest('/event_subscriptions', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EventSubscription((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Event Subscription
     *
     * GET /event_subscriptions/{id}
     *
     * @param int $id Event Subscription ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventSubscription
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

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventSubscription((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Event Subscription
     *
     * GET /event_subscriptions/{id}
     *
     * Alias of find().
     *
     * @param int $id Event Subscription ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventSubscription
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Event Subscription
     *
     * POST /event_subscriptions
     *
     * @param array $params Request parameters.
     *     - `event_channel_id` (int, optional): Event Channel ID
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace or site-wide.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace subscription applies to events from all workspaces.
     *     - `name` (string, required): Event Subscription name.
     *     - `subject` (string, optional): Custom subject line to use for notification emails.
     *     - `message` (string, optional): Custom message to include in notification emails.
     *     - `message_only` (bool, optional): If true, notification email bodies contain only the custom message, omitting event details and the review button. Requires a custom message, defaults to false, and does not affect non-email targets.
     *     - `enabled` (bool, optional): Whether this Event Subscription can dispatch events.
     *     - `event_types` (string[], optional): Event type strings matched by this subscription. Blank means all event types. Valid values: `automation_run.canceled.v1`, `automation_run.failure.v1`, `automation_run.failure_will_retry.v1`, `automation_run.partial_failure.v1`, `automation_run.partial_failure_will_retry.v1`, `automation_run.skipped.v1`, `automation_run.success.v1`, `expectation_evaluation.invalid.v1`, `expectation_evaluation.late.v1`, `expectation_evaluation.missing.v1`, `expectation_evaluation.success.v1`, `expectation_incident.acknowledged.v1`, `expectation_incident.open.v1`, `expectation_incident.resolved.v1`, `expectation_incident.snoozed.v1`, `external_event.client_log.failure.v1`, `external_event.client_log.partial_failure.v1`, `external_event.client_log.skipped.v1`, `external_event.client_log.success.v1`, `malware_scan_log.quarantined.v1`, `pending_work_event.failure.v1`, `pending_work_event.partial_failure.v1`, `pending_work_event.skipped.v1`, `pending_work_event.success.v1`, `siem_http_destination_event.failure.v1`, `siem_http_destination_event.partial_failure.v1`, `siem_http_destination_event.skipped.v1`, `siem_http_destination_event.success.v1`, `sso_event.ldap_login.failure.v1`, `sso_event.ldap_login.partial_failure.v1`, `sso_event.ldap_login.skipped.v1`, `sso_event.ldap_login.success.v1`, `sso_event.ldap_sync.failure.v1`, `sso_event.ldap_sync.partial_failure.v1`, `sso_event.ldap_sync.skipped.v1`, `sso_event.ldap_sync.success.v1`, `sso_event.saml_login.failure.v1`, `sso_event.saml_login.partial_failure.v1`, `sso_event.saml_login.skipped.v1`, `sso_event.saml_login.success.v1`, `sync_run.failure.v1`, `sync_run.partial_failure.v1`, `sync_run.skipped.v1`, `sync_run.success.v1`, `user_security_event.lockout.v1`
     *     - `filter` (array|object, optional): Conditions on event fields that an event must meet to be delivered, keyed by dot-notation field path. All conditions must match.
     *     - `delivery_policy` (array|object, optional): Event Subscription delivery policy. Overrides retry settings from each Event Target's delivery policy.
     *     - `event_target_ids` (int[], optional): Event Target IDs this subscription sends to.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventSubscription
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!empty($params['event_channel_id']) && !is_int($params['event_channel_id'])) {
            throw new \Files\Exception\InvalidParameterException('$event_channel_id must be of type int; received ' . gettype(@$params['event_channel_id']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['subject']) && !is_string($params['subject'])) {
            throw new \Files\Exception\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
        }

        if (!empty($params['message']) && !is_string($params['message'])) {
            throw new \Files\Exception\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
        }

        if (!empty($params['event_types']) && !is_array($params['event_types'])) {
            throw new \Files\Exception\InvalidParameterException('$event_types must be of type array; received ' . gettype(@$params['event_types']));
        }

        if (!empty($params['event_target_ids']) && !is_array($params['event_target_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$event_target_ids must be of type array; received ' . gettype(@$params['event_target_ids']));
        }

        $response = Api::sendRequest('/event_subscriptions', 'POST', $params, $options);

        return new EventSubscription((array) (@$response->data ?: []), $options);
    }
}
