<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EmailIncomingMessage is an audit log designed to track status of email to inbox uploads.
 *
 * @property int|null $id Id of the Email Incoming Message
 * @property int|null $inbox_id Id of the Inbox associated with this message
 * @property string|null $sender Sender of the email
 * @property string|null $sender_name Sender name
 * @property string|null $status Status of the message
 * @property string|null $body Body of the email
 * @property string|null $message Message describing the failure
 * @property string|null $created_at Message creation date/time
 * @property string|null $inbox_title Title of the Inbox associated with this message
 * @package Files
 */
class EmailIncomingMessage
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
     * Id of the Email Incoming Message
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Id of the Inbox associated with this message
     *
     * @return int|null
     */
    public function getInboxId()
    {
        return isset($this->attributes['inbox_id']) ? $this->attributes['inbox_id'] : null;
    }
    /**
     * Sender of the email
     *
     * @return string|null
     */
    public function getSender()
    {
        return isset($this->attributes['sender']) ? $this->attributes['sender'] : null;
    }
    /**
     * Sender name
     *
     * @return string|null
     */
    public function getSenderName()
    {
        return isset($this->attributes['sender_name']) ? $this->attributes['sender_name'] : null;
    }
    /**
     * Status of the message
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Body of the email
     *
     * @return string|null
     */
    public function getBody()
    {
        return isset($this->attributes['body']) ? $this->attributes['body'] : null;
    }
    /**
     * Message describing the failure
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * Message creation date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Title of the Inbox associated with this message
     *
     * @return string|null
     */
    public function getInboxTitle()
    {
        return isset($this->attributes['inbox_title']) ? $this->attributes['inbox_title'] : null;
    }

    /**
     * List Email Incoming Messages
     *
     * GET /email_incoming_messages
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `sender`, `status` or `inbox_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `inbox_id`, `sender` or `status`. Valid field combinations are `[ inbox_id, created_at ]`, `[ sender, created_at ]`, `[ status, created_at ]`, `[ inbox_id, status ]`, `[ status, sender ]`, `[ inbox_id, status, created_at ]`, `[ status, sender, created_at ]`, `[ inbox_id, status, sender ]` or `[ inbox_id, status, sender, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `sender`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EmailIncomingMessage[]
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

        $response = Api::sendRequest('/email_incoming_messages', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EmailIncomingMessage((array) $obj, $options);
        }

        return $return_array;
    }
}
