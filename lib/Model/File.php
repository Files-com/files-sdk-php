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

    // A caller's upload parameters, split by where they go. provided_mtime and custom_metadata, which only the end of
    // an upload takes, go to the end, as given. The state an upload helper owns (where the upload goes, its parts and
    // ref, and the end's action, size, length, ETags and restart) is never taken from them: a parameter of one of those
    // names is not sent. Every other parameter goes with the request for the first part, as before.
    private static function splitUploadParams($params)
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $first = [];
        $end = [];
        foreach ($params as $name => $value) {
            if (in_array($name, ['provided_mtime', 'custom_metadata'], true)) {
                $end[$name] = $value;
            } elseif (!in_array($name, ['path', 'part', 'parts', 'ref', 'restart', 'action', 'size', 'length', 'etags'], true)) {
                $first[$name] = $value;
            }
        }

        return [$first, $end];
    }

    // A caller's upload options, split into the progress callback, which only the helper calls, transfer (the options of
    // a Files\Transfer\TransferJob, for an adaptive upload), and the rest, which the upload's API requests take as the
    // generated methods do. No option reaches a part's storage request.
    private static function splitUploadOptions($options)
    {
        if (!is_array($options)) {
            throw new \Files\Exception\InvalidParameterException('$options must be of type array; received ' . gettype($options));
        }

        $progress = isset($options['progress']) ? $options['progress'] : null;
        if ($progress !== null && !is_callable($progress)) {
            throw new \Files\Exception\InvalidParameterException('$options[\'progress\'] must be callable; received ' . gettype($progress));
        }

        $transfer = array_key_exists('transfer', $options) ? $options['transfer'] : null;
        if ($transfer !== null && !is_array($transfer)) {
            throw new \Files\Exception\InvalidParameterException('$options[\'transfer\'] must be an array of transfer options; received ' . gettype($transfer));
        }

        unset($options['progress'], $options['transfer']);
        return [$options, $progress, $transfer];
    }

    // An upload helper's call with $options['transfer']: the same upload run as one Files\Transfer\TransferJob of those
    // options, its parts sent concurrently as measured throughput allows, on 64-bit PHP with a native monotonic clock
    // only (elsewhere a ConfigurationException, before anything is read or sent). $transfer['size'] gives the size of a
    // stream uploadData sends. Returns nothing, as the helpers do, and throws what stopped the upload.
    private static function uploadAdaptively($method, $destinationPath, $source, $params, $apiOptions, $progress, $transfer)
    {
        $options = $apiOptions;
        if ($progress !== null) {
            $options['progress'] = $progress;
        }

        if (array_key_exists('size', $transfer)) {
            $options['size'] = $transfer['size'];
            unset($transfer['size']);
        }

        $job = new \Files\Transfer\TransferJob($transfer);
        $job->$method($destinationPath, $source, $params, $options);
        $job->wait();
        return null;
    }

    // Begins part $number of the upload to $path with File#beginUpload, one part a request: the first with the caller's
    // $first parameters, each later one by the upload's $ref. The API must grant exactly that part, of the upload's
    // ref, with a positive partsize and an upload_uri; anything else is refused before the part is read or sent.
    private static function beginUploadPart($path, $number, $ref, $first, $options)
    {
        $request = $number === 1
            ? $first + ['part' => 1, 'parts' => 1]
            : ['ref' => $ref, 'part' => $number, 'parts' => 1];
        $parts = (new File(['path' => $path], $options))->beginUpload($request);

        $part = count($parts) === 1 ? $parts[0] : null;
        $grantedRef = $part ? $part->getRef() : null;
        if (
            !$part || $part->getPartNumber() !== $number
            || !is_string($grantedRef) || $grantedRef === '' || ($ref !== null && $grantedRef !== $ref)
            || !is_int($part->getPartsize()) || $part->getPartsize() <= 0
            || !is_string($part->getUploadUri()) || $part->getUploadUri() === ''
        ) {
            throw new \Files\Exception\ApiBadResponseException('Beginning part ' . $number . ' of the upload to ' . $path . ' granted ' . count($parts) . ' parts, not just that part of the upload with a positive partsize and an upload_uri');
        }

        return $part;
    }

    // How a granted part is sent: with its http_method (PUT, the usual one, when it gives none) and exactly the headers
    // it gives, each a string. Form parameters and a form body, which a grant can also ask for, have no meaning this
    // helper knows, so a grant asking for either is refused before the part is read or sent.
    private static function uploadPartInstructions($part, $number)
    {
        $method = $part->getHttpMethod();
        if ($method === null || $method === '') {
            $method = 'PUT';
        }

        if (!is_string($method) || !preg_match('/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/', $method)) {
            throw new \Files\Exception\ApiBadResponseException('Part ' . $number . ' is granted an http_method that is not an HTTP method');
        }

        foreach (['parameters' => $part->getParameters(), 'send' => $part->getSend()] as $name => $value) {
            if ($value !== null && $value !== '' && (array) $value !== []) {
                throw new \Files\Exception\NotImplementedException('Part ' . $number . ' asks for ' . $name . ', which the upload helpers do not send');
            }
        }

        $headers = $part->getHeaders();
        if ($headers !== null && !is_array($headers) && !is_object($headers)) {
            throw new \Files\Exception\ApiBadResponseException('Part ' . $number . ' is granted headers that are not a map of names to values');
        }

        $headers = (array) $headers;
        foreach ($headers as $name => $value) {
            if (!is_string($value)) {
                throw new \Files\Exception\ApiBadResponseException('Part ' . $number . ' is granted the header ' . $name . ' with a value that is not a string');
            }
        }

        return [$method, $headers];
    }

    // The value of a header a grant names, its name matched case-insensitively, as HTTP header names are; null when it
    // names no such header.
    private static function grantedHeader($headers, $name)
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp((string) $key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }

    // The next part's bytes from $io: as many reads as it takes to fill $partsize bytes, or fewer when the source ends.
    // A read that fails, or that gives nothing before the source ends, stops the upload before the part is sent: these
    // helpers read blocking streams, which give nothing only at their end.
    private static function readUploadPart($io, $partsize)
    {
        $bytes = '';
        while (strlen($bytes) < $partsize) {
            $read = fread($io, $partsize - strlen($bytes));
            if ($read === false) {
                throw new \Files\Exception\InvalidParameterException('Reading the upload\'s source failed');
            }

            if ($read === '') {
                if (feof($io)) {
                    break;
                }

                throw new \Files\Exception\InvalidParameterException('The upload\'s source gave no bytes before its end');
            }

            $bytes .= $read;
        }

        return $bytes;
    }

    // The ETag that acknowledges a part: its response must be a success giving exactly one ETag (matched in any case)
    // that is not empty. Anything else stops the upload, with the part unacknowledged.
    private static function uploadPartReceipt($response, $number)
    {
        $etags = Api::headerValues($response->headers, 'ETag');
        if ($response->status < 200 || $response->status >= 300 || count($etags) !== 1 || !is_string($etags[0]) || $etags[0] === '') {
            throw new \Files\Exception\ApiBadResponseException('The response to part ' . $number . ' (status ' . $response->status . ') did not acknowledge it with exactly one ETag');
        }

        return $etags[0];
    }

    // Uploads what $io holds to $path, a part at a time as the API grants each, and returns the upload's ref, each
    // part's ETag and the bytes acknowledged. Each part is filled from as many reads as it takes, and the upload ends
    // with the part during which the source ends (so a source ending exactly where a part ends sends one more, empty,
    // part). $progress, when given, is called after each part is acknowledged; anything it throws stops the upload
    // there, before any later part or the end.
    private static function uploadParts($io, $path, $first, $options, $total, $progress)
    {
        $ref = null;
        $etags = [];
        $acknowledged = 0;
        $number = 0;
        while (true) {
            $number++;
            $part = self::beginUploadPart($path, $number, $ref, $first, $options);
            $ref = $part->getRef();
            list($method, $headers) = self::uploadPartInstructions($part, $number);
            $bytes = self::readUploadPart($io, $part->getPartsize());

            // Content-Type and Content-Length are added only when the grant names no such header, in any case; a
            // granted Content-Length must be the part's own.
            $contentLength = self::grantedHeader($headers, 'Content-Length');
            if ($contentLength !== null && $contentLength !== (string) strlen($bytes)) {
                throw new \Files\Exception\ApiBadResponseException('Part ' . $number . ' is granted a Content-Length other than its ' . strlen($bytes) . ' bytes');
            }

            if ($contentLength === null) {
                $headers += ['Content-Length' => strlen($bytes)];
            }

            if (self::grantedHeader($headers, 'Content-Type') === null) {
                $headers += ['Content-Type' => 'application/octet-stream'];
            }

            // A part whose grant says it cannot be retried (retry_parts false) is sent once. Otherwise a connection
            // failure is retried as for any request; a grant without retry_parts keeps that policy, which is the SDK's,
            // not a service guarantee.
            $response = Api::sendFile($part->getUploadUri(), $method, $bytes, $headers, $part->getRetryParts() !== false);
            $etags[] = ['etag' => self::uploadPartReceipt($response, $number), 'part' => $number];
            $acknowledged += strlen($bytes);

            if ($progress !== null) {
                call_user_func($progress, [
                    'total_bytes' => $total,
                    'acknowledged_bytes' => $acknowledged,
                    'part_number' => $number,
                    'part_bytes' => strlen($bytes),
                ]);
            }

            if (feof($io)) {
                return [$ref, $etags, $acknowledged];
            }
        }
    }

    private static function underscoreDestinationPath($root, $id, $relativePath = null)
    {
        return \Files\Util\PathUtil::normalize('_', $root, strval($id), $relativePath);
    }

    // Uploads the file at $sourceFilePath to $destinationPath part by part (uploadParts), and returns nothing. The
    // upload ends at $destinationPath: the path a part reports is the API's own, which need not resolve in the caller's
    // context (in a workspace it can be the site path, _/Workspaces/<id>/...). $params: see splitUploadParams;
    // provided_mtime defaults to the file's modification time. $options: those of the generated methods, for the
    // upload's API requests, and progress, a callable given an array of total_bytes (the file's size),
    // acknowledged_bytes, part_number and part_bytes after each part is acknowledged; anything it throws stops the
    // upload and is thrown here. The file is closed on every path. With transfer, the upload runs adaptively
    // (uploadAdaptively), and progress also gets elapsed_seconds and bytes_per_second.
    public static function uploadFile($destinationPath, $sourceFilePath, $params = [], $options = [])
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

        list($first, $end) = self::splitUploadParams($params);
        list($apiOptions, $progress, $transfer) = self::splitUploadOptions($options);
        if ($transfer !== null) {
            return self::uploadAdaptively('uploadFile', $destinationPath, $sourceFilePath, $params, $apiOptions, $progress, $transfer);
        }

        $sourceFileHandle = fopen($sourceFilePath, 'rb');
        try {
            $filesize = filesize($sourceFilePath);
            list($ref, $etags, $bytesWritten) = self::uploadParts($sourceFileHandle, $destinationPath, $first, $apiOptions, $filesize, $progress);
        } finally {
            if (is_resource($sourceFileHandle)) {
                fclose($sourceFileHandle);
            }
        }

        self::create($destinationPath, array_merge([
            'action' => 'end',
            'ref' => $ref,
            'etags' => $etags,
            'provided_mtime' => date('c', filemtime($sourceFilePath)),
            'size' => $filesize
        ], $end), $apiOptions);

        return null;
    }

    public static function uploadToRemoteServer($remoteServerId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath), $sourceFilePath, $params, $options);
    }

    public static function uploadToSnapshot($snapshotId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath), $sourceFilePath, $params, $options);
    }

    public static function uploadToChildSite($siteId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('Sites', $siteId, $destinationPath), $sourceFilePath, $params, $options);
    }


    // Uploads $data, a string or a stream read from where it stands, to $destinationPath as uploadFile does, and
    // returns nothing; provided_mtime defaults to now, and progress gives total_bytes for a string only (null for a
    // stream). A caller's stream is closed once every part is acknowledged and progress has returned, before the upload
    // ends, so a failure of the end leaves it closed; any earlier failure leaves it open. A string's own stream is
    // closed on every path. With transfer, the upload runs adaptively (uploadAdaptively), a stream's parts concurrently
    // only when transfer gives its size.
    public static function uploadData($destinationPath, $data, $params = [], $options = [])
    {
        if (!$destinationPath) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destinationPath');
        }

        if (!$data) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: data');
        }

        list($first, $end) = self::splitUploadParams($params);
        list($apiOptions, $progress, $transfer) = self::splitUploadOptions($options);
        if ($transfer !== null) {
            return self::uploadAdaptively('uploadData', $destinationPath, $data, $params, $apiOptions, $progress, $transfer);
        }

        $owned = !is_resource($data);
        if ($owned) {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $data);
            rewind($stream);
        } else {
            $stream = $data;
        }

        try {
            list($ref, $etags, $bytesWritten) = self::uploadParts($stream, $destinationPath, $first, $apiOptions, $owned ? strlen($data) : null, $progress);
        } finally {
            if ($owned) {
                fclose($stream);
            }
        }

        if (!$owned) {
            fclose($stream);
        }

        self::create($destinationPath, array_merge([
            'action' => 'end',
            'ref' => $ref,
            'etags' => $etags,
            'provided_mtime' => date('c', time()),
            'size' => $bytesWritten
        ], $end), $apiOptions);

        return null;
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

        // Header names are case-insensitive. A whole response has no Content-Range; one without Content-Length (a
        // chunked body) reports 0 received, as before.
        $contentLength = Api::headerValues($response->headers, 'Content-Length');
        $contentRange = Api::headerValues($response->headers, 'Content-Range');
        $fetchedLength = $contentLength ? intval($contentLength[0]) : 0;
        $totalLength = $contentRange
            ? intval(explode('/', $contentRange[0])[1])
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

    // Downloads the file at $remoteFilePath to $destinationPath, retrying a failed download (when $enableRetry) by
    // resuming from the bytes already written. With $options['transfer'] (the options of a Files\Transfer\TransferJob),
    // the download runs adaptively instead: version-bound ranges fetched concurrently and verified, or a whole response
    // restarted from its first byte, written to a staging file that replaces $destinationPath only once complete, on
    // 64-bit PHP with a native monotonic clock only. Its other options are progress and the generated methods' options
    // for its API request; $enableRetry false allows no retries. Returns the bytes received and the total.
    public static function downloadToFile($remoteFilePath, $destinationPath, $enableRetry = true, $options = [])
    {
        if ($options !== []) {
            if (!is_array($options) || !array_key_exists('transfer', $options) || !is_array($options['transfer'])) {
                throw new \Files\Exception\InvalidParameterException('$options apply to downloadToFile only with $options[\'transfer\'], an array of transfer options');
            }

            $transfer = $options['transfer'];
            unset($options['transfer']);
            if (!$enableRetry) {
                $transfer['max_retries'] = 0;
            }

            $job = new \Files\Transfer\TransferJob($transfer);
            $handle = $job->downloadToFile($remoteFilePath, $destinationPath, $options);
            $job->wait();
            return $handle->result();
        }

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

    public function copyToRemoteServer($remoteServerId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath);
        return $this->copy($params);
    }

    public function copyToSnapshot($snapshotId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath);
        return $this->copy($params);
    }

    public function copyToChildSite($siteId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Sites', $siteId, $destinationPath);
        return $this->copy($params);
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

    public function moveToRemoteServer($remoteServerId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath);
        return $this->move($params);
    }

    public function moveToSnapshot($snapshotId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath);
        return $this->move($params);
    }

    public function moveToChildSite($siteId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Sites', $siteId, $destinationPath);
        return $this->move($params);
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
    // int64 # ID of the Sync that created the file/folder
    public function getCreatedBySyncId()
    {
        return @$this->attributes['created_by_sync_id'];
    }

    public function setCreatedBySyncId($value)
    {
        return $this->attributes['created_by_sync_id'] = $value;
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
    // int64 # ID of the Sync that last modified the file/folder
    public function getLastModifiedBySyncId()
    {
        return @$this->attributes['last_modified_by_sync_id'];
    }

    public function setLastModifiedBySyncId($value)
    {
        return $this->attributes['last_modified_by_sync_id'] = $value;
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
    // DirectConnectionInfo # Optional direct connection information for direct Agent transfer attempts
    public function getDirectConnectionInfo()
    {
        return @$this->attributes['direct_connection_info'];
    }

    public function setDirectConnectionInfo($value)
    {
        return $this->attributes['direct_connection_info'] = $value;
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
    // boolean # If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
    public function getCopyBehaviors()
    {
        return @$this->attributes['copy_behaviors'];
    }

    public function setCopyBehaviors($value)
    {
        return $this->attributes['copy_behaviors'] = $value;
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
    // boolean # If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
    public function getBufferedUpload()
    {
        return @$this->attributes['buffered_upload'];
    }

    public function setBufferedUpload($value)
    {
        return $this->attributes['buffered_upload'] = $value;
    }
    // boolean # Include optional direct connection information for a direct Agent transfer attempt?
    public function getWithDirectConnectionInfo()
    {
        return @$this->attributes['with_direct_connection_info'];
    }

    public function setWithDirectConnectionInfo($value)
    {
        return $this->attributes['with_direct_connection_info'] = $value;
    }

    // Download File
    //
    // Parameters:
    //   action - string - Can be blank, `redirect` or `stat`.  If set to `stat`, we will return file information but without a download URL, and without logging a download.  If set to `redirect` we will serve a 302 redirect directly to the file.  This is used for integrations with Zapier, and is not recommended for most integrations.
    //   preview_size - string - Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
    //   with_previews - boolean - Include file preview information?
    //   with_priority_color - boolean - Include file priority color information?
    //   with_direct_connection_info - boolean - Include optional direct connection information for a direct Agent transfer attempt?
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

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $this->options);
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

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'PATCH', $params, $this->options);
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

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    // List the contents of a ZIP file
    public function zipListContents($params = [])
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

        $response = Api::sendRequest('/file_actions/zip_list/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $this->options);
        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new ZipListEntry((array) $obj, $this->options);
        }

        return $return_array;
    }

    // Delete File/Folder and return its File Action.  A pending or processing deletion continues in the background after this returns; check its File Migration for completion.  Waiting for the File Migration does not cancel the deletion
    //
    // Parameters:
    //   recursive - boolean - If true, will recursively delete folders.  Otherwise, will error on non-empty folders.
    public function beginDelete($params = [])
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

        $response = Api::sendRequest('/file_actions/delete/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Copy File/Folder
    //
    // Parameters:
    //   destination (required) - string - Copy destination path.
    //   copy_behaviors - boolean - If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
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

        $response = Api::sendRequest('/file_actions/copy/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
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

        $response = Api::sendRequest('/file_actions/move/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Transform a file and save the output to a destination path
    //
    // Parameters:
    //   destination (required) - string - Destination file path for the transformed output.
    //   transform_type (required) - string - Transform type. Supported values are `image_convert`, `document_convert`, and `files_transform_script_execute`.
    //   target_format (required) - string - Destination format to create.
    //   script - string - Files TransformScript source. Required when transform_type is `files_transform_script_execute`.
    //   width - int64 - Maximum output width for image_convert.
    //   height - int64 - Maximum output height for image_convert.
    //   overwrite - boolean - Overwrite existing file in the destination?
    public function transform($params = [])
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

        if (!@$params['transform_type']) {
            if (@$this->transform_type) {
                $params['transform_type'] = $this->transform_type;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: transform_type');
            }
        }

        if (!@$params['target_format']) {
            if (@$this->target_format) {
                $params['target_format'] = $this->target_format;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: target_format');
            }
        }

        if (@$params['path'] && !is_string(@$params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (@$params['transform_type'] && !is_string(@$params['transform_type'])) {
            throw new \Files\Exception\InvalidParameterException('$transform_type must be of type string; received ' . gettype(@$params['transform_type']));
        }

        if (@$params['target_format'] && !is_string(@$params['target_format'])) {
            throw new \Files\Exception\InvalidParameterException('$target_format must be of type string; received ' . gettype(@$params['target_format']));
        }

        if (@$params['script'] && !is_string(@$params['script'])) {
            throw new \Files\Exception\InvalidParameterException('$script must be of type string; received ' . gettype(@$params['script']));
        }

        if (@$params['width'] && !is_int(@$params['width'])) {
            throw new \Files\Exception\InvalidParameterException('$width must be of type int; received ' . gettype(@$params['width']));
        }

        if (@$params['height'] && !is_int(@$params['height'])) {
            throw new \Files\Exception\InvalidParameterException('$height must be of type int; received ' . gettype(@$params['height']));
        }

        $response = Api::sendRequest('/file_actions/transform/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Decrypt a GPG-encrypted file and save it to a destination path
    //
    // Parameters:
    //   destination (required) - string - Destination file path for the decrypted file.
    //   gpg_key_ids - array(int64) - GPG Key IDs to decrypt with. If omitted, every accessible private GPG key in the source workspace is used.
    //   gpg_key_partner_id - int64 - Partner ID whose GPG keys should be used for decryption.
    //   use_all_private_keys - boolean - Use every accessible private GPG key in the source workspace for decryption.
    //   ignore_mdc_error - boolean - Ignore errors from the MDC (modification detection code) check.
    //   overwrite - boolean - Overwrite existing file in the destination?
    public function gpgDecrypt($params = [])
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

        if (@$params['gpg_key_ids'] && !is_array(@$params['gpg_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_ids must be of type array; received ' . gettype(@$params['gpg_key_ids']));
        }

        if (@$params['gpg_key_partner_id'] && !is_int(@$params['gpg_key_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_partner_id must be of type int; received ' . gettype(@$params['gpg_key_partner_id']));
        }

        $response = Api::sendRequest('/file_actions/gpg_decrypt/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Encrypt a file with GPG and save it to a destination path
    //
    // Parameters:
    //   destination (required) - string - Destination file path for the encrypted file.
    //   gpg_key_ids - array(int64) - GPG Key IDs to encrypt with.
    //   gpg_key_partner_id - int64 - Partner ID whose GPG keys should be used for encryption.
    //   signing_key_id - int64 - Optional GPG Key ID to sign with.
    //   armor - boolean - Output ASCII-armored encrypted data.
    //   overwrite - boolean - Overwrite existing file in the destination?
    public function gpgEncrypt($params = [])
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

        if (@$params['gpg_key_ids'] && !is_array(@$params['gpg_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_ids must be of type array; received ' . gettype(@$params['gpg_key_ids']));
        }

        if (@$params['gpg_key_partner_id'] && !is_int(@$params['gpg_key_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_partner_id must be of type int; received ' . gettype(@$params['gpg_key_partner_id']));
        }

        if (@$params['signing_key_id'] && !is_int(@$params['signing_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$signing_key_id must be of type int; received ' . gettype(@$params['signing_key_id']));
        }

        $response = Api::sendRequest('/file_actions/gpg_encrypt/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction((array) (@$response->data ?: []), $this->options);
    }

    // Extract a ZIP file to a destination folder
    //
    // Parameters:
    //   destination (required) - string - Destination folder path for extracted files.
    //   filename - string - Optional single entry filename to extract.
    //   overwrite - boolean - Overwrite existing files in the destination?
    public function unzip($params = [])
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

        if (@$params['filename'] && !is_string(@$params['filename'])) {
            throw new \Files\Exception\InvalidParameterException('$filename must be of type string; received ' . gettype(@$params['filename']));
        }

        $response = Api::sendRequest('/file_actions/unzip', 'POST', $params, $this->options);
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
    //   buffered_upload - boolean - If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
    //   with_direct_connection_info - boolean - Include optional direct connection information for a direct Agent transfer attempt?
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

        $response = Api::sendRequest('/file_actions/begin_upload/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new FileUploadPart((array) $obj, $this->options);
        }

        return $return_array;
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
    //   custom_metadata - object - Custom metadata map to save when `action=end` completes the upload.  Replaces existing metadata; an empty map clears it.  No separate metadata-edit permission is required.  Supported on native files and configured remote mounts, excluding remote server automount paths.  Limited to 32 keys, 256 characters per key and 1024 characters per value.
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
    //   copy_behaviors - boolean - If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
    //   structure - string - If copying folder, copy just the structure?
    //   with_rename - boolean - Allow file rename instead of overwrite?
    //   buffered_upload - boolean - If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
    //   with_direct_connection_info - boolean - Include optional direct connection information for a direct Agent transfer attempt?
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

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $options);

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

        $response = Api::sendRequest('/file_actions/metadata/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        return new File((array) (@$response->data ?: []), $options);
    }
    public static function get($path, $params = [], $options = [])
    {
        return self::find($path, $params, $options);
    }

    // Parameters:
    //   paths (required) - array(string) - Paths to include in the ZIP.
    //   destination (required) - string - Destination file path for the ZIP.
    //   overwrite - boolean - Overwrite existing file in the destination?
    public static function zip($params = [], $options = [])
    {
        if (!@$params['paths']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: paths');
        }

        if (!@$params['destination']) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
        }

        if (@$params['paths'] && !is_array(@$params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (@$params['destination'] && !is_string(@$params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        $response = Api::sendRequest('/file_actions/zip', 'POST', $params, $options);

        return new FileAction((array) (@$response->data ?: []), $options);
    }
}
