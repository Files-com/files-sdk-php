<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FormFieldSet
 *
 * @package Files
 */
class FormFieldSet
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // int64 # Form field set id
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Title to be displayed
    public function getTitle()
    {
        return isset($this->attributes['title']) ? $this->attributes['title'] : null;
    }

    public function setTitle($value)
    {
        return $this->attributes['title'] = $value;
    }
    // boolean # Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
    public function getRequireCompleteMetadataCapture()
    {
        return isset($this->attributes['require_complete_metadata_capture']) ? $this->attributes['require_complete_metadata_capture'] : null;
    }

    public function setRequireCompleteMetadataCapture($value)
    {
        return $this->attributes['require_complete_metadata_capture'] = $value;
    }
    // array(int64) # Layout of the form
    public function getFormLayout()
    {
        return isset($this->attributes['form_layout']) ? $this->attributes['form_layout'] : null;
    }

    public function setFormLayout($value)
    {
        return $this->attributes['form_layout'] = $value;
    }
    // array(object) # Associated form field definitions; authenticated form field set responses include historical definitions, while form_layout identifies current fields
    public function getFormFields()
    {
        return isset($this->attributes['form_fields']) ? $this->attributes['form_fields'] : null;
    }

    public function setFormFields($value)
    {
        return $this->attributes['form_fields'] = $value;
    }
    // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing name
    public function getSkipName()
    {
        return isset($this->attributes['skip_name']) ? $this->attributes['skip_name'] : null;
    }

    public function setSkipName($value)
    {
        return $this->attributes['skip_name'] = $value;
    }
    // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing email
    public function getSkipEmail()
    {
        return isset($this->attributes['skip_email']) ? $this->attributes['skip_email'] : null;
    }

    public function setSkipEmail($value)
    {
        return $this->attributes['skip_email'] = $value;
    }
    // boolean # Any associated InboxRegistrations or BundleRegistrations can be saved without providing company
    public function getSkipCompany()
    {
        return isset($this->attributes['skip_company']) ? $this->attributes['skip_company'] : null;
    }

    public function setSkipCompany($value)
    {
        return $this->attributes['skip_company'] = $value;
    }
    // boolean # Form Field Set is in use by an active Inbox / Bundle / Inbox Registration / Bundle Registration
    public function getInUse()
    {
        return isset($this->attributes['in_use']) ? $this->attributes['in_use'] : null;
    }

    public function setInUse($value)
    {
        return $this->attributes['in_use'] = $value;
    }
    // int64 # User ID.  Provide a value of `0` to operate the current session's user.
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // int64 # Workspace ID
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }

    // Parameters:
    //   title - string - Title to be displayed
    //   require_complete_metadata_capture - boolean - Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
    //   workspace_id - int64 - Workspace ID
    //   skip_email - boolean - Skip validating form email
    //   skip_name - boolean - Skip validating form name
    //   skip_company - boolean - Skip validating company
    //   form_fields - array(object)
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

        if (!empty($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['form_fields']) && !is_array($params['form_fields'])) {
            throw new \Files\Exception\InvalidParameterException('$form_fields must be of type array; received ' . gettype(@$params['form_fields']));
        }

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new FormFieldSet((array) (@$response->data ?: []), $this->options);
    }

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

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

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


    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
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

        $response = Api::sendRequest('/form_field_sets', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new FormFieldSet((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Form Field Set ID.
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

        $response = Api::sendRequest('/form_field_sets/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new FormFieldSet((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   title - string - Title to be displayed
    //   require_complete_metadata_capture - boolean - Reject answers over 1024 characters or more than 32 answered custom fields to ensure complete file metadata capture
    //   workspace_id - int64 - Workspace ID
    //   skip_email - boolean - Skip validating form email
    //   skip_name - boolean - Skip validating form name
    //   skip_company - boolean - Skip validating company
    //   form_fields - array(object)
    public static function create($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['title']) && !is_string($params['title'])) {
            throw new \Files\Exception\InvalidParameterException('$title must be of type string; received ' . gettype(@$params['title']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['form_fields']) && !is_array($params['form_fields'])) {
            throw new \Files\Exception\InvalidParameterException('$form_fields must be of type array; received ' . gettype(@$params['form_fields']));
        }

        $response = Api::sendRequest('/form_field_sets', 'POST', $params, $options);

        return new FormFieldSet((array) (@$response->data ?: []), $options);
    }
}
