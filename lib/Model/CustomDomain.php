<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class CustomDomain
 *
 * @package Files
 */
class CustomDomain
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
    // int64 # Custom Domain ID.
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Customer-owned domain name.
    public function getDomain()
    {
        return @$this->attributes['domain'];
    }

    public function setDomain($value)
    {
        return $this->attributes['domain'] = $value;
    }
    // string # Where this custom domain routes. Can be `site_alias`, `public_hosting`, or `s3_endpoint`.
    public function getDestination()
    {
        return @$this->attributes['destination'];
    }

    public function setDestination($value)
    {
        return $this->attributes['destination'] = $value;
    }
    // string # Current DNS verification status.
    public function getDnsStatus()
    {
        return @$this->attributes['dns_status'];
    }

    public function setDnsStatus($value)
    {
        return $this->attributes['dns_status'] = $value;
    }
    // int64 # Current SSL certificate ID.
    public function getSslCertificateId()
    {
        return @$this->attributes['ssl_certificate_id'];
    }

    public function setSslCertificateId($value)
    {
        return $this->attributes['ssl_certificate_id'] = $value;
    }
    // boolean # Is this domain's SSL certificate automatically managed and renewed by Files.com?
    public function getBrickManaged()
    {
        return @$this->attributes['brick_managed'];
    }

    public function setBrickManaged($value)
    {
        return $this->attributes['brick_managed'] = $value;
    }
    // int64 # Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.
    public function getFolderBehaviorId()
    {
        return @$this->attributes['folder_behavior_id'];
    }

    public function setFolderBehaviorId($value)
    {
        return $this->attributes['folder_behavior_id'] = $value;
    }
    // date-time # When this Custom Domain was created.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # When this Custom Domain was last updated.
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }

    // Parameters:
    //   destination - string - Where this custom domain routes. Can be `site_alias`, `public_hosting`, or `s3_endpoint`.
    //   folder_behavior_id - int64 - Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.
    //   ssl_certificate_id - int64 - Current SSL certificate ID.
    //   domain - string - Customer-owned domain name.
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

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (@$params['folder_behavior_id'] && !is_int(@$params['folder_behavior_id'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype(@$params['folder_behavior_id']));
        }

        if (@$params['ssl_certificate_id'] && !is_int(@$params['ssl_certificate_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate_id must be of type int; received ' . gettype(@$params['ssl_certificate_id']));
        }

        if (@$params['domain'] && !is_string(@$params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new CustomDomain((array) (@$response->data ?: []), $this->options);
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

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
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
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `id`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/custom_domains', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new CustomDomain((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Custom Domain ID.
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

        $response = Api::sendRequest('/custom_domains/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new CustomDomain((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   destination - string - Where this custom domain routes. Can be `site_alias`, `public_hosting`, or `s3_endpoint`.
    //   folder_behavior_id - int64 - Public Hosting behavior ID when this domain routes to a specific Public Hosting behavior.
    //   ssl_certificate_id - int64 - Current SSL certificate ID.
    //   domain (required) - string - Customer-owned domain name.
    public static function create($params = [], $options = [])
    {
        if (!@$params['domain']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: domain');
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (@$params['folder_behavior_id'] && !is_int(@$params['folder_behavior_id'])) {
            throw new \Files\Exception\InvalidParameterException('$folder_behavior_id must be of type int; received ' . gettype(@$params['folder_behavior_id']));
        }

        if (@$params['ssl_certificate_id'] && !is_int(@$params['ssl_certificate_id'])) {
            throw new \Files\Exception\InvalidParameterException('$ssl_certificate_id must be of type int; received ' . gettype(@$params['ssl_certificate_id']));
        }

        if (@$params['domain'] && !is_string(@$params['domain'])) {
            throw new \Files\Exception\InvalidParameterException('$domain must be of type string; received ' . gettype(@$params['domain']));
        }

        $response = Api::sendRequest('/custom_domains', 'POST', $params, $options);

        return new CustomDomain((array) (@$response->data ?: []), $options);
    }
}
