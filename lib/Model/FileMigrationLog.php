<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileMigrationLog
 *
 * @package Files
 */
class FileMigrationLog
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
    // date-time # Start Time of Action. Deprecrated: Use created_at.
    public function getTimestamp()
    {
        return @$this->attributes['timestamp'];
    }
    // int64 # File Migration ID
    public function getFileMigrationId()
    {
        return @$this->attributes['file_migration_id'];
    }
    // string # Destination path, for moves and copies
    public function getDestPath()
    {
        return @$this->attributes['dest_path'];
    }
    // string # Error type, if applicable
    public function getErrorType()
    {
        return @$this->attributes['error_type'];
    }
    // string # Message
    public function getMessage()
    {
        return @$this->attributes['message'];
    }
    // string # Operation type
    public function getOperation()
    {
        return @$this->attributes['operation'];
    }
    // string # File path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }
    // string # Status
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // date-time # Start Time of Action
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `file_migration_id`, `operation`, `status`, `type` or `created_at`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `operation` and `status`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ file_migration_id ]`, `[ operation ]`, `[ status ]`, `[ type ]`, `[ created_at ]`, `[ file_migration_id, operation ]`, `[ file_migration_id, status ]`, `[ file_migration_id, type ]`, `[ file_migration_id, created_at ]`, `[ operation, status ]`, `[ operation, type ]`, `[ operation, created_at ]`, `[ status, type ]`, `[ status, created_at ]`, `[ type, created_at ]`, `[ file_migration_id, operation, status ]`, `[ file_migration_id, operation, type ]`, `[ file_migration_id, operation, created_at ]`, `[ file_migration_id, status, type ]`, `[ file_migration_id, status, created_at ]`, `[ file_migration_id, type, created_at ]`, `[ operation, status, type ]`, `[ operation, status, created_at ]`, `[ operation, type, created_at ]`, `[ status, type, created_at ]`, `[ file_migration_id, operation, status, type ]`, `[ file_migration_id, operation, status, created_at ]`, `[ file_migration_id, operation, type, created_at ]`, `[ file_migration_id, status, type, created_at ]`, `[ operation, status, type, created_at ]` or `[ file_migration_id, operation, status, type, created_at ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/file_migration_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new FileMigrationLog((array) $obj, $options);
        }

        return $return_array;
    }
}
