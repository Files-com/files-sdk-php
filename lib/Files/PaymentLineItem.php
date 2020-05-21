<?php

declare(strict_types=1);

namespace Files;

/**
 * Class PaymentLineItem
 *
 * @package Files
 */
class PaymentLineItem {
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

  // double # Payment line item amount
  public function getAmount() {
    return $this->attributes['amount'];
  }

  // date-time # Payment line item created at date/time
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // int64 # Invoice ID
  public function getInvoiceId() {
    return $this->attributes['invoice_id'];
  }

  // int64 # Payment ID
  public function getPaymentId() {
    return $this->attributes['payment_id'];
  }

  // date-time # Payment line item updated at date/time
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }
}
