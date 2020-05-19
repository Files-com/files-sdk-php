<?php

declare(strict_types=1);

namespace Files;

/**
 * Class FileCommentReaction
 *
 * @package Files
 */
class FileCommentReaction {
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

  // int64 # Reaction ID
  public function getId() {
    return $this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Emoji used in the reaction.
  public function getEmoji() {
    return $this->attributes['emoji'];
  }

  public function setEmoji($value) {
    return $this->attributes['emoji'] = $value;
  }

  // int64 # User ID.  Provide a value of `0` to operate the current session's user.
  public function getUserId() {
    return $this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // int64 # ID of file comment to attach reaction to.
  public function getFileCommentId() {
    return $this->attributes['file_comment_id'];
  }

  public function setFileCommentId($value) {
    return $this->attributes['file_comment_id'] = $value;
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no ID');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if ($params['id'] && !is_int($params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!$params['id']) {
      if ($this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/file_comment_reactions/' . $params['id'] . '', 'DELETE', $params);
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
    if ($this->attributes['id']) {
      throw new \BadMethodCallException('The FileCommentReaction object doesn\'t support updates.');
    } else {
      $new_obj = self::create($this->attributes, $this->options);
      $this->attributes = $new_obj->attributes;
      return true;
    }
  }

  // Parameters:
  //   user_id - integer - User ID.  Provide a value of `0` to operate the current session's user.
  //   file_comment_id (required) - integer - ID of file comment to attach reaction to.
  //   emoji (required) - string - Emoji to react with.
  public static function create($params = [], $options = []) {
    if (!$params['file_comment_id']) {
      throw new \Error('Parameter missing: file_comment_id');
    }

    if (!$params['emoji']) {
      throw new \Error('Parameter missing: emoji');
    }

    if ($params['user_id'] && !is_int($params['user_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_id must be of type int; received ' . gettype($user_id));
    }

    if ($params['file_comment_id'] && !is_int($params['file_comment_id'])) {
      throw new \InvalidArgumentException('Bad parameter: $file_comment_id must be of type int; received ' . gettype($file_comment_id));
    }

    if ($params['emoji'] && !is_string($params['emoji'])) {
      throw new \InvalidArgumentException('Bad parameter: $emoji must be of type string; received ' . gettype($emoji));
    }

    $response = Api::sendRequest('/file_comment_reactions', 'POST', $params);

    return new FileCommentReaction((array)$response->data, $options);
  }
}
