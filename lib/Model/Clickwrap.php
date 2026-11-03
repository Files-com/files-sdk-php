<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Clickwrap is a legal agreement (such as an NDA or Terms of Use) that your Users and/or Bundle/Inbox participants will need to agree to via a "Clickwrap" UI before accessing the site, bundle, or inbox.
 *
 * @property int|null $id Clickwrap ID
 * @property string|null $name Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
 * @property string|null $body Body text of Clickwrap (supports Markdown formatting).
 * @property string|null $use_with_users Use this Clickwrap for Users? Values: `none`, `require` (new user signup via email invitation only), `require_all_users_once` (show to all users at their next web login; once accepted, not shown again), `require_all_users_always` (show to all users on every web login).
 * @property string|null $use_with_bundles Use this Clickwrap for Bundles?
 * @property string|null $use_with_inboxes Use this Clickwrap for Inboxes?
 * @package Files
 */
class Clickwrap
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
     * Clickwrap ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Clickwrap ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * Body text of Clickwrap (supports Markdown formatting).
     *
     * @return string|null
     */
    public function getBody()
    {
        return isset($this->attributes['body']) ? $this->attributes['body'] : null;
    }

    /**
     * Body text of Clickwrap (supports Markdown formatting).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setBody($value)
    {
        return $this->attributes['body'] = $value;
    }
    /**
     * Use this Clickwrap for Users?  Values: `none`, `require` (new user signup via email invitation only), `require_all_users_once` (show to all users at their next web login; once accepted, not shown again), `require_all_users_always` (show to all users on every web login).
     *
     * @return string|null
     */
    public function getUseWithUsers()
    {
        return isset($this->attributes['use_with_users']) ? $this->attributes['use_with_users'] : null;
    }

    /**
     * Use this Clickwrap for Users?  Values: `none`, `require` (new user signup via email invitation only), `require_all_users_once` (show to all users at their next web login; once accepted, not shown again), `require_all_users_always` (show to all users on every web login).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUseWithUsers($value)
    {
        return $this->attributes['use_with_users'] = $value;
    }
    /**
     * Use this Clickwrap for Bundles?
     *
     * @return string|null
     */
    public function getUseWithBundles()
    {
        return isset($this->attributes['use_with_bundles']) ? $this->attributes['use_with_bundles'] : null;
    }

    /**
     * Use this Clickwrap for Bundles?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUseWithBundles($value)
    {
        return $this->attributes['use_with_bundles'] = $value;
    }
    /**
     * Use this Clickwrap for Inboxes?
     *
     * @return string|null
     */
    public function getUseWithInboxes()
    {
        return isset($this->attributes['use_with_inboxes']) ? $this->attributes['use_with_inboxes'] : null;
    }

    /**
     * Use this Clickwrap for Inboxes?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUseWithInboxes($value)
    {
        return $this->attributes['use_with_inboxes'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Clickwrap with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Clickwrap
     *
     * PATCH /clickwraps/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Clickwrap ID.
     *     - `name` (string, optional): Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
     *     - `body` (string, optional): Body text of Clickwrap (supports Markdown formatting).
     *     - `use_with_bundles` (string, optional): Use this Clickwrap for Bundles? Allowed values: none, available, require, available_to_all_users.
     *     - `use_with_inboxes` (string, optional): Use this Clickwrap for Inboxes? Allowed values: none, available, require, available_to_all_users.
     *     - `use_with_users` (string, optional): Use this Clickwrap for Users?  Values: `none`, `require` (new user signup via email invitation only), `require_all_users_once` (show to all users at their next web login; once accepted, not shown again), `require_all_users_always` (show to all users on every web login). Allowed values: none, require, require_all_users_once, require_all_users_always.
     * @return \Files\Model\Clickwrap
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

        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['body']) && !is_string($params['body'])) {
            throw new \Files\Exception\InvalidParameterException('$body must be of type string; received ' . gettype(@$params['body']));
        }

        if (isset($params['use_with_bundles']) && !is_string($params['use_with_bundles'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_bundles must be of type string; received ' . gettype(@$params['use_with_bundles']));
        }

        if (isset($params['use_with_inboxes']) && !is_string($params['use_with_inboxes'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_inboxes must be of type string; received ' . gettype(@$params['use_with_inboxes']));
        }

        if (isset($params['use_with_users']) && !is_string($params['use_with_users'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_users must be of type string; received ' . gettype(@$params['use_with_users']));
        }

        $response = Api::sendRequest('/clickwraps/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Clickwrap(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Clickwrap
     *
     * DELETE /clickwraps/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Clickwrap ID.
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

        $response = Api::sendRequest('/clickwraps/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Clickwrap
     *
     * DELETE /clickwraps/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Clickwrap ID.
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
     * List Clickwraps
     *
     * GET /clickwraps
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Clickwrap[]
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

        $response = Api::sendRequest('/clickwraps', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Clickwrap(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/clickwraps', $params, $options, Clickwrap::class);
    }

    /**
     * Show Clickwrap
     *
     * GET /clickwraps/{id}
     *
     * @param int $id Clickwrap ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Clickwrap
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

        $response = Api::sendRequest('/clickwraps/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Clickwrap(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Clickwrap
     *
     * GET /clickwraps/{id}
     *
     * Alias of find().
     *
     * @param int $id Clickwrap ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Clickwrap
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Clickwrap
     *
     * POST /clickwraps
     *
     * @param array $params Request parameters.
     *     - `name` (string, optional): Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
     *     - `body` (string, optional): Body text of Clickwrap (supports Markdown formatting).
     *     - `use_with_bundles` (string, optional): Use this Clickwrap for Bundles? Allowed values: none, available, require, available_to_all_users.
     *     - `use_with_inboxes` (string, optional): Use this Clickwrap for Inboxes? Allowed values: none, available, require, available_to_all_users.
     *     - `use_with_users` (string, optional): Use this Clickwrap for Users?  Values: `none`, `require` (new user signup via email invitation only), `require_all_users_once` (show to all users at their next web login; once accepted, not shown again), `require_all_users_always` (show to all users on every web login). Allowed values: none, require, require_all_users_once, require_all_users_always.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Clickwrap
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (isset($params['body']) && !is_string($params['body'])) {
            throw new \Files\Exception\InvalidParameterException('$body must be of type string; received ' . gettype(@$params['body']));
        }

        if (isset($params['use_with_bundles']) && !is_string($params['use_with_bundles'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_bundles must be of type string; received ' . gettype(@$params['use_with_bundles']));
        }

        if (isset($params['use_with_inboxes']) && !is_string($params['use_with_inboxes'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_inboxes must be of type string; received ' . gettype(@$params['use_with_inboxes']));
        }

        if (isset($params['use_with_users']) && !is_string($params['use_with_users'])) {
            throw new \Files\Exception\InvalidParameterException('$use_with_users must be of type string; received ' . gettype(@$params['use_with_users']));
        }

        $response = Api::sendRequest('/clickwraps', 'POST', $params, $options);

        return new Clickwrap(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
