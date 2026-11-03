<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * DirectConnectionInfo API model.
 *
 * @property int|null $version Direct connection information schema version.
 * @property string|null $server_name TLS server name (SNI and Host header) for the Agent's direct transfer listener.
 * @property string[]|null $addresses Validated ip:port candidates that may be dialed over TCP+TLS for this transfer.
 * @property string|null $direct_uri Signed HTTPS URI for direct Agent transfer traffic.
 * @property string|null $ca_pem PEM-encoded CA certificate used to verify the Agent's direct transfer TLS certificate.
 * @package Files
 */
class DirectConnectionInfo
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
     * Direct connection information schema version.
     *
     * @return int|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    /**
     * TLS server name (SNI and Host header) for the Agent's direct transfer listener.
     *
     * @return string|null
     */
    public function getServerName()
    {
        return isset($this->attributes['server_name']) ? $this->attributes['server_name'] : null;
    }
    /**
     * Validated ip:port candidates that may be dialed over TCP+TLS for this transfer.
     *
     * @return string[]|null
     */
    public function getAddresses()
    {
        return isset($this->attributes['addresses']) ? $this->attributes['addresses'] : null;
    }
    /**
     * Signed HTTPS URI for direct Agent transfer traffic.
     *
     * @return string|null
     */
    public function getDirectUri()
    {
        return isset($this->attributes['direct_uri']) ? $this->attributes['direct_uri'] : null;
    }
    /**
     * PEM-encoded CA certificate used to verify the Agent's direct transfer TLS certificate.
     *
     * @return string|null
     */
    public function getCaPem()
    {
        return isset($this->attributes['ca_pem']) ? $this->attributes['ca_pem'] : null;
    }
}
