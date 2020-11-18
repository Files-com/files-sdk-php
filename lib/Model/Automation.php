<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Automation
 *
 * @package Files
 */
class Automation {
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

  // int64 # Automation ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // string # Automation type
  public function getAutomation() {
    return @$this->attributes['automation'];
  }

  public function setAutomation($value) {
    return $this->attributes['automation'] = $value;
  }

  // string # Source Path
  public function getSource() {
    return @$this->attributes['source'];
  }

  public function setSource($value) {
    return $this->attributes['source'] = $value;
  }

  // string # Destination Path
  public function getDestination() {
    return @$this->attributes['destination'];
  }

  public function setDestination($value) {
    return $this->attributes['destination'] = $value;
  }

  // string # If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
  public function getDestinationReplaceFrom() {
    return @$this->attributes['destination_replace_from'];
  }

  public function setDestinationReplaceFrom($value) {
    return $this->attributes['destination_replace_from'] = $value;
  }

  // string # If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
  public function getDestinationReplaceTo() {
    return @$this->attributes['destination_replace_to'];
  }

  public function setDestinationReplaceTo($value) {
    return $this->attributes['destination_replace_to'] = $value;
  }

  // string # How often to run this automation?  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  public function getInterval() {
    return @$this->attributes['interval'];
  }

  public function setInterval($value) {
    return $this->attributes['interval'] = $value;
  }

  // string # Date this automation will next run.
  public function getNextProcessOn() {
    return @$this->attributes['next_process_on'];
  }

  public function setNextProcessOn($value) {
    return $this->attributes['next_process_on'] = $value;
  }

  // string # Path on which this Automation runs.  Supports globs. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // boolean # Does this automation run in real time?  This is a read-only property based on automation type.
  public function getRealtime() {
    return @$this->attributes['realtime'];
  }

  public function setRealtime($value) {
    return $this->attributes['realtime'] = $value;
  }

  // int64 # User ID of the Automation's creator.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // array # IDs of Users for the Automation (i.e. who to Request File from)
  public function getUserIds() {
    return @$this->attributes['user_ids'];
  }

  public function setUserIds($value) {
    return $this->attributes['user_ids'] = $value;
  }

  // array # IDs of Groups for the Automation (i.e. who to Request File from)
  public function getGroupIds() {
    return @$this->attributes['group_ids'];
  }

  public function setGroupIds($value) {
    return $this->attributes['group_ids'] = $value;
  }

  // string # How this automation is triggered to run. One of: `realtime` or `custom_schedule`.
  public function getTrigger() {
    return @$this->attributes['trigger'];
  }

  public function setTrigger($value) {
    return $this->attributes['trigger'] = $value;
  }

  // object # Custom schedule description for when the automation should be run.
  public function getSchedule() {
    return @$this->attributes['schedule'];
  }

  public function setSchedule($value) {
    return $this->attributes['schedule'] = $value;
  }

  // Parameters:
  //   automation (required) - string - Automation type
  //   source - string - Source Path
  //   destination - string - Destination Path
  //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
  //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
  //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  //   path - string - Path on which this Automation runs.  Supports globs.
  //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   schedule - object - Custom schedule for running this automation.
  //   trigger - string - How this automation is triggered to run. One of: `realtime` or `custom_schedule`.
  public function update($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no id');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }
    if (@$params['automation'] && !is_string(@$params['automation'])) {
      throw new \InvalidArgumentException('Bad parameter: $automation must be of type string; received ' . gettype($automation));
    }
    if (@$params['source'] && !is_string(@$params['source'])) {
      throw new \InvalidArgumentException('Bad parameter: $source must be of type string; received ' . gettype($source));
    }
    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination must be of type string; received ' . gettype($destination));
    }
    if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination_replace_from must be of type string; received ' . gettype($destination_replace_from));
    }
    if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination_replace_to must be of type string; received ' . gettype($destination_replace_to));
    }
    if (@$params['interval'] && !is_string(@$params['interval'])) {
      throw new \InvalidArgumentException('Bad parameter: $interval must be of type string; received ' . gettype($interval));
    }
    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }
    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_ids must be of type string; received ' . gettype($user_ids));
    }
    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_ids must be of type string; received ' . gettype($group_ids));
    }
    if (@$params['trigger'] && !is_string(@$params['trigger'])) {
      throw new \InvalidArgumentException('Bad parameter: $trigger must be of type string; received ' . gettype($trigger));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    if (!@$params['automation']) {
      if ($this->automation) {
        $params['automation'] = @$this->automation;
      } else {
        throw new \Error('Parameter missing: automation');
      }
    }

    return Api::sendRequest('/automations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
  }

  public function delete($params = []) {
    if (!$this->id) {
      throw new \Error('Current object has no id');
    }

    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $this->id;

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    if (!@$params['id']) {
      if ($this->id) {
        $params['id'] = @$this->id;
      } else {
        throw new \Error('Parameter missing: id');
      }
    }

    return Api::sendRequest('/automations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   sort_by - object - If set, sort records by the specified field in either 'asc' or 'desc' direction (e.g. sort_by[last_login_at]=desc). Valid fields are `site_id` and `automation`.
  //   filter - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `automation`.
  //   filter_gt - object - If set, return records where the specifiied field is greater than the supplied value. Valid fields are `automation`.
  //   filter_gteq - object - If set, return records where the specifiied field is greater than or equal to the supplied value. Valid fields are `automation`.
  //   filter_like - object - If set, return records where the specifiied field is equal to the supplied value. Valid fields are `automation`.
  //   filter_lt - object - If set, return records where the specifiied field is less than the supplied value. Valid fields are `automation`.
  //   filter_lteq - object - If set, return records where the specifiied field is less than or equal to the supplied value. Valid fields are `automation`.
  //   automation - string - DEPRECATED: Type of automation to filter by. Use `filter[automation]` instead.
  public static function list($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \InvalidArgumentException('Bad parameter: $cursor must be of type string; received ' . gettype($cursor));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \InvalidArgumentException('Bad parameter: $per_page must be of type int; received ' . gettype($per_page));
    }

    if (@$params['automation'] && !is_string(@$params['automation'])) {
      throw new \InvalidArgumentException('Bad parameter: $automation must be of type string; received ' . gettype($automation));
    }

    $response = Api::sendRequest('/automations', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Automation((array)$obj, $options);
    }

    return $return_array;
  }

  public static function all($params = [], $options = []) {
    return self::list($params, $options);
  }

  // Parameters:
  //   id (required) - int64 - Automation ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \InvalidArgumentException('Bad parameter: $params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Error('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \InvalidArgumentException('Bad parameter: $id must be of type int; received ' . gettype($id));
    }

    $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'GET', $params, $options);

    return new Automation((array)(@$response->data ?: []), $options);
  }

  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }

  // Parameters:
  //   automation (required) - string - Automation type
  //   source - string - Source Path
  //   destination - string - Destination Path
  //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
  //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
  //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  //   path - string - Path on which this Automation runs.  Supports globs.
  //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   schedule - object - Custom schedule for running this automation.
  //   trigger - string - How this automation is triggered to run. One of: `realtime` or `custom_schedule`.
  public static function create($params = [], $options = []) {
    if (!@$params['automation']) {
      throw new \Error('Parameter missing: automation');
    }

    if (@$params['automation'] && !is_string(@$params['automation'])) {
      throw new \InvalidArgumentException('Bad parameter: $automation must be of type string; received ' . gettype($automation));
    }

    if (@$params['source'] && !is_string(@$params['source'])) {
      throw new \InvalidArgumentException('Bad parameter: $source must be of type string; received ' . gettype($source));
    }

    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination must be of type string; received ' . gettype($destination));
    }

    if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination_replace_from must be of type string; received ' . gettype($destination_replace_from));
    }

    if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
      throw new \InvalidArgumentException('Bad parameter: $destination_replace_to must be of type string; received ' . gettype($destination_replace_to));
    }

    if (@$params['interval'] && !is_string(@$params['interval'])) {
      throw new \InvalidArgumentException('Bad parameter: $interval must be of type string; received ' . gettype($interval));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \InvalidArgumentException('Bad parameter: $path must be of type string; received ' . gettype($path));
    }

    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $user_ids must be of type string; received ' . gettype($user_ids));
    }

    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \InvalidArgumentException('Bad parameter: $group_ids must be of type string; received ' . gettype($group_ids));
    }

    if (@$params['trigger'] && !is_string(@$params['trigger'])) {
      throw new \InvalidArgumentException('Bad parameter: $trigger must be of type string; received ' . gettype($trigger));
    }

    $response = Api::sendRequest('/automations', 'POST', $params, $options);

    return new Automation((array)(@$response->data ?: []), $options);
  }
}
