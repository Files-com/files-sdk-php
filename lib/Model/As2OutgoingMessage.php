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

  // string # AS2 Message Integrity Check
  public function getMic() {
    return @$this->attributes['mic'];
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

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
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
