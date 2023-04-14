<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Bundle
 *
 * @package Files
 */
class Bundle {
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

  // string # Bundle code.  This code forms the end part of the Public URL.
  public function getCode() {
    return @$this->attributes['code'];
  }

  public function setCode($value) {
    return $this->attributes['code'] = $value;
  }

  // string # Public URL of Share Link
  public function getUrl() {
    return @$this->attributes['url'];
  }

  public function setUrl($value) {
    return $this->attributes['url'] = $value;
  }

  // string # Public description
  public function getDescription() {
    return @$this->attributes['description'];
  }

  public function setDescription($value) {
    return $this->attributes['description'] = $value;
  }

  // boolean # Is this bundle password protected?
  public function getPasswordProtected() {
    return @$this->attributes['password_protected'];
  }

  public function setPasswordProtected($value) {
    return $this->attributes['password_protected'] = $value;
  }

  // string # Permissions that apply to Folders in this Share Link.
  public function getPermissions() {
    return @$this->attributes['permissions'];
  }

  public function setPermissions($value) {
    return $this->attributes['permissions'] = $value;
  }

  // boolean # Restrict users to previewing files only?
  public function getPreviewOnly() {
    return @$this->attributes['preview_only'];
  }

  public function setPreviewOnly($value) {
    return $this->attributes['preview_only'] = $value;
  }

  // boolean # Show a registration page that captures the downloader's name and email address?
  public function getRequireRegistration() {
    return @$this->attributes['require_registration'];
  }

  public function setRequireRegistration($value) {
    return $this->attributes['require_registration'] = $value;
  }

