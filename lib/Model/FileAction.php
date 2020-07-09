<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileAction
 *
 * @package Files
 */
class FileAction {
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

  // Copy file/folder
  //
  // Parameters:
  //   destination (required) - string - Copy destination path.
  //   structure - boolean - Copy structure only?
  public function copy($params = []) {
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
    if ($params['destination'] && !is_string($params['destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination must be of type string; received ' . gettype($destination));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    if (!$params['destination']) {
      if ($this->destination) {
        $params['destination'] = $this->destination;
      } else {
        throw new \Error('Parameter missing: destination');
      }
    }

    return Api::sendRequest('/file_actions/copy/' . rawurlencode($params['path']) . '', 'POST', $params, $this->options);
  }

  // Move file/folder
  //
  // Parameters:
  //   destination (required) - string - Move destination path.
  public function move($params = []) {
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
    if ($params['destination'] && !is_string($params['destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination must be of type string; received ' . gettype($destination));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    if (!$params['destination']) {
      if ($this->destination) {
        $params['destination'] = $this->destination;
      } else {
        throw new \Error('Parameter missing: destination');
      }
    }

    return Api::sendRequest('/file_actions/move/' . rawurlencode($params['path']) . '', 'POST', $params, $this->options);
  }

  // Begin file upload
  //
  // Parameters:
  //   mkdir_parents - boolean - Create parent directories if they do not exist?
  //   part - int64 - Part if uploading a part.
  //   parts - int64 - How many parts to fetch?
  //   ref - string -
  //   restart - int64 - File byte offset to restart from.
  //   with_rename - boolean - Allow file rename instead of overwrite?
  public function beginUpload($params = []) {
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
    if ($params['part'] && !is_int($params['part'])) {
      throw new \InvalidArgumentException('Bad parameter: $part must be of type int; received ' . gettype($part));
    }
    if ($params['parts'] && !is_int($params['parts'])) {
      throw new \InvalidArgumentException('Bad parameter: $parts must be of type int; received ' . gettype($parts));
    }
    if ($params['ref'] && !is_string($params['ref'])) {
      throw new \InvalidArgumentException('Bad parameter: $ref must be of type string; received ' . gettype($ref));
    }
    if ($params['restart'] && !is_int($params['restart'])) {
      throw new \InvalidArgumentException('Bad parameter: $restart must be of type int; received ' . gettype($restart));
    }

    if (!$params['path']) {
      if ($this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Error('Parameter missing: path');
      }
    }

    return Api::sendRequest('/file_actions/begin_upload/' . rawurlencode($params['path']) . '', 'POST', $params, $this->options);
  }
}
