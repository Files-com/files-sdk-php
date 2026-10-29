<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Preview
 *
 * @package Files
 */
class Preview
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
    // int64 # Preview ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # Preview status.  Can be invalid, not_generated, generating, complete, or file_too_large
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // string # Link to download preview
    public function getDownloadUri()
    {
        return isset($this->attributes['download_uri']) ? $this->attributes['download_uri'] : null;
    }
    // string # Preview type. Can be image, pdf, pdf_native, video, audio, or text
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }
    // string # Preview size
    public function getSize()
    {
        return isset($this->attributes['size']) ? $this->attributes['size'] : null;
    }
}
