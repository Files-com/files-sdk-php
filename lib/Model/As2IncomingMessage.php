<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AS2IncomingMessage is a single record created for each individual AS2 file transfer incoming from a Partner.
 *
 * @property int|null $id ID of this AS2 incoming message record.
 * @property int|null $replayed_from_message_id ID of the incoming message record this message was replayed from. Empty for messages received directly from a trading partner.
 * @property int|null $workspace_id ID of the Workspace associated with this AS2 Incoming Message.
 * @property int|null $as2_partner_id Id of the AS2 Partner associated with this message.
 * @property int|null $as2_station_id Id of the AS2 Station associated with this message.
 * @property string|null $uuid UUID assigned to this message.
 * @property string|null $content_type Content Type header of the incoming message.
 * @property object|null $http_headers HTTP Headers sent with this message.
 * @property string|null $processing_result Result of processing.
 * @property string|null $processing_result_description Result of processing description.
 * @property string|null $mic AS2 Message Integrity Check
 * @property string|null $mic_algo AS2 Message Integrity Check Algorithm Used
 * @property string|null $as2_to AS2 TO header of message
 * @property string|null $as2_from AS2 FROM header of message
 * @property string|null $message_id AS2 Message Id
 * @property string|null $subject AS2 Subject Header
 * @property string|null $date Date Header
 * @property string|null $body_size Encrypted Payload Body Size
 * @property string|null $attachment_filename Filename of the file being received.
 * @property string|null $ip IP Address of the Sender
 * @property string|null $created_at Message creation date/time
 * @property string|null $http_response_code HTTP Response Code sent for this message
 * @property object|null $http_response_headers HTTP Headers sent for this message.
 * @property string|null $recipient_serial Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial
 * @property string|null $hex_recipient_serial Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial in hex format.
 * @property string|null $recipient_issuer Incoming Message Recipient(the Client Cert used to encrypt this message)'s issuer
 * @property bool|null $message_received Message body received?
 * @property bool|null $message_decrypted Message decrypted successfully?
 * @property bool|null $message_signature_verified Message signature verified?
 * @property bool|null $message_processing_success Message processed successfully?
 * @property bool|null $message_mdn_returned MDN returned?
 * @property string|null $encrypted_uri URL to download the encrypted signed smime that is to sent as AS2 body
 * @property string|null $smime_signed_uri URL to download the file contents as smime with signature
 * @property string|null $smime_uri URL to download the file contents encoded as smime
 * @property string|null $raw_uri URL to download the original file contents
 * @property string|null $mdn_response_uri URL to download the http response body
 * @package Files
 */
