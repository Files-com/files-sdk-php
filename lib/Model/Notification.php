<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A Notification is our feature that sends E-Mails when specific actions occur in the folder.
 *
 * @property int|null $id Notification ID
 * @property string|null $path Folder path to notify on. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $group_id ID of Group to receive notifications
 * @property string|null $group_name Group name, if a Group ID is set
 * @property int[]|null $group_ids Group IDs when the notification requires multiple groups
 * @property string[]|null $group_names Group names when the notification requires multiple groups
 * @property int[]|null $triggering_group_ids If set, will only notify on actions made by a member of one of the specified groups
 * @property int[]|null $triggering_user_ids If set, will only notify on actions made one of the specified users
 * @property bool|null $trigger_by_share_recipients Notify when actions are performed by a share recipient?
 * @property bool|null $notify_user_actions If true, will send notifications about a user's own activity to that user. If false, only activity performed by other users (or anonymous users) will be sent in notifications.
 * @property bool|null $notify_on_copy Trigger on files copied to this path?
 * @property bool|null $notify_on_delete Trigger on files deleted in this path?
 * @property bool|null $notify_on_download Trigger on files downloaded in this path?
 * @property bool|null $notify_on_move Trigger on files moved to this path?
 * @property bool|null $notify_on_upload Trigger on files created/uploaded/updated/changed in this path?
 * @property bool|null $recursive Apply notification recursively? This will enable notifications for each subfolder.
 * @property string|null $send_interval The time interval that notifications are aggregated to
 * @property string|null $subject Custom subject line to use for notification emails
 * @property string|null $message Custom message to include in notification emails
 * @property string[]|null $triggering_filenames Array of filenames (possibly with wildcards) to scope trigger
 * @property int|null $workspace_id Workspace ID. `0` means the default workspace.
 * @property bool|null $unsubscribed Is the user unsubscribed from this notification?
 * @property string|null $unsubscribed_reason The reason that the user unsubscribed
 * @property int|null $user_id Notification user ID
 * @property string|null $username Notification username
 * @property bool|null $suppressed_email If true, it means that the recipient at this user's email address has manually unsubscribed from all emails, or had their email "hard bounce", which means that we are unable to send mail to this user's current email address. Notifications will resume if the user changes their email address.
 * @package Files
 */
