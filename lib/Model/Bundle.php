<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Bundle is the API/SDK term for the feature called Share Links in the web interface.
 *
 * @property string|null $code Bundle code. This code forms the end part of the Public URL.
 * @property string|null $color_left Page link and button color
 * @property string|null $color_link Top bar link color
 * @property string|null $color_text Page link and button color
 * @property string|null $color_top Top bar background color
 * @property string|null $color_top_text Top bar text color
 * @property string|null $url Public URL of Share Link
 * @property string|null $description Public description
 * @property string|null $expires_at Explicit Bundle expiration date/time. If not set, the site-wide expiration setting may apply.
 * @property bool|null $password_protected Is this bundle password protected?
 * @property string|null $permissions Permissions that apply to Folders in this Share Link.
 * @property bool|null $preview_only
 * @property bool|null $require_registration Show a registration page that captures the downloader's name and email address?
 * @property bool|null $require_share_recipient Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
 * @property bool|null $require_logout If true, we will hide the 'Remember Me' box on the Bundle registration page, requiring that the user logout and log back in every time they visit the page.
 * @property string|null $clickwrap_body Legal text that must be agreed to prior to accessing Bundle.
 * @property \stdClass|null $form_field_set Custom Form to use
 * @property bool|null $skip_name BundleRegistrations can be saved without providing name?
 * @property bool|null $skip_email BundleRegistrations can be saved without providing email?
 * @property string|null $start_access_on_date Date when share will start to be accessible. If `nil` access granted right after create.
 * @property bool|null $skip_company BundleRegistrations can be saved without providing company?
 * @property int|null $id Bundle ID
 * @property bool|null $bypasses_site_expiration_rules If true, this Share Link bypasses site-wide expiration rules. Only site admins may set this.
 * @property string|null $created_at Bundle created at date/time
 * @property bool|null $deleted Indicates if the bundle has been deleted.
 * @property string|null $deleted_at Bundle deleted at date/time
 * @property bool|null $dont_separate_submissions_by_folder Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
 * @property string|null $effective_expires_at Read-only expiration date/time, using the explicit expiration or the site-wide setting when applicable. Null when the Share Link does not expire.
 * @property int|null $max_uses Maximum number of times bundle can be accessed
 * @property string|null $internal_name Internal name for identifying this Share Link.
 * @property string|null $note Bundle internal note
 * @property string|null $path_template Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
 * @property string|null $path_template_time_zone Timezone to use when rendering timestamps in path templates.
 * @property bool|null $send_email_receipt_to_uploader Send delivery receipt to the uploader. Note: For writable share only
 * @property int|null $snapshot_id ID of the snapshot containing this bundle's contents.
 * @property int|null $user_id Bundle creator user ID
 * @property string|null $username Bundle creator username
 * @property int|null $group_id Owning group ID. If set, members of this group can view, edit, and share this Share Link.
 * @property int|null $clickwrap_id ID of the clickwrap to use with this bundle.
 * @property int|null $inbox_id ID of the associated inbox, if available.
 * @property \stdClass|null $watermark_attachment Preview watermark image applied to all bundle items.
 * @property object|null $watermark_value Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
 * @property bool|null $send_one_time_password_to_recipient_at_registration If true, require_share_recipient bundles will send a one-time password to the recipient when they register. Cannot be enabled if the bundle has a password set.
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property bool|null $has_inbox Does this bundle have an associated inbox?
 * @property bool|null $dont_allow_folders_in_uploads Should folder uploads be prevented?
 * @property object[]|null $requested_upload_slots Upload slots requested by the associated Inbox. Each slot contains a name used as its label and destination subfolder name.
 * @property string[]|null $paths A list of paths in this bundle. For performance reasons, this is not provided when listing bundles.
 * @property object[]|null $bundlepaths A list of bundlepaths in this bundle. For performance reasons, this is not provided when listing bundles.
 * @property string|null $password Password for this bundle.
 * @property int|null $form_field_set_id Id of Form Field Set to use with this bundle
 * @property bool|null $create_snapshot If true, create a snapshot of this bundle's contents.
 * @property bool|null $finalize_snapshot If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
 * @property mixed $watermark_attachment_file Preview watermark image applied to all bundle items.
 * @property bool|null $watermark_attachment_delete If true, will delete the file stored in watermark_attachment
 * @package Files
 */
