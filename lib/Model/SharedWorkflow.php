<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * SharedWorkflow API model.
 *
 * @property int|null $id Shared Workflow ID.
 * @property string|null $name Name displayed to recipients.
 * @property string|null $description Description displayed to recipients.
 * @property string|null $submission_revision Revision required when submitting a Shared Workflow run.
 * @property object[]|null $form_fields Current custom questions in display order.
 * @package Files
 */
class SharedWorkflow
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
     * Shared Workflow ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Name displayed to recipients.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }
    /**
     * Description displayed to recipients.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }
    /**
     * Revision required when submitting a Shared Workflow run.
     *
     * @return string|null
     */
    public function getSubmissionRevision()
    {
        return isset($this->attributes['submission_revision']) ? $this->attributes['submission_revision'] : null;
    }
    /**
     * Current custom questions in display order.
     *
     * @return object[]|null
     */
    public function getFormFields()
    {
        return isset($this->attributes['form_fields']) ? $this->attributes['form_fields'] : null;
    }

    /**
     * Run a Shared Workflow
     *
     * POST /shared_workflows/{id}/run_workflow
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Shared Workflow ID.
     *     - `submission_revision` (string, required): Revision returned when opening the Shared Workflow.
     *     - `submission_id` (string, required): Client-generated UUID used to retry the same submission without starting another run.
     *     - `file_path` (string, required): Path of a file the recipient can read, including a file uploaded to a Snapshot created for this workflow.
     * @return \Files\Model\AutomationRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function runWorkflow($params = [])
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

        if (empty($params['submission_revision'])) {
            if (@$this->submission_revision) {
                $params['submission_revision'] = $this->submission_revision;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: submission_revision');
            }
        }

        if (empty($params['submission_id'])) {
            if (@$this->submission_id) {
                $params['submission_id'] = $this->submission_id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: submission_id');
            }
        }

        if (empty($params['file_path'])) {
            if (@$this->file_path) {
                $params['file_path'] = $this->file_path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: file_path');
            }
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['submission_revision']) && !is_string($params['submission_revision'])) {
            throw new \Files\Exception\InvalidParameterException('$submission_revision must be of type string; received ' . gettype(@$params['submission_revision']));
        }

        if (isset($params['submission_id']) && !is_string($params['submission_id'])) {
            throw new \Files\Exception\InvalidParameterException('$submission_id must be of type string; received ' . gettype(@$params['submission_id']));
        }

        if (isset($params['file_path']) && !is_string($params['file_path'])) {
            throw new \Files\Exception\InvalidParameterException('$file_path must be of type string; received ' . gettype(@$params['file_path']));
        }

        $response = Api::sendRequest('/shared_workflows/' . rawurlencode(strval(@$params['id'])) . '/run_workflow', 'POST', $params, $this->options);
        return new AutomationRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * List Shared Workflows
     *
     * GET /shared_workflows
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SharedWorkflow[]
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

        $response = Api::sendRequest('/shared_workflows', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new SharedWorkflow(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/shared_workflows', $params, $options, SharedWorkflow::class);
    }

    /**
     * Show Shared Workflow
     *
     * GET /shared_workflows/{id}
     *
     * @param int $id Shared Workflow ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SharedWorkflow
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

        $response = Api::sendRequest('/shared_workflows/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new SharedWorkflow(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Shared Workflow
     *
     * GET /shared_workflows/{id}
     *
     * Alias of find().
     *
     * @param int $id Shared Workflow ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\SharedWorkflow
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Show a Shared Workflow run started by the recipient
     *
     * GET /shared_workflows/{id}/runs/{run_id}
     *
     * @param int $id Shared Workflow ID.
     * @param int $run_id ID of the run returned by the Shared Workflow submission.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationRun
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function findRun($id, $run_id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;
        $params['run_id'] = $run_id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (empty($params['run_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: run_id');
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['run_id']) && !is_int($params['run_id'])) {
            throw new \Files\Exception\InvalidParameterException('$run_id must be of type int; received ' . gettype(@$params['run_id']));
        }

        $response = Api::sendRequest('/shared_workflows/' . rawurlencode(strval(@$params['id'])) . '/runs/' . rawurlencode(strval(@$params['run_id'])) . '', 'GET', $params, $options);

        return new AutomationRun(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
