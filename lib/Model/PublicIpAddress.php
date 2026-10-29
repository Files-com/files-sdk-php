<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PublicIpAddress
 *
 * @package Files
 */
class PublicIpAddress
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
    // string # The public IP address.
    public function getIpAddress()
    {
        return isset($this->attributes['ip_address']) ? $this->attributes['ip_address'] : null;
    }
    // string # The name of the frontend server.
    public function getServerName()
    {
        return isset($this->attributes['server_name']) ? $this->attributes['server_name'] : null;
    }
    // boolean
    public function getFtpEnabled()
    {
        return isset($this->attributes['ftp_enabled']) ? $this->attributes['ftp_enabled'] : null;
    }
    // boolean
    public function getSftpEnabled()
    {
        return isset($this->attributes['sftp_enabled']) ? $this->attributes['sftp_enabled'] : null;
    }
}
