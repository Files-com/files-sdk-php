<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A BundleAction is an action performed on a bundle.
 *
 * @property string|null $action Type of action
 * @property \stdClass|null $bundle_registration Object that contains bundle registration information
 * @property string|null $created_at Action occurrence date/time
 * @property string|null $destination The destination path for this bundle action, if applicable
 * @property string|null $path Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $source The source path for this bundle action, if applicable
 * @property int|null $bundle_registration_id Identifier of the associated bundle registration
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $bundle_registration_name Name of the registrant who performed the action
 * @property string|null $bundle_registration_email Email of the registrant
 * @property string|null $bundle_registration_ip IP address of the registrant
 * @property int|null $form_field_set_id Id of associated form field set
 * @property object|null $form_field_data Data for form field set with form field ids as keys and user data as values
 * @package Files
 */
class BundleAction
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
     * Type of action
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * Object that contains bundle registration information
     *
     * @return \stdClass|null
     */
    public function getBundleRegistration()
    {
        return isset($this->attributes['bundle_registration']) ? $this->attributes['bundle_registration'] : null;
    }
    /**
     * Action occurrence date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * The destination path for this bundle action, if applicable
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    /**
     * Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * The source path for this bundle action, if applicable
     *
     * @return string|null
     */
    public function getSource()
    {
        return isset($this->attributes['source']) ? $this->attributes['source'] : null;
    }
    /**
     * Identifier of the associated bundle registration
     *
     * @return int|null
     */
    public function getBundleRegistrationId()
    {
        return isset($this->attributes['bundle_registration_id']) ? $this->attributes['bundle_registration_id'] : null;
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
     * Name of the registrant who performed the action
     *
     * @return string|null
     */
    public function getBundleRegistrationName()
    {
        return isset($this->attributes['bundle_registration_name']) ? $this->attributes['bundle_registration_name'] : null;
    }
    /**
     * Email of the registrant
     *
     * @return string|null
     */
    public function getBundleRegistrationEmail()
    {
        return isset($this->attributes['bundle_registration_email']) ? $this->attributes['bundle_registration_email'] : null;
    }
    /**
     * IP address of the registrant
     *
     * @return string|null
     */
    public function getBundleRegistrationIp()
    {
        return isset($this->attributes['bundle_registration_ip']) ? $this->attributes['bundle_registration_ip'] : null;
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
     * List Bundle Actions
     *
     * GET /bundle_actions
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `bundle_registration_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `bundle_id` or `bundle_registration_id`. Valid field combinations are `[ bundle_id, created_at ]`, `[ bundle_registration_id, created_at ]`, `[ bundle_id, bundle_registration_id ]` or `[ bundle_id, bundle_registration_id, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     *     - `bundle_id` (int, optional): Bundle ID
     *     - `bundle_registration_id` (int, optional): Bundle Registration ID
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleAction[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['bundle_id']) && !is_int($params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        if (isset($params['bundle_registration_id']) && !is_int($params['bundle_registration_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_id must be of type int; received ' . gettype(@$params['bundle_registration_id']));
        }

        $response = Api::sendRequest('/bundle_actions', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new BundleAction(Api::modelAttributes($obj, $response->status), $options);
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
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['bundle_id']) && !is_int($params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        if (isset($params['bundle_registration_id']) && !is_int($params['bundle_registration_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_id must be of type int; received ' . gettype(@$params['bundle_registration_id']));
        }

        return \Files\Page::fetch('/bundle_actions', $params, $options, BundleAction::class);
    }
}
