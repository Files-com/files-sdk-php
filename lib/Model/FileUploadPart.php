<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A FileUploadPart is a result of a begin upload operation which is the first step in our upload process.
 *
 * @property object|null $send Content-Type and File to send
 * @property string|null $action Type of upload
 * @property bool|null $ask_about_overwrites If `true`, this file exists and you may wish to ask the user for overwrite confirmation
 * @property int|null $available_parts Number of parts in the upload
 * @property string|null $expires Date/time of when this Upload part expires and the URL cannot be used any more
 * @property object|null $headers Additional upload headers to provide as part of the upload
 * @property string|null $http_method HTTP Method to use for uploading the part, usually `PUT`
 * @property int|null $next_partsize Size in bytes for this part
 * @property bool|null $parallel_parts If `true`, multiple parts may be uploaded in parallel. If `false`, be sure to only upload one part at a time, in order.
 * @property bool|null $retry_parts If `true`, parts may be retried. If `false`, a part cannot be retried and the upload should be restarted.
 * @property object|null $parameters Additional HTTP parameters to send with the upload
 * @property int|null $part_number Number of this upload part
 * @property int|null $partsize Size in bytes for the next upload part
 * @property string|null $path New file path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property string|null $ref Reference name for this upload part
 * @property string|null $upload_uri URI to upload this part to
 * @property \stdClass|null $direct_connection_info Optional direct connection information for direct Agent transfer attempts
 * @package Files
 */
class FileUploadPart
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
     * Content-Type and File to send
     *
     * @return object|null
     */
    public function getSend()
    {
        return isset($this->attributes['send']) ? $this->attributes['send'] : null;
    }
    /**
     * Type of upload
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    /**
     * If `true`, this file exists and you may wish to ask the user for overwrite confirmation
     *
     * @return bool|null
     */
    public function getAskAboutOverwrites()
    {
        return isset($this->attributes['ask_about_overwrites']) ? $this->attributes['ask_about_overwrites'] : null;
    }
    /**
     * Number of parts in the upload
     *
     * @return int|null
     */
    public function getAvailableParts()
    {
        return isset($this->attributes['available_parts']) ? $this->attributes['available_parts'] : null;
    }
    /**
     * Date/time of when this Upload part expires and the URL cannot be used any more
     *
     * @return string|null
     */
    public function getExpires()
    {
        return isset($this->attributes['expires']) ? $this->attributes['expires'] : null;
    }
    /**
     * Additional upload headers to provide as part of the upload
     *
     * @return object|null
     */
    public function getHeaders()
    {
        return isset($this->attributes['headers']) ? $this->attributes['headers'] : null;
    }
    /**
     * HTTP Method to use for uploading the part, usually `PUT`
     *
     * @return string|null
     */
    public function getHttpMethod()
    {
        return isset($this->attributes['http_method']) ? $this->attributes['http_method'] : null;
    }
    /**
     * Size in bytes for this part
     *
     * @return int|null
     */
    public function getNextPartsize()
    {
        return isset($this->attributes['next_partsize']) ? $this->attributes['next_partsize'] : null;
    }
    /**
     * If `true`, multiple parts may be uploaded in parallel.  If `false`, be sure to only upload one part at a time, in order.
     *
     * @return bool|null
     */
    public function getParallelParts()
    {
        return isset($this->attributes['parallel_parts']) ? $this->attributes['parallel_parts'] : null;
    }
    /**
     * If `true`, parts may be retried. If `false`, a part cannot be retried and the upload should be restarted.
     *
     * @return bool|null
     */
    public function getRetryParts()
    {
        return isset($this->attributes['retry_parts']) ? $this->attributes['retry_parts'] : null;
    }
    /**
     * Additional HTTP parameters to send with the upload
     *
     * @return object|null
     */
    public function getParameters()
    {
        return isset($this->attributes['parameters']) ? $this->attributes['parameters'] : null;
    }
    /**
     * Number of this upload part
     *
     * @return int|null
     */
    public function getPartNumber()
    {
        return isset($this->attributes['part_number']) ? $this->attributes['part_number'] : null;
    }
    /**
     * Size in bytes for the next upload part
     *
     * @return int|null
     */
    public function getPartsize()
    {
        return isset($this->attributes['partsize']) ? $this->attributes['partsize'] : null;
    }
    /**
     * New file path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    /**
     * Reference name for this upload part
     *
     * @return string|null
     */
    public function getRef()
    {
        return isset($this->attributes['ref']) ? $this->attributes['ref'] : null;
    }
    /**
     * URI to upload this part to
     *
     * @return string|null
     */
    public function getUploadUri()
    {
        return isset($this->attributes['upload_uri']) ? $this->attributes['upload_uri'] : null;
    }
    /**
     * Optional direct connection information for direct Agent transfer attempts
     *
     * @return \stdClass|null
     */
    public function getDirectConnectionInfo()
    {
        return isset($this->attributes['direct_connection_info']) ? $this->attributes['direct_connection_info'] : null;
    }
}
