<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AgentNodeInstance
 *
 * @package Files
 */
class AgentNodeInstance
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // string # Ephemeral ID for this running Agent process
    public function getInstanceId()
    {
        return isset($this->attributes['instance_id']) ? $this->attributes['instance_id'] : null;
    }
    // string # Role of this process during an Agent update
    public function getProcessState()
    {
        return isset($this->attributes['process_state']) ? $this->attributes['process_state'] : null;
    }
    // string # Whether this process has an available proxy connection
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // boolean # Whether this process receives new unscoped work for its node
    public function getIsDefault()
    {
        return isset($this->attributes['is_default']) ? $this->attributes['is_default'] : null;
    }
    // string # Agent version reported by this process
    public function getAgentVersion()
    {
        return isset($this->attributes['agent_version']) ? $this->attributes['agent_version'] : null;
    }
    // date-time # Most recent successful observation for this process
    public function getLastSeenAt()
    {
        return isset($this->attributes['last_seen_at']) ? $this->attributes['last_seen_at'] : null;
    }
    // array(object) # Proxy connections observed for this process
    public function getConnections()
    {
        return isset($this->attributes['connections']) ? $this->attributes['connections'] : null;
    }
}
