<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A UserCipherUse is a way to see the exact set of encryption ciphers and protocols used by a given user.
 *
 * @property int|null $id UserCipherUse ID
 * @property int|null $user_id ID of the user who performed this access
 * @property string|null $username Username of the user who performed this access
 * @property string|null $protocol_cipher The protocol and cipher employed
 * @property string|null $created_at The earliest recorded use of this combination of interface and protocol and cipher (for this user)
 * @property bool|null $insecure Is this cipher considered insecure?
 * @property string|null $interface The interface accessed
 * @property string|null $updated_at The most recent use of this combination of interface and protocol and cipher (for this user)
 * @package Files
 */
class UserCipherUse
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
     * UserCipherUse ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * ID of the user who performed this access
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }
    /**
     * Username of the user who performed this access
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }
    /**
     * The protocol and cipher employed
     *
     * @return string|null
     */
    public function getProtocolCipher()
    {
        return isset($this->attributes['protocol_cipher']) ? $this->attributes['protocol_cipher'] : null;
    }
    /**
     * The earliest recorded use of this combination of interface and protocol and cipher (for this user)
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Is this cipher considered insecure?
     *
     * @return bool|null
     */
    public function getInsecure()
    {
        return isset($this->attributes['insecure']) ? $this->attributes['insecure'] : null;
    }
    /**
     * The interface accessed
     *
     * @return string|null
     */
    public function getInterface()
    {
        return isset($this->attributes['interface']) ? $this->attributes['interface'] : null;
    }
    /**
     * The most recent use of this combination of interface and protocol and cipher (for this user)
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return isset($this->attributes['updated_at']) ? $this->attributes['updated_at'] : null;
    }

    /**
     * List User Cipher Uses
     *
     * GET /user_cipher_uses
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID. If provided, will return uses for this user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `updated_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `insecure` and `updated_at`. Valid field combinations are `[ insecure, updated_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `updated_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `updated_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `updated_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `updated_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\UserCipherUse[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/user_cipher_uses', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new UserCipherUse(Api::modelAttributes($obj, $response->status), $options);
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
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/user_cipher_uses', $params, $options, UserCipherUse::class);
    }
}
