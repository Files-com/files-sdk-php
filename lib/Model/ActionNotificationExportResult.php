<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ActionNotificationExportResult
 *
 * @package Files
 */
class ActionNotificationExportResult
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // int64 # Notification ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # When the notification was sent.
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // int64 # HTTP status code returned in the webhook response.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // string # A message indicating the overall status of the webhook notification.
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    // boolean # `true` if the webhook succeeded by receiving a 200 or 204 response.
    public function getSuccess()
    {
        return isset($this->attributes['success']) ? $this->attributes['success'] : null;
    }
    // string # A JSON-encoded string with headers that were sent with the webhook.
    public function getRequestHeaders()
    {
        return isset($this->attributes['request_headers']) ? $this->attributes['request_headers'] : null;
    }
    // string # The HTTP verb used to perform the webhook.
    public function getRequestMethod()
    {
        return isset($this->attributes['request_method']) ? $this->attributes['request_method'] : null;
    }
    // string # The webhook request URL.
    public function getRequestUrl()
    {
        return isset($this->attributes['request_url']) ? $this->attributes['request_url'] : null;
    }
    // string # The path to the actual file that triggered this notification. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    // string # The folder associated with the triggering action for this notification.
    public function getFolder()
    {
        return isset($this->attributes['folder']) ? $this->attributes['folder'] : null;
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   action_notification_export_id (required) - int64 - ID of the associated action notification export.
    public static function all($params = [], $options = [])
    {
        if (empty($params['action_notification_export_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: action_notification_export_id');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['action_notification_export_id']) && !is_int($params['action_notification_export_id'])) {
            throw new \Files\Exception\InvalidParameterException('$action_notification_export_id must be of type int; received ' . gettype(@$params['action_notification_export_id']));
        }

        $response = Api::sendRequest('/action_notification_export_results', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ActionNotificationExportResult((array) $obj, $options);
        }

        return $return_array;
    }
}
