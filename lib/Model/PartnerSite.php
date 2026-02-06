<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PartnerSite
 *
 * @package Files
 */
class PartnerSite
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
    // int64 # Partner ID
    public function getPartnerId()
    {
        return @$this->attributes['partner_id'];
    }
    // string # Partner Name
    public function getPartnerName()
    {
        return @$this->attributes['partner_name'];
    }
    // int64 # Linked Site ID
    public function getLinkedSiteId()
    {
        return @$this->attributes['linked_site_id'];
    }
    // string # Linked Site Name
    public function getLinkedSiteName()
    {
        return @$this->attributes['linked_site_name'];
    }
    // int64 # Main Site ID
    public function getMainSiteId()
    {
        return @$this->attributes['main_site_id'];
    }
    // string # Main Site Name
    public function getMainSiteName()
    {
        return @$this->attributes['main_site_name'];
    }

    public static function linkeds($params = [], $options = [])
    {
        $response = Api::sendRequest('/partner_sites/linked_partner_sites', 'GET', $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PartnerSite((array) $obj, $options);
        }

        return $return_array;
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

        $response = Api::sendRequest('/partner_sites', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PartnerSite((array) $obj, $options);
        }

        return $return_array;
    }
}
