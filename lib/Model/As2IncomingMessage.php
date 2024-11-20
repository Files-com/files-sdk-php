<?php

declare(strict_types=1);

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
    // int64 # Id of the AS2 Partner.
    public function getId()
    {
        return @$this->attributes['id'];
    }
    // int64 # Id of the AS2 Partner associated with this message.
    public function getAs2PartnerId()
    {
        return @$this->attributes['as2_partner_id'];
    }
    // int64 # Id of the AS2 Station associated with this message.
    public function getAs2StationId()
    {
        return @$this->attributes['as2_station_id'];
    }
    // string # UUID assigned to this message.
    public function getUuid()
    {
        return @$this->attributes['uuid'];
    }
    // string # Content Type header of the incoming message.
    public function getContentType()
    {
        return @$this->attributes['content_type'];
    }
    // object # HTTP Headers sent with this message.
    public function getHttpHeaders()
    {
        return @$this->attributes['http_headers'];
    }
    // string # JSON Structure of the activity log.
    public function getActivityLog()
    {
        return @$this->attributes['activity_log'];
    }
    // string # Result of processing.
    public function getProcessingResult()
    {
        return @$this->attributes['processing_result'];
    }
    // string # Result of processing description.
    public function getProcessingResultDescription()
    {
        return @$this->attributes['processing_result_description'];
    }
    // string # AS2 Message Integrity Check
    public function getMic()
    {
        return @$this->attributes['mic'];
    }
    // string # AS2 Message Integrity Check Algorithm Used
    public function getMicAlgo()
    {
        return @$this->attributes['mic_algo'];
    }
    // string # AS2 TO header of message
    public function getAs2To()
    {
        return @$this->attributes['as2_to'];
    }
    // string # AS2 FROM header of message
    public function getAs2From()
    {
        return @$this->attributes['as2_from'];
    }
    // string # AS2 Message Id
    public function getMessageId()
    {
        return @$this->attributes['message_id'];
    }
    // string # AS2 Subject Header
    public function getSubject()
    {
        return @$this->attributes['subject'];
    }
    // string # Date Header
    public function getDate()
    {
        return @$this->attributes['date'];
    }
    // string # Encrypted Payload Body Size
    public function getBodySize()
    {
        return @$this->attributes['body_size'];
    }
    // string # Filename of the file being received.
    public function getAttachmentFilename()
    {
        return @$this->attributes['attachment_filename'];
    }
    // string # IP Address of the Sender
    public function getIp()
    {
        return @$this->attributes['ip'];
    }
    // date-time # Message creation date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // string # HTTP Response Code sent for this message
    public function getHttpResponseCode()
    {
        return @$this->attributes['http_response_code'];
    }
    // object # HTTP Headers sent for this message.
    public function getHttpResponseHeaders()
    {
        return @$this->attributes['http_response_headers'];
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial
    public function getRecipientSerial()
    {
        return @$this->attributes['recipient_serial'];
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s serial in hex format.
    public function getHexRecipientSerial()
    {
        return @$this->attributes['hex_recipient_serial'];
    }
    // string # Incoming Message Recipient(the Client Cert used to encrypt this message)'s issuer
    public function getRecipientIssuer()
    {
        return @$this->attributes['recipient_issuer'];
    }
    // boolean # Message body received?
    public function getMessageReceived()
    {
        return @$this->attributes['message_received'];
    }
    // boolean # Message decrypted successfully?
    public function getMessageDecrypted()
    {
        return @$this->attributes['message_decrypted'];
    }
    // boolean # Message signature verified?
    public function getMessageSignatureVerified()
    {
        return @$this->attributes['message_signature_verified'];
    }
    // boolean # Message processed successfully?
    public function getMessageProcessingSuccess()
    {
        return @$this->attributes['message_processing_success'];
    }
    // boolean # MDN returned?
    public function getMessageMdnReturned()
    {
        return @$this->attributes['message_mdn_returned'];
    }
    // string # URL to download the encrypted signed smime that is to sent as AS2 body
    public function getEncryptedUri()
    {
        return @$this->attributes['encrypted_uri'];
    }
    // string # URL to download the file contents as smime with signature
    public function getSmimeSignedUri()
    {
        return @$this->attributes['smime_signed_uri'];
    }
    // string # URL to download the file contents encoded as smime
    public function getSmimeUri()
    {
        return @$this->attributes['smime_uri'];
    }
    // string # URL to download the original file contents
    public function getRawUri()
    {
        return @$this->attributes['raw_uri'];
    }
    // string # URL to download the http response body
    public function getMdnResponseUri()
    {
        return @$this->attributes['mdn_response_uri'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at` and `as2_partner_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    //   as2_partner_id - int64 - As2 Partner ID.  If provided, will return message specific to that partner.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['as2_partner_id'] && !is_int(@$params['as2_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_partner_id must be of type int; received ' . gettype(@$params['as2_partner_id']));
        }

        $response = Api::sendRequest('/as2_incoming_messages', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new As2IncomingMessage((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at` and `as2_partner_id`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    //   as2_partner_id - int64 - As2 Partner ID.  If provided, will return message specific to that partner.
    public static function createExport($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['as2_partner_id'] && !is_int(@$params['as2_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$as2_partner_id must be of type int; received ' . gettype(@$params['as2_partner_id']));
        }

        $response = Api::sendRequest('/as2_incoming_messages/create_export', 'POST', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }
}
