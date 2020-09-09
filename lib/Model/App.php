<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class App
 *
 * @package Files
 */
class App {
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

  // string # Name of the App
  public function getName() {
    return @$this->attributes['name'];
  }

  // string # Long form description of the App
  public function getExtendedDescription() {
    return @$this->attributes['extended_description'];
  }

  // string # Collection of named links to documentation
  public function getDocumentationLinks() {
    return @$this->attributes['documentation_links'];
  }

  // string # App icon
  public function getIconUrl() {
    return @$this->attributes['icon_url'];
  }

  // string # Full size logo for the App
  public function getLogoUrl() {
    return @$this->attributes['logo_url'];
  }

  // string # Screenshots of the App
  public function getScreenshotListUrls() {
    return @$this->attributes['screenshot_list_urls'];
  }

  // string # Logo thumbnail for the App
  public function getLogoThumbnailUrl() {
    return @$this->attributes['logo_thumbnail_url'];
  }

  // string # Associated SSO Strategy type, if any
  public function getSsoStrategyType() {
    return @$this->attributes['sso_strategy_type'];
  }

  // string # Associated Remote Server type, if any
  public function getRemoteServerType() {
    return @$this->attributes['remote_server_type'];
  }

  // string # Associated Folder Behavior type, if any
  public function getFolderBehaviorType() {
    return @$this->attributes['folder_behavior_type'];
  }

  // string # Link to external homepage
  public function getExternalHomepageUrl() {
    return @$this->attributes['external_homepage_url'];
  }

  // string # The type of the App
  public function getAppType() {
    return @$this->attributes['app_type'];
  }

  // boolean # Is featured on the App listing?
  public function getFeatured() {
    return @$this->attributes['featured'];
  }

  // Parameters:
  //   page - int64 - Current page number.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   action - string - Deprecated: If set to `count` returns a count of matching records rather than the records themselves.
  //   cursor - string - Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header.
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `name` and `app_type`.
  //   filter - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `name` and `app_type`.
  //   filter_gt - object - If set, return records where the specifiied field is greater than the supplied value. Valid fields are `name` and `app_type`.
  //   filter_gteq - object - If set, return records where the specifiied field is greater than or equal to the supplied value. Valid fields are `name` and `app_type`.
  //   filter_like - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `name` and `app_type`.
  //   filter_lt - object - If set, return records where the specifiied field is less than the supplied value. Valid fields are `name` and `app_type`.
  //   filter_lteq - object - If set, return records where the specifiied field is less than or equal to the supplied value. Valid fields are `name` and `app_type`.
  public static function list($params = [], $options = []) {
    if (@$params['page'] && !is_int(@$params['page'])) {
      throw new \InvalidArgumentException('Bad parameter: $page must be of type int; received ' . gettype($page));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['action'] && !is_string(@$params['action'])) {
      throw new \InvalidArgumentException('Bad parameter: $action must be of type string; received ' . gettype($action));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    $response = Api::sendRequest('/apps', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new App((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }
}
