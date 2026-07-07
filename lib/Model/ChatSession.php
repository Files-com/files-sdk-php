<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ChatSession
 *
 * @package Files
 */
class ChatSession
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
    // string # Chat Session ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // string # Short AI-generated chat title.
    public function getTitle()
    {
        return @$this->attributes['title'];
    }
    // int64 # User ID.
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }
    // int64 # AI Task ID. Present when the conversation was started by an AI Task.
    public function getAiTaskId()
    {
        return @$this->attributes['ai_task_id'];
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }
    // date-time # Most recent chat activity date/time.
    public function getLastActiveAt()
    {
        return @$this->attributes['last_active_at'];
    }
    // date-time # Chat session creation date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // array(object) # Visible conversation messages in this chat session. For performance reasons, this is not provided when listing chat sessions.
    public function getMessages()
    {
        return @$this->attributes['messages'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `ai_task_id`, `user_id` or `workspace_id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/chat_sessions', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ChatSession((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - string - Chat Session ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_string(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type string; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/chat_sessions/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ChatSession((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
