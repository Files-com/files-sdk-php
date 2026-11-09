<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Behavior is an API resource for what are also known as Folder Settings.
 *
 * @property int|null $id Folder behavior ID
 * @property string|null $path Folder path. Note that Behavior paths cannot be updated once initially set. You will need to remove and re-create the behavior on the new path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $attachment_url URL for attached file
 * @property string|null $behavior Behavior type.
 * @property string|null $name Name for this behavior.
 * @property string|null $description Description for this behavior.
 * @property object|null $value Settings for this behavior. See the section above for an example value to provide here. Formatting is different for each Behavior type. May be sent as nested JSON or a single JSON-encoded string. If using XML encoding for the API call, this data must be sent as a JSON-encoded string.
 * @property string|null $public_hosting_url Public URL for this publicly hosted folder when the `Serve Publicly` behavior has a key configured. When a Custom Domain with `public_hosting` destination is attached to this behavior, the URL uses that domain. Otherwise it uses the site's `subdomain.hosted-by-files.com` host, unless `custom_domain_only` is enabled, in which case no URL is returned.
 * @property bool|null $disable_parent_folder_behavior If true, disables the inherited behavior for this folder and its children. Valid only for behavior types that child folders may override, and requires recursive to be true. Rejected for all other behavior types.
 * @property bool|null $recursive Whether this behavior is recursive for this record. `always` behaviors are always `true`, `never` behaviors are always `false`, and `sometimes` behaviors may be either value.
 * @property bool|null $inherited If true, this behavior is inherited from a higher scope rather than owned by the requested workspace.
 * @property bool|null $managed If true, this behavior is controlled by a parent-site policy and cannot be modified locally.
 * @property bool|null $root_behavior_site_admin_only If true, this behavior may only be modified by a site admin because it is at the site root or disables a root behavior.
 * @property mixed $attachment_file Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
 * @property bool|null $attachment_delete If `true`, delete the file stored in `attachment`.
 * @package Files
 */
