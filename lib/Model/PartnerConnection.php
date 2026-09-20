<?php

declare(strict_types=1);

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
    // int64 # Relationship ID used with DELETE /partner_sites/:id to disconnect.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # This Partner's role in this connection. A host shares local files with the connected site; a guest accesses files shared by the connected site.
    public function getRole()
    {
        return @$this->attributes['role'];
    }
    // int64 # ID of the connected site.
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }
    // string # Name of the connected site.
    public function getSiteName()
    {
        return @$this->attributes['site_name'];
    }
    // string # File API path to the connected Host's mount on this site when role is guest. Null when role is host. File access remains subject to the caller's permissions and the Host Partner's grants.
    public function getMountPath()
    {
        return @$this->attributes['mount_path'];
    }
}
