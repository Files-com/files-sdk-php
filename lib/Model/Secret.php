<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Secret stores named, typed secret material for later use by features that reference the Secret by ID.
 *
 * @property int|null $id Secret ID.
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace.
 * @property string|null $name Secret name.
 * @property string|null $description Internal description for your reference.
 * @property string|null $secret_type Secret type.
 * @property object|null $metadata Non-secret metadata for the Secret type.
 * @property string[]|null $value_field_names Names of configured secret value fields. Secret values are never returned.
 * @property string|null $created_at Secret create date/time.
 * @property string|null $updated_at Secret update date/time.
 * @package Files
 */
class Secret
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
     * Secret ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Secret ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Workspace ID. 0 means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID. 0 means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Secret name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Secret name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Internal description for your reference.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Internal description for your reference.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Secret type.
     *
     * @return string|null
     */
    public function getSecretType()
    {
        return isset($this->attributes['secret_type']) ? $this->attributes['secret_type'] : null;
    }

    /**
     * Secret type.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSecretType($value)
    {
        return $this->attributes['secret_type'] = $value;
    }
    /**
     * Non-secret metadata for the Secret type.
     *
     * @return object|null
     */
    public function getMetadata()
    {
        return isset($this->attributes['metadata']) ? $this->attributes['metadata'] : null;
    }

    /**
     * Non-secret metadata for the Secret type.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setMetadata($value)
    {
        return $this->attributes['metadata'] = $value;
    }
    /**
     * Names of configured secret value fields. Secret values are never returned.
     *
     * @return string[]|null
     */
    public function getValueFieldNames()
    {
        return isset($this->attributes['value_field_names']) ? $this->attributes['value_field_names'] : null;
    }

    /**
     * Names of configured secret value fields. Secret values are never returned.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setValueFieldNames($value)
    {
        return $this->attributes['value_field_names'] = $value;
    }
    /**
     * Secret create date/time.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Secret update date/time.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Update Secret
     *
     * PATCH /secrets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Secret ID.
     *     - `name` (string, optional): Secret name.
     *     - `description` (string, optional): Internal description for your reference.
     *     - `secret_type` (string, optional): Secret type. Allowed values: basic, token, headers, certificate, key_value.
     *     - `metadata` (array|object, optional): Non-secret metadata for the Secret type.
     * @return \Files\Model\Secret
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

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['secret_type']) && !is_string($params['secret_type'])) {
            throw new \Files\Exception\InvalidParameterException('$secret_type must be of type string; received ' . gettype(@$params['secret_type']));
        }

        $response = Api::sendRequest('/secrets/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Secret((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Secret
     *
     * DELETE /secrets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Secret ID.
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

        $response = Api::sendRequest('/secrets/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Secret
     *
     * DELETE /secrets/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Secret ID.
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
     * List Secrets
     *
     * GET /secrets
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `name` or `secret_type`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`, `name` or `secret_type`. Valid field combinations are `[ workspace_id, name ]`, `[ workspace_id, secret_type ]`, `[ secret_type, name ]` or `[ workspace_id, secret_type, name ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Secret[]
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

        $response = Api::sendRequest('/secrets', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Secret((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Secret
     *
     * GET /secrets/{id}
     *
     * @param int $id Secret ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Secret
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

        $response = Api::sendRequest('/secrets/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Secret((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Secret
     *
     * GET /secrets/{id}
     *
     * Alias of find().
     *
     * @param int $id Secret ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Secret
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Secret
     *
     * POST /secrets
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Secret name.
     *     - `description` (string, optional): Internal description for your reference.
     *     - `secret_type` (string, required): Secret type. Allowed values: basic, token, headers, certificate, key_value.
     *     - `metadata` (array|object, optional): Non-secret metadata for the Secret type.
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Secret
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['secret_type'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: secret_type');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['secret_type']) && !is_string($params['secret_type'])) {
            throw new \Files\Exception\InvalidParameterException('$secret_type must be of type string; received ' . gettype(@$params['secret_type']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/secrets', 'POST', $params, $options);

        return new Secret((array) (@$response->data ?: []), $options);
    }
}
