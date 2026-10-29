<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileUploadPart
 *
 * @package Files
 */
class FileUploadPart
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
    // object # Content-Type and File to send
    public function getSend()
    {
        return isset($this->attributes['send']) ? $this->attributes['send'] : null;
    }
    // string # Type of upload
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }
    // boolean # If `true`, this file exists and you may wish to ask the user for overwrite confirmation
    public function getAskAboutOverwrites()
    {
        return isset($this->attributes['ask_about_overwrites']) ? $this->attributes['ask_about_overwrites'] : null;
    }
    // int64 # Number of parts in the upload
    public function getAvailableParts()
    {
        return isset($this->attributes['available_parts']) ? $this->attributes['available_parts'] : null;
    }
    // string # Date/time of when this Upload part expires and the URL cannot be used any more
    public function getExpires()
    {
        return isset($this->attributes['expires']) ? $this->attributes['expires'] : null;
    }
    // object # Additional upload headers to provide as part of the upload
    public function getHeaders()
    {
        return isset($this->attributes['headers']) ? $this->attributes['headers'] : null;
    }
    // string # HTTP Method to use for uploading the part, usually `PUT`
    public function getHttpMethod()
    {
        return isset($this->attributes['http_method']) ? $this->attributes['http_method'] : null;
    }
    // int64 # Size in bytes for this part
    public function getNextPartsize()
    {
        return isset($this->attributes['next_partsize']) ? $this->attributes['next_partsize'] : null;
    }
    // boolean # If `true`, multiple parts may be uploaded in parallel.  If `false`, be sure to only upload one part at a time, in order.
    public function getParallelParts()
    {
        return isset($this->attributes['parallel_parts']) ? $this->attributes['parallel_parts'] : null;
    }
    // boolean # If `true`, parts may be retried. If `false`, a part cannot be retried and the upload should be restarted.
    public function getRetryParts()
    {
        return isset($this->attributes['retry_parts']) ? $this->attributes['retry_parts'] : null;
    }
    // object # Additional HTTP parameters to send with the upload
    public function getParameters()
    {
        return isset($this->attributes['parameters']) ? $this->attributes['parameters'] : null;
    }
    // int64 # Number of this upload part
    public function getPartNumber()
    {
        return isset($this->attributes['part_number']) ? $this->attributes['part_number'] : null;
    }
    // int64 # Size in bytes for the next upload part
    public function getPartsize()
    {
        return isset($this->attributes['partsize']) ? $this->attributes['partsize'] : null;
    }
    // string # New file path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }
    // string # Reference name for this upload part
    public function getRef()
    {
        return isset($this->attributes['ref']) ? $this->attributes['ref'] : null;
    }
    // string # URI to upload this part to
    public function getUploadUri()
    {
        return isset($this->attributes['upload_uri']) ? $this->attributes['upload_uri'] : null;
    }
    // DirectConnectionInfo # Optional direct connection information for direct Agent transfer attempts
    public function getDirectConnectionInfo()
    {
        return isset($this->attributes['direct_connection_info']) ? $this->attributes['direct_connection_info'] : null;
    }
}
