<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * An AS2OutgoingMessage is a single record created for each individual AS2 file transfer out to a Partner.
 *
 * @property int|null $id Id of the AS2 Partner.
 * @property int|null $workspace_id ID of the Workspace associated with this AS2 Outgoing Message.
 * @property int|null $as2_partner_id Id of the AS2 Partner associated with this message.
 * @property int|null $as2_station_id Id of the AS2 Station associated with this message.
 * @property string|null $uuid UUID assigned to this message.
 * @property object|null $http_headers HTTP Headers sent with this message.
 * @property string|null $processing_result Result of processing.
 * @property string|null $processing_result_description Result of processing description.
 * @property string|null $mic AS2 Message Integrity Check SHA1
 * @property string|null $mic_sha_256 AS2 Message Integrity Check SHA256
 * @property string|null $as2_to AS2 TO
 * @property string|null $as2_from AS2 FROM
 * @property string|null $as2_subject AS2 Subject
 * @property string|null $date Date Header
 * @property string|null $message_id AS2 Message Id
 * @property string|null $body_size Encrypted Payload Body Size
 * @property string|null $attachment_filename Filename of the file being sent.
 * @property string|null $created_at Message creation date/time
 * @property string|null $http_response_code HTTP Response Code received for this message
 * @property object|null $http_response_headers HTTP Headers received for this message.
 * @property float|null $http_transmission_duration HTTP transmission duration in seceonds
 * @property bool|null $mdn_received Did the partner give a response body?
 * @property bool|null $mdn_valid Is the response in MDN format?
 * @property bool|null $mdn_signature_verified MDN signature verified?
 * @property bool|null $mdn_message_id_matched MDN message id matched?
 * @property bool|null $mdn_mic_matched MDN MIC matched?
 * @property bool|null $mdn_processing_success MDN disposition indicate a successful processing?
 * @property string|null $raw_uri URL to download the original file contents
 * @property string|null $smime_uri URL to download the file contents encoded as smime
 * @property string|null $smime_signed_uri URL to download the file contents as smime with signature
 * @property string|null $encrypted_uri URL to download the encrypted signed smime that is to sent as AS2 body
 * @property string|null $mdn_response_uri URL to download the http response body
 * @package Files
 */
class As2OutgoingMessage
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
     * Id of the AS2 Partner.
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    /**
     * ID of the Workspace associated with this AS2 Outgoing Message.
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
     * AS2 Message Integrity Check SHA1
     *
     * @return string|null
     */
    public function getMic()
    {
        return isset($this->attributes['mic']) ? $this->attributes['mic'] : null;
    }
    /**
     * AS2 Message Integrity Check SHA256
     *
     * @return string|null
     */
    public function getMicSha256()
    {
        return isset($this->attributes['mic_sha_256']) ? $this->attributes['mic_sha_256'] : null;
    }
    /**
     * AS2 TO
     *
     * @return string|null
     */
    public function getAs2To()
    {
        return isset($this->attributes['as2_to']) ? $this->attributes['as2_to'] : null;
    }
    /**
     * AS2 FROM
     *
     * @return string|null
     */
    public function getAs2From()
    {
        return isset($this->attributes['as2_from']) ? $this->attributes['as2_from'] : null;
    }
    /**
     * AS2 Subject
     *
     * @return string|null
     */
    public function getAs2Subject()
    {
        return isset($this->attributes['as2_subject']) ? $this->attributes['as2_subject'] : null;
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
     * AS2 Message Id
     *
     * @return string|null
     */
    public function getMessageId()
    {
        return isset($this->attributes['message_id']) ? $this->attributes['message_id'] : null;
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
     * Filename of the file being sent.
     *
     * @return string|null
     */
    public function getAttachmentFilename()
    {
        return isset($this->attributes['attachment_filename']) ? $this->attributes['attachment_filename'] : null;
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
     * HTTP Response Code received for this message
     *
     * @return string|null
     */
    public function getHttpResponseCode()
    {
        return isset($this->attributes['http_response_code']) ? $this->attributes['http_response_code'] : null;
    }
    /**
     * HTTP Headers received for this message.
     *
     * @return object|null
     */
    public function getHttpResponseHeaders()
    {
        return isset($this->attributes['http_response_headers']) ? $this->attributes['http_response_headers'] : null;
    }
    /**
     * HTTP transmission duration in seceonds
     *
     * @return float|null
     */
    public function getHttpTransmissionDuration()
    {
        return isset($this->attributes['http_transmission_duration']) ? $this->attributes['http_transmission_duration'] : null;
    }
    /**
     * Did the partner give a response body?
     *
     * @return bool|null
     */
    public function getMdnReceived()
    {
        return isset($this->attributes['mdn_received']) ? $this->attributes['mdn_received'] : null;
    }
    /**
     * Is the response in MDN format?
     *
     * @return bool|null
     */
    public function getMdnValid()
    {
        return isset($this->attributes['mdn_valid']) ? $this->attributes['mdn_valid'] : null;
    }
    /**
     * MDN signature verified?
     *
     * @return bool|null
     */
    public function getMdnSignatureVerified()
    {
        return isset($this->attributes['mdn_signature_verified']) ? $this->attributes['mdn_signature_verified'] : null;
    }
    /**
     * MDN message id matched?
     *
     * @return bool|null
     */
    public function getMdnMessageIdMatched()
    {
        return isset($this->attributes['mdn_message_id_matched']) ? $this->attributes['mdn_message_id_matched'] : null;
    }
    /**
     * MDN MIC matched?
     *
     * @return bool|null
     */
    public function getMdnMicMatched()
    {
        return isset($this->attributes['mdn_mic_matched']) ? $this->attributes['mdn_mic_matched'] : null;
    }
    /**
     * MDN disposition indicate a successful processing?
     *
     * @return bool|null
     */
    public function getMdnProcessingSuccess()
    {
        return isset($this->attributes['mdn_processing_success']) ? $this->attributes['mdn_processing_success'] : null;
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
     * URL to download the file contents encoded as smime
     *
     * @return string|null
     */
    public function getSmimeUri()
    {
        return isset($this->attributes['smime_uri']) ? $this->attributes['smime_uri'] : null;
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
     * URL to download the encrypted signed smime that is to sent as AS2 body
     *
     * @return string|null
     */
    public function getEncryptedUri()
    {
        return isset($this->attributes['encrypted_uri']) ? $this->attributes['encrypted_uri'] : null;
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
     * List AS2 Outgoing Messages
     *
     * GET /as2_outgoing_messages
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `as2_partner_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `as2_station_id`, `workspace_id` or `as2_partner_id`. Valid field combinations are `[ as2_station_id, created_at ]`, `[ workspace_id, created_at ]`, `[ as2_partner_id, created_at ]`, `[ workspace_id, as2_station_id ]`, `[ workspace_id, as2_partner_id ]`, `[ workspace_id, as2_station_id, created_at ]` or `[ workspace_id, as2_partner_id, created_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\As2OutgoingMessage[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/as2_outgoing_messages', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new As2OutgoingMessage(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        return \Files\Page::fetch('/as2_outgoing_messages', $params, $options, As2OutgoingMessage::class);
    }
}
