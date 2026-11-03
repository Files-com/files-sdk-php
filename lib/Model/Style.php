<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Style is a custom set of branding that can be applied on a per-folder basis.
 *
 * @property int|null $id Style ID
 * @property string|null $path Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property \stdClass|null $logo Logo
 * @property string|null $logo_click_href URL to open when a public visitor clicks the logo
 * @property \stdClass|null $thumbnail Logo thumbnail
 * @property mixed $file Logo for custom branding. Required when creating a new style.
 * @package Files
 */
class Style
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
        return !!@$this->attributes['path'];
    }
    /**
     * Style ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Style ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * Logo
     *
     * @return \stdClass|null
     */
    public function getLogo()
    {
        return isset($this->attributes['logo']) ? $this->attributes['logo'] : null;
    }

    /**
     * Logo
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setLogo($value)
    {
        return $this->attributes['logo'] = $value;
    }
    /**
     * URL to open when a public visitor clicks the logo
     *
     * @return string|null
     */
    public function getLogoClickHref()
    {
        return isset($this->attributes['logo_click_href']) ? $this->attributes['logo_click_href'] : null;
    }

    /**
     * URL to open when a public visitor clicks the logo
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLogoClickHref($value)
    {
        return $this->attributes['logo_click_href'] = $value;
    }
    /**
     * Logo thumbnail
     *
     * @return \stdClass|null
     */
    public function getThumbnail()
    {
        return isset($this->attributes['thumbnail']) ? $this->attributes['thumbnail'] : null;
    }

    /**
     * Logo thumbnail
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setThumbnail($value)
    {
        return $this->attributes['thumbnail'] = $value;
    }
    /**
     * Logo for custom branding. Required when creating a new style.
     *
     * @return mixed
     */
    public function getFile()
    {
        return isset($this->attributes['file']) ? $this->attributes['file'] : null;
    }

    /**
     * Logo for custom branding. Required when creating a new style.
     *
     * @param mixed $value
     * @return mixed The assigned value.
     */
    public function setFile($value)
    {
        return $this->attributes['file'] = $value;
    }

    /**
     * Update Style
     *
     * PATCH /styles/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Style path.
     *     - `file` (mixed, optional): Logo for custom branding. Required when creating a new style.
     *     - `logo_click_href` (string, optional): URL to open when a public visitor clicks the logo.
     * @return \Files\Model\Style
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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

    /**
     * Delete Style
     *
     * DELETE /styles/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Style path.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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

    /**
     * Delete Style
     *
     * DELETE /styles/{path}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Style path.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        $new_obj = $this->update($this->attributes);
        $this->attributes = $new_obj->attributes;
        return true;
    }

    /**
     * Show Style
     *
     * GET /styles/{path}
     *
     * @param string $path Style path.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Style
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
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
    /**
     * Show Style
     *
     * GET /styles/{path}
     *
     * Alias of find().
     *
     * @param string $path Style path.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Style
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($path, $params = [], $options = [])
    {
        return self::find($path, $params, $options);
    }
}
