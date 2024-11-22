<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class BundleDownload
 *
 * @package Files
 */
class BundleDownload
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
    // BundleRegistration
    public function getBundleRegistration()
    {
        return @$this->attributes['bundle_registration'];
    }
    // string # Download method (file or full_zip)
    public function getDownloadMethod()
    {
        return @$this->attributes['download_method'];
    }
    // string # Download path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // date-time # Download date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    //   bundle_id - int64 - Bundle ID
    //   bundle_registration_id - int64 - BundleRegistration ID
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['bundle_id'] && !is_int(@$params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        if (@$params['bundle_registration_id'] && !is_int(@$params['bundle_registration_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_id must be of type int; received ' . gettype(@$params['bundle_registration_id']));
        }

        $response = Api::sendRequest('/bundle_downloads', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new BundleDownload((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    //   bundle_id - int64 - Bundle ID
    //   bundle_registration_id - int64 - BundleRegistration ID
    public static function createExport($params = [], $options = [])
    {
        if (@$params['bundle_id'] && !is_int(@$params['bundle_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_id must be of type int; received ' . gettype(@$params['bundle_id']));
        }

        if (@$params['bundle_registration_id'] && !is_int(@$params['bundle_registration_id'])) {
            throw new \Files\Exception\InvalidParameterException('$bundle_registration_id must be of type int; received ' . gettype(@$params['bundle_registration_id']));
        }

        $response = Api::sendRequest('/bundle_downloads/create_export', 'POST', $params, $options);

        return new Export((array) (@$response->data ?: []), $options);
    }
}
