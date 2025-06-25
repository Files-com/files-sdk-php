<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteMountBackend
 *
 * @package Files
 */
class RemoteMountBackend
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
    // string # Path to the canary file used for health checks.
    public function getCanaryFilePath()
    {
        return @$this->attributes['canary_file_path'];
    }

    public function setCanaryFilePath($value)
    {
        return $this->attributes['canary_file_path'] = $value;
    }
    // boolean # True if this backend is enabled.
    public function getEnabled()
    {
        return @$this->attributes['enabled'];
    }

    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    // int64 # Number of consecutive failures before considering the backend unhealthy.
    public function getFall()
    {
        return @$this->attributes['fall'];
    }

    public function setFall($value)
    {
        return $this->attributes['fall'] = $value;
    }
    // boolean # True if health checks are enabled for this backend.
    public function getHealthCheckEnabled()
    {
        return @$this->attributes['health_check_enabled'];
    }

    public function setHealthCheckEnabled($value)
    {
        return $this->attributes['health_check_enabled'] = $value;
    }
    // string # Type of health check to perform.
    public function getHealthCheckType()
    {
        return @$this->attributes['health_check_type'];
    }

    public function setHealthCheckType($value)
    {
        return $this->attributes['health_check_type'] = $value;
    }
    // int64 # Unique identifier for this backend.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Interval in seconds between health checks.
    public function getInterval()
    {
        return @$this->attributes['interval'];
    }

    public function setInterval($value)
    {
        return $this->attributes['interval'] = $value;
    }
    // double # Minimum free CPU percentage required for this backend to be considered healthy.
    public function getMinFreeCpu()
    {
        return @$this->attributes['min_free_cpu'];
    }

    public function setMinFreeCpu($value)
    {
        return $this->attributes['min_free_cpu'] = $value;
    }
    // double # Minimum free memory percentage required for this backend to be considered healthy.
    public function getMinFreeMem()
    {
        return @$this->attributes['min_free_mem'];
    }

    public function setMinFreeMem($value)
    {
        return $this->attributes['min_free_mem'] = $value;
    }
    // int64 # Priority of this backend.
    public function getPriority()
    {
        return @$this->attributes['priority'];
    }

    public function setPriority($value)
    {
        return $this->attributes['priority'] = $value;
    }
    // string # Path on the remote server to treat as the root of this mount.
    public function getRemotePath()
    {
        return @$this->attributes['remote_path'];
    }

    public function setRemotePath($value)
    {
        return $this->attributes['remote_path'] = $value;
    }
    // int64 # The remote server that this backend is associated with.
    public function getRemoteServerId()
    {
        return @$this->attributes['remote_server_id'];
    }

    public function setRemoteServerId($value)
    {
        return $this->attributes['remote_server_id'] = $value;
    }
    // int64 # The mount ID of the Remote Server Mount that this backend is associated with.
    public function getRemoteServerMountId()
    {
        return @$this->attributes['remote_server_mount_id'];
    }

    public function setRemoteServerMountId($value)
    {
        return $this->attributes['remote_server_mount_id'] = $value;
    }
    // int64 # Number of consecutive successes before considering the backend healthy.
    public function getRise()
    {
        return @$this->attributes['rise'];
    }

    public function setRise($value)
    {
        return $this->attributes['rise'] = $value;
    }
    // string # Status of this backend.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // boolean # True if this backend is undergoing maintenance.
    public function getUndergoingMaintenance()
    {
        return @$this->attributes['undergoing_maintenance'];
    }

    public function setUndergoingMaintenance($value)
    {
        return $this->attributes['undergoing_maintenance'] = $value;
    }

    // Reset backend status to healthy
    public function resetStatus($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . @$params['id'] . '/reset_status', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   canary_file_path (required) - string - Path to the canary file used for health checks.
    //   remote_server_mount_id (required) - int64 - The mount ID of the Remote Server Mount that this backend is associated with.
    //   remote_server_id (required) - int64 - The remote server that this backend is associated with.
    //   enabled - boolean - True if this backend is enabled.
    //   fall - int64 - Number of consecutive failures before considering the backend unhealthy.
    //   health_check_enabled - boolean - True if health checks are enabled for this backend.
    //   health_check_type - string - Type of health check to perform.
    //   interval - int64 - Interval in seconds between health checks.
    //   min_free_cpu - double - Minimum free CPU percentage required for this backend to be considered healthy.
    //   min_free_mem - double - Minimum free memory percentage required for this backend to be considered healthy.
    //   priority - int64 - Priority of this backend.
    //   remote_path - string - Path on the remote server to treat as the root of this mount.
    //   rise - int64 - Number of consecutive successes before considering the backend healthy.
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!@$params['canary_file_path']) {
            if (@$this->canary_file_path) {
                $params['canary_file_path'] = $this->canary_file_path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: canary_file_path');
            }
        }

        if (!@$params['remote_server_mount_id']) {
            if (@$this->remote_server_mount_id) {
                $params['remote_server_mount_id'] = $this->remote_server_mount_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_mount_id');
            }
        }

        if (!@$params['remote_server_id']) {
            if (@$this->remote_server_id) {
                $params['remote_server_id'] = $this->remote_server_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['canary_file_path'] && !is_string(@$params['canary_file_path'])) {
            throw new \Files\Exception\InvalidParameterException('$canary_file_path must be of type string; received ' . gettype(@$params['canary_file_path']));
        }

        if (@$params['remote_server_mount_id'] && !is_int(@$params['remote_server_mount_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_mount_id must be of type int; received ' . gettype(@$params['remote_server_mount_id']));
        }

        if (@$params['remote_server_id'] && !is_int(@$params['remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_id must be of type int; received ' . gettype(@$params['remote_server_id']));
        }

        if (@$params['fall'] && !is_int(@$params['fall'])) {
            throw new \Files\Exception\InvalidParameterException('$fall must be of type int; received ' . gettype(@$params['fall']));
        }

        if (@$params['health_check_type'] && !is_string(@$params['health_check_type'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_type must be of type string; received ' . gettype(@$params['health_check_type']));
        }

        if (@$params['interval'] && !is_int(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type int; received ' . gettype(@$params['interval']));
        }

        if (@$params['priority'] && !is_int(@$params['priority'])) {
            throw new \Files\Exception\InvalidParameterException('$priority must be of type int; received ' . gettype(@$params['priority']));
        }

        if (@$params['remote_path'] && !is_string(@$params['remote_path'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_path must be of type string; received ' . gettype(@$params['remote_path']));
        }

        if (@$params['rise'] && !is_int(@$params['rise'])) {
            throw new \Files\Exception\InvalidParameterException('$rise must be of type int; received ' . gettype(@$params['rise']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new RemoteMountBackend((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . @$params['id'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

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


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `remote_server_mount_id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_mount_backends', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new RemoteMountBackend((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Remote Mount Backend ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/remote_mount_backends/' . @$params['id'] . '', 'GET', $params, $options);

        return new RemoteMountBackend((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   canary_file_path (required) - string - Path to the canary file used for health checks.
    //   remote_server_mount_id (required) - int64 - The mount ID of the Remote Server Mount that this backend is associated with.
    //   remote_server_id (required) - int64 - The remote server that this backend is associated with.
    //   enabled - boolean - True if this backend is enabled.
    //   fall - int64 - Number of consecutive failures before considering the backend unhealthy.
    //   health_check_enabled - boolean - True if health checks are enabled for this backend.
    //   health_check_type - string - Type of health check to perform.
    //   interval - int64 - Interval in seconds between health checks.
    //   min_free_cpu - double - Minimum free CPU percentage required for this backend to be considered healthy.
    //   min_free_mem - double - Minimum free memory percentage required for this backend to be considered healthy.
    //   priority - int64 - Priority of this backend.
    //   remote_path - string - Path on the remote server to treat as the root of this mount.
    //   rise - int64 - Number of consecutive successes before considering the backend healthy.
    public static function create($params = [], $options = [])
    {
        if (!@$params['canary_file_path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: canary_file_path');
        }

        if (!@$params['remote_server_mount_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_mount_id');
        }

        if (!@$params['remote_server_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: remote_server_id');
        }

        if (@$params['canary_file_path'] && !is_string(@$params['canary_file_path'])) {
            throw new \Files\Exception\InvalidParameterException('$canary_file_path must be of type string; received ' . gettype(@$params['canary_file_path']));
        }

        if (@$params['remote_server_mount_id'] && !is_int(@$params['remote_server_mount_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_mount_id must be of type int; received ' . gettype(@$params['remote_server_mount_id']));
        }

        if (@$params['remote_server_id'] && !is_int(@$params['remote_server_id'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_server_id must be of type int; received ' . gettype(@$params['remote_server_id']));
        }

        if (@$params['fall'] && !is_int(@$params['fall'])) {
            throw new \Files\Exception\InvalidParameterException('$fall must be of type int; received ' . gettype(@$params['fall']));
        }

        if (@$params['health_check_type'] && !is_string(@$params['health_check_type'])) {
            throw new \Files\Exception\InvalidParameterException('$health_check_type must be of type string; received ' . gettype(@$params['health_check_type']));
        }

        if (@$params['interval'] && !is_int(@$params['interval'])) {
            throw new \Files\Exception\InvalidParameterException('$interval must be of type int; received ' . gettype(@$params['interval']));
        }

        if (@$params['priority'] && !is_int(@$params['priority'])) {
            throw new \Files\Exception\InvalidParameterException('$priority must be of type int; received ' . gettype(@$params['priority']));
        }

        if (@$params['remote_path'] && !is_string(@$params['remote_path'])) {
            throw new \Files\Exception\InvalidParameterException('$remote_path must be of type string; received ' . gettype(@$params['remote_path']));
        }

        if (@$params['rise'] && !is_int(@$params['rise'])) {
            throw new \Files\Exception\InvalidParameterException('$rise must be of type int; received ' . gettype(@$params['rise']));
        }

        $response = Api::sendRequest('/remote_mount_backends', 'POST', $params, $options);

        return new RemoteMountBackend((array) (@$response->data ?: []), $options);
    }
}
