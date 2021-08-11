<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileAction
 *
 * @package Files
 */
class FileAction {
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
    return !!@$this->attributes['path'];
  }

  // string # Status of file operation. Possible values: completed, enqueued.
  public function getStatus() {
    return @$this->attributes['status'];
  }

  // int64 # If status is enqueued, this is the id of the FileMigration to check for status updates.
  public function getFileMigrationId() {
    return @$this->attributes['file_migration_id'];
  }
}
