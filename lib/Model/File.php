<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Files;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class File
 *
 * @package Files
 */
class File {
  private $attributes = [];
  private $options = [];

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

  public function isLoaded() {
    return !!@$this->attributes['path'];
  }

  private static function openUpload($path, $params = []) {
    $params = array_merge($params, ['action' => 'put']);
    $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

    $partData = (array)(@$response->data ?: []);
    $partData['headers'] = $response->headers;
    $partData['parameters'] = $params;

    return new FileUploadPart($partData);
  }

  private static function continueUpload($path, $partNumber, $firstFileUploadPart) {
    $params = [
      'action' => 'put',
      'part' => $partNumber,
      'ref' => $firstFileUploadPart->ref,
    ];

    $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

    $partData = (array)(@$response->data ?: []);
    $partData['headers'] = $response->headers;
    $partData['parameters'] = $params;

    return new FileUploadPart($partData);
  }

  private static function completeUpload($fileUploadPart) {
    $params = [
      'action' => 'end',
      'ref' => $fileUploadPart->ref,
    ];

    $response = Api::sendRequest('/files/' . rawurlencode($fileUploadPart->path), 'POST', $params);
  }

  public static function uploadFile($destinationPath, $sourceFilePath, $params = []) {
    if (!$destinationPath) {
      throw new \Files\MissingParameterException('Parameter missing: destinationPath');
    }

    if (!$sourceFilePath) {
      throw new \Files\MissingParameterException('Parameter missing: sourceFilePath');
    }

    $fileUploadPart = self::openUpload($destinationPath, $params);

    Logger::debug('File::uploadFile() fileUploadPart = ' . print_r($fileUploadPart, true));

    $sourceFileHandle = fopen($sourceFilePath, 'rb');

    $filesize = filesize($sourceFilePath);
    $totalParts = ceil($filesize / $fileUploadPart->partsize);

    if ($totalParts === 1) {
      Api::sendFile($fileUploadPart->upload_uri, 'PUT', $sourceFileHandle);
    } else {
      // send part 1
      $partFilePath = tempnam(sys_get_temp_dir(), basename($fileUploadPart->path));
      $partFileHandle = fopen($partFilePath, 'w+b');
      stream_copy_to_stream($sourceFileHandle, $partFileHandle, $fileUploadPart->partsize);
      rewind($partFileHandle);

      Api::sendFile($fileUploadPart->upload_uri, 'PUT', $partFileHandle);

      unlink($partFilePath);

      $failed = false;

      // send parts 2..n
      for ($part = 2; $part <= $totalParts; ++$part) {
        $response = null;
        $retries = 0;

        $sourceOffset = ftell($sourceFileHandle);

        do {
          $nextFileUploadPart = self::continueUpload($destinationPath, $part, $fileUploadPart);

          $partFilePath = tempnam(sys_get_temp_dir(), basename($fileUploadPart->path) . '~part' . $part);
          $partFileHandle = fopen($partFilePath, 'w+b');

          if ($retries > 0) {
            fseek($sourceFileHandle, $sourceOffset);
          }

          stream_copy_to_stream($sourceFileHandle, $partFileHandle, $nextFileUploadPart->partsize);

          rewind($partFileHandle);

          $response = Api::sendFile($nextFileUploadPart->upload_uri, 'PUT', $partFileHandle);

          unlink($partFilePath);
        } while (!$response && ++$retries <= \Files\Files::$maxNetworkRetries);

        if ($retries > \Files\Files::$maxNetworkRetries) {
          $failed = true;
          break;
        }
      }
    }

    self::completeUpload($fileUploadPart);

    return !$failed;
  }

  public static function uploadData($destinationPath, $data, $params = []) {
    if (!$destinationPath) {
      throw new \Files\MissingParameterException('Parameter missing: destinationPath');
    }

    if (!$data) {
      throw new \Files\MissingParameterException('Parameter missing: data');
    }

    $tempPath = tempnam(sys_get_temp_dir(), basename($destinationPath));
    file_put_contents($tempPath, $data);

    $result = self::uploadFile($destinationPath, $tempPath, $params);

    unlink($tempPath);

    return $result;
  }

