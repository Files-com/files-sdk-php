<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class PaymentLineItem
 *
 * @package Files
 */
class PaymentLineItem
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
    // decimal # Payment line item amount
    public function getAmount()
    {
        return isset($this->attributes['amount']) ? $this->attributes['amount'] : null;
    }
    // date-time # Payment line item created at date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // int64 # Invoice ID
    public function getInvoiceId()
    {
        return isset($this->attributes['invoice_id']) ? $this->attributes['invoice_id'] : null;
    }
    // int64 # Payment ID
    public function getPaymentId()
    {
        return isset($this->attributes['payment_id']) ? $this->attributes['payment_id'] : null;
    }
}
