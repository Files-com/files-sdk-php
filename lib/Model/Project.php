<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Project
 *
 * @package Files
 */
class Project {
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

  // int64 # Project ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Global access settings
  public function getGlobalAccess() {
    return @$this->attributes['global_access'];
  }

  public function setGlobalAccess($value) {
    return $this->attributes['global_access'] = $value;
  }

  // Parameters:
  //   global_access (required) - string - Global permissions.  Can be: `none`, `anyone_with_read`, `anyone_with_full`.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Files\EmptyPropertyException('The current Project object has no $id value');
    }

    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }
    if (@$params['global_access'] && !is_string(@$params['global_access'])) {
      throw new \Files\InvalidParameterException('$global_access must be of type string; received ' . gettype($global_access));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (!@$params['global_access']) {
      if ($this->global_access) {
        $params['global_access'] = @$this->global_access;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: global_access');
      }
    }

    return Api::sendRequest('/projects/' . @$params['id'] . '', 'PATCH', $params, $this->options);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Files\EmptyPropertyException('The current Project object has no $id value');
    }

    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    return Api::sendRequest('/projects/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/projects', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Project((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Project ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/projects/' . @$params['id'] . '', 'GET', $params, $options);

    return new Project((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   global_access (required) - string - Global permissions.  Can be: `none`, `anyone_with_read`, `anyone_with_full`.
  public static function create($params = [], $options = []) {
    if (!@$params['global_access']) {
      throw new \Files\MissingParameterException('Parameter missing: global_access');
    }

    if (@$params['global_access'] && !is_string(@$params['global_access'])) {
      throw new \Files\InvalidParameterException('$global_access must be of type string; received ' . gettype($global_access));
    }

    $response = Api::sendRequest('/projects', 'POST', $params, $options);

    return new Project((array)(@$response->data ?: []), $options);
  }
}
