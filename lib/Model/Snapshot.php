<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Snapshots allow you to create a read-only archive of files at a specific point in time.
 *
 * @property int|null $id The snapshot's unique ID.
 * @property string|null $expires_at When the snapshot expires.
 * @property string|null $finalized_at When the snapshot was finalized.
 * @property string|null $name A name for the snapshot.
 * @property int|null $user_id The user that created this snapshot, if applicable.
 * @property int|null $bundle_id The bundle using this snapshot, if applicable.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property string[]|null $paths An array of paths to add to the snapshot.
 * @package Files
 */
class Snapshot
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
     * The snapshot's unique ID.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * The snapshot's unique ID.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * When the snapshot expires.
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * When the snapshot expires.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * When the snapshot was finalized.
     *
     * @return string|null
     */
    public function getFinalizedAt()
    {
        return isset($this->attributes['finalized_at']) ? $this->attributes['finalized_at'] : null;
    }

    /**
     * When the snapshot was finalized.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFinalizedAt($value)
    {
        return $this->attributes['finalized_at'] = $value;
    }
    /**
     * A name for the snapshot.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * A name for the snapshot.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * The user that created this snapshot, if applicable.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * The user that created this snapshot, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * The bundle using this snapshot, if applicable.
     *
     * @return int|null
     */
    public function getBundleId()
    {
        return isset($this->attributes['bundle_id']) ? $this->attributes['bundle_id'] : null;
    }

    /**
     * The bundle using this snapshot, if applicable.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setBundleId($value)
    {
        return $this->attributes['bundle_id'] = $value;
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
    /**
     * An array of paths to add to the snapshot.
     *
     * @return string[]|null
     */
    public function getPaths()
    {
        return isset($this->attributes['paths']) ? $this->attributes['paths'] : null;
    }

    /**
     * An array of paths to add to the snapshot.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setPaths($value)
    {
        return $this->attributes['paths'] = $value;
    }

    /**
     * Finalize Snapshot
     *
     * POST /snapshots/{id}/finalize
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Snapshot ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function finalize($params = [])
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

        $response = Api::sendRequest('/snapshots/' . rawurlencode(strval(@$params['id'])) . '/finalize', 'POST', $params, $this->options);
        return;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Snapshot with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Snapshot
     *
     * PATCH /snapshots/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Snapshot ID.
     *     - `expires_at` (string, optional): When the snapshot expires.
     *     - `name` (string, optional): A name for the snapshot.
     *     - `paths` (string[], optional): An array of paths to add to the snapshot.
     * @return \Files\Model\Snapshot
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

        if (isset($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['paths']) && !is_array($params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        $response = Api::sendRequest('/snapshots/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Snapshot(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Snapshot
     *
     * DELETE /snapshots/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Snapshot ID.
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

        $response = Api::sendRequest('/snapshots/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Snapshot
     *
     * DELETE /snapshots/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Snapshot ID.
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
     * List Snapshots
     *
     * GET /snapshots
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Snapshot[]
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

        $response = Api::sendRequest('/snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Snapshot(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/snapshots', $params, $options, Snapshot::class);
    }

    /**
     * Show Snapshot
     *
     * GET /snapshots/{id}
     *
     * @param int $id Snapshot ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Snapshot
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

        $response = Api::sendRequest('/snapshots/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Snapshot(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Snapshot
     *
     * GET /snapshots/{id}
     *
     * Alias of find().
     *
     * @param int $id Snapshot ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Snapshot
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Snapshot
     *
     * POST /snapshots
     *
     * @param array $params Request parameters.
     *     - `expires_at` (string, optional): When the snapshot expires.
     *     - `name` (string, optional): A name for the snapshot.
     *     - `paths` (string[], optional): An array of paths to add to the snapshot.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Snapshot
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['paths']) && !is_array($params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/snapshots', 'POST', $params, $options);

        return new Snapshot(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
