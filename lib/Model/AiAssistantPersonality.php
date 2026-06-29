<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class AiAssistantPersonality
 *
 * @package Files
 */
class AiAssistantPersonality
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
    // int64 # AI Assistant Personality ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Workspace ID. `0` means the default workspace.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # System prompt injected into the in-app AI Assistant.
    public function getSystemPrompt()
    {
        return @$this->attributes['system_prompt'];
    }

    public function setSystemPrompt($value)
    {
        return $this->attributes['system_prompt'] = $value;
    }
    // boolean # Whether this personality is the default personality for the Workspace.
    public function getUseByDefault()
    {
        return @$this->attributes['use_by_default'];
    }

    public function setUseByDefault($value)
    {
        return $this->attributes['use_by_default'] = $value;
    }
    // boolean # If true, this default-workspace personality can apply to users in all workspaces.
    public function getApplyToAllWorkspaces()
    {
        return @$this->attributes['apply_to_all_workspaces'];
    }

    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    // date-time # Creation time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # Last update time.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   apply_to_all_workspaces - boolean - If true, this default-workspace personality can apply to users in all workspaces.
    //   system_prompt - string - System prompt injected into the in-app AI Assistant.
    //   use_by_default - boolean - Whether this personality is the default personality for the Workspace.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
    public function update($params = [])
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

        if (@$params['system_prompt'] && !is_string(@$params['system_prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$system_prompt must be of type string; received ' . gettype(@$params['system_prompt']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new AiAssistantPersonality((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
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

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            $new_obj = $this->update($this->attributes);
            $this->attributes = $new_obj->attributes;
            return true;
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AiAssistantPersonality((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Ai Assistant Personality ID.
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

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AiAssistantPersonality((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   apply_to_all_workspaces - boolean - If true, this default-workspace personality can apply to users in all workspaces.
    //   system_prompt (required) - string - System prompt injected into the in-app AI Assistant.
    //   use_by_default - boolean - Whether this personality is the default personality for the Workspace.
    //   workspace_id - int64 - Workspace ID. `0` means the default workspace.
    public static function create($params = [], $options = [])
    {
        if (!@$params['system_prompt']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: system_prompt');
        }

        if (@$params['system_prompt'] && !is_string(@$params['system_prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$system_prompt must be of type string; received ' . gettype(@$params['system_prompt']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities', 'POST', $params, $options);

        return new AiAssistantPersonality((array) (@$response->data ?: []), $options);
    }
}
