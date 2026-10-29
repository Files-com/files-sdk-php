<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class InboxRegistration
 *
 * @package Files
 */
class InboxRegistration
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
    // string # Registration cookie code
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }
    // string # Registrant name
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    // string # Registrant company name
    public function getCompany()
    {
        return isset($this->attributes['company']) ? $this->attributes['company'] : null;
    }
    // string # Registrant email address
    public function getEmail()
    {
        return isset($this->attributes['email']) ? $this->attributes['email'] : null;
    }
    // string # Registrant IP Address
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    // string # Clickwrap text that was shown to the registrant
    public function getClickwrapBody()
    {
        return isset($this->attributes['clickwrap_body']) ? $this->attributes['clickwrap_body'] : null;
    }
    // int64 # Id of associated form field set
    public function getFormFieldSetId()
    {
        return isset($this->attributes['form_field_set_id']) ? $this->attributes['form_field_set_id'] : null;
    }
    // object # Data for form field set with form field ids as keys and user data as values
    public function getFormFieldData()
    {
        return isset($this->attributes['form_field_data']) ? $this->attributes['form_field_data'] : null;
    }
    // int64 # Id of associated inbox
    public function getInboxId()
    {
        return isset($this->attributes['inbox_id']) ? $this->attributes['inbox_id'] : null;
    }
    // int64 # Id of associated inbox recipient
    public function getInboxRecipientId()
    {
        return isset($this->attributes['inbox_recipient_id']) ? $this->attributes['inbox_recipient_id'] : null;
    }
    // string # Title of associated inbox
    public function getInboxTitle()
    {
        return isset($this->attributes['inbox_title']) ? $this->attributes['inbox_title'] : null;
    }
    // date-time # Registration creation date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   folder_behavior_id - int64 - ID of the associated Inbox. This is required if the user is not a site admin.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['folder_behavior_id']) && !is_int($params['folder_behavior_id'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype(@$params['folder_behavior_id']));
        }

        $response = Api::sendRequest('/inbox_registrations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new InboxRegistration((array) $obj, $options);
        }

        return $return_array;
    }
}
