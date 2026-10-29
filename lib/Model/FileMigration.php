<?php

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
    // int64 # File migration ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // string # Source path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    // string # Destination path
    public function getDestPath()
    {
        return isset($this->attributes['dest_path']) ? $this->attributes['dest_path'] : null;
    }
    // string # Reason for the failure, if applicable.
    public function getFailureMessage()
    {
        return isset($this->attributes['failure_message']) ? $this->attributes['failure_message'] : null;
    }
    // int64 # Number of files processed
    public function getFilesMoved()
    {
        return isset($this->attributes['files_moved']) ? $this->attributes['files_moved'] : null;
    }
    // int64
    public function getFilesTotal()
    {
        return isset($this->attributes['files_total']) ? $this->attributes['files_total'] : null;
    }
    // string # The type of operation
    public function getOperation()
    {
        return isset($this->attributes['operation']) ? $this->attributes['operation'] : null;
    }
    // string # Region
    public function getRegion()
    {
        return isset($this->attributes['region']) ? $this->attributes['region'] : null;
    }
    // string # Status
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // string # Link to download the log file for this migration.
    public function getLogUrl()
    {
        return isset($this->attributes['log_url']) ? $this->attributes['log_url'] : null;
    }

    // Parameters:
    //   id (required) - int64 - File Migration ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/file_migrations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new FileMigration((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
