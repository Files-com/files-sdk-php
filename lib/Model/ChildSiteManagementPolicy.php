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
    // int64 # Policy ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Type of policy.  Valid values: `settings`.
    public function getPolicyType()
    {
        return @$this->attributes['policy_type'];
    }

    public function setPolicyType($value)
    {
        return $this->attributes['policy_type'] = $value;
    }
    // string # Name for this policy.
    public function getName()
    {
        return @$this->attributes['name'];
    }

    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    // string # Description for this policy.
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // object # Policy configuration data. Attributes differ by policy type. For more information, refer to the Value Hash section of the developer documentation.
    public function getValue()
    {
        return @$this->attributes['value'];
    }

    public function setValue($value)
    {
        return $this->attributes['value'] = $value;
    }
    // array(int64) # IDs of child sites that this policy has been applied to. This field is read-only.
    public function getAppliedChildSiteIds()
    {
        return @$this->attributes['applied_child_site_ids'];
    }

    public function setAppliedChildSiteIds($value)
    {
        return $this->attributes['applied_child_site_ids'] = $value;
    }
    // array(int64) # IDs of child sites that this policy has been exempted from. If `skip_child_site_ids` is empty, the policy will be applied to all child sites. To apply a policy to a child site that has been exempted, remove it from `skip_child_site_ids` or set it to an empty array (`[]`).
    public function getSkipChildSiteIds()
    {
        return @$this->attributes['skip_child_site_ids'];
    }

    public function setSkipChildSiteIds($value)
    {
        return $this->attributes['skip_child_site_ids'] = $value;
    }
    // date-time # When this policy was created.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # When this policy was last updated.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   value - object - Policy configuration data. Attributes differ by policy type. For more information, refer to the Value Hash section of the developer documentation.
    //   skip_child_site_ids - array(int64) - IDs of child sites that this policy has been exempted from. If `skip_child_site_ids` is empty, the policy will be applied to all child sites. To apply a policy to a child site that has been exempted, remove it from `skip_child_site_ids` or set it to an empty array (`[]`).
    //   policy_type - string - Type of policy.  Valid values: `settings`.
    //   name - string - Name for this policy.
    //   description - string - Description for this policy.
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

        if (@$params['skip_child_site_ids'] && !is_array(@$params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        if (@$params['policy_type'] && !is_string(@$params['policy_type'])) {
            throw new \Files\Exception\InvalidParameterException('$policy_type must be of type string; received ' . gettype(@$params['policy_type']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
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
    //   value - object - Policy configuration data. Attributes differ by policy type. For more information, refer to the Value Hash section of the developer documentation.
    //   skip_child_site_ids - array(int64) - IDs of child sites that this policy has been exempted from. If `skip_child_site_ids` is empty, the policy will be applied to all child sites. To apply a policy to a child site that has been exempted, remove it from `skip_child_site_ids` or set it to an empty array (`[]`).
    //   policy_type (required) - string - Type of policy.  Valid values: `settings`.
    //   name - string - Name for this policy.
    //   description - string - Description for this policy.
    public static function create($params = [], $options = [])
    {
        if (!@$params['policy_type']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: policy_type');
        }

        if (@$params['skip_child_site_ids'] && !is_array(@$params['skip_child_site_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$skip_child_site_ids must be of type array; received ' . gettype(@$params['skip_child_site_ids']));
        }

        if (@$params['policy_type'] && !is_string(@$params['policy_type'])) {
            throw new \Files\Exception\InvalidParameterException('$policy_type must be of type string; received ' . gettype(@$params['policy_type']));
        }

        if (@$params['name'] && !is_string(@$params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        $response = Api::sendRequest('/child_site_management_policies', 'POST', $params, $options);

        return new ChildSiteManagementPolicy((array) (@$response->data ?: []), $options);
    }
}
