<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AgentNode
 *
 * @package Files
 */
class AgentNode
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
    // string # Stable Agent installation ID
    public function getNodeId()
    {
        return isset($this->attributes['node_id']) ? $this->attributes['node_id'] : null;
    }
    // string # Customer-configured Agent node name
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    // string # Hostname reported by the Agent
    public function getHostname()
    {
        return isset($this->attributes['hostname']) ? $this->attributes['hostname'] : null;
    }
    // string # Configured traffic preference
    public function getAvailabilityRole()
    {
        return isset($this->attributes['availability_role']) ? $this->attributes['availability_role'] : null;
    }
    // string # Whether this node currently has an available Agent instance
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // boolean # Whether this node is the current default route for new unscoped work
    public function getIsDefault()
    {
        return isset($this->attributes['is_default']) ? $this->attributes['is_default'] : null;
    }
    // boolean # Whether the proxy recently validated a direct connection to this Agent node. False means direct transfers are enabled but not currently available; null means disabled or unsupported.
    public function getDirectTransferAvailable()
    {
        return isset($this->attributes['direct_transfer_available']) ? $this->attributes['direct_transfer_available'] : null;
    }
    // date-time # Most recent successful node observation
    public function getLastSeenAt()
    {
        return isset($this->attributes['last_seen_at']) ? $this->attributes['last_seen_at'] : null;
    }
    // array(object) # Current Agent processes for this node
    public function getInstances()
    {
        return isset($this->attributes['instances']) ? $this->attributes['instances'] : null;
    }
}
