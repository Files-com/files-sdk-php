<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
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
  private static $static_mapped_functions = [
    'list' => 'all',
  ];

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

  public static function __callStatic($name, $arguments) {
    if(in_array($name, array_keys(self::$static_mapped_functions))){
      $method = self::$static_mapped_functions[$name];
      if (method_exists(__CLASS__, $method)){
        return @self::$method($arguments);
      }
    }
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

  // boolean # Indicates if the automation has been deleted.
  public function getDeleted() {
    return @$this->attributes['deleted'];
  }

  public function setDeleted($value) {
    return $this->attributes['deleted'] = $value;
  }

  // boolean # If true, this automation will not run.
  public function getDisabled() {
    return @$this->attributes['disabled'];
  }

  public function setDisabled($value) {
    return $this->attributes['disabled'] = $value;
  }

  // string # How this automation is triggered to run.
  public function getTrigger() {
    return @$this->attributes['trigger'];
  }

  public function setTrigger($value) {
    return $this->attributes['trigger'] = $value;
  }

  // string # If trigger is `daily`, this specifies how often to run this automation.  One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  public function getInterval() {
    return @$this->attributes['interval'];
  }

  public function setInterval($value) {
    return $this->attributes['interval'] = $value;
  }

  // date-time # Time when automation was last modified. Does not change for name or description updates.
  public function getLastModifiedAt() {
    return @$this->attributes['last_modified_at'];
  }

  public function setLastModifiedAt($value) {
    return $this->attributes['last_modified_at'] = $value;
  }

  // string # Name for this automation.
  public function getName() {
    return @$this->attributes['name'];
  }

  public function setName($value) {
    return $this->attributes['name'] = $value;
  }

  // object # If trigger is `custom_schedule`, Custom schedule description for when the automation should be run.
  public function getSchedule() {
    return @$this->attributes['schedule'];
  }

  public function setSchedule($value) {
    return $this->attributes['schedule'] = $value;
  }

  // string # Source Path
  public function getSource() {
    return @$this->attributes['source'];
  }

  public function setSource($value) {
    return $this->attributes['source'] = $value;
  }

  // array # Destination Paths
  public function getDestinations() {
    return @$this->attributes['destinations'];
  }

  public function setDestinations($value) {
    return $this->attributes['destinations'] = $value;
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

  // string # Description for the this Automation.
  public function getDescription() {
    return @$this->attributes['description'];
  }

  public function setDescription($value) {
    return $this->attributes['description'] = $value;
  }

  // int64 # If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
  public function getRecurringDay() {
    return @$this->attributes['recurring_day'];
  }

  public function setRecurringDay($value) {
    return $this->attributes['recurring_day'] = $value;
  }

  // string # Path on which this Automation runs.  Supports globs, except on remote mounts. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // int64 # User ID of the Automation's creator.
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // array # IDs of remote sync folder behaviors to run by this Automation
  public function getSyncIds() {
    return @$this->attributes['sync_ids'];
  }

  public function setSyncIds($value) {
    return $this->attributes['sync_ids'] = $value;
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

  // string # If trigger is `webhook`, this is the URL of the webhook to trigger the Automation.
  public function getWebhookUrl() {
    return @$this->attributes['webhook_url'];
  }

  public function setWebhookUrl($value) {
    return $this->attributes['webhook_url'] = $value;
  }

  // array # If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, copy
  public function getTriggerActions() {
    return @$this->attributes['trigger_actions'];
  }

  public function setTriggerActions($value) {
    return $this->attributes['trigger_actions'] = $value;
  }

  // object # A Hash of attributes specific to the automation type.
  public function getValue() {
    return @$this->attributes['value'];
  }

  public function setValue($value) {
    return $this->attributes['value'] = $value;
  }

  // string # DEPRECATED: Destination Path. Use `destinations` instead.
  public function getDestination() {
    return @$this->attributes['destination'];
  }

  public function setDestination($value) {
    return $this->attributes['destination'] = $value;
  }

  // Manually run automation
  public function manualRun($params = []) {
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/automations/' . @$params['id'] . '/manual_run', 'POST', $params, $this->options);
    return;
  }

  // Parameters:
  //   source - string - Source Path
  //   destination - string - DEPRECATED: Destination Path. Use `destinations` instead.
  //   destinations - array(string) - A list of String destination paths or Hash of folder_path and optional file_path.
  //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
  //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
  //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  //   path - string - Path on which this Automation runs.  Supports globs.
  //   sync_ids - string - A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   schedule - object - Custom schedule for running this automation.
  //   description - string - Description for the this Automation.
  //   disabled - boolean - If true, this automation will not run.
  //   name - string - Name for this automation.
  //   trigger - string - How this automation is triggered to run.
  //   trigger_actions - array(string) - If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, copy
  //   value - object - A Hash of attributes specific to the automation type.
  //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
  //   automation - string - Automation type
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    if (@$params['source'] && !is_string(@$params['source'])) {
      throw new \Files\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
    }

    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \Files\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
    }

    if (@$params['destinations'] && !is_array(@$params['destinations'])) {
      throw new \Files\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
    }

    if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
      throw new \Files\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
    }

    if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
      throw new \Files\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
    }

    if (@$params['interval'] && !is_string(@$params['interval'])) {
      throw new \Files\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['sync_ids'] && !is_string(@$params['sync_ids'])) {
      throw new \Files\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
    }

    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \Files\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
    }

    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \Files\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['trigger'] && !is_string(@$params['trigger'])) {
      throw new \Files\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
    }

    if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
      throw new \Files\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
    }

    if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
      throw new \Files\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
    }

    if (@$params['automation'] && !is_string(@$params['automation'])) {
      throw new \Files\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
    }

    $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'PATCH', $params, $this->options);
    return new Automation((array)(@$response->data ?: []), $options);
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'DELETE', $params, $this->options);
    return;
  }

  public function destroy($params = []) {
    $this->delete($params);
    return;
  }

  public function save() {
      if (@$this->attributes['id']) {
        $new_obj = $this->update($this->attributes);
        $this->attributes = $new_obj->attributes;
        return true;
      } else {
        $new_obj = self::create($this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
      }
  }


  // Parameters:
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[automation]=desc`). Valid fields are `automation`, `disabled`, `last_modified_at` or `name`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `disabled`, `last_modified_at` or `automation`. Valid field combinations are `[ automation, disabled ]` and `[ disabled, automation ]`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `last_modified_at`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `last_modified_at`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `last_modified_at`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `last_modified_at`.
  //   with_deleted - boolean - Set to true to include deleted automations in the results.
  public static function all($params = [], $options = []) {
    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/automations', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Automation((array)$obj, $options);
    }

    return $return_array;
  }




  // Parameters:
  //   id (required) - int64 - Automation ID.
  public static function find($id, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['id'] = $id;

    if (!@$params['id']) {
      throw new \Files\MissingParameterException('Parameter missing: id');
    }

    if (@$params['id'] && !is_int(@$params['id'])) {
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/automations/' . @$params['id'] . '', 'GET', $params, $options);

    return new Automation((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }


  // Parameters:
  //   source - string - Source Path
  //   destination - string - DEPRECATED: Destination Path. Use `destinations` instead.
  //   destinations - array(string) - A list of String destination paths or Hash of folder_path and optional file_path.
  //   destination_replace_from - string - If set, this string in the destination path will be replaced with the value in `destination_replace_to`.
  //   destination_replace_to - string - If set, this string will replace the value `destination_replace_from` in the destination filename. You can use special patterns here.
  //   interval - string - How often to run this automation? One of: `day`, `week`, `week_end`, `month`, `month_end`, `quarter`, `quarter_end`, `year`, `year_end`
  //   path - string - Path on which this Automation runs.  Supports globs.
  //   sync_ids - string - A list of sync IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   user_ids - string - A list of user IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   group_ids - string - A list of group IDs the automation is associated with. If sent as a string, it should be comma-delimited.
  //   schedule - object - Custom schedule for running this automation.
  //   description - string - Description for the this Automation.
  //   disabled - boolean - If true, this automation will not run.
  //   name - string - Name for this automation.
  //   trigger - string - How this automation is triggered to run.
  //   trigger_actions - array(string) - If trigger is `action`, this is the list of action types on which to trigger the automation. Valid actions are create, read, update, destroy, move, copy
  //   value - object - A Hash of attributes specific to the automation type.
  //   recurring_day - int64 - If trigger type is `daily`, this specifies a day number to run in one of the supported intervals: `week`, `month`, `quarter`, `year`.
  //   automation (required) - string - Automation type
  public static function create($params = [], $options = []) {
    if (!@$params['automation']) {
      throw new \Files\MissingParameterException('Parameter missing: automation');
    }

    if (@$params['source'] && !is_string(@$params['source'])) {
      throw new \Files\InvalidParameterException('$source must be of type string; received ' . gettype(@$params['source']));
    }

    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \Files\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
    }

    if (@$params['destinations'] && !is_array(@$params['destinations'])) {
      throw new \Files\InvalidParameterException('$destinations must be of type array; received ' . gettype(@$params['destinations']));
    }

    if (@$params['destination_replace_from'] && !is_string(@$params['destination_replace_from'])) {
      throw new \Files\InvalidParameterException('$destination_replace_from must be of type string; received ' . gettype(@$params['destination_replace_from']));
    }

    if (@$params['destination_replace_to'] && !is_string(@$params['destination_replace_to'])) {
      throw new \Files\InvalidParameterException('$destination_replace_to must be of type string; received ' . gettype(@$params['destination_replace_to']));
    }

    if (@$params['interval'] && !is_string(@$params['interval'])) {
      throw new \Files\InvalidParameterException('$interval must be of type string; received ' . gettype(@$params['interval']));
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
    }

    if (@$params['sync_ids'] && !is_string(@$params['sync_ids'])) {
      throw new \Files\InvalidParameterException('$sync_ids must be of type string; received ' . gettype(@$params['sync_ids']));
    }

    if (@$params['user_ids'] && !is_string(@$params['user_ids'])) {
      throw new \Files\InvalidParameterException('$user_ids must be of type string; received ' . gettype(@$params['user_ids']));
    }

    if (@$params['group_ids'] && !is_string(@$params['group_ids'])) {
      throw new \Files\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['name'] && !is_string(@$params['name'])) {
      throw new \Files\InvalidParameterException('$name must be of type string; received ' . gettype(@$params['name']));
    }

    if (@$params['trigger'] && !is_string(@$params['trigger'])) {
      throw new \Files\InvalidParameterException('$trigger must be of type string; received ' . gettype(@$params['trigger']));
    }

    if (@$params['trigger_actions'] && !is_array(@$params['trigger_actions'])) {
      throw new \Files\InvalidParameterException('$trigger_actions must be of type array; received ' . gettype(@$params['trigger_actions']));
    }

    if (@$params['recurring_day'] && !is_int(@$params['recurring_day'])) {
      throw new \Files\InvalidParameterException('$recurring_day must be of type int; received ' . gettype(@$params['recurring_day']));
    }

    if (@$params['automation'] && !is_string(@$params['automation'])) {
      throw new \Files\InvalidParameterException('$automation must be of type string; received ' . gettype(@$params['automation']));
    }

    $response = Api::sendRequest('/automations', 'POST', $params, $options);

    return new Automation((array)(@$response->data ?: []), $options);
  }

}
