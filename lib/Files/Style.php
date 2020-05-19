<?php

declare(strict_types=1);

namespace Files;

/**
 * Class Style
 *
 * @package Files
 */
class Style {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // int64 # Style ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return $this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // Logo
  public function getLogo() {
    return $this->attributes['logo'];
  }

  public function setLogo($value) {
    return $this->attributes['logo'] = $value;
  }

  // Logo thumbnail
  public function getThumbnail() {
    return $this->attributes['thumbnail'];
  }

  public function setThumbnail($value) {
    return $this->attributes['thumbnail'] = $value;
  }

  // file # Logo for custom branding.
  public function getFile() {
    return $this->attributes['file'];
  }

  public function setFile($value) {
    return $this->attributes['file'] = $value;
  }

  // Parameters:
  //   file (required) - file - Logo for custom branding.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    if (!$params['file']) {
      if ($this->file) {
        $params['file'] = $this->file;
      } else {
        throw new \Error('Parameter missing: file');
      }
    }

    return Api::sendRequest('/styles/' . rawurlencode($params['path']) . '', 'PATCH', $params);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    return Api::sendRequest('/styles/' . rawurlencode($params['path']) . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    return $this->update($this->attributes);
  }

  // Parameters:
  //   path (required) - string - Style path.
  public static function list($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if ($params['path'] && !is_string($params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/styles/' . rawurlencode($params['path']) . '', 'GET', $params);

    return new Style((array)$response->data, $options);
  }

  public static function all($path, $params = [], $options = []) {
    return self::list($path, $params, $options);
  }
}
