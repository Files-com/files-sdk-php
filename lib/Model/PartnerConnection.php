<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PartnerConnection
 *
 * @package Files
 */
class PartnerConnection
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
    // int64 # Relationship ID used with DELETE /partner_sites/:id to disconnect.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # This Partner's role in this connection. A host shares local files with the connected site; a guest accesses files shared by the connected site.
    public function getRole()
    {
        return isset($this->attributes['role']) ? $this->attributes['role'] : null;
    }
    // int64 # ID of the connected site.
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }
    // string # Name of the connected site.
    public function getSiteName()
    {
        return isset($this->attributes['site_name']) ? $this->attributes['site_name'] : null;
    }
    // string # File API path to the connected Host's mount on this site when role is guest. Null when role is host. File access remains subject to the caller's permissions and the Host Partner's grants.
    public function getMountPath()
    {
        return isset($this->attributes['mount_path']) ? $this->attributes['mount_path'] : null;
    }
}
