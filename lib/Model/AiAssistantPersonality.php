<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AI Assistant Personality defines a system prompt used to customize the in-app AI Assistant.
 *
 * @property int|null $id AI Assistant Personality ID.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string|null $name AI Assistant Personality name.
 * @property string|null $system_prompt System prompt injected into the in-app AI Assistant.
 * @property bool|null $use_by_default Whether this personality is the default personality for the Workspace.
 * @property bool|null $apply_to_all_workspaces If true, this default-workspace personality can apply to users in all workspaces.
 * @property string|null $created_at Creation time.
 * @property string|null $updated_at Last update time.
 * @package Files
 */
class AiAssistantPersonality
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
     * AI Assistant Personality ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * AI Assistant Personality ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
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
     * Workspace ID. `0` means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * AI Assistant Personality name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * AI Assistant Personality name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * System prompt injected into the in-app AI Assistant.
     *
     * @return string|null
     */
    public function getSystemPrompt()
    {
        return isset($this->attributes['system_prompt']) ? $this->attributes['system_prompt'] : null;
    }

    /**
     * System prompt injected into the in-app AI Assistant.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSystemPrompt($value)
    {
        return $this->attributes['system_prompt'] = $value;
    }
    /**
     * Whether this personality is the default personality for the Workspace.
     *
     * @return bool|null
     */
    public function getUseByDefault()
    {
        return isset($this->attributes['use_by_default']) ? $this->attributes['use_by_default'] : null;
    }

    /**
     * Whether this personality is the default personality for the Workspace.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUseByDefault($value)
    {
        return $this->attributes['use_by_default'] = $value;
    }
    /**
     * If true, this default-workspace personality can apply to users in all workspaces.
     *
     * @return bool|null
     */
    public function getApplyToAllWorkspaces()
    {
        return isset($this->attributes['apply_to_all_workspaces']) ? $this->attributes['apply_to_all_workspaces'] : null;
    }

    /**
     * If true, this default-workspace personality can apply to users in all workspaces.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    /**
     * Creation time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Last update time.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Update Ai Assistant Personality
     *
     * PATCH /ai_assistant_personalities/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Assistant Personality ID.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace personality can apply to users in all workspaces.
     *     - `name` (string, optional): AI Assistant Personality name.
     *     - `system_prompt` (string, optional): System prompt injected into the in-app AI Assistant.
     *     - `use_by_default` (bool, optional): Whether this personality is the default personality for the Workspace.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\AiAssistantPersonality
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
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

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['system_prompt']) && !is_string($params['system_prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$system_prompt must be of type string; received ' . gettype(@$params['system_prompt']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new AiAssistantPersonality((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Ai Assistant Personality
     *
     * DELETE /ai_assistant_personalities/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Assistant Personality ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
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

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Ai Assistant Personality
     *
     * DELETE /ai_assistant_personalities/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Ai Assistant Personality ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
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


    /**
     * List Ai Assistant Personalities
     *
     * GET /ai_assistant_personalities
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiAssistantPersonality[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AiAssistantPersonality((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Ai Assistant Personality
     *
     * GET /ai_assistant_personalities/{id}
     *
     * @param int $id Ai Assistant Personality ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiAssistantPersonality
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new AiAssistantPersonality((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Ai Assistant Personality
     *
     * GET /ai_assistant_personalities/{id}
     *
     * Alias of find().
     *
     * @param int $id Ai Assistant Personality ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiAssistantPersonality
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Ai Assistant Personality
     *
     * POST /ai_assistant_personalities
     *
     * @param array $params Request parameters.
     *     - `apply_to_all_workspaces` (bool, optional): If true, this default-workspace personality can apply to users in all workspaces.
     *     - `name` (string, required): AI Assistant Personality name.
     *     - `system_prompt` (string, required): System prompt injected into the in-app AI Assistant.
     *     - `use_by_default` (bool, optional): Whether this personality is the default personality for the Workspace.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\AiAssistantPersonality
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['system_prompt'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: system_prompt');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['system_prompt']) && !is_string($params['system_prompt'])) {
            throw new \Files\Exception\InvalidParameterException('$system_prompt must be of type string; received ' . gettype(@$params['system_prompt']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/ai_assistant_personalities', 'POST', $params, $options);

        return new AiAssistantPersonality((array) (@$response->data ?: []), $options);
    }
}
