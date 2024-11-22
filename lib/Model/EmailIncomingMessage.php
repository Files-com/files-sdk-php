<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class EmailIncomingMessage
 *
 * @package Files
 */
class EmailIncomingMessage
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
    // int64 # Id of the Email Incoming Message
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # Id of the Inbox associated with this message
    public function getInboxId()
    {
        return @$this->attributes['inbox_id'];
    }
    // string # Sender of the email
    public function getSender()
    {
        return @$this->attributes['sender'];
    }
    // string # Sender name
    public function getSenderName()
    {
        return @$this->attributes['sender_name'];
    }
    // string # Status of the message
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Body of the email
    public function getBody()
    {
        return @$this->attributes['body'];
    }
    // string # Message describing the failure
    public function getMessage()
    {
        return @$this->attributes['message'];
    }
    // date-time # Message creation date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # Title of the Inbox associated with this message
    public function getInboxTitle()
    {
        return @$this->attributes['inbox_title'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `sender`, `status` or `inbox_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `inbox_id`, `sender` or `status`. Valid field combinations are `[ created_at, inbox_id ]`, `[ created_at, sender ]`, `[ created_at, status ]`, `[ inbox_id, status ]`, `[ created_at, inbox_id, status ]`, `[ inbox_id, sender, status ]` or `[ created_at, inbox_id, sender, status ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `sender`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
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
