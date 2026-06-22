<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ChatMessage
 *
 * @package Files
 */
class ChatMessage
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
    // int64 # Chat Message ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Message role.
    public function getRole()
    {
        return @$this->attributes['role'];
    }
    // string # Message content.
    public function getContent()
    {
        return @$this->attributes['content'];
    }
    // date-time # Message creation date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
}
