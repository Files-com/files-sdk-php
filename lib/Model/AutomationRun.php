<?php

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
    // int64 # ID.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # ID of the associated Automation.
    public function getAutomationId()
    {
        return isset($this->attributes['automation_id']) ? $this->attributes['automation_id'] : null;
    }
    // int64 # ID of the immutable Automation version pinned by this run.
    public function getAutomationVersionId()
    {
        return isset($this->attributes['automation_version_id']) ? $this->attributes['automation_version_id'] : null;
    }
    // int64 # Pinned Automation v2 definition version.
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    // int64 # Workspace ID.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    // date-time # Date/time at which cancellation was requested.
    public function getCancelRequestedAt()
    {
        return isset($this->attributes['cancel_requested_at']) ? $this->attributes['cancel_requested_at'] : null;
    }
    // date-time # Automation run completion/failure date/time.
    public function getCompletedAt()
    {
        return isset($this->attributes['completed_at']) ? $this->attributes['completed_at'] : null;
    }
    // date-time # Automation run start date/time.
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // date-time # If set, this automation will be retried at this date/time due to `failure` or `partial_failure`.
    public function getRetryAt()
    {
        return isset($this->attributes['retry_at']) ? $this->attributes['retry_at'] : null;
    }
    // date-time # If set, this Automation run was retried due to `failure` or `partial_failure`.
    public function getRetriedAt()
    {
        return isset($this->attributes['retried_at']) ? $this->attributes['retried_at'] : null;
    }
    // int64 # ID of the run that is or will be retrying this run.
    public function getRetriedInRunId()
    {
        return isset($this->attributes['retried_in_run_id']) ? $this->attributes['retried_in_run_id'] : null;
    }
    // int64 # ID of the original run that this run is retrying.
    public function getRetryOfRunId()
    {
        return isset($this->attributes['retry_of_run_id']) ? $this->attributes['retry_of_run_id'] : null;
    }
    // int64 # ID of the run whose persisted node outputs this run reused.
    public function getRerunOfRunId()
    {
        return isset($this->attributes['rerun_of_run_id']) ? $this->attributes['rerun_of_run_id'] : null;
    }
    // string # Node at which this run resumed execution.
    public function getRerunFromNodeId()
    {
        return isset($this->attributes['rerun_from_node_id']) ? $this->attributes['rerun_from_node_id'] : null;
    }
    // double # Automation run runtime.
    public function getRuntime()
    {
        return isset($this->attributes['runtime']) ? $this->attributes['runtime'] : null;
    }
    // string # The status of the AutomationRun. One of `queued`, `running`, `success`, `partial_failure`, `failure`, `skipped`, or `canceled`.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // int64 # Count of successful operations.
    public function getSuccessfulOperations()
    {
        return isset($this->attributes['successful_operations']) ? $this->attributes['successful_operations'] : null;
    }
    // int64 # Count of failed operations.
    public function getFailedOperations()
    {
        return isset($this->attributes['failed_operations']) ? $this->attributes['failed_operations'] : null;
    }
    // object # Automation definition snapshot pinned by this run. For performance reasons, this is not provided when listing Automation runs.
    public function getDefinition()
    {
        return isset($this->attributes['definition']) ? $this->attributes['definition'] : null;
    }
    // object # Status and execution stage for each node in this run. For performance reasons, this is not provided when listing Automation runs.
    public function getNodeStates()
    {
        return isset($this->attributes['node_states']) ? $this->attributes['node_states'] : null;
    }
    // array(object) # Execution status, timing, and bounded output summaries for each node. For performance reasons, this is not provided when listing Automation runs.
    public function getExecutionNodes()
    {
        return isset($this->attributes['execution_nodes']) ? $this->attributes['execution_nodes'] : null;
    }
    // string # Link to the run journal artifact.
    public function getJournalUrl()
    {
        return isset($this->attributes['journal_url']) ? $this->attributes['journal_url'] : null;
    }
    // string # Link to status messages log file.
    public function getStatusMessagesUrl()
    {
        return isset($this->attributes['status_messages_url']) ? $this->attributes['status_messages_url'] : null;
    }

    // Cancel Automation Run
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

        if (!empty($params['id']) && !is_int($params['id'])) {
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['node_id']) && !is_string($params['node_id'])) {
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
        if (empty($params['automation_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['automation_id']) && !is_int($params['automation_id'])) {
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

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
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

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (empty($params['node_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: node_id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['node_id']) && !is_string($params['node_id'])) {
            throw new \Files\Exception\InvalidParameterException('$node_id must be of type string; received ' . gettype(@$params['node_id']));
        }

        $response = Api::sendRequest('/automation_runs/' . rawurlencode(strval(@$params['id'])) . '/node', 'GET', $params, $options);

        return new AutomationExecutionNode((array) (@$response->data ?: []), $options);
    }
}
