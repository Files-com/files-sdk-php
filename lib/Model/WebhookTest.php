<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A WebhookTest is an object that can be sent to your Webhook endpoint for connection and response tests.
 *
 * @property int|null $code Status HTTP code
 * @property string|null $message Error message
 * @property string|null $status Status message
 * @property \stdClass|null $data Additional data
 * @property bool|null $success The success status of the webhook test
 * @property string|null $url URL for testing the webhook.
 * @property string|null $method HTTP method(GET or POST).
 * @property string|null $encoding HTTP encoding method. Can be JSON, XML, or RAW (form data).
 * @property object|null $headers Additional request headers.
 * @property object|null $body Additional body parameters.
 * @property string|null $raw_body raw body text
 * @property bool|null $file_as_body Send the file data as the request body?
 * @property string|null $file_form_field Send the file data as a named parameter in the request POST body
 * @property string|null $action action for test body
 * @property bool|null $use_dedicated_ips Use dedicated IPs for sending the webhook?
 * @package Files
 */
class WebhookTest
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * Status HTTP code
     *
     * @return int|null
     */
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }

    /**
     * Status HTTP code
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCode($value)
    {
        return $this->attributes['code'] = $value;
    }
    /**
     * Error message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }

    /**
     * Error message
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMessage($value)
    {
        return $this->attributes['message'] = $value;
    }
    /**
     * Status message
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }

    /**
     * Status message
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStatus($value)
    {
        return $this->attributes['status'] = $value;
    }
    /**
     * Additional data
     *
     * @return \stdClass|null
     */
    public function getData()
    {
        return isset($this->attributes['data']) ? $this->attributes['data'] : null;
    }

    /**
     * Additional data
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setData($value)
    {
        return $this->attributes['data'] = $value;
    }
    /**
     * The success status of the webhook test
     *
     * @return bool|null
     */
    public function getSuccess()
    {
        return isset($this->attributes['success']) ? $this->attributes['success'] : null;
    }

    /**
     * The success status of the webhook test
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSuccess($value)
    {
        return $this->attributes['success'] = $value;
    }
    /**
     * URL for testing the webhook.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return isset($this->attributes['url']) ? $this->attributes['url'] : null;
    }

    /**
     * URL for testing the webhook.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUrl($value)
    {
        return $this->attributes['url'] = $value;
    }
    /**
     * HTTP method(GET or POST).
     *
     * @return string|null
     */
    public function getMethod()
    {
        return isset($this->attributes['method']) ? $this->attributes['method'] : null;
    }

    /**
     * HTTP method(GET or POST).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMethod($value)
    {
        return $this->attributes['method'] = $value;
    }
    /**
     * HTTP encoding method.  Can be JSON, XML, or RAW (form data).
     *
     * @return string|null
     */
    public function getEncoding()
    {
        return isset($this->attributes['encoding']) ? $this->attributes['encoding'] : null;
    }

    /**
     * HTTP encoding method.  Can be JSON, XML, or RAW (form data).
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEncoding($value)
    {
        return $this->attributes['encoding'] = $value;
    }
    /**
     * Additional request headers.
     *
     * @return object|null
     */
    public function getHeaders()
    {
        return isset($this->attributes['headers']) ? $this->attributes['headers'] : null;
    }

    /**
     * Additional request headers.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setHeaders($value)
    {
        return $this->attributes['headers'] = $value;
    }
    /**
     * Additional body parameters.
     *
     * @return object|null
     */
    public function getBody()
    {
        return isset($this->attributes['body']) ? $this->attributes['body'] : null;
    }

    /**
     * Additional body parameters.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setBody($value)
    {
        return $this->attributes['body'] = $value;
    }
    /**
     * raw body text
     *
     * @return string|null
     */
    public function getRawBody()
    {
        return isset($this->attributes['raw_body']) ? $this->attributes['raw_body'] : null;
    }

    /**
     * raw body text
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRawBody($value)
    {
        return $this->attributes['raw_body'] = $value;
    }
    /**
     * Send the file data as the request body?
     *
     * @return bool|null
     */
    public function getFileAsBody()
    {
        return isset($this->attributes['file_as_body']) ? $this->attributes['file_as_body'] : null;
    }

    /**
     * Send the file data as the request body?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFileAsBody($value)
    {
        return $this->attributes['file_as_body'] = $value;
    }
    /**
     * Send the file data as a named parameter in the request POST body
     *
     * @return string|null
     */
    public function getFileFormField()
    {
        return isset($this->attributes['file_form_field']) ? $this->attributes['file_form_field'] : null;
    }

    /**
     * Send the file data as a named parameter in the request POST body
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setFileFormField($value)
    {
        return $this->attributes['file_form_field'] = $value;
    }
    /**
     * action for test body
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }

    /**
     * action for test body
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAction($value)
    {
        return $this->attributes['action'] = $value;
    }
    /**
     * Use dedicated IPs for sending the webhook?
     *
     * @return bool|null
     */
    public function getUseDedicatedIps()
    {
        return isset($this->attributes['use_dedicated_ips']) ? $this->attributes['use_dedicated_ips'] : null;
    }

    /**
     * Use dedicated IPs for sending the webhook?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUseDedicatedIps($value)
    {
        return $this->attributes['use_dedicated_ips'] = $value;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The WebhookTest object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * Create Webhook Test
     *
     * POST /webhook_tests
     *
     * @param array $params Request parameters.
     *     - `url` (string, required): URL for testing the webhook.
     *     - `method` (string, optional): HTTP method(GET or POST).
     *     - `encoding` (string, optional): HTTP encoding method.  Can be JSON, XML, or RAW (form data).
     *     - `headers` (array|object, optional): Additional request headers.
     *     - `body` (array|object, optional): Additional body parameters.
     *     - `raw_body` (string, optional): raw body text
     *     - `file_as_body` (bool, optional): Send the file data as the request body?
     *     - `file_form_field` (string, optional): Send the file data as a named parameter in the request POST body
     *     - `action` (string, optional): action for test body
     *     - `use_dedicated_ips` (bool, optional): Use dedicated IPs for sending the webhook?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\WebhookTest
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['url'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: url');
        }

        if (isset($params['url']) && !is_string($params['url'])) {
            throw new \Files\Exception\InvalidParameterException('$url must be of type string; received ' . gettype(@$params['url']));
        }

        if (isset($params['method']) && !is_string($params['method'])) {
            throw new \Files\Exception\InvalidParameterException('$method must be of type string; received ' . gettype(@$params['method']));
        }

        if (isset($params['encoding']) && !is_string($params['encoding'])) {
            throw new \Files\Exception\InvalidParameterException('$encoding must be of type string; received ' . gettype(@$params['encoding']));
        }

        if (isset($params['raw_body']) && !is_string($params['raw_body'])) {
            throw new \Files\Exception\InvalidParameterException('$raw_body must be of type string; received ' . gettype(@$params['raw_body']));
        }

        if (isset($params['file_as_body']) && !is_scalar($params['file_as_body'])) {
            throw new \Files\Exception\InvalidParameterException('$file_as_body must be of type boolean; received ' . gettype(@$params['file_as_body']));
        }

        if (isset($params['file_form_field']) && !is_string($params['file_form_field'])) {
            throw new \Files\Exception\InvalidParameterException('$file_form_field must be of type string; received ' . gettype(@$params['file_form_field']));
        }

        if (isset($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (isset($params['use_dedicated_ips']) && !is_scalar($params['use_dedicated_ips'])) {
            throw new \Files\Exception\InvalidParameterException('$use_dedicated_ips must be of type boolean; received ' . gettype(@$params['use_dedicated_ips']));
        }

        $response = Api::sendRequest('/webhook_tests', 'POST', $params, $options);

        return new WebhookTest(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
