<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileMigration
 *
 * @package Files
 */
class FileMigration
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
                return @self::$method($arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // int64 # File migration ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Source path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // string # Destination path
    public function getDestPath()
    {
        return @$this->attributes['dest_path'];
    }
    // int64 # Number of files processed
    public function getFilesMoved()
    {
        return @$this->attributes['files_moved'];
    }
    // int64
    public function getFilesTotal()
    {
        return @$this->attributes['files_total'];
    }
    // string # The type of operation
    public function getOperation()
    {
        return @$this->attributes['operation'];
    }
    // string # Region
    public function getRegion()
    {
        return @$this->attributes['region'];
    }
    // string # Status
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Link to download the log file for this migration.
    public function getLogUrl()
    {
        return @$this->attributes['log_url'];
    }

    // Parameters:
    //   id (required) - int64 - File Migration ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/file_migrations/' . @$params['id'] . '', 'GET', $params, $options);

        return new FileMigration((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
