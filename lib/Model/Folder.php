<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * A File object represents a file or folder on your Files.com site.
 *
 * @property string|null $path File/Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
 * @property int|null $created_by_id User ID of the User who created the file/folder
 * @property int|null $created_by_api_key_id ID of the API key that created the file/folder
 * @property int|null $created_by_as2_incoming_message_id ID of the AS2 Incoming Message that created the file/folder
 * @property int|null $created_by_automation_id ID of the Automation that created the file/folder
 * @property int|null $created_by_bundle_registration_id ID of the Bundle Registration that created the file/folder
 * @property int|null $created_by_inbox_id ID of the Inbox that created the file/folder
 * @property int|null $created_by_remote_server_id ID of the Remote Server that created the file/folder
 * @property int|null $created_by_sync_id ID of the Sync that created the file/folder
 * @property object|null $custom_metadata Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
 * @property string|null $display_name File/Folder display name
 * @property string|null $type Type: `directory` or `file`.
 * @property int|null $size File/Folder size
 * @property string|null $created_at File created date/time
 * @property int|null $last_modified_by_id User ID of the User who last modified the file/folder
 * @property int|null $last_modified_by_api_key_id ID of the API key that last modified the file/folder
 * @property int|null $last_modified_by_automation_id ID of the Automation that last modified the file/folder
 * @property int|null $last_modified_by_bundle_registration_id ID of the Bundle Registration that last modified the file/folder
 * @property int|null $last_modified_by_remote_server_id ID of the Remote Server that last modified the file/folder
 * @property int|null $last_modified_by_sync_id ID of the Sync that last modified the file/folder
 * @property string|null $mtime File last modified date/time, according to the server. This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
 * @property string|null $provided_mtime File last modified date/time, according to the client who set it. Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times. This allows Desktop<->Cloud syncing to preserve modified at times.
 * @property string|null $crc32 File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
 * @property string|null $md5 File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
 * @property string|null $sha1 File SHA1 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
 * @property string|null $sha256 File SHA256 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
 * @property string|null $mime_type MIME Type. This is determined by the filename extension and is not stored separately internally.
 * @property string|null $region Region location
 * @property string|null $permissions A short string representing the current user's permissions. Can be `r` (Read),`w` (Write),`d` (Delete), `l` (List) or any combination
 * @property bool|null $subfolders_locked Are subfolders locked and unable to be modified?
 * @property bool|null $is_locked Is this folder locked and unable to be modified?
 * @property string|null $download_uri Link to download file. Provided only in response to a download request.
 * @property \stdClass|null $direct_connection_info Optional direct connection information for direct Agent transfer attempts
 * @property string|null $priority_color Bookmark/priority color of file/folder
 * @property int|null $preview_id File preview ID
 * @property \stdClass|null $preview File preview
 * @property bool|null $mkdir_parents Create parent directories if they do not exist?
 * @package Files
 */
