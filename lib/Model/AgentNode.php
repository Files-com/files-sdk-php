<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * AgentNode API model.
 *
 * @property string|null $node_id Stable Agent installation ID
 * @property string|null $name Customer-configured Agent node name
 * @property string|null $hostname Hostname reported by the Agent
 * @property string|null $availability_role Configured traffic preference
 * @property string|null $status Whether this node currently has an available Agent instance
 * @property bool|null $is_default Whether this node is the current default route for new unscoped work
 * @property bool|null $direct_transfer_available Whether the proxy recently validated a direct connection to this Agent node. False means direct transfers are enabled but not currently available; null means disabled or unsupported.
 * @property string|null $last_seen_at Most recent successful node observation
 * @property object[]|null $instances Current Agent processes for this node
 * @package Files
 */
class AgentNode
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
     * Stable Agent installation ID
     *
     * @return string|null
     */
    public function getNodeId()
    {
        return isset($this->attributes['node_id']) ? $this->attributes['node_id'] : null;
    }
    /**
     * Customer-configured Agent node name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * Hostname reported by the Agent
     *
     * @return string|null
     */
    public function getHostname()
    {
        return isset($this->attributes['hostname']) ? $this->attributes['hostname'] : null;
    }
    /**
     * Configured traffic preference
     *
     * @return string|null
     */
    public function getAvailabilityRole()
    {
        return isset($this->attributes['availability_role']) ? $this->attributes['availability_role'] : null;
    }
    /**
     * Whether this node currently has an available Agent instance
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Whether this node is the current default route for new unscoped work
     *
     * @return bool|null
     */
    public function getIsDefault()
    {
        return isset($this->attributes['is_default']) ? $this->attributes['is_default'] : null;
    }
    /**
     * Whether the proxy recently validated a direct connection to this Agent node. False means direct transfers are enabled but not currently available; null means disabled or unsupported.
     *
     * @return bool|null
     */
    public function getDirectTransferAvailable()
    {
        return isset($this->attributes['direct_transfer_available']) ? $this->attributes['direct_transfer_available'] : null;
    }
    /**
     * Most recent successful node observation
     *
     * @return string|null
     */
    public function getLastSeenAt()
    {
        return isset($this->attributes['last_seen_at']) ? $this->attributes['last_seen_at'] : null;
    }
    /**
     * Current Agent processes for this node
     *
     * @return object[]|null
     */
    public function getInstances()
    {
        return isset($this->attributes['instances']) ? $this->attributes['instances'] : null;
    }
}
