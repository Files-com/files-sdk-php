<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Database Destination lets an Automation append rows to an existing table.
 *
 * @property int|null $id Database Destination ID.
 * @property int|null $workspace_id Workspace ID. 0 means the default workspace.
 * @property string|null $name Name of the destination.
 * @property string|null $provider Database provider.
 * @property object|null $config Provider settings for the destination. Use the schema matching provider. The connection address and table are fixed after creation.
 * @property int|null $secret_id ID of a Secret in the same Workspace. Use a Token Secret for Snowflake, a service account Key/Value Secret for BigQuery, or a Username/Password Secret for a database login.
 * @property string|null $created_at When the destination was created.
 * @property string|null $updated_at When the destination was last updated.
 * @package Files
 */
class DatabaseDestination
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
     * Database Destination ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Database Destination ID.
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
     * Name of the destination.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name of the destination.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Database provider.
     *
     * @return string|null
     */
    public function getProvider()
    {
        return isset($this->attributes['provider']) ? $this->attributes['provider'] : null;
    }

    /**
     * Database provider.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setProvider($value)
    {
        return $this->attributes['provider'] = $value;
    }
    /**
     * Provider settings for the destination. Use the schema matching provider. The connection address and table are fixed after creation.
     *
     * @return object|null
     */
    public function getConfig()
    {
        return isset($this->attributes['config']) ? $this->attributes['config'] : null;
    }

    /**
     * Provider settings for the destination. Use the schema matching provider. The connection address and table are fixed after creation.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setConfig($value)
    {
        return $this->attributes['config'] = $value;
    }
    /**
     * ID of a Secret in the same Workspace. Use a Token Secret for Snowflake, a service account Key/Value Secret for BigQuery, or a Username/Password Secret for a database login.
     *
     * @return int|null
     */
    public function getSecretId()
    {
        return isset($this->attributes['secret_id']) ? $this->attributes['secret_id'] : null;
    }

    /**
     * ID of a Secret in the same Workspace. Use a Token Secret for Snowflake, a service account Key/Value Secret for BigQuery, or a Username/Password Secret for a database login.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSecretId($value)
    {
        return $this->attributes['secret_id'] = $value;
    }
    /**
     * When the destination was created.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * When the destination was last updated.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // DatabaseDestination with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Database Destination
     *
     * PATCH /database_destinations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Database Destination ID.
     *     - `name` (string, optional): Name of the destination.
     *     - `secret_id` (int, optional): ID of a Secret in the same Workspace. Use a Token Secret for Snowflake, a service account Key/Value Secret for BigQuery, or a Username/Password Secret for a database login.
     *     - `config` (array|object, optional): Provider settings for the destination. Use the schema matching provider. The connection address and table are fixed after creation.
     * @return \Files\Model\DatabaseDestination
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['secret_id']) && !is_int($params['secret_id'])) {
            throw new \Files\Exception\InvalidParameterException('$secret_id must be of type int; received ' . gettype(@$params['secret_id']));
        }

        $response = Api::sendRequest('/database_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new DatabaseDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Database Destination
     *
     * DELETE /database_destinations/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Database Destination ID.
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/database_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Database Destination
     *
     * DELETE /database_destinations/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Database Destination ID.
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
     * List Database Destinations
     *
     * GET /database_destinations
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id` and `name`. Valid field combinations are `[ workspace_id, name ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DatabaseDestination[]
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

        $response = Api::sendRequest('/database_destinations', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new DatabaseDestination(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/database_destinations', $params, $options, DatabaseDestination::class);
    }

    /**
     * Show Database Destination
     *
     * GET /database_destinations/{id}
     *
     * @param int $id Database Destination ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DatabaseDestination
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

        $response = Api::sendRequest('/database_destinations/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new DatabaseDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Database Destination
     *
     * GET /database_destinations/{id}
     *
     * Alias of find().
     *
     * @param int $id Database Destination ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DatabaseDestination
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Database Destination
     *
     * POST /database_destinations
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Name of the destination.
     *     - `provider` (string, required): Database provider. Allowed values: snowflake, bigquery, supabase, postgresql, mysql.
     *     - `config` (array|object, required): Provider settings for the destination. Use the schema matching provider. The connection address and table are fixed after creation.
     *     - `secret_id` (int, required): ID of a Secret in the same Workspace. Use a Token Secret for Snowflake, a service account Key/Value Secret for BigQuery, or a Username/Password Secret for a database login.
     *     - `workspace_id` (int, optional): Workspace ID. 0 means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DatabaseDestination
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['provider'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: provider');
        }

        if (empty($params['config'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: config');
        }

        if (empty($params['secret_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: secret_id');
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['provider']) && !is_string($params['provider'])) {
            throw new \Files\Exception\InvalidParameterException('$provider must be of type string; received ' . gettype(@$params['provider']));
        }

        if (isset($params['secret_id']) && !is_int($params['secret_id'])) {
            throw new \Files\Exception\InvalidParameterException('$secret_id must be of type int; received ' . gettype(@$params['secret_id']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/database_destinations', 'POST', $params, $options);

        return new DatabaseDestination(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
