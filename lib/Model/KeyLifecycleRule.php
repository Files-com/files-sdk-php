<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A KeyLifecycleRule represents a rule that applies to API keys, GPG keys, and SSH keys (also called User Public Keys) based on their inactivity or age.
 *
 * @property int|null $id Key Lifecycle Rule ID
 * @property string|null $key_type Key type for which the rule will apply (gpg, ssh, or api).
 * @property int|null $inactivity_days Number of days of inactivity before the rule applies.
 * @property int|null $expiration_days Number of days after creation before an SSH key expires. Applies only to SSH keys.
 * @property bool|null $apply_to_all_workspaces If true, a default-workspace rule also applies to keys in all workspaces.
 * @property string|null $name Key Lifecycle Rule name
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @package Files
 */
class KeyLifecycleRule
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
     * Key Lifecycle Rule ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Key Lifecycle Rule ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Key type for which the rule will apply (gpg, ssh, or api).
     *
     * @return string|null
     */
    public function getKeyType()
    {
        return isset($this->attributes['key_type']) ? $this->attributes['key_type'] : null;
    }

    /**
     * Key type for which the rule will apply (gpg, ssh, or api).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setKeyType($value)
    {
        return $this->attributes['key_type'] = $value;
    }
    /**
     * Number of days of inactivity before the rule applies.
     *
     * @return int|null
     */
    public function getInactivityDays()
    {
        return isset($this->attributes['inactivity_days']) ? $this->attributes['inactivity_days'] : null;
    }

    /**
     * Number of days of inactivity before the rule applies.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInactivityDays($value)
    {
        return $this->attributes['inactivity_days'] = $value;
    }
    /**
     * Number of days after creation before an SSH key expires. Applies only to SSH keys.
     *
     * @return int|null
     */
    public function getExpirationDays()
    {
        return isset($this->attributes['expiration_days']) ? $this->attributes['expiration_days'] : null;
    }

    /**
     * Number of days after creation before an SSH key expires. Applies only to SSH keys.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setExpirationDays($value)
    {
        return $this->attributes['expiration_days'] = $value;
    }
    /**
     * If true, a default-workspace rule also applies to keys in all workspaces.
     *
     * @return bool|null
     */
    public function getApplyToAllWorkspaces()
    {
        return isset($this->attributes['apply_to_all_workspaces']) ? $this->attributes['apply_to_all_workspaces'] : null;
    }

    /**
     * If true, a default-workspace rule also applies to keys in all workspaces.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    /**
     * Key Lifecycle Rule name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Key Lifecycle Rule name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
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

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // KeyLifecycleRule with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Key Lifecycle Rule
     *
     * PATCH /key_lifecycle_rules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Key Lifecycle Rule ID.
     *     - `apply_to_all_workspaces` (bool, optional): If true, a default-workspace rule also applies to keys in all workspaces.
     *     - `expiration_days` (int, optional): Number of days after creation before an SSH key expires. Applies only to SSH keys.
     *     - `key_type` (string, optional): Key type for which the rule will apply (gpg, ssh, or api). Allowed values: gpg, ssh, api.
     *     - `inactivity_days` (int, optional): Number of days of inactivity before the rule applies.
     *     - `name` (string, optional): Key Lifecycle Rule name
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\KeyLifecycleRule
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

        if (isset($params['apply_to_all_workspaces']) && !is_scalar($params['apply_to_all_workspaces'])) {
            throw new \Files\Exception\InvalidParameterException('$apply_to_all_workspaces must be of type boolean; received ' . gettype(@$params['apply_to_all_workspaces']));
        }

        if (isset($params['expiration_days']) && !is_int($params['expiration_days'])) {
            throw new \Files\Exception\InvalidParameterException('$expiration_days must be of type int; received ' . gettype(@$params['expiration_days']));
        }

        if (isset($params['key_type']) && !is_string($params['key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$key_type must be of type string; received ' . gettype(@$params['key_type']));
        }

        if (isset($params['inactivity_days']) && !is_int($params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/key_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new KeyLifecycleRule(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Key Lifecycle Rule
     *
     * DELETE /key_lifecycle_rules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Key Lifecycle Rule ID.
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

        $response = Api::sendRequest('/key_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Key Lifecycle Rule
     *
     * DELETE /key_lifecycle_rules/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Key Lifecycle Rule ID.
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
     * List Key Lifecycle Rules
     *
     * GET /key_lifecycle_rules
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `key_type`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `key_type` and `workspace_id`. Valid field combinations are `[ workspace_id, key_type ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\KeyLifecycleRule[]
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

        $response = Api::sendRequest('/key_lifecycle_rules', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new KeyLifecycleRule(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/key_lifecycle_rules', $params, $options, KeyLifecycleRule::class);
    }

    /**
     * Show Key Lifecycle Rule
     *
     * GET /key_lifecycle_rules/{id}
     *
     * @param int $id Key Lifecycle Rule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\KeyLifecycleRule
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

        $response = Api::sendRequest('/key_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new KeyLifecycleRule(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Key Lifecycle Rule
     *
     * GET /key_lifecycle_rules/{id}
     *
     * Alias of find().
     *
     * @param int $id Key Lifecycle Rule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\KeyLifecycleRule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Key Lifecycle Rule
     *
     * POST /key_lifecycle_rules
     *
     * @param array $params Request parameters.
     *     - `apply_to_all_workspaces` (bool, optional): If true, a default-workspace rule also applies to keys in all workspaces.
     *     - `expiration_days` (int, optional): Number of days after creation before an SSH key expires. Applies only to SSH keys.
     *     - `key_type` (string, optional): Key type for which the rule will apply (gpg, ssh, or api). Allowed values: gpg, ssh, api.
     *     - `inactivity_days` (int, optional): Number of days of inactivity before the rule applies.
     *     - `name` (string, optional): Key Lifecycle Rule name
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\KeyLifecycleRule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['apply_to_all_workspaces']) && !is_scalar($params['apply_to_all_workspaces'])) {
            throw new \Files\Exception\InvalidParameterException('$apply_to_all_workspaces must be of type boolean; received ' . gettype(@$params['apply_to_all_workspaces']));
        }

        if (isset($params['expiration_days']) && !is_int($params['expiration_days'])) {
            throw new \Files\Exception\InvalidParameterException('$expiration_days must be of type int; received ' . gettype(@$params['expiration_days']));
        }

        if (isset($params['key_type']) && !is_string($params['key_type'])) {
            throw new \Files\Exception\InvalidParameterException('$key_type must be of type string; received ' . gettype(@$params['key_type']));
        }

        if (isset($params['inactivity_days']) && !is_int($params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/key_lifecycle_rules', 'POST', $params, $options);

        return new KeyLifecycleRule(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