  public static function getDownloadUrl($remoteFilePath) {
    $file = new File();
    $file->path = $remoteFilePath;

    $response = $file->download();
    return $response->download_uri;
  }

  public static function downloadToStream($remoteFilePath, $destinationStream, $startOffset = null, $endOffset = null) {
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

    return (object)[
      'received' => $fetchedLength,
      'total' => $totalLength
    ];
  }

  public static function partialDownloadToFile($remoteFilePath, $destinationPath, $startOffset = null, $endOffset = null) {
    return self::downloadToStream($remoteFilePath, $destinationPath, $startOffset, $endOffset);
  }

  public static function resumeDownloadToFile($remoteFilePath, $destinationPath) {
    $readStream = fopen($destinationPath, 'r');
    fseek($readStream, 0, SEEK_END);
    $rangeStart = ftell($readStream);
    fclose($readStream);

    $stream = fopen($destinationPath, 'a');
    return self::downloadToStream($remoteFilePath, $stream, $rangeStart);
  }

  public static function downloadToFile($remoteFilePath, $destinationPath, $enableRetry = true) {
    $retries = 0;
    
    while (true) {
      try {
        if (!$retries) {
          $response = self::downloadToStream($remoteFilePath, $destinationPath);
        } else {
          $response = self::resumeDownloadToFile($remoteFilePath, $destinationPath);
        }

        break;
      } catch (\Exception $error) {
        ++$retries;

        if (!$enableRetry || $retries > \Files\Files::$maxNetworkRetries) {
          Logger::info('Retries exhausted - giving up on this file download');
          handleErrorResponse($error);
        } else {
          Logger::info('Retrying file download (retry #' . $retries . ')');
        }
      }
    }

    return $response;
  }

  public static function deletePath($path) {
    if (!$path) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    $file = new File();
    return $file->delete(['path' => $path]);
  }

  public function copyTo($destinationFilePath) {
    if (!$this->path) {
      throw new \Files\EmptyPropertyException('Empty object property: path');
    }

    if (!$destinationFilePath) {
      throw new \Files\MissingParameterException('Parameter missing: destinationFilePath');
    }

    $params = ['destination' => $destinationFilePath];
    $response = Api::sendRequest('/file_actions/copy/' . rawurlencode($this->path), 'POST', $params);
    return $response->data;
  }

  public function moveTo($destinationFilePath) {
    if (!$this->path) {
      throw new \Files\EmptyPropertyException('Empty object property: path');
    }

    if (!$destinationFilePath) {
      throw new \Files\MissingParameterException('Parameter missing: destinationFilePath');
    }

    $params = ['destination' => $destinationFilePath];
    $response = Api::sendRequest('/file_actions/move/' . rawurlencode($this->path), 'POST', $params);
    return $response->data;
  }

  // string # File/Folder path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
  public function getPath() {
    return @$this->attributes['path'];
  }

  public function setPath($value) {
    return $this->attributes['path'] = $value;
  }

  // string # File/Folder display name
  public function getDisplayName() {
    return @$this->attributes['display_name'];
  }

  public function setDisplayName($value) {
    return $this->attributes['display_name'] = $value;
  }

  // string # Type: `directory` or `file`.
  public function getType() {
    return @$this->attributes['type'];
  }

  public function setType($value) {
    return $this->attributes['type'] = $value;
  }

  // int64 # File/Folder size
  public function getSize() {
    return @$this->attributes['size'];
  }

  public function setSize($value) {
    return $this->attributes['size'] = $value;
  }

  // date-time # File created date/time
  public function getCreatedAt() {
    return @$this->attributes['created_at'];
  }

  // date-time # File last modified date/time, according to the server.  This is the timestamp of the last Files.com operation of the file, regardless of what modified timestamp was sent.
  public function getMtime() {
    return @$this->attributes['mtime'];
  }

