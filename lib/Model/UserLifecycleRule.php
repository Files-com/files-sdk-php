<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A UserLifecycleRule represents a rule that applies to users based on their inactivity, state and authentication method.
 *
 * @property int|null $id User Lifecycle Rule ID
 * @property string|null $authentication_method User authentication method for which the rule will apply. Use `all_non_sso` to target every non-SSO authentication method with one rule.
 * @property int[]|null $group_ids Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
 * @property string|null $action Action to take on inactive users (disable or delete)
 * @property int|null $inactivity_days Number of days of inactivity before the rule applies
 * @property bool|null $include_folder_admins If true, the rule will apply to folder admins.
 * @property bool|null $include_site_admins If true, the rule includes Site Administrators, who always belong to the Default Workspace. Can only be enabled when `workspace_id` is `0`.
 * @property bool|null $apply_to_all_workspaces If true, a Default Workspace rule also applies to users in all Custom Workspaces. Can only be enabled when `workspace_id` is `0`.
 * @property string|null $name User Lifecycle Rule name
 * @property bool|null $notify_users If true, users will be emailed before the rule disables or deletes them.
 * @property string|null $partner_tag If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
 * @property int|null $site_id Site ID
 * @property int|null $workspace_id Workspace whose users the rule applies to. `0` means the Default Workspace. A Custom Workspace rule applies only to users who belong to that Workspace, regardless of access granted to other users.
 * @property string|null $user_state State of the users to apply the rule to (inactive or disabled)
 * @property string|null $user_tag If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
 * @package Files
 */
