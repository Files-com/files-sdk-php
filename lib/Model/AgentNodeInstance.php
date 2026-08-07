<?php

declare(strict_types=1);

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
    // string # Ephemeral ID for this running Agent process
    public function getInstanceId()
    {
        return @$this->attributes['instance_id'];
    }
    // string # Role of this process during an Agent update
    public function getProcessState()
    {
        return @$this->attributes['process_state'];
    }
    // string # Whether this process has an available proxy connection
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // boolean # Whether this process receives new unscoped work for its node
    public function getIsDefault()
    {
        return @$this->attributes['is_default'];
    }
    // string # Agent version reported by this process
    public function getAgentVersion()
    {
        return @$this->attributes['agent_version'];
    }
    // date-time # Most recent successful observation for this process
    public function getLastSeenAt()
    {
        return @$this->attributes['last_seen_at'];
    }
    // array(object) # Proxy connections observed for this process
    public function getConnections()
    {
        return @$this->attributes['connections'];
    }
}
