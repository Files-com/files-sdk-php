<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AccountLineItem is a single line item in the accounting ledger for a billing account.
 *
 * @property int|null $id Line item Id
 * @property string|null $amount Line item amount
 * @property string|null $balance Line item balance
 * @property string|null $created_at Line item created at
 * @property string|null $currency Line item currency
 * @property string|null $download_uri Line item download uri
 * @property object[]|null $invoice_line_items Associated invoice line items
 * @property string|null $method Line item payment method
 * @property object[]|null $payment_line_items Associated payment line items
 * @property string|null $payment_reversed_at Date/time payment was reversed if applicable
 * @property string|null $payment_type Type of payment if applicable
 * @property string|null $site_name Site name this line item is for
 * @property string|null $type Type of line item, either payment or invoice
 * @package Files
 */
class AccountLineItem
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
     * Line item Id
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Line item amount
     *
     * @return string|null
     */
    public function getAmount()
    {
        return isset($this->attributes['amount']) ? $this->attributes['amount'] : null;
    }
    /**
     * Line item balance
     *
     * @return string|null
     */
    public function getBalance()
    {
        return isset($this->attributes['balance']) ? $this->attributes['balance'] : null;
    }
    /**
     * Line item created at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Line item currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return isset($this->attributes['currency']) ? $this->attributes['currency'] : null;
    }
    /**
     * Line item download uri
     *
     * @return string|null
     */
    public function getDownloadUri()
    {
        return isset($this->attributes['download_uri']) ? $this->attributes['download_uri'] : null;
    }
    /**
     * Associated invoice line items
     *
     * @return object[]|null
     */
    public function getInvoiceLineItems()
    {
        return isset($this->attributes['invoice_line_items']) ? $this->attributes['invoice_line_items'] : null;
    }
    /**
     * Line item payment method
     *
     * @return string|null
     */
    public function getMethod()
    {
        return isset($this->attributes['method']) ? $this->attributes['method'] : null;
    }
    /**
     * Associated payment line items
     *
     * @return object[]|null
     */
    public function getPaymentLineItems()
    {
        return isset($this->attributes['payment_line_items']) ? $this->attributes['payment_line_items'] : null;
    }
    /**
     * Date/time payment was reversed if applicable
     *
     * @return string|null
     */
    public function getPaymentReversedAt()
    {
        return isset($this->attributes['payment_reversed_at']) ? $this->attributes['payment_reversed_at'] : null;
    }
    /**
     * Type of payment if applicable
     *
     * @return string|null
     */
    public function getPaymentType()
    {
        return isset($this->attributes['payment_type']) ? $this->attributes['payment_type'] : null;
    }
    /**
     * Site name this line item is for
     *
     * @return string|null
     */
    public function getSiteName()
    {
        return isset($this->attributes['site_name']) ? $this->attributes['site_name'] : null;
    }
    /**
     * Type of line item, either payment or invoice
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }
}
