<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * AgentNodeInstance API model.
 *
 * @property string|null $instance_id Ephemeral ID for this running Agent process
 * @property string|null $process_state Role of this process during an Agent update
 * @property string|null $status Whether this process has an available proxy connection
 * @property bool|null $is_default Whether this process receives new unscoped work for its node
 * @property string|null $agent_version Agent version reported by this process
 * @property string|null $last_seen_at Most recent successful observation for this process
 * @property object[]|null $connections Proxy connections observed for this process
 * @package Files
 */
class AgentNodeInstance
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
     * Ephemeral ID for this running Agent process
     *
     * @return string|null
     */
    public function getInstanceId()
    {
        return isset($this->attributes['instance_id']) ? $this->attributes['instance_id'] : null;
    }
    /**
     * Role of this process during an Agent update
     *
     * @return string|null
     */
    public function getProcessState()
    {
        return isset($this->attributes['process_state']) ? $this->attributes['process_state'] : null;
    }
    /**
     * Whether this process has an available proxy connection
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Whether this process receives new unscoped work for its node
     *
     * @return bool|null
     */
    public function getIsDefault()
    {
        return isset($this->attributes['is_default']) ? $this->attributes['is_default'] : null;
    }
    /**
     * Agent version reported by this process
     *
     * @return string|null
     */
    public function getAgentVersion()
    {
        return isset($this->attributes['agent_version']) ? $this->attributes['agent_version'] : null;
    }
    /**
     * Most recent successful observation for this process
     *
     * @return string|null
     */
    public function getLastSeenAt()
    {
        return isset($this->attributes['last_seen_at']) ? $this->attributes['last_seen_at'] : null;
    }
    /**
     * Proxy connections observed for this process
     *
     * @return object[]|null
     */
    public function getConnections()
    {
        return isset($this->attributes['connections']) ? $this->attributes['connections'] : null;
    }
}
