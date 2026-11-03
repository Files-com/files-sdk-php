<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AutomationRun is a record with information about a single execution of a given Automation.
 *
 * @property int|null $id ID.
 * @property int|null $automation_id ID of the associated Automation.
 * @property int|null $automation_version_id ID of the immutable Automation version pinned by this run.
 * @property int|null $version Pinned Automation v2 definition version.
 * @property int|null $workspace_id Workspace ID.
 * @property string|null $cancel_requested_at Date/time at which cancellation was requested.
 * @property string|null $completed_at Automation run completion/failure date/time.
 * @property string|null $created_at Automation run start date/time.
 * @property string|null $retry_at If set, this automation will be retried at this date/time due to `failure` or `partial_failure`.
 * @property string|null $retried_at If set, this Automation run was retried due to `failure` or `partial_failure`.
 * @property int|null $retried_in_run_id ID of the run that is or will be retrying this run.
 * @property int|null $retry_of_run_id ID of the original run that this run is retrying.
 * @property int|null $rerun_of_run_id ID of the run whose persisted node outputs this run reused.
 * @property string|null $rerun_from_node_id Node at which this run resumed execution.
 * @property float|null $runtime Automation run runtime.
 * @property string|null $status The status of the AutomationRun. One of `queued`, `running`, `success`, `partial_failure`, `failure`, `skipped`, or `canceled`.
 * @property int|null $successful_operations Count of successful operations.
 * @property int|null $failed_operations Count of failed operations.
 * @property object|null $definition Automation definition snapshot pinned by this run. For performance reasons, this is not provided when listing Automation runs.
 * @property object|null $node_states Status and execution stage for each node in this run. For performance reasons, this is not provided when listing Automation runs.
 * @property object[]|null $execution_nodes Execution status, timing, and bounded output summaries for each node. For performance reasons, this is not provided when listing Automation runs.
 * @property string|null $journal_url Link to the run journal artifact.
 * @property string|null $status_messages_url Link to status messages log file.
 * @package Files
 */
