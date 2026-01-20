<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PartnerSiteRequest
 *
 * @package Files
 */
class PartnerSiteRequest
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
    // int64 # Partner Site Request ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Partner ID
    public function getPartnerId()
    {
        return @$this->attributes['partner_id'];
    }

    public function setPartnerId($value)
    {
        return $this->attributes['partner_id'] = $value;
    }
    // int64 # Linked Site ID
    public function getLinkedSiteId()
    {
        return @$this->attributes['linked_site_id'];
    }

    public function setLinkedSiteId($value)
    {
        return $this->attributes['linked_site_id'] = $value;
    }
    // string # Request status (pending, approved, rejected)
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // string # Pairing key used to approve this request on the target site
    public function getPairingKey()
    {
        return @$this->attributes['pairing_key'];
    }

    public function setPairingKey($value)
    {
        return $this->attributes['pairing_key'] = $value;
    }
    // date-time # Request creation date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // date-time # Request last updated date/time
    public function getUpdatedAt()
    {
        return @$this->attributes['updated_at'];
    }
    // string # Site URL to link to
    public function getSiteUrl()
    {
        return @$this->attributes['site_url'];
    }

    public function setSiteUrl($value)
    {
        return $this->attributes['site_url'] = $value;
    }

    // Reject partner site request
    public function reject($params = [])
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

        $response = Api::sendRequest('/partner_site_requests/' . @$params['id'] . '/reject', 'POST', $params, $this->options);
        return;
    }

    // Approve partner site request
    public function approve($params = [])
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

        $response = Api::sendRequest('/partner_site_requests/' . @$params['id'] . '/approve', 'POST', $params, $this->options);
        return;
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

        $response = Api::sendRequest('/partner_site_requests/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
            throw new \Files\Exception\NotImplementedException('The PartnerSiteRequest object doesn\'t support updates.');
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

        $response = Api::sendRequest('/partner_site_requests', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new PartnerSiteRequest((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   pairing_key (required) - string - Pairing key for the partner site request
    public static function findByPairingKey($params = [], $options = [])
    {
        if (!@$params['pairing_key']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: pairing_key');
        }

        if (@$params['pairing_key'] && !is_string(@$params['pairing_key'])) {
            throw new \Files\Exception\InvalidParameterException('$pairing_key must be of type string; received ' . gettype(@$params['pairing_key']));
        }

        $response = Api::sendRequest('/partner_site_requests/find_by_pairing_key', 'GET', $params, $options);

        return;
    }

    // Parameters:
    //   partner_id (required) - int64 - Partner ID to link with
    //   site_url (required) - string - Site URL to link to
    public static function create($params = [], $options = [])
    {
        if (!@$params['partner_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: partner_id');
        }

        if (!@$params['site_url']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: site_url');
        }

        if (@$params['partner_id'] && !is_int(@$params['partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partner_id must be of type int; received ' . gettype(@$params['partner_id']));
        }

        if (@$params['site_url'] && !is_string(@$params['site_url'])) {
            throw new \Files\Exception\InvalidParameterException('$site_url must be of type string; received ' . gettype(@$params['site_url']));
        }

        $response = Api::sendRequest('/partner_site_requests', 'POST', $params, $options);

        return new PartnerSiteRequest((array) (@$response->data ?: []), $options);
    }
}
