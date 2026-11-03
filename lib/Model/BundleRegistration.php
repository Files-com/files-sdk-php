<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A BundleRegistration is a registration record when a user fills out the form to access the bundle.
 *
 * @property string|null $code Registration cookie code
 * @property string|null $name Registrant name
 * @property string|null $company Registrant company name
 * @property string|null $email Registrant email address
 * @property string|null $ip Registrant IP Address
 * @property string|null $inbox_code InboxRegistration cookie code, if there is an associated InboxRegistration
 * @property string|null $clickwrap_body Clickwrap text that was shown to the registrant
 * @property int|null $form_field_set_id Id of associated form field set
 * @property object|null $form_field_data Data for form field set with form field ids as keys and user data as values
 * @property string|null $bundle_code Bundle URL code
 * @property int|null $bundle_id Id of associated bundle
 * @property int|null $bundle_recipient_id Id of associated bundle recipient
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $created_at Registration creation date/time
 * @package Files
 */
class BundleRegistration
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
     * InboxRegistration cookie code, if there is an associated InboxRegistration
     *
     * @return string|null
     */
    public function getInboxCode()
    {
        return isset($this->attributes['inbox_code']) ? $this->attributes['inbox_code'] : null;
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
     * Bundle URL code
     *
     * @return string|null
     */
    public function getBundleCode()
    {
        return isset($this->attributes['bundle_code']) ? $this->attributes['bundle_code'] : null;
    }
    /**
     * Id of associated bundle
     *
     * @return int|null
     */
    public function getBundleId()
    {
        return isset($this->attributes['bundle_id']) ? $this->attributes['bundle_id'] : null;
    }
    /**
     * Id of associated bundle recipient
     *
     * @return int|null
     */
    public function getBundleRecipientId()
    {
        return isset($this->attributes['bundle_recipient_id']) ? $this->attributes['bundle_recipient_id'] : null;
    }
    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
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
     * List Share Link Registrations
     *
     * GET /bundle_registrations
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `bundle_id` or `created_at`.
     *     - `bundle_id` (int, optional): ID of the associated Bundle
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleRegistration[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['bundle_id']) && !is_int($params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        $response = Api::sendRequest('/bundle_registrations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new BundleRegistration((array) $obj, $options);
        }

        return $return_array;
    }
}
