<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ExpectationEvaluation
 *
 * @package Files
 */
class ExpectationEvaluation
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
    // int64 # ExpectationEvaluation ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    // int64 # Expectation ID.
    public function getExpectationId()
    {
        return isset($this->attributes['expectation_id']) ? $this->attributes['expectation_id'] : null;
    }
    // string # Evaluation status.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // string # How the evaluation window was opened.
    public function getOpenedVia()
    {
        return isset($this->attributes['opened_via']) ? $this->attributes['opened_via'] : null;
    }
    // date-time # When the evaluation row was opened.
    public function getOpenedAt()
    {
        return isset($this->attributes['opened_at']) ? $this->attributes['opened_at'] : null;
    }
    // date-time # Logical start of the candidate window.
    public function getWindowStartAt()
    {
        return isset($this->attributes['window_start_at']) ? $this->attributes['window_start_at'] : null;
    }
    // date-time # Actual candidate cutoff boundary for the window.
    public function getWindowEndAt()
    {
        return isset($this->attributes['window_end_at']) ? $this->attributes['window_end_at'] : null;
    }
    // date-time # Logical due boundary for schedule-driven windows.
    public function getDeadlineAt()
    {
        return isset($this->attributes['deadline_at']) ? $this->attributes['deadline_at'] : null;
    }
    // date-time # Logical cutoff for late acceptance, when present.
    public function getLateAcceptanceCutoffAt()
    {
        return isset($this->attributes['late_acceptance_cutoff_at']) ? $this->attributes['late_acceptance_cutoff_at'] : null;
    }
    // date-time # Hard stop after which the window may not remain open.
    public function getHardCloseAt()
    {
        return isset($this->attributes['hard_close_at']) ? $this->attributes['hard_close_at'] : null;
    }
    // date-time # When the evaluation row was finalized.
    public function getClosedAt()
    {
        return isset($this->attributes['closed_at']) ? $this->attributes['closed_at'] : null;
    }
    // array(object) # Captured evidence for files that matched the window.
    public function getMatchedFiles()
    {
        return isset($this->attributes['matched_files']) ? $this->attributes['matched_files'] : null;
    }
    // array(object) # Captured evidence for required files that were missing.
    public function getMissingFiles()
    {
        return isset($this->attributes['missing_files']) ? $this->attributes['missing_files'] : null;
    }
    // array(string) # Captured criteria failures for the window, including file-scoped FTS errors and returned validation details.
    public function getCriteriaErrors()
    {
        return isset($this->attributes['criteria_errors']) ? $this->attributes['criteria_errors'] : null;
    }
    // object # Compact evaluator summary payload.
    public function getSummary()
    {
        return isset($this->attributes['summary']) ? $this->attributes['summary'] : null;
    }
    // date-time # Creation time.
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // date-time # Last update time.
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `expectation_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `expectation_id` and `workspace_id`. Valid field combinations are `[ workspace_id, expectation_id ]`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/expectation_evaluations', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExpectationEvaluation((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Expectation Evaluation ID.
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

        $response = Api::sendRequest('/expectation_evaluations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ExpectationEvaluation((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
