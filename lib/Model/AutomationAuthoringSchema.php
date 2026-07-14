<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AutomationAuthoringSchema
 *
 * @package Files
 */
class AutomationAuthoringSchema
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
    // object # JSON Schema for active Automation v2 graph definitions.
    public function getDefinitionSchema()
    {
        return @$this->attributes['definition_schema'];
    }
    // array(object) # Typed error families accepted by Automation v2 on_error rules.
    public function getErrorFamilies()
    {
        return @$this->attributes['error_families'];
    }
    // array(object) # Active Automation v2 node authoring metadata.
    public function getNodes()
    {
        return @$this->attributes['nodes'];
    }
}
