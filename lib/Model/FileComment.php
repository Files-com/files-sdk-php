<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileComment
 *
 * @package Files
 */
class FileComment {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # File Comment ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Comment body.
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // array # Reactions to this comment.
  public function getReactions() {
    return @$this->attributes['reactions'];
  }

  public function setReactions($value) {
    return $this->attributes['reactions'] = $value;
  }

  // string # File path.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // Parameters:
  //   body (required) - string - Comment body.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no id');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \InvalidArgumentException('Bad parameter: $body must be of type string; received ' . gettype($body));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    if (!@$params['body']) {
      if ($this->body) {
        $params['body'] = @$this->body;
      } else {
        throw new \Error('Parameter missing: body');
      }
    }

    return Api::sendRequest('/file_comments/' . @$params['id'] . '', 'PATCH', $params, $this->options);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no id');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/file_comments/' . @$params['id'] . '', 'DELETE', $params, $this->options);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   path (required) - string - Path to operate on.
  public static function listFor($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/file_comments/files/' . @$params['path'] . '', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new FileComment((array)$obj, $options);
    }

    return $return_array;
  }

  // Parameters:
  //   body (required) - string - Comment body.
  //   path (required) - string - File path.
  public static function create($params = [], $options = []) {
    if (!@$params['body']) {
      throw new \Error('Parameter missing: body');
    }

    if (!@$params['path']) {
      throw new \Error('Parameter missing: path');
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \InvalidArgumentException('Bad parameter: $body must be of type string; received ' . gettype($body));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/file_comments', 'POST', $params, $options);

    return new FileComment((array)(@$response->data ?: []), $options);
  }
}
