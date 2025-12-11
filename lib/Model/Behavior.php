<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Behavior
 *
 * @package Files
 */
class Behavior
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
        return @$this->attributes[$name];
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
    // int64 # Folder behavior ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Folder path.  Note that Behavior paths cannot be updated once initially set.  You will need to remove and re-create the behavior on the new path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # URL for attached file
    public function getAttachmentUrl()
    {
        return @$this->attributes['attachment_url'];
    }

    public function setAttachmentUrl($value)
    {
        return $this->attributes['attachment_url'] = $value;
    }
    // string # Behavior type.
    public function getBehavior()
    {
        return @$this->attributes['behavior'];
    }

    public function setBehavior($value)
    {
        return $this->attributes['behavior'] = $value;
    }
    // string # Name for this behavior.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Description for this behavior.
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // object # Settings for this behavior.  See the section above for an example value to provide here.  Formatting is different for each Behavior type.  May be sent as nested JSON or a single JSON-encoded string.  If using XML encoding for the API call, this data must be sent as a JSON-encoded string.
    public function getValue()
    {
        return @$this->attributes['value'];
    }

    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    // boolean # If true, the parent folder's behavior will be disabled for this folder and its children.
    public function getDisableParentFolderBehavior()
    {
        return @$this->attributes['disable_parent_folder_behavior'];
    }

    public function setDisableParentFolderBehavior($value)
    {
        return $this->attributes['disable_parent_folder_behavior'] = $value;
    }
    // boolean # Is behavior recursive?
    public function getRecursive()
    {
        return @$this->attributes['recursive'];
    }

    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    // file # Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
    public function getAttachmentFile()
    {
        return @$this->attributes['attachment_file'];
    }

    public function setAttachmentFile($value)
    {
        return $this->attributes['attachment_file'] = $value;
    }
    // boolean # If `true`, delete the file stored in `attachment`.
    public function getAttachmentDelete()
    {
        return @$this->attributes['attachment_delete'];
    }

    public function setAttachmentDelete($value)
    {
        return $this->attributes['attachment_delete'] = $value;
    }

    // Parameters:
    //   value - string - This field stores a hash of data specific to the type of behavior. See The Behavior Types section for example values for each type of behavior.
    //   attachment_file - file - Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
    //   disable_parent_folder_behavior - boolean - If `true`, the parent folder's behavior will be disabled for this folder and its children. This is the main mechanism for canceling out a `recursive` behavior higher in the folder tree.
    //   recursive - boolean - If `true`, behavior is treated as recursive, meaning that it impacts child folders as well.
    //   name - string - Name for this behavior.
    //   description - string - Description for this behavior.
    //   attachment_delete - boolean - If `true`, delete the file stored in `attachment`.
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['value'] && !is_string(@$params['value'])) {
            throw new \Files\Exception\InvalidParameterException('$value must be of type string; received ' . gettype(@$params['value']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        $response = Api::sendRequest('/behaviors/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Behavior((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/behaviors/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `behavior`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `clickwrap_id`, `form_field_set_id`, `impacts_ui`, `remote_server_id` or `behavior`. Valid field combinations are `[ impacts_ui, behavior ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/behaviors', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Behavior((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Behavior ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/behaviors/' . @$params['id'] . '', 'GET', $params, $options);

        return new Behavior((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `behavior`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `impacts_ui` and `behavior`. Valid field combinations are `[ impacts_ui, behavior ]`.
    //   path (required) - string - Path to operate on.
    //   ancestor_behaviors - boolean - If `true`, behaviors above this path are shown.
    public static function listFor($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/behaviors/folders/' . @$params['path'] . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Behavior((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   value - string - This field stores a hash of data specific to the type of behavior. See The Behavior Types section for example values for each type of behavior.
    //   attachment_file - file - Certain behaviors may require a file, for instance, the `watermark` behavior requires a watermark image. Attach that file here.
    //   disable_parent_folder_behavior - boolean - If `true`, the parent folder's behavior will be disabled for this folder and its children. This is the main mechanism for canceling out a `recursive` behavior higher in the folder tree.
    //   recursive - boolean - If `true`, behavior is treated as recursive, meaning that it impacts child folders as well.
    //   name - string - Name for this behavior.
    //   description - string - Description for this behavior.
    //   path (required) - string - Path where this behavior should apply.
    //   behavior (required) - string - Behavior type.
    public static function create($params = [], $options = [])
    {
        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!@$params['behavior']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: behavior');
        }

        if (@$params['value'] && !is_string(@$params['value'])) {
            throw new \Files\Exception\InvalidParameterException('$value must be of type string; received ' . gettype(@$params['value']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['behavior'] && !is_string(@$params['behavior'])) {
            throw new \Files\Exception\InvalidParameterException('$behavior must be of type string; received ' . gettype(@$params['behavior']));
        }

        $response = Api::sendRequest('/behaviors', 'POST', $params, $options);

        return new Behavior((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   url (required) - string - URL for testing the webhook.
    //   method - string - HTTP request method (GET or POST).
    //   encoding - string - Encoding type for the webhook payload. Can be JSON, XML, or RAW (form data).
    //   headers - object - Additional request headers to send via HTTP.
    //   body - object - Additional body parameters to include in the webhook payload.
    //   action - string - Action for test body.
    public static function webhookTest($params = [], $options = [])
    {
        if (!@$params['url']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: url');
        }

        if (@$params['url'] && !is_string(@$params['url'])) {
            throw new \Files\Exception\InvalidParameterException('$url must be of type string; received ' . gettype(@$params['url']));
        }

        if (@$params['method'] && !is_string(@$params['method'])) {
            throw new \Files\Exception\InvalidParameterException('$method must be of type string; received ' . gettype(@$params['method']));
        }

        if (@$params['encoding'] && !is_string(@$params['encoding'])) {
            throw new \Files\Exception\InvalidParameterException('$encoding must be of type string; received ' . gettype(@$params['encoding']));
        }

        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        $response = Api::sendRequest('/behaviors/webhook/test', 'POST', $params, $options);

        return;
    }
}
