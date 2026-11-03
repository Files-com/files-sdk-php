<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An ActionNotificationExport is an operation that provides access to outgoing webhook logs.
 *
 * @property int|null $id History Export ID
 * @property int|null $workspace_id Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
 * @property string|null $export_version Version of the underlying records for the export.
 * @property string|null $start_at Start date/time of export range.
 * @property string|null $end_at End date/time of export range.
 * @property string|null $status Status of export. Valid values: `building`, `ready`, or `failed`
 * @property string|null $query_path Return notifications that were triggered by actions on this specific path.
 * @property string|null $query_folder Return notifications that were triggered by actions in this folder.
 * @property string|null $query_message Error message associated with the request, if any.
 * @property string|null $query_request_method The HTTP request method used by the webhook.
 * @property string|null $query_request_url The target webhook URL.
 * @property string|null $query_status The HTTP status returned from the server in response to the webhook request.
 * @property bool|null $query_success true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
 * @property string|null $results_url If `status` is `ready`, this will be a URL where all the results can be downloaded at once as a CSV.
 * @property int|null $user_id User ID. Provide a value of `0` to operate the current session's user.
 * @package Files
 */
class ActionNotificationExport
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
     * History Export ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * History Export ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Version of the underlying records for the export.
     *
     * @return string|null
     */
    public function getExportVersion()
    {
        return isset($this->attributes['export_version']) ? $this->attributes['export_version'] : null;
    }

    /**
     * Version of the underlying records for the export.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExportVersion($value)
    {
        return $this->attributes['export_version'] = $value;
    }
    /**
     * Start date/time of export range.
     *
     * @return string|null
     */
    public function getStartAt()
    {
        return isset($this->attributes['start_at']) ? $this->attributes['start_at'] : null;
    }

    /**
     * Start date/time of export range.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStartAt($value)
    {
        return $this->attributes['start_at'] = $value;
    }
    /**
     * End date/time of export range.
     *
     * @return string|null
     */
    public function getEndAt()
    {
        return isset($this->attributes['end_at']) ? $this->attributes['end_at'] : null;
    }

    /**
     * End date/time of export range.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEndAt($value)
    {
        return $this->attributes['end_at'] = $value;
    }
    /**
     * Status of export.  Valid values: `building`, `ready`, or `failed`
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Status of export.  Valid values: `building`, `ready`, or `failed`
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * Return notifications that were triggered by actions on this specific path.
     *
     * @return string|null
     */
    public function getQueryPath()
    {
        return isset($this->attributes['query_path']) ? $this->attributes['query_path'] : null;
    }

    /**
     * Return notifications that were triggered by actions on this specific path.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryPath($value)
    {
        return $this->attributes['query_path'] = $value;
    }
    /**
     * Return notifications that were triggered by actions in this folder.
     *
     * @return string|null
     */
    public function getQueryFolder()
    {
        return isset($this->attributes['query_folder']) ? $this->attributes['query_folder'] : null;
    }

    /**
     * Return notifications that were triggered by actions in this folder.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryFolder($value)
    {
        return $this->attributes['query_folder'] = $value;
    }
    /**
     * Error message associated with the request, if any.
     *
     * @return string|null
     */
    public function getQueryMessage()
    {
        return isset($this->attributes['query_message']) ? $this->attributes['query_message'] : null;
    }

    /**
     * Error message associated with the request, if any.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryMessage($value)
    {
        return $this->attributes['query_message'] = $value;
    }
    /**
     * The HTTP request method used by the webhook.
     *
     * @return string|null
     */
    public function getQueryRequestMethod()
    {
        return isset($this->attributes['query_request_method']) ? $this->attributes['query_request_method'] : null;
    }

    /**
     * The HTTP request method used by the webhook.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryRequestMethod($value)
    {
        return $this->attributes['query_request_method'] = $value;
    }
    /**
     * The target webhook URL.
     *
     * @return string|null
     */
    public function getQueryRequestUrl()
    {
        return isset($this->attributes['query_request_url']) ? $this->attributes['query_request_url'] : null;
    }

    /**
     * The target webhook URL.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryRequestUrl($value)
    {
        return $this->attributes['query_request_url'] = $value;
    }
    /**
     * The HTTP status returned from the server in response to the webhook request.
     *
     * @return string|null
     */
    public function getQueryStatus()
    {
        return isset($this->attributes['query_status']) ? $this->attributes['query_status'] : null;
    }

    /**
     * The HTTP status returned from the server in response to the webhook request.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setQueryStatus($value)
    {
        return $this->attributes['query_status'] = $value;
    }
    /**
     * true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
     *
     * @return bool|null
     */
    public function getQuerySuccess()
    {
        return isset($this->attributes['query_success']) ? $this->attributes['query_success'] : null;
    }

    /**
     * true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setQuerySuccess($value)
    {
        return $this->attributes['query_success'] = $value;
    }
    /**
     * If `status` is `ready`, this will be a URL where all the results can be downloaded at once as a CSV.
     *
     * @return string|null
     */
    public function getResultsUrl()
    {
        return isset($this->attributes['results_url']) ? $this->attributes['results_url'] : null;
    }

    /**
     * If `status` is `ready`, this will be a URL where all the results can be downloaded at once as a CSV.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setResultsUrl($value)
    {
        return $this->attributes['results_url'] = $value;
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
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The ActionNotificationExport object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * Show Action Notification Export
     *
     * GET /action_notification_exports/{id}
     *
     * @param int $id Action Notification Export ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ActionNotificationExport
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

        $response = Api::sendRequest('/action_notification_exports/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ActionNotificationExport(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Action Notification Export
     *
     * GET /action_notification_exports/{id}
     *
     * Alias of find().
     *
     * @param int $id Action Notification Export ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ActionNotificationExport
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Action Notification Export
     *
     * POST /action_notification_exports
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `workspace_id` (int, optional): Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
     *     - `start_at` (string, optional): Start date/time of export range.
     *     - `end_at` (string, optional): End date/time of export range.
     *     - `query_message` (string, optional): Error message associated with the request, if any.
     *     - `query_request_method` (string, optional): The HTTP request method used by the webhook.
     *     - `query_request_url` (string, optional): The target webhook URL.
     *     - `query_status` (string, optional): The HTTP status returned from the server in response to the webhook request.
     *     - `query_success` (bool, optional): true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
     *     - `query_path` (string, optional): Return notifications that were triggered by actions on this specific path.
     *     - `query_folder` (string, optional): Return notifications that were triggered by actions in this folder.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ActionNotificationExport
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (isset($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (isset($params['query_message']) && !is_string($params['query_message'])) {
            throw new \Files\Exception\InvalidParameterException('$query_message must be of type string; received ' . gettype(@$params['query_message']));
        }

        if (isset($params['query_request_method']) && !is_string($params['query_request_method'])) {
            throw new \Files\Exception\InvalidParameterException('$query_request_method must be of type string; received ' . gettype(@$params['query_request_method']));
        }

        if (isset($params['query_request_url']) && !is_string($params['query_request_url'])) {
            throw new \Files\Exception\InvalidParameterException('$query_request_url must be of type string; received ' . gettype(@$params['query_request_url']));
        }

        if (isset($params['query_status']) && !is_string($params['query_status'])) {
            throw new \Files\Exception\InvalidParameterException('$query_status must be of type string; received ' . gettype(@$params['query_status']));
        }

        if (isset($params['query_success']) && !is_scalar($params['query_success'])) {
            throw new \Files\Exception\InvalidParameterException('$query_success must be of type boolean; received ' . gettype(@$params['query_success']));
        }

        if (isset($params['query_path']) && !is_string($params['query_path'])) {
            throw new \Files\Exception\InvalidParameterException('$query_path must be of type string; received ' . gettype(@$params['query_path']));
        }

        if (isset($params['query_folder']) && !is_string($params['query_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$query_folder must be of type string; received ' . gettype(@$params['query_folder']));
        }

        $response = Api::sendRequest('/action_notification_exports', 'POST', $params, $options);

        return new ActionNotificationExport(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
