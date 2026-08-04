<?php

declare(strict_types=1);

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
    // string # Stable Agent installation ID
    public function getNodeId()
    {
        return @$this->attributes['node_id'];
    }
    // string # Customer-configured Agent node name
    public function getName()
    {
        return @$this->attributes['name'];
    }
    // string # Hostname reported by the Agent
    public function getHostname()
    {
        return @$this->attributes['hostname'];
    }
    // string # Configured traffic preference
    public function getAvailabilityRole()
    {
        return @$this->attributes['availability_role'];
    }
    // string # Whether this node is currently available for traffic
    public function getConnectionStatus()
    {
        return @$this->attributes['connection_status'];
    }
    // boolean # Whether this node is the current default route for new unscoped work
    public function getIsDefault()
    {
        return @$this->attributes['is_default'];
    }
    // string # Agent version reported by this node
    public function getAgentVersion()
    {
        return @$this->attributes['agent_version'];
    }
    // boolean # Whether the proxy recently validated a direct connection to this Agent node. False means direct transfers are enabled but not currently available; null means disabled or unsupported.
    public function getDirectTransferAvailable()
    {
        return @$this->attributes['direct_transfer_available'];
    }
    // date-time # Most recent successful node observation
    public function getLastSeenAt()
    {
        return @$this->attributes['last_seen_at'];
    }
}
