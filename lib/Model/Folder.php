<?php

declare(strict_types=1);

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
        return @$this->attributes[$name];
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method($arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['path'];
    }
    // string # File/Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
    public function getPath()
    {
        return @$this->attributes['path'];
    }

    public function setPath($value)
    {
        return $this->attributes['path'] = $value;
    }
    // int64 # User ID of the User who created the file/folder
    public function getCreatedById()
    {
        return @$this->attributes['created_by_id'];
    }

    public function setCreatedById($value)
    {
        return $this->attributes['created_by_id'] = $value;
    }
    // int64 # ID of the API key that created the file/folder
    public function getCreatedByApiKeyId()
    {
        return @$this->attributes['created_by_api_key_id'];
    }

    public function setCreatedByApiKeyId($value)
    {
        return $this->attributes['created_by_api_key_id'] = $value;
    }
    // int64 # ID of the AS2 Incoming Message that created the file/folder
    public function getCreatedByAs2IncomingMessageId()
    {
        return @$this->attributes['created_by_as2_incoming_message_id'];
    }

    public function setCreatedByAs2IncomingMessageId($value)
    {
        return $this->attributes['created_by_as2_incoming_message_id'] = $value;
    }
    // int64 # ID of the Automation that created the file/folder
    public function getCreatedByAutomationId()
    {
        return @$this->attributes['created_by_automation_id'];
    }

    public function setCreatedByAutomationId($value)
    {
        return $this->attributes['created_by_automation_id'] = $value;
    }
    // int64 # ID of the Bundle Registration that created the file/folder
    public function getCreatedByBundleRegistrationId()
    {
        return @$this->attributes['created_by_bundle_registration_id'];
    }

    public function setCreatedByBundleRegistrationId($value)
    {
        return $this->attributes['created_by_bundle_registration_id'] = $value;
    }
    // int64 # ID of the Inbox that created the file/folder
    public function getCreatedByInboxId()
    {
        return @$this->attributes['created_by_inbox_id'];
    }

    public function setCreatedByInboxId($value)
    {
        return $this->attributes['created_by_inbox_id'] = $value;
    }
    // int64 # ID of the Remote Server that created the file/folder
    public function getCreatedByRemoteServerId()
    {
        return @$this->attributes['created_by_remote_server_id'];
    }

    public function setCreatedByRemoteServerId($value)
    {
        return $this->attributes['created_by_remote_server_id'] = $value;
    }
    // int64 # ID of the Remote Server Sync that created the file/folder
    public function getCreatedByRemoteServerSyncId()
    {
        return @$this->attributes['created_by_remote_server_sync_id'];
    }

    public function setCreatedByRemoteServerSyncId($value)
    {
        return $this->attributes['created_by_remote_server_sync_id'] = $value;
    }
    // object # Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
    public function getCustomMetadata()
    {
        return @$this->attributes['custom_metadata'];
    }

    public function setCustomMetadata($value)
    {
        return $this->attributes['custom_metadata'] = $value;
    }
    // string # File/Folder display name
    public function getDisplayName()
    {
        return @$this->attributes['display_name'];
    }

    public function setDisplayName($value)
    {
        return $this->attributes['display_name'] = $value;
    }
    // string # Type: `directory` or `file`.
    public function getType()
    {
        return @$this->attributes['type'];
    }

    public function setType($value)
    {
        return $this->attributes['type'] = $value;
    }
    // int64 # File/Folder size
    public function getSize()
    {
        return @$this->attributes['size'];
    }

    public function setSize($value)
    {
        return $this->attributes['size'] = $value;
    }
    // date-time # File created date/time
    public function getCreatedAt()
    {
        return @$this->attributes['created_at'];
    }
    // int64 # User ID of the User who last modified the file/folder
    public function getLastModifiedById()
    {
        return @$this->attributes['last_modified_by_id'];
    }

    public function setLastModifiedById($value)
    {
        return $this->attributes['last_modified_by_id'] = $value;
    }
    // int64 # ID of the API key that last modified the file/folder
    public function getLastModifiedByApiKeyId()
    {
        return @$this->attributes['last_modified_by_api_key_id'];
    }

    public function setLastModifiedByApiKeyId($value)
    {
        return $this->attributes['last_modified_by_api_key_id'] = $value;
    }
    // int64 # ID of the Automation that last modified the file/folder
    public function getLastModifiedByAutomationId()
    {
        return @$this->attributes['last_modified_by_automation_id'];
    }

    public function setLastModifiedByAutomationId($value)
    {
        return $this->attributes['last_modified_by_automation_id'] = $value;
    }
    // int64 # ID of the Bundle Registration that last modified the file/folder
    public function getLastModifiedByBundleRegistrationId()
    {
        return @$this->attributes['last_modified_by_bundle_registration_id'];
    }

    public function setLastModifiedByBundleRegistrationId($value)
    {
        return $this->attributes['last_modified_by_bundle_registration_id'] = $value;
    }
    // int64 # ID of the Remote Server that last modified the file/folder
    public function getLastModifiedByRemoteServerId()
    {
        return @$this->attributes['last_modified_by_remote_server_id'];
    }

    public function setLastModifiedByRemoteServerId($value)
    {
        return $this->attributes['last_modified_by_remote_server_id'] = $value;
    }
    // int64 # ID of the Remote Server Sync that last modified the file/folder
    public function getLastModifiedByRemoteServerSyncId()
    {
        return @$this->attributes['last_modified_by_remote_server_sync_id'];
    }

    public function setLastModifiedByRemoteServerSyncId($value)
    {
        return $this->attributes['last_modified_by_remote_server_sync_id'] = $value;
    }
    // date-time # File last modified date/time, according to the server.  This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
    public function getMtime()
    {
        return @$this->attributes['mtime'];
    }

    public function setMtime($value)
    {
        return $this->attributes['mtime'] = $value;
    }
    // date-time # File last modified date/time, according to the client who set it.  Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times.  This allows Desktop<->Cloud syncing to preserve modified at times.
    public function getProvidedMtime()
    {
        return @$this->attributes['provided_mtime'];
    }

    public function setProvidedMtime($value)
    {
        return $this->attributes['provided_mtime'] = $value;
    }
    // string # File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getCrc32()
    {
        return @$this->attributes['crc32'];
    }

    public function setCrc32($value)
    {
        return $this->attributes['crc32'] = $value;
    }
    // string # File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getMd5()
    {
        return @$this->attributes['md5'];
    }

    public function setMd5($value)
    {
        return $this->attributes['md5'] = $value;
    }
    // string # MIME Type.  This is determined by the filename extension and is not stored separately internally.
    public function getMimeType()
    {
        return @$this->attributes['mime_type'];
    }

    public function setMimeType($value)
    {
        return $this->attributes['mime_type'] = $value;
    }
    // string # Region location
    public function getRegion()
    {
        return @$this->attributes['region'];
    }

    public function setRegion($value)
    {
        return $this->attributes['region'] = $value;
    }
    // string # A short string representing the current user's permissions.  Can be `r` (Read),`w` (Write),`d` (Delete), `l` (List) or any combination
    public function getPermissions()
    {
        return @$this->attributes['permissions'];
    }

    public function setPermissions($value)
    {
        return $this->attributes['permissions'] = $value;
    }
    // boolean # Are subfolders locked and unable to be modified?
    public function getSubfoldersLocked()
    {
        return @$this->attributes['subfolders_locked'];
    }

    public function setSubfoldersLocked($value)
    {
        return $this->attributes['subfolders_locked'] = $value;
    }
    // boolean # Is this folder locked and unable to be modified?
    public function getIsLocked()
    {
        return @$this->attributes['is_locked'];
    }

    public function setIsLocked($value)
    {
        return $this->attributes['is_locked'] = $value;
    }
    // string # Link to download file. Provided only in response to a download request.
    public function getDownloadUri()
    {
        return @$this->attributes['download_uri'];
    }

    public function setDownloadUri($value)
    {
        return $this->attributes['download_uri'] = $value;
    }
    // string # Bookmark/priority color of file/folder
    public function getPriorityColor()
    {
        return @$this->attributes['priority_color'];
    }

    public function setPriorityColor($value)
    {
        return $this->attributes['priority_color'] = $value;
    }
    // int64 # File preview ID
    public function getPreviewId()
    {
        return @$this->attributes['preview_id'];
    }

    public function setPreviewId($value)
    {
        return $this->attributes['preview_id'] = $value;
    }
    // Preview # File preview
    public function getPreview()
    {
        return @$this->attributes['preview'];
    }

    public function setPreview($value)
    {
        return $this->attributes['preview'] = $value;
    }
    // boolean # Create parent directories if they do not exist?
    public function getMkdirParents()
    {
        return @$this->attributes['mkdir_parents'];
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
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   page - int64
    //   path (required) - string - Path to operate on.
    //   filter - string - If specified, will filter folders/files list by name. Ignores text before last `/`. Wildcards of `*` and `?` are acceptable here.
    //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
    //   sort_by - object - Search by field and direction. Valid fields are `path`, `size`, `modified_at_datetime`, `provided_modified_at`.  Valid directions are `asc` and `desc`.  Defaults to `{"path":"asc"}`.
    //   search - string - If `search_all` is `true`, provide the search string here.  Otherwise, this parameter acts like an alias of `filter`.
    //   search_all - boolean - Search entire site?  If set, we will ignore the folder path provided and search the entire site.  This is the same API used by the search bar in the UI.  Search results are a best effort, not real time, and not guaranteed to match every file.  This field should only be used for ad-hoc (human) searching, and not as part of an automated process.
    //   with_previews - boolean - Include file previews?
    //   with_priority_color - boolean - Include file priority color information?
    public static function listFor($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        if (@$params['page'] && !is_int(@$params['page'])) {
            throw new \Files\Exception\InvalidParameterException('$page must be of type int; received ' . gettype(@$params['page']));
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['filter'] && !is_string(@$params['filter'])) {
            throw new \Files\Exception\InvalidParameterException('$filter must be of type string; received ' . gettype(@$params['filter']));
        }

        if (@$params['preview_size'] && !is_string(@$params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        if (@$params['search'] && !is_string(@$params['search'])) {
            throw new \Files\Exception\InvalidParameterException('$search must be of type string; received ' . gettype(@$params['search']));
        }

        $response = Api::sendRequest('/folders/' . @$params['path'] . '', 'GET', $params, $options);

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

        if (!@$params['path']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['provided_mtime'] && !is_string(@$params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        $response = Api::sendRequest('/folders/' . @$params['path'] . '', 'POST', $params, $options);

        return new File((array) (@$response->data ?: []), $options);
    }
}
