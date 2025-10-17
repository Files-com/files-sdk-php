<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class UserLifecycleRule
 *
 * @package Files
 */
class UserLifecycleRule
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
    // int64 # User Lifecycle Rule ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # User authentication method for which the rule will apply.
    public function getAuthenticationMethod()
    {
        return @$this->attributes['authentication_method'];
    }

    public function setAuthenticationMethod($value)
    {
        return $this->attributes['authentication_method'] = $value;
    }
    // array(int64) # Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
    public function getGroupIds()
    {
        return @$this->attributes['group_ids'];
    }

    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    // string # Action to take on inactive users (disable or delete)
    public function getAction()
    {
        return @$this->attributes['action'];
    }

    public function setAction($value)
    {
        return $this->attributes['action'] = $value;
    }
    // int64 # Number of days of inactivity before the rule applies
    public function getInactivityDays()
    {
        return @$this->attributes['inactivity_days'];
    }

    public function setInactivityDays($value)
    {
        return $this->attributes['inactivity_days'] = $value;
    }
    // boolean # If true, the rule will apply to folder admins.
    public function getIncludeFolderAdmins()
    {
        return @$this->attributes['include_folder_admins'];
    }

    public function setIncludeFolderAdmins($value)
    {
        return $this->attributes['include_folder_admins'] = $value;
    }
    // boolean # If true, the rule will apply to site admins.
    public function getIncludeSiteAdmins()
    {
        return @$this->attributes['include_site_admins'];
    }

    public function setIncludeSiteAdmins($value)
    {
        return $this->attributes['include_site_admins'] = $value;
    }
    // string # User Lifecycle Rule name
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
    public function getPartnerTag()
    {
        return @$this->attributes['partner_tag'];
    }

    public function setPartnerTag($value)
    {
        return $this->attributes['partner_tag'] = $value;
    }
    // int64 # Site ID
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }

    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    // string # State of the users to apply the rule to (inactive or disabled)
    public function getUserState()
    {
        return @$this->attributes['user_state'];
    }

    public function setUserState($value)
    {
        return $this->attributes['user_state'] = $value;
    }
    // string # If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
    public function getUserTag()
    {
        return @$this->attributes['user_tag'];
    }

    public function setUserTag($value)
    {
        return $this->attributes['user_tag'] = $value;
    }

    // Parameters:
    //   action - string - Action to take on inactive users (disable or delete)
    //   authentication_method - string - User authentication method for which the rule will apply.
    //   group_ids - array(int64) - Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
    //   inactivity_days - int64 - Number of days of inactivity before the rule applies
    //   include_site_admins - boolean - If true, the rule will apply to site admins.
    //   include_folder_admins - boolean - If true, the rule will apply to folder admins.
    //   name - string - User Lifecycle Rule name
    //   partner_tag - string - If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
    //   user_state - string - State of the users to apply the rule to (inactive or disabled)
    //   user_tag - string - If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
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

        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (@$params['authentication_method'] && !is_string(@$params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (@$params['group_ids'] && !is_array(@$params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type array; received ' . gettype(@$params['group_ids']));
        }

        if (@$params['inactivity_days'] && !is_int(@$params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['partner_tag'] && !is_string(@$params['partner_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_tag must be of type string; received ' . gettype(@$params['partner_tag']));
        }

        if (@$params['user_state'] && !is_string(@$params['user_state'])) {
            throw new \Files\Exception\InvalidParameterException('$user_state must be of type string; received ' . gettype(@$params['user_state']));
        }

        if (@$params['user_tag'] && !is_string(@$params['user_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$user_tag must be of type string; received ' . gettype(@$params['user_tag']));
        }

        $response = Api::sendRequest('/user_lifecycle_rules/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new UserLifecycleRule((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/user_lifecycle_rules/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `site_id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/user_lifecycle_rules', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new UserLifecycleRule((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - User Lifecycle Rule ID.
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

        $response = Api::sendRequest('/user_lifecycle_rules/' . @$params['id'] . '', 'GET', $params, $options);

        return new UserLifecycleRule((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   action - string - Action to take on inactive users (disable or delete)
    //   authentication_method - string - User authentication method for which the rule will apply.
    //   group_ids - array(int64) - Array of Group IDs to which the rule applies. If empty or not set, the rule applies to all users.
    //   inactivity_days - int64 - Number of days of inactivity before the rule applies
    //   include_site_admins - boolean - If true, the rule will apply to site admins.
    //   include_folder_admins - boolean - If true, the rule will apply to folder admins.
    //   name - string - User Lifecycle Rule name
    //   partner_tag - string - If provided, only users belonging to Partners with this tag at the Partner level will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
    //   user_state - string - State of the users to apply the rule to (inactive or disabled)
    //   user_tag - string - If provided, only users with this tag will be affected by the rule. Tags must only contain lowercase letters, numbers, and hyphens.
    public static function create($params = [], $options = [])
    {
        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (@$params['authentication_method'] && !is_string(@$params['authentication_method'])) {
            throw new \Files\Exception\InvalidParameterException('$authentication_method must be of type string; received ' . gettype(@$params['authentication_method']));
        }

        if (@$params['group_ids'] && !is_array(@$params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type array; received ' . gettype(@$params['group_ids']));
        }

        if (@$params['inactivity_days'] && !is_int(@$params['inactivity_days'])) {
            throw new \Files\Exception\InvalidParameterException('$inactivity_days must be of type int; received ' . gettype(@$params['inactivity_days']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['partner_tag'] && !is_string(@$params['partner_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_tag must be of type string; received ' . gettype(@$params['partner_tag']));
        }

        if (@$params['user_state'] && !is_string(@$params['user_state'])) {
            throw new \Files\Exception\InvalidParameterException('$user_state must be of type string; received ' . gettype(@$params['user_state']));
        }

        if (@$params['user_tag'] && !is_string(@$params['user_tag'])) {
            throw new \Files\Exception\InvalidParameterException('$user_tag must be of type string; received ' . gettype(@$params['user_tag']));
        }

        $response = Api::sendRequest('/user_lifecycle_rules', 'POST', $params, $options);

        return new UserLifecycleRule((array) (@$response->data ?: []), $options);
    }
}