class Folder
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
        return !!@$this->attributes['path'];
    }
    /**
     * File/Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @return string|null
     */
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    /**
     * File/Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    /**
     * User ID of the User who created the file/folder
     *
     * @return int|null
     */
    public function getCreatedById()
    {
        return isset($this->attributes['created_by_id']) ? $this->attributes['created_by_id'] : null;
    }

    /**
     * User ID of the User who created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedById($value)
    {
        return $this->attributes['created_by_id'] = $value;
    }
    /**
     * ID of the API key that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByApiKeyId()
    {
        return isset($this->attributes['created_by_api_key_id']) ? $this->attributes['created_by_api_key_id'] : null;
    }

    /**
     * ID of the API key that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByApiKeyId($value)
    {
        return $this->attributes['created_by_api_key_id'] = $value;
    }
    /**
     * ID of the AS2 Incoming Message that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByAs2IncomingMessageId()
    {
        return isset($this->attributes['created_by_as2_incoming_message_id']) ? $this->attributes['created_by_as2_incoming_message_id'] : null;
    }

    /**
     * ID of the AS2 Incoming Message that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByAs2IncomingMessageId($value)
    {
        return $this->attributes['created_by_as2_incoming_message_id'] = $value;
    }
    /**
     * ID of the Automation that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByAutomationId()
    {
        return isset($this->attributes['created_by_automation_id']) ? $this->attributes['created_by_automation_id'] : null;
    }

    /**
     * ID of the Automation that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByAutomationId($value)
    {
        return $this->attributes['created_by_automation_id'] = $value;
    }
    /**
     * ID of the Bundle Registration that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByBundleRegistrationId()
    {
        return isset($this->attributes['created_by_bundle_registration_id']) ? $this->attributes['created_by_bundle_registration_id'] : null;
    }

    /**
     * ID of the Bundle Registration that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByBundleRegistrationId($value)
    {
        return $this->attributes['created_by_bundle_registration_id'] = $value;
    }
    /**
     * ID of the Inbox that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByInboxId()
    {
        return isset($this->attributes['created_by_inbox_id']) ? $this->attributes['created_by_inbox_id'] : null;
    }

    /**
     * ID of the Inbox that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByInboxId($value)
    {
        return $this->attributes['created_by_inbox_id'] = $value;
    }
    /**
     * ID of the Remote Server that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedByRemoteServerId()
    {
        return isset($this->attributes['created_by_remote_server_id']) ? $this->attributes['created_by_remote_server_id'] : null;
    }

    /**
     * ID of the Remote Server that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedByRemoteServerId($value)
    {
        return $this->attributes['created_by_remote_server_id'] = $value;
    }
    /**
     * ID of the Sync that created the file/folder
     *
     * @return int|null
     */
    public function getCreatedBySyncId()
    {
        return isset($this->attributes['created_by_sync_id']) ? $this->attributes['created_by_sync_id'] : null;
    }

    /**
     * ID of the Sync that created the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setCreatedBySyncId($value)
    {
        return $this->attributes['created_by_sync_id'] = $value;
    }
    /**
     * Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
     *
     * @return object|null
     */
    public function getCustomMetadata()
    {
        return isset($this->attributes['custom_metadata']) ? $this->attributes['custom_metadata'] : null;
    }

    /**
     * Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
     *
     * @param object|null $value
     * @return object|null The assigned value.
     */
    public function setCustomMetadata($value)
    {
        return $this->attributes['custom_metadata'] = $value;
    }
    /**
     * File/Folder display name
     *
     * @return string|null
     */
    public function getDisplayName()
    {
        return isset($this->attributes['display_name']) ? $this->attributes['display_name'] : null;
    }

    /**
     * File/Folder display name
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDisplayName($value)
    {
        return $this->attributes['display_name'] = $value;
    }
    /**
     * Type: `directory` or `file`.
     *
     * @return string|null
     */
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }

    /**
     * Type: `directory` or `file`.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setType($value)
    {
        return $this->attributes['type'] = $value;
    }
    /**
     * File/Folder size
     *
     * @return int|null
     */
    public function getSize()
    {
        return isset($this->attributes['size']) ? $this->attributes['size'] : null;
    }

    /**
     * File/Folder size
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setSize($value)
    {
        return $this->attributes['size'] = $value;
    }
    /**
     * File created date/time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    /**
     * User ID of the User who last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedById()
    {
        return isset($this->attributes['last_modified_by_id']) ? $this->attributes['last_modified_by_id'] : null;
    }

    /**
     * User ID of the User who last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedById($value)
    {
        return $this->attributes['last_modified_by_id'] = $value;
    }
    /**
     * ID of the API key that last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedByApiKeyId()
    {
        return isset($this->attributes['last_modified_by_api_key_id']) ? $this->attributes['last_modified_by_api_key_id'] : null;
    }

    /**
     * ID of the API key that last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedByApiKeyId($value)
    {
        return $this->attributes['last_modified_by_api_key_id'] = $value;
    }
    /**
     * ID of the Automation that last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedByAutomationId()
    {
        return isset($this->attributes['last_modified_by_automation_id']) ? $this->attributes['last_modified_by_automation_id'] : null;
    }

    /**
     * ID of the Automation that last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedByAutomationId($value)
    {
        return $this->attributes['last_modified_by_automation_id'] = $value;
    }
    /**
     * ID of the Bundle Registration that last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedByBundleRegistrationId()
    {
        return isset($this->attributes['last_modified_by_bundle_registration_id']) ? $this->attributes['last_modified_by_bundle_registration_id'] : null;
    }

    /**
     * ID of the Bundle Registration that last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedByBundleRegistrationId($value)
    {
        return $this->attributes['last_modified_by_bundle_registration_id'] = $value;
    }
    /**
     * ID of the Remote Server that last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedByRemoteServerId()
    {
        return isset($this->attributes['last_modified_by_remote_server_id']) ? $this->attributes['last_modified_by_remote_server_id'] : null;
    }

    /**
     * ID of the Remote Server that last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedByRemoteServerId($value)
    {
        return $this->attributes['last_modified_by_remote_server_id'] = $value;
    }
    /**
     * ID of the Sync that last modified the file/folder
     *
     * @return int|null
     */
    public function getLastModifiedBySyncId()
    {
        return isset($this->attributes['last_modified_by_sync_id']) ? $this->attributes['last_modified_by_sync_id'] : null;
    }

    /**
     * ID of the Sync that last modified the file/folder
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLastModifiedBySyncId($value)
    {
        return $this->attributes['last_modified_by_sync_id'] = $value;
    }
    /**
     * File last modified date/time, according to the server.  This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
     *
     * @return string|null
     */
    public function getMtime()
    {
        return isset($this->attributes['mtime']) ? $this->attributes['mtime'] : null;
    }

    /**
     * File last modified date/time, according to the server.  This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMtime($value)
    {
        return $this->attributes['mtime'] = $value;
    }
    /**
     * File last modified date/time, according to the client who set it.  Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times.  This allows Desktop<->Cloud syncing to preserve modified at times.
     *
     * @return string|null
     */
    public function getProvidedMtime()
    {
        return isset($this->attributes['provided_mtime']) ? $this->attributes['provided_mtime'] : null;
    }

    /**
     * File last modified date/time, according to the client who set it.  Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times.  This allows Desktop<->Cloud syncing to preserve modified at times.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setProvidedMtime($value)
    {
        return $this->attributes['provided_mtime'] = $value;
    }
    /**
     * File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @return string|null
     */
    public function getCrc32()
    {
        return isset($this->attributes['crc32']) ? $this->attributes['crc32'] : null;
    }

    /**
     * File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setCrc32($value)
    {
        return $this->attributes['crc32'] = $value;
    }
    /**
     * File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @return string|null
     */
    public function getMd5()
    {
        return isset($this->attributes['md5']) ? $this->attributes['md5'] : null;
    }

    /**
     * File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMd5($value)
    {
        return $this->attributes['md5'] = $value;
    }
    /**
     * File SHA1 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @return string|null
     */
    public function getSha1()
    {
        return isset($this->attributes['sha1']) ? $this->attributes['sha1'] : null;
    }

    /**
     * File SHA1 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSha1($value)
    {
        return $this->attributes['sha1'] = $value;
    }
    /**
     * File SHA256 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @return string|null
     */
    public function getSha256()
    {
        return isset($this->attributes['sha256']) ? $this->attributes['sha256'] : null;
    }

    /**
     * File SHA256 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setSha256($value)
    {
        return $this->attributes['sha256'] = $value;
    }
    /**
     * MIME Type.  This is determined by the filename extension and is not stored separately internally.
     *
     * @return string|null
     */
    public function getMimeType()
    {
        return isset($this->attributes['mime_type']) ? $this->attributes['mime_type'] : null;
    }

    /**
     * MIME Type.  This is determined by the filename extension and is not stored separately internally.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setMimeType($value)
    {
        return $this->attributes['mime_type'] = $value;
    }
    /**
     * Region location
     *
     * @return string|null
     */
    public function getRegion()
    {
        return isset($this->attributes['region']) ? $this->attributes['region'] : null;
    }

    /**
     * Region location
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRegion($value)
    {
        return $this->attributes['region'] = $value;
    }
    /**
     * A short string representing the current user's permissions.  Can be `r` (Read),`w` (Write),`d` (Delete), `l` (List) or any combination
     *
     * @return string|null
     */
    public function getPermissions()
    {
        return isset($this->attributes['permissions']) ? $this->attributes['permissions'] : null;
    }

    /**
     * A short string representing the current user's permissions.  Can be `r` (Read),`w` (Write),`d` (Delete), `l` (List) or any combination
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPermissions($value)
    {
        return $this->attributes['permissions'] = $value;
    }
    /**
     * Are subfolders locked and unable to be modified?
     *
     * @return bool|null
     */
    public function getSubfoldersLocked()
    {
        return isset($this->attributes['subfolders_locked']) ? $this->attributes['subfolders_locked'] : null;
    }

    /**
     * Are subfolders locked and unable to be modified?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setSubfoldersLocked($value)
    {
        return $this->attributes['subfolders_locked'] = $value;
    }
    /**
     * Is this folder locked and unable to be modified?
     *
     * @return bool|null
     */
    public function getIsLocked()
    {
        return isset($this->attributes['is_locked']) ? $this->attributes['is_locked'] : null;
    }

    /**
     * Is this folder locked and unable to be modified?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setIsLocked($value)
    {
        return $this->attributes['is_locked'] = $value;
    }
    /**
     * Link to download file. Provided only in response to a download request.
     *
     * @return string|null
     */
    public function getDownloadUri()
    {
        return isset($this->attributes['download_uri']) ? $this->attributes['download_uri'] : null;
    }

    /**
     * Link to download file. Provided only in response to a download request.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setDownloadUri($value)
    {
        return $this->attributes['download_uri'] = $value;
    }
    /**
     * Optional direct connection information for direct Agent transfer attempts
     *
     * @return \stdClass|null
     */
    public function getDirectConnectionInfo()
    {
        return isset($this->attributes['direct_connection_info']) ? $this->attributes['direct_connection_info'] : null;
    }

    /**
     * Optional direct connection information for direct Agent transfer attempts
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setDirectConnectionInfo($value)
    {
        return $this->attributes['direct_connection_info'] = $value;
    }
    /**
     * Bookmark/priority color of file/folder
     *
     * @return string|null
     */
    public function getPriorityColor()
    {
        return isset($this->attributes['priority_color']) ? $this->attributes['priority_color'] : null;
    }

    /**
     * Bookmark/priority color of file/folder
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setPriorityColor($value)
    {
        return $this->attributes['priority_color'] = $value;
    }
    /**
     * File preview ID
     *
     * @return int|null
     */
    public function getPreviewId()
    {
        return isset($this->attributes['preview_id']) ? $this->attributes['preview_id'] : null;
    }

    /**
     * File preview ID
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPreviewId($value)
    {
        return $this->attributes['preview_id'] = $value;
    }
    /**
     * File preview
     *
     * @return \stdClass|null
     */
    public function getPreview()
    {
        return isset($this->attributes['preview']) ? $this->attributes['preview'] : null;
    }

    /**
     * File preview
     *
     * @param \stdClass|null $value
     * @return \stdClass|null The assigned value.
     */
    public function setPreview($value)
    {
        return $this->attributes['preview'] = $value;
    }
    /**
     * Create parent directories if they do not exist?
     *
     * @return bool|null
     */
    public function getMkdirParents()
    {
        return isset($this->attributes['mkdir_parents']) ? $this->attributes['mkdir_parents'] : null;
    }

    /**
     * Create parent directories if they do not exist?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setMkdirParents($value)
    {
        return $this->attributes['mkdir_parents'] = $value;
    }

    /**
     * Persist this model's attributes and replace them with the API response.
     *
     * @return bool True after the operation succeeds.
     * @throws \Files\Exception\FilesException When saving fails or updates are unsupported.
     */
    public function save()
    {
        $new_obj = self::create($this->path, $this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
    }


    /**
     * List Folders by Path
     *
     * GET /folders/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `cursor` (string, optional): Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
     *     - `per_page` (int, optional): Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
     *     - `preview_size` (string, optional): Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
     *     - `sort_by` (array|object, optional): Search by field and direction. Valid fields are `path`, `size`, `modified_at_datetime`, `provided_modified_at`.  Valid directions are `asc` and `desc`.  Defaults to `{"path":"asc"}`.
     *     - `search` (string, optional): If specified, will search the folders/files list by name. Ignores text before last `/`. This is the same API used by the search bar in the web UI when running 'Search This Folder'.  Search results are a best effort, not real time, and not guaranteed to perfectly match the latest folder listing.  Results may be truncated if more than 1,000 possible matches exist.  This field should only be used for ad-hoc (human) searching, and not as part of an automated process.
     *     - `search_custom_metadata_key` (string, optional): If provided, the search string in `search` will search for files where this custom metadata key matches the value sent in `search`.  Set this to `*` to allow any metadata key to match the value sent in `search`.
     *     - `search_all` (bool, optional): Search entire site?  If true, we will search the entire site.  Do not provide a path when using this parameter.  This is the same API used by the search bar in the web UI when running 'Search All Files'.  Search results are a best effort, not real time, and not guaranteed to match every file.  This field should only be used for ad-hoc (human) searching, and not as part of an automated process.
     *     - `with_previews` (bool, optional): Include file previews?
     *     - `with_priority_color` (bool, optional): Include file priority color information?
     *     - `type` (string, optional): Type of objects to return.  Can be `folder` or `file`.
     *     - `modified_at_datetime` (string, optional): If provided, will only return files/folders modified after this time. Can be used only in combination with `type` filter.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\File[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function listFor($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['cursor']) && !is_string($params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (!empty($params['per_page']) && !is_int($params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['preview_size']) && !is_string($params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        if (!empty($params['search']) && !is_string($params['search'])) {
            throw new \Files\Exception\InvalidParameterException('$search must be of type string; received ' . gettype(@$params['search']));
        }

        if (!empty($params['search_custom_metadata_key']) && !is_string($params['search_custom_metadata_key'])) {
            throw new \Files\Exception\InvalidParameterException('$search_custom_metadata_key must be of type string; received ' . gettype(@$params['search_custom_metadata_key']));
        }

        if (!empty($params['type']) && !is_string($params['type'])) {
            throw new \Files\Exception\InvalidParameterException('$type must be of type string; received ' . gettype(@$params['type']));
        }

        if (!empty($params['modified_at_datetime']) && !is_string($params['modified_at_datetime'])) {
            throw new \Files\Exception\InvalidParameterException('$modified_at_datetime must be of type string; received ' . gettype(@$params['modified_at_datetime']));
        }

        $response = Api::sendRequest('/folders/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new File((array) $obj, $options);
        }

        return $return_array;
    }

    /**
     * Create Folder
     *
     * POST /folders/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `mkdir_parents` (bool, optional): Create parent directories if they do not exist?
     *     - `provided_mtime` (string, optional): User provided modification time.
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\File
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function create($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (!empty($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (!empty($params['provided_mtime']) && !is_string($params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        $response = Api::sendRequest('/folders/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $options);

        return new File((array) (@$response->data ?: []), $options);
    }
}
