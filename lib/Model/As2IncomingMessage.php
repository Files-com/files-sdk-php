<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2IncomingMessage
 *
 * @package Files
 */
class As2IncomingMessage
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
    // int64 # ID of this AS2 incoming message record.
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }
    // int64 # ID of the incoming message record this message was replayed from. Empty for messages received directly from a trading partner.
    public function getReplayedFromMessageId()
    {
        return isset($this->attributes['replayed_from_message_id']) ? $this->attributes['replayed_from_message_id'] : null;
    }
    // int64 # ID of the Workspace associated with this AS2 Incoming Message.
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
    // string # Content Type header of the incoming message.
    public function getContentType()
    {
        return isset($this->attributes['content_type']) ? $this->attributes['content_type'] : null;
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
    // string # AS2 Message Integrity Check
    public function getMic()
    {
        return isset($this->attributes['mic']) ? $this->attributes['mic'] : null;
    }
    // string # AS2 Message Integrity Check Algorithm Used
    public function getMicAlgo()
    {
        return isset($this->attributes['mic_algo']) ? $this->attributes['mic_algo'] : null;
    }
    // string # AS2 TO header of message
    public function getAs2To()
    {
        return isset($this->attributes['as2_to']) ? $this->attributes['as2_to'] : null;
    }
    // string # AS2 FROM header of message
    public function getAs2From()
    {
        return isset($this->attributes['as2_from']) ? $this->attributes['as2_from'] : null;
    }
    // string # AS2 Message Id
    public function getMessageId()
    {
        return isset($this->attributes['message_id']) ? $this->attributes['message_id'] : null;
    }
    // string # AS2 Subject Header
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }
    // string # Date Header
    public function getDate()
    {
        return isset($this->attributes['date']) ? $this->attributes['date'] : null;
    }
    // string # Encrypted Payload Body Size
    public function getBodySize()
    {
        return isset($this->attributes['body_size']) ? $this->attributes['body_size'] : null;
    }
    // string # Filename of the file being received.
    public function getAttachmentFilename()
    {
        return isset($this->attributes['attachment_filename']) ? $this->attributes['attachment_filename'] : null;
    }
    // string # IP Address of the Sender
    public function getIp()
    {
        return isset($this->attributes['ip']) ? $this->attributes['ip'] : null;
    }
    // date-time # Message creation date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // string # HTTP Response Code sent for this message
    public function getHttpResponseCode()
    {
        return isset($this->attributes['http_response_code']) ? $this->attributes['http_response_code'] : null;
    }
    // object # HTTP Headers sent for this message.
    public function getHttpResponseHeaders()
    {
        return isset($this->attributes['http_response_headers']) ? $this->attributes['http_response_headers'] : null;
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial
    public function getRecipientSerial()
    {
        return isset($this->attributes['recipient_serial']) ? $this->attributes['recipient_serial'] : null;
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial in hex format.
    public function getHexRecipientSerial()
    {
        return isset($this->attributes['hex_recipient_serial']) ? $this->attributes['hex_recipient_serial'] : null;
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s issuer
    public function getRecipientIssuer()
    {
        return isset($this->attributes['recipient_issuer']) ? $this->attributes['recipient_issuer'] : null;
    }
    // boolean # Message body received?
    public function getMessageReceived()
    {
        return isset($this->attributes['message_received']) ? $this->attributes['message_received'] : null;
    }
    // boolean # Message decrypted successfully?
    public function getMessageDecrypted()
    {
        return isset($this->attributes['message_decrypted']) ? $this->attributes['message_decrypted'] : null;
    }
    // boolean # Message signature verified?
    public function getMessageSignatureVerified()
    {
        return isset($this->attributes['message_signature_verified']) ? $this->attributes['message_signature_verified'] : null;
    }
    // boolean # Message processed successfully?
    public function getMessageProcessingSuccess()
    {
        return isset($this->attributes['message_processing_success']) ? $this->attributes['message_processing_success'] : null;
    }
    // boolean # MDN returned?
    public function getMessageMdnReturned()
    {
        return isset($this->attributes['message_mdn_returned']) ? $this->attributes['message_mdn_returned'] : null;
    }
    // string # URL to download the encrypted signed smime that is to sent as AS2 body
    public function getEncryptedUri()
    {
        return isset($this->attributes['encrypted_uri']) ? $this->attributes['encrypted_uri'] : null;
    }
    // string # URL to download the file contents as smime with signature
    public function getSmimeSignedUri()
    {
        return isset($this->attributes['smime_signed_uri']) ? $this->attributes['smime_signed_uri'] : null;
    }
    // string # URL to download the file contents encoded as smime
    public function getSmimeUri()
    {
        return isset($this->attributes['smime_uri']) ? $this->attributes['smime_uri'] : null;
    }
    // string # URL to download the original file contents
    public function getRawUri()
    {
        return isset($this->attributes['raw_uri']) ? $this->attributes['raw_uri'] : null;
    }
    // string # URL to download the http response body
    public function getMdnResponseUri()
    {
        return isset($this->attributes['mdn_response_uri']) ? $this->attributes['mdn_response_uri'] : null;
    }

    // Replay AS2 Incoming Message
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

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `created_at` or `as2_partner_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `as2_station_id`, `workspace_id`, `replayed_from_message_id` or `as2_partner_id`. Valid field combinations are `[ as2_station_id, created_at ]`, `[ workspace_id, created_at ]`, `[ replayed_from_message_id, created_at ]`, `[ as2_partner_id, created_at ]`, `[ workspace_id, as2_station_id ]`, `[ workspace_id, replayed_from_message_id ]`, `[ workspace_id, as2_partner_id ]`, `[ workspace_id, as2_station_id, created_at ]`, `[ workspace_id, replayed_from_message_id, created_at ]` or `[ workspace_id, as2_partner_id, created_at ]`.
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

        $response = Api::sendRequest('/as2_incoming_messages', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2IncomingMessage((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - As2 Incoming Message ID.
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
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }
}