class Notification
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
     * Notification ID
     *
     * @return int|null
     */
    public function getId()
    {
        return isset($this->attributes['id']) ? $this->attributes['id'] : null;
    }

    /**
     * Notification ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setId($value)
    {
        return $this->attributes['id'] = $value;
    }
    /**
     * Folder path to notify on. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * Folder path to notify on. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * ID of Group to receive notifications
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return isset($this->attributes['group_id']) ? $this->attributes['group_id'] : null;
    }

    /**
     * ID of Group to receive notifications
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setGroupId($value)
    {
        return $this->attributes['group_id'] = $value;
    }
    /**
     * Group name, if a Group ID is set
     *
     * @return string|null
     */
    public function getGroupName()
    {
        return isset($this->attributes['group_name']) ? $this->attributes['group_name'] : null;
    }

    /**
     * Group name, if a Group ID is set
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setGroupName($value)
    {
        return $this->attributes['group_name'] = $value;
    }
    /**
     * Group IDs when the notification requires multiple groups
     *
     * @return int[]|null
     */
    public function getGroupIds()
    {
        return isset($this->attributes['group_ids']) ? $this->attributes['group_ids'] : null;
    }

    /**
     * Group IDs when the notification requires multiple groups
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setGroupIds($value)
    {
        return $this->attributes['group_ids'] = $value;
    }
    /**
     * Group names when the notification requires multiple groups
     *
     * @return string[]|null
     */
    public function getGroupNames()
    {
        return isset($this->attributes['group_names']) ? $this->attributes['group_names'] : null;
    }

    /**
     * Group names when the notification requires multiple groups
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setGroupNames($value)
    {
        return $this->attributes['group_names'] = $value;
    }
    /**
     * If set, will only notify on actions made by a member of one of the specified groups
     *
     * @return int[]|null
     */
    public function getTriggeringGroupIds()
    {
        return isset($this->attributes['triggering_group_ids']) ? $this->attributes['triggering_group_ids'] : null;
    }

    /**
     * If set, will only notify on actions made by a member of one of the specified groups
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setTriggeringGroupIds($value)
    {
        return $this->attributes['triggering_group_ids'] = $value;
    }
    /**
     * If set, will only notify on actions made one of the specified users
     *
     * @return int[]|null
     */
    public function getTriggeringUserIds()
    {
        return isset($this->attributes['triggering_user_ids']) ? $this->attributes['triggering_user_ids'] : null;
    }

    /**
     * If set, will only notify on actions made one of the specified users
     *
     * @param int[]|null $value
     * @return int[]|null The assigned value.
     */
    public function setTriggeringUserIds($value)
    {
        return $this->attributes['triggering_user_ids'] = $value;
    }
    /**
     * Notify when actions are performed by a share recipient?
     *
     * @return bool|null
     */
    public function getTriggerByShareRecipients()
    {
        return isset($this->attributes['trigger_by_share_recipients']) ? $this->attributes['trigger_by_share_recipients'] : null;
    }

    /**
     * Notify when actions are performed by a share recipient?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setTriggerByShareRecipients($value)
    {
        return $this->attributes['trigger_by_share_recipients'] = $value;
    }
    /**
     * If true, will send notifications about a user's own activity to that user.  If false, only activity performed by other users (or anonymous users) will be sent in notifications.
     *
     * @return bool|null
     */
    public function getNotifyUserActions()
    {
        return isset($this->attributes['notify_user_actions']) ? $this->attributes['notify_user_actions'] : null;
    }

    /**
     * If true, will send notifications about a user's own activity to that user.  If false, only activity performed by other users (or anonymous users) will be sent in notifications.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyUserActions($value)
    {
        return $this->attributes['notify_user_actions'] = $value;
    }
    /**
     * Trigger on files copied to this path?
     *
     * @return bool|null
     */
    public function getNotifyOnCopy()
    {
        return isset($this->attributes['notify_on_copy']) ? $this->attributes['notify_on_copy'] : null;
    }

    /**
     * Trigger on files copied to this path?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnCopy($value)
    {
        return $this->attributes['notify_on_copy'] = $value;
    }
    /**
     * Trigger on files deleted in this path?
     *
     * @return bool|null
     */
    public function getNotifyOnDelete()
    {
        return isset($this->attributes['notify_on_delete']) ? $this->attributes['notify_on_delete'] : null;
    }

    /**
     * Trigger on files deleted in this path?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnDelete($value)
    {
        return $this->attributes['notify_on_delete'] = $value;
    }
    /**
     * Trigger on files downloaded in this path?
     *
     * @return bool|null
     */
    public function getNotifyOnDownload()
    {
        return isset($this->attributes['notify_on_download']) ? $this->attributes['notify_on_download'] : null;
    }

    /**
     * Trigger on files downloaded in this path?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnDownload($value)
    {
        return $this->attributes['notify_on_download'] = $value;
    }
    /**
     * Trigger on files moved to this path?
     *
     * @return bool|null
     */
    public function getNotifyOnMove()
    {
        return isset($this->attributes['notify_on_move']) ? $this->attributes['notify_on_move'] : null;
    }

    /**
     * Trigger on files moved to this path?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnMove($value)
    {
        return $this->attributes['notify_on_move'] = $value;
    }
    /**
     * Trigger on files created/uploaded/updated/changed in this path?
     *
     * @return bool|null
     */
    public function getNotifyOnUpload()
    {
        return isset($this->attributes['notify_on_upload']) ? $this->attributes['notify_on_upload'] : null;
    }

    /**
     * Trigger on files created/uploaded/updated/changed in this path?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setNotifyOnUpload($value)
    {
        return $this->attributes['notify_on_upload'] = $value;
    }
    /**
     * Apply notification recursively?  This will enable notifications for each subfolder.
     *
     * @return bool|null
     */
    public function getRecursive()
    {
        return isset($this->attributes['recursive']) ? $this->attributes['recursive'] : null;
    }

    /**
     * Apply notification recursively?  This will enable notifications for each subfolder.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setRecursive($value)
    {
        return $this->attributes['recursive'] = $value;
    }
    /**
     * The time interval that notifications are aggregated to
     *
     * @return string|null
     */
    public function getSendInterval()
    {
        return isset($this->attributes['send_interval']) ? $this->attributes['send_interval'] : null;
    }

    /**
     * The time interval that notifications are aggregated to
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSendInterval($value)
    {
        return $this->attributes['send_interval'] = $value;
    }
    /**
     * Custom subject line to use for notification emails
     *
     * @return string|null
     */
    public function getSubject()
    {
        return isset($this->attributes['subject']) ? $this->attributes['subject'] : null;
    }

    /**
     * Custom subject line to use for notification emails
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSubject($value)
    {
        return $this->attributes['subject'] = $value;
    }
    /**
     * Custom message to include in notification emails
     *
     * @return string|null
     */
    public function getMessage()
    {
        return isset($this->attributes['message']) ? $this->attributes['message'] : null;
    }

    /**
     * Custom message to include in notification emails
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMessage($value)
    {
        return $this->attributes['message'] = $value;
    }
    /**
     * Array of filenames (possibly with wildcards) to scope trigger
     *
     * @return string[]|null
     */
    public function getTriggeringFilenames()
    {
        return isset($this->attributes['triggering_filenames']) ? $this->attributes['triggering_filenames'] : null;
    }

    /**
     * Array of filenames (possibly with wildcards) to scope trigger
     *
     * @param string[]|null $value
     * @return string[]|null The assigned value.
     */
    public function setTriggeringFilenames($value)
    {
        return $this->attributes['triggering_filenames'] = $value;
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
     * Is the user unsubscribed from this notification?
     *
     * @return bool|null
     */
    public function getUnsubscribed()
    {
        return isset($this->attributes['unsubscribed']) ? $this->attributes['unsubscribed'] : null;
    }

    /**
     * Is the user unsubscribed from this notification?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setUnsubscribed($value)
    {
        return $this->attributes['unsubscribed'] = $value;
    }
    /**
     * The reason that the user unsubscribed
     *
     * @return string|null
     */
    public function getUnsubscribedReason()
    {
        return isset($this->attributes['unsubscribed_reason']) ? $this->attributes['unsubscribed_reason'] : null;
    }

    /**
     * The reason that the user unsubscribed
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUnsubscribedReason($value)
    {
        return $this->attributes['unsubscribed_reason'] = $value;
    }
    /**
     * Notification user ID
     *
     * @return int|null
     */
    public function getUserId()
    {
        return isset($this->attributes['user_id']) ? $this->attributes['user_id'] : null;
    }

    /**
     * Notification user ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setUserId($value)
    {
        return $this->attributes['user_id'] = $value;
    }
    /**
     * Notification username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return isset($this->attributes['username']) ? $this->attributes['username'] : null;
    }

    /**
     * Notification username
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setUsername($value)
    {
        return $this->attributes['username'] = $value;
    }
    /**
     * If true, it means that the recipient at this user's email address has manually unsubscribed from all emails, or had their email "hard bounce", which means that we are unable to send mail to this user's current email address. Notifications will resume if the user changes their email address.
     *
     * @return bool|null
     */
    public function getSuppressedEmail()
    {
        return isset($this->attributes['suppressed_email']) ? $this->attributes['suppressed_email'] : null;
    }

    /**
     * If true, it means that the recipient at this user's email address has manually unsubscribed from all emails, or had their email "hard bounce", which means that we are unable to send mail to this user's current email address. Notifications will resume if the user changes their email address.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSuppressedEmail($value)
    {
        return $this->attributes['suppressed_email'] = $value;
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // Notification with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update Notification
     *
     * PATCH /notifications/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Notification ID.
     *     - `notify_on_copy` (bool, optional): If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
     *     - `notify_on_delete` (bool, optional): Trigger on files deleted in this path?
     *     - `notify_on_download` (bool, optional): Trigger on files downloaded in this path?
     *     - `notify_on_move` (bool, optional): Trigger on files moved to this path?
     *     - `notify_on_upload` (bool, optional): Trigger on files created/uploaded/updated/changed in this path?
     *     - `notify_user_actions` (bool, optional): If `true` actions initiated by the user will still result in a notification
     *     - `recursive` (bool, optional): If `true`, enable notifications for each subfolder in this path
     *     - `send_interval` (string, optional): The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
     *     - `subject` (string, optional): Custom subject line to use for notification emails
     *     - `message` (string, optional): Custom message to include in notification emails
     *     - `triggering_filenames` (string[], optional): Array of filenames (possibly with wildcards) to scope trigger
     *     - `triggering_group_ids` (int[], optional): If set, will only notify on actions made by a member of one of the specified groups
     *     - `triggering_user_ids` (int[], optional): If set, will only notify on actions made one of the specified users
     *     - `trigger_by_share_recipients` (bool, optional): Notify when actions are performed by a share recipient?
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     * @return \Files\Model\Notification
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        if (isset($params['notify_on_copy']) && !is_scalar($params['notify_on_copy'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_copy must be of type boolean; received ' . gettype(@$params['notify_on_copy']));
        }

        if (isset($params['notify_on_delete']) && !is_scalar($params['notify_on_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_delete must be of type boolean; received ' . gettype(@$params['notify_on_delete']));
        }

        if (isset($params['notify_on_download']) && !is_scalar($params['notify_on_download'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_download must be of type boolean; received ' . gettype(@$params['notify_on_download']));
        }

        if (isset($params['notify_on_move']) && !is_scalar($params['notify_on_move'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_move must be of type boolean; received ' . gettype(@$params['notify_on_move']));
        }

        if (isset($params['notify_on_upload']) && !is_scalar($params['notify_on_upload'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_upload must be of type boolean; received ' . gettype(@$params['notify_on_upload']));
        }

        if (isset($params['notify_user_actions']) && !is_scalar($params['notify_user_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_user_actions must be of type boolean; received ' . gettype(@$params['notify_user_actions']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        if (isset($params['send_interval']) && !is_string($params['send_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$send_interval must be of type string; received ' . gettype(@$params['send_interval']));
        }

        if (isset($params['subject']) && !is_string($params['subject'])) {
            throw new \Files\Exception\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
        }

        if (isset($params['message']) && !is_string($params['message'])) {
            throw new \Files\Exception\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
        }

        if (isset($params['triggering_filenames']) && !is_array($params['triggering_filenames'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_filenames must be of type array; received ' . gettype(@$params['triggering_filenames']));
        }

        if (isset($params['triggering_group_ids']) && !is_array($params['triggering_group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_group_ids must be of type array; received ' . gettype(@$params['triggering_group_ids']));
        }

        if (isset($params['triggering_user_ids']) && !is_array($params['triggering_user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_user_ids must be of type array; received ' . gettype(@$params['triggering_user_ids']));
        }

        if (isset($params['trigger_by_share_recipients']) && !is_scalar($params['trigger_by_share_recipients'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_by_share_recipients must be of type boolean; received ' . gettype(@$params['trigger_by_share_recipients']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        $response = Api::sendRequest('/notifications/' . rawurlencode(strval(@$params['id'])) . '', 'PATCH', $params, $this->options);
        $updated = new Notification(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete Notification
     *
     * DELETE /notifications/{id}
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Notification ID.
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/notifications/' . rawurlencode(strval(@$params['id'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete Notification
     *
     * DELETE /notifications/{id}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `id` (int, optional when set on this model): Notification ID.
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
     * List Notifications
     *
     * GET /notifications
     *
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `sort_by` (array|object, optional): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `workspace_id`, `path`, `user_id` or `group_id`.
     *     - `filter` (array|object, optional): If set, return records where the specified field is equal to the supplied value. Valid fields are `path`, `user_id`, `workspace_id` or `group_id`. Valid field combinations are `[ workspace_id, path ]`, `[ workspace_id, user_id ]`, `[ workspace_id, group_id ]` or `[ workspace_id, user_id, path ]`.
     *     - `filter_prefix` (array|object, optional): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `path`.
     *     - `path` (string, optional): Show notifications for this Path.
     *     - `include_ancestors` (bool, optional): If `include_ancestors` is `true` and `path` is specified, include notifications for any parent paths. Ignored if `path` is not specified.
     *     - `group_id` (string, optional):
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Notification[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function all($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['include_ancestors']) && !is_scalar($params['include_ancestors'])) {
            throw new \Files\Exception\InvalidParameterException('$include_ancestors must be of type boolean; received ' . gettype(@$params['include_ancestors']));
        }

        if (isset($params['group_id']) && !is_string($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type string; received ' . gettype(@$params['group_id']));
        }

        $response = Api::sendRequest('/notifications', 'GET', $params, $options);

        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new Notification(Api::modelAttributes($obj, $response->status), $options);
        }

        return $return_array;
    }

    /**
     * Fetches one page of all()'s results, with the same parameters and options,
     * whatever Files::$autoPaginate is. Iterate the page, step with nextPage(), or read every result lazily with
     * autoPagingIterator(). A 'cursor' parameter starts from a page saved with getNextCursor().
     *
     * @return \Files\Page
     */
    public static function listPage($params = [], $options = [])
    {
        if (isset($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (isset($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['include_ancestors']) && !is_scalar($params['include_ancestors'])) {
            throw new \Files\Exception\InvalidParameterException('$include_ancestors must be of type boolean; received ' . gettype(@$params['include_ancestors']));
        }

        if (isset($params['group_id']) && !is_string($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type string; received ' . gettype(@$params['group_id']));
        }

        return \Files\Page::fetch('/notifications', $params, $options, Notification::class);
    }

    /**
     * Show Notification
     *
     * GET /notifications/{id}
     *
     * @param int $id Notification ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Notification
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

        if (isset($params['id']) && !is_int($params['id'])) {
            throw new \Files\Exception\InvalidParameterException('$id must be of type int; received ' . gettype(@$params['id']));
        }

        $response = Api::sendRequest('/notifications/' . rawurlencode(strval(@$params['id'])) . '', 'GET', $params, $options);

        return new Notification(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Show Notification
     *
     * GET /notifications/{id}
     *
     * Alias of find().
     *
     * @param int $id Notification ID.
     * @param array $params Request parameters.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Notification
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($id, $params = [], $options = [])
    {
        return self::find($id, $params, $options);
    }

    /**
     * Create Notification
     *
     * POST /notifications
     *
     * @param array $params Request parameters.
     *     - `user_id` (int, optional): The id of the user to notify. Provide `user_id`, `username` or `group_id`.
     *     - `notify_on_copy` (bool, optional): If `true`, copying or moving resources into this path will trigger a notification, in addition to just uploads.
     *     - `notify_on_delete` (bool, optional): Trigger on files deleted in this path?
     *     - `notify_on_download` (bool, optional): Trigger on files downloaded in this path?
     *     - `notify_on_move` (bool, optional): Trigger on files moved to this path?
     *     - `notify_on_upload` (bool, optional): Trigger on files created/uploaded/updated/changed in this path?
     *     - `notify_user_actions` (bool, optional): If `true` actions initiated by the user will still result in a notification
     *     - `recursive` (bool, optional): If `true`, enable notifications for each subfolder in this path
     *     - `send_interval` (string, optional): The time interval that notifications are aggregated by.  Can be `five_minutes`, `fifteen_minutes`, `hourly`, or `daily`.
     *     - `subject` (string, optional): Custom subject line to use for notification emails
     *     - `message` (string, optional): Custom message to include in notification emails
     *     - `triggering_filenames` (string[], optional): Array of filenames (possibly with wildcards) to scope trigger
     *     - `triggering_group_ids` (int[], optional): If set, will only notify on actions made by a member of one of the specified groups
     *     - `triggering_user_ids` (int[], optional): If set, will only notify on actions made one of the specified users
     *     - `trigger_by_share_recipients` (bool, optional): Notify when actions are performed by a share recipient?
     *     - `workspace_id` (int, optional): Workspace ID. `0` means the default workspace.
     *     - `group_id` (int, optional): The ID of the group to notify.  Provide `user_id`, `username` or `group_id`.
     *     - `group_ids` (string, optional): Group IDs when the notification requires multiple groups. If sent as a string, it should be comma-delimited.
     *     - `path` (string, optional): Path
     *     - `username` (string, optional): The username of the user to notify.  Provide `user_id`, `username` or `group_id`.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\Notification
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($params = [], $options = [])
    {
        if (isset($params['user_id']) && !is_int($params['user_id'])) {
            throw new \Files\Exception\InvalidParameterException('$user_id must be of type int; received ' . gettype(@$params['user_id']));
        }

        if (isset($params['notify_on_copy']) && !is_scalar($params['notify_on_copy'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_copy must be of type boolean; received ' . gettype(@$params['notify_on_copy']));
        }

        if (isset($params['notify_on_delete']) && !is_scalar($params['notify_on_delete'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_delete must be of type boolean; received ' . gettype(@$params['notify_on_delete']));
        }

        if (isset($params['notify_on_download']) && !is_scalar($params['notify_on_download'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_download must be of type boolean; received ' . gettype(@$params['notify_on_download']));
        }

        if (isset($params['notify_on_move']) && !is_scalar($params['notify_on_move'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_move must be of type boolean; received ' . gettype(@$params['notify_on_move']));
        }

        if (isset($params['notify_on_upload']) && !is_scalar($params['notify_on_upload'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_on_upload must be of type boolean; received ' . gettype(@$params['notify_on_upload']));
        }

        if (isset($params['notify_user_actions']) && !is_scalar($params['notify_user_actions'])) {
            throw new \Files\Exception\InvalidParameterException('$notify_user_actions must be of type boolean; received ' . gettype(@$params['notify_user_actions']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        if (isset($params['send_interval']) && !is_string($params['send_interval'])) {
            throw new \Files\Exception\InvalidParameterException('$send_interval must be of type string; received ' . gettype(@$params['send_interval']));
        }

        if (isset($params['subject']) && !is_string($params['subject'])) {
            throw new \Files\Exception\InvalidParameterException('$subject must be of type string; received ' . gettype(@$params['subject']));
        }

        if (isset($params['message']) && !is_string($params['message'])) {
            throw new \Files\Exception\InvalidParameterException('$message must be of type string; received ' . gettype(@$params['message']));
        }

        if (isset($params['triggering_filenames']) && !is_array($params['triggering_filenames'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_filenames must be of type array; received ' . gettype(@$params['triggering_filenames']));
        }

        if (isset($params['triggering_group_ids']) && !is_array($params['triggering_group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_group_ids must be of type array; received ' . gettype(@$params['triggering_group_ids']));
        }

        if (isset($params['triggering_user_ids']) && !is_array($params['triggering_user_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$triggering_user_ids must be of type array; received ' . gettype(@$params['triggering_user_ids']));
        }

        if (isset($params['trigger_by_share_recipients']) && !is_scalar($params['trigger_by_share_recipients'])) {
            throw new \Files\Exception\InvalidParameterException('$trigger_by_share_recipients must be of type boolean; received ' . gettype(@$params['trigger_by_share_recipients']));
        }

        if (isset($params['workspace_id']) && !is_int($params['workspace_id'])) {
            throw new \Files\Exception\InvalidParameterException('$workspace_id must be of type int; received ' . gettype(@$params['workspace_id']));
        }

        if (isset($params['group_id']) && !is_int($params['group_id'])) {
            throw new \Files\Exception\InvalidParameterException('$group_id must be of type int; received ' . gettype(@$params['group_id']));
        }

        if (isset($params['group_ids']) && !is_string($params['group_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$group_ids must be of type string; received ' . gettype(@$params['group_ids']));
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['username']) && !is_string($params['username'])) {
            throw new \Files\Exception\InvalidParameterException('$username must be of type string; received ' . gettype(@$params['username']));
        }

        $response = Api::sendRequest('/notifications', 'POST', $params, $options);

        return new Notification(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
