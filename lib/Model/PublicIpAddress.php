<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A PublicIPAddress is an IP address that `app.files.com` (or `*.files.com`) may potentially resolve to over the next 30 days.
 *
 * @property string|null $ip_address The public IP address.
 * @property string|null $server_name The name of the frontend server.
 * @property bool|null $ftp_enabled
 * @property bool|null $sftp_enabled
 * @package Files
 */
class PublicIpAddress
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
     * The public IP address.
     *
     * @return string|null
     */
    public function getIpAddress()
    {
        return isset($this->attributes['ip_address']) ? $this->attributes['ip_address'] : null;
    }
    /**
     * The name of the frontend server.
     *
     * @return string|null
     */
    public function getServerName()
    {
        return isset($this->attributes['server_name']) ? $this->attributes['server_name'] : null;
    }
    /**
     * Read ftp_enabled.
     *
     * @return bool|null
     */
    public function getFtpEnabled()
    {
        return isset($this->attributes['ftp_enabled']) ? $this->attributes['ftp_enabled'] : null;
    }
    /**
     * Read sftp_enabled.
     *
     * @return bool|null
     */
    public function getSftpEnabled()
    {
        return isset($this->attributes['sftp_enabled']) ? $this->attributes['sftp_enabled'] : null;
    }
}
