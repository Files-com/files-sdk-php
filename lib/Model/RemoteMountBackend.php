<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Remote Mount Backend is used to provide high availability for a Remote Server Mount Folder Behavior.
 *
 * @property string|null $canary_file_path Path to the canary file used for health checks.
 * @property bool|null $enabled True if this backend is enabled.
 * @property int|null $fall Number of consecutive failures before considering the backend unhealthy.
 * @property bool|null $health_check_enabled True if health checks are enabled for this backend.
 * @property object[]|null $health_check_results Array of recent health check results.
 * @property string|null $health_check_type Type of health check to perform.
 * @property int|null $id Unique identifier for this backend.
 * @property int|null $interval Interval in seconds between health checks.
 * @property string|null $min_free_cpu Minimum free CPU percentage required for this backend to be considered healthy.
 * @property string|null $min_free_mem Minimum free memory percentage required for this backend to be considered healthy.
 * @property int|null $priority Priority of this backend.
 * @property string|null $remote_path Path on the remote server to treat as the root of this mount.
 * @property int|null $remote_server_id The remote server that this backend is associated with.
 * @property int|null $remote_server_mount_id The mount ID of the Remote Server Mount that this backend is associated with.
 * @property int|null $rise Number of consecutive successes before considering the backend healthy.
 * @property string|null $status Status of this backend.
 * @property bool|null $undergoing_maintenance True if this backend is undergoing maintenance.
 * @package Files
 */
