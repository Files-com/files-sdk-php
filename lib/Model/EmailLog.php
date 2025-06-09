<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class EmailLog
 *
 * @package Files
 */
class EmailLog
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
    // string # Log Message
    public function getMessage()
    {
        return @$this->attributes['message'];
    }
    // string # Status of E-Mail delivery
    public function getStatus()
    {
        return @$this->attributes['status'];
    }
    // string # Subject line of E-Mail
    public function getSubject()
    {
        return @$this->attributes['subject'];
    }
    // string # To field of E-Mail
    public function getTo()
    {
        return @$this->attributes['to'];
    }
    // string # CC field of E-Mail
    public function getCc()
    {
        return @$this->attributes['cc'];
    }
    // string # How was the email delivered?  `customer_smtp` or `files.com`
    public function getDeliveryMethod()
    {
        return @$this->attributes['delivery_method'];
    }
    // string # Customer SMTP Hostname used.
    public function getSmtpHostname()
    {
        return @$this->attributes['smtp_hostname'];
    }
    // string # Customer SMTP IP address as resolved for use (useful for troubleshooting DNS issues with customer SMTP).
    public function getSmtpIp()
    {
        return @$this->attributes['smtp_ip'];
    }
    // date-time # Start Time of Action
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `status` or `created_at`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `status`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ status ]`, `[ created_at ]`, `[ start_date, end_date ]`, `[ start_date, status ]`, `[ start_date, created_at ]`, `[ end_date, status ]`, `[ end_date, created_at ]`, `[ status, created_at ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, created_at ]`, `[ start_date, status, created_at ]` or `[ end_date, status, created_at ]`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
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