class Bundle
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * Bundle code.  This code forms the end part of the Public URL.
     *
     * @return string|null
     */
    public function getCode()
    {
        return isset($this->attributes['code']) ? $this->attributes['code'] : null;
    }

    /**
     * Bundle code.  This code forms the end part of the Public URL.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCode($value)
    {
        return $this->attributes['code'] = $value;
    }
    /**
     * Page link and button color
     *
     * @return string|null
     */
    public function getColorLeft()
    {
        return isset($this->attributes['color_left']) ? $this->attributes['color_left'] : null;
    }

    /**
     * Page link and button color
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setColorLeft($value)
    {
        return $this->attributes['color_left'] = $value;
    }
    /**
     * Top bar link color
     *
     * @return string|null
     */
    public function getColorLink()
    {
        return isset($this->attributes['color_link']) ? $this->attributes['color_link'] : null;
    }

    /**
     * Top bar link color
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setColorLink($value)
    {
        return $this->attributes['color_link'] = $value;
    }
    /**
     * Page link and button color
     *
     * @return string|null
     */
    public function getColorText()
    {
        return isset($this->attributes['color_text']) ? $this->attributes['color_text'] : null;
    }

    /**
     * Page link and button color
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setColorText($value)
    {
        return $this->attributes['color_text'] = $value;
    }
    /**
     * Top bar background color
     *
     * @return string|null
     */
    public function getColorTop()
    {
        return isset($this->attributes['color_top']) ? $this->attributes['color_top'] : null;
    }

    /**
     * Top bar background color
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setColorTop($value)
    {
        return $this->attributes['color_top'] = $value;
    }
    /**
     * Top bar text color
     *
     * @return string|null
     */
    public function getColorTopText()
    {
        return isset($this->attributes['color_top_text']) ? $this->attributes['color_top_text'] : null;
    }

    /**
     * Top bar text color
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setColorTopText($value)
    {
        return $this->attributes['color_top_text'] = $value;
    }
    /**
     * Public URL of Share Link
     *
     * @return string|null
     */
    public function getUrl()
    {
        return isset($this->attributes['url']) ? $this->attributes['url'] : null;
    }

    /**
     * Public URL of Share Link
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUrl($value)
    {
        return $this->attributes['url'] = $value;
    }
    /**
     * Public description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return isset($this->attributes['description']) ? $this->attributes['description'] : null;
    }

    /**
     * Public description
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDescription($value)
    {
        return $this->attributes['description'] = $value;
    }
    /**
     * Explicit Bundle expiration date/time. If not set, the site-wide expiration setting may apply.
     *
     * @return string|null
     */
    public function getExpiresAt()
    {
        return isset($this->attributes['expires_at']) ? $this->attributes['expires_at'] : null;
    }

    /**
     * Explicit Bundle expiration date/time. If not set, the site-wide expiration setting may apply.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setExpiresAt($value)
    {
        return $this->attributes['expires_at'] = $value;
    }
    /**
     * Is this bundle password protected?
     *
     * @return bool|null
     */
    public function getPasswordProtected()
    {
        return isset($this->attributes['password_protected']) ? $this->attributes['password_protected'] : null;
    }

    /**
     * Is this bundle password protected?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPasswordProtected($value)
    {
        return $this->attributes['password_protected'] = $value;
    }
    /**
     * Permissions that apply to Folders in this Share Link.
     *
     * @return string|null
     */
    public function getPermissions()
    {
        return isset($this->attributes['permissions']) ? $this->attributes['permissions'] : null;
    }

    /**
     * Permissions that apply to Folders in this Share Link.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPermissions($value)
    {
        return $this->attributes['permissions'] = $value;
    }
    /**
     * Read preview_only.
     *
     * @return bool|null
     */
    public function getPreviewOnly()
    {
        return isset($this->attributes['preview_only']) ? $this->attributes['preview_only'] : null;
    }

    /**
     * Set preview_only locally.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setPreviewOnly($value)
    {
        return $this->attributes['preview_only'] = $value;
    }
    /**
     * Show a registration page that captures the downloader's name and email address?
     *
     * @return bool|null
     */
    public function getRequireRegistration()
    {
        return isset($this->attributes['require_registration']) ? $this->attributes['require_registration'] : null;
    }

    /**
     * Show a registration page that captures the downloader's name and email address?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRequireRegistration($value)
    {
        return $this->attributes['require_registration'] = $value;
    }
    /**
     * Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
     *
     * @return bool|null
     */
    public function getRequireShareRecipient()
    {
        return isset($this->attributes['require_share_recipient']) ? $this->attributes['require_share_recipient'] : null;
    }

    /**
     * Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRequireShareRecipient($value)
    {
        return $this->attributes['require_share_recipient'] = $value;
    }
    /**
     * If true, we will hide the 'Remember Me' box on the Bundle registration page, requiring that the user logout and log back in every time they visit the page.
     *
     * @return bool|null
     */
    public function getRequireLogout()
    {
        return isset($this->attributes['require_logout']) ? $this->attributes['require_logout'] : null;
    }

    /**
     * If true, we will hide the 'Remember Me' box on the Bundle registration page, requiring that the user logout and log back in every time they visit the page.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRequireLogout($value)
    {
        return $this->attributes['require_logout'] = $value;
    }
    /**
     * Legal text that must be agreed to prior to accessing Bundle.
     *
     * @return string|null
     */
    public function getClickwrapBody()
    {
        return isset($this->attributes['clickwrap_body']) ? $this->attributes['clickwrap_body'] : null;
    }

    /**
     * Legal text that must be agreed to prior to accessing Bundle.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setClickwrapBody($value)
    {
        return $this->attributes['clickwrap_body'] = $value;
    }
    /**
     * Custom Form to use
     *
     * @return \stdClass|null
     */
    public function getFormFieldSet()
    {
        return isset($this->attributes['form_field_set']) ? $this->attributes['form_field_set'] : null;
    }

    /**
     * Custom Form to use
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setFormFieldSet($value)
    {
        return $this->attributes['form_field_set'] = $value;
    }
    /**
     * BundleRegistrations can be saved without providing name?
     *
     * @return bool|null
     */
    public function getSkipName()
    {
        return isset($this->attributes['skip_name']) ? $this->attributes['skip_name'] : null;
    }

    /**
     * BundleRegistrations can be saved without providing name?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipName($value)
    {
        return $this->attributes['skip_name'] = $value;
    }
    /**
     * BundleRegistrations can be saved without providing email?
     *
     * @return bool|null
     */
    public function getSkipEmail()
    {
        return isset($this->attributes['skip_email']) ? $this->attributes['skip_email'] : null;
    }

    /**
     * BundleRegistrations can be saved without providing email?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipEmail($value)
    {
        return $this->attributes['skip_email'] = $value;
    }
    /**
     * Date when share will start to be accessible. If `nil` access granted right after create.
     *
     * @return string|null
     */
    public function getStartAccessOnDate()
    {
        return isset($this->attributes['start_access_on_date']) ? $this->attributes['start_access_on_date'] : null;
    }

    /**
     * Date when share will start to be accessible. If `nil` access granted right after create.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStartAccessOnDate($value)
    {
        return $this->attributes['start_access_on_date'] = $value;
    }
    /**
     * BundleRegistrations can be saved without providing company?
     *
     * @return bool|null
     */
    public function getSkipCompany()
    {
        return isset($this->attributes['skip_company']) ? $this->attributes['skip_company'] : null;
    }

    /**
     * BundleRegistrations can be saved without providing company?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSkipCompany($value)
    {
        return $this->attributes['skip_company'] = $value;
    }
    /**
     * Bundle ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Bundle ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * If true, this Share Link bypasses site-wide expiration rules. Only site admins may set this.
     *
     * @return bool|null
     */
    public function getBypassesSiteExpirationRules()
    {
        return isset($this->attributes['bypasses_site_expiration_rules']) ? $this->attributes['bypasses_site_expiration_rules'] : null;
    }

    /**
     * If true, this Share Link bypasses site-wide expiration rules. Only site admins may set this.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBypassesSiteExpirationRules($value)
    {
        return $this->attributes['bypasses_site_expiration_rules'] = $value;
    }
    /**
     * Bundle created at date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * Indicates if the bundle has been deleted.
     *
     * @return bool|null
     */
    public function getDeleted()
    {
        return isset($this->attributes['deleted']) ? $this->attributes['deleted'] : null;
    }

    /**
     * Indicates if the bundle has been deleted.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDeleted($value)
    {
        return $this->attributes['deleted'] = $value;
    }
    /**
     * Bundle deleted at date/time
     *
     * @return string|null
     */
    public function getDeletedAt()
    {
        return isset($this->attributes['deleted_at']) ? $this->attributes['deleted_at'] : null;
    }

    /**
     * Bundle deleted at date/time
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDeletedAt($value)
    {
        return $this->attributes['deleted_at'] = $value;
    }
    /**
     * Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
     *
     * @return bool|null
     */
    public function getDontSeparateSubmissionsByFolder()
    {
        return isset($this->attributes['dont_separate_submissions_by_folder']) ? $this->attributes['dont_separate_submissions_by_folder'] : null;
    }

    /**
     * Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDontSeparateSubmissionsByFolder($value)
    {
        return $this->attributes['dont_separate_submissions_by_folder'] = $value;
    }
    /**
     * Read-only expiration date/time, using the explicit expiration or the site-wide setting when applicable. Null when the Share Link does not expire.
     *
     * @return string|null
     */
    public function getEffectiveExpiresAt()
    {
        return isset($this->attributes['effective_expires_at']) ? $this->attributes['effective_expires_at'] : null;
    }

    /**
     * Read-only expiration date/time, using the explicit expiration or the site-wide setting when applicable. Null when the Share Link does not expire.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setEffectiveExpiresAt($value)
    {
        return $this->attributes['effective_expires_at'] = $value;
    }
    /**
     * Maximum number of times bundle can be accessed
     *
     * @return int|null
     */
    public function getMaxUses()
    {
        return isset($this->attributes['max_uses']) ? $this->attributes['max_uses'] : null;
    }

    /**
     * Maximum number of times bundle can be accessed
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setMaxUses($value)
    {
        return $this->attributes['max_uses'] = $value;
    }
    /**
     * Internal name for identifying this Share Link.
     *
     * @return string|null
     */
    public function getInternalName()
    {
        return isset($this->attributes['internal_name']) ? $this->attributes['internal_name'] : null;
    }

    /**
     * Internal name for identifying this Share Link.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setInternalName($value)
    {
        return $this->attributes['internal_name'] = $value;
    }
    /**
     * Bundle internal note
     *
     * @return string|null
     */
    public function getNote()
    {
        return isset($this->attributes['note']) ? $this->attributes['note'] : null;
    }

    /**
     * Bundle internal note
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setNote($value)
    {
        return $this->attributes['note'] = $value;
    }
    /**
     * Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
     *
     * @return string|null
     */
    public function getPathTemplate()
    {
        return isset($this->attributes['path_template']) ? $this->attributes['path_template'] : null;
    }

    /**
     * Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPathTemplate($value)
    {
        return $this->attributes['path_template'] = $value;
    }
    /**
     * Timezone to use when rendering timestamps in path templates.
     *
     * @return string|null
     */
    public function getPathTemplateTimeZone()
    {
        return isset($this->attributes['path_template_time_zone']) ? $this->attributes['path_template_time_zone'] : null;
    }

    /**
     * Timezone to use when rendering timestamps in path templates.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPathTemplateTimeZone($value)
    {
        return $this->attributes['path_template_time_zone'] = $value;
    }
    /**
     * Send delivery receipt to the uploader. Note: For writable share only
     *
     * @return bool|null
     */
    public function getSendEmailReceiptToUploader()
    {
        return isset($this->attributes['send_email_receipt_to_uploader']) ? $this->attributes['send_email_receipt_to_uploader'] : null;
    }

    /**
     * Send delivery receipt to the uploader. Note: For writable share only
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSendEmailReceiptToUploader($value)
    {
        return $this->attributes['send_email_receipt_to_uploader'] = $value;
    }
    /**
     * ID of the snapshot containing this bundle's contents.
     *
     * @return int|null
     */
    public function getSnapshotId()
    {
        return isset($this->attributes['snapshot_id']) ? $this->attributes['snapshot_id'] : null;
    }

    /**
     * ID of the snapshot containing this bundle's contents.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSnapshotId($value)
    {
        return $this->attributes['snapshot_id'] = $value;
    }
    /**
     * Bundle creator user ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * Bundle creator user ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Bundle creator username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Bundle creator username
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * Owning group ID. If set, members of this group can view, edit, and share this Share Link.
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return isset($this->attributes['group_id']) ? $this->attributes['group_id'] : null;
    }

    /**
     * Owning group ID. If set, members of this group can view, edit, and share this Share Link.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setGroupId($value)
    {
        return $this->attributes['group_id'] = $value;
    }
    /**
     * ID of the clickwrap to use with this bundle.
     *
     * @return int|null
     */
    public function getClickwrapId()
    {
        return isset($this->attributes['clickwrap_id']) ? $this->attributes['clickwrap_id'] : null;
    }

    /**
     * ID of the clickwrap to use with this bundle.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setClickwrapId($value)
    {
        return $this->attributes['clickwrap_id'] = $value;
    }
    /**
     * ID of the associated inbox, if available.
     *
     * @return int|null
     */
    public function getInboxId()
    {
        return isset($this->attributes['inbox_id']) ? $this->attributes['inbox_id'] : null;
    }

    /**
     * ID of the associated inbox, if available.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setInboxId($value)
    {
        return $this->attributes['inbox_id'] = $value;
    }
    /**
     * Preview watermark image applied to all bundle items.
     *
     * @return \stdClass|null
     */
    public function getWatermarkAttachment()
    {
        return isset($this->attributes['watermark_attachment']) ? $this->attributes['watermark_attachment'] : null;
    }

    /**
     * Preview watermark image applied to all bundle items.
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setWatermarkAttachment($value)
    {
        return $this->attributes['watermark_attachment'] = $value;
    }
    /**
     * Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     *
     * @return object|null
     */
    public function getWatermarkValue()
    {
        return isset($this->attributes['watermark_value']) ? $this->attributes['watermark_value'] : null;
    }

    /**
     * Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setWatermarkValue($value)
    {
        return $this->attributes['watermark_value'] = $value;
    }
    /**
     * If true, require_share_recipient bundles will send a one-time password to the recipient when they register. Cannot be enabled if the bundle has a password set.
     *
     * @return bool|null
     */
    public function getSendOneTimePasswordToRecipientAtRegistration()
    {
        return isset($this->attributes['send_one_time_password_to_recipient_at_registration']) ? $this->attributes['send_one_time_password_to_recipient_at_registration'] : null;
    }

    /**
     * If true, require_share_recipient bundles will send a one-time password to the recipient when they register. Cannot be enabled if the bundle has a password set.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSendOneTimePasswordToRecipientAtRegistration($value)
    {
        return $this->attributes['send_one_time_password_to_recipient_at_registration'] = $value;
    }
    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @return int|null
     */
    public function getWorkspaceId()
    {
        return isset($this->attributes['workspace_id']) ? $this->attributes['workspace_id'] : null;
    }

    /**
     * Workspace ID. `0` means the default workspace.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setWorkspaceId($value)
    {
        return $this->attributes['workspace_id'] = $value;
    }
    /**
     * Does this bundle have an associated inbox?
     *
     * @return bool|null
     */
    public function getHasInbox()
    {
        return isset($this->attributes['has_inbox']) ? $this->attributes['has_inbox'] : null;
    }

    /**
     * Does this bundle have an associated inbox?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setHasInbox($value)
    {
        return $this->attributes['has_inbox'] = $value;
    }
    /**
     * Should folder uploads be prevented?
     *
     * @return bool|null
     */
    public function getDontAllowFoldersInUploads()
    {
        return isset($this->attributes['dont_allow_folders_in_uploads']) ? $this->attributes['dont_allow_folders_in_uploads'] : null;
    }

    /**
     * Should folder uploads be prevented?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setDontAllowFoldersInUploads($value)
    {
        return $this->attributes['dont_allow_folders_in_uploads'] = $value;
    }
    /**
     * Upload slots requested by the associated Inbox. Each slot contains a name used as its label and destination subfolder name.
     *
     * @return object[]|null
     */
    public function getRequestedUploadSlots()
    {
        return isset($this->attributes['requested_upload_slots']) ? $this->attributes['requested_upload_slots'] : null;
    }

    /**
     * Upload slots requested by the associated Inbox. Each slot contains a name used as its label and destination subfolder name.
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setRequestedUploadSlots($value)
    {
        return $this->attributes['requested_upload_slots'] = $value;
    }
    /**
     * A list of paths in this bundle.  For performance reasons, this is not provided when listing bundles.
     *
     * @return string[]|null
     */
    public function getPaths()
    {
        return isset($this->attributes['paths']) ? $this->attributes['paths'] : null;
    }

    /**
     * A list of paths in this bundle.  For performance reasons, this is not provided when listing bundles.
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setPaths($value)
    {
        return $this->attributes['paths'] = $value;
    }
    /**
     * A list of bundlepaths in this bundle.  For performance reasons, this is not provided when listing bundles.
     *
     * @return object[]|null
     */
    public function getBundlepaths()
    {
        return isset($this->attributes['bundlepaths']) ? $this->attributes['bundlepaths'] : null;
    }

    /**
     * A list of bundlepaths in this bundle.  For performance reasons, this is not provided when listing bundles.
     *
     * @param object[]|null $value
     * @return object[]|null The assigned value.
     */
    public function setBundlepaths($value)
    {
        return $this->attributes['bundlepaths'] = $value;
    }
    /**
     * Password for this bundle.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return isset($this->attributes['password']) ? $this->attributes['password'] : null;
    }

    /**
     * Password for this bundle.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPassword($value)
    {
        return $this->attributes['password'] = $value;
    }
    /**
     * Id of Form Field Set to use with this bundle
     *
     * @return int|null
     */
    public function getFormFieldSetId()
    {
        return isset($this->attributes['form_field_set_id']) ? $this->attributes['form_field_set_id'] : null;
    }

    /**
     * Id of Form Field Set to use with this bundle
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setFormFieldSetId($value)
    {
        return $this->attributes['form_field_set_id'] = $value;
    }
    /**
     * If true, create a snapshot of this bundle's contents.
     *
     * @return bool|null
     */
    public function getCreateSnapshot()
    {
        return isset($this->attributes['create_snapshot']) ? $this->attributes['create_snapshot'] : null;
    }

    /**
     * If true, create a snapshot of this bundle's contents.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setCreateSnapshot($value)
    {
        return $this->attributes['create_snapshot'] = $value;
    }
    /**
     * If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
     *
     * @return bool|null
     */
    public function getFinalizeSnapshot()
    {
        return isset($this->attributes['finalize_snapshot']) ? $this->attributes['finalize_snapshot'] : null;
    }

    /**
     * If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setFinalizeSnapshot($value)
    {
        return $this->attributes['finalize_snapshot'] = $value;
    }
    /**
     * Preview watermark image applied to all bundle items.
     *
     * @return mixed
     */
    public function getWatermarkAttachmentFile()
    {
        return isset($this->attributes['watermark_attachment_file']) ? $this->attributes['watermark_attachment_file'] : null;
    }

    /**
     * Preview watermark image applied to all bundle items.
     *
     * @param mixed $value
     * @return mixed The assigned value.
     */
    public function setWatermarkAttachmentFile($value)
    {
        return $this->attributes['watermark_attachment_file'] = $value;
    }
    /**
     * If true, will delete the file stored in watermark_attachment
     *
     * @return bool|null
     */
    public function getWatermarkAttachmentDelete()
    {
        return isset($this->attributes['watermark_attachment_delete']) ? $this->attributes['watermark_attachment_delete'] : null;
    }

    /**
     * If true, will delete the file stored in watermark_attachment
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setWatermarkAttachmentDelete($value)
    {
        return $this->attributes['watermark_attachment_delete'] = $value;
    }

    /**
     * Send email(s) with a link to bundle
     *
     * POST /bundles/{id}/share
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle ID.
     *     - `to` (string[], optional): A list of email addresses to share this bundle with. Required unless `recipients` is used.
     *     - `note` (string, optional): Note to include in email.
     *     - `recipients` ((array|object)[], optional): A list of recipients to share this bundle with. Required unless `to` is used.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function share($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['to']) && !is_array($params['to'])) {
            throw new \Files\Exception\InvalidParameterException('$to must be of type array; received ' . gettype(@$params['to']));
        }

        if (!empty($params['note']) && !is_string($params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (!empty($params['recipients']) && !is_array($params['recipients'])) {
            throw new \Files\Exception\InvalidParameterException('$recipients must be of type array; received ' . gettype(@$params['recipients']));
        }

        $response = Api::sendRequest('/bundles/' . rawurlencode(strval(@$params['id'])) . '/share', 'POST', $params, $this->options);
        return;
    }

    /**
     * Update Share Link
     *
     * PATCH /bundles/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle ID.
     *     - `paths` (string[], optional): A list of paths to include in this bundle.
     *     - `password` (string, optional): Password for this bundle.
     *     - `bypasses_site_expiration_rules` (bool, optional): If true, this Share Link bypasses site-wide expiration rules. Only site admins may set this.
     *     - `form_field_set_id` (int, optional): Id of Form Field Set to use with this bundle
     *     - `clickwrap_id` (int, optional): ID of the clickwrap to use with this bundle.
     *     - `code` (string, optional): Bundle code.  This code forms the end part of the Public URL.
     *     - `create_snapshot` (bool, optional): If true, create a snapshot of this bundle's contents.
     *     - `description` (string, optional): Public description
     *     - `dont_separate_submissions_by_folder` (bool, optional): Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
     *     - `expires_at` (string, optional): Explicit Bundle expiration date/time. If not set, the site-wide expiration setting may apply.
     *     - `finalize_snapshot` (bool, optional): If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
     *     - `inbox_id` (int, optional): ID of the associated inbox, if available.
     *     - `max_uses` (int, optional): Maximum number of times bundle can be accessed
     *     - `group_id` (int, optional): Owning group ID. If set, members of this group can view, edit, and share this Share Link.
     *     - `internal_name` (string, optional): Internal name for identifying this Share Link.
     *     - `note` (string, optional): Bundle internal note
     *     - `path_template` (string, optional): Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
     *     - `path_template_time_zone` (string, optional): Timezone to use when rendering timestamps in path templates.
     *     - `permissions` (string, optional): Permissions that apply to Folders in this Share Link. Allowed values: read, write, read_write, full, none, preview_only.
     *     - `require_registration` (bool, optional): Show a registration page that captures the downloader's name and email address?
     *     - `require_share_recipient` (bool, optional): Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
     *     - `send_one_time_password_to_recipient_at_registration` (bool, optional): If true, require_share_recipient bundles will send a one-time password to the recipient when they register. Cannot be enabled if the bundle has a password set.
     *     - `send_email_receipt_to_uploader` (bool, optional): Send delivery receipt to the uploader. Note: For writable share only
     *     - `skip_company` (bool, optional): BundleRegistrations can be saved without providing company?
     *     - `start_access_on_date` (string, optional): Date when share will start to be accessible. If `nil` access granted right after create.
     *     - `skip_email` (bool, optional): BundleRegistrations can be saved without providing email?
     *     - `skip_name` (bool, optional): BundleRegistrations can be saved without providing name?
     *     - `user_id` (int, optional): The owning user id. Only site admins can set this.
     *     - `watermark_attachment_delete` (bool, optional): If true, will delete the file stored in watermark_attachment
     *     - `watermark_attachment_file` (mixed, optional): Preview watermark image applied to all bundle items.
     *     - `watermark_value` (array|object, optional): Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\Bundle
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (!empty($params['paths']) && !is_array($params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['form_field_set_id']) && !is_int($params['form_field_set_id'])) {
            throw new \Files\Exception\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
        }

        if (!empty($params['clickwrap_id']) && !is_int($params['clickwrap_id'])) {
            throw new \Files\Exception\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
        }

        if (!empty($params['code']) && !is_string($params['code'])) {
            throw new \Files\Exception\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (!empty($params['inbox_id']) && !is_int($params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        if (!empty($params['max_uses']) && !is_int($params['max_uses'])) {
            throw new \Files\Exception\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['internal_name']) && !is_string($params['internal_name'])) {
            throw new \Files\Exception\InvalidParameterException('$internal_name must be of type string; received ' . gettype(@$params['internal_name']));
        }

        if (!empty($params['note']) && !is_string($params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (!empty($params['path_template']) && !is_string($params['path_template'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
        }

        if (!empty($params['path_template_time_zone']) && !is_string($params['path_template_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template_time_zone must be of type string; received ' . gettype(@$params['path_template_time_zone']));
        }

        if (!empty($params['permissions']) && !is_string($params['permissions'])) {
            throw new \Files\Exception\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
        }

        if (!empty($params['start_access_on_date']) && !is_string($params['start_access_on_date'])) {
            throw new \Files\Exception\InvalidParameterException('$start_access_on_date must be of type string; received ' . gettype(@$params['start_access_on_date']));
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/bundles/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        return new Bundle((array) (@$response->data ?: []), $this->options);
    }

    /**
     * Delete Share Link
     *
     * DELETE /bundles/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['id'])) {
            if (@$this->id) {
                $params['id'] = $this->id;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: id');
            }
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/bundles/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Share Link
     *
     * DELETE /bundles/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Bundle ID.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
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


    /**
     * List Share Links
     *
     * GET /bundles
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `expires_at`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `expires_at`, `code`, `group_id`, `user_id` or `bypasses_site_expiration_rules`. Valid field combinations are `[ group_id, expires_at ]` and `[ user_id, expires_at ]`.
     *     - `filter_gt` (array|object, optional): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at` and `expires_at`.
     *     - `filter_gteq` (array|object, optional): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at` and `expires_at`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `code`.
     *     - `filter_lt` (array|object, optional): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at` and `expires_at`.
     *     - `filter_lteq` (array|object, optional): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at` and `expires_at`.
     *     - `deleted` (bool, optional): If true, only list deleted Share Links.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Bundle[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/bundles', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new Bundle((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Show Share Link
     *
     * GET /bundles/{id}
     *
     * @param int $id Bundle ID.
     * @param array $params Request parameters.
     *     - `deleted` (bool, optional): If true, show a deleted Share Link.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Bundle
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($id, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['id'] = $id;

        if (empty($params['id'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: id');
        }

        if (!empty($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/bundles/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Bundle((array) (@$response->data ?: []), $options);
    }
    /**
     * Show Share Link
     *
     * GET /bundles/{id}
     *
     * Alias of find().
     *
     * @param int $id Bundle ID.
     * @param array $params Request parameters.
     *     - `deleted` (bool, optional): If true, show a deleted Share Link.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Bundle
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Share Link
     *
     * POST /bundles
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): User ID.  Provide a value of `0` to operate the current session's user.
     *     - `paths` (string[], required): A list of paths to include in this bundle.
     *     - `password` (string, optional): Password for this bundle.
     *     - `bypasses_site_expiration_rules` (bool, optional): If true, this Share Link bypasses site-wide expiration rules. Only site admins may set this.
     *     - `form_field_set_id` (int, optional): Id of Form Field Set to use with this bundle
     *     - `create_snapshot` (bool, optional): If true, create a snapshot of this bundle's contents.
     *     - `dont_separate_submissions_by_folder` (bool, optional): Do not create subfolders for files uploaded to this share. Note: there are subtle security pitfalls with allowing anonymous uploads from multiple users to live in the same folder. We strongly discourage use of this option unless absolutely required.
     *     - `expires_at` (string, optional): Explicit Bundle expiration date/time. If not set, the site-wide expiration setting may apply.
     *     - `finalize_snapshot` (bool, optional): If true, finalize the snapshot of this bundle's contents. Note that `create_snapshot` must also be true.
     *     - `max_uses` (int, optional): Maximum number of times bundle can be accessed
     *     - `group_id` (int, optional): Owning group ID. If set, members of this group can view, edit, and share this Share Link.
     *     - `internal_name` (string, optional): Internal name for identifying this Share Link.
     *     - `description` (string, optional): Public description
     *     - `note` (string, optional): Bundle internal note
     *     - `code` (string, optional): Bundle code.  This code forms the end part of the Public URL.
     *     - `path_template` (string, optional): Template for creating submission subfolders. Can use the uploader's name, email address, ip, company, `strftime` directives, and any custom form data.
     *     - `path_template_time_zone` (string, optional): Timezone to use when rendering timestamps in path templates.
     *     - `permissions` (string, optional): Permissions that apply to Folders in this Share Link. Allowed values: read, write, read_write, full, none, preview_only.
     *     - `require_registration` (bool, optional): Show a registration page that captures the downloader's name and email address?
     *     - `clickwrap_id` (int, optional): ID of the clickwrap to use with this bundle.
     *     - `inbox_id` (int, optional): ID of the associated inbox, if available.
     *     - `require_share_recipient` (bool, optional): Only allow access to recipients who have explicitly received the share via an email sent through the Files.com UI?
     *     - `send_one_time_password_to_recipient_at_registration` (bool, optional): If true, require_share_recipient bundles will send a one-time password to the recipient when they register. Cannot be enabled if the bundle has a password set.
     *     - `send_email_receipt_to_uploader` (bool, optional): Send delivery receipt to the uploader. Note: For writable share only
     *     - `skip_email` (bool, optional): BundleRegistrations can be saved without providing email?
     *     - `skip_name` (bool, optional): BundleRegistrations can be saved without providing name?
     *     - `skip_company` (bool, optional): BundleRegistrations can be saved without providing company?
     *     - `start_access_on_date` (string, optional): Date when share will start to be accessible. If `nil` access granted right after create.
     *     - `snapshot_id` (int, optional): ID of the snapshot containing this bundle's contents.
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     *     - `watermark_attachment_file` (mixed, optional): Preview watermark image applied to all bundle items.
     *     - `watermark_value` (array|object, optional): Preview watermark settings applied to all bundle items. Uses the same keys as Behavior.value
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Bundle
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (empty($params['paths'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: paths');
        }

        if (!empty($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (!empty($params['paths']) && !is_array($params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (!empty($params['password']) && !is_string($params['password'])) {
            throw new \Files\Exception\InvalidParameterException('$password must be of type string; received ' . gettype(@$params['password']));
        }

        if (!empty($params['form_field_set_id']) && !is_int($params['form_field_set_id'])) {
            throw new \Files\Exception\InvalidParameterException('$form_field_set_id must be of type int; received ' . gettype(@$params['form_field_set_id']));
        }

        if (!empty($params['expires_at']) && !is_string($params['expires_at'])) {
            throw new \Files\Exception\InvalidParameterException('$expires_at must be of type string; received ' . gettype(@$params['expires_at']));
        }

        if (!empty($params['max_uses']) && !is_int($params['max_uses'])) {
            throw new \Files\Exception\InvalidParameterException('$max_uses must be of type int; received ' . gettype(@$params['max_uses']));
        }

        if (!empty($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (!empty($params['internal_name']) && !is_string($params['internal_name'])) {
            throw new \Files\Exception\InvalidParameterException('$internal_name must be of type string; received ' . gettype(@$params['internal_name']));
        }

        if (!empty($params['description']) && !is_string($params['description'])) {
            throw new \Files\Exception\InvalidParameterException('$description must be of type string; received ' . gettype(@$params['description']));
        }

        if (!empty($params['note']) && !is_string($params['note'])) {
            throw new \Files\Exception\InvalidParameterException('$note must be of type string; received ' . gettype(@$params['note']));
        }

        if (!empty($params['code']) && !is_string($params['code'])) {
            throw new \Files\Exception\InvalidParameterException('$code must be of type string; received ' . gettype(@$params['code']));
        }

        if (!empty($params['path_template']) && !is_string($params['path_template'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template must be of type string; received ' . gettype(@$params['path_template']));
        }

        if (!empty($params['path_template_time_zone']) && !is_string($params['path_template_time_zone'])) {
            throw new \Files\Exception\InvalidParameterException('$path_template_time_zone must be of type string; received ' . gettype(@$params['path_template_time_zone']));
        }

        if (!empty($params['permissions']) && !is_string($params['permissions'])) {
            throw new \Files\Exception\InvalidParameterException('$permissions must be of type string; received ' . gettype(@$params['permissions']));
        }

        if (!empty($params['clickwrap_id']) && !is_int($params['clickwrap_id'])) {
            throw new \Files\Exception\InvalidParameterException('$clickwrap_id must be of type int; received ' . gettype(@$params['clickwrap_id']));
        }

        if (!empty($params['inbox_id']) && !is_int($params['inbox_id'])) {
            throw new \Files\Exception\InvalidParameterException('$inbox_id must be of type int; received ' . gettype(@$params['inbox_id']));
        }

        if (!empty($params['start_access_on_date']) && !is_string($params['start_access_on_date'])) {
            throw new \Files\Exception\InvalidParameterException('$start_access_on_date must be of type string; received ' . gettype(@$params['start_access_on_date']));
        }

        if (!empty($params['snapshot_id']) && !is_int($params['snapshot_id'])) {
            throw new \Files\Exception\InvalidParameterException('$snapshot_id must be of type int; received ' . gettype(@$params['snapshot_id']));
        }

        if (!empty($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/bundles', 'POST', $params, $options);

        return new Bundle((array) (@$response->data ?: []), $options);
    }
}
