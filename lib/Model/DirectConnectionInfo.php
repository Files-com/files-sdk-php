<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class DirectConnectionInfo
 *
 * @package Files
 */
class DirectConnectionInfo
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
    // int64 # Direct connection information schema version.
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    // string # TLS server name (SNI and Host header) for the Agent's direct transfer listener.
    public function getServerName()
    {
        return isset($this->attributes['server_name']) ? $this->attributes['server_name'] : null;
    }
    // array(string) # Validated ip:port candidates that may be dialed over TCP+TLS for this transfer.
    public function getAddresses()
    {
        return isset($this->attributes['addresses']) ? $this->attributes['addresses'] : null;
    }
    // string # Signed HTTPS URI for direct Agent transfer traffic.
    public function getDirectUri()
    {
        return isset($this->attributes['direct_uri']) ? $this->attributes['direct_uri'] : null;
    }
    // string # PEM-encoded CA certificate used to verify the Agent's direct transfer TLS certificate.
    public function getCaPem()
    {
        return isset($this->attributes['ca_pem']) ? $this->attributes['ca_pem'] : null;
    }
}
