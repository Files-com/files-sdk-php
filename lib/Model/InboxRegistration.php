<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An InboxRegistration is created when a user fills out the form to access the inbox.
 *
 * @property string|null $code Registration cookie code
 * @property string|null $name Registrant name
 * @property string|null $company Registrant company name
 * @property string|null $email Registrant email address
 * @property string|null $ip Registrant IP Address
 * @property string|null $clickwrap_body Clickwrap text that was shown to the registrant
 * @property int|null $form_field_set_id Id of associated form field set
 * @property object|null $form_field_data Data for form field set with form field ids as keys and user data as values
 * @property int|null $inbox_id Id of associated inbox
 * @property int|null $inbox_recipient_id Id of associated inbox recipient
 * @property string|null $inbox_title Title of associated inbox
 * @property string|null $created_at Registration creation date/time
 * @package Files
 */
class InboxRegistration
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
     * Registration cookie code
     *
     * @return string|null
     */
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }
    /**
     * Registrant name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * Registrant company name
     *
     * @return string|null
     */
    public function getCompany()
    {
        return isset($this->attributes['company']) ? $this->attributes['company'] : null;
    }
    /**
     * Registrant email address
     *
     * @return string|null
     */
    public function getEmail()
    {
        return isset($this->attributes['email']) ? $this->attributes['email'] : null;
    }
    /**
     * Registrant IP Address
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * Clickwrap text that was shown to the registrant
     *
     * @return string|null
     */
    public function getClickwrapBody()
    {
        return isset($this->attributes['clickwrap_body']) ? $this->attributes['clickwrap_body'] : null;
    }
    /**
     * Id of associated form field set
     *
     * @return int|null
     */
    public function getFormFieldSetId()
    {
        return isset($this->attributes['form_field_set_id']) ? $this->attributes['form_field_set_id'] : null;
    }
    /**
     * Data for form field set with form field ids as keys and user data as values
     *
     * @return object|null
     */
    public function getFormFieldData()
    {
        return isset($this->attributes['form_field_data']) ? $this->attributes['form_field_data'] : null;
    }
    /**
     * Id of associated inbox
     *
     * @return int|null
     */
    public function getInboxId()
    {
        return isset($this->attributes['inbox_id']) ? $this->attributes['inbox_id'] : null;
    }
    /**
     * Id of associated inbox recipient
     *
     * @return int|null
     */
    public function getInboxRecipientId()
    {
        return isset($this->attributes['inbox_recipient_id']) ? $this->attributes['inbox_recipient_id'] : null;
    }
    /**
     * Title of associated inbox
     *
     * @return string|null
     */
    public function getInboxTitle()
    {
        return isset($this->attributes['inbox_title']) ? $this->attributes['inbox_title'] : null;
    }
    /**
     * Registration creation date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Inbox Registrations
     *
     * GET /inbox_registrations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `folder_behavior_id` (int, optional): ID of the associated Inbox. This is required if the user is not a site admin.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\InboxRegistration[]
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
