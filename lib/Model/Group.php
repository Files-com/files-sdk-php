<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Group is a powerful tool for permissions and user management on Files.com.
 *
 * @property int|null $id Group ID
 * @property string|null $name Group name
 * @property string|null $allowed_ips A list of allowed IPs if applicable. Newline delimited
 * @property string|null $admin_ids Comma-delimited list of user IDs who are group administrators (separated by commas)
 * @property string|null $notes Notes about this group
 * @property string|null $user_ids Comma-delimited list of user IDs who belong to this group (separated by commas)
 * @property string|null $usernames Comma-delimited list of usernames who belong to this group (separated by commas)
 * @property int|null $ai_assistant_personality_id AI Assistant Personality ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user or Partner assignment overrides it.
 * @property bool|null $ftp_permission If true, users in this group can use FTP to login. This will override a false value of `ftp_permission` on the user level.
 * @property bool|null $sftp_permission If true, users in this group can use SFTP to login. This will override a false value of `sftp_permission` on the user level.
 * @property bool|null $dav_permission If true, users in this group can use WebDAV to login. This will override a false value of `dav_permission` on the user level.
 * @property bool|null $restapi_permission If true, users in this group can use the REST API to login. This will override a false value of `restapi_permission` on the user level.
 * @property bool|null $s3_compatible_endpoint_permission If true, users in this group can access the S3-compatible endpoint. This will override a false value of `s3_compatible_endpoint_permission` on the user level. Defaults to false.
 * @property int|null $desktop_configuration_profile_id Desktop Configuration Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
 * @property int|null $integration_centric_profile_id Integration Centric Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
 * @property int|null $site_id Site ID
 * @property int|null $workspace_id Workspace ID
 * @package Files
 */
