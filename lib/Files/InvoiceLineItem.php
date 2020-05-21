<?php

declare(strict_types=1);

namespace Files;

/**
 * Class InvoiceLineItem
 *
 * @package Files
 */
class InvoiceLineItem {
  private $attributes = [];
  private $options = [];

  function __construct($attributes = [], $options = []) {
    foreach ($attributes as $key => $value) {
      $this->attributes[str_replace('?', '', $key)] = $value;
    }

    $this->options = $options;
  }

  public function __get($name) {
    return $this->attributes[$name];
  }

  public function isLoaded() {
    return !!$this->attributes['id'];
  }

  // double # Invoice line item amount
  public function getAmount() {
    return $this->attributes['amount'];
  }

  // date-time # Invoice line item created at date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // string # Invoice line item description
  public function getDescription() {
    return $this->attributes['description'];
  }

  // string # Invoice line item type
  public function getType() {
    return $this->attributes['type'];
  }

  // date-time # Invoice line item service end date/time
  public function getServiceEndAt() {
    return $this->attributes['service_end_at'];
  }

  // date-time # Invoice line item service start date/time
  public function getServiceStartAt() {
    return $this->attributes['service_start_at'];
  }

  // date-time # Invoice line item updated date/time
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }

  // string # Plan name
  public function getPlan() {
    return $this->attributes['plan'];
  }

  // string # Site name
  public function getSite() {
    return $this->attributes['site'];
  }
}
