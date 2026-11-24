<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AutomationVersion is an immutable revision of an Automation v2 graph.
 *
 * @property int|null $id Automation revision ID.
 * @property int|null $automation_id ID of the Automation that owns this revision.
 * @property int|null $workspace_id Workspace that owns the Automation. Zero is the Default Workspace.
 * @property int|null $version Revision number within the Automation.
 * @property string|null $created_at Time the revision was saved.
 * @property int|null $user_id ID of the user who saved the revision, when available.
 * @property string|null $username Username recorded when the revision was saved, when available.
 * @property int|null $api_key_id ID of the API key used to save the revision, when available.
 * @property object|null $definition Saved Automation v2 graph definition. Included when retrieving a single revision.
 * @package Files
 */
class AutomationVersion
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
     * Automation revision ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * ID of the Automation that owns this revision.
     *
     * @return int|null
     */
    public function getAutomationId()
    {
        return isset($this->attributes['automation_id']) ? $this->attributes['automation_id'] : null;
    }
    /**
     * Workspace that owns the Automation. Zero is the Default Workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Revision number within the Automation.
     *
     * @return int|null
     */
    public function getVersion()
    {
        return isset($this->attributes['version']) ? $this->attributes['version'] : null;
    }
    /**
     * Time the revision was saved.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * ID of the user who saved the revision, when available.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * Username recorded when the revision was saved, when available.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    /**
     * ID of the API key used to save the revision, when available.
     *
     * @return int|null
     */
    public function getApiKeyId()
    {
        return isset($this->attributes['api_key_id']) ? $this->attributes['api_key_id'] : null;
    }
    /**
     * Saved Automation v2 graph definition. Included when retrieving a single revision.
     *
     * @return object|null
     */
    public function getDefinition()
    {
        return isset($this->attributes['definition']) ? $this->attributes['definition'] : null;
    }

    /**
     * Restore an Automation v2 definition as a new current version
     *
     * POST /automation_versions/{id}/restore
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Automation Version ID.
     * @return \Files\Model\Automation
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function restore($params = [])
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

        $response = Api::sendRequest('/automation_versions/' . rawurlencode(strval(@$params['id'])) . '/restore', 'POST', $params, $this->options);
        return new Automation(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * List Automation Versions
     *
     * GET /automation_versions
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     *     - `automation_id` (int, required): ID of the Automation that owns this revision.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationVersion[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (empty($params['automation_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: automation_id');
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

        $response = Api::sendRequest('/automation_versions', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new AutomationVersion(Api::modelAttributes($obj, $response->status), $options);
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

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['automation_id']) && !is_int($params['automation_id'])) {
            throw new \Files\Exception\InvalidParameterException('$automation_id must be of type int; received ' . gettype(@$params['automation_id']));
        }

        return \Files\Page::fetch('/automation_versions', $params, $options, AutomationVersion::class);
    }

    /**
     * Show Automation Version
     *
     * GET /automation_versions/{id}
     *
     * @param int $id Automation Version ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationVersion
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

        $response = Api::sendRequest('/automation_versions/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AutomationVersion(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Automation Version
     *
     * GET /automation_versions/{id}
     *
     * Alias of find().
     *
     * @param int $id Automation Version ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AutomationVersion
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
