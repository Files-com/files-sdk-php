<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An ActionLog is an audit log of file actions performed by users or the system.
 *
 * @property string|null $action The type of action performed
 * @property string|null $created_at Time the action was performed
 * @property string|null $destination Destination path, for moves and copies
 * @property string|null $failure_type Failure type, if applicable
 * @property string|null $folder Folder involved in the action
 * @property string|null $interface Interface used to perform the action
 * @property string|null $ip IP address from which the action was performed
 * @property int|null $metadata_dm_id File or Folder ID associated with the action
 * @property int|null $parent_metadata_dm_id Parent File or Folder ID associated with the action
 * @property string|null $path File path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $site_id Site ID where the action took place
 * @property string|null $src Source path, for moves and copies
 * @property int|null $user_id User ID who performed the action
 * @property string|null $username Username who performed the action
 * @package Files
 */
class ActionLog
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
     * The type of action performed
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * Time the action was performed
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Destination path, for moves and copies
     *
     * @return string|null
     */
    public function getDestination()
    {
        return isset($this->attributes['destination']) ? $this->attributes['destination'] : null;
    }
    /**
     * Failure type, if applicable
     *
     * @return string|null
     */
    public function getFailureType()
    {
        return isset($this->attributes['failure_type']) ? $this->attributes['failure_type'] : null;
    }
    /**
     * Folder involved in the action
     *
     * @return string|null
     */
    public function getFolder()
    {
        return isset($this->attributes['folder']) ? $this->attributes['folder'] : null;
    }
    /**
     * Interface used to perform the action
     *
     * @return string|null
     */
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }
    /**
     * IP address from which the action was performed
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * File or Folder ID associated with the action
     *
     * @return int|null
     */
    public function getMetadataDmId()
    {
        return isset($this->attributes['metadata_dm_id']) ? $this->attributes['metadata_dm_id'] : null;
    }
    /**
     * Parent File or Folder ID associated with the action
     *
     * @return int|null
     */
    public function getParentMetadataDmId()
    {
        return isset($this->attributes['parent_metadata_dm_id']) ? $this->attributes['parent_metadata_dm_id'] : null;
    }
    /**
     * File path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Site ID where the action took place
     *
     * @return int|null
     */
    public function getSiteId()
    {
        return isset($this->attributes['site_id']) ? $this->attributes['site_id'] : null;
    }
    /**
     * Source path, for moves and copies
     *
     * @return string|null
     */
    public function getSrc()
    {
        return isset($this->attributes['src']) ? $this->attributes['src'] : null;
    }
    /**
     * User ID who performed the action
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * Username who performed the action
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * List Action Logs
     *
     * GET /action_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `folder`, `path`, `src`, `destination`, `created_at`, `user_id` or `username`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `folder`, `path`, `src`, `destination` or `username`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ folder ]`, `[ path ]`, `[ src ]`, `[ destination ]`, `[ created_at ]`, `[ user_id ]`, `[ username ]`, `[ folder, path ]`, `[ folder, src ]`, `[ folder, destination ]`, `[ folder, created_at ]`, `[ folder, user_id ]`, `[ folder, username ]`, `[ path, src ]`, `[ path, destination ]`, `[ path, created_at ]`, `[ path, user_id ]`, `[ path, username ]`, `[ src, destination ]`, `[ src, created_at ]`, `[ src, user_id ]`, `[ src, username ]`, `[ destination, created_at ]`, `[ destination, user_id ]`, `[ destination, username ]`, `[ created_at, user_id ]`, `[ created_at, username ]`, `[ user_id, username ]`, `[ folder, path, src ]`, `[ folder, path, destination ]`, `[ folder, path, created_at ]`, `[ folder, path, user_id ]`, `[ folder, path, username ]`, `[ folder, src, destination ]`, `[ folder, src, created_at ]`, `[ folder, src, user_id ]`, `[ folder, src, username ]`, `[ folder, destination, created_at ]`, `[ folder, destination, user_id ]`, `[ folder, destination, username ]`, `[ folder, created_at, user_id ]`, `[ folder, created_at, username ]`, `[ folder, user_id, username ]`, `[ path, src, destination ]`, `[ path, src, created_at ]`, `[ path, src, user_id ]`, `[ path, src, username ]`, `[ path, destination, created_at ]`, `[ path, destination, user_id ]`, `[ path, destination, username ]`, `[ path, created_at, user_id ]`, `[ path, created_at, username ]`, `[ path, user_id, username ]`, `[ src, destination, created_at ]`, `[ src, destination, user_id ]`, `[ src, destination, username ]`, `[ src, created_at, user_id ]`, `[ src, created_at, username ]`, `[ src, user_id, username ]`, `[ destination, created_at, user_id ]`, `[ destination, created_at, username ]`, `[ destination, user_id, username ]`, `[ created_at, user_id, username ]`, `[ folder, path, src, destination ]`, `[ folder, path, src, created_at ]`, `[ folder, path, src, user_id ]`, `[ folder, path, src, username ]`, `[ folder, path, destination, created_at ]`, `[ folder, path, destination, user_id ]`, `[ folder, path, destination, username ]`, `[ folder, path, created_at, user_id ]`, `[ folder, path, created_at, username ]`, `[ folder, path, user_id, username ]`, `[ folder, src, destination, created_at ]`, `[ folder, src, destination, user_id ]`, `[ folder, src, destination, username ]`, `[ folder, src, created_at, user_id ]`, `[ folder, src, created_at, username ]`, `[ folder, src, user_id, username ]`, `[ folder, destination, created_at, user_id ]`, `[ folder, destination, created_at, username ]`, `[ folder, destination, user_id, username ]`, `[ folder, created_at, user_id, username ]`, `[ path, src, destination, created_at ]`, `[ path, src, destination, user_id ]`, `[ path, src, destination, username ]`, `[ path, src, created_at, user_id ]`, `[ path, src, created_at, username ]`, `[ path, src, user_id, username ]`, `[ path, destination, created_at, user_id ]`, `[ path, destination, created_at, username ]`, `[ path, destination, user_id, username ]`, `[ path, created_at, user_id, username ]`, `[ src, destination, created_at, user_id ]`, `[ src, destination, created_at, username ]`, `[ src, destination, user_id, username ]`, `[ src, created_at, user_id, username ]`, `[ destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at ]`, `[ folder, path, src, destination, user_id ]`, `[ folder, path, src, destination, username ]`, `[ folder, path, src, created_at, user_id ]`, `[ folder, path, src, created_at, username ]`, `[ folder, path, src, user_id, username ]`, `[ folder, path, destination, created_at, user_id ]`, `[ folder, path, destination, created_at, username ]`, `[ folder, path, destination, user_id, username ]`, `[ folder, path, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id ]`, `[ folder, src, destination, created_at, username ]`, `[ folder, src, destination, user_id, username ]`, `[ folder, src, created_at, user_id, username ]`, `[ folder, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id ]`, `[ path, src, destination, created_at, username ]`, `[ path, src, destination, user_id, username ]`, `[ path, src, created_at, user_id, username ]`, `[ path, destination, created_at, user_id, username ]`, `[ src, destination, created_at, user_id, username ]`, `[ folder, path, src, destination, created_at, user_id ]`, `[ folder, path, src, destination, created_at, username ]`, `[ folder, path, src, destination, user_id, username ]`, `[ folder, path, src, created_at, user_id, username ]`, `[ folder, path, destination, created_at, user_id, username ]`, `[ folder, src, destination, created_at, user_id, username ]`, `[ path, src, destination, created_at, user_id, username ]` or `[ folder, path, src, destination, created_at, user_id, username ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ActionLog[]
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

        $response = Api::sendRequest('/action_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ActionLog((array) $obj, $options);
        }

        return $return_array;
    }
}
