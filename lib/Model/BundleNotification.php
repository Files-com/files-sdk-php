<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A BundleNotification is an E-Mail sent out to users when certain actions are performed on or within a shared set of files and folders.
 *
 * @property int|null $bundle_id Bundle ID to notify on
 * @property int|null $id Bundle Notification ID
 * @property bool|null $notify_on_registration Triggers bundle notification when a registration action occurs for it.
 * @property bool|null $notify_on_upload Triggers bundle notification when a upload action occurs for it.
 * @property bool|null $notify_current_user Is the current user the user to notify?
 * @property int|null $notify_user_id The id of the user to notify.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property int|null $user_id User ID. Provide a value of `0` to operate the current session's user.
 * @package Files
 */
class BundleNotification
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
     * Bundle ID to notify on
     *
     * @return int|null
     */
    public function getBundleId()
    {
        return isset($this->attributes['bundle_id']) ? $this->attributes['bundle_id'] : null;
    }

    /**
     * Bundle ID to notify on
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setBundleId($value)
    {
        return $this->attributes['bundle_id'] = $value;
    }
    /**
     * Bundle Notification ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Bundle Notification ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Triggers bundle notification when a registration action occurs for it.
     *
     * @return bool|null
     */
    public function getNotifyOnRegistration()
    {
        return isset($this->attributes['notify_on_registration']) ? $this->attributes['notify_on_registration'] : null;
    }

    /**
     * Triggers bundle notification when a registration action occurs for it.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnRegistration($value)
    {
        return $this->attributes['notify_on_registration'] = $value;
    }
    /**
     * Triggers bundle notification when a upload action occurs for it.
     *
     * @return bool|null
     */
    public function getNotifyOnUpload()
    {
        return isset($this->attributes['notify_on_upload']) ? $this->attributes['notify_on_upload'] : null;
    }

    /**
     * Triggers bundle notification when a upload action occurs for it.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnUpload($value)
    {
        return $this->attributes['notify_on_upload'] = $value;
    }
    /**
     * Is the current user the user to notify?
     *
     * @return bool|null
     */
    public function getNotifyCurrentUser()
    {
        return isset($this->attributes['notify_current_user']) ? $this->attributes['notify_current_user'] : null;
    }

    /**
     * Is the current user the user to notify?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyCurrentUser($value)
    {
        return $this->attributes['notify_current_user'] = $value;
    }
    /**
     * The id of the user to notify.
     *
     * @return int|null
     */
    public function getNotifyUserId()
    {
        return isset($this->attributes['notify_user_id']) ? $this->attributes['notify_user_id'] : null;
    }

    /**
     * The id of the user to notify.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setNotifyUserId($value)
    {
        return $this->attributes['notify_user_id'] = $value;
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
     * Workspace ID. `0` means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }

    /**
     * Update Share Link Notification
     *
     * PATCH /bundle_notifications/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle Notification ID.
     *     - `notify_on_registration` (bool, optional): Triggers bundle notification when a registration action occurs for it.
     *     - `notify_on_upload` (bool, optional): Triggers bundle notification when a upload action occurs for it.
     * @return \Files\Model\BundleNotification
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

        $response = Api::sendRequest('/bundle_notifications/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new BundleNotification((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Share Link Notification
     *
     * DELETE /bundle_notifications/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle Notification ID.
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

        $response = Api::sendRequest('/bundle_notifications/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Share Link Notification
     *
     * DELETE /bundle_notifications/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle Notification ID.
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
     * List Share Link Notifications
     *
     * GET /bundle_notifications
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `bundle_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `bundle_id`.
     *     - `bundle_id` (int, optional): Bundle ID
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleNotification[]
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

        $response = Api::sendRequest('/bundle_notifications', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new BundleNotification((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Share Link Notification
     *
     * GET /bundle_notifications/{id}
     *
     * @param int $id Bundle Notification ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleNotification
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

        $response = Api::sendRequest('/bundle_notifications/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new BundleNotification((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Share Link Notification
     *
     * GET /bundle_notifications/{id}
     *
     * Alias of find().
     *
     * @param int $id Bundle Notification ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleNotification
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Share Link Notification
     *
     * POST /bundle_notifications
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `bundle_id` (int, required): Bundle ID to notify on
     *     - `notify_user_id` (int, optional): The id of the user to notify.
     *     - `notify_on_registration` (bool, optional): Triggers bundle notification when a registration action occurs for it.
     *     - `notify_on_upload` (bool, optional): Triggers bundle notification when a upload action occurs for it.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\BundleNotification
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['bundle_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: bundle_id');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['bundle_id']) && !is_int($params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        if (!empty($params['notify_user_id']) && !is_int($params['notify_user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_user_id must be of type int; received ' . gettype(@$params['notify_user_id']));
        }

        $response = Api::sendRequest('/bundle_notifications', 'POST', $params, $options);

        return new BundleNotification((array) (@$response->data ?: []), $options);
    }
}
