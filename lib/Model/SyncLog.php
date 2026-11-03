<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A SyncLog is an audit log for monitoring and troubleshooting operations made by Remote Server Sync.
 *
 * @property string|null $timestamp Start Time of Action. Deprecrated: Use created_at.
 * @property int|null $sync_id Sync ID
 * @property int|null $external_event_id External Event ID used by legacy syncs
 * @property int|null $sync_run_id Sync Run ID
 * @property string|null $error_type Error type, if applicable
 * @property string|null $message Message
 * @property string|null $operation Operation type
 * @property string|null $path File path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $size File size
 * @property string|null $file_type File type
 * @property string|null $status Status
 * @property string|null $created_at Start Time of Action
 * @package Files
 */
class SyncLog
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
     * Sync ID
     *
     * @return int|null
     */
    public function getSyncId()
    {
        return isset($this->attributes['sync_id']) ? $this->attributes['sync_id'] : null;
    }
    /**
     * External Event ID used by legacy syncs
     *
     * @return int|null
     */
    public function getExternalEventId()
    {
        return isset($this->attributes['external_event_id']) ? $this->attributes['external_event_id'] : null;
    }
    /**
     * Sync Run ID
     *
     * @return int|null
     */
    public function getSyncRunId()
    {
        return isset($this->attributes['sync_run_id']) ? $this->attributes['sync_run_id'] : null;
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
     * Message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * Operation type
     *
     * @return string|null
     */
    public function getOperation()
    {
        return isset($this->attributes['operation']) ? $this->attributes['operation'] : null;
    }
    /**
     * File path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * File size
     *
     * @return int|null
     */
    public function getSize()
    {
        return isset($this->attributes['size']) ? $this->attributes['size'] : null;
    }
    /**
     * File type
     *
     * @return string|null
     */
    public function getFileType()
    {
        return isset($this->attributes['file_type']) ? $this->attributes['file_type'] : null;
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
     * Start Time of Action
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Sync Logs
     *
     * GET /sync_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `external_event_id`, `operation`, `status`, `sync_id`, `created_at` or `sync_run_id`. Valid field combinations are `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ created_at ]`, `[ sync_run_id ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, created_at ]`, `[ external_event_id, sync_run_id ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, created_at ]`, `[ operation, sync_run_id ]`, `[ status, sync_id ]`, `[ status, created_at ]`, `[ status, sync_run_id ]`, `[ sync_id, created_at ]`, `[ sync_id, sync_run_id ]`, `[ created_at, sync_run_id ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, created_at ]`, `[ external_event_id, operation, sync_run_id ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, created_at ]`, `[ external_event_id, status, sync_run_id ]`, `[ external_event_id, sync_id, created_at ]`, `[ external_event_id, sync_id, sync_run_id ]`, `[ external_event_id, created_at, sync_run_id ]`, `[ operation, status, sync_id ]`, `[ operation, status, created_at ]`, `[ operation, status, sync_run_id ]`, `[ operation, sync_id, created_at ]`, `[ operation, sync_id, sync_run_id ]`, `[ operation, created_at, sync_run_id ]`, `[ status, sync_id, created_at ]`, `[ status, sync_id, sync_run_id ]`, `[ status, created_at, sync_run_id ]`, `[ sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, created_at ]`, `[ external_event_id, operation, status, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at ]`, `[ external_event_id, operation, sync_id, sync_run_id ]`, `[ external_event_id, operation, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at ]`, `[ external_event_id, status, sync_id, sync_run_id ]`, `[ external_event_id, status, created_at, sync_run_id ]`, `[ external_event_id, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at ]`, `[ operation, status, sync_id, sync_run_id ]`, `[ operation, status, created_at, sync_run_id ]`, `[ operation, sync_id, created_at, sync_run_id ]`, `[ status, sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id, created_at ]`, `[ external_event_id, operation, status, sync_id, sync_run_id ]`, `[ external_event_id, operation, status, created_at, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at, sync_run_id ]` or `[ external_event_id, operation, status, sync_id, created_at, sync_run_id ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ created_at ]`, `[ sync_run_id ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, created_at ]`, `[ external_event_id, sync_run_id ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, created_at ]`, `[ operation, sync_run_id ]`, `[ status, sync_id ]`, `[ status, created_at ]`, `[ status, sync_run_id ]`, `[ sync_id, created_at ]`, `[ sync_id, sync_run_id ]`, `[ created_at, sync_run_id ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, created_at ]`, `[ external_event_id, operation, sync_run_id ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, created_at ]`, `[ external_event_id, status, sync_run_id ]`, `[ external_event_id, sync_id, created_at ]`, `[ external_event_id, sync_id, sync_run_id ]`, `[ external_event_id, created_at, sync_run_id ]`, `[ operation, status, sync_id ]`, `[ operation, status, created_at ]`, `[ operation, status, sync_run_id ]`, `[ operation, sync_id, created_at ]`, `[ operation, sync_id, sync_run_id ]`, `[ operation, created_at, sync_run_id ]`, `[ status, sync_id, created_at ]`, `[ status, sync_id, sync_run_id ]`, `[ status, created_at, sync_run_id ]`, `[ sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, created_at ]`, `[ external_event_id, operation, status, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at ]`, `[ external_event_id, operation, sync_id, sync_run_id ]`, `[ external_event_id, operation, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at ]`, `[ external_event_id, status, sync_id, sync_run_id ]`, `[ external_event_id, status, created_at, sync_run_id ]`, `[ external_event_id, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at ]`, `[ operation, status, sync_id, sync_run_id ]`, `[ operation, status, created_at, sync_run_id ]`, `[ operation, sync_id, created_at, sync_run_id ]`, `[ status, sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id, created_at ]`, `[ external_event_id, operation, status, sync_id, sync_run_id ]`, `[ external_event_id, operation, status, created_at, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at, sync_run_id ]` or `[ external_event_id, operation, status, sync_id, created_at, sync_run_id ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ created_at ]`, `[ sync_run_id ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, created_at ]`, `[ external_event_id, sync_run_id ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, created_at ]`, `[ operation, sync_run_id ]`, `[ status, sync_id ]`, `[ status, created_at ]`, `[ status, sync_run_id ]`, `[ sync_id, created_at ]`, `[ sync_id, sync_run_id ]`, `[ created_at, sync_run_id ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, created_at ]`, `[ external_event_id, operation, sync_run_id ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, created_at ]`, `[ external_event_id, status, sync_run_id ]`, `[ external_event_id, sync_id, created_at ]`, `[ external_event_id, sync_id, sync_run_id ]`, `[ external_event_id, created_at, sync_run_id ]`, `[ operation, status, sync_id ]`, `[ operation, status, created_at ]`, `[ operation, status, sync_run_id ]`, `[ operation, sync_id, created_at ]`, `[ operation, sync_id, sync_run_id ]`, `[ operation, created_at, sync_run_id ]`, `[ status, sync_id, created_at ]`, `[ status, sync_id, sync_run_id ]`, `[ status, created_at, sync_run_id ]`, `[ sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, created_at ]`, `[ external_event_id, operation, status, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at ]`, `[ external_event_id, operation, sync_id, sync_run_id ]`, `[ external_event_id, operation, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at ]`, `[ external_event_id, status, sync_id, sync_run_id ]`, `[ external_event_id, status, created_at, sync_run_id ]`, `[ external_event_id, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at ]`, `[ operation, status, sync_id, sync_run_id ]`, `[ operation, status, created_at, sync_run_id ]`, `[ operation, sync_id, created_at, sync_run_id ]`, `[ status, sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id, created_at ]`, `[ external_event_id, operation, status, sync_id, sync_run_id ]`, `[ external_event_id, operation, status, created_at, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at, sync_run_id ]` or `[ external_event_id, operation, status, sync_id, created_at, sync_run_id ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ created_at ]`, `[ sync_run_id ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, created_at ]`, `[ external_event_id, sync_run_id ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, created_at ]`, `[ operation, sync_run_id ]`, `[ status, sync_id ]`, `[ status, created_at ]`, `[ status, sync_run_id ]`, `[ sync_id, created_at ]`, `[ sync_id, sync_run_id ]`, `[ created_at, sync_run_id ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, created_at ]`, `[ external_event_id, operation, sync_run_id ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, created_at ]`, `[ external_event_id, status, sync_run_id ]`, `[ external_event_id, sync_id, created_at ]`, `[ external_event_id, sync_id, sync_run_id ]`, `[ external_event_id, created_at, sync_run_id ]`, `[ operation, status, sync_id ]`, `[ operation, status, created_at ]`, `[ operation, status, sync_run_id ]`, `[ operation, sync_id, created_at ]`, `[ operation, sync_id, sync_run_id ]`, `[ operation, created_at, sync_run_id ]`, `[ status, sync_id, created_at ]`, `[ status, sync_id, sync_run_id ]`, `[ status, created_at, sync_run_id ]`, `[ sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, created_at ]`, `[ external_event_id, operation, status, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at ]`, `[ external_event_id, operation, sync_id, sync_run_id ]`, `[ external_event_id, operation, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at ]`, `[ external_event_id, status, sync_id, sync_run_id ]`, `[ external_event_id, status, created_at, sync_run_id ]`, `[ external_event_id, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at ]`, `[ operation, status, sync_id, sync_run_id ]`, `[ operation, status, created_at, sync_run_id ]`, `[ operation, sync_id, created_at, sync_run_id ]`, `[ status, sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id, created_at ]`, `[ external_event_id, operation, status, sync_id, sync_run_id ]`, `[ external_event_id, operation, status, created_at, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at, sync_run_id ]` or `[ external_event_id, operation, status, sync_id, created_at, sync_run_id ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ created_at ]`, `[ sync_run_id ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, created_at ]`, `[ external_event_id, sync_run_id ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, created_at ]`, `[ operation, sync_run_id ]`, `[ status, sync_id ]`, `[ status, created_at ]`, `[ status, sync_run_id ]`, `[ sync_id, created_at ]`, `[ sync_id, sync_run_id ]`, `[ created_at, sync_run_id ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, created_at ]`, `[ external_event_id, operation, sync_run_id ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, created_at ]`, `[ external_event_id, status, sync_run_id ]`, `[ external_event_id, sync_id, created_at ]`, `[ external_event_id, sync_id, sync_run_id ]`, `[ external_event_id, created_at, sync_run_id ]`, `[ operation, status, sync_id ]`, `[ operation, status, created_at ]`, `[ operation, status, sync_run_id ]`, `[ operation, sync_id, created_at ]`, `[ operation, sync_id, sync_run_id ]`, `[ operation, created_at, sync_run_id ]`, `[ status, sync_id, created_at ]`, `[ status, sync_id, sync_run_id ]`, `[ status, created_at, sync_run_id ]`, `[ sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, created_at ]`, `[ external_event_id, operation, status, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at ]`, `[ external_event_id, operation, sync_id, sync_run_id ]`, `[ external_event_id, operation, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at ]`, `[ external_event_id, status, sync_id, sync_run_id ]`, `[ external_event_id, status, created_at, sync_run_id ]`, `[ external_event_id, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at ]`, `[ operation, status, sync_id, sync_run_id ]`, `[ operation, status, created_at, sync_run_id ]`, `[ operation, sync_id, created_at, sync_run_id ]`, `[ status, sync_id, created_at, sync_run_id ]`, `[ external_event_id, operation, status, sync_id, created_at ]`, `[ external_event_id, operation, status, sync_id, sync_run_id ]`, `[ external_event_id, operation, status, created_at, sync_run_id ]`, `[ external_event_id, operation, sync_id, created_at, sync_run_id ]`, `[ external_event_id, status, sync_id, created_at, sync_run_id ]`, `[ operation, status, sync_id, created_at, sync_run_id ]` or `[ external_event_id, operation, status, sync_id, created_at, sync_run_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SyncLog[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/sync_logs', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new SyncLog(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/sync_logs', $params, $options, SyncLog::class);
    }
}
