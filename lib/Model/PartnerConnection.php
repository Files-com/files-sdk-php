<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * PartnerConnection API model.
 *
 * @property int|null $id Relationship ID used with DELETE /partner_sites/:id to disconnect.
 * @property string|null $role This Partner's role in this connection. A host shares local files with the connected site; a guest accesses files shared by the connected site.
 * @property int|null $site_id ID of the connected site.
 * @property string|null $site_name Name of the connected site.
 * @property string|null $mount_path File API path to the connected Host's mount on this site when role is guest. Null when role is host. File access remains subject to the caller's permissions and the Host Partner's grants.
 * @package Files
 */
class PartnerConnection
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
     * Relationship ID used with DELETE /partner_sites/:id to disconnect.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * This Partner's role in this connection. A host shares local files with the connected site; a guest accesses files shared by the connected site.
     *
     * @return string|null
     */
    public function getRole()
    {
        return isset($this->attributes['role']) ? $this->attributes['role'] : null;
    }
    /**
     * ID of the connected site.
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }
    /**
     * Name of the connected site.
     *
     * @return string|null
     */
    public function getSiteName()
    {
        return isset($this->attributes['site_name']) ? $this->attributes['site_name'] : null;
    }
    /**
     * File API path to the connected Host's mount on this site when role is guest. Null when role is host. File access remains subject to the caller's permissions and the Host Partner's grants.
     *
     * @return string|null
     */
    public function getMountPath()
    {
        return isset($this->attributes['mount_path']) ? $this->attributes['mount_path'] : null;
    }
}
