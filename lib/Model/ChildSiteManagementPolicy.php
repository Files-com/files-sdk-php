<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Child Site Management Policy is a centralized policy defined by a parent site to enforce consistent configurations across child sites.
 *
 * @property int|null $id Policy ID.
 * @property string|null $policy_type Type of policy. Valid values: `settings`.
 * @property string|null $name Name for this policy.
 * @property string|null $description Description for this policy.
 * @property object|null $value Policy configuration data. On a Child Site, contains only managed setting names with null values. Settings policies accept site settings plus an optional `folder_behaviors` array for parent-managed root behaviors on child sites. For more information, refer to the Value Hash section of the developer documentation.
 * @property int[]|null $applied_child_site_ids IDs of child sites that this policy has been applied to. This field is read-only.
 * @property int[]|null $skip_child_site_ids IDs of child sites excluded from this default policy.
 * @property int[]|null $child_site_ids IDs of child sites explicitly assigned to this non-default policy.
 * @property bool|null $default_policy Whether this policy applies to child sites not explicitly assigned to another policy.
 * @property string|null $created_at When this policy was created.
 * @property string|null $updated_at When this policy was last updated.
 * @package Files
 */
class ChildSiteManagementPolicy
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
     * Policy ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Policy ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Type of policy.  Valid values: `settings`.
     *
     * @return string|null
     */
    public function getPolicyType()
    {
        return isset($this->attributes['policy_type']) ? $this->attributes['policy_type'] : null;
    }

    /**
     * Type of policy.  Valid values: `settings`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPolicyType($value)
    {
        return $this->attributes['policy_type'] = $value;
    }
    /**
     * Name for this policy.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this policy.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Description for this policy.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Description for this policy.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Policy configuration data. On a Child Site, contains only managed setting names with null values. Settings policies accept site settings plus an optional `folder_behaviors` array for parent-managed root behaviors on child sites. For more information, refer to the Value Hash section of the developer documentation.
     *
     * @return object|null
     */
    public function getValue()
    {
        return isset($this->attributes['value']) ? $this->attributes['value'] : null;
    }

    /**
     * Policy configuration data. On a Child Site, contains only managed setting names with null values. Settings policies accept site settings plus an optional `folder_behaviors` array for parent-managed root behaviors on child sites. For more information, refer to the Value Hash section of the developer documentation.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    /**
     * IDs of child sites that this policy has been applied to. This field is read-only.
     *
     * @return int[]|null
     */
    public function getAppliedChildSiteIds()
    {
        return isset($this->attributes['applied_child_site_ids']) ? $this->attributes['applied_child_site_ids'] : null;
    }

    /**
     * IDs of child sites that this policy has been applied to. This field is read-only.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setAppliedChildSiteIds($value)
    {
        return $this->attributes['applied_child_site_ids'] = $value;
    }
    /**
     * IDs of child sites excluded from this default policy.
     *
     * @return int[]|null
     */
    public function getSkipChildSiteIds()
    {
        return isset($this->attributes['skip_child_site_ids']) ? $this->attributes['skip_child_site_ids'] : null;
    }

    /**
     * IDs of child sites excluded from this default policy.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setSkipChildSiteIds($value)
    {
        return $this->attributes['skip_child_site_ids'] = $value;
    }
    /**
     * IDs of child sites explicitly assigned to this non-default policy.
     *
     * @return int[]|null
     */
    public function getChildSiteIds()
    {
        return isset($this->attributes['child_site_ids']) ? $this->attributes['child_site_ids'] : null;
    }

    /**
     * IDs of child sites explicitly assigned to this non-default policy.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setChildSiteIds($value)
    {
        return $this->attributes['child_site_ids'] = $value;
    }
    /**
     * Whether this policy applies to child sites not explicitly assigned to another policy.
     *
     * @return bool|null
     */
    public function getDefaultPolicy()
    {
        return isset($this->attributes['default_policy']) ? $this->attributes['default_policy'] : null;
    }

    /**
     * Whether this policy applies to child sites not explicitly assigned to another policy.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDefaultPolicy($value)
    {
        return $this->attributes['default_policy'] = $value;
    }
    /**
     * When this policy was created.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * When this policy was last updated.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * Update Child Site Management Policy
     *
     * PATCH /child_site_management_policies/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Child Site Management Policy ID.
     *     - `value` (array|object, optional): Policy configuration data. Attributes differ by policy type. For more information, refer to the Value Hash section of the developer documentation.
     *     - `skip_child_site_ids` (int[], optional): IDs of child sites excluded from this default policy.
     *     - `child_site_ids` (int[], optional): IDs of child sites explicitly assigned to this non-default policy.
     *     - `default_policy` (bool, optional): Whether this policy applies to child sites not explicitly assigned to another policy.
     *     - `policy_type` (string, optional): Type of policy.  Valid values: `settings`. Allowed values: settings.
     *     - `name` (string, optional): Name for this policy.
     *     - `description` (string, optional): Description for this policy.
     * @return \Files\Model\ChildSiteManagementPolicy
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

        if (!empty($params['skip_child_site_ids']) && !is_array($params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        if (!empty($params['child_site_ids']) && !is_array($params['child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$child_site_ids must be of type array; received ' . gettype(@$params['child_site_ids']));
        }

        if (!empty($params['policy_type']) && !is_string($params['policy_type'])) {
            throw new \Files\Exception\InvalidParameterException('$policy_type must be of type string; received ' . gettype(@$params['policy_type']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        $response = Api::sendRequest('/child_site_management_policies/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Child Site Management Policy
     *
     * DELETE /child_site_management_policies/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Child Site Management Policy ID.
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

        $response = Api::sendRequest('/child_site_management_policies/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Child Site Management Policy
     *
     * DELETE /child_site_management_policies/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Child Site Management Policy ID.
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
     * List Child Site Management Policies. On a Child Site, returns only the policy covering that site. Its value contains managed setting names with null values
     *
     * GET /child_site_management_policies
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChildSiteManagementPolicy[]
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

        $response = Api::sendRequest('/child_site_management_policies', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ChildSiteManagementPolicy((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Child Site Management Policy
     *
     * GET /child_site_management_policies/{id}
     *
     * @param int $id Child Site Management Policy ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChildSiteManagementPolicy
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

        $response = Api::sendRequest('/child_site_management_policies/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Child Site Management Policy
     *
     * GET /child_site_management_policies/{id}
     *
     * Alias of find().
     *
     * @param int $id Child Site Management Policy ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChildSiteManagementPolicy
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Child Site Management Policy
     *
     * POST /child_site_management_policies
     *
     * @param array $params Request parameters.
     *     - `value` (array|object, optional): Policy configuration data. Attributes differ by policy type. For more information, refer to the Value Hash section of the developer documentation.
     *     - `skip_child_site_ids` (int[], optional): IDs of child sites excluded from this default policy.
     *     - `child_site_ids` (int[], optional): IDs of child sites explicitly assigned to this non-default policy.
     *     - `default_policy` (bool, optional): Whether this policy applies to child sites not explicitly assigned to another policy.
     *     - `policy_type` (string, required): Type of policy.  Valid values: `settings`. Allowed values: settings.
     *     - `name` (string, optional): Name for this policy.
     *     - `description` (string, optional): Description for this policy.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ChildSiteManagementPolicy
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['policy_type'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: policy_type');
        }

        if (!empty($params['skip_child_site_ids']) && !is_array($params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        if (!empty($params['child_site_ids']) && !is_array($params['child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$child_site_ids must be of type array; received ' . gettype(@$params['child_site_ids']));
        }

        if (!empty($params['policy_type']) && !is_string($params['policy_type'])) {
            throw new \Files\Exception\InvalidParameterException('$policy_type must be of type string; received ' . gettype(@$params['policy_type']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        $response = Api::sendRequest('/child_site_management_policies', 'POST', $params, $options);

        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $options);
    }
}
