<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class Folder
 *
 * @package Files
 */
class Folder
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
        return !!@$this->attributes['path'];
    }
    // string # File/Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return isset($this->attributes['path']) ? $this->attributes['path'] : null;
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // int64 # User ID of the User who created the file/folder
    public function getCreatedById()
    {
        return isset($this->attributes['created_by_id']) ? $this->attributes['created_by_id'] : null;
    }

    public function setCreatedById($value)
    {
        return $this->attributes['created_by_id'] = $value;
    }
    // int64 # ID of the API key that created the file/folder
    public function getCreatedByApiKeyId()
    {
        return isset($this->attributes['created_by_api_key_id']) ? $this->attributes['created_by_api_key_id'] : null;
    }

    public function setCreatedByApiKeyId($value)
    {
        return $this->attributes['created_by_api_key_id'] = $value;
    }
    // int64 # ID of the AS2 Incoming Message that created the file/folder
    public function getCreatedByAs2IncomingMessageId()
    {
        return isset($this->attributes['created_by_as2_incoming_message_id']) ? $this->attributes['created_by_as2_incoming_message_id'] : null;
    }

    public function setCreatedByAs2IncomingMessageId($value)
    {
        return $this->attributes['created_by_as2_incoming_message_id'] = $value;
    }
    // int64 # ID of the Automation that created the file/folder
    public function getCreatedByAutomationId()
    {
        return isset($this->attributes['created_by_automation_id']) ? $this->attributes['created_by_automation_id'] : null;
    }

    public function setCreatedByAutomationId($value)
    {
        return $this->attributes['created_by_automation_id'] = $value;
    }
    // int64 # ID of the Bundle Registration that created the file/folder
    public function getCreatedByBundleRegistrationId()
    {
        return isset($this->attributes['created_by_bundle_registration_id']) ? $this->attributes['created_by_bundle_registration_id'] : null;
    }

    public function setCreatedByBundleRegistrationId($value)
    {
        return $this->attributes['created_by_bundle_registration_id'] = $value;
    }
    // int64 # ID of the Inbox that created the file/folder
    public function getCreatedByInboxId()
    {
        return isset($this->attributes['created_by_inbox_id']) ? $this->attributes['created_by_inbox_id'] : null;
    }

    public function setCreatedByInboxId($value)
    {
        return $this->attributes['created_by_inbox_id'] = $value;
    }
    // int64 # ID of the Remote Server that created the file/folder
    public function getCreatedByRemoteServerId()
    {
        return isset($this->attributes['created_by_remote_server_id']) ? $this->attributes['created_by_remote_server_id'] : null;
    }

    public function setCreatedByRemoteServerId($value)
    {
        return $this->attributes['created_by_remote_server_id'] = $value;
    }
    // int64 # ID of the Sync that created the file/folder
    public function getCreatedBySyncId()
    {
        return isset($this->attributes['created_by_sync_id']) ? $this->attributes['created_by_sync_id'] : null;
    }

    public function setCreatedBySyncId($value)
    {
        return $this->attributes['created_by_sync_id'] = $value;
    }
    // object # Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
    public function getCustomMetadata()
    {
        return isset($this->attributes['custom_metadata']) ? $this->attributes['custom_metadata'] : null;
    }

    public function setCustomMetadata($value)
    {
        return $this->attributes['custom_metadata'] = $value;
    }
    // string # File/Folder display name
    public function getDisplayName()
    {
        return isset($this->attributes['display_name']) ? $this->attributes['display_name'] : null;
    }

    public function setDisplayName($value)
    {
        return $this->attributes['display_name'] = $value;
    }
    // string # Type: `directory` or `file`.
    public function getType()
    {
        return isset($this->attributes['type']) ? $this->attributes['type'] : null;
    }

    public function setType($value)
    {
        return $this->attributes['type'] = $value;
    }
    // int64 # File/Folder size
    public function getSize()
    {
        return isset($this->attributes['size']) ? $this->attributes['size'] : null;
    }

    public function setSize($value)
    {
        return $this->attributes['size'] = $value;
    }
    // date-time # File created date/time
    public function getCreatedAt()
    {
        return isset($this->attributes['created_at']) ? $this->attributes['created_at'] : null;
    }
    // int64 # User ID of the User who last modified the file/folder
    public function getLastModifiedById()
    {
        return isset($this->attributes['last_modified_by_id']) ? $this->attributes['last_modified_by_id'] : null;
    }

    public function setLastModifiedById($value)
    {
        return $this->attributes['last_modified_by_id'] = $value;
    }
    // int64 # ID of the API key that last modified the file/folder
    public function getLastModifiedByApiKeyId()
    {
        return isset($this->attributes['last_modified_by_api_key_id']) ? $this->attributes['last_modified_by_api_key_id'] : null;
    }

    public function setLastModifiedByApiKeyId($value)
    {
        return $this->attributes['last_modified_by_api_key_id'] = $value;
    }
    // int64 # ID of the Automation that last modified the file/folder
    public function getLastModifiedByAutomationId()
    {
        return isset($this->attributes['last_modified_by_automation_id']) ? $this->attributes['last_modified_by_automation_id'] : null;
    }

    public function setLastModifiedByAutomationId($value)
    {
        return $this->attributes['last_modified_by_automation_id'] = $value;
    }
    // int64 # ID of the Bundle Registration that last modified the file/folder
    public function getLastModifiedByBundleRegistrationId()
    {
        return isset($this->attributes['last_modified_by_bundle_registration_id']) ? $this->attributes['last_modified_by_bundle_registration_id'] : null;
    }

    public function setLastModifiedByBundleRegistrationId($value)
    {
        return $this->attributes['last_modified_by_bundle_registration_id'] = $value;
    }
    // int64 # ID of the Remote Server that last modified the file/folder
    public function getLastModifiedByRemoteServerId()
    {
        return isset($this->attributes['last_modified_by_remote_server_id']) ? $this->attributes['last_modified_by_remote_server_id'] : null;
    }

    public function setLastModifiedByRemoteServerId($value)
    {
        return $this->attributes['last_modified_by_remote_server_id'] = $value;
    }
    // int64 # ID of the Sync that last modified the file/folder
    public function getLastModifiedBySyncId()
    {
        return isset($this->attributes['last_modified_by_sync_id']) ? $this->attributes['last_modified_by_sync_id'] : null;
    }

    public function setLastModifiedBySyncId($value)
    {
        return $this->attributes['last_modified_by_sync_id'] = $value;
    }
    // date-time # File last modified date/time, according to the server.  This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
    public function getMtime()
    {
        return isset($this->attributes['mtime']) ? $this->attributes['mtime'] : null;
    }

    public function setMtime($value)
    {
        return $this->attributes['mtime'] = $value;
    }
    // date-time # File last modified date/time, according to the client who set it.  Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times.  This allows Desktop<->Cloud syncing to preserve modified at times.
    public function getProvidedMtime()
    {
        return isset($this->attributes['provided_mtime']) ? $this->attributes['provided_mtime'] : null;
    }

    public function setProvidedMtime($value)
    {
        return $this->attributes['provided_mtime'] = $value;
    }
    // string # File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getCrc32()
    {
        return isset($this->attributes['crc32']) ? $this->attributes['crc32'] : null;
    }

    public function setCrc32($value)
    {
        return $this->attributes['crc32'] = $value;
    }
    // string # File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getMd5()
    {
        return isset($this->attributes['md5']) ? $this->attributes['md5'] : null;
    }

    public function setMd5($value)
    {
        return $this->attributes['md5'] = $value;
    }
    // string # File SHA1 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getSha1()
    {
        return isset($this->attributes['sha1']) ? $this->attributes['sha1'] : null;
    }

    public function setSha1($value)
    {
        return $this->attributes['sha1'] = $value;
    }
    // string # File SHA256 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getSha256()
    {
        return isset($this->attributes['sha256']) ? $this->attributes['sha256'] : null;
    }

    public function setSha256($value)
    {
        return $this->attributes['sha256'] = $value;
    }
    // string # MIME Type.  This is determined by the filename extension and is not stored separately internally.
    public function getMimeType()
    {
        return isset($this->attributes['mime_type']) ? $this->attributes['mime_type'] : null;
    }

    public function setMimeType($value)
    {
        return $this->attributes['mime_type'] = $value;
    }
    // string # Region location
    public function getRegion()
    {
        return isset($this->attributes['region']) ? $this->attributes['region'] : null;
    }

    public function setRegion($value)
    {
        return $this->attributes['region'] = $value;
    }
    // string # A short string representing the current user's permissions.  Can be `r` (Read),`w` (Write),`d` (Delete), `l` (List) or any combination
    public function getPermissions()
    {
        return isset($this->attributes['permissions']) ? $this->attributes['permissions'] : null;
    }

    public function setPermissions($value)
    {
        return $this->attributes['permissions'] = $value;
    }
    // boolean # Are subfolders locked and unable to be modified?
    public function getSubfoldersLocked()
    {
        return isset($this->attributes['subfolders_locked']) ? $this->attributes['subfolders_locked'] : null;
    }

    public function setSubfoldersLocked($value)
    {
        return $this->attributes['subfolders_locked'] = $value;
    }
    // boolean # Is this folder locked and unable to be modified?
    public function getIsLocked()
    {
        return isset($this->attributes['is_locked']) ? $this->attributes['is_locked'] : null;
    }

    public function setIsLocked($value)
    {
        return $this->attributes['is_locked'] = $value;
    }
    // string # Link to download file. Provided only in response to a download request.
    public function getDownloadUri()
    {
        return isset($this->attributes['download_uri']) ? $this->attributes['download_uri'] : null;
    }

    public function setDownloadUri($value)
    {
        return $this->attributes['download_uri'] = $value;
    }
    // DirectConnectionInfo # Optional direct connection information for direct Agent transfer attempts
    public function getDirectConnectionInfo()
    {
        return isset($this->attributes['direct_connection_info']) ? $this->attributes['direct_connection_info'] : null;
    }

    public function setDirectConnectionInfo($value)
    {
        return $this->attributes['direct_connection_info'] = $value;
    }
    // string # Bookmark/priority color of file/folder
    public function getPriorityColor()
    {
        return isset($this->attributes['priority_color']) ? $this->attributes['priority_color'] : null;
    }

    public function setPriorityColor($value)
    {
        return $this->attributes['priority_color'] = $value;
    }
    // int64 # File preview ID
    public function getPreviewId()
    {
        return isset($this->attributes['preview_id']) ? $this->attributes['preview_id'] : null;
    }

    public function setPreviewId($value)
    {
        return $this->attributes['preview_id'] = $value;
    }
    // Preview # File preview
    public function getPreview()
    {
        return isset($this->attributes['preview']) ? $this->attributes['preview'] : null;
    }

    public function setPreview($value)
    {
        return $this->attributes['preview'] = $value;
    }
    // boolean # Create parent directories if they do not exist?
    public function getMkdirParents()
    {
        return isset($this->attributes['mkdir_parents']) ? $this->attributes['mkdir_parents'] : null;
    }

    public function setMkdirParents($value)
    {
        return $this->attributes['mkdir_parents'] = $value;
    }

    public function save()
    {
        $new_obj = self::create($this->path, $this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
    }


    // Parameters:
    //   cursor - string - Send cursor to resume an existing list from the point at which you left off.  Get a cursor from an existing list via the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
    //   per_page - int64 - Number of records to show per page.  (Max: 10000, 1,000 or less is recommended).
    //   path (required) - string - Path to operate on.
    //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
    //   sort_by - object - Search by field and direction. Valid fields are `path`, `size`, `modified_at_datetime`, `provided_modified_at`.  Valid directions are `asc` and `desc`.  Defaults to `{"path":"asc"}`.
    //   search - string - If specified, will search the folders/files list by name. Ignores text before last `/`. This is the same API used by the search bar in the web UI when running 'Search This Folder'.  Search results are a best effort, not real time, and not guaranteed to perfectly match the latest folder listing.  Results may be truncated if more than 1,000 possible matches exist.  This field should only be used for ad-hoc (human) searching, and not as part of an automated process.
    //   search_custom_metadata_key - string - If provided, the search string in `search` will search for files where this custom metadata key matches the value sent in `search`.  Set this to `*` to allow any metadata key to match the value sent in `search`.
    //   search_all - boolean - Search entire site?  If true, we will search the entire site.  Do not provide a path when using this parameter.  This is the same API used by the search bar in the web UI when running 'Search All Files'.  Search results are a best effort, not real time, and not guaranteed to match every file.  This field should only be used for ad-hoc (human) searching, and not as part of an automated process.
    //   with_previews - boolean - Include file previews?
    //   with_priority_color - boolean - Include file priority color information?
    //   type - string - Type of objects to return.  Can be `folder` or `file`.
    //   modified_at_datetime - string - If provided, will only return files/folders modified after this time. Can be used only in combination with `type` filter.
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

    // Parameters:
    //   path (required) - string - Path to operate on.
    //   mkdir_parents - boolean - Create parent directories if they do not exist?
    //   provided_mtime - string - User provided modification time.
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