  public function setMtime($value) {
    return $this->attributes['mtime'] = $value;
  }

  // date-time # File last modified date/time, according to the client who set it.  Files.com allows desktop, FTP, SFTP, and WebDAV clients to set modified at times.  This allows Desktop<->Cloud syncing to preserve modified at times.
  public function getProvidedMtime() {
    return @$this->attributes['provided_mtime'];
  }

  public function setProvidedMtime($value) {
    return $this->attributes['provided_mtime'] = $value;
  }

  // string # File CRC32 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
  public function getCrc32() {
    return @$this->attributes['crc32'];
  }

  public function setCrc32($value) {
    return $this->attributes['crc32'] = $value;
  }

  // string # File MD5 checksum. This is sometimes delayed, so if you get a blank response, wait and try again.
  public function getMd5() {
    return @$this->attributes['md5'];
  }

  public function setMd5($value) {
    return $this->attributes['md5'] = $value;
  }

  // string # MIME Type.  This is determined by the filename extension and is not stored separately internally.
  public function getMimeType() {
    return @$this->attributes['mime_type'];
  }

  public function setMimeType($value) {
    return $this->attributes['mime_type'] = $value;
  }

  // string # Region location
  public function getRegion() {
    return @$this->attributes['region'];
  }

  public function setRegion($value) {
    return $this->attributes['region'] = $value;
  }

  // string # A short string representing the current user's permissions.  Can be `r`,`w`,`d`, `l` or any combination
  public function getPermissions() {
    return @$this->attributes['permissions'];
  }

  public function setPermissions($value) {
    return $this->attributes['permissions'] = $value;
  }

  // boolean # Are subfolders locked and unable to be modified?
  public function getSubfoldersLocked() {
    return @$this->attributes['subfolders_locked'];
  }

  public function setSubfoldersLocked($value) {
    return $this->attributes['subfolders_locked'] = $value;
  }

  // string # Link to download file. Provided only in response to a download request.
  public function getDownloadUri() {
    return @$this->attributes['download_uri'];
  }

  public function setDownloadUri($value) {
    return $this->attributes['download_uri'] = $value;
  }

  // string # Bookmark/priority color of file/folder
  public function getPriorityColor() {
    return @$this->attributes['priority_color'];
  }

  public function setPriorityColor($value) {
    return $this->attributes['priority_color'] = $value;
  }

  // int64 # File preview ID
  public function getPreviewId() {
    return @$this->attributes['preview_id'];
  }

  public function setPreviewId($value) {
    return $this->attributes['preview_id'] = $value;
  }

  // Preview # File preview
  public function getPreview() {
    return @$this->attributes['preview'];
  }

  public function setPreview($value) {
    return $this->attributes['preview'] = $value;
  }

  // string # The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
  public function getAction() {
    return @$this->attributes['action'];
  }

  public function setAction($value) {
    return $this->attributes['action'] = $value;
  }

  // int64 # Length of file.
  public function getLength() {
    return @$this->attributes['length'];
  }

  public function setLength($value) {
    return $this->attributes['length'] = $value;
  }

  // boolean # Create parent directories if they do not exist?
  public function getMkdirParents() {
    return @$this->attributes['mkdir_parents'];
  }

  public function setMkdirParents($value) {
    return $this->attributes['mkdir_parents'] = $value;
  }

  // int64 # Part if uploading a part.
  public function getPart() {
    return @$this->attributes['part'];
  }

  public function setPart($value) {
    return $this->attributes['part'] = $value;
  }

  // int64 # How many parts to fetch?
  public function getParts() {
    return @$this->attributes['parts'];
  }

  public function setParts($value) {
    return $this->attributes['parts'] = $value;
  }

  // string #
  public function getRef() {
    return @$this->attributes['ref'];
  }

  public function setRef($value) {
    return $this->attributes['ref'] = $value;
  }