class AutomationRun
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
     * ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * ID of the associated Automation.
     *
     * @return int|null
     */
    public function getAutomationId()
    {
        return isset($this->attributes['automation_id']) ? $this->attributes['automation_id'] : null;
    }
    /**
     * ID of the immutable Automation version pinned by this run.
     *
     * @return int|null
     */
    public function getAutomationVersionId()
    {
        return isset($this->attributes['automation_version_id']) ? $this->attributes['automation_version_id'] : null;
    }
    /**
     * Pinned Automation v2 definition version.
     *
     * @return int|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    /**
     * Workspace ID.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Date/time at which cancellation was requested.
     *
     * @return string|null
     */
    public function getCancelRequestedAt()
    {
        return isset($this->attributes['cancel_requested_at']) ? $this->attributes['cancel_requested_at'] : null;
    }
    /**
     * Automation run completion/failure date/time.
     *
     * @return string|null
     */
    public function getCompletedAt()
    {
        return isset($this->attributes['completed_at']) ? $this->attributes['completed_at'] : null;
    }
    /**
     * Automation run start date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * If set, this automation will be retried at this date/time due to `failure` or `partial_failure`.
     *
     * @return string|null
     */
    public function getRetryAt()
    {
        return isset($this->attributes['retry_at']) ? $this->attributes['retry_at'] : null;
    }
    /**
     * If set, this Automation run was retried due to `failure` or `partial_failure`.
     *
     * @return string|null
     */
    public function getRetriedAt()
    {
        return isset($this->attributes['retried_at']) ? $this->attributes['retried_at'] : null;
    }
    /**
     * ID of the run that is or will be retrying this run.
     *
     * @return int|null
     */
    public function getRetriedInRunId()
    {
        return isset($this->attributes['retried_in_run_id']) ? $this->attributes['retried_in_run_id'] : null;
    }
    /**
     * ID of the original run that this run is retrying.
     *
     * @return int|null
     */
    public function getRetryOfRunId()
    {
        return isset($this->attributes['retry_of_run_id']) ? $this->attributes['retry_of_run_id'] : null;
    }
    /**
     * ID of the run whose persisted node outputs this run reused.
     *
     * @return int|null
     */
    public function getRerunOfRunId()
    {
        return isset($this->attributes['rerun_of_run_id']) ? $this->attributes['rerun_of_run_id'] : null;
    }
    /**
     * Node at which this run resumed execution.
     *
     * @return string|null
     */
    public function getRerunFromNodeId()
    {
        return isset($this->attributes['rerun_from_node_id']) ? $this->attributes['rerun_from_node_id'] : null;
    }
    /**
     * Automation run runtime.
     *
     * @return float|null
     */
    public function getRuntime()
    {
        return isset($this->attributes['runtime']) ? $this->attributes['runtime'] : null;
    }
    /**
     * The status of the AutomationRun. One of `queued`, `running`, `success`, `partial_failure`, `failure`, `skipped`, or `canceled`.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Count of successful operations.
     *
     * @return int|null
     */
    public function getSuccessfulOperations()
    {
        return isset($this->attributes['successful_operations']) ? $this->attributes['successful_operations'] : null;
    }
    /**
     * Count of failed operations.
     *
     * @return int|null
     */
    public function getFailedOperations()
    {
        return isset($this->attributes['failed_operations']) ? $this->attributes['failed_operations'] : null;
    }
    /**
     * Automation definition snapshot pinned by this run. For performance reasons, this is not provided when listing Automation runs.
     *
     * @return object|null
     */
    public function getDefinition()
    {
        return isset($this->attributes['definition']) ? $this->attributes['definition'] : null;
    }
    /**
     * Status and execution stage for each node in this run. For performance reasons, this is not provided when listing Automation runs.
     *
     * @return object|null
     */
    public function getNodeStates()
    {
        return isset($this->attributes['node_states']) ? $this->attributes['node_states'] : null;
    }
    /**
     * Execution status, timing, and bounded output summaries for each node. For performance reasons, this is not provided when listing Automation runs.
     *
     * @return object[]|null
     */
    public function getExecutionNodes()
    {
        return isset($this->attributes['execution_nodes']) ? $this->attributes['execution_nodes'] : null;
    }
    /**
     * Link to the run journal artifact.
     *
     * @return string|null
     */
    public function getJournalUrl()
    {
        return isset($this->attributes['journal_url']) ? $this->attributes['journal_url'] : null;
    }
    /**
     * Link to status messages log file.
     *
     * @return string|null
     */
    public function getStatusMessagesUrl()
    {
        return isset($this->attributes['status_messages_url']) ? $this->attributes['status_messages_url'] : null;
    }

    /**
     * Cancel Automation Run
     *
     * POST /automation_runs/{id}/cancel
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation Run ID.
     * @return \Files\Model\AutomationRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function cancel($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/cancel', 'POST', $params, $this->options);
        return new AutomationRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Re-run Automation from Node
     *
     * POST /automation_runs/{id}/rerun
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation Run ID.
     *     - `node_id` (string, required): Node ID at which execution should resume.
     * @return \Files\Model\AutomationRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function rerun($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (empty($params['node_id'])) {
            if (@$this->node_id) {
                $params['node_id'] = $this->node_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: node_id');
            }
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['node_id']) && !is_string($params['node_id'])) {
            throw new \Files\Exception\InvalidParameterException('$node_id must be of type string; received ' . gettype(@$params['node_id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/rerun', 'POST', $params, $this->options);
        return new AutomationRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * List Automation Runs
     *
     * GET /automation_runs
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `automation_id`, `created_at` or `status`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `status`, `workspace_id` or `automation_id`. Valid field combinations are `[ workspace_id, status ]`, `[ automation_id, status ]`, `[ workspace_id, automation_id ]` or `[ workspace_id, automation_id, status ]`.
     *     - `automation_id` (int, required): ID of the associated Automation.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationRun[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (empty($params['automation_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
        }

        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['automation_id']) && !is_int($params['automation_id'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_id must be of type int; received ' . gettype(@$params['automation_id']));
        }

        $response = Api::sendRequest('/automation_runs', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new AutomationRun(Api::modelAttributes($obj, $response->status), $options);
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
        if (empty($params['automation_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
        }

        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['automation_id']) && !is_int($params['automation_id'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_id must be of type int; received ' . gettype(@$params['automation_id']));
        }

        return \Files\Page::fetch('/automation_runs', $params, $options, AutomationRun::class);
    }

    /**
     * Show Automation Run
     *
     * GET /automation_runs/{id}
     *
     * @param int $id Automation Run ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationRun
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

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AutomationRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Automation Run
     *
     * GET /automation_runs/{id}
     *
     * Alias of find().
     *
     * @param int $id Automation Run ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Show Automation Run Node
     *
     * GET /automation_runs/{id}/node
     *
     * @param int $id Automation Run ID.
     * @param array $params Request parameters.
     *     - `node_id` (string, required): Node ID from the pinned Automation definition.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationExecutionNode
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function findNode($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (empty($params['node_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: node_id');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['node_id']) && !is_string($params['node_id'])) {
            throw new \Files\Exception\InvalidParameterException('$node_id must be of type string; received ' . gettype(@$params['node_id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/node', 'GET', $params, $options);

        return new AutomationExecutionNode(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
