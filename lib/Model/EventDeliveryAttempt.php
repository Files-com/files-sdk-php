<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EventDeliveryAttempt records delivery state for an EventRecord and EventTarget.
 *
 * @property int|null $id Event Delivery Attempt ID
 * @property int|null $event_record_id Event Record ID
 * @property int|null $event_subscription_id Event Subscription ID
 * @property int|null $event_target_id Event Target ID
 * @property int|null $activity_notification_batch_id Shared summary ID when this event is delivered in a Slack or Teams subfolder summary. All events in a summary share its delivery outcome.
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace or site-wide.
 * @property string|null $status Delivery status.
 * @property int|null $attempt_number Number of delivery attempts made.
 * @property int|null $response_code HTTP response code, if applicable.
 * @property string|null $error_message Delivery error message, if applicable.
 * @property string|null $response_body Delivery response body, if applicable.
 * @property int|null $latency_ms Delivery latency in milliseconds.
 * @property string|null $delivered_at Successful delivery date/time.
 * @property string|null $last_attempted_at Most recent attempt date/time.
 * @property string|null $next_attempt_at Next scheduled attempt date/time.
 * @property string|null $created_at Delivery Attempt create date/time.
 * @package Files
 */
class EventDeliveryAttempt
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
     * Event Delivery Attempt ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Event Record ID
     *
     * @return int|null
     */
    public function getEventRecordId()
    {
        return isset($this->attributes['event_record_id']) ? $this->attributes['event_record_id'] : null;
    }
    /**
     * Event Subscription ID
     *
     * @return int|null
     */
    public function getEventSubscriptionId()
    {
        return isset($this->attributes['event_subscription_id']) ? $this->attributes['event_subscription_id'] : null;
    }
    /**
     * Event Target ID
     *
     * @return int|null
     */
    public function getEventTargetId()
    {
        return isset($this->attributes['event_target_id']) ? $this->attributes['event_target_id'] : null;
    }
    /**
     * Shared summary ID when this event is delivered in a Slack or Teams subfolder summary. All events in a summary share its delivery outcome.
     *
     * @return int|null
     */
    public function getActivityNotificationBatchId()
    {
        return isset($this->attributes['activity_notification_batch_id']) ? $this->attributes['activity_notification_batch_id'] : null;
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
     * Delivery status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Number of delivery attempts made.
     *
     * @return int|null
     */
    public function getAttemptNumber()
    {
        return isset($this->attributes['attempt_number']) ? $this->attributes['attempt_number'] : null;
    }
    /**
     * HTTP response code, if applicable.
     *
     * @return int|null
     */
    public function getResponseCode()
    {
        return isset($this->attributes['response_code']) ? $this->attributes['response_code'] : null;
    }
    /**
     * Delivery error message, if applicable.
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return isset($this->attributes['error_message']) ? $this->attributes['error_message'] : null;
    }
    /**
     * Delivery response body, if applicable.
     *
     * @return string|null
     */
    public function getResponseBody()
    {
        return isset($this->attributes['response_body']) ? $this->attributes['response_body'] : null;
    }
    /**
     * Delivery latency in milliseconds.
     *
     * @return int|null
     */
    public function getLatencyMs()
    {
        return isset($this->attributes['latency_ms']) ? $this->attributes['latency_ms'] : null;
    }
    /**
     * Successful delivery date/time.
     *
     * @return string|null
     */
    public function getDeliveredAt()
    {
        return isset($this->attributes['delivered_at']) ? $this->attributes['delivered_at'] : null;
    }
    /**
     * Most recent attempt date/time.
     *
     * @return string|null
     */
    public function getLastAttemptedAt()
    {
        return isset($this->attributes['last_attempted_at']) ? $this->attributes['last_attempted_at'] : null;
    }
    /**
     * Next scheduled attempt date/time.
     *
     * @return string|null
     */
    public function getNextAttemptAt()
    {
        return isset($this->attributes['next_attempt_at']) ? $this->attributes['next_attempt_at'] : null;
    }
    /**
     * Delivery Attempt create date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Event Delivery Attempts
     *
     * GET /event_delivery_attempts
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `status`, `event_record_id`, `event_target_id` or `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `status`, `workspace_id`, `event_record_id` or `event_target_id`. Valid field combinations are `[ workspace_id, status ]`, `[ workspace_id, event_record_id ]` or `[ workspace_id, event_target_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventDeliveryAttempt[]
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

        $response = Api::sendRequest('/event_delivery_attempts', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new EventDeliveryAttempt(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/event_delivery_attempts', $params, $options, EventDeliveryAttempt::class);
    }

    /**
     * Show Event Delivery Attempt
     *
     * GET /event_delivery_attempts/{id}
     *
     * @param int $id Event Delivery Attempt ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventDeliveryAttempt
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

        $response = Api::sendRequest('/event_delivery_attempts/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventDeliveryAttempt(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Event Delivery Attempt
     *
     * GET /event_delivery_attempts/{id}
     *
     * Alias of find().
     *
     * @param int $id Event Delivery Attempt ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventDeliveryAttempt
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
