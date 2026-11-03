<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An InboxRecipient is a person who has had an inbox shared with them via email invitation.
 *
 * @property string|null $company The recipient's company.
 * @property string|null $name The recipient's name.
 * @property string|null $note A note sent to the recipient with the inbox.
 * @property string|null $recipient The recipient's email address.
 * @property string|null $sent_at When the Inbox was shared with this recipient.
 * @property int|null $inbox_id Inbox to share.
 * @property bool|null $share_after_create Set to true to share the link with the recipient upon creation.
 * @package Files
 */
class InboxRecipient
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
     * The recipient's company.
     *
     * @return string|null
     */
    public function getCompany()
    {
        return isset($this->attributes['company']) ? $this->attributes['company'] : null;
    }

    /**
     * The recipient's company.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCompany($value)
    {
        return $this->attributes['company'] = $value;
    }
    /**
     * The recipient's name.
     *
     * @return string|null
     */
    public function getName()
    {
        return isset($this->attributes['name']) ? $this->attributes['name'] : null;
    }

    /**
     * The recipient's name.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setName($value)
    {
        return $this->attributes['name'] = $value;
    }
    /**
     * A note sent to the recipient with the inbox.
     *
     * @return string|null
     */
    public function getNote()
    {
        return isset($this->attributes['note']) ? $this->attributes['note'] : null;
    }

    /**
     * A note sent to the recipient with the inbox.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNote($value)
    {
        return $this->attributes['note'] = $value;
    }
    /**
     * The recipient's email address.
     *
     * @return string|null
     */
    public function getRecipient()
    {
        return isset($this->attributes['recipient']) ? $this->attributes['recipient'] : null;
    }

    /**
     * The recipient's email address.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRecipient($value)
    {
        return $this->attributes['recipient'] = $value;
    }
    /**
     * When the Inbox was shared with this recipient.
     *
     * @return string|null
     */
    public function getSentAt()
    {
        return isset($this->attributes['sent_at']) ? $this->attributes['sent_at'] : null;
    }

    /**
     * When the Inbox was shared with this recipient.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSentAt($value)
    {
        return $this->attributes['sent_at'] = $value;
    }
    /**
     * Inbox to share.
     *
     * @return int|null
     */
    public function getInboxId()
    {
        return isset($this->attributes['inbox_id']) ? $this->attributes['inbox_id'] : null;
    }

    /**
     * Inbox to share.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInboxId($value)
    {
        return $this->attributes['inbox_id'] = $value;
    }
    /**
     * Set to true to share the link with the recipient upon creation.
     *
     * @return bool|null
     */
    public function getShareAfterCreate()
    {
        return isset($this->attributes['share_after_create']) ? $this->attributes['share_after_create'] : null;
    }

    /**
     * Set to true to share the link with the recipient upon creation.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setShareAfterCreate($value)
    {
        return $this->attributes['share_after_create'] = $value;
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
            throw new \Files\Exception\NotImplementedException('The InboxRecipient object doesn\'t support updates.');
        } else {
            $new_obj = self::create($this->attributes, $this->options);
            $this->attributes = $new_obj->attributes;
            return true;
        }
    }


    /**
     * List Inbox Recipients
     *
     * GET /inbox_recipients
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are .
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `has_registrations`.
     *     - `inbox_id` (int, required): List recipients for the inbox with this ID.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\InboxRecipient[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (empty($params['inbox_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: inbox_id');
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['inbox_id']) && !is_int($params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        $response = Api::sendRequest('/inbox_recipients', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new InboxRecipient((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Create Inbox Recipient
     *
     * POST /inbox_recipients
     *
     * @param array $params Request parameters.
     *     - `inbox_id` (int, required): Inbox to share.
     *     - `recipient` (string, required): Email address to share this inbox with.
     *     - `name` (string, optional): Name of recipient.
     *     - `company` (string, optional): Company of recipient.
     *     - `note` (string, optional): Note to include in email.
     *     - `share_after_create` (bool, optional): Set to true to share the link with the recipient upon creation.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\InboxRecipient
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['inbox_id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: inbox_id');
        }

        if (empty($params['recipient'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: recipient');
        }

        if (!empty($params['inbox_id']) && !is_int($params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        if (!empty($params['recipient']) && !is_string($params['recipient'])) {
            throw new \Files\Exception\InvalidParameterException('$recipient must be of type string; received ' . gettype(@$params['recipient']));
        }

        if (!empty($params['name']) && !is_string($params['name'])) {
            throw new \Files\Exception\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
        }

        if (!empty($params['company']) && !is_string($params['company'])) {
            throw new \Files\Exception\InvalidParameterException('$company must be of type string; received ' . gettype(@$params['company']));
        }

        if (!empty($params['note']) && !is_string($params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        $response = Api::sendRequest('/inbox_recipients', 'POST', $params, $options);

        return new InboxRecipient((array) (@$response->data ?: []), $options);
    }
}
