<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A SyncRun represents a single execution (run) of a Sync job.
 *
 * @property int|null $id SyncRun ID
 * @property string|null $body Log or summary body for this run
 * @property int|null $bytes_synced Total bytes synced in this run
 * @property int|null $compared_files Number of files compared
 * @property int|null $compared_folders Number of folders compared
 * @property string|null $completed_at When this run was completed
 * @property string|null $created_at When this run was created
 * @property string|null $dest_remote_server_type Destination remote server type, if any
 * @property bool|null $dry_run Whether this run was a dry run (no actual changes made)
 * @property int|null $errored_files Number of files that errored
 * @property int|null $estimated_bytes_count Estimated bytes count for this run
 * @property string[]|null $event_errors Array of errors encountered during the run
 * @property string|null $log_url Link to external log file.
 * @property float|null $runtime Total runtime in seconds
 * @property int|null $site_id Site ID
 * @property int|null $workspace_id Workspace ID
 * @property string|null $src_remote_server_type Source remote server type, if any
 * @property string|null $status Status of the sync run (success, failure, partial_failure, in_progress, skipped)
 * @property int|null $successful_files Number of files successfully synced
 * @property int|null $sync_id ID of the Sync this run belongs to
 * @property string|null $sync_name Name of the Sync this run belongs to
 * @property string|null $updated_at When this run was last updated
 * @property object[]|null $live_transfers Array of in-progress file transfers with progress data. Only present when the sync run status is in_progress.
 * @package Files
 */
class SyncRun
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
     * SyncRun ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Log or summary body for this run
     *
     * @return string|null
     */
    public function getBody()
    {
        return isset($this->attributes['body']) ? $this->attributes['body'] : null;
    }
    /**
     * Total bytes synced in this run
     *
     * @return int|null
     */
    public function getBytesSynced()
    {
        return isset($this->attributes['bytes_synced']) ? $this->attributes['bytes_synced'] : null;
    }
    /**
     * Number of files compared
     *
     * @return int|null
     */
    public function getComparedFiles()
    {
        return isset($this->attributes['compared_files']) ? $this->attributes['compared_files'] : null;
    }
    /**
     * Number of folders compared
     *
     * @return int|null
     */
    public function getComparedFolders()
    {
        return isset($this->attributes['compared_folders']) ? $this->attributes['compared_folders'] : null;
    }
    /**
     * When this run was completed
     *
     * @return string|null
     */
    public function getCompletedAt()
    {
        return isset($this->attributes['completed_at']) ? $this->attributes['completed_at'] : null;
    }
    /**
     * When this run was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Destination remote server type, if any
     *
     * @return string|null
     */
    public function getDestRemoteServerType()
    {
        return isset($this->attributes['dest_remote_server_type']) ? $this->attributes['dest_remote_server_type'] : null;
    }
    /**
     * Whether this run was a dry run (no actual changes made)
     *
     * @return bool|null
     */
    public function getDryRun()
    {
        return isset($this->attributes['dry_run']) ? $this->attributes['dry_run'] : null;
    }
    /**
     * Number of files that errored
     *
     * @return int|null
     */
    public function getErroredFiles()
    {
        return isset($this->attributes['errored_files']) ? $this->attributes['errored_files'] : null;
    }
    /**
     * Estimated bytes count for this run
     *
     * @return int|null
     */
    public function getEstimatedBytesCount()
    {
        return isset($this->attributes['estimated_bytes_count']) ? $this->attributes['estimated_bytes_count'] : null;
    }
    /**
     * Array of errors encountered during the run
     *
     * @return string[]|null
     */
    public function getEventErrors()
    {
        return isset($this->attributes['event_errors']) ? $this->attributes['event_errors'] : null;
    }
    /**
     * Link to external log file.
     *
     * @return string|null
     */
    public function getLogUrl()
    {
        return isset($this->attributes['log_url']) ? $this->attributes['log_url'] : null;
    }
    /**
     * Total runtime in seconds
     *
     * @return float|null
     */
    public function getRuntime()
    {
        return isset($this->attributes['runtime']) ? $this->attributes['runtime'] : null;
    }
    /**
     * Site ID
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }
    /**
     * Workspace ID
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Source remote server type, if any
     *
     * @return string|null
     */
    public function getSrcRemoteServerType()
    {
        return isset($this->attributes['src_remote_server_type']) ? $this->attributes['src_remote_server_type'] : null;
    }
    /**
     * Status of the sync run (success, failure, partial_failure, in_progress, skipped)
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Number of files successfully synced
     *
     * @return int|null
     */
    public function getSuccessfulFiles()
    {
        return isset($this->attributes['successful_files']) ? $this->attributes['successful_files'] : null;
    }
    /**
     * ID of the Sync this run belongs to
     *
     * @return int|null
     */
    public function getSyncId()
    {
        return isset($this->attributes['sync_id']) ? $this->attributes['sync_id'] : null;
    }
    /**
     * Name of the Sync this run belongs to
     *
     * @return string|null
     */
    public function getSyncName()
    {
        return isset($this->attributes['sync_name']) ? $this->attributes['sync_name'] : null;
    }
    /**
     * When this run was last updated
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }
    /**
     * Array of in-progress file transfers with progress data. Only present when the sync run status is in_progress.
     *
     * @return object[]|null
     */
    public function getLiveTransfers()
    {
        return isset($this->attributes['live_transfers']) ? $this->attributes['live_transfers'] : null;
    }

    /**
     * List Sync Runs
     *
     * GET /sync_runs
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id`, `sync_id`, `created_at` or `status`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `status`, `dry_run`, `workspace_id`, `src_remote_server_type`, `dest_remote_server_type` or `sync_id`. Valid field combinations are `[ status, created_at ]`, `[ workspace_id, created_at ]`, `[ src_remote_server_type, created_at ]`, `[ dest_remote_server_type, created_at ]`, `[ sync_id, created_at ]`, `[ workspace_id, status ]`, `[ src_remote_server_type, status ]`, `[ dest_remote_server_type, status ]`, `[ sync_id, status ]`, `[ workspace_id, src_remote_server_type ]`, `[ workspace_id, dest_remote_server_type ]`, `[ workspace_id, sync_id ]`, `[ workspace_id, status, created_at ]`, `[ src_remote_server_type, status, created_at ]`, `[ dest_remote_server_type, status, created_at ]`, `[ sync_id, status, created_at ]`, `[ workspace_id, src_remote_server_type, created_at ]`, `[ workspace_id, dest_remote_server_type, created_at ]`, `[ workspace_id, sync_id, created_at ]`, `[ workspace_id, src_remote_server_type, status ]`, `[ workspace_id, dest_remote_server_type, status ]`, `[ workspace_id, sync_id, status ]`, `[ workspace_id, src_remote_server_type, status, created_at ]`, `[ workspace_id, dest_remote_server_type, status, created_at ]` or `[ workspace_id, sync_id, status, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SyncRun[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/sync_runs', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new SyncRun(Api::modelAttributes($obj, $response->status), $options);
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
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/sync_runs', $params, $options, SyncRun::class);
    }

    /**
     * Show Sync Run
     *
     * GET /sync_runs/{id}
     *
     * @param int $id Sync Run ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SyncRun
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

        $response = Api::sendRequest('/sync_runs/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SyncRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Sync Run
     *
     * GET /sync_runs/{id}
     *
     * Alias of find().
     *
     * @param int $id Sync Run ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SyncRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
