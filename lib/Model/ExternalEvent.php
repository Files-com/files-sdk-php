<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ExternalEvent
 *
 * @package Files
 */
class ExternalEvent
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
    // int64 # Event ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Type of event being recorded.
    public function getEventType()
    {
        return @$this->attributes['event_type'];
    }

    public function setEventType($value)
    {
        return $this->attributes['event_type'] = $value;
    }
    // string # Status of event.
    public function getStatus()
    {
        return @$this->attributes['status'];
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // string # Event body
    public function getBody()
    {
        return @$this->attributes['body'];
    }

    public function setBody($value)
    {
        return $this->attributes['body'] = $value;
    }
    // date-time # External event create date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # Link to log file.
    public function getBodyUrl()
    {
        return @$this->attributes['body_url'];
    }

    public function setBodyUrl($value)
    {
        return $this->attributes['body_url'] = $value;
    }
    // int64 # Folder Behavior ID
    public function getFolderBehaviorId()
    {
        return @$this->attributes['folder_behavior_id'];
    }

    public function setFolderBehaviorId($value)
    {
        return $this->attributes['folder_behavior_id'] = $value;
    }
    // int64 # SIEM HTTP Destination ID.
    public function getSiemHttpDestinationId()
    {
        return @$this->attributes['siem_http_destination_id'];
    }

    public function setSiemHttpDestinationId($value)
    {
        return $this->attributes['siem_http_destination_id'] = $value;
    }
    // int64 # For sync events, the number of files handled successfully.
    public function getSuccessfulFiles()
    {
        return @$this->attributes['successful_files'];
    }

    public function setSuccessfulFiles($value)
    {
        return $this->attributes['successful_files'] = $value;
    }
    // int64 # For sync events, the number of files that encountered errors.
    public function getErroredFiles()
    {
        return @$this->attributes['errored_files'];
    }

    public function setErroredFiles($value)
    {
        return $this->attributes['errored_files'] = $value;
    }
    // int64 # For sync events, the total number of bytes synced.
    public function getBytesSynced()
    {
        return @$this->attributes['bytes_synced'];
    }

    public function setBytesSynced($value)
    {
        return $this->attributes['bytes_synced'] = $value;
    }
    // int64 # For sync events, the number of files considered for the sync.
    public function getComparedFiles()
    {
        return @$this->attributes['compared_files'];
    }

    public function setComparedFiles($value)
    {
        return $this->attributes['compared_files'] = $value;
    }
    // int64 # For sync events, the number of folders listed and considered for the sync.
    public function getComparedFolders()
    {
        return @$this->attributes['compared_folders'];
    }

    public function setComparedFolders($value)
    {
        return $this->attributes['compared_folders'] = $value;
    }
    // string # Associated Remote Server type, if any
    public function getRemoteServerType()
    {
        return @$this->attributes['remote_server_type'];
    }

    public function setRemoteServerType($value)
    {
        return $this->attributes['remote_server_type'] = $value;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The ExternalEvent object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `remote_server_type`, `id`, `siem_http_destination_id`, `created_at`, `event_type` or `status`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status`, `folder_behavior_id` or `siem_http_destination_id`. Valid field combinations are `[ event_type, created_at ]`, `[ remote_server_type, created_at ]`, `[ status, created_at ]`, `[ event_type, status ]` or `[ event_type, status, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `remote_server_type`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/external_events', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ExternalEvent((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - External Event ID.
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

        $response = Api::sendRequest('/external_events/' . @$params['id'] . '', 'GET', $params, $options);

        return new ExternalEvent((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   status (required) - string - Status of event.
    //   body (required) - string - Event body
    public static function create($params = [], $options = [])
    {
        if (!@$params['status']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: status');
        }

        if (!@$params['body']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: body');
        }

        if (@$params['status'] && !is_string(@$params['status'])) {
            throw new \Files\Exception\InvalidParameterException('$status must be of type string; received ' . gettype(@$params['status']));
        }

        if (@$params['body'] && !is_string(@$params['body'])) {
            throw new \Files\Exception\InvalidParameterException('$body must be of type string; received ' . gettype(@$params['body']));
        }

        $response = Api::sendRequest('/external_events', 'POST', $params, $options);

        return new ExternalEvent((array) (@$response->data ?: []), $options);
    }
}
