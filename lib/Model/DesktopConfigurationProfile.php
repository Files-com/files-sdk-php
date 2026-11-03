<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Desktop Configuration Profile centrally defines desktop mount point mappings for users in a Site or Workspace.
 *
 * @property int|null $id Desktop Configuration Profile ID
 * @property string|null $name Profile name
 * @property int|null $workspace_id Workspace ID
 * @property bool|null $use_for_all_users Whether this profile applies to all users in the Workspace by default
 * @property bool|null $disable_drive_mounting Whether the desktop app should hide drive mounting, prevent new drive mounts, and unmount active drive mounts for users with this profile
 * @property object|null $mount_mappings Mount point mappings for the desktop app. Keys must be a single uppercase Windows drive letter other than A, B, or C, and values are Files.com paths to mount there.
 * @package Files
 */
class DesktopConfigurationProfile
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
     * Desktop Configuration Profile ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Desktop Configuration Profile ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Profile name
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Profile name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
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
    /**
     * Whether this profile applies to all users in the Workspace by default
     *
     * @return bool|null
     */
    public function getUseForAllUsers()
    {
        return isset($this->attributes['use_for_all_users']) ? $this->attributes['use_for_all_users'] : null;
    }

    /**
     * Whether this profile applies to all users in the Workspace by default
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUseForAllUsers($value)
    {
        return $this->attributes['use_for_all_users'] = $value;
    }
    /**
     * Whether the desktop app should hide drive mounting, prevent new drive mounts, and unmount active drive mounts for users with this profile
     *
     * @return bool|null
     */
    public function getDisableDriveMounting()
    {
        return isset($this->attributes['disable_drive_mounting']) ? $this->attributes['disable_drive_mounting'] : null;
    }

    /**
     * Whether the desktop app should hide drive mounting, prevent new drive mounts, and unmount active drive mounts for users with this profile
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisableDriveMounting($value)
    {
        return $this->attributes['disable_drive_mounting'] = $value;
    }
    /**
     * Mount point mappings for the desktop app. Keys must be a single uppercase Windows drive letter other than A, B, or C, and values are Files.com paths to mount there.
     *
     * @return object|null
     */
    public function getMountMappings()
    {
        return isset($this->attributes['mount_mappings']) ? $this->attributes['mount_mappings'] : null;
    }

    /**
     * Mount point mappings for the desktop app. Keys must be a single uppercase Windows drive letter other than A, B, or C, and values are Files.com paths to mount there.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setMountMappings($value)
    {
        return $this->attributes['mount_mappings'] = $value;
    }

    /**
     * Update Desktop Configuration Profile
     *
     * PATCH /desktop_configuration_profiles/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Desktop Configuration Profile ID.
     *     - `name` (string, optional): Profile name
     *     - `workspace_id` (int, optional): Workspace ID
     *     - `mount_mappings` (array|object, optional): Mount point mappings for the desktop app. Keys must be a single uppercase Windows drive letter other than A, B, or C, and values are Files.com paths to mount there.
     *     - `use_for_all_users` (bool, optional): Whether this profile applies to all users in the Workspace by default
     *     - `disable_drive_mounting` (bool, optional): Whether the desktop app should hide drive mounting, prevent new drive mounts, and unmount active drive mounts for users with this profile
     * @return \Files\Model\DesktopConfigurationProfile
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

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/desktop_configuration_profiles/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new DesktopConfigurationProfile((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Desktop Configuration Profile
     *
     * DELETE /desktop_configuration_profiles/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Desktop Configuration Profile ID.
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

        $response = Api::sendRequest('/desktop_configuration_profiles/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Desktop Configuration Profile
     *
     * DELETE /desktop_configuration_profiles/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Desktop Configuration Profile ID.
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
     * List Desktop Configuration Profiles
     *
     * GET /desktop_configuration_profiles
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id` and `name`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `workspace_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DesktopConfigurationProfile[]
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

        $response = Api::sendRequest('/desktop_configuration_profiles', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new DesktopConfigurationProfile((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Desktop Configuration Profile
     *
     * GET /desktop_configuration_profiles/{id}
     *
     * @param int $id Desktop Configuration Profile ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DesktopConfigurationProfile
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

        $response = Api::sendRequest('/desktop_configuration_profiles/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new DesktopConfigurationProfile((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Desktop Configuration Profile
     *
     * GET /desktop_configuration_profiles/{id}
     *
     * Alias of find().
     *
     * @param int $id Desktop Configuration Profile ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DesktopConfigurationProfile
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Desktop Configuration Profile
     *
     * POST /desktop_configuration_profiles
     *
     * @param array $params Request parameters.
     *     - `name` (string, required): Profile name
     *     - `mount_mappings` (array|object, required): Mount point mappings for the desktop app. Keys must be a single uppercase Windows drive letter other than A, B, or C, and values are Files.com paths to mount there.
     *     - `workspace_id` (int, optional): Workspace ID
     *     - `use_for_all_users` (bool, optional): Whether this profile applies to all users in the Workspace by default
     *     - `disable_drive_mounting` (bool, optional): Whether the desktop app should hide drive mounting, prevent new drive mounts, and unmount active drive mounts for users with this profile
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\DesktopConfigurationProfile
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['mount_mappings'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: mount_mappings');
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/desktop_configuration_profiles', 'POST', $params, $options);

        return new DesktopConfigurationProfile((array) (@$response->data ?: []), $options);
    }
}
