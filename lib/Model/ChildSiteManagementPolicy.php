<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ChildSiteManagementPolicy
 *
 * @package Files
 */
class ChildSiteManagementPolicy
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
    // int64 # ChildSiteManagementPolicy ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # ID of the Site managing the policy
    public function getSiteId()
    {
        return @$this->attributes['site_id'];
    }

    public function setSiteId($value)
    {
        return $this->attributes['site_id'] = $value;
    }
    // string # The name of the setting that is managed by the policy
    public function getSiteSettingName()
    {
        return @$this->attributes['site_setting_name'];
    }

    public function setSiteSettingName($value)
    {
        return $this->attributes['site_setting_name'] = $value;
    }
    // string # The value for the setting that will be enforced for all child sites that are not exempt
    public function getManagedValue()
    {
        return @$this->attributes['managed_value'];
    }

    public function setManagedValue($value)
    {
        return $this->attributes['managed_value'] = $value;
    }
    // array(int64) # The list of child site IDs that are exempt from this policy
    public function getSkipChildSiteIds()
    {
        return @$this->attributes['skip_child_site_ids'];
    }

    public function setSkipChildSiteIds($value)
    {
        return $this->attributes['skip_child_site_ids'] = $value;
    }

    // Parameters:
    //   site_setting_name (required) - string - The name of the setting that is managed by the policy
    //   managed_value (required) - string - The value for the setting that will be enforced for all child sites that are not exempt
    //   skip_child_site_ids - array(int64) - The list of child site IDs that are exempt from this policy
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

        if (!@$params['site_setting_name']) {
            if (@$this->site_setting_name) {
                $params['site_setting_name'] = $this->site_setting_name;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: site_setting_name');
            }
        }

        if (!@$params['managed_value']) {
            if (@$this->managed_value) {
                $params['managed_value'] = $this->managed_value;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: managed_value');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['site_setting_name'] && !is_string(@$params['site_setting_name'])) {
            throw new \Files\Exception\InvalidParameterException('$site_setting_name must be of type string; received ' . gettype(@$params['site_setting_name']));
        }

        if (@$params['managed_value'] && !is_string(@$params['managed_value'])) {
            throw new \Files\Exception\InvalidParameterException('$managed_value must be of type string; received ' . gettype(@$params['managed_value']));
        }

        if (@$params['skip_child_site_ids'] && !is_array(@$params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        $response = Api::sendRequest('/child_site_management_policies/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/child_site_management_policies/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/child_site_management_policies', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ChildSiteManagementPolicy((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Child Site Management Policy ID.
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

        $response = Api::sendRequest('/child_site_management_policies/' . @$params['id'] . '', 'GET', $params, $options);

        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   site_setting_name (required) - string - The name of the setting that is managed by the policy
    //   managed_value (required) - string - The value for the setting that will be enforced for all child sites that are not exempt
    //   skip_child_site_ids - array(int64) - The list of child site IDs that are exempt from this policy
    public static function create($params = [], $options = [])
    {
        if (!@$params['site_setting_name']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: site_setting_name');
        }

        if (!@$params['managed_value']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: managed_value');
        }

        if (@$params['site_setting_name'] && !is_string(@$params['site_setting_name'])) {
            throw new \Files\Exception\InvalidParameterException('$site_setting_name must be of type string; received ' . gettype(@$params['site_setting_name']));
        }

        if (@$params['managed_value'] && !is_string(@$params['managed_value'])) {
            throw new \Files\Exception\InvalidParameterException('$managed_value must be of type string; received ' . gettype(@$params['managed_value']));
        }

        if (@$params['skip_child_site_ids'] && !is_array(@$params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        $response = Api::sendRequest('/child_site_management_policies', 'POST', $params, $options);

        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $options);
    }
}