class RemoteMountBackend
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
     * Path to the canary file used for health checks.
     *
     * @return string|null
     */
    public function getCanaryFilePath()
    {
        return isset($this->attributes['canary_file_path']) ? $this->attributes['canary_file_path'] : null;
    }

    /**
     * Path to the canary file used for health checks.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCanaryFilePath($value)
    {
        return $this->attributes['canary_file_path'] = $value;
    }
    /**
     * True if this backend is enabled.
     *
     * @return bool|null
     */
    public function getEnabled()
    {
        return isset($this->attributes['enabled']) ? $this->attributes['enabled'] : null;
    }

    /**
     * True if this backend is enabled.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    /**
     * Number of consecutive failures before considering the backend unhealthy.
     *
     * @return int|null
     */
    public function getFall()
    {
        return isset($this->attributes['fall']) ? $this->attributes['fall'] : null;
    }

    /**
     * Number of consecutive failures before considering the backend unhealthy.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFall($value)
    {
        return $this->attributes['fall'] = $value;
    }
    /**
     * True if health checks are enabled for this backend.
     *
     * @return bool|null
     */
    public function getHealthCheckEnabled()
    {
        return isset($this->attributes['health_check_enabled']) ? $this->attributes['health_check_enabled'] : null;
    }

    /**
     * True if health checks are enabled for this backend.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setHealthCheckEnabled($value)
    {
        return $this->attributes['health_check_enabled'] = $value;
    }
    /**
     * Array of recent health check results.
     *
     * @return object[]|null
     */
    public function getHealthCheckResults()
    {
        return isset($this->attributes['health_check_results']) ? $this->attributes['health_check_results'] : null;
    }

    /**
     * Array of recent health check results.
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setHealthCheckResults($value)
    {
        return $this->attributes['health_check_results'] = $value;
    }
    /**
     * Type of health check to perform.
     *
     * @return string|null
     */
    public function getHealthCheckType()
    {
        return isset($this->attributes['health_check_type']) ? $this->attributes['health_check_type'] : null;
    }

    /**
     * Type of health check to perform.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setHealthCheckType($value)
    {
        return $this->attributes['health_check_type'] = $value;
    }
    /**
     * Unique identifier for this backend.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Unique identifier for this backend.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Interval in seconds between health checks.
     *
     * @return int|null
     */
    public function getInterval()
    {
        return isset($this->attributes['interval']) ? $this->attributes['interval'] : null;
    }

    /**
     * Interval in seconds between health checks.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    /**
     * Minimum free CPU percentage required for this backend to be considered healthy.
     *
     * @return string|null
     */
    public function getMinFreeCpu()
    {
        return isset($this->attributes['min_free_cpu']) ? $this->attributes['min_free_cpu'] : null;
    }

    /**
     * Minimum free CPU percentage required for this backend to be considered healthy.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMinFreeCpu($value)
    {
        return $this->attributes['min_free_cpu'] = $value;
    }
    /**
     * Minimum free memory percentage required for this backend to be considered healthy.
     *
     * @return string|null
     */
    public function getMinFreeMem()
    {
        return isset($this->attributes['min_free_mem']) ? $this->attributes['min_free_mem'] : null;
    }

    /**
     * Minimum free memory percentage required for this backend to be considered healthy.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMinFreeMem($value)
    {
        return $this->attributes['min_free_mem'] = $value;
    }
    /**
     * Priority of this backend.
     *
     * @return int|null
     */
    public function getPriority()
    {
        return isset($this->attributes['priority']) ? $this->attributes['priority'] : null;
    }

    /**
     * Priority of this backend.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPriority($value)
    {
        return $this->attributes['priority'] = $value;
    }
    /**
     * Path on the remote server to treat as the root of this mount.
     *
     * @return string|null
     */
    public function getRemotePath()
    {
        return isset($this->attributes['remote_path']) ? $this->attributes['remote_path'] : null;
    }

    /**
     * Path on the remote server to treat as the root of this mount.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRemotePath($value)
    {
        return $this->attributes['remote_path'] = $value;
    }
    /**
     * The remote server that this backend is associated with.
     *
     * @return int|null
     */
    public function getRemoteServerId()
    {
        return isset($this->attributes['remote_server_id']) ? $this->attributes['remote_server_id'] : null;
    }

    /**
     * The remote server that this backend is associated with.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRemoteServerId($value)
    {
        return $this->attributes['remote_server_id'] = $value;
    }
    /**
     * The mount ID of the Remote Server Mount that this backend is associated with.
     *
     * @return int|null
     */
    public function getRemoteServerMountId()
    {
        return isset($this->attributes['remote_server_mount_id']) ? $this->attributes['remote_server_mount_id'] : null;
    }

    /**
     * The mount ID of the Remote Server Mount that this backend is associated with.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRemoteServerMountId($value)
    {
        return $this->attributes['remote_server_mount_id'] = $value;
    }
    /**
     * Number of consecutive successes before considering the backend healthy.
     *
     * @return int|null
     */
    public function getRise()
    {
        return isset($this->attributes['rise']) ? $this->attributes['rise'] : null;
    }

    /**
     * Number of consecutive successes before considering the backend healthy.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRise($value)
    {
        return $this->attributes['rise'] = $value;
    }
    /**
     * Status of this backend.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Status of this backend.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * True if this backend is undergoing maintenance.
     *
     * @return bool|null
     */
    public function getUndergoingMaintenance()
    {
        return isset($this->attributes['undergoing_maintenance']) ? $this->attributes['undergoing_maintenance'] : null;
    }

    /**
     * True if this backend is undergoing maintenance.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUndergoingMaintenance($value)
    {
        return $this->attributes['undergoing_maintenance'] = $value;
    }

    /**
     * Reset backend status to healthy
     *
     * POST /remote_mount_backends/{id}/reset_status
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Mount Backend ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function resetStatus($params = [])
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . rawurlencode(strval(@$params['id'])) . '/reset_status', 'POST', $params, $this->options);
        return;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // RemoteMountBackend with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Remote Mount Backend
     *
     * PATCH /remote_mount_backends/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Mount Backend ID.
     *     - `enabled` (bool, optional): True if this backend is enabled.
     *     - `fall` (int, optional): Number of consecutive failures before considering the backend unhealthy.
     *     - `health_check_enabled` (bool, optional): True if health checks are enabled for this backend.
     *     - `health_check_type` (string, optional): Type of health check to perform. Allowed values: active, passive.
     *     - `interval` (int, optional): Interval in seconds between health checks.
     *     - `min_free_cpu` (float, optional): Minimum free CPU percentage required for this backend to be considered healthy.
     *     - `min_free_mem` (float, optional): Minimum free memory percentage required for this backend to be considered healthy.
     *     - `priority` (int, optional): Priority of this backend.
     *     - `remote_path` (string, optional): Path on the remote server to treat as the root of this mount.
     *     - `rise` (int, optional): Number of consecutive successes before considering the backend healthy.
     *     - `canary_file_path` (string, optional): Path to the canary file used for health checks.
     *     - `remote_server_id` (int, optional): The remote server that this backend is associated with.
     * @return \Files\Model\RemoteMountBackend
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['enabled']) && !is_scalar($params['enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$enabled must be of type boolean; received ' . gettype(@$params['enabled']));
        }

        if (isset($params['fall']) && !is_int($params['fall'])) {
            throw new \Files\Exception\InvalidParameterException('$fall must be of type int; received ' . gettype(@$params['fall']));
        }

        if (isset($params['health_check_enabled']) && !is_scalar($params['health_check_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_enabled must be of type boolean; received ' . gettype(@$params['health_check_enabled']));
        }

        if (isset($params['health_check_type']) && !is_string($params['health_check_type'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_type must be of type string; received ' . gettype(@$params['health_check_type']));
        }

        if (isset($params['interval']) && !is_int($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type int; received ' . gettype(@$params['interval']));
        }

        if (isset($params['priority']) && !is_int($params['priority'])) {
            throw new \Files\Exception\InvalidParameterException('$priority must be of type int; received ' . gettype(@$params['priority']));
        }

        if (isset($params['remote_path']) && !is_string($params['remote_path'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_path must be of type string; received ' . gettype(@$params['remote_path']));
        }

        if (isset($params['rise']) && !is_int($params['rise'])) {
            throw new \Files\Exception\InvalidParameterException('$rise must be of type int; received ' . gettype(@$params['rise']));
        }

        if (isset($params['canary_file_path']) && !is_string($params['canary_file_path'])) {
            throw new \Files\Exception\InvalidParameterException('$canary_file_path must be of type string; received ' . gettype(@$params['canary_file_path']));
        }

        if (isset($params['remote_server_id']) && !is_int($params['remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_id must be of type int; received ' . gettype(@$params['remote_server_id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new RemoteMountBackend(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Remote Mount Backend
     *
     * DELETE /remote_mount_backends/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Mount Backend ID.
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Remote Mount Backend
     *
     * DELETE /remote_mount_backends/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Remote Mount Backend ID.
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
     * List Remote Mount Backends
     *
     * GET /remote_mount_backends
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `remote_server_mount_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteMountBackend[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_mount_backends', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new RemoteMountBackend(Api::modelAttributes($obj, $response->status), $options);
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
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/remote_mount_backends', $params, $options, RemoteMountBackend::class);
    }

    /**
     * Show Remote Mount Backend
     *
     * GET /remote_mount_backends/{id}
     *
     * @param int $id Remote Mount Backend ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteMountBackend
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

        $response = Api::sendRequest('/remote_mount_backends/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new RemoteMountBackend(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Remote Mount Backend
     *
     * GET /remote_mount_backends/{id}
     *
     * Alias of find().
     *
     * @param int $id Remote Mount Backend ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteMountBackend
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Remote Mount Backend
     *
     * POST /remote_mount_backends
     *
     * @param array $params Request parameters.
     *     - `enabled` (bool, optional): True if this backend is enabled.
     *     - `fall` (int, optional): Number of consecutive failures before considering the backend unhealthy.
     *     - `health_check_enabled` (bool, optional): True if health checks are enabled for this backend.
     *     - `health_check_type` (string, optional): Type of health check to perform. Allowed values: active, passive.
     *     - `interval` (int, optional): Interval in seconds between health checks.
     *     - `min_free_cpu` (float, optional): Minimum free CPU percentage required for this backend to be considered healthy.
     *     - `min_free_mem` (float, optional): Minimum free memory percentage required for this backend to be considered healthy.
     *     - `priority` (int, optional): Priority of this backend.
     *     - `remote_path` (string, optional): Path on the remote server to treat as the root of this mount.
     *     - `rise` (int, optional): Number of consecutive successes before considering the backend healthy.
     *     - `canary_file_path` (string, required): Path to the canary file used for health checks.
     *     - `remote_server_mount_id` (int, required): The mount ID of the Remote Server Mount that this backend is associated with.
     *     - `remote_server_id` (int, required): The remote server that this backend is associated with.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\RemoteMountBackend
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['canary_file_path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: canary_file_path');
        }

        if (empty($params['remote_server_mount_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_mount_id');
        }

        if (empty($params['remote_server_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_id');
        }

        if (isset($params['enabled']) && !is_scalar($params['enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$enabled must be of type boolean; received ' . gettype(@$params['enabled']));
        }

        if (isset($params['fall']) && !is_int($params['fall'])) {
            throw new \Files\Exception\InvalidParameterException('$fall must be of type int; received ' . gettype(@$params['fall']));
        }

        if (isset($params['health_check_enabled']) && !is_scalar($params['health_check_enabled'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_enabled must be of type boolean; received ' . gettype(@$params['health_check_enabled']));
        }

        if (isset($params['health_check_type']) && !is_string($params['health_check_type'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_type must be of type string; received ' . gettype(@$params['health_check_type']));
        }

        if (isset($params['interval']) && !is_int($params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type int; received ' . gettype(@$params['interval']));
        }

        if (isset($params['priority']) && !is_int($params['priority'])) {
            throw new \Files\Exception\InvalidParameterException('$priority must be of type int; received ' . gettype(@$params['priority']));
        }

        if (isset($params['remote_path']) && !is_string($params['remote_path'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_path must be of type string; received ' . gettype(@$params['remote_path']));
        }

        if (isset($params['rise']) && !is_int($params['rise'])) {
            throw new \Files\Exception\InvalidParameterException('$rise must be of type int; received ' . gettype(@$params['rise']));
        }

        if (isset($params['canary_file_path']) && !is_string($params['canary_file_path'])) {
            throw new \Files\Exception\InvalidParameterException('$canary_file_path must be of type string; received ' . gettype(@$params['canary_file_path']));
        }

        if (isset($params['remote_server_mount_id']) && !is_int($params['remote_server_mount_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_mount_id must be of type int; received ' . gettype(@$params['remote_server_mount_id']));
        }

        if (isset($params['remote_server_id']) && !is_int($params['remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_id must be of type int; received ' . gettype(@$params['remote_server_id']));
        }

        $response = Api::sendRequest('/remote_mount_backends', 'POST', $params, $options);

        return new RemoteMountBackend(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
