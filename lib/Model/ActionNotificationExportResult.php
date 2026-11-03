<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An ActionNotificationExportResult is a single record containing webhook log information that can be obtained as a part of Action Notification Export request.
 *
 * @property int|null $id Notification ID
 * @property int|null $created_at When the notification was sent.
 * @property int|null $status HTTP status code returned in the webhook response.
 * @property string|null $message A message indicating the overall status of the webhook notification.
 * @property bool|null $success `true` if the webhook succeeded by receiving a 200 or 204 response.
 * @property string|null $request_headers A JSON-encoded string with headers that were sent with the webhook.
 * @property string|null $request_method The HTTP verb used to perform the webhook.
 * @property string|null $request_url The webhook request URL.
 * @property string|null $path The path to the actual file that triggered this notification. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $folder The folder associated with the triggering action for this notification.
 * @package Files
 */
class ActionNotificationExportResult
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
     * Notification ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * When the notification was sent.
     *
     * @return int|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * HTTP status code returned in the webhook response.
     *
     * @return int|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * A message indicating the overall status of the webhook notification.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * `true` if the webhook succeeded by receiving a 200 or 204 response.
     *
     * @return bool|null
     */
    public function getSuccess()
    {
        return isset($this->attributes['success']) ? $this->attributes['success'] : null;
    }
    /**
     * A JSON-encoded string with headers that were sent with the webhook.
     *
     * @return string|null
     */
    public function getRequestHeaders()
    {
        return isset($this->attributes['request_headers']) ? $this->attributes['request_headers'] : null;
    }
    /**
     * The HTTP verb used to perform the webhook.
     *
     * @return string|null
     */
    public function getRequestMethod()
    {
        return isset($this->attributes['request_method']) ? $this->attributes['request_method'] : null;
    }
    /**
     * The webhook request URL.
     *
     * @return string|null
     */
    public function getRequestUrl()
    {
        return isset($this->attributes['request_url']) ? $this->attributes['request_url'] : null;
    }
    /**
     * The path to the actual file that triggered this notification. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * The folder associated with the triggering action for this notification.
     *
     * @return string|null
     */
    public function getFolder()
    {
        return isset($this->attributes['folder']) ? $this->attributes['folder'] : null;
    }

    /**
     * List Action Notification Export Results
     *
     * GET /action_notification_export_results
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `action_notification_export_id` (int, required): ID of the associated action notification export.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ActionNotificationExportResult[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (empty($params['action_notification_export_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: action_notification_export_id');
        }

        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['action_notification_export_id']) && !is_int($params['action_notification_export_id'])) {
            throw new \Files\Exception\InvalidParameterException('$action_notification_export_id must be of type int; received ' . gettype(@$params['action_notification_export_id']));
        }

        $response = Api::sendRequest('/action_notification_export_results', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new ActionNotificationExportResult(Api::modelAttributes($obj, $response->status), $options);
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
        if (empty($params['action_notification_export_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: action_notification_export_id');
        }

        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['action_notification_export_id']) && !is_int($params['action_notification_export_id'])) {
            throw new \Files\Exception\InvalidParameterException('$action_notification_export_id must be of type int; received ' . gettype(@$params['action_notification_export_id']));
        }

        return \Files\Page::fetch('/action_notification_export_results', $params, $options, ActionNotificationExportResult::class);
    }
}
