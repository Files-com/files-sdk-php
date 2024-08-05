<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class RemoteBandwidthSnapshot
 *
 * @package Files
 */
class RemoteBandwidthSnapshot
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
                return @self::$method($arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // int64 # Site bandwidth ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // double # Site sync bandwidth report bytes received
    public function getSyncBytesReceived()
    {
        return @$this->attributes['sync_bytes_received'];
    }
    // double # Site sync bandwidth report bytes sent
    public function getSyncBytesSent()
    {
        return @$this->attributes['sync_bytes_sent'];
    }
    // date-time # Time the site bandwidth report was logged
    public function getLoggedAt()
    {
        return @$this->attributes['logged_at'];
    }
    // int64 # ID of related Remote Server
    public function getRemoteServerId()
    {
        return @$this->attributes['remote_server_id'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `logged_at`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `logged_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `logged_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `logged_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `logged_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `logged_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/remote_bandwidth_snapshots', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new RemoteBandwidthSnapshot((array) $obj, $options);
        }

        return $return_array;
    }
}
