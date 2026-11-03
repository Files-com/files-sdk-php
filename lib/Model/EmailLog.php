<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An EmailLog is a record of the email notifications that were sent.
 *
 * @property string|null $timestamp Start Time of Action. Deprecrated: Use created_at.
 * @property string|null $message Log Message
 * @property string|null $status Status of E-Mail delivery
 * @property string|null $subject Subject line of E-Mail
 * @property string|null $to To field of E-Mail
 * @property string|null $cc CC field of E-Mail
 * @property string|null $delivery_method How was the email delivered? `customer_smtp` or `files.com`
 * @property string|null $smtp_hostname Customer SMTP Hostname used.
 * @property string|null $smtp_ip Customer SMTP IP address as resolved for use (useful for troubleshooting DNS issues with customer SMTP).
 * @property string|null $created_at Start Time of Action
 * @package Files
 */
class EmailLog
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
     * Start Time of Action. Deprecrated: Use created_at.
     *
     * @return string|null
     */
    public function getTimestamp()
    {
        return isset($this->attributes['timestamp']) ? $this->attributes['timestamp'] : null;
    }
    /**
     * Log Message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }
    /**
     * Status of E-Mail delivery
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Subject line of E-Mail
     *
     * @return string|null
     */
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }
    /**
     * To field of E-Mail
     *
     * @return string|null
     */
    public function getTo()
    {
        return isset($this->attributes['to']) ? $this->attributes['to'] : null;
    }
    /**
     * CC field of E-Mail
     *
     * @return string|null
     */
    public function getCc()
    {
        return isset($this->attributes['cc']) ? $this->attributes['cc'] : null;
    }
    /**
     * How was the email delivered?  `customer_smtp` or `files.com`
     *
     * @return string|null
     */
    public function getDeliveryMethod()
    {
        return isset($this->attributes['delivery_method']) ? $this->attributes['delivery_method'] : null;
    }
    /**
     * Customer SMTP Hostname used.
     *
     * @return string|null
     */
    public function getSmtpHostname()
    {
        return isset($this->attributes['smtp_hostname']) ? $this->attributes['smtp_hostname'] : null;
    }
    /**
     * Customer SMTP IP address as resolved for use (useful for troubleshooting DNS issues with customer SMTP).
     *
     * @return string|null
     */
    public function getSmtpIp()
    {
        return isset($this->attributes['smtp_ip']) ? $this->attributes['smtp_ip'] : null;
    }
    /**
     * Start Time of Action
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }

    /**
     * List Email Logs
     *
     * GET /email_logs
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 1000, 1,000 or less is recommended).
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `status` and `created_at`. Valid field combinations are `[ status ]`, `[ created_at ]` or `[ status, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ status ]`, `[ created_at ]` or `[ status, created_at ]`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ status ]`, `[ created_at ]` or `[ status, created_at ]`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ status ]`, `[ created_at ]` or `[ status, created_at ]`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ status ]`, `[ created_at ]` or `[ status, created_at ]`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\EmailLog[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/email_logs', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new EmailLog((array) $obj, $options);
        }

        return $return_array;
    }
}
