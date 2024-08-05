<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Invoice
 *
 * @package Files
 */
class Invoice
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
    // int64 # Line item Id
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // double # Line item amount
    public function getAmount()
    {
        return @$this->attributes['amount'];
    }
    // double # Line item balance
    public function getBalance()
    {
        return @$this->attributes['balance'];
    }
    // date-time # Line item created at
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # Line item currency
    public function getCurrency()
    {
        return @$this->attributes['currency'];
    }
    // string # Line item download uri
    public function getDownloadUri()
    {
        return @$this->attributes['download_uri'];
    }
    // array(object) # Associated invoice line items
    public function getInvoiceLineItems()
    {
        return @$this->attributes['invoice_line_items'];
    }
    // string # Line item payment method
    public function getMethod()
    {
        return @$this->attributes['method'];
    }
    // array(object) # Associated payment line items
    public function getPaymentLineItems()
    {
        return @$this->attributes['payment_line_items'];
    }
    // date-time # Date/time payment was reversed if applicable
    public function getPaymentReversedAt()
    {
        return @$this->attributes['payment_reversed_at'];
    }
    // string # Type of payment if applicable
    public function getPaymentType()
    {
        return @$this->attributes['payment_type'];
    }
    // string # Site name this line item is for
    public function getSiteName()
    {
        return @$this->attributes['site_name'];
    }
    // string # Type of line item, either payment or invoice
    public function getType()
    {
        return @$this->attributes['type'];
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

        $response = Api::sendRequest('/invoices', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new AccountLineItem((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Invoice ID.
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

        $response = Api::sendRequest('/invoices/' . @$params['id'] . '', 'GET', $params, $options);

        return new AccountLineItem((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
