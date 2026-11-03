<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A ShareGroup is a way for you to store and name groups of email contacts to be used for sending share and inbox invitations.
 *
 * @property int|null $id Share Group ID
 * @property string|null $name Name of the share group
 * @property string|null $notes Additional notes of the share group
 * @property int|null $user_id Owner User ID
 * @property object[]|null $members A list of share group members
 * @package Files
 */
class ShareGroup
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
     * Share Group ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Share Group ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Name of the share group
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name of the share group
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Additional notes of the share group
     *
     * @return string|null
     */
    public function getNotes()
    {
        return isset($this->attributes['notes']) ? $this->attributes['notes'] : null;
    }

    /**
     * Additional notes of the share group
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    /**
     * Owner User ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * Owner User ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * A list of share group members
     *
     * @return object[]|null
     */
    public function getMembers()
    {
        return isset($this->attributes['members']) ? $this->attributes['members'] : null;
    }

    /**
     * A list of share group members
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setMembers($value)
    {
        return $this->attributes['members'] = $value;
    }

    /**
     * Update Share Group
     *
     * PATCH /share_groups/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Share Group ID.
     *     - `notes` (string, optional): Additional notes of the share group
     *     - `name` (string, optional): Name of the share group
     *     - `members` ((array|object)[], optional): A list of share group members.
     * @return \Files\Model\ShareGroup
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

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['members']) && !is_array($params['members'])) {
            throw new \Files\Exception\InvalidParameterException('$members must be of type array; received ' . gettype(@$params['members']));
        }

        $response = Api::sendRequest('/share_groups/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new ShareGroup((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Share Group
     *
     * DELETE /share_groups/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Share Group ID.
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

        $response = Api::sendRequest('/share_groups/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Share Group
     *
     * DELETE /share_groups/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Share Group ID.
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
     * List Share Groups
     *
     * GET /share_groups
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ShareGroup[]
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

        $response = Api::sendRequest('/share_groups', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ShareGroup((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Share Group
     *
     * GET /share_groups/{id}
     *
     * @param int $id Share Group ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ShareGroup
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

        $response = Api::sendRequest('/share_groups/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ShareGroup((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Share Group
     *
     * GET /share_groups/{id}
     *
     * Alias of find().
     *
     * @param int $id Share Group ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ShareGroup
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Share Group
     *
     * POST /share_groups
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `notes` (string, optional): Additional notes of the share group
     *     - `name` (string, required): Name of the share group
     *     - `members` ((array|object)[], required): A list of share group members.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\ShareGroup
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['name'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (empty($params['members'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: members');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['notes']) && !is_string($params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['members']) && !is_array($params['members'])) {
            throw new \Files\Exception\InvalidParameterException('$members must be of type array; received ' . gettype(@$params['members']));
        }

        $response = Api::sendRequest('/share_groups', 'POST', $params, $options);

        return new ShareGroup((array) (@$response->data ?: []), $options);
    }
}