  // int64 # File byte offset to restart from.
  public function getRestart() {
    return @$this->attributes['restart'];
  }

  public function setRestart($value) {
    return $this->attributes['restart'] = $value;
  }

  // string # If copying folder, copy just the structure?
  public function getStructure() {
    return @$this->attributes['structure'];
  }

  public function setStructure($value) {
    return $this->attributes['structure'] = $value;
  }

  // boolean # Allow file rename instead of overwrite?
  public function getWithRename() {
    return @$this->attributes['with_rename'];
  }

  public function setWithRename($value) {
    return $this->attributes['with_rename'] = $value;
  }

  // Download file
  //
  // Parameters:
  //   action - string - Can be blank, `redirect` or `stat`.  If set to `stat`, we will return file information but without a download URL, and without logging a download.  If set to `redirect` we will serve a 302 redirect directly to the file.  This is used for integrations with Zapier, and is not recommended for most integrations.
  //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
  //   with_previews - boolean - Include file preview information?
  //   with_priority_color - boolean - Include file priority color information?
  public function download($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['action'] && !is_string(@$params['action'])) {
      throw new \Files\InvalidParameterException('$action must be of type string; received ' . gettype($action));
    }

    if (@$params['preview_size'] && !is_string(@$params['preview_size'])) {
      throw new \Files\InvalidParameterException('$preview_size must be of type string; received ' . gettype($preview_size));
    }

    $response = Api::sendRequest('/files/' . @$params['path'] . '', 'GET', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   provided_mtime - string - Modified time of file.
  //   priority_color - string - Priority/Bookmark color of file.
  public function update($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['provided_mtime'] && !is_string(@$params['provided_mtime'])) {
      throw new \Files\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype($provided_mtime));
    }

    if (@$params['priority_color'] && !is_string(@$params['priority_color'])) {
      throw new \Files\InvalidParameterException('$priority_color must be of type string; received ' . gettype($priority_color));
    }

