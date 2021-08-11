<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class ActionWebhookFailure
 *
 * @package Files
 */
class ActionWebhookFailure {
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

  // retry Action Webhook Failure
  public function retry($params = []) {
    if (!$this->id) {
      throw new \Files\EmptyPropertyException('The current ActionWebhookFailure object has no $id value');
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

    return Api::sendRequest('/action_webhook_failures/' . @$params['id'] . '/retry', 'POST', $params, $this->options);
  }
}
