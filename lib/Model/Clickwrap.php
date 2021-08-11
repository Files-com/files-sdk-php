<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Clickwrap
 *
 * @package Files
 */
class Clickwrap {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return @$this->attributes[$name];
  }

  public function isLoaded() {
    return !!@$this->attributes['id'];
  }

  // int64 # Clickwrap ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // string # Body text of Clickwrap (supports Markdown formatting).
  public function getBody() {
    return @$this->attributes['body'];
  }

  public function setBody($value) {
    return $this->attributes['body'] = $value;
  }

  // string # Use this Clickwrap for User Registrations?  Note: This only applies to User Registrations where the User is invited to your Files.com site using an E-Mail invitation process where they then set their own password.
  public function getUseWithUsers() {
    return @$this->attributes['use_with_users'];
  }

  public function setUseWithUsers($value) {
    return $this->attributes['use_with_users'] = $value;
  }

  // string # Use this Clickwrap for Bundles?
  public function getUseWithBundles() {
    return @$this->attributes['use_with_bundles'];
  }

  public function setUseWithBundles($value) {
    return $this->attributes['use_with_bundles'] = $value;
  }

  // string # Use this Clickwrap for Inboxes?
  public function getUseWithInboxes() {
    return @$this->attributes['use_with_inboxes'];
  }

  public function setUseWithInboxes($value) {
    return $this->attributes['use_with_inboxes'] = $value;
  }

  // Parameters:
  //   name - string - Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
  //   body - string - Body text of Clickwrap (supports Markdown formatting).
  //   use_with_bundles - string - Use this Clickwrap for Bundles?
  //   use_with_inboxes - string - Use this Clickwrap for Inboxes?
  //   use_with_users - string - Use this Clickwrap for User Registrations?  Note: This only applies to User Registrations where the User is invited to your Files.com site using an E-Mail invitation process where they then set their own password.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Files\EmptyPropertyException('The current Clickwrap object has no $id value');
    }

    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }
    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }
    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \Files\InvalidParameterException('$body must be of type string; received ' . gettype($body));
    }
    if (@$params['use_with_bundles'] && !is_string(@$params['use_with_bundles'])) {
      throw new \Files\InvalidParameterException('$use_with_bundles must be of type string; received ' . gettype($use_with_bundles));
    }
    if (@$params['use_with_inboxes'] && !is_string(@$params['use_with_inboxes'])) {
      throw new \Files\InvalidParameterException('$use_with_inboxes must be of type string; received ' . gettype($use_with_inboxes));
    }
    if (@$params['use_with_users'] && !is_string(@$params['use_with_users'])) {
      throw new \Files\InvalidParameterException('$use_with_users must be of type string; received ' . gettype($use_with_users));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    return Api::sendRequest('/clickwraps/' . @$params['id'] . '', 'PATCH', $params, $this->options);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Files\EmptyPropertyException('The current Clickwrap object has no $id value');
    }

    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype($id));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: id');
      }
    }

    return Api::sendRequest('/clickwraps/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   cursor - string - Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype($per_page));
    }

    $response = Api::sendRequest('/clickwraps', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Clickwrap((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Clickwrap ID.
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

    $response = Api::sendRequest('/clickwraps/' . @$params['id'] . '', 'GET', $params, $options);

    return new Clickwrap((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   name - string - Name of the Clickwrap agreement (used when selecting from multiple Clickwrap agreements.)
  //   body - string - Body text of Clickwrap (supports Markdown formatting).
  //   use_with_bundles - string - Use this Clickwrap for Bundles?
  //   use_with_inboxes - string - Use this Clickwrap for Inboxes?
  //   use_with_users - string - Use this Clickwrap for User Registrations?  Note: This only applies to User Registrations where the User is invited to your Files.com site using an E-Mail invitation process where they then set their own password.
  public static function create($params = [], $options = []) {
    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype($name));
    }

    if (@$params['body'] && !is_string(@$params['body'])) {
      throw new \Files\InvalidParameterException('$body must be of type string; received ' . gettype($body));
    }

    if (@$params['use_with_bundles'] && !is_string(@$params['use_with_bundles'])) {
      throw new \Files\InvalidParameterException('$use_with_bundles must be of type string; received ' . gettype($use_with_bundles));
    }

    if (@$params['use_with_inboxes'] && !is_string(@$params['use_with_inboxes'])) {
      throw new \Files\InvalidParameterException('$use_with_inboxes must be of type string; received ' . gettype($use_with_inboxes));
    }

    if (@$params['use_with_users'] && !is_string(@$params['use_with_users'])) {
      throw new \Files\InvalidParameterException('$use_with_users must be of type string; received ' . gettype($use_with_users));
    }

    $response = Api::sendRequest('/clickwraps', 'POST', $params, $options);

    return new Clickwrap((array)(@$response->data ?: []), $options);
  }
}