class Behavior
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
     * Folder behavior ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Folder behavior ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Folder path.  Note that Behavior paths cannot be updated once initially set.  You will need to remove and re-create the behavior on the new path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Folder path.  Note that Behavior paths cannot be updated once initially set.  You will need to remove and re-create the behavior on the new path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * URL for attached file
     *
     * @return string|null
     */
    public function getAttachmentUrl()
    {
        return isset($this->attributes['attachment_url']) ? $this->attributes['attachment_url'] : null;
    }

    /**
     * URL for attached file
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAttachmentUrl($value)
    {
        return $this->attributes['attachment_url'] = $value;
    }
    /**
     * Behavior type.
     *
     * @return string|null
     */
    public function getBehavior()
    {
        return isset($this->attributes['behavior']) ? $this->attributes['behavior'] : null;
    }

    /**
     * Behavior type.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBehavior($value)
    {
        return $this->attributes['behavior'] = $value;
    }
    /**
     * Name for this behavior.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name for this behavior.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Description for this behavior.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Description for this behavior.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Settings for this behavior.  See the section above for an example value to provide here.  Formatting is different for each Behavior type.  May be sent as nested JSON or a single JSON-encoded string.  If using XML encoding for the API call, this data must be sent as a JSON-encoded string.
     *
     * @return object|null
     */
    public function getValue()
    {
        return isset($this->attributes['value']) ? $this->attributes['value'] : null;
    }

    /**
     * Settings for this behavior.  See the section above for an example value to provide here.  Formatting is different for each Behavior type.  May be sent as nested JSON or a single JSON-encoded string.  If using XML encoding for the API call, this data must be sent as a JSON-encoded string.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    /**
     * Public URL for this publicly hosted folder when the `Serve Publicly` behavior has a key configured. When a Custom Domain with `public_hosting` destination is attached to this behavior, the URL uses that domain. Otherwise it uses the site's `subdomain.hosted-by-files.com` host, unless `custom_domain_only` is enabled, in which case no URL is returned.
     *
     * @return string|null
     */
    public function getPublicHostingUrl()
    {
        return isset($this->attributes['public_hosting_url']) ? $this->attributes['public_hosting_url'] : null;
    }

    /**
     * Public URL for this publicly hosted folder when the `Serve Publicly` behavior has a key configured. When a Custom Domain with `public_hosting` destination is attached to this behavior, the URL uses that domain. Otherwise it uses the site's `subdomain.hosted-by-files.com` host, unless `custom_domain_only` is enabled, in which case no URL is returned.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPublicHostingUrl($value)
    {
        return $this->attributes['public_hosting_url'] = $value;
    }
    /**
     * If true, disables the inherited behavior for this folder and its children. Valid only for behavior types that child folders may override, and requires recursive to be true. Rejected for all other behavior types.
     *
     * @return bool|null
     */
    public function getDisableParentFolderBehavior()
    {
        return isset($this->attributes['disable_parent_folder_behavior']) ? $this->attributes['disable_parent_folder_behavior'] : null;
    }

    /**
     * If true, disables the inherited behavior for this folder and its children. Valid only for behavior types that child folders may override, and requires recursive to be true. Rejected for all other behavior types.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDisableParentFolderBehavior($value)
    {
        return $this->attributes['disable_parent_folder_behavior'] = $value;
    }
    /**
     * Whether this behavior is recursive for this record. `always` behaviors are always `true`, `never` behaviors are always `false`, and `sometimes` behaviors may be either value.
     *
     * @return bool|null
     */
    public function getRecursive()
    {
        return isset($this->attributes['recursive']) ? $this->attributes['recursive'] : null;
    }

    /**
     * Whether this behavior is recursive for this record. `always` behaviors are always `true`, `never` behaviors are always `false`, and `sometimes` behaviors may be either value.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    /**
     * If true, this behavior is inherited from a higher scope rather than owned by the requested workspace.
     *
     * @return bool|null
     */
    public function getInherited()
    {
        return isset($this->attributes['inherited']) ? $this->attributes['inherited'] : null;
    }

    /**
     * If true, this behavior is inherited from a higher scope rather than owned by the requested workspace.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setInherited($value)
    {
        return $this->attributes['inherited'] = $value;
    }
    /**
     * If true, this behavior is controlled by a parent-site policy and cannot be modified locally.
     *
     * @return bool|null
     */
    public function getManaged()
    {
        return isset($this->attributes['managed']) ? $this->attributes['managed'] : null;
    }

    /**
     * If true, this behavior is controlled by a parent-site policy and cannot be modified locally.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setManaged($value)
    {
        return $this->attributes['managed'] = $value;
    }
    /**
     * If true, this behavior may only be modified by a site admin because it is at the site root or disables a root behavior.
     *
     * @return bool|null
     */
    public function getRootBehaviorSiteAdminOnly()
    {
        return isset($this->attributes['root_behavior_site_admin_only']) ? $this->attributes['root_behavior_site_admin_only'] : null;
    }

    /**
     * If true, this behavior may only be modified by a site admin because it is at the site root or disables a root behavior.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRootBehaviorSiteAdminOnly($value)
    {
        return $this->attributes['root_behavior_site_admin_only'] = $value;
    }
    /**
     * Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
     *
     * @return mixed
     */
    public function getAttachmentFile()
    {
        return isset($this->attributes['attachment_file']) ? $this->attributes['attachment_file'] : null;
    }

    /**
     * Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
     *
     * @param mixed $value
     * @return mixed The assigned value.
     */
    public function setAttachmentFile($value)
    {
        return $this->attributes['attachment_file'] = $value;
    }
    /**
     * If `true`, delete the file stored in `attachment`.
     *
     * @return bool|null
     */
    public function getAttachmentDelete()
    {
        return isset($this->attributes['attachment_delete']) ? $this->attributes['attachment_delete'] : null;
    }

    /**
     * If `true`, delete the file stored in `attachment`.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setAttachmentDelete($value)
    {
        return $this->attributes['attachment_delete'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Behavior with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Behavior
     *
     * PATCH /behaviors/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (array|object|string|int, optional when set on this model): Behavior ID.
     *     - `value` (array|object, optional): This field stores data specific to the type of behavior. See The Behavior Types section for the accepted value for each type of behavior.
     *     - `attachment_file` (mixed, optional): Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
     *     - `disable_parent_folder_behavior` (bool, optional): If true, disables the inherited behavior for this folder and its children. Valid only for behavior types that child folders may override, and requires recursive to be true. Rejected for all other behavior types.
     *     - `recursive` (bool, optional): Whether the behavior should apply to child folders. This is only configurable for behavior types whose recursion mode is `sometimes`; `always` behaviors stay recursive and `never` behaviors stay non-recursive.
     *     - `name` (string, optional): Name for this behavior.
     *     - `description` (string, optional): Description for this behavior.
     *     - `attachment_delete` (bool, optional): If `true`, delete the file stored in `attachment`.
     * @return \Files\Model\Behavior
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

        if (isset($params['disable_parent_folder_behavior']) && !is_scalar($params['disable_parent_folder_behavior'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_parent_folder_behavior must be of type boolean; received ' . gettype(@$params['disable_parent_folder_behavior']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['attachment_delete']) && !is_scalar($params['attachment_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$attachment_delete must be of type boolean; received ' . gettype(@$params['attachment_delete']));
        }

        $response = Api::sendRequest('/behaviors/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Behavior(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Behavior
     *
     * DELETE /behaviors/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Behavior ID.
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

        $response = Api::sendRequest('/behaviors/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Behavior
     *
     * DELETE /behaviors/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Behavior ID.
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
     * List Behaviors
     *
     * GET /behaviors
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `behavior`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `clickwrap_id`, `form_field_set_id`, `impacts_ui`, `remote_server_id` or `behavior`. Valid field combinations are `[ impacts_ui, behavior ]` and `[ behavior, remote_server_id ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Behavior[]
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

        $response = Api::sendRequest('/behaviors', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Behavior(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/behaviors', $params, $options, Behavior::class);
    }

    /**
     * List Behaviors by Path
     *
     * GET /behaviors/folders/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `behavior`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `impacts_ui` and `behavior`. Valid field combinations are `[ impacts_ui, behavior ]`.
     *     - `ancestor_behaviors` (bool, optional): If `true`, behaviors above this path are shown.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Behavior[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listFor($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['ancestor_behaviors']) && !is_scalar($params['ancestor_behaviors'])) {
            throw new \Files\Exception\InvalidParameterException('$ancestor_behaviors must be of type boolean; received ' . gettype(@$params['ancestor_behaviors']));
        }

        $response = Api::sendRequest('/behaviors/folders/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Behavior(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of listFor()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listForPage($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['ancestor_behaviors']) && !is_scalar($params['ancestor_behaviors'])) {
            throw new \Files\Exception\InvalidParameterException('$ancestor_behaviors must be of type boolean; received ' . gettype(@$params['ancestor_behaviors']));
        }

        return \Files\Page::fetch('/behaviors/folders/' . rawurlencode(strval(@$params['path'])) . '', $params, $options, Behavior::class);
    }

    /**
     * Show Behavior
     *
     * GET /behaviors/{id}
     *
     * @param array|object|string|int $id Behavior ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Behavior
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

        $response = Api::sendRequest('/behaviors/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Behavior(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Behavior
     *
     * GET /behaviors/{id}
     *
     * Alias of find().
     *
     * @param array|object|string|int $id Behavior ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Behavior
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Behavior
     *
     * POST /behaviors
     *
     * @param array $params Request parameters.
     *     - `value` (array|object, optional): This field stores data specific to the type of behavior. See The Behavior Types section for the accepted value for each type of behavior.
     *     - `attachment_file` (mixed, optional): Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
     *     - `disable_parent_folder_behavior` (bool, optional): If true, disables the inherited behavior for this folder and its children. Valid only for behavior types that child folders may override, and requires recursive to be true. Rejected for all other behavior types.
     *     - `recursive` (bool, optional): Whether the behavior should apply to child folders. This is only configurable for behavior types whose recursion mode is `sometimes`; `always` behaviors stay recursive and `never` behaviors stay non-recursive.
     *     - `name` (string, optional): Name for this behavior.
     *     - `description` (string, optional): Description for this behavior.
     *     - `path` (string, required): Path where this behavior should apply.
     *     - `behavior` (string, required): Behavior type.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Behavior
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (empty($params['behavior'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: behavior');
        }

        if (isset($params['disable_parent_folder_behavior']) && !is_scalar($params['disable_parent_folder_behavior'])) {
            throw new \Files\Exception\InvalidParameterException('$disable_parent_folder_behavior must be of type boolean; received ' . gettype(@$params['disable_parent_folder_behavior']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['behavior']) && !is_string($params['behavior'])) {
            throw new \Files\Exception\InvalidParameterException('$behavior must be of type string; received ' . gettype(@$params['behavior']));
        }

        $response = Api::sendRequest('/behaviors', 'POST', $params, $options);

        return new Behavior(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Test Webhook
     *
     * POST /behaviors/webhook/test
     *
     * @param array $params Request parameters.
     *     - `url` (string, required): URL for testing the webhook.
     *     - `method` (string, optional): HTTP request method (GET or POST).
     *     - `encoding` (string, optional): Encoding type for the webhook payload. Can be JSON, XML, or RAW (form data).
     *     - `headers` (array|object, optional): Additional request headers to send via HTTP.
     *     - `body` (array|object, optional): Additional body parameters to include in the webhook payload.
     *     - `action` (string, optional): Action for test body.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function webhookTest($params = [], $options = [])
    {
        if (empty($params['url'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: url');
        }

        if (isset($params['url']) && !is_string($params['url'])) {
            throw new \Files\Exception\InvalidParameterException('$url must be of type string; received ' . gettype(@$params['url']));
        }

        if (isset($params['method']) && !is_string($params['method'])) {
            throw new \Files\Exception\InvalidParameterException('$method must be of type string; received ' . gettype(@$params['method']));
        }

        if (isset($params['encoding']) && !is_string($params['encoding'])) {
            throw new \Files\Exception\InvalidParameterException('$encoding must be of type string; received ' . gettype(@$params['encoding']));
        }

        if (isset($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        $response = Api::sendRequest('/behaviors/webhook/test', 'POST', $params, $options);

        return;
    }
}
