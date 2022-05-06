<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2OutgoingMessage
 *
 * @package Files
 */
class As2OutgoingMessage {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __set($name, $value) {
    $this->attributes[$name] = $value;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Id of the AS2 Partner.
  public function getId() {
    return @$this->attributes['id'];
  }

  // int64 # Id of the AS2 Partner associated with this message.
  public function getAs2PartnerId() {
    return @$this->attributes['as2_partner_id'];
  }

  // int64 # Id of the AS2 Station associated with this message.
  public function getAs2StationId() {
    return @$this->attributes['as2_station_id'];
  }

  // string # UUID assigned to this message.
  public function getUuid() {
    return @$this->attributes['uuid'];
  }

  // object # HTTP Headers sent with this message.
  public function getHttpHeaders() {
    return @$this->attributes['http_headers'];
  }

  // string # JSON Structure of the activity log.
  public function getActivityLog() {
    return @$this->attributes['activity_log'];
  }

  // string # Result of processing.
  public function getProcessingResult() {
    return @$this->attributes['processing_result'];
  }

  // string # Result of processing description.
  public function getProcessingResultDescription() {
    return @$this->attributes['processing_result_description'];
  }

  // string # AS2 Message Integrity Check SHA1
  public function getMic() {
    return @$this->attributes['mic'];
  }

  // string # AS2 Message Integrity Check SHA256
  public function getMicSha256() {
    return @$this->attributes['mic_sha_256'];
  }

  // string # AS2 TO
  public function getAs2To() {
    return @$this->attributes['as2_to'];
  }

  // string # AS2 FROM
  public function getAs2From() {
    return @$this->attributes['as2_from'];
  }

  // string # Date Header
  public function getDate() {
    return @$this->attributes['date'];
  }

  // string # AS2 Message Id
  public function getMessageId() {
    return @$this->attributes['message_id'];
  }

  // string # Encrypted Payload Body Size
  public function getBodySize() {
    return @$this->attributes['body_size'];
  }

  // string # Filename of the file being sent.
  public function getAttachmentFilename() {
    return @$this->attributes['attachment_filename'];
  }

  // date-time # Message creation date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // string # HTTP Response Code received for this message
  public function getHttpResponseCode() {
    return @$this->attributes['http_response_code'];
  }

  // object # HTTP Headers received for this message.
  public function getHttpResponseHeaders() {
    return @$this->attributes['http_response_headers'];
  }

  // double # HTTP transmission duration in seceonds
  public function getHttpTransmissionDuration() {
    return @$this->attributes['http_transmission_duration'];
  }

  // boolean # Did the partner give a response body?
  public function getMdnReceived() {
    return @$this->attributes['mdn_received'];
  }

  // boolean # Is the response in MDN format?
  public function getMdnValid() {
    return @$this->attributes['mdn_valid'];
  }

  // boolean # MDN signature verified?
  public function getMdnSignatureVerified() {
    return @$this->attributes['mdn_signature_verified'];
  }

  // boolean # MDN message id matched?
  public function getMdnMessageIdMatched() {
    return @$this->attributes['mdn_message_id_matched'];
  }

  // boolean # MDN MIC matched?
  public function getMdnMicMatched() {
    return @$this->attributes['mdn_mic_matched'];
  }

  // boolean # MDN disposition indicate a successful processing?
  public function getMdnProcessingSuccess() {
    return @$this->attributes['mdn_processing_success'];
  }

  // string # URL to download the original file contents
  public function getRawUri() {
    return @$this->attributes['raw_uri'];
  }

  // string # URL to download the file contents encoded as smime
  public function getSmimeUri() {
    return @$this->attributes['smime_uri'];
  }

  // string # URL to download the file contents as smime with signature
  public function getSmimeSignedUri() {
    return @$this->attributes['smime_signed_uri'];
  }

  // string # URL to download the encrypted signed smime that is to sent as AS2 body
  public function getEncryptedUri() {
    return @$this->attributes['encrypted_uri'];
  }

  // string # URL to download the http response body
  public function getMdnResponseUri() {
    return @$this->attributes['mdn_response_uri'];
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `created_at` and `as2_partner_id`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal to the supplied value. Valid fields are `created_at`.
  //   filter_like - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal to the supplied value. Valid fields are `created_at`.
  //   as2_partner_id - int64 - As2 Partner ID.  If provided, will return message specific to that partner.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['as2_partner_id'] && !is_int(@$params['as2_partner_id'])) {
      throw new \Files\InvalidParameterException('$as2_partner_id must be of type int; received ' . gettype($as2_partner_id));
    }

    $response = Api::sendRequest('/as2_outgoing_messages', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new As2OutgoingMessage((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
