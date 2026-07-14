<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AutomationRun
 *
 * @package Files
 */
class AutomationRun
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
    // int64 # ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # ID of the associated Automation.
    public function getAutomationId()
    {
        return @$this->attributes['automation_id'];
    }
    // int64 # ID of the immutable Automation version pinned by this run.
    public function getAutomationVersionId()
    {
        return @$this->attributes['automation_version_id'];
    }
    // int64 # Workspace ID.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }
    // date-time # Date/time at which cancellation was requested.
    public function getCancelRequestedAt()
    {
        return @$this->attributes['cancel_requested_at'];
    }
    // date-time # Automation run completion/failure date/time.
    public function getCompletedAt()
    {
        return @$this->attributes['completed_at'];
    }
    // date-time # Automation run start date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # If set, this automation will be retried at this date/time due to `failure` or `partial_failure`.
    public function getRetryAt()
    {
        return @$this->attributes['retry_at'];
    }
    // date-time # If set, this Automation run was retried due to `failure` or `partial_failure`.
    public function getRetriedAt()
    {
        return @$this->attributes['retried_at'];
    }
    // int64 # ID of the run that is or will be retrying this run.
    public function getRetriedInRunId()
    {
        return @$this->attributes['retried_in_run_id'];
    }
    // int64 # ID of the original run that this run is retrying.
    public function getRetryOfRunId()
    {
        return @$this->attributes['retry_of_run_id'];
    }
    // int64 # ID of the run whose persisted node outputs this run reused.
    public function getRerunOfRunId()
    {
        return @$this->attributes['rerun_of_run_id'];
    }
    // string # Node at which this run resumed execution.
    public function getRerunFromNodeId()
    {
        return @$this->attributes['rerun_from_node_id'];
    }
    // double # Automation run runtime.
    public function getRuntime()
    {
        return @$this->attributes['runtime'];
    }
    // string # The status of the AutomationRun. One of `queued`, `running`, `success`, `partial_failure`, `failure`, `skipped`, or `canceled`.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // int64 # Count of successful operations.
    public function getSuccessfulOperations()
    {
        return @$this->attributes['successful_operations'];
    }
    // int64 # Count of failed operations.
    public function getFailedOperations()
    {
        return @$this->attributes['failed_operations'];
    }
    // object # Automation definition snapshot pinned by this run. For performance reasons, this is not provided when listing Automation runs.
    public function getDefinition()
    {
        return @$this->attributes['definition'];
    }
    // object # Status and execution stage for each node in this run. For performance reasons, this is not provided when listing Automation runs.
    public function getNodeStates()
    {
        return @$this->attributes['node_states'];
    }
    // array(object) # Execution status, timing, and bounded output summaries for each node. For performance reasons, this is not provided when listing Automation runs.
    public function getExecutionNodes()
    {
        return @$this->attributes['execution_nodes'];
    }
    // string # Link to the run journal artifact.
    public function getJournalUrl()
    {
        return @$this->attributes['journal_url'];
    }
    // string # Link to status messages log file.
    public function getStatusMessagesUrl()
    {
        return @$this->attributes['status_messages_url'];
    }

    // Cancel Automation Run
    public function cancel($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/cancel', 'POST', $params, $this->options);
        return new AutomationRun((array) (@$response->data ?: []), $this->options);
    }

    // Re-run Automation from Node
    //
    // Parameters:
    //   node_id (required) - string - Node ID at which execution should resume.
    public function rerun($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!@$params['node_id']) {
            if (@$this->node_id) {
                $params['node_id'] = $this->node_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: node_id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['node_id'] && !is_string(@$params['node_id'])) {
            throw new \Files\Exception\InvalidParameterException('$node_id must be of type string; received ' . gettype(@$params['node_id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/rerun', 'POST', $params, $this->options);
        return new AutomationRun((array) (@$response->data ?: []), $this->options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `automation_id`, `created_at` or `status`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `status`, `workspace_id` or `automation_id`. Valid field combinations are `[ workspace_id, status ]`, `[ automation_id, status ]`, `[ workspace_id, automation_id ]` or `[ workspace_id, automation_id, status ]`.
    //   automation_id (required) - int64 - ID of the associated Automation.
    public static function all($params = [], $options = [])
    {
        if (!@$params['automation_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['automation_id'] && !is_int(@$params['automation_id'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_id must be of type int; received ' . gettype(@$params['automation_id']));
        }

        $response = Api::sendRequest('/automation_runs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AutomationRun((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Automation Run ID.
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

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AutomationRun((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   id (required) - int64 - Automation Run ID.
    //   node_id (required) - string - Node ID from the pinned Automation definition.
    public static function findNode($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!@$params['node_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: node_id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['node_id'] && !is_string(@$params['node_id'])) {
            throw new \Files\Exception\InvalidParameterException('$node_id must be of type string; received ' . gettype(@$params['node_id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/node', 'GET', $params, $options);

        return new AutomationExecutionNode((array) (@$response->data ?: []), $options);
    }
}
