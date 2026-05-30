<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class EventTarget
 *
 * @package Files
 */
class EventTarget
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
    // int64 # Event Target ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Event Target name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Event Target type.
    public function getTargetType()
    {
        return @$this->attributes['target_type'];
    }

    public function setTargetType($value)
    {
        return $this->attributes['target_type'] = $value;
    }
    // int64 # Workspace ID. 0 means the default workspace or site-wide.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // boolean # If true, this default-workspace target can receive events from all workspaces.
    public function getApplyToAllWorkspaces()
    {
        return @$this->attributes['apply_to_all_workspaces'];
    }

    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    // boolean # Whether this Event Target can receive events.
    public function getEnabled()
    {
        return @$this->attributes['enabled'];
    }

    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    // object # Event Target configuration.
    public function getConfig()
    {
        return @$this->attributes['config'];
    }

    public function setConfig($value)
    {
        return $this->attributes['config'] = $value;
    }
    // object # Event Target delivery policy. Email targets support batch_interval in seconds, between 600 and 86400.
    public function getDeliveryPolicy()
    {
        return @$this->attributes['delivery_policy'];
    }

    public function setDeliveryPolicy($value)
    {
        return $this->attributes['delivery_policy'] = $value;
    }
    // date-time # Event Target create date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # Event Target update date/time.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   name - string - Event Target name.
    //   workspace_id - int64 - Workspace ID. 0 means the default workspace or site-wide.
    //   apply_to_all_workspaces - boolean - If true, this default-workspace target can receive events from all workspaces.
    //   target_type - string - Event Target type.
    //   enabled - boolean - Whether this Event Target can receive events.
    //   config - object - Event Target configuration.
    //   delivery_policy - object - Event Target delivery policy. Email targets support batch_interval in seconds, between 600 and 86400.
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

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (@$params['target_type'] && !is_string(@$params['target_type'])) {
            throw new \Files\Exception\InvalidParameterException('$target_type must be of type string; received ' . gettype(@$params['target_type']));
        }

        $response = Api::sendRequest('/event_targets/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new EventTarget((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/event_targets/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `enabled` or `workspace_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `enabled`, `target_type` or `workspace_id`. Valid field combinations are `[ enabled, target_type ]`, `[ workspace_id, enabled ]` or `[ workspace_id, enabled, target_type ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/event_targets', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EventTarget((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Event Target ID.
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

        $response = Api::sendRequest('/event_targets/' . @$params['id'] . '', 'GET', $params, $options);

        return new EventTarget((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   name (required) - string - Event Target name.
    //   workspace_id - int64 - Workspace ID. 0 means the default workspace or site-wide.
    //   apply_to_all_workspaces - boolean - If true, this default-workspace target can receive events from all workspaces.
    //   target_type (required) - string - Event Target type.
    //   enabled - boolean - Whether this Event Target can receive events.
    //   config (required) - object - Event Target configuration.
    //   delivery_policy - object - Event Target delivery policy. Email targets support batch_interval in seconds, between 600 and 86400.
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!@$params['target_type']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: target_type');
        }

        if (!@$params['config']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: config');
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (@$params['target_type'] && !is_string(@$params['target_type'])) {
            throw new \Files\Exception\InvalidParameterException('$target_type must be of type string; received ' . gettype(@$params['target_type']));
        }

        $response = Api::sendRequest('/event_targets', 'POST', $params, $options);

        return new EventTarget((array) (@$response->data ?: []), $options);
    }
}
