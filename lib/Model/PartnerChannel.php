<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PartnerChannel
 *
 * @package Files
 */
class PartnerChannel
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
    // int64 # The unique ID of the Partner Channel.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # ID of the Workspace associated with this Partner Channel.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // int64 # ID of the Partner this Channel belongs to.
    public function getPartnerId()
    {
        return @$this->attributes['partner_id'];
    }

    public function setPartnerId($value)
    {
        return $this->attributes['partner_id'] = $value;
    }
    // string # Channel path relative to the Partner root folder. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // string # Optional Channel-level to-Partner folder name override.
    public function getToPartnerFolderName()
    {
        return @$this->attributes['to_partner_folder_name'];
    }

    public function setToPartnerFolderName($value)
    {
        return $this->attributes['to_partner_folder_name'] = $value;
    }
    // string # Optional Channel-level from-Partner folder name override.
    public function getFromPartnerFolderName()
    {
        return @$this->attributes['from_partner_folder_name'];
    }

    public function setFromPartnerFolderName($value)
    {
        return $this->attributes['from_partner_folder_name'] = $value;
    }
    // string # Optional route path for files uploaded by the Partner.
    public function getFromPartnerRoutePath()
    {
        return @$this->attributes['from_partner_route_path'];
    }

    public function setFromPartnerRoutePath($value)
    {
        return $this->attributes['from_partner_route_path'] = $value;
    }
    // string # Optional route path for files delivered to the Partner.
    public function getToPartnerRoutePath()
    {
        return @$this->attributes['to_partner_route_path'];
    }

    public function setToPartnerRoutePath($value)
    {
        return $this->attributes['to_partner_route_path'] = $value;
    }
    // string # Resolved to-Partner folder name after Channel override and default.
    public function getEffectiveToPartnerFolderName()
    {
        return @$this->attributes['effective_to_partner_folder_name'];
    }

    public function setEffectiveToPartnerFolderName($value)
    {
        return $this->attributes['effective_to_partner_folder_name'] = $value;
    }
    // string # Resolved from-Partner folder name after Channel override and default.
    public function getEffectiveFromPartnerFolderName()
    {
        return @$this->attributes['effective_from_partner_folder_name'];
    }

    public function setEffectiveFromPartnerFolderName($value)
    {
        return $this->attributes['effective_from_partner_folder_name'] = $value;
    }
    // string # Resolved Channel folder path.
    public function getChannelPath()
    {
        return @$this->attributes['channel_path'];
    }

    public function setChannelPath($value)
    {
        return $this->attributes['channel_path'] = $value;
    }
    // string # Resolved to-Partner folder path.
    public function getToPartnerFolderPath()
    {
        return @$this->attributes['to_partner_folder_path'];
    }

    public function setToPartnerFolderPath($value)
    {
        return $this->attributes['to_partner_folder_path'] = $value;
    }
    // string # Resolved from-Partner folder path.
    public function getFromPartnerFolderPath()
    {
        return @$this->attributes['from_partner_folder_path'];
    }

    public function setFromPartnerFolderPath($value)
    {
        return $this->attributes['from_partner_folder_path'] = $value;
    }

    // Parameters:
    //   from_partner_folder_name - string - Optional Channel-level from-Partner folder name override.
    //   from_partner_route_path - string - Optional route path for files uploaded by the Partner.
    //   to_partner_folder_name - string - Optional Channel-level to-Partner folder name override.
    //   to_partner_route_path - string - Optional route path for files delivered to the Partner.
    //   path - string - Channel path relative to the Partner root folder.
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

        if (@$params['from_partner_folder_name'] && !is_string(@$params['from_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_folder_name must be of type string; received ' . gettype(@$params['from_partner_folder_name']));
        }

        if (@$params['from_partner_route_path'] && !is_string(@$params['from_partner_route_path'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_route_path must be of type string; received ' . gettype(@$params['from_partner_route_path']));
        }

        if (@$params['to_partner_folder_name'] && !is_string(@$params['to_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_folder_name must be of type string; received ' . gettype(@$params['to_partner_folder_name']));
        }

        if (@$params['to_partner_route_path'] && !is_string(@$params['to_partner_route_path'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_route_path must be of type string; received ' . gettype(@$params['to_partner_route_path']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/partner_channels/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new PartnerChannel((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/partner_channels/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `path` or `partner_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `partner_id` and `workspace_id`. Valid field combinations are `[ workspace_id, partner_id ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/partner_channels', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PartnerChannel((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Partner Channel ID.
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

        $response = Api::sendRequest('/partner_channels/' . @$params['id'] . '', 'GET', $params, $options);

        return new PartnerChannel((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   from_partner_folder_name - string - Optional Channel-level from-Partner folder name override.
    //   from_partner_route_path - string - Optional route path for files uploaded by the Partner.
    //   to_partner_folder_name - string - Optional Channel-level to-Partner folder name override.
    //   to_partner_route_path - string - Optional route path for files delivered to the Partner.
    //   partner_id (required) - int64 - ID of the Partner this Channel belongs to.
    //   path (required) - string - Channel path relative to the Partner root folder.
    //   workspace_id - int64 - ID of the Workspace associated with this Partner Channel.
    public static function create($params = [], $options = [])
    {
        if (!@$params['partner_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: partner_id');
        }

        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['from_partner_folder_name'] && !is_string(@$params['from_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_folder_name must be of type string; received ' . gettype(@$params['from_partner_folder_name']));
        }

        if (@$params['from_partner_route_path'] && !is_string(@$params['from_partner_route_path'])) {
            throw new \Files\Exception\InvalidParameterException('$from_partner_route_path must be of type string; received ' . gettype(@$params['from_partner_route_path']));
        }

        if (@$params['to_partner_folder_name'] && !is_string(@$params['to_partner_folder_name'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_folder_name must be of type string; received ' . gettype(@$params['to_partner_folder_name']));
        }

        if (@$params['to_partner_route_path'] && !is_string(@$params['to_partner_route_path'])) {
            throw new \Files\Exception\InvalidParameterException('$to_partner_route_path must be of type string; received ' . gettype(@$params['to_partner_route_path']));
        }

        if (@$params['partner_id'] && !is_int(@$params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['workspace_id'] && !is_int(@$params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/partner_channels', 'POST', $params, $options);

        return new PartnerChannel((array) (@$response->data ?: []), $options);
    }
}
