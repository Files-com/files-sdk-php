<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A FileCommentReaction is a reaction that is attached to a comment on a file.
 *
 * @property int|null $id Reaction ID
 * @property string|null $emoji Emoji used in the reaction.
 * @property int|null $user_id User ID. Provide a value of `0` to operate the current session's user.
 * @property int|null $file_comment_id ID of file comment to attach reaction to.
 * @package Files
 */
class FileCommentReaction
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
        return !!@$this->attributes['id'];
    }
    /**
     * Reaction ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Reaction ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Emoji used in the reaction.
     *
     * @return string|null
     */
    public function getEmoji()
    {
        return isset($this->attributes['emoji']) ? $this->attributes['emoji'] : null;
    }

    /**
     * Emoji used in the reaction.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEmoji($value)
    {
        return $this->attributes['emoji'] = $value;
    }
    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * User ID.  Provide a value of `0` to operate the current session's user.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * ID of file comment to attach reaction to.
     *
     * @return int|null
     */
    public function getFileCommentId()
    {
        return isset($this->attributes['file_comment_id']) ? $this->attributes['file_comment_id'] : null;
    }

    /**
     * ID of file comment to attach reaction to.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFileCommentId($value)
    {
        return $this->attributes['file_comment_id'] = $value;
    }

    /**
     * Delete File Comment Reaction
     *
     * DELETE /file_comment_reactions/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): File Comment Reaction ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/file_comment_reactions/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete File Comment Reaction
     *
     * DELETE /file_comment_reactions/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): File Comment Reaction ID.
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
        if (@$this->attributes['id']) {
            throw new \Files\Exception\NotImplementedException('The FileCommentReaction object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * Create File Comment Reaction
     *
     * POST /file_comment_reactions
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `file_comment_id` (int, required): ID of file comment to attach reaction to.
     *     - `emoji` (string, required): Emoji to react with.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FileCommentReaction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['file_comment_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: file_comment_id');
        }

        if (empty($params['emoji'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: emoji');
        }

        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['file_comment_id']) && !is_int($params['file_comment_id'])) {
            throw new \Files\Exception\InvalidParameterException('$file_comment_id must be of type int; received ' . gettype(@$params['file_comment_id']));
        }

        if (isset($params['emoji']) && !is_string($params['emoji'])) {
            throw new \Files\Exception\InvalidParameterException('$emoji must be of type string; received ' . gettype(@$params['emoji']));
        }

        $response = Api::sendRequest('/file_comment_reactions', 'POST', $params, $options);

        return new FileCommentReaction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
