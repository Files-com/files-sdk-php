<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Message
 *
 * @package Files
 */
class Message {
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

  // int64 # Message ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Message subject.
  public function getSubject() {
    return @$this->attributes['subject'];
  }

  public function setSubject($value) {
    return $this->attributes['subject'] = $value;
  }

  // string # Message body.
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // Comments.
  public function getComments() {
    return @$this->attributes['comments'];
  }

  public function setComments($value) {
    return $this->attributes['comments'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // int64 # Project to which the message should be attached.
  public function getProjectId() {
    return @$this->attributes['project_id'];
  }

  public function setProjectId($value) {
    return $this->attributes['project_id'] = $value;
  }

  // Parameters:
  //   project_id (required) - int64 - Project to which the message should be attached.
  //   subject (required) - string - Message subject.
  //   body (required) - string - Message body.
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
    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $project_id must be of type int; received ' . gettype($project_id));
    }
    if (@$params['subject'] && !is_string(@$params['subject'])) {
      throw new \InvalidArgumentException('Bad parameter: $subject must be of type string; received ' . gettype($subject));
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

    if (!@$params['project_id']) {
      if ($this->project_id) {
        $params['project_id'] = @$this->project_id;
      } else {
        throw new \Error('Parameter missing: project_id');
      }
    }

    if (!@$params['subject']) {
      if ($this->subject) {
        $params['subject'] = @$this->subject;
      } else {
        throw new \Error('Parameter missing: subject');
      }
    }

    if (!@$params['body']) {
      if ($this->body) {
        $params['body'] = @$this->body;
      } else {
        throw new \Error('Parameter missing: body');
      }
    }

    return Api::sendRequest('/messages/' . @$params['id'] . '', 'PATCH', $params, $this->options);
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

    return Api::sendRequest('/messages/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   project_id (required) - int64 - Project for which to return messages.
  public static function list($params = [], $options = []) {
    if (!@$params['project_id']) {
      throw new \Error('Parameter missing: project_id');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $project_id must be of type int; received ' . gettype($project_id));
    }

    $response = Api::sendRequest('/messages', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Message((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Message ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/messages/' . @$params['id'] . '', 'GET', $params, $options);

    return new Message((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   project_id (required) - int64 - Project to which the message should be attached.
  //   subject (required) - string - Message subject.
  //   body (required) - string - Message body.
  public static function create($params = [], $options = []) {
    if (!@$params['project_id']) {
      throw new \Error('Parameter missing: project_id');
    }

    if (!@$params['subject']) {
      throw new \Error('Parameter missing: subject');
    }

    if (!@$params['body']) {
      throw new \Error('Parameter missing: body');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if (@$params['project_id'] && !is_int(@$params['project_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $project_id must be of type int; received ' . gettype($project_id));
    }

    if (@$params['subject'] && !is_string(@$params['subject'])) {
      throw new \InvalidArgumentException('Bad parameter: $subject must be of type string; received ' . gettype($subject));
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \InvalidArgumentException('Bad parameter: $body must be of type string; received ' . gettype($body));
    }

    $response = Api::sendRequest('/messages', 'POST', $params, $options);

    return new Message((array)(@$response->data ?: []), $options);
  }
}