class Group
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
     * Group ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Group ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Group name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Group name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * A list of allowed IPs if applicable.  Newline delimited
     *
     * @return string|null
     */
    public function getAllowedIps()
    {
        return isset($this->attributes['allowed_ips']) ? $this->attributes['allowed_ips'] : null;
    }

    /**
     * A list of allowed IPs if applicable.  Newline delimited
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAllowedIps($value)
    {
        return $this->attributes['allowed_ips'] = $value;
    }
    /**
     * Comma-delimited list of user IDs who are group administrators (separated by commas)
     *
     * @return string|null
     */
    public function getAdminIds()
    {
        return isset($this->attributes['admin_ids']) ? $this->attributes['admin_ids'] : null;
    }

    /**
     * Comma-delimited list of user IDs who are group administrators (separated by commas)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAdminIds($value)
    {
        return $this->attributes['admin_ids'] = $value;
    }
    /**
     * Notes about this group
     *
     * @return string|null
     */
    public function getNotes()
    {
        return isset($this->attributes['notes']) ? $this->attributes['notes'] : null;
    }

    /**
     * Notes about this group
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    /**
     * Comma-delimited list of user IDs who belong to this group (separated by commas)
     *
     * @return string|null
     */
    public function getUserIds()
    {
        return isset($this->attributes['user_ids']) ? $this->attributes['user_ids'] : null;
    }

    /**
     * Comma-delimited list of user IDs who belong to this group (separated by commas)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }
    /**
     * Comma-delimited list of usernames who belong to this group (separated by commas)
     *
     * @return string|null
     */
    public function getUsernames()
    {
        return isset($this->attributes['usernames']) ? $this->attributes['usernames'] : null;
    }

    /**
     * Comma-delimited list of usernames who belong to this group (separated by commas)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsernames($value)
    {
        return $this->attributes['usernames'] = $value;
    }
    /**
     * AI Assistant Personality ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user or Partner assignment overrides it.
     *
     * @return int|null
     */
    public function getAiAssistantPersonalityId()
    {
        return isset($this->attributes['ai_assistant_personality_id']) ? $this->attributes['ai_assistant_personality_id'] : null;
    }

    /**
     * AI Assistant Personality ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user or Partner assignment overrides it.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setAiAssistantPersonalityId($value)
    {
        return $this->attributes['ai_assistant_personality_id'] = $value;
    }
    /**
     * If true, users in this group can use FTP to login.  This will override a false value of `ftp_permission` on the user level.
     *
     * @return bool|null
     */
    public function getFtpPermission()
    {
        return isset($this->attributes['ftp_permission']) ? $this->attributes['ftp_permission'] : null;
    }

    /**
     * If true, users in this group can use FTP to login.  This will override a false value of `ftp_permission` on the user level.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFtpPermission($value)
    {
        return $this->attributes['ftp_permission'] = $value;
    }
    /**
     * If true, users in this group can use SFTP to login.  This will override a false value of `sftp_permission` on the user level.
     *
     * @return bool|null
     */
    public function getSftpPermission()
    {
        return isset($this->attributes['sftp_permission']) ? $this->attributes['sftp_permission'] : null;
    }

    /**
     * If true, users in this group can use SFTP to login.  This will override a false value of `sftp_permission` on the user level.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSftpPermission($value)
    {
        return $this->attributes['sftp_permission'] = $value;
    }
    /**
     * If true, users in this group can use WebDAV to login.  This will override a false value of `dav_permission` on the user level.
     *
     * @return bool|null
     */
    public function getDavPermission()
    {
        return isset($this->attributes['dav_permission']) ? $this->attributes['dav_permission'] : null;
    }

    /**
     * If true, users in this group can use WebDAV to login.  This will override a false value of `dav_permission` on the user level.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDavPermission($value)
    {
        return $this->attributes['dav_permission'] = $value;
    }
    /**
     * If true, users in this group can use the REST API to login.  This will override a false value of `restapi_permission` on the user level.
     *
     * @return bool|null
     */
    public function getRestapiPermission()
    {
        return isset($this->attributes['restapi_permission']) ? $this->attributes['restapi_permission'] : null;
    }

    /**
     * If true, users in this group can use the REST API to login.  This will override a false value of `restapi_permission` on the user level.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRestapiPermission($value)
    {
        return $this->attributes['restapi_permission'] = $value;
    }
    /**
     * If true, users in this group can access the S3-compatible endpoint. This will override a false value of `s3_compatible_endpoint_permission` on the user level. Defaults to false.
     *
     * @return bool|null
     */
    public function getS3CompatibleEndpointPermission()
    {
        return isset($this->attributes['s3_compatible_endpoint_permission']) ? $this->attributes['s3_compatible_endpoint_permission'] : null;
    }

    /**
     * If true, users in this group can access the S3-compatible endpoint. This will override a false value of `s3_compatible_endpoint_permission` on the user level. Defaults to false.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setS3CompatibleEndpointPermission($value)
    {
        return $this->attributes['s3_compatible_endpoint_permission'] = $value;
    }
    /**
     * Desktop Configuration Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *
     * @return int|null
     */
    public function getDesktopConfigurationProfileId()
    {
        return isset($this->attributes['desktop_configuration_profile_id']) ? $this->attributes['desktop_configuration_profile_id'] : null;
    }

    /**
     * Desktop Configuration Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setDesktopConfigurationProfileId($value)
    {
        return $this->attributes['desktop_configuration_profile_id'] = $value;
    }
    /**
     * Integration Centric Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *
     * @return int|null
     */
    public function getIntegrationCentricProfileId()
    {
        return isset($this->attributes['integration_centric_profile_id']) ? $this->attributes['integration_centric_profile_id'] : null;
    }

    /**
     * Integration Centric Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setIntegrationCentricProfileId($value)
    {
        return $this->attributes['integration_centric_profile_id'] = $value;
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
     * Workspace ID
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Group with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Group
     *
     * PATCH /groups/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group ID.
     *     - `notes` (string, optional): Group notes.
     *     - `user_ids` (string, optional): A list of user ids. If sent as a string, should be comma-delimited.
     *     - `admin_ids` (string, optional): A list of group admin user ids. If sent as a string, should be comma-delimited.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user or Partner assignment overrides it.
     *     - `ftp_permission` (bool, optional): If true, users in this group can use FTP to login.  This will override a false value of `ftp_permission` on the user level.
     *     - `sftp_permission` (bool, optional): If true, users in this group can use SFTP to login.  This will override a false value of `sftp_permission` on the user level.
     *     - `dav_permission` (bool, optional): If true, users in this group can use WebDAV to login.  This will override a false value of `dav_permission` on the user level.
     *     - `restapi_permission` (bool, optional): If true, users in this group can use the REST API to login.  This will override a false value of `restapi_permission` on the user level.
     *     - `s3_compatible_endpoint_permission` (bool, optional): If true, users in this group can access the S3-compatible endpoint. This will override a false value of `s3_compatible_endpoint_permission` on the user level. Defaults to false.
     *     - `desktop_configuration_profile_id` (int, optional): Desktop Configuration Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *     - `integration_centric_profile_id` (int, optional): Integration Centric Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *     - `allowed_ips` (string, optional): A list of allowed IPs if applicable.  Newline delimited
     *     - `name` (string, optional): Group name.
     * @return \Files\Model\Group
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

        if (isset($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (isset($params['user_ids']) && !is_string($params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (isset($params['admin_ids']) && !is_string($params['admin_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$admin_ids must be of type string; received ' . gettype(@$params['admin_ids']));
        }

        if (isset($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (isset($params['ftp_permission']) && !is_scalar($params['ftp_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_permission must be of type boolean; received ' . gettype(@$params['ftp_permission']));
        }

        if (isset($params['sftp_permission']) && !is_scalar($params['sftp_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_permission must be of type boolean; received ' . gettype(@$params['sftp_permission']));
        }

        if (isset($params['dav_permission']) && !is_scalar($params['dav_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$dav_permission must be of type boolean; received ' . gettype(@$params['dav_permission']));
        }

        if (isset($params['restapi_permission']) && !is_scalar($params['restapi_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$restapi_permission must be of type boolean; received ' . gettype(@$params['restapi_permission']));
        }

        if (isset($params['s3_compatible_endpoint_permission']) && !is_scalar($params['s3_compatible_endpoint_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint_permission must be of type boolean; received ' . gettype(@$params['s3_compatible_endpoint_permission']));
        }

        if (isset($params['desktop_configuration_profile_id']) && !is_int($params['desktop_configuration_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_configuration_profile_id must be of type int; received ' . gettype(@$params['desktop_configuration_profile_id']));
        }

        if (isset($params['integration_centric_profile_id']) && !is_int($params['integration_centric_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_profile_id must be of type int; received ' . gettype(@$params['integration_centric_profile_id']));
        }

        if (isset($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/groups/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Group(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Group
     *
     * DELETE /groups/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group ID.
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

        $response = Api::sendRequest('/groups/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Group
     *
     * DELETE /groups/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Group ID.
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
     * List Groups
     *
     * GET /groups
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`, `workspace_id` or `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `name` and `workspace_id`. Valid field combinations are `[ workspace_id, name ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
     *     - `ids` (string, optional): Comma-separated list of group ids to include in results.
     *     - `include_parent_site_groups` (bool, optional): Include groups from the parent site.
     *     - `include_default_workspace_groups` (bool, optional): When scoped to a Custom Workspace, include Default Workspace groups that have permissions in that Workspace. Available to Site Administrators, and to Workspace Administrators and Folder Admins in that Workspace when the site shares its Default Workspace directory with Workspaces.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Group[]
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

        if (isset($params['ids']) && !is_string($params['ids'])) {
            throw new \Files\Exception\InvalidParameterException('$ids must be of type string; received ' . gettype(@$params['ids']));
        }

        if (isset($params['include_parent_site_groups']) && !is_scalar($params['include_parent_site_groups'])) {
            throw new \Files\Exception\InvalidParameterException('$include_parent_site_groups must be of type boolean; received ' . gettype(@$params['include_parent_site_groups']));
        }

        if (isset($params['include_default_workspace_groups']) && !is_scalar($params['include_default_workspace_groups'])) {
            throw new \Files\Exception\InvalidParameterException('$include_default_workspace_groups must be of type boolean; received ' . gettype(@$params['include_default_workspace_groups']));
        }

        $response = Api::sendRequest('/groups', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Group(Api::modelAttributes($obj, $response->status), $options);
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

        if (isset($params['ids']) && !is_string($params['ids'])) {
            throw new \Files\Exception\InvalidParameterException('$ids must be of type string; received ' . gettype(@$params['ids']));
        }

        if (isset($params['include_parent_site_groups']) && !is_scalar($params['include_parent_site_groups'])) {
            throw new \Files\Exception\InvalidParameterException('$include_parent_site_groups must be of type boolean; received ' . gettype(@$params['include_parent_site_groups']));
        }

        if (isset($params['include_default_workspace_groups']) && !is_scalar($params['include_default_workspace_groups'])) {
            throw new \Files\Exception\InvalidParameterException('$include_default_workspace_groups must be of type boolean; received ' . gettype(@$params['include_default_workspace_groups']));
        }

        return \Files\Page::fetch('/groups', $params, $options, Group::class);
    }

    /**
     * Show Group
     *
     * GET /groups/{id}
     *
     * @param int $id Group ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Group
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

        $response = Api::sendRequest('/groups/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Group(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Group
     *
     * GET /groups/{id}
     *
     * Alias of find().
     *
     * @param int $id Group ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Group
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Group
     *
     * POST /groups
     *
     * @param array $params Request parameters.
     *     - `notes` (string, optional): Group notes.
     *     - `user_ids` (string, optional): A list of user ids. If sent as a string, should be comma-delimited.
     *     - `admin_ids` (string, optional): A list of group admin user ids. If sent as a string, should be comma-delimited.
     *     - `ai_assistant_personality_id` (int, optional): AI Assistant Personality ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user or Partner assignment overrides it.
     *     - `ftp_permission` (bool, optional): If true, users in this group can use FTP to login.  This will override a false value of `ftp_permission` on the user level.
     *     - `sftp_permission` (bool, optional): If true, users in this group can use SFTP to login.  This will override a false value of `sftp_permission` on the user level.
     *     - `dav_permission` (bool, optional): If true, users in this group can use WebDAV to login.  This will override a false value of `dav_permission` on the user level.
     *     - `restapi_permission` (bool, optional): If true, users in this group can use the REST API to login.  This will override a false value of `restapi_permission` on the user level.
     *     - `s3_compatible_endpoint_permission` (bool, optional): If true, users in this group can access the S3-compatible endpoint. This will override a false value of `s3_compatible_endpoint_permission` on the user level. Defaults to false.
     *     - `desktop_configuration_profile_id` (int, optional): Desktop Configuration Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *     - `integration_centric_profile_id` (int, optional): Integration Centric Profile ID assigned to this Group, if any. Users in the Group inherit it unless a direct per-user assignment overrides it.
     *     - `allowed_ips` (string, optional): A list of allowed IPs if applicable.  Newline delimited
     *     - `name` (string, required): Group name.
     *     - `workspace_id` (int, optional): Workspace ID
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Group
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (isset($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (isset($params['user_ids']) && !is_string($params['user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
        }

        if (isset($params['admin_ids']) && !is_string($params['admin_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$admin_ids must be of type string; received ' . gettype(@$params['admin_ids']));
        }

        if (isset($params['ai_assistant_personality_id']) && !is_int($params['ai_assistant_personality_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ai_assistant_personality_id must be of type int; received ' . gettype(@$params['ai_assistant_personality_id']));
        }

        if (isset($params['ftp_permission']) && !is_scalar($params['ftp_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$ftp_permission must be of type boolean; received ' . gettype(@$params['ftp_permission']));
        }

        if (isset($params['sftp_permission']) && !is_scalar($params['sftp_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$sftp_permission must be of type boolean; received ' . gettype(@$params['sftp_permission']));
        }

        if (isset($params['dav_permission']) && !is_scalar($params['dav_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$dav_permission must be of type boolean; received ' . gettype(@$params['dav_permission']));
        }

        if (isset($params['restapi_permission']) && !is_scalar($params['restapi_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$restapi_permission must be of type boolean; received ' . gettype(@$params['restapi_permission']));
        }

        if (isset($params['s3_compatible_endpoint_permission']) && !is_scalar($params['s3_compatible_endpoint_permission'])) {
            throw new \Files\Exception\InvalidParameterException('$s3_compatible_endpoint_permission must be of type boolean; received ' . gettype(@$params['s3_compatible_endpoint_permission']));
        }

        if (isset($params['desktop_configuration_profile_id']) && !is_int($params['desktop_configuration_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$desktop_configuration_profile_id must be of type int; received ' . gettype(@$params['desktop_configuration_profile_id']));
        }

        if (isset($params['integration_centric_profile_id']) && !is_int($params['integration_centric_profile_id'])) {
            throw new \Files\Exception\InvalidParameterException('$integration_centric_profile_id must be of type int; received ' . gettype(@$params['integration_centric_profile_id']));
        }

        if (isset($params['allowed_ips']) && !is_string($params['allowed_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$allowed_ips must be of type string; received ' . gettype(@$params['allowed_ips']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/groups', 'POST', $params, $options);

        return new Group(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
