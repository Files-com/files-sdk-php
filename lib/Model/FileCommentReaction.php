<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileCommentReaction
 *
 * @package Files
 */
class FileCommentReaction
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
        return @$this->attributes[$name];
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
    // int64 # Reaction ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // string # Emoji used in the reaction.
    public function getEmoji()
    {
        return @$this->attributes['emoji'];
    }

    public function setEmoji($value)
    {
        return $this->attributes['emoji'] = $value;
    }
    // int64 # User ID.  Provide a value of `0` to operate the current session's user.
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // int64 # ID of file comment to attach reaction to.
    public function getFileCommentId()
    {
        return @$this->attributes['file_comment_id'];
    }

    public function setFileCommentId($value)
    {
        return $this->attributes['file_comment_id'] = $value;
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/file_comment_reactions/' . @$params['id'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    public function save()
    {
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The FileCommentReaction object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   file_comment_id (required) - int64 - ID of file comment to attach reaction to.
    //   emoji (required) - string - Emoji to react with.
    public static function create($params = [], $options = [])
    {
        if (!@$params['file_comment_id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: file_comment_id');
        }

        if (!@$params['emoji']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: emoji');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['file_comment_id'] && !is_int(@$params['file_comment_id'])) {
            throw new \Files\Exception\InvalidParameterException('$file_comment_id must be of type int; received ' . gettype(@$params['file_comment_id']));
        }

        if (@$params['emoji'] && !is_string(@$params['emoji'])) {
            throw new \Files\Exception\InvalidParameterException('$emoji must be of type string; received ' . gettype(@$params['emoji']));
        }

        $response = Api::sendRequest('/file_comment_reactions', 'POST', $params, $options);

        return new FileCommentReaction((array) (@$response->data ?: []), $options);
    }
}
