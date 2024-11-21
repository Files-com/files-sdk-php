<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Bundle
 *
 * @package Files
 */
class Bundle
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return @$this->attributes[$name];
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // string # Bundle code.  This code forms the end part of the Public URL.
    public function getCode()
    {
        return @$this->attributes['code'];
    }

    public function setCode($value)
    {
        return $this->attributes['code'] = $value;
    }
    // string # Page link and button color
    public function getColorLeft()
    {
        return @$this->attributes['color_left'];
    }

    public function setColorLeft($value)
    {
        return $this->attributes['color_left'] = $value;
    }
    // string # Top bar link color
    public function getColorLink()
    {
        return @$this->attributes['color_link'];
    }

    public function setColorLink($value)
    {
        return $this->attributes['color_link'] = $value;
    }
    // string # Page link and button color
    public function getColorText()
    {
        return @$this->attributes['color_text'];
    }

    public function setColorText($value)
    {
        return $this->attributes['color_text'] = $value;
    }
    // string # Top bar background color
    public function getColorTop()
    {
        return @$this->attributes['color_top'];
    }

    public function setColorTop($value)
    {
        return $this->attributes['color_top'] = $value;
    }
    // string # Top bar text color
    public function getColorTopText()
    {
        return @$this->attributes['color_top_text'];
    }

    public function setColorTopText($value)
    {
        return $this->attributes['color_top_text'] = $value;
    }
    // string # Public URL of Share Link
    public function getUrl()
    {
        return @$this->attributes['url'];
    }

    public function setUrl($value)
    {
        return $this->attributes['url'] = $value;
    }
    // string # Public description
    public function getDescription()
    {
        return @$this->attributes['description'];
    }

    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    // date-time # Bundle expiration date/time
    public function getExpiresAt()
    {
        return @$this->attributes['expires_at'];
    }

    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    // boolean # Is this bundle password protected?
    public function getPasswordProtected()
    {
        return @$this->attributes['password_protected'];
    }

    public function setPasswordProtected($value)
    {
        return $this->attributes['password_protected'] = $value;
    }
    // string # Permissions that apply to Folders in this Share Link.
    public function getPermissions()
    {
        return @$this->attributes['permissions'];
    }

    public function setPermissions($value)
    {
        return $this->attributes['permissions'] = $value;
    }
    // boolean
    public function getPreviewOnly()
    {
        return @$this->attributes['preview_only'];
    }

    public function setPreviewOnly($value)
    {
        return $this->attributes['preview_only'] = $value;
    }
    // boolean # Show a registration page that captures the downloader's name and email address?
    public function getRequireRegistration()
    {
        return @$this->attributes['require_registration'];
    }

    public function setRequireRegistration($value)
    {
        return $this->attributes['require_registration'] = $value;
    }
    // boolean # Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
    public function getRequireShareRecipient()
    {
        return @$this->attributes['require_share_recipient'];
    }

    public function setRequireShareRecipient($value)
    {
        return $this->attributes['require_share_recipient'] = $value;
    }
    // boolean # If true, we will hide the 'Remember Me' box on the Bundle registration page, requiring that the user logout and log back in every time they visit the page.
    public function getRequireLogout()
    {
        return @$this->attributes['require_logout'];
    }

    public function setRequireLogout($value)
    {
        return $this->attributes['require_logout'] = $value;
    }
    // string # Legal text that must be agreed to prior to accessing Bundle.
    public function getClickwrapBody()
    {
        return @$this->attributes['clickwrap_body'];
    }

    public function setClickwrapBody($value)
    {
        return $this->attributes['clickwrap_body'] = $value;
    }
    // FormFieldSet # Custom Form to use
    public function getFormFieldSet()
    {
        return @$this->attributes['form_field_set'];
    }

    public function setFormFieldSet($value)
    {
        return $this->attributes['form_field_set'] = $value;
    }
    // boolean # BundleRegistrations can be saved without providing name?
    public function getSkipName()
    {
        return @$this->attributes['skip_name'];
    }

    public function setSkipName($value)
    {
        return $this->attributes['skip_name'] = $value;
    }
    // boolean # BundleRegistrations can be saved without providing email?
    public function getSkipEmail()
    {
        return @$this->attributes['skip_email'];
    }

    public function setSkipEmail($value)
    {
        return $this->attributes['skip_email'] = $value;
    }
    // date-time # Date when share will start to be accessible. If `nil` access granted right after create.
    public function getStartAccessOnDate()
    {
        return @$this->attributes['start_access_on_date'];
    }

    public function setStartAccessOnDate($value)
    {
        return $this->attributes['start_access_on_date'] = $value;
    }
    // boolean # BundleRegistrations can be saved without providing company?
    public function getSkipCompany()
    {
        return @$this->attributes['skip_company'];
    }

    public function setSkipCompany($value)
    {
        return $this->attributes['skip_company'] = $value;
    }
    // int64 # Bundle ID
    public function getId()
    {
        return @$this->attributes['id'];
    }

    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    // date-time # Bundle created at date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // boolean # Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
    public function getDontSeparateSubmissionsByFolder()
    {
        return @$this->attributes['dont_separate_submissions_by_folder'];
    }

    public function setDontSeparateSubmissionsByFolder($value)
    {
        return $this->attributes['dont_separate_submissions_by_folder'] = $value;
    }
    // int64 # Maximum number of times bundle can be accessed
    public function getMaxUses()
    {
        return @$this->attributes['max_uses'];
    }

    public function setMaxUses($value)
    {
        return $this->attributes['max_uses'] = $value;
    }
    // string # Bundle internal note
    public function getNote()
    {
        return @$this->attributes['note'];
    }

    public function setNote($value)
    {
        return $this->attributes['note'] = $value;
    }
    // string # Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
    public function getPathTemplate()
    {
        return @$this->attributes['path_template'];
    }

    public function setPathTemplate($value)
    {
        return $this->attributes['path_template'] = $value;
    }
    // string # Timezone to use when rendering timestamps in path templates.
    public function getPathTemplateTimeZone()
    {
        return @$this->attributes['path_template_time_zone'];
    }

    public function setPathTemplateTimeZone($value)
    {
        return $this->attributes['path_template_time_zone'] = $value;
    }
    // boolean # Send delivery receipt to the uploader. Note: For writable share only
    public function getSendEmailReceiptToUploader()
    {
        return @$this->attributes['send_email_receipt_to_uploader'];
    }

    public function setSendEmailReceiptToUploader($value)
    {
        return $this->attributes['send_email_receipt_to_uploader'] = $value;
    }
    // int64 # ID of the snapshot containing this bundle's contents.
    public function getSnapshotId()
    {
        return @$this->attributes['snapshot_id'];
    }

    public function setSnapshotId($value)
    {
        return $this->attributes['snapshot_id'] = $value;
    }
    // int64 # Bundle creator user ID
    public function getUserId()
    {
        return @$this->attributes['user_id'];
    }

    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    // string # Bundle creator username
    public function getUsername()
    {
        return @$this->attributes['username'];
    }

    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    // int64 # ID of the clickwrap to use with this bundle.
    public function getClickwrapId()
    {
        return @$this->attributes['clickwrap_id'];
    }

    public function setClickwrapId($value)
    {
        return $this->attributes['clickwrap_id'] = $value;
    }
    // int64 # ID of the associated inbox, if available.
    public function getInboxId()
    {
        return @$this->attributes['inbox_id'];
    }

    public function setInboxId($value)
    {
        return $this->attributes['inbox_id'] = $value;
    }
    // Image # Preview watermark image applied to all bundle items.
    public function getWatermarkAttachment()
    {
        return @$this->attributes['watermark_attachment'];
    }

    public function setWatermarkAttachment($value)
    {
        return $this->attributes['watermark_attachment'] = $value;
    }
    // object # Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
    public function getWatermarkValue()
    {
        return @$this->attributes['watermark_value'];
    }

    public function setWatermarkValue($value)
    {
        return $this->attributes['watermark_value'] = $value;
    }
    // boolean # Does this bundle have an associated inbox?
    public function getHasInbox()
    {
        return @$this->attributes['has_inbox'];
    }

    public function setHasInbox($value)
    {
        return $this->attributes['has_inbox'] = $value;
    }
    // boolean # Should folder uploads be prevented?
    public function getDontAllowFoldersInUploads()
    {
        return @$this->attributes['dont_allow_folders_in_uploads'];
    }

    public function setDontAllowFoldersInUploads($value)
    {
        return $this->attributes['dont_allow_folders_in_uploads'] = $value;
    }
    // array(string) # A list of paths in this bundle.  For performance reasons, this is not provided when listing bundles.
    public function getPaths()
    {
        return @$this->attributes['paths'];
    }

    public function setPaths($value)
    {
        return $this->attributes['paths'] = $value;
    }
    // array(object) # A list of bundlepaths in this bundle.  For performance reasons, this is not provided when listing bundles.
    public function getBundlepaths()
    {
        return @$this->attributes['bundlepaths'];
    }

    public function setBundlepaths($value)
    {
        return $this->attributes['bundlepaths'] = $value;
    }
    // string # Password for this bundle.
    public function getPassword()
    {
        return @$this->attributes['password'];
    }

    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    // int64 # Id of Form Field Set to use with this bundle
    public function getFormFieldSetId()
    {
        return @$this->attributes['form_field_set_id'];
    }

    public function setFormFieldSetId($value)
    {
        return $this->attributes['form_field_set_id'] = $value;
    }
    // boolean # If true, create a snapshot of this bundle's contents.
    public function getCreateSnapshot()
    {
        return @$this->attributes['create_snapshot'];
    }

    public function setCreateSnapshot($value)
    {
        return $this->attributes['create_snapshot'] = $value;
    }
    // boolean # If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
    public function getFinalizeSnapshot()
    {
        return @$this->attributes['finalize_snapshot'];
    }

    public function setFinalizeSnapshot($value)
    {
        return $this->attributes['finalize_snapshot'] = $value;
    }
    // file # Preview watermark image applied to all bundle items.
    public function getWatermarkAttachmentFile()
    {
        return @$this->attributes['watermark_attachment_file'];
    }

    public function setWatermarkAttachmentFile($value)
    {
        return $this->attributes['watermark_attachment_file'] = $value;
    }
    // boolean # If true, will delete the file stored in watermark_attachment
    public function getWatermarkAttachmentDelete()
    {
        return @$this->attributes['watermark_attachment_delete'];
    }

    public function setWatermarkAttachmentDelete($value)
    {
        return $this->attributes['watermark_attachment_delete'] = $value;
    }

    // Send email(s) with a link to bundle
    //
    // Parameters:
    //   to - array(string) - A list of email addresses to share this bundle with. Required unless `recipients` is used.
    //   note - string - Note to include in email.
    //   recipients - array(object) - A list of recipients to share this bundle with. Required unless `to` is used.
    public function share($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['to'] && !is_array(@$params['to'])) {
            throw new \Files\Exception\InvalidParameterException('$to must be of type array; received ' . gettype(@$params['to']));
        }

        if (@$params['note'] && !is_string(@$params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (@$params['recipients'] && !is_array(@$params['recipients'])) {
            throw new \Files\Exception\InvalidParameterException('$recipients must be of type array; received ' . gettype(@$params['recipients']));
        }

        $response = Api::sendRequest('/bundles/' . @$params['id'] . '/share', 'POST', $params, $this->options);
        return;
    }

    // Parameters:
    //   paths - array(string) - A list of paths to include in this bundle.
    //   password - string - Password for this bundle.
    //   form_field_set_id - int64 - Id of Form Field Set to use with this bundle
    //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
    //   code - string - Bundle code.  This code forms the end part of the Public URL.
    //   create_snapshot - boolean - If true, create a snapshot of this bundle's contents.
    //   description - string - Public description
    //   dont_separate_submissions_by_folder - boolean - Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
    //   expires_at - string - Bundle expiration date/time
    //   finalize_snapshot - boolean - If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
    //   inbox_id - int64 - ID of the associated inbox, if available.
    //   max_uses - int64 - Maximum number of times bundle can be accessed
    //   note - string - Bundle internal note
    //   path_template - string - Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
    //   path_template_time_zone - string - Timezone to use when rendering timestamps in path templates.
    //   permissions - string - Permissions that apply to Folders in this Share Link.
    //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
    //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
    //   send_email_receipt_to_uploader - boolean - Send delivery receipt to the uploader. Note: For writable share only
    //   skip_company - boolean - BundleRegistrations can be saved without providing company?
    //   start_access_on_date - string - Date when share will start to be accessible. If `nil` access granted right after create.
    //   skip_email - boolean - BundleRegistrations can be saved without providing email?
    //   skip_name - boolean - BundleRegistrations can be saved without providing name?
    //   watermark_attachment_delete - boolean - If true, will delete the file stored in watermark_attachment
    //   watermark_attachment_file - file - Preview watermark image applied to all bundle items.
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (@$params['paths'] && !is_array(@$params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (@$params['password'] && !is_string(@$params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (@$params['form_field_set_id'] && !is_int(@$params['form_field_set_id'])) {
            throw new \Files\Exception\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
        }

        if (@$params['clickwrap_id'] && !is_int(@$params['clickwrap_id'])) {
            throw new \Files\Exception\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
        }

        if (@$params['code'] && !is_string(@$params['code'])) {
            throw new \Files\Exception\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (@$params['inbox_id'] && !is_int(@$params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        if (@$params['max_uses'] && !is_int(@$params['max_uses'])) {
            throw new \Files\Exception\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
        }

        if (@$params['note'] && !is_string(@$params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (@$params['path_template'] && !is_string(@$params['path_template'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
        }

        if (@$params['path_template_time_zone'] && !is_string(@$params['path_template_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template_time_zone must be of type string; received ' . gettype(@$params['path_template_time_zone']));
        }

        if (@$params['permissions'] && !is_string(@$params['permissions'])) {
            throw new \Files\Exception\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
        }

        if (@$params['start_access_on_date'] && !is_string(@$params['start_access_on_date'])) {
            throw new \Files\Exception\InvalidParameterException('$start_access_on_date must be of type string; received ' . gettype(@$params['start_access_on_date']));
        }

        $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'PATCH', $params, $this->options);
        return new Bundle((array) (@$response->data ?: []), $this->options);
    }

    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['id']) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    public function save()
    {
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
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `code` or `note`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `code`, `note` or `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `code` and `note`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function all($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/bundles', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Bundle((array) $obj, $options);
        }

        return $return_array;
    }

    // Parameters:
    //   id (required) - int64 - Bundle ID.
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (!@$params['id']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (@$params['id'] && !is_int(@$params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/bundles/' . @$params['id'] . '', 'GET', $params, $options);

        return new Bundle((array) (@$response->data ?: []), $options);
    }
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   paths (required) - array(string) - A list of paths to include in this bundle.
    //   password - string - Password for this bundle.
    //   form_field_set_id - int64 - Id of Form Field Set to use with this bundle
    //   create_snapshot - boolean - If true, create a snapshot of this bundle's contents.
    //   dont_separate_submissions_by_folder - boolean - Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
    //   expires_at - string - Bundle expiration date/time
    //   finalize_snapshot - boolean - If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
    //   max_uses - int64 - Maximum number of times bundle can be accessed
    //   description - string - Public description
    //   note - string - Bundle internal note
    //   code - string - Bundle code.  This code forms the end part of the Public URL.
    //   path_template - string - Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
    //   path_template_time_zone - string - Timezone to use when rendering timestamps in path templates.
    //   permissions - string - Permissions that apply to Folders in this Share Link.
    //   require_registration - boolean - Show a registration page that captures the downloader's name and email address?
    //   clickwrap_id - int64 - ID of the clickwrap to use with this bundle.
    //   inbox_id - int64 - ID of the associated inbox, if available.
    //   require_share_recipient - boolean - Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
    //   send_email_receipt_to_uploader - boolean - Send delivery receipt to the uploader. Note: For writable share only
    //   skip_email - boolean - BundleRegistrations can be saved without providing email?
    //   skip_name - boolean - BundleRegistrations can be saved without providing name?
    //   skip_company - boolean - BundleRegistrations can be saved without providing company?
    //   start_access_on_date - string - Date when share will start to be accessible. If `nil` access granted right after create.
    //   snapshot_id - int64 - ID of the snapshot containing this bundle's contents.
    //   watermark_attachment_file - file - Preview watermark image applied to all bundle items.
    public static function create($params = [], $options = [])
    {
        if (!@$params['paths']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: paths');
        }

        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (@$params['paths'] && !is_array(@$params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (@$params['password'] && !is_string(@$params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (@$params['form_field_set_id'] && !is_int(@$params['form_field_set_id'])) {
            throw new \Files\Exception\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
        }

        if (@$params['expires_at'] && !is_string(@$params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (@$params['max_uses'] && !is_int(@$params['max_uses'])) {
            throw new \Files\Exception\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
        }

        if (@$params['description'] && !is_string(@$params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (@$params['note'] && !is_string(@$params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (@$params['code'] && !is_string(@$params['code'])) {
            throw new \Files\Exception\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
        }

        if (@$params['path_template'] && !is_string(@$params['path_template'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
        }

        if (@$params['path_template_time_zone'] && !is_string(@$params['path_template_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template_time_zone must be of type string; received ' . gettype(@$params['path_template_time_zone']));
        }

        if (@$params['permissions'] && !is_string(@$params['permissions'])) {
            throw new \Files\Exception\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
        }

        if (@$params['clickwrap_id'] && !is_int(@$params['clickwrap_id'])) {
            throw new \Files\Exception\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
        }

        if (@$params['inbox_id'] && !is_int(@$params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        if (@$params['start_access_on_date'] && !is_string(@$params['start_access_on_date'])) {
            throw new \Files\Exception\InvalidParameterException('$start_access_on_date must be of type string; received ' . gettype(@$params['start_access_on_date']));
        }

        if (@$params['snapshot_id'] && !is_int(@$params['snapshot_id'])) {
            throw new \Files\Exception\InvalidParameterException('$snapshot_id must be of type int; received ' . gettype(@$params['snapshot_id']));
        }

        $response = Api::sendRequest('/bundles', 'POST', $params, $options);

        return new Bundle((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   user_id - int64 - User ID.  Provide a value of `0` to operate the current session's user.
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `code` or `note`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `code`, `note` or `created_at`.
    //   filter_gt - object - If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
    //   filter_gteq - object - If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `code` and `note`.
    //   filter_lt - object - If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
    //   filter_lteq - object - If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
    public static function createExport($params = [], $options = [])
    {
        if (@$params['user_id'] && !is_int(@$params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        $response = Api::sendRequest('/bundles/create_export', 'POST', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Export((array) $obj, $options);
        }

        return $return_array;
    }
}
