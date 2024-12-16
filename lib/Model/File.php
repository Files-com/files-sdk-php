<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class File
 *
 * @package Files
 */
class File
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
        return !!@$this->attributes['path'];
    }

    private static function openUpload($path, $params = [])
    {
        $params = array_merge($params, ['action' => 'put']);
        $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

        $partData = (array) (@$response->data ?: []);
        $partData['headers'] = $response->headers;
        $partData['parameters'] = $params;

        return new FileUploadPart($partData);
    }

    private static function continueUpload($path, $partNumber, $firstFileUploadPart)
    {
        $params = [
            'action' => 'put',
            'part' => $partNumber,
            'ref' => $firstFileUploadPart->ref,
        ];

        $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

        $partData = (array) (@$response->data ?: []);
        $partData['headers'] = $response->headers;
        $partData['parameters'] = $params;

        return new FileUploadPart($partData);
    }

    private static function completeUpload($fileUploadPart, $params = [])
    {
        $response = Api::sendRequest('/files/' . rawurlencode($fileUploadPart->path), 'POST', $params);
    }

    private static function uploadChunks($io, $path, $upload = null, $etags = [], $params = [])
    {
        $bytesWritten = 0;
        while (true) {
            if (empty($upload)) {
                $params = array_merge($params, [
                    'part' => 1
                ]);
            } else {
                $params = array_merge($params, [
                    'ref' => $upload->ref,
                    'part' => $upload->part_number + 1
                ]);
            }

            $beginUpload = empty($upload) ? self::openUpload($path, $params) : self::continueUpload($path, $upload->part_number + 1, $upload);
            $upload = is_array($beginUpload) ? array_shift($beginUpload) : $beginUpload;

            $buf = fread($io, $upload->partsize);
            $buf = $buf === false ? '' : $buf;
            $bytesWritten += strlen($buf);

            $method = strtolower($upload->http_method);
            $headers = [
                'Content-Length' => strlen($buf),
                'Content-Type' => 'application/octet-stream'
            ];


            $response = Api::sendFile($upload->upload_uri, 'PUT', $buf, $headers);

            if ($response->headers['ETag']) {
                array_push($etags, [
                    'etag' => $response->headers['ETag'][0],
                    'part' => $upload->part_number
                ]);
            }

            if (feof($io)) {
                return [$upload, $etags, $bytesWritten];
            }
        }
    }

    public static function uploadFile($destinationPath, $sourceFilePath, $params = [])
    {
        if (!$destinationPath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destinationPath');
        }

        if (!$sourceFilePath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: sourceFilePath');
        }

        if (!file_exists($sourceFilePath)) {
            throw new \Files\Exception\EmptyPropertyException('Empty sourceFilePath: No such file or directory');
        }

        $sourceFileHandle = fopen($sourceFilePath, 'rb');
        $filesize = filesize($sourceFilePath);
        list($upload, $etags, $bytesWritten) = File::uploadChunks($sourceFileHandle, $destinationPath);
        fclose($sourceFileHandle);

        $params = [
            'action' => 'end',
            'ref' => $upload->ref,
            'etags' => $etags,
            'provided_mtime' => date('c', filemtime($sourceFilePath)),
            'size' => $filesize
        ];

        $response = self::completeUpload($upload, $params);

        return $response;
    }

    public static function uploadData($destinationPath, $data, $params = [])
    {
        if (!$destinationPath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destinationPath');
        }

        if (!$data) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: data');
        }

        if (!is_resource($data)) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $data);
            rewind($stream);
        } else {
            $stream = $data;
        }

        list($upload, $etags, $bytesWritten) = File::uploadChunks($stream, $destinationPath);
        fclose($stream);

        $params = [
            'action' => 'end',
            'ref' => $upload->ref,
            'etags' => $etags,
            'provided_mtime' => date('c', time()),
            'size' => $bytesWritten
        ];

        $response = self::completeUpload($upload, $params);

        return $response;
    }

    public static function getDownloadUrl($remoteFilePath)
    {
        $file = new File();
        $file->path = $remoteFilePath;

        $response = $file->download();
        return $response->download_uri;
    }

    public static function downloadToStream($remoteFilePath, $destinationStream, $startOffset = null, $endOffset = null)
    {
        $downloadUrl = self::getDownloadUrl($remoteFilePath);

        $options = [
            'sink' => $destinationStream,
        ];

        if ($startOffset !== null || $endOffset !== null) {
            $rangeStart = $startOffset !== null ? intval($startOffset) : '';
            $rangeEnd = $endOffset !== null ? intval($endOffset) : '';

            Logger::debug('Downloading file from bytes ' . ($rangeStart ?: '0') . ' to ' . ($rangeEnd ?: 'end'));

            $options['headers'] = [
                'Range' => "bytes={$rangeStart}-{$rangeEnd}",
            ];
        }

        $response = Api::sendRequest($downloadUrl, 'GET', null, $options);

        $fetchedLength = intval($response->headers['Content-Length'][0]);
        $totalLength = $response->headers['Content-Range']
            ? intval(explode('/', $response->headers['Content-Range'][0])[1])
            : $fetchedLength;

        return (object) [
            'received' => $fetchedLength,
            'total' => $totalLength,
        ];
    }

    public static function partialDownloadToFile($remoteFilePath, $destinationPath, $startOffset = null, $endOffset = null)
    {
        return self::downloadToStream($remoteFilePath, $destinationPath, $startOffset, $endOffset);
    }

    public static function resumeDownloadToFile($remoteFilePath, $destinationPath)
    {
        $readStream = fopen($destinationPath, 'r');
        fseek($readStream, 0, SEEK_END);
        $rangeStart = ftell($readStream);
        fclose($readStream);

        $stream = fopen($destinationPath, 'a');
        return self::downloadToStream($remoteFilePath, $stream, $rangeStart);
    }

    public static function downloadToFile($remoteFilePath, $destinationPath, $enableRetry = true)
    {
        $retries = 0;

        while (true) {
            try {
                if (!$retries || !file_exists($destinationPath)) {
                    $response = self::downloadToStream($remoteFilePath, $destinationPath);
                } else {
                    $response = self::resumeDownloadToFile($remoteFilePath, $destinationPath);
                }

                break;
            } catch (\Exception $error) {
                ++$retries;

                if (!$enableRetry || $retries > \Files\Files::$maxNetworkRetries) {
                    Logger::info('Retries exhausted - giving up on this file download');
                    throw $error;
                } else {
                    Logger::info('Retrying file download (retry #' . $retries . ')');
                }
            }
        }

        return $response;
    }

    public static function deletePath($path)
    {
        if (!$path) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        $file = new File();
        return $file->delete(['path' => $path]);
    }

    public function copyTo($destinationFilePath)
    {
        if (!$this->path) {
            throw new \Files\Exception\EmptyPropertyException('Empty object property: path');
        }

        if (!$destinationFilePath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destinationFilePath');
        }

        $params = ['destination' => $destinationFilePath];
        $response = Api::sendRequest('/file_actions/copy/' . rawurlencode($this->path), 'POST', $params);
        return $response->data;
    }

    public function moveTo($destinationFilePath)
    {
        if (!$this->path) {
            throw new \Files\Exception\EmptyPropertyException('Empty object property: path');
        }

        if (!$destinationFilePath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destinationFilePath');
        }

        $params = ['destination' => $destinationFilePath];
        $response = Api::sendRequest('/file_actions/move/' . rawurlencode($this->path), 'POST', $params);
        return $response->data;
    }
    // string # File/Folder path. This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
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
    // string # File SHA1 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getSha1()
    {
        return @$this->attributes['sha1'];
    }

    public function setSha1($value)
    {
        return $this->attributes['sha1'] = $value;
    }
    // string # File SHA256 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
    public function getSha256()
    {
        return @$this->attributes['sha256'];
    }

    public function setSha256($value)
    {
        return $this->attributes['sha256'] = $value;
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
    // string # The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
    public function getAction()
    {
        return @$this->attributes['action'];
    }

    public function setAction($value)
    {
        return $this->attributes['action'] = $value;
    }
    // int64 # Length of file.
    public function getLength()
    {
        return @$this->attributes['length'];
    }

    public function setLength($value)
    {
        return $this->attributes['length'] = $value;
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
    // int64 # Part if uploading a part.
    public function getPart()
    {
        return @$this->attributes['part'];
    }

    public function setPart($value)
    {
        return $this->attributes['part'] = $value;
    }
    // int64 # How many parts to fetch?
    public function getParts()
    {
        return @$this->attributes['parts'];
    }

    public function setParts($value)
    {
        return $this->attributes['parts'] = $value;
    }
    // string #
    public function getRef()
    {
        return @$this->attributes['ref'];
    }

    public function setRef($value)
    {
        return $this->attributes['ref'] = $value;
    }
    // int64 # File byte offset to restart from.
    public function getRestart()
    {
        return @$this->attributes['restart'];
    }

    public function setRestart($value)
    {
        return $this->attributes['restart'] = $value;
    }
    // string # If copying folder, copy just the structure?
    public function getStructure()
    {
        return @$this->attributes['structure'];
    }

    public function setStructure($value)
    {
        return $this->attributes['structure'] = $value;
    }
    // boolean # Allow file rename instead of overwrite?
    public function getWithRename()
    {
        return @$this->attributes['with_rename'];
    }

    public function setWithRename($value)
    {
        return $this->attributes['with_rename'] = $value;
    }

    // Download File
    //
    // Parameters:
    //   action - string - Can be blank, `redirect` or `stat`.  If set to `stat`, we will return file information but without a download URL, and without logging a download.  If set to `redirect` we will serve a 302 redirect directly to the file.  This is used for integrations with Zapier, and is not recommended for most integrations.
    //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
    //   with_previews - boolean - Include file preview information?
    //   with_priority_color - boolean - Include file priority color information?
    public function download($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (@$params['preview_size'] && !is_string(@$params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        $response = Api::sendRequest('/files/' . @$params['path'] . '', 'GET', $params, $this->options);
        return new File((array) (@$response->data ?: []), $this->options);
    }

    // Parameters:
    //   custom_metadata - object - Custom metadata map of keys and values. Limited to 32 keys, 256 characters per key and 1024 characters per value.
    //   provided_mtime - string - Modified time of file.
    //   priority_color - string - Priority/Bookmark color of file.
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['provided_mtime'] && !is_string(@$params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        if (@$params['priority_color'] && !is_string(@$params['priority_color'])) {
            throw new \Files\Exception\InvalidParameterException('$priority_color must be of type string; received ' . gettype(@$params['priority_color']));
        }

        $response = Api::sendRequest('/files/' . @$params['path'] . '', 'PATCH', $params, $this->options);
        return new File((array) (@$response->data ?: []), $this->options);
    }

    // Parameters:
    //   recursive - boolean - If true, will recursively delete folders.  Otherwise, will error on non-empty folders.
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/files/' . @$params['path'] . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    // Copy File/Folder
    //
    // Parameters:
    //   destination (required) - string - Copy destination path.
    //   structure - boolean - Copy structure only?
    //   overwrite - boolean - Overwrite existing file(s) in the destination?
    public function copy($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (!@$params['destination']) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        $response = Api::sendRequest('/file_actions/copy/' . @$params['path'] . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Move File/Folder
    //
    // Parameters:
    //   destination (required) - string - Move destination path.
    //   overwrite - boolean - Overwrite existing file(s) in the destination?
    public function move($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (!@$params['destination']) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        $response = Api::sendRequest('/file_actions/move/' . @$params['path'] . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Begin File Upload
    //
    // Parameters:
    //   mkdir_parents - boolean - Create parent directories if they do not exist?
    //   part - int64 - Part if uploading a part.
    //   parts - int64 - How many parts to fetch?
    //   ref - string -
    //   restart - int64 - File byte offset to restart from.
    //   size - int64 - Total bytes of file being uploaded (include bytes being retained if appending/restarting).
    //   with_rename - boolean - Allow file rename instead of overwrite?
    public function beginUpload($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (!@$params['path']) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['part'] && !is_int(@$params['part'])) {
            throw new \Files\Exception\InvalidParameterException('$part must be of type int; received ' . gettype(@$params['part']));
        }

        if (@$params['parts'] && !is_int(@$params['parts'])) {
            throw new \Files\Exception\InvalidParameterException('$parts must be of type int; received ' . gettype(@$params['parts']));
        }

        if (@$params['ref'] && !is_string(@$params['ref'])) {
            throw new \Files\Exception\InvalidParameterException('$ref must be of type string; received ' . gettype(@$params['ref']));
        }

        if (@$params['restart'] && !is_int(@$params['restart'])) {
            throw new \Files\Exception\InvalidParameterException('$restart must be of type int; received ' . gettype(@$params['restart']));
        }

        if (@$params['size'] && !is_int(@$params['size'])) {
            throw new \Files\Exception\InvalidParameterException('$size must be of type int; received ' . gettype(@$params['size']));
        }

        $response = Api::sendRequest('/file_actions/begin_upload/' . @$params['path'] . '', 'POST', $params, $this->options);
        return new FileUploadPart((array) (@$response->data ?: []), $this->options);
    }

    public function save()
    {
        $new_obj = self::create($this->path, $this->attributes, $this->options);
        $this->attributes = $new_obj->attributes;
        return true;
    }


    // Parameters:
    //   path (required) - string - Path to operate on.
    //   action - string - The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
    //   etags[etag] (required) - array(string) - etag identifier.
    //   etags[part] (required) - array(int64) - Part number.
    //   length - int64 - Length of file.
    //   mkdir_parents - boolean - Create parent directories if they do not exist?
    //   part - int64 - Part if uploading a part.
    //   parts - int64 - How many parts to fetch?
    //   provided_mtime - string - User provided modification time.
    //   ref - string -
    //   restart - int64 - File byte offset to restart from.
    //   size - int64 - Size of file.
    //   structure - string - If copying folder, copy just the structure?
    //   with_rename - boolean - Allow file rename instead of overwrite?
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

        if (@$params['action'] && !is_string(@$params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (@$params['length'] && !is_int(@$params['length'])) {
            throw new \Files\Exception\InvalidParameterException('$length must be of type int; received ' . gettype(@$params['length']));
        }

        if (@$params['part'] && !is_int(@$params['part'])) {
            throw new \Files\Exception\InvalidParameterException('$part must be of type int; received ' . gettype(@$params['part']));
        }

        if (@$params['parts'] && !is_int(@$params['parts'])) {
            throw new \Files\Exception\InvalidParameterException('$parts must be of type int; received ' . gettype(@$params['parts']));
        }

        if (@$params['provided_mtime'] && !is_string(@$params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        if (@$params['ref'] && !is_string(@$params['ref'])) {
            throw new \Files\Exception\InvalidParameterException('$ref must be of type string; received ' . gettype(@$params['ref']));
        }

        if (@$params['restart'] && !is_int(@$params['restart'])) {
            throw new \Files\Exception\InvalidParameterException('$restart must be of type int; received ' . gettype(@$params['restart']));
        }

        if (@$params['size'] && !is_int(@$params['size'])) {
            throw new \Files\Exception\InvalidParameterException('$size must be of type int; received ' . gettype(@$params['size']));
        }

        if (@$params['structure'] && !is_string(@$params['structure'])) {
            throw new \Files\Exception\InvalidParameterException('$structure must be of type string; received ' . gettype(@$params['structure']));
        }

        $response = Api::sendRequest('/files/' . @$params['path'] . '', 'POST', $params, $options);

        return new File((array) (@$response->data ?: []), $options);
    }

    // Parameters:
    //   path (required) - string - Path to operate on.
    //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
    //   with_previews - boolean - Include file preview information?
    //   with_priority_color - boolean - Include file priority color information?
    public static function find($path, $params = [], $options = [])
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

        if (@$params['preview_size'] && !is_string(@$params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        $response = Api::sendRequest('/file_actions/metadata/' . @$params['path'] . '', 'GET', $params, $options);

        return new File((array) (@$response->data ?: []), $options);
    }
    public static function get($path, $params = [], $options = [])
    {
        return self::find($path, $params, $options);
    }
}
