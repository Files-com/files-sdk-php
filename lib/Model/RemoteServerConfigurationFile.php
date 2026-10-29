<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteServerConfigurationFile
 *
 * @package Files
 */
class RemoteServerConfigurationFile
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
    // int64 # The remote server ID of the agent
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # The permission set for the agent ['read_write', 'read_only', 'write_only']
    public function getPermissionSet()
    {
        return isset($this->attributes['permission_set']) ? $this->attributes['permission_set'] : null;
    }
    // string # The private key for the agent
    public function getPrivateKey()
    {
        return isset($this->attributes['private_key']) ? $this->attributes['private_key'] : null;
    }
    // string # Files.com subdomain site name
    public function getSubdomain()
    {
        return isset($this->attributes['subdomain']) ? $this->attributes['subdomain'] : null;
    }
    // string # The root directory for the agent
    public function getRoot()
    {
        return isset($this->attributes['root']) ? $this->attributes['root'] : null;
    }
    // string # Optional customer-facing name for this Agent installation
    public function getNodeName()
    {
        return isset($this->attributes['node_name']) ? $this->attributes['node_name'] : null;
    }
    // boolean # Follow symlinks when traversing directories
    public function getFollowLinks()
    {
        return isset($this->attributes['follow_links']) ? $this->attributes['follow_links'] : null;
    }
    // string # Preferred network protocol ['udp', 'tcp'] (default udp)
    public function getPreferProtocol()
    {
        return isset($this->attributes['prefer_protocol']) ? $this->attributes['prefer_protocol'] : null;
    }
    // string # DNS lookup method ['auto','doh','system'] (default auto)
    public function getDns()
    {
        return isset($this->attributes['dns']) ? $this->attributes['dns'] : null;
    }
    // boolean # Proxy all outbound traffic through files.com proxy server
    public function getProxyAllOutbound()
    {
        return isset($this->attributes['proxy_all_outbound']) ? $this->attributes['proxy_all_outbound'] : null;
    }
    // string # Custom site endpoint URL
    public function getEndpointOverride()
    {
        return isset($this->attributes['endpoint_override']) ? $this->attributes['endpoint_override'] : null;
    }
    // string # Log file name and location
    public function getLogFile()
    {
        return isset($this->attributes['log_file']) ? $this->attributes['log_file'] : null;
    }
    // string # Log level for the agent logs ['debug', 'info', 'warn', 'error', 'fatal'] (default info)
    public function getLogLevel()
    {
        return isset($this->attributes['log_level']) ? $this->attributes['log_level'] : null;
    }
    // int64 # Log route for agent logs. (default 5)
    public function getLogRotateNum()
    {
        return isset($this->attributes['log_rotate_num']) ? $this->attributes['log_rotate_num'] : null;
    }
    // int64 # Log route size in MB for agent logs. (default 20)
    public function getLogRotateSize()
    {
        return isset($this->attributes['log_rotate_size']) ? $this->attributes['log_rotate_size'] : null;
    }
    // int64 # Maximum number of concurrent jobs (default 500)
    public function getOverrideMaxConcurrentJobs()
    {
        return isset($this->attributes['override_max_concurrent_jobs']) ? $this->attributes['override_max_concurrent_jobs'] : null;
    }
    // int64 # Graceful shutdown timeout in seconds (default 15)
    public function getGracefulShutdownTimeout()
    {
        return isset($this->attributes['graceful_shutdown_timeout']) ? $this->attributes['graceful_shutdown_timeout'] : null;
    }
    // string # File transfer (upload/download) rate limit
    //  `<limit>-<period>`, with the given periods:
    // * 'S': second
    // * 'M': minute
    // * 'H': hour
    // * 'D': day
    // Examples:
    // * 5 requests/second: '5-S'
    // * 10 requests/minute: '10-M'
    // * 1000 requests/hour: '1000-H'
    // * 2000 requests/day: '2000-D'
    public function getTransferRateLimit()
    {
        return isset($this->attributes['transfer_rate_limit']) ? $this->attributes['transfer_rate_limit'] : null;
    }
    // string # Auto update policy ['manual_trigger', 'critical_only', 'always', 'never'] (default always)
    public function getAutoUpdatePolicy()
    {
        return isset($this->attributes['auto_update_policy']) ? $this->attributes['auto_update_policy'] : null;
    }
}
