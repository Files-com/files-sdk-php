<?php

declare(strict_types=1);

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
    // int64 # The remote server ID of the agent
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # The permission set for the agent ['read_write', 'read_only', 'write_only']
    public function getPermissionSet()
    {
        return @$this->attributes['permission_set'];
    }
    // string # The private key for the agent
    public function getPrivateKey()
    {
        return @$this->attributes['private_key'];
    }
    // string # Files.com subdomain site name
    public function getSubdomain()
    {
        return @$this->attributes['subdomain'];
    }
    // string # The root directory for the agent
    public function getRoot()
    {
        return @$this->attributes['root'];
    }
    // boolean # Follow symlinks when traversing directories
    public function getFollowLinks()
    {
        return @$this->attributes['follow_links'];
    }
    // string # Preferred network protocol ['udp', 'tcp'] (default udp)
    public function getPreferProtocol()
    {
        return @$this->attributes['prefer_protocol'];
    }
    // string # DNS lookup method ['auto','doh','system'] (default auto)
    public function getDns()
    {
        return @$this->attributes['dns'];
    }
    // boolean # Proxy all outbound traffic through files.com proxy server
    public function getProxyAllOutbound()
    {
        return @$this->attributes['proxy_all_outbound'];
    }
    // string # Custom site endpoint URL
    public function getEndpointOverride()
    {
        return @$this->attributes['endpoint_override'];
    }
    // string # Log file name and location
    public function getLogFile()
    {
        return @$this->attributes['log_file'];
    }
    // string # Log level for the agent logs ['debug', 'info', 'warn', 'error', 'fatal'] (default info)
    public function getLogLevel()
    {
        return @$this->attributes['log_level'];
    }
    // int64 # Log route for agent logs. (default 5)
    public function getLogRotateNum()
    {
        return @$this->attributes['log_rotate_num'];
    }
    // int64 # Log route size in MB for agent logs. (default 20)
    public function getLogRotateSize()
    {
        return @$this->attributes['log_rotate_size'];
    }
    // int64 # Maximum number of concurrent jobs (default 500)
    public function getOverrideMaxConcurrentJobs()
    {
        return @$this->attributes['override_max_concurrent_jobs'];
    }
    // int64 # Graceful shutdown timeout in seconds (default 15)
    public function getGracefulShutdownTimeout()
    {
        return @$this->attributes['graceful_shutdown_timeout'];
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
        return @$this->attributes['transfer_rate_limit'];
    }
    // string # Files Agent API Token
    public function getApiToken()
    {
        return @$this->attributes['api_token'];
    }
    // int64 # Incoming port for files agent connections
    public function getPort()
    {
        return @$this->attributes['port'];
    }
    // string
    public function getHostname()
    {
        return @$this->attributes['hostname'];
    }
    // string # public key
    public function getPublicKey()
    {
        return @$this->attributes['public_key'];
    }
    // string # either running or shutdown
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string
    public function getServerHostKey()
    {
        return @$this->attributes['server_host_key'];
    }
    // string # agent config version
    public function getConfigVersion()
    {
        return @$this->attributes['config_version'];
    }
}
