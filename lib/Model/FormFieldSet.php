<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Form Field Set is a custom form to be used for bundle and inbox registrations.
 *
 * @property int|null $id Form field set id
 * @property string|null $title Title to be displayed
 * @property bool|null $require_complete_metadata_capture Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
 * @property int[]|null $form_layout Layout of the form
 * @property object[]|null $form_fields Associated form field definitions; authenticated form field set responses include historical definitions, while form_layout identifies current fields
 * @property bool|null $skip_name Any associated InboxRegistrations or BundleRegistrations can be saved without providing name
 * @property bool|null $skip_email Any associated InboxRegistrations or BundleRegistrations can be saved without providing email
 * @property bool|null $skip_company Any associated InboxRegistrations or BundleRegistrations can be saved without providing company
 * @property bool|null $in_use Form Field Set is in use by an active Inbox / Bundle / Inbox Registration / Bundle Registration
 * @property int|null $user_id User ID. Provide a value of `0` to operate the current session's user.
 * @property int|null $workspace_id Workspace ID
 * @package Files
 */
class FormFieldSet
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
     * Form field set id
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Form field set id
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Title to be displayed
     *
     * @return string|null
     */
    public function getTitle()
    {
        return isset($this->attributes['title']) ? $this->attributes['title'] : null;
    }

    /**
     * Title to be displayed
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setTitle($value)
    {
        return $this->attributes['title'] = $value;
    }
    /**
     * Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
     *
     * @return bool|null
     */
    public function getRequireCompleteMetadataCapture()
    {
        return isset($this->attributes['require_complete_metadata_capture']) ? $this->attributes['require_complete_metadata_capture'] : null;
    }

    /**
     * Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRequireCompleteMetadataCapture($value)
    {
        return $this->attributes['require_complete_metadata_capture'] = $value;
    }
    /**
     * Layout of the form
     *
     * @return int[]|null
     */
    public function getFormLayout()
    {
        return isset($this->attributes['form_layout']) ? $this->attributes['form_layout'] : null;
    }

    /**
     * Layout of the form
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setFormLayout($value)
    {
        return $this->attributes['form_layout'] = $value;
    }
    /**
     * Associated form field definitions; authenticated form field set responses include historical definitions, while form_layout identifies current fields
     *
     * @return object[]|null
     */
    public function getFormFields()
    {
        return isset($this->attributes['form_fields']) ? $this->attributes['form_fields'] : null;
    }

    /**
     * Associated form field definitions; authenticated form field set responses include historical definitions, while form_layout identifies current fields
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setFormFields($value)
    {
        return $this->attributes['form_fields'] = $value;
    }
    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing name
     *
     * @return bool|null
     */
    public function getSkipName()
    {
        return isset($this->attributes['skip_name']) ? $this->attributes['skip_name'] : null;
    }

    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing name
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipName($value)
    {
        return $this->attributes['skip_name'] = $value;
    }
    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing email
     *
     * @return bool|null
     */
    public function getSkipEmail()
    {
        return isset($this->attributes['skip_email']) ? $this->attributes['skip_email'] : null;
    }

    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing email
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipEmail($value)
    {
        return $this->attributes['skip_email'] = $value;
    }
    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing company
     *
     * @return bool|null
     */
    public function getSkipCompany()
    {
        return isset($this->attributes['skip_company']) ? $this->attributes['skip_company'] : null;
    }

    /**
     * Any associated InboxRegistrations or BundleRegistrations can be saved without providing company
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipCompany($value)
    {
        return $this->attributes['skip_company'] = $value;
    }
    /**
     * Form Field Set is in use by an active Inbox / Bundle / Inbox Registration / Bundle Registration
     *
     * @return bool|null
     */
    public function getInUse()
    {
        return isset($this->attributes['in_use']) ? $this->attributes['in_use'] : null;
    }

    /**
     * Form Field Set is in use by an active Inbox / Bundle / Inbox Registration / Bundle Registration
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setInUse($value)
    {
        return $this->attributes['in_use'] = $value;
    }
    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
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
    // FormFieldSet with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Form Field Set
     *
     * PATCH /form_field_sets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Form Field Set ID.
     *     - `title` (string, optional): Title to be displayed
     *     - `require_complete_metadata_capture` (bool, optional): Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
     *     - `workspace_id` (int, optional): Workspace ID
     *     - `skip_email` (bool, optional): Skip validating form email
     *     - `skip_name` (bool, optional): Skip validating form name
     *     - `skip_company` (bool, optional): Skip validating company
     *     - `form_fields` ((array|object)[], optional):
     * @return \Files\Model\FormFieldSet
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

        if (isset($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (isset($params['require_complete_metadata_capture']) && !is_scalar($params['require_complete_metadata_capture'])) {
            throw new \Files\Exception\InvalidParameterException('$require_complete_metadata_capture must be of type boolean; received ' . gettype(@$params['require_complete_metadata_capture']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['skip_email']) && !is_scalar($params['skip_email'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_email must be of type boolean; received ' . gettype(@$params['skip_email']));
        }

        if (isset($params['skip_name']) && !is_scalar($params['skip_name'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_name must be of type boolean; received ' . gettype(@$params['skip_name']));
        }

        if (isset($params['skip_company']) && !is_scalar($params['skip_company'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_company must be of type boolean; received ' . gettype(@$params['skip_company']));
        }

        if (isset($params['form_fields']) && !is_array($params['form_fields'])) {
            throw new \Files\Exception\InvalidParameterException('$form_fields must be of type array; received ' . gettype(@$params['form_fields']));
        }

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new FormFieldSet(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Form Field Set
     *
     * DELETE /form_field_sets/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Form Field Set ID.
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

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Form Field Set
     *
     * DELETE /form_field_sets/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Form Field Set ID.
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
     * List Form Field Sets
     *
     * GET /form_field_sets
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FormFieldSet[]
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

        $response = Api::sendRequest('/form_field_sets', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new FormFieldSet(Api::modelAttributes($obj, $response->status), $options);
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

        return \Files\Page::fetch('/form_field_sets', $params, $options, FormFieldSet::class);
    }

    /**
     * Show Form Field Set
     *
     * GET /form_field_sets/{id}
     *
     * @param int $id Form Field Set ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FormFieldSet
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

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new FormFieldSet(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Form Field Set
     *
     * GET /form_field_sets/{id}
     *
     * Alias of find().
     *
     * @param int $id Form Field Set ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FormFieldSet
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Form Field Set
     *
     * POST /form_field_sets
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `title` (string, optional): Title to be displayed
     *     - `require_complete_metadata_capture` (bool, optional): Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
     *     - `workspace_id` (int, optional): Workspace ID
     *     - `skip_email` (bool, optional): Skip validating form email
     *     - `skip_name` (bool, optional): Skip validating form name
     *     - `skip_company` (bool, optional): Skip validating company
     *     - `form_fields` ((array|object)[], optional):
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FormFieldSet
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (isset($params['require_complete_metadata_capture']) && !is_scalar($params['require_complete_metadata_capture'])) {
            throw new \Files\Exception\InvalidParameterException('$require_complete_metadata_capture must be of type boolean; received ' . gettype(@$params['require_complete_metadata_capture']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['skip_email']) && !is_scalar($params['skip_email'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_email must be of type boolean; received ' . gettype(@$params['skip_email']));
        }

        if (isset($params['skip_name']) && !is_scalar($params['skip_name'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_name must be of type boolean; received ' . gettype(@$params['skip_name']));
        }

        if (isset($params['skip_company']) && !is_scalar($params['skip_company'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_company must be of type boolean; received ' . gettype(@$params['skip_company']));
        }

        if (isset($params['form_fields']) && !is_array($params['form_fields'])) {
            throw new \Files\Exception\InvalidParameterException('$form_fields must be of type array; received ' . gettype(@$params['form_fields']));
        }

        $response = Api::sendRequest('/form_field_sets', 'POST', $params, $options);

        return new FormFieldSet(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
