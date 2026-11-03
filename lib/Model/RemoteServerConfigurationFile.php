<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A RemoteServerConfigurationFile is a configuration file (required for some Remote Server integrations, such as the Files.com Agent).
 *
 * @property int|null $id The remote server ID of the agent
 * @property string|null $permission_set The permission set for the agent ['read_write', 'read_only', 'write_only']
 * @property string|null $private_key The private key for the agent
 * @property string|null $subdomain Files.com subdomain site name
 * @property string|null $root The root directory for the agent
 * @property string|null $node_name Optional customer-facing name for this Agent installation
 * @property bool|null $follow_links Follow symlinks when traversing directories
 * @property string|null $prefer_protocol Preferred network protocol ['udp', 'tcp'] (default udp)
 * @property string|null $dns DNS lookup method ['auto','doh','system'] (default auto)
 * @property bool|null $proxy_all_outbound Proxy all outbound traffic through files.com proxy server
 * @property string|null $endpoint_override Custom site endpoint URL
 * @property string|null $log_file Log file name and location
 * @property string|null $log_level Log level for the agent logs ['debug', 'info', 'warn', 'error', 'fatal'] (default info)
 * @property int|null $log_rotate_num Log route for agent logs. (default 5)
 * @property int|null $log_rotate_size Log route size in MB for agent logs. (default 20)
 * @property int|null $override_max_concurrent_jobs Maximum number of concurrent jobs (default 500)
 * @property int|null $graceful_shutdown_timeout Graceful shutdown timeout in seconds (default 15)
 * @property string|null $transfer_rate_limit File transfer (upload/download) rate limit `<limit>-<period>`, with the given periods: * 'S': second * 'M': minute * 'H': hour * 'D': day Examples: * 5 requests/second: '5-S' * 10 requests/minute: '10-M' * 1000 requests/hour: '1000-H' * 2000 requests/day: '2000-D'
 * @property string|null $auto_update_policy Auto update policy ['manual_trigger', 'critical_only', 'always', 'never'] (default always)
 * @package Files
 */
class RemoteServerConfigurationFile
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
     * The remote server ID of the agent
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * The permission set for the agent ['read_write', 'read_only', 'write_only']
     *
     * @return string|null
     */
    public function getPermissionSet()
    {
        return isset($this->attributes['permission_set']) ? $this->attributes['permission_set'] : null;
    }
    /**
     * The private key for the agent
     *
     * @return string|null
     */
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }
    /**
     * Files.com subdomain site name
     *
     * @return string|null
     */
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    /**
     * The root directory for the agent
     *
     * @return string|null
     */
    public function getRoot()
    {
        return isset($this->attributes['root']) ? $this->attributes['root'] : null;
    }
    /**
     * Optional customer-facing name for this Agent installation
     *
     * @return string|null
     */
    public function getNodeName()
    {
        return isset($this->attributes['node_name']) ? $this->attributes['node_name'] : null;
    }
    /**
     * Follow symlinks when traversing directories
     *
     * @return bool|null
     */
    public function getFollowLinks()
    {
        return isset($this->attributes['follow_links']) ? $this->attributes['follow_links'] : null;
    }
    /**
     * Preferred network protocol ['udp', 'tcp'] (default udp)
     *
     * @return string|null
     */
    public function getPreferProtocol()
    {
        return isset($this->attributes['prefer_protocol']) ? $this->attributes['prefer_protocol'] : null;
    }
    /**
     * DNS lookup method ['auto','doh','system'] (default auto)
     *
     * @return string|null
     */
    public function getDns()
    {
        return isset($this->attributes['dns']) ? $this->attributes['dns'] : null;
    }
    /**
     * Proxy all outbound traffic through files.com proxy server
     *
     * @return bool|null
     */
    public function getProxyAllOutbound()
    {
        return isset($this->attributes['proxy_all_outbound']) ? $this->attributes['proxy_all_outbound'] : null;
    }
    /**
     * Custom site endpoint URL
     *
     * @return string|null
     */
    public function getEndpointOverride()
    {
        return isset($this->attributes['endpoint_override']) ? $this->attributes['endpoint_override'] : null;
    }
    /**
     * Log file name and location
     *
     * @return string|null
     */
    public function getLogFile()
    {
        return isset($this->attributes['log_file']) ? $this->attributes['log_file'] : null;
    }
    /**
     * Log level for the agent logs ['debug', 'info', 'warn', 'error', 'fatal'] (default info)
     *
     * @return string|null
     */
    public function getLogLevel()
    {
        return isset($this->attributes['log_level']) ? $this->attributes['log_level'] : null;
    }
    /**
     * Log route for agent logs. (default 5)
     *
     * @return int|null
     */
    public function getLogRotateNum()
    {
        return isset($this->attributes['log_rotate_num']) ? $this->attributes['log_rotate_num'] : null;
    }
    /**
     * Log route size in MB for agent logs. (default 20)
     *
     * @return int|null
     */
    public function getLogRotateSize()
    {
        return isset($this->attributes['log_rotate_size']) ? $this->attributes['log_rotate_size'] : null;
    }
    /**
     * Maximum number of concurrent jobs (default 500)
     *
     * @return int|null
     */
    public function getOverrideMaxConcurrentJobs()
    {
        return isset($this->attributes['override_max_concurrent_jobs']) ? $this->attributes['override_max_concurrent_jobs'] : null;
    }
    /**
     * Graceful shutdown timeout in seconds (default 15)
     *
     * @return int|null
     */
    public function getGracefulShutdownTimeout()
    {
        return isset($this->attributes['graceful_shutdown_timeout']) ? $this->attributes['graceful_shutdown_timeout'] : null;
    }
    /**
     * File transfer (upload/download) rate limit
     *  `<limit>-<period>`, with the given periods:
     * * 'S': second
     * * 'M': minute
     * * 'H': hour
     * * 'D': day
     * Examples:
     * * 5 requests/second: '5-S'
     * * 10 requests/minute: '10-M'
     * * 1000 requests/hour: '1000-H'
     * * 2000 requests/day: '2000-D'
     *
     * @return string|null
     */
    public function getTransferRateLimit()
    {
        return isset($this->attributes['transfer_rate_limit']) ? $this->attributes['transfer_rate_limit'] : null;
    }
    /**
     * Auto update policy ['manual_trigger', 'critical_only', 'always', 'never'] (default always)
     *
     * @return string|null
     */
    public function getAutoUpdatePolicy()
    {
        return isset($this->attributes['auto_update_policy']) ? $this->attributes['auto_update_policy'] : null;
    }
}
