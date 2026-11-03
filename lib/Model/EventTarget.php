<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EventTarget is a delivery destination for EventRecords.
 *
 * @property int|null $id Event Target ID
 * @property string|null $name Event Target name.
 * @property string|null $target_type Event Target type.
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace or site-wide.
 * @property bool|null $apply_to_all_workspaces If true, this default-workspace target can receive events from all workspaces.
 * @property bool|null $enabled Whether this Event Target can receive events.
 * @property object|null $config Event Target configuration. Fields depend on target_type.
 * @property object|null $delivery_policy Event Target delivery policy. Email and folder targets support batch_interval in seconds, between 600 and 86400. Slack and Teams support delivery_mode (immediate or subfolder_summary), summary_path (empty string means root), and summary_delay (15–300 seconds, default 60). The summary applies to matching file activity under summary_path; other matching events retain immediate delivery.
 * @property string|null $created_at Event Target create date/time.
 * @property string|null $updated_at Event Target update date/time.
 * @package Files
 */
class EventTarget
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
     * Event Target ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Event Target ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Event Target name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Event Target name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Event Target type.
     *
     * @return string|null
     */
    public function getTargetType()
    {
        return isset($this->attributes['target_type']) ? $this->attributes['target_type'] : null;
    }

    /**
     * Event Target type.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTargetType($value)
    {
        return $this->attributes['target_type'] = $value;
    }
    /**
     * Workspace ID. 0 means the default workspace or site-wide.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID. 0 means the default workspace or site-wide.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * If true, this default-workspace target can receive events from all workspaces.
     *
     * @return bool|null
     */
    public function getApplyToAllWorkspaces()
    {
        return isset($this->attributes['apply_to_all_workspaces']) ? $this->attributes['apply_to_all_workspaces'] : null;
    }

    /**
     * If true, this default-workspace target can receive events from all workspaces.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    /**
     * Whether this Event Target can receive events.
     *
     * @return bool|null
     */
    public function getEnabled()
    {
        return isset($this->attributes['enabled']) ? $this->attributes['enabled'] : null;
    }

    /**
     * Whether this Event Target can receive events.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setEnabled($value)
    {
        return $this->attributes['enabled'] = $value;
    }
    /**
     * Event Target configuration. Fields depend on target_type.
     *
     * @return object|null
     */
    public function getConfig()
    {
        return isset($this->attributes['config']) ? $this->attributes['config'] : null;
    }

    /**
     * Event Target configuration. Fields depend on target_type.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setConfig($value)
    {
        return $this->attributes['config'] = $value;
    }
    /**
     * Event Target delivery policy. Email and folder targets support batch_interval in seconds, between 600 and 86400. Slack and Teams support delivery_mode (immediate or subfolder_summary), summary_path (empty string means root), and summary_delay (15–300 seconds, default 60). The summary applies to matching file activity under summary_path; other matching events retain immediate delivery.
     *
     * @return object|null
     */
    public function getDeliveryPolicy()
    {
        return isset($this->attributes['delivery_policy']) ? $this->attributes['delivery_policy'] : null;
    }

    /**
     * Event Target delivery policy. Email and folder targets support batch_interval in seconds, between 600 and 86400. Slack and Teams support delivery_mode (immediate or subfolder_summary), summary_path (empty string means root), and summary_delay (15–300 seconds, default 60). The summary applies to matching file activity under summary_path; other matching events retain immediate delivery.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setDeliveryPolicy($value)
    {
        return $this->attributes['delivery_policy'] = $value;
    }
    /**
     * Event Target create date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Event Target update date/time.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Update Event Target
     *
     * PATCH /event_targets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Target ID.
     *     - `name` (string, optional): Event Target name.
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace or site-wide.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace target can receive events from all workspaces.
     *     - `enabled` (bool, optional): Whether this Event Target can receive events.
     *     - `config` (array|object, optional): Event Target configuration. Fields depend on target_type.
     *     - `delivery_policy` (array|object, optional): Event Target delivery policy. Email and folder targets support batch_interval in seconds, between 600 and 86400. Slack and Teams support delivery_mode (immediate or subfolder_summary), summary_path (empty string means root), and summary_delay (15–300 seconds, default 60). The summary applies to matching file activity under summary_path; other matching events retain immediate delivery.
     * @return \Files\Model\EventTarget
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

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/event_targets/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new EventTarget((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Event Target
     *
     * DELETE /event_targets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Target ID.
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

        $response = Api::sendRequest('/event_targets/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Event Target
     *
     * DELETE /event_targets/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Event Target ID.
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
     * List Event Targets
     *
     * GET /event_targets
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`, `enabled` or `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `enabled`, `target_type` or `workspace_id`. Valid field combinations are `[ enabled, target_type ]`, `[ workspace_id, enabled ]` or `[ workspace_id, enabled, target_type ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventTarget[]
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

        $response = Api::sendRequest('/event_targets', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EventTarget((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Event Target
     *
     * GET /event_targets/{id}
     *
     * @param int $id Event Target ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventTarget
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

        $response = Api::sendRequest('/event_targets/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventTarget((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Event Target
     *
     * GET /event_targets/{id}
     *
     * Alias of find().
     *
     * @param int $id Event Target ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventTarget
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Event Target
     *
     * POST /event_targets
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Event Target name.
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace or site-wide.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace target can receive events from all workspaces.
     *     - `enabled` (bool, optional): Whether this Event Target can receive events.
     *     - `config` (array|object, required): Event Target configuration. Fields depend on target_type.
     *     - `delivery_policy` (array|object, optional): Event Target delivery policy. Email and folder targets support batch_interval in seconds, between 600 and 86400. Slack and Teams support delivery_mode (immediate or subfolder_summary), summary_path (empty string means root), and summary_delay (15–300 seconds, default 60). The summary applies to matching file activity under summary_path; other matching events retain immediate delivery.
     *     - `target_type` (string, required): Event Target type. Allowed values: email, webhook, slack_webhook, teams_webhook, amazon_sns, google_pubsub, folder.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EventTarget
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['config'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: config');
        }

        if (empty($params['target_type'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: target_type');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['target_type']) && !is_string($params['target_type'])) {
            throw new \Files\Exception\InvalidParameterException('$target_type must be of type string; received ' . gettype(@$params['target_type']));
        }

        $response = Api::sendRequest('/event_targets', 'POST', $params, $options);

        return new EventTarget((array) (@$response->data ?: []), $options);
    }
}
