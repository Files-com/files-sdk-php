<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An InvoiceLineItem is your invoice record.
 *
 * @property int|null $id Invoice Line item Id
 * @property string|null $amount Invoice line item amount
 * @property string|null $created_at Invoice line item created at date/time
 * @property string|null $description Invoice line item description
 * @property string|null $type Invoice line item type
 * @property string|null $service_end_at Invoice line item service end date/time
 * @property string|null $service_start_at Invoice line item service start date/time
 * @property string|null $plan Plan name
 * @property string|null $site Site name
 * @property int|null $prepaid_bytes Prepaid bytes purchased for this invoice line item
 * @property string|null $prepaid_bytes_expire_at When the prepaid bytes expire
 * @property int|null $prepaid_bytes_used Total prepaid bytes used for this invoice line item
 * @property int|null $prepaid_bytes_available Available prepaid bytes for this invoice line item
 * @package Files
 */
class InvoiceLineItem
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
     * Invoice Line item Id
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * Invoice line item amount
     *
     * @return string|null
     */
    public function getAmount()
    {
        return isset($this->attributes['amount']) ? $this->attributes['amount'] : null;
    }
    /**
     * Invoice line item created at date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Invoice line item description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }
    /**
     * Invoice line item type
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }
    /**
     * Invoice line item service end date/time
     *
     * @return string|null
     */
    public function getServiceEndAt()
    {
        return isset($this->attributes['service_end_at']) ? $this->attributes['service_end_at'] : null;
    }
    /**
     * Invoice line item service start date/time
     *
     * @return string|null
     */
    public function getServiceStartAt()
    {
        return isset($this->attributes['service_start_at']) ? $this->attributes['service_start_at'] : null;
    }
    /**
     * Plan name
     *
     * @return string|null
     */
    public function getPlan()
    {
        return isset($this->attributes['plan']) ? $this->attributes['plan'] : null;
    }
    /**
     * Site name
     *
     * @return string|null
     */
    public function getSite()
    {
        return isset($this->attributes['site']) ? $this->attributes['site'] : null;
    }
    /**
     * Prepaid bytes purchased for this invoice line item
     *
     * @return int|null
     */
    public function getPrepaidBytes()
    {
        return isset($this->attributes['prepaid_bytes']) ? $this->attributes['prepaid_bytes'] : null;
    }
    /**
     * When the prepaid bytes expire
     *
     * @return string|null
     */
    public function getPrepaidBytesExpireAt()
    {
        return isset($this->attributes['prepaid_bytes_expire_at']) ? $this->attributes['prepaid_bytes_expire_at'] : null;
    }
    /**
     * Total prepaid bytes used for this invoice line item
     *
     * @return int|null
     */
    public function getPrepaidBytesUsed()
    {
        return isset($this->attributes['prepaid_bytes_used']) ? $this->attributes['prepaid_bytes_used'] : null;
    }
    /**
     * Available prepaid bytes for this invoice line item
     *
     * @return int|null
     */
    public function getPrepaidBytesAvailable()
    {
        return isset($this->attributes['prepaid_bytes_available']) ? $this->attributes['prepaid_bytes_available'] : null;
    }
}
