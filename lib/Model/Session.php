<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Session is an operation that allows you to make further API calls using our REST API or SDKs as a specific user.
 *
 * @property string|null $id Session ID
 * @property string|null $language Session language
 * @property bool|null $read_only Is this session read only?
 * @property bool|null $sftp_insecure_ciphers Are insecure SFTP ciphers allowed for this user? (If this is set to true, the site administrator has signaled that it is ok to use less secure SSH ciphers for this user.)
 * @property string|null $username Username to sign in as
 * @property string|null $password Password for sign in
 * @property string|null $otp If this user has a 2FA device, provide its OTP or code here.
 * @property string|null $partial_session_id Identifier for a partially-completed login
 * @package Files
 */
class Session
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
     * Session ID
     *
     * @return string|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Session ID
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Session language
     *
     * @return string|null
     */
    public function getLanguage()
    {
        return isset($this->attributes['language']) ? $this->attributes['language'] : null;
    }

    /**
     * Session language
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setLanguage($value)
    {
        return $this->attributes['language'] = $value;
    }
    /**
     * Is this session read only?
     *
     * @return bool|null
     */
    public function getReadOnly()
    {
        return isset($this->attributes['read_only']) ? $this->attributes['read_only'] : null;
    }

    /**
     * Is this session read only?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setReadOnly($value)
    {
        return $this->attributes['read_only'] = $value;
    }
    /**
     * Are insecure SFTP ciphers allowed for this user? (If this is set to true, the site administrator has signaled that it is ok to use less secure SSH ciphers for this user.)
     *
     * @return bool|null
     */
    public function getSftpInsecureCiphers()
    {
        return isset($this->attributes['sftp_insecure_ciphers']) ? $this->attributes['sftp_insecure_ciphers'] : null;
    }

    /**
     * Are insecure SFTP ciphers allowed for this user? (If this is set to true, the site administrator has signaled that it is ok to use less secure SSH ciphers for this user.)
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSftpInsecureCiphers($value)
    {
        return $this->attributes['sftp_insecure_ciphers'] = $value;
    }
    /**
     * Username to sign in as
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Username to sign in as
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * Password for sign in
     *
     * @return string|null
     */
    public function getPassword()
    {
        return isset($this->attributes['password']) ? $this->attributes['password'] : null;
    }

    /**
     * Password for sign in
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    /**
     * If this user has a 2FA device, provide its OTP or code here.
     *
     * @return string|null
     */
    public function getOtp()
    {
        return isset($this->attributes['otp']) ? $this->attributes['otp'] : null;
    }

    /**
     * If this user has a 2FA device, provide its OTP or code here.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setOtp($value)
    {
        return $this->attributes['otp'] = $value;
    }
    /**
     * Identifier for a partially-completed login
     *
     * @return string|null
     */
    public function getPartialSessionId()
    {
        return isset($this->attributes['partial_session_id']) ? $this->attributes['partial_session_id'] : null;
    }

    /**
     * Identifier for a partially-completed login
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPartialSessionId($value)
    {
        return $this->attributes['partial_session_id'] = $value;
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
            throw new \Files\Exception\NotImplementedException('The Session object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * Create user session (log in)
     *
     * POST /sessions
     *
     * @param array $params Request parameters.
     *     - `username` (string, optional): Username to sign in as
     *     - `password` (string, optional): Password for sign in
     *     - `otp` (string, optional): If this user has a 2FA device, provide its OTP or code here.
     *     - `partial_session_id` (string, optional): Identifier for a partially-completed login
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Session
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        if (isset($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (isset($params['otp']) && !is_string($params['otp'])) {
            throw new \Files\Exception\InvalidParameterException('$otp must be of type string; received ' . gettype(@$params['otp']));
        }

        if (isset($params['partial_session_id']) && !is_string($params['partial_session_id'])) {
            throw new \Files\Exception\InvalidParameterException('$partial_session_id must be of type string; received ' . gettype(@$params['partial_session_id']));
        }

        $response = Api::sendRequest('/sessions', 'POST', $params, $options);

        return new Session(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Delete user session (log out)
     *
     * DELETE /sessions
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function delete($params = [], $options = [])
    {
        $response = Api::sendRequest('/sessions', 'DELETE', null, $options);

        return;
    }
    /**
     * Delete user session (log out)
     *
     * DELETE /sessions
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function destroy($params = [], $options = [])
    {
        return self::delete($params, $options);
    }
}
