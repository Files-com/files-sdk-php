<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An APIKey is a key that allows programmatic access to your Site.
 *
 * @property int|null $id API Key ID
 * @property string|null $descriptive_label Unique label that describes this API key. Useful for external systems where you may have API keys from multiple accounts and want a human-readable label for each key.
 * @property string|null $description User-supplied description of API key.
 * @property string|null $created_at Time which API Key was created
 * @property string|null $expires_at API Key expiration date
 * @property string|null $key API Key actual key string
 * @property bool|null $aws_style_credentials If `true`, this API key will be usable with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
 * @property string|null $aws_access_key_id AWS Access Key ID to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
 * @property string|null $aws_secret_key AWS Secret Key to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
 * @property string|null $last_use_at API Key last used - note this value is only updated once per 3 hour period, so the 'actual' time of last use may be up to 3 hours later than this timestamp.
 * @property string|null $name Internal name for the API Key. For your use.
 * @property string|null $permission_set Permissions for this API Key. Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations). Keys with the `mobile_app` permission set can only perform the functions provided by our Mobile App (File and Share Link operations). Keys with the `office_integration` permission set are auto generated, and automatically expire, to allow users to interact with office integration platforms. Keys with the `files_only` permission set can use only the files, folders, and file_actions endpoints, excluding the GPG Encrypt and GPG Decrypt file actions, where they perform file operations as a full-access file user in the key's workspace scope, along with `GET /file_migrations/{id}` and `GET /api_key`. On the migration lookup, any key with a `path` can read only migrations it started. Without a `path`, user-owned keys retain user-scoped access; site-wide Workspace-bound keys can read only migrations whose source and destination are both in their Workspace. Every `files_only` key is Workspace-bound, including Workspace `0`, as is a full-access key in a named Workspace. A full-access site-wide key in the default Workspace retains Site-wide migration access. Migrations outside the caller's visibility return `not-found`. Keys with `files_only` cannot use site admin, workspace admin, folder admin, group admin, partner admin, or billing privileges from the owning user. Both GPG file actions and every other endpoint deny them with `not-authorized/api-key-only-for-file-operations`. Folder-configured GPG encryption and decryption still apply to files uploaded with a `files_only` key.
 * @property string|null $platform If this API key represents a Desktop app, what platform was it created on?
 * @property int|null $site_id Site ID
 * @property string|null $site_name Site Name
 * @property string|null $url URL for API host.
 * @property int|null $user_id User ID for the owner of this API Key. May be blank for Site-wide API Keys.
 * @property int|null $workspace_id Workspace ID for this API Key. `0` means the default workspace.
 * @property string|null $path Restricts the file and folder operations made with this key, meaning the files, folders, and file_actions endpoints, to the specified folder and its descendants, including copy and move destinations. For `GET /file_migrations/{id}`, a key with a path can read only migrations it started. Does not grant access beyond the owning user's permissions. Optional for `files_only` keys and required for `office_integration` keys, which need a path the owning user can read. Keys with any other permission set cannot have a path.
 * @package Files
 */
