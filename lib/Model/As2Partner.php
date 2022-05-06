<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class As2Partner
 *
 * @package Files
 */
class As2Partner {
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

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // int64 # Id of the AS2 Station associated with this partner.
  public function getAs2StationId() {
    return @$this->attributes['as2_station_id'];
  }

  public function setAs2StationId($value) {
    return $this->attributes['as2_station_id'] = $value;
  }

  // string # The partner's formal AS2 name.
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # Public URI for sending AS2 message to.
  public function getUri() {
    return @$this->attributes['uri'];
  }

  public function setUri($value) {
    return $this->attributes['uri'] = $value;
  }

  // string # Remote server certificate security setting
  public function getServerCertificate() {
    return @$this->attributes['server_certificate'];
  }

  public function setServerCertificate($value) {
    return $this->attributes['server_certificate'] = $value;
  }

  // string # Serial of public certificate used for message security in hex format.
  public function getHexPublicCertificateSerial() {
    return @$this->attributes['hex_public_certificate_serial'];
  }

  public function setHexPublicCertificateSerial($value) {
    return $this->attributes['hex_public_certificate_serial'] = $value;
  }

  // string # MD5 hash of public certificate used for message security.
  public function getPublicCertificateMd5() {
    return @$this->attributes['public_certificate_md5'];
  }

  public function setPublicCertificateMd5($value) {
    return $this->attributes['public_certificate_md5'] = $value;
  }

  // string # Subject of public certificate used for message security.
  public function getPublicCertificateSubjec() {
    return @$this->attributes['public_certificate_subjec'];
  }

  public function setPublicCertificateSubjec($value) {
    return $this->attributes['public_certificate_subjec'] = $value;
  }

  // string # Issuer of public certificate used for message security.
  public function getPublicCertificateIssuer() {
    return @$this->attributes['public_certificate_issuer'];
  }

  public function setPublicCertificateIssuer($value) {
    return $this->attributes['public_certificate_issuer'] = $value;
  }

  // string # Serial of public certificate used for message security.
  public function getPublicCertificateSerial() {
    return @$this->attributes['public_certificate_serial'];
  }

  public function setPublicCertificateSerial($value) {
    return $this->attributes['public_certificate_serial'] = $value;
  }

  // string # Not before value of public certificate used for message security.
  public function getPublicCertificateNotBefore() {
    return @$this->attributes['public_certificate_not_before'];
  }

  public function setPublicCertificateNotBefore($value) {
    return $this->attributes['public_certificate_not_before'] = $value;
  }

  // string # Not after value of public certificate used for message security.
  public function getPublicCertificateNotAfter() {
    return @$this->attributes['public_certificate_not_after'];
  }

  public function setPublicCertificateNotAfter($value) {
    return $this->attributes['public_certificate_not_after'] = $value;
  }

  // string
  public function getPublicCertificate() {
    return @$this->attributes['public_certificate'];
  }

  public function setPublicCertificate($value) {
    return $this->attributes['public_certificate'] = $value;
  }

  // Parameters:
  //   name - string - AS2 Name
  //   uri - string - URL base for AS2 responses
  //   server_certificate - string - Remote server certificate security setting
  //   public_certificate - string
  public function update($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['uri'] && !is_string(@$params['uri'])) {
      throw new \Files\InvalidParameterException('$uri must be of type string; received ' . gettype($uri));
    }

    if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
      throw new \Files\InvalidParameterException('$server_certificate must be of type string; received ' . gettype($server_certificate));
    }

    if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
      throw new \Files\InvalidParameterException('$public_certificate must be of type string; received ' . gettype($public_certificate));
    }

    $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['id']) {
      if (@$this->id) {
        $params['id'] = $this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  public function save() {
      if (@$this->attributes['id']) {
        return $this->update($this->attributes);
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }

  // Parameters:
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/as2_partners', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new As2Partner((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - As2 Partner ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/as2_partners/' . @$params['id'] . '', 'GET', $params, $options);

    return new As2Partner((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   name (required) - string - AS2 Name
  //   uri (required) - string - URL base for AS2 responses
  //   public_certificate (required) - string
  //   as2_station_id (required) - int64 - Id of As2Station for this partner
  //   server_certificate - string - Remote server certificate security setting
  public static function create($params = [], $options = []) {
    if (!@$params['name']) {
      throw new \Files\MissingParameterException('Parameter missing: name');
    }

    if (!@$params['uri']) {
      throw new \Files\MissingParameterException('Parameter missing: uri');
    }

    if (!@$params['public_certificate']) {
      throw new \Files\MissingParameterException('Parameter missing: public_certificate');
    }

    if (!@$params['as2_station_id']) {
      throw new \Files\MissingParameterException('Parameter missing: as2_station_id');
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['uri'] && !is_string(@$params['uri'])) {
      throw new \Files\InvalidParameterException('$uri must be of type string; received ' . gettype($uri));
    }

    if (@$params['public_certificate'] && !is_string(@$params['public_certificate'])) {
      throw new \Files\InvalidParameterException('$public_certificate must be of type string; received ' . gettype($public_certificate));
    }

    if (@$params['as2_station_id'] && !is_int(@$params['as2_station_id'])) {
      throw new \Files\InvalidParameterException('$as2_station_id must be of type int; received ' . gettype($as2_station_id));
    }

    if (@$params['server_certificate'] && !is_string(@$params['server_certificate'])) {
      throw new \Files\InvalidParameterException('$server_certificate must be of type string; received ' . gettype($server_certificate));
    }

    $response = Api::sendRequest('/as2_partners', 'POST', $params, $options);

    return new As2Partner((array)(@$response->data ?: []), $options);
  }
}
