<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ActionNotificationExport
 *
 * @package Files
 */
class ActionNotificationExport
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // int64 # History Export ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // int64 # Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    // string # Version of the underlying records for the export.
    public function getExportVersion()
    {
        return isset($this->attributes['export_version']) ? $this->attributes['export_version'] : null;
    }

    public function setExportVersion($value)
    {
        return $this->attributes['export_version'] = $value;
    }
    // date-time # Start date/time of export range.
    public function getStartAt()
    {
        return isset($this->attributes['start_at']) ? $this->attributes['start_at'] : null;
    }

    public function setStartAt($value)
    {
        return $this->attributes['start_at'] = $value;
    }
    // date-time # End date/time of export range.
    public function getEndAt()
    {
        return isset($this->attributes['end_at']) ? $this->attributes['end_at'] : null;
    }

    public function setEndAt($value)
    {
        return $this->attributes['end_at'] = $value;
    }
    // string # Status of export.  Valid values: `building`, `ready`, or `failed`
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    // string # Return notifications that were triggered by actions on this specific path.
    public function getQueryPath()
    {
        return isset($this->attributes['query_path']) ? $this->attributes['query_path'] : null;
    }

    public function setQueryPath($value)
    {
        return $this->attributes['query_path'] = $value;
    }
    // string # Return notifications that were triggered by actions in this folder.
    public function getQueryFolder()
    {
        return isset($this->attributes['query_folder']) ? $this->attributes['query_folder'] : null;
    }

    public function setQueryFolder($value)
    {
        return $this->attributes['query_folder'] = $value;
    }
    // string # Error message associated with the request, if any.
    public function getQueryMessage()
    {
        return isset($this->attributes['query_message']) ? $this->attributes['query_message'] : null;
    }

    public function setQueryMessage($value)
    {
        return $this->attributes['query_message'] = $value;
    }
    // string # The HTTP request method used by the webhook.
    public function getQueryRequestMethod()
    {
        return isset($this->attributes['query_request_method']) ? $this->attributes['query_request_method'] : null;
    }

    public function setQueryRequestMethod($value)
    {
        return $this->attributes['query_request_method'] = $value;
    }
    // string # The target webhook URL.
    public function getQueryRequestUrl()
    {
        return isset($this->attributes['query_request_url']) ? $this->attributes['query_request_url'] : null;
    }

    public function setQueryRequestUrl($value)
    {
        return $this->attributes['query_request_url'] = $value;
    }
    // string # The HTTP status returned from the server in response to the webhook request.
    public function getQueryStatus()
    {
        return isset($this->attributes['query_status']) ? $this->attributes['query_status'] : null;
    }

    public function setQueryStatus($value)
    {
        return $this->attributes['query_status'] = $value;
    }
    // boolean # true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
    public function getQuerySuccess()
    {
        return isset($this->attributes['query_success']) ? $this->attributes['query_success'] : null;
    }

    public function setQuerySuccess($value)
    {
        return $this->attributes['query_success'] = $value;
    }
    // string # If `status` is `ready`, this will be a URL where all the results can be downloaded at once as a CSV.
    public function getResultsUrl()
    {
        return isset($this->attributes['results_url']) ? $this->attributes['results_url'] : null;
    }

    public function setResultsUrl($value)
    {
        return $this->attributes['results_url'] = $value;
    }
    // int64 # User ID.  Provide a value of `0` to operate the current session's user.
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The ActionNotificationExport object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   id (required) - int64 - Action Notification Export ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/action_notification_exports/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new ActionNotificationExport((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   workspace_id - int64 - Workspace whose logs are exported. Set to `0` for the default workspace. A null value means a site-wide export.
    //   start_at - string - Start date/time of export range.
    //   end_at - string - End date/time of export range.
    //   query_message - string - Error message associated with the request, if any.
    //   query_request_method - string - The HTTP request method used by the webhook.
    //   query_request_url - string - The target webhook URL.
    //   query_status - string - The HTTP status returned from the server in response to the webhook request.
    //   query_success - boolean - true if the webhook request succeeded (i.e. returned a 200 or 204 response status). false otherwise.
    //   query_path - string - Return notifications that were triggered by actions on this specific path.
    //   query_folder - string - Return notifications that were triggered by actions in this folder.
    public static function create($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (!empty($params['start_at']) && !is_string($params['start_at'])) {
            throw new \Files\Exception\InvalidParameterException('$start_at must be of type string; received ' . gettype(@$params['start_at']));
        }

        if (!empty($params['end_at']) && !is_string($params['end_at'])) {
            throw new \Files\Exception\InvalidParameterException('$end_at must be of type string; received ' . gettype(@$params['end_at']));
        }

        if (!empty($params['query_message']) && !is_string($params['query_message'])) {
            throw new \Files\Exception\InvalidParameterException('$query_message must be of type string; received ' . gettype(@$params['query_message']));
        }

        if (!empty($params['query_request_method']) && !is_string($params['query_request_method'])) {
            throw new \Files\Exception\InvalidParameterException('$query_request_method must be of type string; received ' . gettype(@$params['query_request_method']));
        }

        if (!empty($params['query_request_url']) && !is_string($params['query_request_url'])) {
            throw new \Files\Exception\InvalidParameterException('$query_request_url must be of type string; received ' . gettype(@$params['query_request_url']));
        }

        if (!empty($params['query_status']) && !is_string($params['query_status'])) {
            throw new \Files\Exception\InvalidParameterException('$query_status must be of type string; received ' . gettype(@$params['query_status']));
        }

        if (!empty($params['query_path']) && !is_string($params['query_path'])) {
            throw new \Files\Exception\InvalidParameterException('$query_path must be of type string; received ' . gettype(@$params['query_path']));
        }

        if (!empty($params['query_folder']) && !is_string($params['query_folder'])) {
            throw new \Files\Exception\InvalidParameterException('$query_folder must be of type string; received ' . gettype(@$params['query_folder']));
        }

        $response = Api::sendRequest('/action_notification_exports', 'POST', $params, $options);

        return new ActionNotificationExport((array) (@$response->data ?: []), $options);
    }
}
