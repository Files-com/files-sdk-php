<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A ChatSession represents one conversation with the Files.com AI Assistant.
 *
 * @property string|null $id Chat Session ID.
 * @property string|null $title Short AI-generated chat title.
 * @property int|null $user_id User ID.
 * @property int|null $ai_task_id AI Task ID. Present when the conversation was started by an AI Task.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $last_active_at Most recent chat activity date/time.
 * @property string|null $created_at Chat session creation date/time.
 * @property object[]|null $messages Visible conversation messages in this chat session. For performance reasons, this is not provided when listing chat sessions.
 * @package Files
 */
class ChatSession
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
     * Chat Session ID.
     *
     * @return string|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Short AI-generated chat title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return isset($this->attributes['title']) ? $this->attributes['title'] : null;
    }
    /**
     * User ID.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * AI Task ID. Present when the conversation was started by an AI Task.
     *
     * @return int|null
     */
    public function getAiTaskId()
    {
        return isset($this->attributes['ai_task_id']) ? $this->attributes['ai_task_id'] : null;
    }
    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Most recent chat activity date/time.
     *
     * @return string|null
     */
    public function getLastActiveAt()
    {
        return isset($this->attributes['last_active_at']) ? $this->attributes['last_active_at'] : null;
    }
    /**
     * Chat session creation date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Visible conversation messages in this chat session. For performance reasons, this is not provided when listing chat sessions.
     *
     * @return object[]|null
     */
    public function getMessages()
    {
        return isset($this->attributes['messages']) ? $this->attributes['messages'] : null;
    }

    /**
     * List Chat Sessions
     *
     * GET /chat_sessions
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `ai_task_id`, `user_id` or `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChatSession[]
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

        $response = Api::sendRequest('/chat_sessions', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new ChatSession(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/chat_sessions', $params, $options, ChatSession::class);
    }

    /**
     * Show Chat Session
     *
     * GET /chat_sessions/{id}
     *
     * @param string $id Chat Session ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChatSession
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

        if (isset($params['id']) && !is_string($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type string; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/chat_sessions/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ChatSession(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Chat Session
     *
     * GET /chat_sessions/{id}
     *
     * Alias of find().
     *
     * @param string $id Chat Session ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChatSession
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