class ApiKey
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
     * API Key ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * API Key ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Unique label that describes this API key.  Useful for external systems where you may have API keys from multiple accounts and want a human-readable label for each key.
     *
     * @return string|null
     */
    public function getDescriptiveLabel()
    {
        return isset($this->attributes['descriptive_label']) ? $this->attributes['descriptive_label'] : null;
    }

    /**
     * Unique label that describes this API key.  Useful for external systems where you may have API keys from multiple accounts and want a human-readable label for each key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescriptiveLabel($value)
    {
        return $this->attributes['descriptive_label'] = $value;
    }
    /**
     * User-supplied description of API key.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * User-supplied description of API key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Time which API Key was created
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * API Key expiration date
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * API Key expiration date
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * API Key actual key string
     *
     * @return string|null
     */
    public function getKey()
    {
        return isset($this->attributes['key']) ? $this->attributes['key'] : null;
    }

    /**
     * API Key actual key string
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setKey($value)
    {
        return $this->attributes['key'] = $value;
    }
    /**
     * If `true`, this API key will be usable with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @return bool|null
     */
    public function getAwsStyleCredentials()
    {
        return isset($this->attributes['aws_style_credentials']) ? $this->attributes['aws_style_credentials'] : null;
    }

    /**
     * If `true`, this API key will be usable with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAwsStyleCredentials($value)
    {
        return $this->attributes['aws_style_credentials'] = $value;
    }
    /**
     * AWS Access Key ID to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @return string|null
     */
    public function getAwsAccessKeyId()
    {
        return isset($this->attributes['aws_access_key_id']) ? $this->attributes['aws_access_key_id'] : null;
    }

    /**
     * AWS Access Key ID to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAwsAccessKeyId($value)
    {
        return $this->attributes['aws_access_key_id'] = $value;
    }
    /**
     * AWS Secret Key to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @return string|null
     */
    public function getAwsSecretKey()
    {
        return isset($this->attributes['aws_secret_key']) ? $this->attributes['aws_secret_key'] : null;
    }

    /**
     * AWS Secret Key to use with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAwsSecretKey($value)
    {
        return $this->attributes['aws_secret_key'] = $value;
    }
    /**
     * API Key last used - note this value is only updated once per 3 hour period, so the 'actual' time of last use may be up to 3 hours later than this timestamp.
     *
     * @return string|null
     */
    public function getLastUseAt()
    {
        return isset($this->attributes['last_use_at']) ? $this->attributes['last_use_at'] : null;
    }

    /**
     * API Key last used - note this value is only updated once per 3 hour period, so the 'actual' time of last use may be up to 3 hours later than this timestamp.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLastUseAt($value)
    {
        return $this->attributes['last_use_at'] = $value;
    }
    /**
     * Internal name for the API Key.  For your use.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Internal name for the API Key.  For your use.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Permissions for this API Key. Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations). Keys with the `mobile_app` permission set can only perform the functions provided by our Mobile App (File and Share Link operations). Keys with the `office_integration` permission set are auto generated, and automatically expire, to allow users to interact with office integration platforms. Keys with the `files_only` permission set can use only the files, folders, and file_actions endpoints, excluding the GPG Encrypt and GPG Decrypt file actions, where they perform file operations as a full-access file user in the key's workspace scope, along with `GET /file_migrations/{id}` and `GET /api_key`. On the migration lookup, any key with a `path` can read only migrations it started. Without a `path`, user-owned keys retain user-scoped access; site-wide Workspace-bound keys can read only migrations whose source and destination are both in their Workspace. Every `files_only` key is Workspace-bound, including Workspace `0`, as is a full-access key in a named Workspace. A full-access site-wide key in the default Workspace retains Site-wide migration access. Migrations outside the caller's visibility return `not-found`. Keys with `files_only` cannot use site admin, workspace admin, folder admin, group admin, partner admin, or billing privileges from the owning user. Both GPG file actions and every other endpoint deny them with `not-authorized/api-key-only-for-file-operations`. Folder-configured GPG encryption and decryption still apply to files uploaded with a `files_only` key.
     *
     * @return string|null
     */
    public function getPermissionSet()
    {
        return isset($this->attributes['permission_set']) ? $this->attributes['permission_set'] : null;
    }

    /**
     * Permissions for this API Key. Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations). Keys with the `mobile_app` permission set can only perform the functions provided by our Mobile App (File and Share Link operations). Keys with the `office_integration` permission set are auto generated, and automatically expire, to allow users to interact with office integration platforms. Keys with the `files_only` permission set can use only the files, folders, and file_actions endpoints, excluding the GPG Encrypt and GPG Decrypt file actions, where they perform file operations as a full-access file user in the key's workspace scope, along with `GET /file_migrations/{id}` and `GET /api_key`. On the migration lookup, any key with a `path` can read only migrations it started. Without a `path`, user-owned keys retain user-scoped access; site-wide Workspace-bound keys can read only migrations whose source and destination are both in their Workspace. Every `files_only` key is Workspace-bound, including Workspace `0`, as is a full-access key in a named Workspace. A full-access site-wide key in the default Workspace retains Site-wide migration access. Migrations outside the caller's visibility return `not-found`. Keys with `files_only` cannot use site admin, workspace admin, folder admin, group admin, partner admin, or billing privileges from the owning user. Both GPG file actions and every other endpoint deny them with `not-authorized/api-key-only-for-file-operations`. Folder-configured GPG encryption and decryption still apply to files uploaded with a `files_only` key.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPermissionSet($value)
    {
        return $this->attributes['permission_set'] = $value;
    }
    /**
     * If this API key represents a Desktop app, what platform was it created on?
     *
     * @return string|null
     */
    public function getPlatform()
    {
        return isset($this->attributes['platform']) ? $this->attributes['platform'] : null;
    }

    /**
     * If this API key represents a Desktop app, what platform was it created on?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPlatform($value)
    {
        return $this->attributes['platform'] = $value;
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
     * Site Name
     *
     * @return string|null
     */
    public function getSiteName()
    {
        return isset($this->attributes['site_name']) ? $this->attributes['site_name'] : null;
    }

    /**
     * Site Name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSiteName($value)
    {
        return $this->attributes['site_name'] = $value;
    }
    /**
     * URL for API host.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return isset($this->attributes['url']) ? $this->attributes['url'] : null;
    }

    /**
     * URL for API host.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUrl($value)
    {
        return $this->attributes['url'] = $value;
    }
    /**
     * User ID for the owner of this API Key.  May be blank for Site-wide API Keys.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID for the owner of this API Key.  May be blank for Site-wide API Keys.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Workspace ID for this API Key. `0` means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID for this API Key. `0` means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Restricts the file and folder operations made with this key, meaning the files, folders, and file_actions endpoints, to the specified folder and its descendants, including copy and move destinations. For `GET /file_migrations/{id}`, a key with a path can read only migrations it started. Does not grant access beyond the owning user's permissions. Optional for `files_only` keys and required for `office_integration` keys, which need a path the owning user can read. Keys with any other permission set cannot have a path.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Restricts the file and folder operations made with this key, meaning the files, folders, and file_actions endpoints, to the specified folder and its descendants, including copy and move destinations. For `GET /file_migrations/{id}`, a key with a path can read only migrations it started. Does not grant access beyond the owning user's permissions. Optional for `files_only` keys and required for `office_integration` keys, which need a path the owning user can read. Keys with any other permission set cannot have a path.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }

    /**
     * Update API Key
     *
     * PATCH /api_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Api Key ID.
     *     - `description` (string, optional): User-supplied description of API key.
     *     - `expires_at` (string, optional): API Key expiration date
     *     - `name` (string, optional): Internal name for the API Key.  For your use.
     * @return \Files\Model\ApiKey
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

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/api_keys/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new ApiKey((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete API Key
     *
     * DELETE /api_keys/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Api Key ID.
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

        $response = Api::sendRequest('/api_keys/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete API Key
     *
     * DELETE /api_keys/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Api Key ID.
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
     * List API Keys
     *
     * GET /api_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id` and `workspace_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `aws_style_credentials` and `expires_at`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `expires_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `expires_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `expires_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `expires_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/api_keys', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ApiKey((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show information about current API key.  (Requires current API connection to be using an API key.)
     *
     * GET /api_key
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function findCurrent($params = [], $options = [])
    {
        $response = Api::sendRequest('/api_key', 'GET', $options);

        return new ApiKey((array) (@$response->data ?: []), $options);
    }

    /**
     * Show API Key
     *
     * GET /api_keys/{id}
     *
     * @param int $id Api Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey
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

        $response = Api::sendRequest('/api_keys/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ApiKey((array) (@$response->data ?: []), $options);
    }
    /**
     * Show API Key
     *
     * GET /api_keys/{id}
     *
     * Alias of find().
     *
     * @param int $id Api Key ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create API Key
     *
     * POST /api_keys
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `description` (string, optional): User-supplied description of API key.
     *     - `expires_at` (string, optional): API Key expiration date
     *     - `name` (string, required): Internal name for the API Key.  For your use.
     *     - `aws_style_credentials` (bool, optional): If `true`, this API key will be usable with AWS-compatible endpoints, such as our Inbound S3-compatible endpoint.
     *     - `path` (string, optional): Restricts the file and folder operations made with this key, meaning the files, folders, and file_actions endpoints, to the specified folder and its descendants, including copy and move destinations. For `GET /file_migrations/{id}`, a key with a path can read only migrations it started. Does not grant access beyond the owning user's permissions. Optional for `files_only` keys and required for `office_integration` keys, which need a path the owning user can read. Keys with any other permission set cannot have a path.
     *     - `permission_set` (string, optional): Permissions for this API Key. Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations). Keys with the `mobile_app` permission set can only perform the functions provided by our Mobile App (File and Share Link operations). Keys with the `office_integration` permission set are auto generated, and automatically expire, to allow users to interact with office integration platforms. Keys with the `files_only` permission set can use only the files, folders, and file_actions endpoints, excluding the GPG Encrypt and GPG Decrypt file actions, where they perform file operations as a full-access file user in the key's workspace scope, along with `GET /file_migrations/{id}` and `GET /api_key`. On the migration lookup, any key with a `path` can read only migrations it started. Without a `path`, user-owned keys retain user-scoped access; site-wide Workspace-bound keys can read only migrations whose source and destination are both in their Workspace. Every `files_only` key is Workspace-bound, including Workspace `0`, as is a full-access key in a named Workspace. A full-access site-wide key in the default Workspace retains Site-wide migration access. Migrations outside the caller's visibility return `not-found`. Keys with `files_only` cannot use site admin, workspace admin, folder admin, group admin, partner admin, or billing privileges from the owning user. Both GPG file actions and every other endpoint deny them with `not-authorized/api-key-only-for-file-operations`. Folder-configured GPG encryption and decryption still apply to files uploaded with a `files_only` key. Allowed values: none, full, desktop_app, sync_app, office_integration, mobile_app, files_only.
     *     - `workspace_id` (int, optional): Workspace ID for this API Key. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['permission_set']) && !is_string($params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/api_keys', 'POST', $params, $options);

        return new ApiKey((array) (@$response->data ?: []), $options);
    }

    /**
     * Update current API key.  (Requires current API connection to be using an API key.)
     *
     * PATCH /api_key
     *
     * @param array $params Request parameters.
     *     - `expires_at` (string, optional): API Key expiration date
     *     - `name` (string, optional): Internal name for the API Key.  For your use.
     *     - `permission_set` (string, optional): Permissions for this API Key. Keys with the `desktop_app` permission set only have the ability to do the functions provided in our Desktop App (File and Share Link operations). Keys with the `mobile_app` permission set can only perform the functions provided by our Mobile App (File and Share Link operations). Keys with the `office_integration` permission set are auto generated, and automatically expire, to allow users to interact with office integration platforms. Keys with the `files_only` permission set can use only the files, folders, and file_actions endpoints, excluding the GPG Encrypt and GPG Decrypt file actions, where they perform file operations as a full-access file user in the key's workspace scope, along with `GET /file_migrations/{id}` and `GET /api_key`. On the migration lookup, any key with a `path` can read only migrations it started. Without a `path`, user-owned keys retain user-scoped access; site-wide Workspace-bound keys can read only migrations whose source and destination are both in their Workspace. Every `files_only` key is Workspace-bound, including Workspace `0`, as is a full-access key in a named Workspace. A full-access site-wide key in the default Workspace retains Site-wide migration access. Migrations outside the caller's visibility return `not-found`. Keys with `files_only` cannot use site admin, workspace admin, folder admin, group admin, partner admin, or billing privileges from the owning user. Both GPG file actions and every other endpoint deny them with `not-authorized/api-key-only-for-file-operations`. Folder-configured GPG encryption and decryption still apply to files uploaded with a `files_only` key. Allowed values: none, full, desktop_app, sync_app, office_integration, mobile_app, files_only.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ApiKey
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function updateCurrent($params = [], $options = [])
    {
        if (!empty($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['permission_set']) && !is_string($params['permission_set'])) {
            throw new \Files\Exception\InvalidParameterException('$permission_set must be of type string; received ' . gettype(@$params['permission_set']));
        }

        $response = Api::sendRequest('/api_key', 'PATCH', $params, $options);

        return new ApiKey((array) (@$response->data ?: []), $options);
    }

    /**
     * Delete current API key.  (Requires current API connection to be using an API key.)
     *
     * DELETE /api_key
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function deleteCurrent($params = [], $options = [])
    {
        $response = Api::sendRequest('/api_key', 'DELETE', $options);

        return;
    }
}