    $response = Api::sendRequest('/files/' . @$params['path'] . '', 'PATCH', $params, $this->options);
    return $response->data;
  }

  // Parameters:
  //   recursive - boolean - If true, will recursively delete folers.  Otherwise, will error on non-empty folders.
  public function delete($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    $response = Api::sendRequest('/files/' . @$params['path'] . '', 'DELETE', $params, $this->options);
    return $response->data;
  }

  public function destroy($params = []) {
    return $this->delete($params);
  }

  // Copy file/folder
  //
  // Parameters:
  //   destination (required) - string - Copy destination path.
  //   structure - boolean - Copy structure only?
  public function copy($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (!@$params['destination']) {
      if (@$this->destination) {
        $params['destination'] = $this->destination;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: destination');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \Files\InvalidParameterException('$destination must be of type string; received ' . gettype($destination));
    }

    $response = Api::sendRequest('/file_actions/copy/' . @$params['path'] . '', 'POST', $params, $this->options);
    return $response->data;
  }

  // Move file/folder
  //
  // Parameters:
  //   destination (required) - string - Move destination path.
  public function move($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (!@$params['destination']) {
      if (@$this->destination) {
        $params['destination'] = $this->destination;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: destination');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['destination'] && !is_string(@$params['destination'])) {
      throw new \Files\InvalidParameterException('$destination must be of type string; received ' . gettype($destination));
    }

    $response = Api::sendRequest('/file_actions/move/' . @$params['path'] . '', 'POST', $params, $this->options);
    return $response->data;
  }

  // Begin file upload
  //
  // Parameters:
  //   mkdir_parents - boolean - Create parent directories if they do not exist?
  //   part - int64 - Part if uploading a part.
  //   parts - int64 - How many parts to fetch?
  //   ref - string -
  //   restart - int64 - File byte offset to restart from.
  //   size - int64 - Total bytes of file being uploaded (include bytes being retained if appending/restarting).
  //   with_rename - boolean - Allow file rename instead of overwrite?
  public function beginUpload($params = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    if (!@$params['path']) {
      if (@$this->path) {
        $params['path'] = $this->path;
      } else {
        throw new \Files\MissingParameterException('Parameter missing: path');
      }
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['part'] && !is_int(@$params['part'])) {
      throw new \Files\InvalidParameterException('$part must be of type int; received ' . gettype($part));
    }

    if (@$params['parts'] && !is_int(@$params['parts'])) {
      throw new \Files\InvalidParameterException('$parts must be of type int; received ' . gettype($parts));
    }

    if (@$params['ref'] && !is_string(@$params['ref'])) {
      throw new \Files\InvalidParameterException('$ref must be of type string; received ' . gettype($ref));
    }

    if (@$params['restart'] && !is_int(@$params['restart'])) {
      throw new \Files\InvalidParameterException('$restart must be of type int; received ' . gettype($restart));
    }

    if (@$params['size'] && !is_int(@$params['size'])) {
      throw new \Files\InvalidParameterException('$size must be of type int; received ' . gettype($size));
    }

    $response = Api::sendRequest('/file_actions/begin_upload/' . @$params['path'] . '', 'POST', $params, $this->options);
    return $response->data;
  }

  public function save() {
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
  public static function create($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['action'] && !is_string(@$params['action'])) {
      throw new \Files\InvalidParameterException('$action must be of type string; received ' . gettype($action));
    }

    if (@$params['length'] && !is_int(@$params['length'])) {
      throw new \Files\InvalidParameterException('$length must be of type int; received ' . gettype($length));
    }

    if (@$params['part'] && !is_int(@$params['part'])) {
      throw new \Files\InvalidParameterException('$part must be of type int; received ' . gettype($part));
    }

    if (@$params['parts'] && !is_int(@$params['parts'])) {
      throw new \Files\InvalidParameterException('$parts must be of type int; received ' . gettype($parts));
    }

    if (@$params['provided_mtime'] && !is_string(@$params['provided_mtime'])) {
      throw new \Files\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype($provided_mtime));
    }

    if (@$params['ref'] && !is_string(@$params['ref'])) {
      throw new \Files\InvalidParameterException('$ref must be of type string; received ' . gettype($ref));
    }

    if (@$params['restart'] && !is_int(@$params['restart'])) {
      throw new \Files\InvalidParameterException('$restart must be of type int; received ' . gettype($restart));
    }

    if (@$params['size'] && !is_int(@$params['size'])) {
      throw new \Files\InvalidParameterException('$size must be of type int; received ' . gettype($size));
    }

    if (@$params['structure'] && !is_string(@$params['structure'])) {
      throw new \Files\InvalidParameterException('$structure must be of type string; received ' . gettype($structure));
    }

    $response = Api::sendRequest('/files/' . @$params['path'] . '', 'POST', $params, $options);

    return new File((array)(@$response->data ?: []), $options);
  }

  // Parameters:
  //   path (required) - string - Path to operate on.
  //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
  //   with_previews - boolean - Include file preview information?
  //   with_priority_color - boolean - Include file priority color information?
  public static function find($path, $params = [], $options = []) {
    if (!is_array($params)) {
      throw new \Files\InvalidParameterException('$params must be of type array; received ' . gettype($params));
    }

    $params['path'] = $path;

    if (!@$params['path']) {
      throw new \Files\MissingParameterException('Parameter missing: path');
    }

    if (@$params['path'] && !is_string(@$params['path'])) {
      throw new \Files\InvalidParameterException('$path must be of type string; received ' . gettype($path));
    }

    if (@$params['preview_size'] && !is_string(@$params['preview_size'])) {
      throw new \Files\InvalidParameterException('$preview_size must be of type string; received ' . gettype($preview_size));
    }

    $response = Api::sendRequest('/file_actions/metadata/' . @$params['path'] . '', 'GET', $params, $options);

    return new File((array)(@$response->data ?: []), $options);
  }

  public static function get($path, $params = [], $options = []) {
    return self::find($path, $params, $options);
  }
}
