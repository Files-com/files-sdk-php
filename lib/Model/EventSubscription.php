<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class EventSubscription
 *
 * @package Files
 */
class EventSubscription
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
    // int64 # Event Subscription ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Event Channel ID
    public function getEventChannelId()
    {
        return @$this->attributes['event_channel_id'];
    }

    public function setEventChannelId($value)
    {
        return $this->attributes['event_channel_id'] = $value;
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
    // boolean # If true, this default-workspace subscription applies to events from all workspaces.
    public function getApplyToAllWorkspaces()
    {
        return @$this->attributes['apply_to_all_workspaces'];
    }

    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    // string # Event Subscription name.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // boolean # Whether this Event Subscription can dispatch events.
    public function getEnabled()
    {
        return @$this->attributes['enabled'];
    }

    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    // array(string) # Event type strings matched by this subscription. Blank means all event types.
    public function getEventTypes()
    {
        return @$this->attributes['event_types'];
    }

    public function setEventTypes($value)
    {
        return $this->attributes['event_types'] = $value;
    }
    // object # Structured event payload filter.
    public function getFilter()
    {
        return @$this->attributes['filter'];
    }

    public function setFilter($value)
    {
        return $this->attributes['filter'] = $value;
    }
    // object # Event Subscription delivery policy.
    public function getDeliveryPolicy()
    {
        return @$this->attributes['delivery_policy'];
    }

    public function setDeliveryPolicy($value)
    {
        return $this->attributes['delivery_policy'] = $value;
    }
    // array(int64) # Event Target IDs this subscription sends to.
    public function getEventTargetIds()
    {
        return @$this->attributes['event_target_ids'];
    }

    public function setEventTargetIds($value)
    {
        return $this->attributes['event_target_ids'] = $value;
    }
    // date-time # Event Subscription create date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # Event Subscription update date/time.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   event_channel_id - int64 - Event Channel ID
    //   workspace_id - int64 - Workspace ID. 0 means the default workspace or site-wide.
    //   apply_to_all_workspaces - boolean - If true, this default-workspace subscription applies to events from all workspaces.
    //   name - string - Event Subscription name.
    //   enabled - boolean - Whether this Event Subscription can dispatch events.
    //   event_types - array(string) - Event type strings matched by this subscription. Blank means all event types.
    //   filter - object - Structured event payload filter.
    //   delivery_policy - object - Event Subscription delivery policy.
    //   event_target_ids - array(int64) - Event Target IDs this subscription sends to.
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

        if (@$params['event_channel_id'] && !is_int(@$params['event_channel_id'])) {
            throw new \Files\Exception\InvalidParameterException('$event_channel_id must be of type int; received ' . gettype(@$params['event_channel_id']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['event_types'] && !is_array(@$params['event_types'])) {
            throw new \Files\Exception\InvalidParameterException('$event_types must be of type array; received ' . gettype(@$params['event_types']));
        }

        if (@$params['event_target_ids'] && !is_array(@$params['event_target_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$event_target_ids must be of type array; received ' . gettype(@$params['event_target_ids']));
        }

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new EventSubscription((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `enabled`, `event_channel_id` or `workspace_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `enabled`, `event_channel_id` or `workspace_id`. Valid field combinations are `[ enabled, event_channel_id ]`, `[ workspace_id, enabled ]` or `[ workspace_id, enabled, event_channel_id ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/event_subscriptions', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EventSubscription((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Event Subscription ID.
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

        $response = Api::sendRequest('/event_subscriptions/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventSubscription((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   event_channel_id - int64 - Event Channel ID
    //   workspace_id - int64 - Workspace ID. 0 means the default workspace or site-wide.
    //   apply_to_all_workspaces - boolean - If true, this default-workspace subscription applies to events from all workspaces.
    //   name (required) - string - Event Subscription name.
    //   enabled - boolean - Whether this Event Subscription can dispatch events.
    //   event_types - array(string) - Event type strings matched by this subscription. Blank means all event types.
    //   filter - object - Structured event payload filter.
    //   delivery_policy - object - Event Subscription delivery policy.
    //   event_target_ids - array(int64) - Event Target IDs this subscription sends to.
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (@$params['event_channel_id'] && !is_int(@$params['event_channel_id'])) {
            throw new \Files\Exception\InvalidParameterException('$event_channel_id must be of type int; received ' . gettype(@$params['event_channel_id']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['event_types'] && !is_array(@$params['event_types'])) {
            throw new \Files\Exception\InvalidParameterException('$event_types must be of type array; received ' . gettype(@$params['event_types']));
        }

        if (@$params['event_target_ids'] && !is_array(@$params['event_target_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$event_target_ids must be of type array; received ' . gettype(@$params['event_target_ids']));
        }

        $response = Api::sendRequest('/event_subscriptions', 'POST', $params, $options);

        return new EventSubscription((array) (@$response->data ?: []), $options);
    }
}