class As2IncomingMessage
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
     * ID of this AS2 incoming message record.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * ID of the incoming message record this message was replayed from. Empty for messages received directly from a trading partner.
     *
     * @return int|null
     */
    public function getReplayedFromMessageId()
    {
        return isset($this->attributes['replayed_from_message_id']) ? $this->attributes['replayed_from_message_id'] : null;
    }
    /**
     * ID of the Workspace associated with this AS2 Incoming Message.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    /**
     * Id of the AS2 Partner associated with this message.
     *
     * @return int|null
     */
    public function getAs2PartnerId()
    {
        return isset($this->attributes['as2_partner_id']) ? $this->attributes['as2_partner_id'] : null;
    }
    /**
     * Id of the AS2 Station associated with this message.
     *
     * @return int|null
     */
    public function getAs2StationId()
    {
        return isset($this->attributes['as2_station_id']) ? $this->attributes['as2_station_id'] : null;
    }
    /**
     * UUID assigned to this message.
     *
     * @return string|null
     */
    public function getUuid()
    {
        return isset($this->attributes['uuid']) ? $this->attributes['uuid'] : null;
    }
    /**
     * Content Type header of the incoming message.
     *
     * @return string|null
     */
    public function getContentType()
    {
        return isset($this->attributes['content_type']) ? $this->attributes['content_type'] : null;
    }
    /**
     * HTTP Headers sent with this message.
     *
     * @return object|null
     */
    public function getHttpHeaders()
    {
        return isset($this->attributes['http_headers']) ? $this->attributes['http_headers'] : null;
    }
    /**
     * Result of processing.
     *
     * @return string|null
     */
    public function getProcessingResult()
    {
        return isset($this->attributes['processing_result']) ? $this->attributes['processing_result'] : null;
    }
    /**
     * Result of processing description.
     *
     * @return string|null
     */
    public function getProcessingResultDescription()
    {
        return isset($this->attributes['processing_result_description']) ? $this->attributes['processing_result_description'] : null;
    }
    /**
     * AS2 Message Integrity Check
     *
     * @return string|null
     */
    public function getMic()
    {
        return isset($this->attributes['mic']) ? $this->attributes['mic'] : null;
    }
    /**
     * AS2 Message Integrity Check Algorithm Used
     *
     * @return string|null
     */
    public function getMicAlgo()
    {
        return isset($this->attributes['mic_algo']) ? $this->attributes['mic_algo'] : null;
    }
    /**
     * AS2 TO header of message
     *
     * @return string|null
     */
    public function getAs2To()
    {
        return isset($this->attributes['as2_to']) ? $this->attributes['as2_to'] : null;
    }
    /**
     * AS2 FROM header of message
     *
     * @return string|null
     */
    public function getAs2From()
    {
        return isset($this->attributes['as2_from']) ? $this->attributes['as2_from'] : null;
    }
    /**
     * AS2 Message Id
     *
     * @return string|null
     */
    public function getMessageId()
    {
        return isset($this->attributes['message_id']) ? $this->attributes['message_id'] : null;
    }
    /**
     * AS2 Subject Header
     *
     * @return string|null
     */
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }
    /**
     * Date Header
     *
     * @return string|null
     */
    public function getDate()
    {
        return isset($this->attributes['date']) ? $this->attributes['date'] : null;
    }
    /**
     * Encrypted Payload Body Size
     *
     * @return string|null
     */
    public function getBodySize()
    {
        return isset($this->attributes['body_size']) ? $this->attributes['body_size'] : null;
    }
    /**
     * Filename of the file being received.
     *
     * @return string|null
     */
    public function getAttachmentFilename()
    {
        return isset($this->attributes['attachment_filename']) ? $this->attributes['attachment_filename'] : null;
    }
    /**
     * IP Address of the Sender
     *
     * @return string|null
     */
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    /**
     * Message creation date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * HTTP Response Code sent for this message
     *
     * @return string|null
     */
    public function getHttpResponseCode()
    {
        return isset($this->attributes['http_response_code']) ? $this->attributes['http_response_code'] : null;
    }
    /**
     * HTTP Headers sent for this message.
     *
     * @return object|null
     */
    public function getHttpResponseHeaders()
    {
        return isset($this->attributes['http_response_headers']) ? $this->attributes['http_response_headers'] : null;
    }
    /**
     * Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial
     *
     * @return string|null
     */
    public function getRecipientSerial()
    {
        return isset($this->attributes['recipient_serial']) ? $this->attributes['recipient_serial'] : null;
    }
    /**
     * Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial in hex format.
     *
     * @return string|null
     */
    public function getHexRecipientSerial()
    {
        return isset($this->attributes['hex_recipient_serial']) ? $this->attributes['hex_recipient_serial'] : null;
    }
    /**
     * Incoming Message Recipient(the Client Cert used to encrypt this message)'s issuer
     *
     * @return string|null
     */
    public function getRecipientIssuer()
    {
        return isset($this->attributes['recipient_issuer']) ? $this->attributes['recipient_issuer'] : null;
    }
    /**
     * Message body received?
     *
     * @return bool|null
     */
    public function getMessageReceived()
    {
        return isset($this->attributes['message_received']) ? $this->attributes['message_received'] : null;
    }
    /**
     * Message decrypted successfully?
     *
     * @return bool|null
     */
    public function getMessageDecrypted()
    {
        return isset($this->attributes['message_decrypted']) ? $this->attributes['message_decrypted'] : null;
    }
    /**
     * Message signature verified?
     *
     * @return bool|null
     */
    public function getMessageSignatureVerified()
    {
        return isset($this->attributes['message_signature_verified']) ? $this->attributes['message_signature_verified'] : null;
    }
    /**
     * Message processed successfully?
     *
     * @return bool|null
     */
    public function getMessageProcessingSuccess()
    {
        return isset($this->attributes['message_processing_success']) ? $this->attributes['message_processing_success'] : null;
    }
    /**
     * MDN returned?
     *
     * @return bool|null
     */
    public function getMessageMdnReturned()
    {
        return isset($this->attributes['message_mdn_returned']) ? $this->attributes['message_mdn_returned'] : null;
    }
    /**
     * URL to download the encrypted signed smime that is to sent as AS2 body
     *
     * @return string|null
     */
    public function getEncryptedUri()
    {
        return isset($this->attributes['encrypted_uri']) ? $this->attributes['encrypted_uri'] : null;
    }
    /**
     * URL to download the file contents as smime with signature
     *
     * @return string|null
     */
    public function getSmimeSignedUri()
    {
        return isset($this->attributes['smime_signed_uri']) ? $this->attributes['smime_signed_uri'] : null;
    }
    /**
     * URL to download the file contents encoded as smime
     *
     * @return string|null
     */
    public function getSmimeUri()
    {
        return isset($this->attributes['smime_uri']) ? $this->attributes['smime_uri'] : null;
    }
    /**
     * URL to download the original file contents
     *
     * @return string|null
     */
    public function getRawUri()
    {
        return isset($this->attributes['raw_uri']) ? $this->attributes['raw_uri'] : null;
    }
    /**
     * URL to download the http response body
     *
     * @return string|null
     */
    public function getMdnResponseUri()
    {
        return isset($this->attributes['mdn_response_uri']) ? $this->attributes['mdn_response_uri'] : null;
    }

    /**
     * Replay AS2 Incoming Message
     *
     * POST /as2_incoming_messages/{id}/replay
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): As2 Incoming Message ID.
     * @return \Files\Model\As2IncomingMessage
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function replay($params = [])
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

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/as2_incoming_messages/' . rawurlencode(strval(@$params['id'])) . '/replay', 'POST', $params, $this->options);
        return new As2IncomingMessage((array) (@$response->data ?: []), $this->options);
    }

    /**
     * List AS2 Incoming Messages
     *
     * GET /as2_incoming_messages
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `as2_partner_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `as2_station_id`, `workspace_id`, `replayed_from_message_id` or `as2_partner_id`. Valid field combinations are `[ as2_station_id, created_at ]`, `[ workspace_id, created_at ]`, `[ replayed_from_message_id, created_at ]`, `[ as2_partner_id, created_at ]`, `[ workspace_id, as2_station_id ]`, `[ workspace_id, replayed_from_message_id ]`, `[ workspace_id, as2_partner_id ]`, `[ workspace_id, as2_station_id, created_at ]`, `[ workspace_id, replayed_from_message_id, created_at ]` or `[ workspace_id, as2_partner_id, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2IncomingMessage[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/as2_incoming_messages', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2IncomingMessage((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show AS2 Incoming Message
     *
     * GET /as2_incoming_messages/{id}
     *
     * @param int $id As2 Incoming Message ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2IncomingMessage
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/as2_incoming_messages/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new As2IncomingMessage((array) (@$response->data ?: []), $options);
    }
    /**
     * Show AS2 Incoming Message
     *
     * GET /as2_incoming_messages/{id}
     *
     * Alias of find().
     *
     * @param int $id As2 Incoming Message ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2IncomingMessage
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
