<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An OutboundConnectionLog is an audit log for monitoring and troubleshooting actions performed by your Remote Servers and Files.com on-premise Agents.
 *
 * @property string|null $timestamp Start Time of Action. Deprecrated: Use created_at.
 * @property string|null $path Remote Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $client_ip End User IP
 * @property int|null $src_remote_server_id Source Remote Server ID
 * @property string|null $src_remote_server_name Source Remote Server name
 * @property int|null $dest_remote_server_id Destination Remote Server ID
 * @property string|null $dest_remote_server_name Destination Remote Server name
 * @property string|null $operation Operation Type
 * @property string|null $error_message Error message, if applicable
 * @property string|null $error_operation Error operation, if applicable
 * @property string|null $error_type Error type, if applicable
 * @property string|null $status Status
 * @property int|null $duration_ms Duration (in milliseconds)
 * @property int|null $bytes_uploaded Data Length in Bytes. Present for upload actions that transfer data.
 * @property int|null $bytes_downloaded Data Length in Bytes. Present for download actions that transfer data.
 * @property int|null $list_count Number of entries returned for a folder list action.
 * @property string|null $created_at Start Time of Action
 * @package Files
 */
class OutboundConnectionLog
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
     * Start Time of Action. Deprecrated: Use created_at.
     *
     * @return string|null
     */
    public function getTimestamp()
    {
        return isset($this->attributes['timestamp']) ? $this->attributes['timestamp'] : null;
    }
    /**
     * Remote Path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * End User IP
     *
     * @return string|null
     */
    public function getClientIp()
    {
        return isset($this->attributes['client_ip']) ? $this->attributes['client_ip'] : null;
    }
    /**
     * Source Remote Server ID
     *
     * @return int|null
     */
    public function getSrcRemoteServerId()
    {
        return isset($this->attributes['src_remote_server_id']) ? $this->attributes['src_remote_server_id'] : null;
    }
    /**
     * Source Remote Server name
     *
     * @return string|null
     */
    public function getSrcRemoteServerName()
    {
        return isset($this->attributes['src_remote_server_name']) ? $this->attributes['src_remote_server_name'] : null;
    }
    /**
     * Destination Remote Server ID
     *
     * @return int|null
     */
    public function getDestRemoteServerId()
    {
        return isset($this->attributes['dest_remote_server_id']) ? $this->attributes['dest_remote_server_id'] : null;
    }
    /**
     * Destination Remote Server name
     *
     * @return string|null
     */
    public function getDestRemoteServerName()
    {
        return isset($this->attributes['dest_remote_server_name']) ? $this->attributes['dest_remote_server_name'] : null;
    }
    /**
     * Operation Type
     *
     * @return string|null
     */
    public function getOperation()
    {
        return isset($this->attributes['operation']) ? $this->attributes['operation'] : null;
    }
    /**
     * Error message, if applicable
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        return isset($this->attributes['error_message']) ? $this->attributes['error_message'] : null;
    }
    /**
     * Error operation, if applicable
     *
     * @return string|null
     */
    public function getErrorOperation()
    {
        return isset($this->attributes['error_operation']) ? $this->attributes['error_operation'] : null;
    }
    /**
     * Error type, if applicable
     *
     * @return string|null
     */
    public function getErrorType()
    {
        return isset($this->attributes['error_type']) ? $this->attributes['error_type'] : null;
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
     * Duration (in milliseconds)
     *
     * @return int|null
     */
    public function getDurationMs()
    {
        return isset($this->attributes['duration_ms']) ? $this->attributes['duration_ms'] : null;
    }
    /**
     * Data Length in Bytes. Present for upload actions that transfer data.
     *
     * @return int|null
     */
    public function getBytesUploaded()
    {
        return isset($this->attributes['bytes_uploaded']) ? $this->attributes['bytes_uploaded'] : null;
    }
    /**
     * Data Length in Bytes. Present for download actions that transfer data.
     *
     * @return int|null
     */
    public function getBytesDownloaded()
    {
        return isset($this->attributes['bytes_downloaded']) ? $this->attributes['bytes_downloaded'] : null;
    }
    /**
     * Number of entries returned for a folder list action.
     *
     * @return int|null
     */
    public function getListCount()
    {
        return isset($this->attributes['list_count']) ? $this->attributes['list_count'] : null;
    }
    /**
     * Start Time of Action
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Outbound Connection Logs
     *
     * GET /outbound_connection_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `operation`, `status`, `src_remote_server_id`, `dest_remote_server_id`, `path`, `client_ip` or `created_at`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ operation ]`, `[ status ]`, `[ src_remote_server_id ]`, `[ dest_remote_server_id ]`, `[ path ]`, `[ client_ip ]`, `[ created_at ]`, `[ operation, status ]`, `[ operation, src_remote_server_id ]`, `[ operation, dest_remote_server_id ]`, `[ operation, path ]`, `[ operation, client_ip ]`, `[ operation, created_at ]`, `[ status, src_remote_server_id ]`, `[ status, dest_remote_server_id ]`, `[ status, path ]`, `[ status, client_ip ]`, `[ status, created_at ]`, `[ src_remote_server_id, dest_remote_server_id ]`, `[ src_remote_server_id, path ]`, `[ src_remote_server_id, client_ip ]`, `[ src_remote_server_id, created_at ]`, `[ dest_remote_server_id, path ]`, `[ dest_remote_server_id, client_ip ]`, `[ dest_remote_server_id, created_at ]`, `[ path, client_ip ]`, `[ path, created_at ]`, `[ client_ip, created_at ]`, `[ operation, status, src_remote_server_id ]`, `[ operation, status, dest_remote_server_id ]`, `[ operation, status, path ]`, `[ operation, status, client_ip ]`, `[ operation, status, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id ]`, `[ operation, src_remote_server_id, path ]`, `[ operation, src_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, created_at ]`, `[ operation, dest_remote_server_id, path ]`, `[ operation, dest_remote_server_id, client_ip ]`, `[ operation, dest_remote_server_id, created_at ]`, `[ operation, path, client_ip ]`, `[ operation, path, created_at ]`, `[ operation, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id ]`, `[ status, src_remote_server_id, path ]`, `[ status, src_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, created_at ]`, `[ status, dest_remote_server_id, path ]`, `[ status, dest_remote_server_id, client_ip ]`, `[ status, dest_remote_server_id, created_at ]`, `[ status, path, client_ip ]`, `[ status, path, created_at ]`, `[ status, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, created_at ]`, `[ src_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, path, created_at ]`, `[ src_remote_server_id, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip ]`, `[ dest_remote_server_id, path, created_at ]`, `[ dest_remote_server_id, client_ip, created_at ]`, `[ path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id ]`, `[ operation, status, src_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, created_at ]`, `[ operation, status, dest_remote_server_id, path ]`, `[ operation, status, dest_remote_server_id, client_ip ]`, `[ operation, status, dest_remote_server_id, created_at ]`, `[ operation, status, path, client_ip ]`, `[ operation, status, path, created_at ]`, `[ operation, status, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, src_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip ]`, `[ operation, dest_remote_server_id, path, created_at ]`, `[ operation, dest_remote_server_id, client_ip, created_at ]`, `[ operation, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ status, src_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip ]`, `[ status, dest_remote_server_id, path, created_at ]`, `[ status, dest_remote_server_id, client_ip, created_at ]`, `[ status, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ src_remote_server_id, path, client_ip, created_at ]`, `[ dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip ]`, `[ operation, status, dest_remote_server_id, path, created_at ]`, `[ operation, status, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ status, src_remote_server_id, path, client_ip, created_at ]`, `[ status, dest_remote_server_id, path, client_ip, created_at ]`, `[ src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, path, created_at ]`, `[ operation, status, src_remote_server_id, dest_remote_server_id, client_ip, created_at ]`, `[ operation, status, src_remote_server_id, path, client_ip, created_at ]`, `[ operation, status, dest_remote_server_id, path, client_ip, created_at ]`, `[ operation, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`, `[ status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]` or `[ operation, status, src_remote_server_id, dest_remote_server_id, path, client_ip, created_at ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\OutboundConnectionLog[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/outbound_connection_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new OutboundConnectionLog((array) $obj, $options);
        }

        return $return_array;
    }
}
