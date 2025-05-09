<?php

declare(strict_types=1);

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
    // int64 # Preview ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Preview status.  Can be invalid, not_generated, generating, complete, or file_too_large
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Link to download preview
    public function getDownloadUri()
    {
        return @$this->attributes['download_uri'];
    }
    // string # Preview type. Can be image, pdf, pdf_native, video, or audio
    public function getType()
    {
        return @$this->attributes['type'];
    }
    // string # Preview size
    public function getSize()
    {
        return @$this->attributes['size'];
    }
}