  // boolean # Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  public function getRequireShareRecipient() {
    return @$this->attributes['require_share_recipient'];
  }

  public function setRequireShareRecipient($value) {
    return $this->attributes['require_share_recipient'] = $value;
  }

  // string # Legal text that must be agreed to prior to accessing Bundle.
  public function getClickwrapBody() {
    return @$this->attributes['clickwrap_body'];
  }

  public function setClickwrapBody($value) {
    return $this->attributes['clickwrap_body'] = $value;
  }

  // FormFieldSet # Custom Form to use
  public function getFormFieldSet() {
    return @$this->attributes['form_field_set'];
  }

  public function setFormFieldSet($value) {
    return $this->attributes['form_field_set'] = $value;
  }

  // boolean # BundleRegistrations can be saved without providing name?
  public function getSkipName() {
    return @$this->attributes['skip_name'];
  }

  public function setSkipName($value) {
    return $this->attributes['skip_name'] = $value;
  }

  // boolean # BundleRegistrations can be saved without providing email?
  public function getSkipEmail() {
    return @$this->attributes['skip_email'];
  }

  public function setSkipEmail($value) {
    return $this->attributes['skip_email'] = $value;
  }

  // boolean # BundleRegistrations can be saved without providing company?
  public function getSkipCompany() {
    return @$this->attributes['skip_company'];
  }

  public function setSkipCompany($value) {
    return $this->attributes['skip_company'] = $value;
  }

  // int64 # Bundle ID
  public function getId() {
    return @$this->attributes['id'];
  }

  public function setId($value) {
    return $this->attributes['id'] = $value;
  }

  // date-time # Bundle created at date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // boolean # Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
  public function getDontSeparateSubmissionsByFolder() {
    return @$this->attributes['dont_separate_submissions_by_folder'];
  }

  public function setDontSeparateSubmissionsByFolder($value) {
    return $this->attributes['dont_separate_submissions_by_folder'] = $value;
  }

  // date-time # Bundle expiration date/time
  public function getExpiresAt() {
    return @$this->attributes['expires_at'];
  }

  public function setExpiresAt($value) {
    return $this->attributes['expires_at'] = $value;
  }

  // int64 # Maximum number of times bundle can be accessed
  public function getMaxUses() {
    return @$this->attributes['max_uses'];
  }

  public function setMaxUses($value) {
    return $this->attributes['max_uses'] = $value;
  }

  // string # Bundle internal note
  public function getNote() {
    return @$this->attributes['note'];
  }

  public function setNote($value) {
    return $this->attributes['note'] = $value;
  }

  // string # Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, and any custom form data.
  public function getPathTemplate() {
    return @$this->attributes['path_template'];
  }

  public function setPathTemplate($value) {
    return $this->attributes['path_template'] = $value;
  }

  // boolean # Send delivery receipt to the uploader. Note: For writable share only
  public function getSendEmailReceiptToUploader() {
    return @$this->attributes['send_email_receipt_to_uploader'];
  }

  public function setSendEmailReceiptToUploader($value) {
    return $this->attributes['send_email_receipt_to_uploader'] = $value;
  }

  // int64 # Bundle creator user ID
  public function getUserId() {
    return @$this->attributes['user_id'];
  }

  public function setUserId($value) {
    return $this->attributes['user_id'] = $value;
  }

  // string # Bundle creator username
  public function getUsername() {
    return @$this->attributes['username'];
  }

  public function setUsername($value) {
    return $this->attributes['username'] = $value;
  }

  // int64 # ID of the clickwrap to use with this bundle.
  public function getClickwrapId() {
    return @$this->attributes['clickwrap_id'];
  }

  public function setClickwrapId($value) {
    return $this->attributes['clickwrap_id'] = $value;
  }

  // int64 # ID of the associated inbox, if available.
  public function getInboxId() {
    return @$this->attributes['inbox_id'];
  }

  public function setInboxId($value) {
    return $this->attributes['inbox_id'] = $value;
  }

  // Image # Preview watermark image applied to all bundle items.
  public function getWatermarkAttachment() {
    return @$this->attributes['watermark_attachment'];
  }

  public function setWatermarkAttachment($value) {
    return $this->attributes['watermark_attachment'] = $value;
  }

  // object # Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
  public function getWatermarkValue() {
    return @$this->attributes['watermark_value'];
  }

  public function setWatermarkValue($value) {
    return $this->attributes['watermark_value'] = $value;
  }

  // boolean # Does this bundle have an associated inbox?
  public function getHasInbox() {
    return @$this->attributes['has_inbox'];
  }

  public function setHasInbox($value) {
    return $this->attributes['has_inbox'] = $value;
  }

  // array # A list of paths in this bundle.  For performance reasons, this is not provided when listing bundles.
  public function getPaths() {
    return @$this->attributes['paths'];
  }

  public function setPaths($value) {
    return $this->attributes['paths'] = $value;
  }

  // string # Password for this bundle.
  public function getPassword() {
    return @$this->attributes['password'];
  }

  public function setPassword($value) {
    return $this->attributes['password'] = $value;
  }

  // int64 # Id of Form Field Set to use with this bundle
  public function getFormFieldSetId() {
    return @$this->attributes['form_field_set_id'];
  }

  public function setFormFieldSetId($value) {
    return $this->attributes['form_field_set_id'] = $value;
  }

  // file # Preview watermark image applied to all bundle items.
  public function getWatermarkAttachmentFile() {
    return @$this->attributes['watermark_attachment_file'];
  }

  public function setWatermarkAttachmentFile($value) {
    return $this->attributes['watermark_attachment_file'] = $value;
  }

  // boolean # If true, will delete the file stored in watermark_attachment
  public function getWatermarkAttachmentDelete() {
    return @$this->attributes['watermark_attachment_delete'];
  }

  public function setWatermarkAttachmentDelete($value) {
    return $this->attributes['watermark_attachment_delete'] = $value;
  }

  // Send email(s) with a link to bundle
  //
  // Parameters:
  //   to - array(string) - A list of email addresses to share this bundle with. Required unless `recipients` is used.
  //   note - string - Note to include in email.
  //   recipients - array(object) - A list of recipients to share this bundle with. Required unless `to` is used.
  public function share($params = []) {
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

    if (@$params['to'] && !is_array(@$params['to'])) {
      throw new \Files\InvalidParameterException('$to must be of type array; received ' . gettype(@$params['to']));
    }

    if (@$params['note'] && !is_string(@$params['note'])) {
      throw new \Files\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
    }

    if (@$params['recipients'] && !is_array(@$params['recipients'])) {
      throw new \Files\InvalidParameterException('$recipients must be of type array; received ' . gettype(@$params['recipients']));
    }

    $response = Api::sendRequest('/bundles/' . @$params['id'] . '/share', 'POST', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   paths - array(string) - A list of paths to include in this bundle.
  //   password - string - Password for this bundle.
  //   form_field_set_id - int64 - Id of Form Field Set to use with this bundle
  //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
  //   code - string - Bundle code.  This code forms the end part of the Public URL.
  //   description - string - Public description
  //   dont_separate_submissions_by_folder - boolean - Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
  //   expires_at - string - Bundle expiration date/time
  //   inbox_id - int64 - ID of the associated inbox, if available.
  //   max_uses - int64 - Maximum number of times bundle can be accessed
  //   note - string - Bundle internal note
  //   path_template - string - Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, and any custom form data.
  //   permissions - string - Permissions that apply to Folders in this Share Link.
  //   preview_only - boolean - Restrict users to previewing files only?
  //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
  //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  //   send_email_receipt_to_uploader - boolean - Send delivery receipt to the uploader. Note: For writable share only
  //   skip_company - boolean - BundleRegistrations can be saved without providing company?
  //   skip_email - boolean - BundleRegistrations can be saved without providing email?
  //   skip_name - boolean - BundleRegistrations can be saved without providing name?
  //   watermark_attachment_delete - boolean - If true, will delete the file stored in watermark_attachment
  //   watermark_attachment_file - file - Preview watermark image applied to all bundle items.
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

    if (@$params['paths'] && !is_array(@$params['paths'])) {
      throw new \Files\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
    }

    if (@$params['form_field_set_id'] && !is_int(@$params['form_field_set_id'])) {
      throw new \Files\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
    }

    if (@$params['clickwrap_id'] && !is_int(@$params['clickwrap_id'])) {
      throw new \Files\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
    }

    if (@$params['code'] && !is_string(@$params['code'])) {
      throw new \Files\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['inbox_id'] && !is_int(@$params['inbox_id'])) {
      throw new \Files\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
    }

    if (@$params['max_uses'] && !is_int(@$params['max_uses'])) {
      throw new \Files\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
    }

    if (@$params['note'] && !is_string(@$params['note'])) {
      throw new \Files\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
    }

    if (@$params['path_template'] && !is_string(@$params['path_template'])) {
      throw new \Files\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
    }

    if (@$params['permissions'] && !is_string(@$params['permissions'])) {
      throw new \Files\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
    }

    $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'PATCH', $params, $this->options);
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
      throw new \Files\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
    }

    $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'DELETE', $params, $this->options);
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
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
  //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
  //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction (e.g. `sort_by[created_at]=desc`). Valid fields are `created_at` and `code`.
  //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`.
  //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
  //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
  //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
  //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
  public static function all($params = [], $options = []) {
    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['cursor'] && !is_string(@$params['cursor'])) {
      throw new \Files\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
    }

    if (@$params['per_page'] && !is_int(@$params['per_page'])) {
      throw new \Files\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
    }

    $response = Api::sendRequest('/bundles', 'GET', $params, $options);

    $return_array = [];

    foreach ($response->data as $obj) {
      $return_array[] = new Bundle((array)$obj, $options);
    }

    return $return_array;
  }


  

  // Parameters:
  //   id (required) - int64 - Bundle ID.
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

    $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'GET', $params, $options);

    return new Bundle((array)(@$response->data ?: []), $options);
  }


  public static function get($id, $params = [], $options = []) {
    return self::find($id, $params, $options);
  }
  

  // Parameters:
  //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
  //   paths (required) - array(string) - A list of paths to include in this bundle.
  //   password - string - Password for this bundle.
  //   form_field_set_id - int64 - Id of Form Field Set to use with this bundle
  //   dont_separate_submissions_by_folder - boolean - Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
  //   expires_at - string - Bundle expiration date/time
  //   max_uses - int64 - Maximum number of times bundle can be accessed
  //   description - string - Public description
  //   note - string - Bundle internal note
  //   code - string - Bundle code.  This code forms the end part of the Public URL.
  //   path_template - string - Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, and any custom form data.
  //   permissions - string - Permissions that apply to Folders in this Share Link.
  //   preview_only - boolean - Restrict users to previewing files only?
  //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
  //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
  //   inbox_id - int64 - ID of the associated inbox, if available.
  //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
  //   send_email_receipt_to_uploader - boolean - Send delivery receipt to the uploader. Note: For writable share only
  //   skip_email - boolean - BundleRegistrations can be saved without providing email?
  //   skip_name - boolean - BundleRegistrations can be saved without providing name?
  //   skip_company - boolean - BundleRegistrations can be saved without providing company?
  //   watermark_attachment_file - file - Preview watermark image applied to all bundle items.
  public static function create($params = [], $options = []) {
    if (!@$params['paths']) {
      throw new \Files\MissingParameterException('Parameter missing: paths');
    }

    if (@$params['user_id'] && !is_int(@$params['user_id'])) {
      throw new \Files\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
    }

    if (@$params['paths'] && !is_array(@$params['paths'])) {
      throw new \Files\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
    }

    if (@$params['password'] && !is_string(@$params['password'])) {
      throw new \Files\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
    }

    if (@$params['form_field_set_id'] && !is_int(@$params['form_field_set_id'])) {
      throw new \Files\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
    }

    if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
      throw new \Files\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
    }

    if (@$params['max_uses'] && !is_int(@$params['max_uses'])) {
      throw new \Files\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
    }

    if (@$params['description'] && !is_string(@$params['description'])) {
      throw new \Files\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
    }

    if (@$params['note'] && !is_string(@$params['note'])) {
      throw new \Files\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
    }

    if (@$params['code'] && !is_string(@$params['code'])) {
      throw new \Files\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
    }

    if (@$params['path_template'] && !is_string(@$params['path_template'])) {
      throw new \Files\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
    }

    if (@$params['permissions'] && !is_string(@$params['permissions'])) {
      throw new \Files\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
    }

    if (@$params['clickwrap_id'] && !is_int(@$params['clickwrap_id'])) {
      throw new \Files\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
    }

    if (@$params['inbox_id'] && !is_int(@$params['inbox_id'])) {
      throw new \Files\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
    }

    $response = Api::sendRequest('/bundles', 'POST', $params, $options);

    return new Bundle((array)(@$response->data ?: []), $options);
  }

}
