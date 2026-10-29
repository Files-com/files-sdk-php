<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class InvoiceLineItem
 *
 * @package Files
 */
class InvoiceLineItem
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
    // int64 # Invoice Line item Id
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // decimal # Invoice line item amount
    public function getAmount()
    {
        return isset($this->attributes['amount']) ? $this->attributes['amount'] : null;
    }
    // date-time # Invoice line item created at date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // string # Invoice line item description
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }
    // string # Invoice line item type
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }
    // date-time # Invoice line item service end date/time
    public function getServiceEndAt()
    {
        return isset($this->attributes['service_end_at']) ? $this->attributes['service_end_at'] : null;
    }
    // date-time # Invoice line item service start date/time
    public function getServiceStartAt()
    {
        return isset($this->attributes['service_start_at']) ? $this->attributes['service_start_at'] : null;
    }
    // string # Plan name
    public function getPlan()
    {
        return isset($this->attributes['plan']) ? $this->attributes['plan'] : null;
    }
    // string # Site name
    public function getSite()
    {
        return isset($this->attributes['site']) ? $this->attributes['site'] : null;
    }
    // int64 # Prepaid bytes purchased for this invoice line item
    public function getPrepaidBytes()
    {
        return isset($this->attributes['prepaid_bytes']) ? $this->attributes['prepaid_bytes'] : null;
    }
    // date-time # When the prepaid bytes expire
    public function getPrepaidBytesExpireAt()
    {
        return isset($this->attributes['prepaid_bytes_expire_at']) ? $this->attributes['prepaid_bytes_expire_at'] : null;
    }
    // int64 # Total prepaid bytes used for this invoice line item
    public function getPrepaidBytesUsed()
    {
        return isset($this->attributes['prepaid_bytes_used']) ? $this->attributes['prepaid_bytes_used'] : null;
    }
    // int64 # Available prepaid bytes for this invoice line item
    public function getPrepaidBytesAvailable()
    {
        return isset($this->attributes['prepaid_bytes_available']) ? $this->attributes['prepaid_bytes_available'] : null;
    }
}
