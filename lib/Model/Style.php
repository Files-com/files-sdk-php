<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Style
 *
 * @package Files
 */
class Style
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
        return !!@$this->attributes['path'];
    }
    // int64 # Style ID
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // Image # Logo
    public function getLogo()
    {
        return isset($this->attributes['logo']) ? $this->attributes['logo'] : null;
    }

    public function setLogo($value)
    {
        return $this->attributes['logo'] = $value;
    }
    // string # URL to open when a public visitor clicks the logo
    public function getLogoClickHref()
    {
        return isset($this->attributes['logo_click_href']) ? $this->attributes['logo_click_href'] : null;
    }

    public function setLogoClickHref($value)
    {
        return $this->attributes['logo_click_href'] = $value;
    }
    // Image # Logo thumbnail
    public function getThumbnail()
    {
        return isset($this->attributes['thumbnail']) ? $this->attributes['thumbnail'] : null;
    }

    public function setThumbnail($value)
    {
        return $this->attributes['thumbnail'] = $value;
    }
    // file # Logo for custom branding. Required when creating a new style.
    public function getFile()
    {
        return isset($this->attributes['file']) ? $this->attributes['file'] : null;
    }

    public function setFile($value)
    {
        return $this->attributes['file'] = $value;
    }

    // Parameters:
    //   file - file - Logo for custom branding. Required when creating a new style.
    //   logo_click_href - string - URL to open when a public visitor clicks the logo.
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['logo_click_href']) && !is_string($params['logo_click_href'])) {
            throw new \Files\Exception\InvalidParameterException('$logo_click_href must be of type string; received ' . gettype(@$params['logo_click_href']));
        }

        $response = Api::sendRequest('/styles/' . rawurlencode(strval(@$params['path'])) . '', 'PATCH', $params, $this->options);
        return new Style((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/styles/' . rawurlencode(strval(@$params['path'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    public function save()
    {
        $new_obj = $this->update($this->attributes);
        $this->attributes = $new_obj->attributes;
        return true;
    }

    // Parameters:
    //   path (required) - string - Style path.
    public static function find($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/styles/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        return new Style((array) (@$response->data ?: []), $options);
    }
    public static function get($path, $params = [], $options = [])
    {
        return self::find($path, $params, $options);
    }
}
