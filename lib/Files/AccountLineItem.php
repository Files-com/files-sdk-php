<?php

declare(strict_types=1);

namespace Files;

/**
 * Class AccountLineItem
 *
 * @package Files
 */
class AccountLineItem {
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

  // int64 # Line item Id
  public function getId() {
    return $this->attributes['id'];
  }

  // double # Line item amount
  public function getAmount() {
    return $this->attributes['amount'];
  }

  // double # Line item balance
  public function getBalance() {
    return $this->attributes['balance'];
  }

  // date-time # Line item created at
  public function getCreatedAt() {
    return $this->attributes['created_at'];
  }

  // string # Line item currency
  public function getCurrency() {
    return $this->attributes['currency'];
  }

  // string # Line item download uri
  public function getDownloadUri() {
    return $this->attributes['download_uri'];
  }

  // array # Associated invoice line items
  public function getInvoiceLineItems() {
    return $this->attributes['invoice_line_items'];
  }

  // string # Line item payment method
  public function getMethod() {
    return $this->attributes['method'];
  }

  // array # Associated payment line items
  public function getPaymentLineItems() {
    return $this->attributes['payment_line_items'];
  }

  // date-time # Date/time payment was reversed if applicable
  public function getPaymentReversedAt() {
    return $this->attributes['payment_reversed_at'];
  }

  // string # Type of payment if applicable
  public function getPaymentType() {
    return $this->attributes['payment_type'];
  }

  // string # Site name this line item is for
  public function getSiteName() {
    return $this->attributes['site_name'];
  }

  // string # Type of line item, either payment or invoice
  public function getType() {
    return $this->attributes['type'];
  }

  // date-time # Line item updated at
  public function getUpdatedAt() {
    return $this->attributes['updated_at'];
  }
}
