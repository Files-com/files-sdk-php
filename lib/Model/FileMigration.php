<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A FileMigration is a background operation on one or more files, such as a copy or a region migration.
 *
 * @property int|null $id File migration ID
 * @property string|null $path Source path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $dest_path Destination path
 * @property string|null $failure_message Reason for the failure, if applicable.
 * @property int|null $files_moved Number of files processed
 * @property int|null $files_total
 * @property string|null $operation The type of operation
 * @property string|null $region Region
 * @property string|null $status Status
 * @property string|null $log_url Link to download the log file for this migration.
 * @package Files
 */
class FileMigration
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
     * File migration ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Source path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Destination path
     *
     * @return string|null
     */
    public function getDestPath()
    {
        return isset($this->attributes['dest_path']) ? $this->attributes['dest_path'] : null;
    }
    /**
     * Reason for the failure, if applicable.
     *
     * @return string|null
     */
    public function getFailureMessage()
    {
        return isset($this->attributes['failure_message']) ? $this->attributes['failure_message'] : null;
    }
    /**
     * Number of files processed
     *
     * @return int|null
     */
    public function getFilesMoved()
    {
        return isset($this->attributes['files_moved']) ? $this->attributes['files_moved'] : null;
    }
    /**
     * Read files_total.
     *
     * @return int|null
     */
    public function getFilesTotal()
    {
        return isset($this->attributes['files_total']) ? $this->attributes['files_total'] : null;
    }
    /**
     * The type of operation
     *
     * @return string|null
     */
    public function getOperation()
    {
        return isset($this->attributes['operation']) ? $this->attributes['operation'] : null;
    }
    /**
     * Region
     *
     * @return string|null
     */
    public function getRegion()
    {
        return isset($this->attributes['region']) ? $this->attributes['region'] : null;
    }
    /**
     * Status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Link to download the log file for this migration.
     *
     * @return string|null
     */
    public function getLogUrl()
    {
        return isset($this->attributes['log_url']) ? $this->attributes['log_url'] : null;
    }

    /**
     * Show File Migration
     *
     * GET /file_migrations/{id}
     *
     * @param int $id File Migration ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FileMigration
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/file_migrations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new FileMigration(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show File Migration
     *
     * GET /file_migrations/{id}
     *
     * Alias of find().
     *
     * @param int $id File Migration ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FileMigration
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
