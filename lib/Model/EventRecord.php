<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class EventRecord
 *
 * @package Files
 */
class EventRecord
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
    // int64 # Event Record ID
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # Workspace ID. 0 means the default workspace or site-wide.
    public function getWorkspaceId()
    {
        return @$this->attributes['workspace_id'];
    }
    // string # Stable event UUID.
    public function getEventUuid()
    {
        return @$this->attributes['event_uuid'];
    }
    // string # Versioned event type string.
    public function getEventType()
    {
        return @$this->attributes['event_type'];
    }
    // string # Event severity.
    public function getSeverity()
    {
        return @$this->attributes['severity'];
    }
    // string # Source record type.
    public function getSourceType()
    {
        return @$this->attributes['source_type'];
    }
    // int64 # Source record ID.
    public function getSourceId()
    {
        return @$this->attributes['source_id'];
    }
    // date-time # Event occurrence date/time.
    public function getOccurredAt()
    {
        return @$this->attributes['occurred_at'];
    }
    // string # Human-readable event title.
    public function getHumanTitle()
    {
        return @$this->attributes['human_title'];
    }
    // string # Human-readable event summary.
    public function getHumanSummary()
    {
        return @$this->attributes['human_summary'];
    }
    // array(object) # Human-readable event detail fields.
    public function getHumanFields()
    {
        return @$this->attributes['human_fields'];
    }
    // object # Actor associated with the event.
    public function getActor()
    {
        return @$this->attributes['actor'];
    }
    // array(object) # Resources associated with the event.
    public function getResources()
    {
        return @$this->attributes['resources'];
    }
    // object # Event payload.
    public function getPayload()
    {
        return @$this->attributes['payload'];
    }
    // date-time # Event Record create date/time.
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `event_type`, `created_at` or `workspace_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `event_type` or `workspace_id`. Valid field combinations are `[ event_type, created_at ]`, `[ workspace_id, created_at ]`, `[ workspace_id, event_type ]` or `[ workspace_id, event_type, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `event_type`.
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

        $response = Api::sendRequest('/event_records', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EventRecord((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Event Record ID.
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

        $response = Api::sendRequest('/event_records/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new EventRecord((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
