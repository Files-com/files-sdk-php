<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2OutgoingMessage
 *
 * @package Files
 */
class As2OutgoingMessage
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
    // int64 # Id of the AS2 Partner.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # ID of the Workspace associated with this AS2 Outgoing Message.
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }
    // int64 # Id of the AS2 Partner associated with this message.
    public function getAs2PartnerId()
    {
        return isset($this->attributes['as2_partner_id']) ? $this->attributes['as2_partner_id'] : null;
    }
    // int64 # Id of the AS2 Station associated with this message.
    public function getAs2StationId()
    {
        return isset($this->attributes['as2_station_id']) ? $this->attributes['as2_station_id'] : null;
    }
    // string # UUID assigned to this message.
    public function getUuid()
    {
        return isset($this->attributes['uuid']) ? $this->attributes['uuid'] : null;
    }
    // object # HTTP Headers sent with this message.
    public function getHttpHeaders()
    {
        return isset($this->attributes['http_headers']) ? $this->attributes['http_headers'] : null;
    }
    // string # Result of processing.
    public function getProcessingResult()
    {
        return isset($this->attributes['processing_result']) ? $this->attributes['processing_result'] : null;
    }
    // string # Result of processing description.
    public function getProcessingResultDescription()
    {
        return isset($this->attributes['processing_result_description']) ? $this->attributes['processing_result_description'] : null;
    }
    // string # AS2 Message Integrity Check SHA1
    public function getMic()
    {
        return isset($this->attributes['mic']) ? $this->attributes['mic'] : null;
    }
    // string # AS2 Message Integrity Check SHA256
    public function getMicSha256()
    {
        return isset($this->attributes['mic_sha_256']) ? $this->attributes['mic_sha_256'] : null;
    }
    // string # AS2 TO
    public function getAs2To()
    {
        return isset($this->attributes['as2_to']) ? $this->attributes['as2_to'] : null;
    }
    // string # AS2 FROM
    public function getAs2From()
    {
        return isset($this->attributes['as2_from']) ? $this->attributes['as2_from'] : null;
    }
    // string # AS2 Subject
    public function getAs2Subject()
    {
        return isset($this->attributes['as2_subject']) ? $this->attributes['as2_subject'] : null;
    }
    // string # Date Header
    public function getDate()
    {
        return isset($this->attributes['date']) ? $this->attributes['date'] : null;
    }
    // string # AS2 Message Id
    public function getMessageId()
    {
        return isset($this->attributes['message_id']) ? $this->attributes['message_id'] : null;
    }
    // string # Encrypted Payload Body Size
    public function getBodySize()
    {
        return isset($this->attributes['body_size']) ? $this->attributes['body_size'] : null;
    }
    // string # Filename of the file being sent.
    public function getAttachmentFilename()
    {
        return isset($this->attributes['attachment_filename']) ? $this->attributes['attachment_filename'] : null;
    }
    // date-time # Message creation date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // string # HTTP Response Code received for this message
    public function getHttpResponseCode()
    {
        return isset($this->attributes['http_response_code']) ? $this->attributes['http_response_code'] : null;
    }
    // object # HTTP Headers received for this message.
    public function getHttpResponseHeaders()
    {
        return isset($this->attributes['http_response_headers']) ? $this->attributes['http_response_headers'] : null;
    }
    // double # HTTP transmission duration in seceonds
    public function getHttpTransmissionDuration()
    {
        return isset($this->attributes['http_transmission_duration']) ? $this->attributes['http_transmission_duration'] : null;
    }
    // boolean # Did the partner give a response body?
    public function getMdnReceived()
    {
        return isset($this->attributes['mdn_received']) ? $this->attributes['mdn_received'] : null;
    }
    // boolean # Is the response in MDN format?
    public function getMdnValid()
    {
        return isset($this->attributes['mdn_valid']) ? $this->attributes['mdn_valid'] : null;
    }
    // boolean # MDN signature verified?
    public function getMdnSignatureVerified()
    {
        return isset($this->attributes['mdn_signature_verified']) ? $this->attributes['mdn_signature_verified'] : null;
    }
    // boolean # MDN message id matched?
    public function getMdnMessageIdMatched()
    {
        return isset($this->attributes['mdn_message_id_matched']) ? $this->attributes['mdn_message_id_matched'] : null;
    }
    // boolean # MDN MIC matched?
    public function getMdnMicMatched()
    {
        return isset($this->attributes['mdn_mic_matched']) ? $this->attributes['mdn_mic_matched'] : null;
    }
    // boolean # MDN disposition indicate a successful processing?
    public function getMdnProcessingSuccess()
    {
        return isset($this->attributes['mdn_processing_success']) ? $this->attributes['mdn_processing_success'] : null;
    }
    // string # URL to download the original file contents
    public function getRawUri()
    {
        return isset($this->attributes['raw_uri']) ? $this->attributes['raw_uri'] : null;
    }
    // string # URL to download the file contents encoded as smime
    public function getSmimeUri()
    {
        return isset($this->attributes['smime_uri']) ? $this->attributes['smime_uri'] : null;
    }
    // string # URL to download the file contents as smime with signature
    public function getSmimeSignedUri()
    {
        return isset($this->attributes['smime_signed_uri']) ? $this->attributes['smime_signed_uri'] : null;
    }
    // string # URL to download the encrypted signed smime that is to sent as AS2 body
    public function getEncryptedUri()
    {
        return isset($this->attributes['encrypted_uri']) ? $this->attributes['encrypted_uri'] : null;
    }
    // string # URL to download the http response body
    public function getMdnResponseUri()
    {
        return isset($this->attributes['mdn_response_uri']) ? $this->attributes['mdn_response_uri'] : null;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `as2_partner_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `as2_station_id`, `workspace_id` or `as2_partner_id`. Valid field combinations are `[ as2_station_id, created_at ]`, `[ workspace_id, created_at ]`, `[ as2_partner_id, created_at ]`, `[ workspace_id, as2_station_id ]`, `[ workspace_id, as2_partner_id ]`, `[ workspace_id, as2_station_id, created_at ]` or `[ workspace_id, as2_partner_id, created_at ]`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/as2_outgoing_messages', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2OutgoingMessage((array) $obj, $options);
        }

        return $return_array;
    }
}