class UserLifecycleRule
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
     * User Lifecycle Rule ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * User Lifecycle Rule ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * User authentication method for which the rule will apply. Use `all_non_sso` to target every non-SSO authentication method with one rule.
     *
     * @return string|null
     */
    public function getAuthenticationMethod()
    {
        return isset($this->attributes['authentication_method']) ? $this->attributes['authentication_method'] : null;
    }

    /**
     * User authentication method for which the rule will apply. Use `all_non_sso` to target every non-SSO authentication method with one rule.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAuthenticationMethod($value)
    {
        return $this->attributes['authentication_method'] = $value;
    }
    /**
     * Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
     *
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    /**
     * Action to take on inactive users (disable or delete)
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }

    /**
     * Action to take on inactive users (disable or delete)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAction($value)
    {
        return $this->attributes['action'] = $value;
    }
    /**
     * Number of days of inactivity before the rule applies
     *
     * @return int|null
     */
    public function getInactivityDays()
    {
        return isset($this->attributes['inactivity_days']) ? $this->attributes['inactivity_days'] : null;
    }

    /**
     * Number of days of inactivity before the rule applies
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInactivityDays($value)
    {
        return $this->attributes['inactivity_days'] = $value;
    }
    /**
     * If true, the rule will apply to folder admins.
     *
     * @return bool|null
     */
    public function getIncludeFolderAdmins()
    {
        return isset($this->attributes['include_folder_admins']) ? $this->attributes['include_folder_admins'] : null;
    }

    /**
     * If true, the rule will apply to folder admins.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setIncludeFolderAdmins($value)
    {
        return $this->attributes['include_folder_admins'] = $value;
    }
    /**
     * If true, the rule includes Site Administrators, who always belong to the Default Workspace. Can only be enabled when `workspace_id` is `0`.
     *
     * @return bool|null
     */
    public function getIncludeSiteAdmins()
    {
        return isset($this->attributes['include_site_admins']) ? $this->attributes['include_site_admins'] : null;
    }

    /**
     * If true, the rule includes Site Administrators, who always belong to the Default Workspace. Can only be enabled when `workspace_id` is `0`.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setIncludeSiteAdmins($value)
    {
        return $this->attributes['include_site_admins'] = $value;
    }
    /**
     * If true, a Default Workspace rule also applies to users in all Custom Workspaces. Can only be enabled when `workspace_id` is `0`.
     *
     * @return bool|null
     */
    public function getApplyToAllWorkspaces()
    {
        return isset($this->attributes['apply_to_all_workspaces']) ? $this->attributes['apply_to_all_workspaces'] : null;
    }

    /**
     * If true, a Default Workspace rule also applies to users in all Custom Workspaces. Can only be enabled when `workspace_id` is `0`.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setApplyToAllWorkspaces($value)
    {
        return $this->attributes['apply_to_all_workspaces'] = $value;
    }
    /**
     * User Lifecycle Rule name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * User Lifecycle Rule name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * If true, users will be emailed before the rule disables or deletes them.
     *
     * @return bool|null
     */
    public function getNotifyUsers()
    {
        return isset($this->attributes['notify_users']) ? $this->attributes['notify_users'] : null;
    }

    /**
     * If true, users will be emailed before the rule disables or deletes them.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyUsers($value)
    {
        return $this->attributes['notify_users'] = $value;
    }
    /**
     * If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @return string|null
     */
    public function getPartnerTag()
    {
        return isset($this->attributes['partner_tag']) ? $this->attributes['partner_tag'] : null;
    }

    /**
     * If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartnerTag($value)
    {
        return $this->attributes['partner_tag'] = $value;
    }
    /**
     * Site ID
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }

    /**
     * Site ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    /**
     * Workspace whose users the rule applies to. `0` means the Default Workspace. A Custom Workspace rule applies only to users who belong to that Workspace, regardless of access granted to other users.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace whose users the rule applies to. `0` means the Default Workspace. A Custom Workspace rule applies only to users who belong to that Workspace, regardless of access granted to other users.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * State of the users to apply the rule to (inactive or disabled)
     *
     * @return string|null
     */
    public function getUserState()
    {
        return isset($this->attributes['user_state']) ? $this->attributes['user_state'] : null;
    }

    /**
     * State of the users to apply the rule to (inactive or disabled)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserState($value)
    {
        return $this->attributes['user_state'] = $value;
    }
    /**
     * If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @return string|null
     */
    public function getUserTag()
    {
        return isset($this->attributes['user_tag']) ? $this->attributes['user_tag'] : null;
    }

    /**
     * If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserTag($value)
    {
        return $this->attributes['user_tag'] = $value;
    }

    /**
     * Update User Lifecycle Rule
     *
     * PATCH /user_lifecycle_rules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User Lifecycle Rule ID.
     *     - `action` (string, optional): Action to take on inactive users (disable or delete) Allowed values: disable, delete.
     *     - `apply_to_all_workspaces` (bool, optional): If true, a Default Workspace rule also applies to users in all Custom Workspaces. Can only be enabled when `workspace_id` is `0`.
     *     - `authentication_method` (string, optional): User authentication method for which the rule will apply. Use `all_non_sso` to target every non-SSO authentication method with one rule. Allowed values: all, password, sso, none, email_signup, password_with_imported_hash, password_and_ssh_key, all_non_sso.
     *     - `group_ids` (int[], optional): Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
     *     - `inactivity_days` (int, optional): Number of days of inactivity before the rule applies
     *     - `include_site_admins` (bool, optional): If true, the rule includes Site Administrators, who always belong to the Default Workspace. Can only be enabled when `workspace_id` is `0`.
     *     - `include_folder_admins` (bool, optional): If true, the rule will apply to folder admins.
     *     - `name` (string, optional): User Lifecycle Rule name
     *     - `notify_users` (bool, optional): If true, users will be emailed before the rule disables or deletes them.
     *     - `partner_tag` (string, optional): If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `user_state` (string, optional): State of the users to apply the rule to (inactive or disabled) Allowed values: inactive, disabled.
     *     - `user_tag` (string, optional): If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `workspace_id` (int, optional): Workspace whose users the rule applies to. `0` means the Default Workspace. A Custom Workspace rule applies only to users who belong to that Workspace, regardless of access granted to other users.
     * @return \Files\Model\UserLifecycleRule
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

        if (!empty($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (!empty($params['authentication_method']) && !is_string($params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (!empty($params['group_ids']) && !is_array($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type array; received ' . gettype(@$params['group_ids']));
        }

        if (!empty($params['inactivity_days']) && !is_int($params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['partner_tag']) && !is_string($params['partner_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_tag must be of type string; received ' . gettype(@$params['partner_tag']));
        }

        if (!empty($params['user_state']) && !is_string($params['user_state'])) {
            throw new \Files\Exception\InvalidParameterException('$user_state must be of type string; received ' . gettype(@$params['user_state']));
        }

        if (!empty($params['user_tag']) && !is_string($params['user_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$user_tag must be of type string; received ' . gettype(@$params['user_tag']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/user_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new UserLifecycleRule((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete User Lifecycle Rule
     *
     * DELETE /user_lifecycle_rules/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User Lifecycle Rule ID.
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

        $response = Api::sendRequest('/user_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete User Lifecycle Rule
     *
     * DELETE /user_lifecycle_rules/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): User Lifecycle Rule ID.
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
     * List User Lifecycle Rules
     *
     * GET /user_lifecycle_rules
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id` and `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UserLifecycleRule[]
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

        $response = Api::sendRequest('/user_lifecycle_rules', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new UserLifecycleRule((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show User Lifecycle Rule
     *
     * GET /user_lifecycle_rules/{id}
     *
     * @param int $id User Lifecycle Rule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UserLifecycleRule
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

        $response = Api::sendRequest('/user_lifecycle_rules/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new UserLifecycleRule((array) (@$response->data ?: []), $options);
    }
    /**
     * Show User Lifecycle Rule
     *
     * GET /user_lifecycle_rules/{id}
     *
     * Alias of find().
     *
     * @param int $id User Lifecycle Rule ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UserLifecycleRule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create User Lifecycle Rule
     *
     * POST /user_lifecycle_rules
     *
     * @param array $params Request parameters.
     *     - `action` (string, optional): Action to take on inactive users (disable or delete) Allowed values: disable, delete.
     *     - `apply_to_all_workspaces` (bool, optional): If true, a Default Workspace rule also applies to users in all Custom Workspaces. Can only be enabled when `workspace_id` is `0`.
     *     - `authentication_method` (string, optional): User authentication method for which the rule will apply. Use `all_non_sso` to target every non-SSO authentication method with one rule. Allowed values: all, password, sso, none, email_signup, password_with_imported_hash, password_and_ssh_key, all_non_sso.
     *     - `group_ids` (int[], optional): Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
     *     - `inactivity_days` (int, optional): Number of days of inactivity before the rule applies
     *     - `include_site_admins` (bool, optional): If true, the rule includes Site Administrators, who always belong to the Default Workspace. Can only be enabled when `workspace_id` is `0`.
     *     - `include_folder_admins` (bool, optional): If true, the rule will apply to folder admins.
     *     - `name` (string, optional): User Lifecycle Rule name
     *     - `notify_users` (bool, optional): If true, users will be emailed before the rule disables or deletes them.
     *     - `partner_tag` (string, optional): If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `user_state` (string, optional): State of the users to apply the rule to (inactive or disabled) Allowed values: inactive, disabled.
     *     - `user_tag` (string, optional): If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
     *     - `workspace_id` (int, optional): Workspace whose users the rule applies to. `0` means the Default Workspace. A Custom Workspace rule applies only to users who belong to that Workspace, regardless of access granted to other users.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UserLifecycleRule
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (!empty($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (!empty($params['authentication_method']) && !is_string($params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (!empty($params['group_ids']) && !is_array($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type array; received ' . gettype(@$params['group_ids']));
        }

        if (!empty($params['inactivity_days']) && !is_int($params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['partner_tag']) && !is_string($params['partner_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_tag must be of type string; received ' . gettype(@$params['partner_tag']));
        }

        if (!empty($params['user_state']) && !is_string($params['user_state'])) {
            throw new \Files\Exception\InvalidParameterException('$user_state must be of type string; received ' . gettype(@$params['user_state']));
        }

        if (!empty($params['user_tag']) && !is_string($params['user_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$user_tag must be of type string; received ' . gettype(@$params['user_tag']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/user_lifecycle_rules', 'POST', $params, $options);

        return new UserLifecycleRule((array) (@$response->data ?: []), $options);
    }
}
