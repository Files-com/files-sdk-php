<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Partner
 *
 * @package Files
 */
class Partner
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
    // boolean # Allow users created under this Partner to bypass Two-Factor Authentication policies.
    public function getAllowBypassing2faPolicies()
    {
        return @$this->attributes['allow_bypassing_2fa_policies'];
    }

    public function setAllowBypassing2faPolicies($value)
    {
        return $this->attributes['allow_bypassing_2fa_policies'] = $value;
    }
    // boolean # Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    public function getAllowCredentialChanges()
    {
        return @$this->attributes['allow_credential_changes'];
    }

    public function setAllowCredentialChanges($value)
    {
        return $this->attributes['allow_credential_changes'] = $value;
    }
    // boolean # Allow Partner Admins to provide GPG keys.
    public function getAllowProvidingGpgKeys()
    {
        return @$this->attributes['allow_providing_gpg_keys'];
    }

    public function setAllowProvidingGpgKeys($value)
    {
        return $this->attributes['allow_providing_gpg_keys'] = $value;
    }
    // boolean # Allow Partner Admins to create users.
    public function getAllowUserCreation()
    {
        return @$this->attributes['allow_user_creation'];
    }

    public function setAllowUserCreation($value)
    {
        return $this->attributes['allow_user_creation'] = $value;
    }
    // int64 # The unique ID of the Partner.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # The name of the Partner.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Notes about this Partner.
    public function getNotes()
    {
        return @$this->attributes['notes'];
    }

    public function setNotes($value)
    {
        return $this->attributes['notes'] = $value;
    }
    // array(int64) # Array of User IDs that are Partner Admins for this Partner.
    public function getPartnerAdminIds()
    {
        return @$this->attributes['partner_admin_ids'];
    }

    public function setPartnerAdminIds($value)
    {
        return $this->attributes['partner_admin_ids'] = $value;
    }
    // string # The root folder path for this Partner.
    public function getRootFolder()
    {
        return @$this->attributes['root_folder'];
    }

    public function setRootFolder($value)
    {
        return $this->attributes['root_folder'] = $value;
    }
    // string # Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    public function getTags()
    {
        return @$this->attributes['tags'];
    }

    public function setTags($value)
    {
        return $this->attributes['tags'] = $value;
    }
    // array(int64) # Array of User IDs that belong to this Partner.
    public function getUserIds()
    {
        return @$this->attributes['user_ids'];
    }

    public function setUserIds($value)
    {
        return $this->attributes['user_ids'] = $value;
    }

    // Parameters:
    //   allow_bypassing_2fa_policies - boolean - Allow users created under this Partner to bypass Two-Factor Authentication policies.
    //   allow_credential_changes - boolean - Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    //   allow_providing_gpg_keys - boolean - Allow Partner Admins to provide GPG keys.
    //   allow_user_creation - boolean - Allow Partner Admins to create users.
    //   notes - string - Notes about this Partner.
    //   root_folder - string - The root folder path for this Partner.
    //   tags - string - Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    //   name - string - The name of the Partner.
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

        if (@$params['notes'] && !is_string(@$params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (@$params['root_folder'] && !is_string(@$params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        if (@$params['tags'] && !is_string(@$params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/partners/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Partner((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/partners/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/partners', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Partner((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Partner ID.
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

        $response = Api::sendRequest('/partners/' . @$params['id'] . '', 'GET', $params, $options);

        return new Partner((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   allow_bypassing_2fa_policies - boolean - Allow users created under this Partner to bypass Two-Factor Authentication policies.
    //   allow_credential_changes - boolean - Allow Partner Admins to change or reset credentials for users belonging to this Partner.
    //   allow_providing_gpg_keys - boolean - Allow Partner Admins to provide GPG keys.
    //   allow_user_creation - boolean - Allow Partner Admins to create users.
    //   notes - string - Notes about this Partner.
    //   root_folder - string - The root folder path for this Partner.
    //   tags - string - Comma-separated list of Tags for this Partner. Tags are used for other features, such as UserLifecycleRules, which can target specific tags.  Tags must only contain lowercase letters, numbers, and hyphens.
    //   name (required) - string - The name of the Partner.
    public static function create($params = [], $options = [])
    {
        if (!@$params['name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: name');
        }

        if (@$params['notes'] && !is_string(@$params['notes'])) {
            throw new \Files\Exception\InvalidParameterException('$notes must be of type string; received ' . gettype(@$params['notes']));
        }

        if (@$params['root_folder'] && !is_string(@$params['root_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$root_folder must be of type string; received ' . gettype(@$params['root_folder']));
        }

        if (@$params['tags'] && !is_string(@$params['tags'])) {
            throw new \Files\Exception\InvalidParameterException('$tags must be of type string; received ' . gettype(@$params['tags']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        $response = Api::sendRequest('/partners', 'POST', $params, $options);

        return new Partner((array) (@$response->data ?: []), $options);
    }
}
