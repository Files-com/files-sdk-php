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
 * @property string|null $action The action to perform. Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
 * @property int|null $length Length of file.
 * @property bool|null $mkdir_parents Create parent directories if they do not exist?
 * @property int|null $part Part if uploading a part.
 * @property int|null $parts How many parts to fetch?
 * @property string|null $ref
 * @property int|null $restart File byte offset to restart from.
 * @property bool|null $copy_behaviors If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
 * @property string|null $structure If copying folder, copy just the structure?
 * @property bool|null $with_rename Allow file rename instead of overwrite?
 * @property bool|null $buffered_upload If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
 * @property bool|null $with_direct_connection_info Include optional direct connection information for a direct Agent transfer attempt?
 * @package Files
 */
class File
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

    /**
     * Upload a local file to a Files.com path.
     *
     * Uploads the file at $sourceFilePath to $destinationPath part by part (uploadParts), and returns nothing. The
     * upload ends at $destinationPath: the path a part reports is the API's own, which need not resolve in the caller's
     * context (in a workspace it can be the site path, _/Workspaces/<id>/...). $params: see splitUploadParams;
     * provided_mtime defaults to the file's modification time. $options: those of the generated methods, for the
     * upload's API requests, and progress, a callable given an array of total_bytes (the file's size),
     * acknowledged_bytes, part_number and part_bytes after each part is acknowledged; anything it throws stops the
     * upload and is thrown here. The file is closed on every path. With transfer, the upload runs adaptively
     * (uploadAdaptively), and progress also gets elapsed_seconds and bytes_per_second.
     *
     * @param string $destinationPath Destination path on Files.com.
     * @param string $sourceFilePath Local source file path.
     * @param array $params Upload parameters, including provided_mtime and custom_metadata.
     * @param array $options Request options, an optional progress callback and adaptive transfer options.
     * @return null
     */
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

    /**
     * Upload a local file to RemoteServers.
     *
     * @param int|string $remoteServerId Destination remote_server ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param string $sourceFilePath Local source file path.
     * @param array $params API parameters for the operation.
     * @param array $options Request options and an optional progress callback.
     * @return null
     */
    public static function uploadToRemoteServer($remoteServerId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath), $sourceFilePath, $params, $options);
    }

    /**
     * Upload a local file to Snapshots.
     *
     * @param int|string $snapshotId Destination snapshot ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param string $sourceFilePath Local source file path.
     * @param array $params API parameters for the operation.
     * @param array $options Request options and an optional progress callback.
     * @return null
     */
    public static function uploadToSnapshot($snapshotId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath), $sourceFilePath, $params, $options);
    }

    /**
     * Upload a local file to Sites.
     *
     * @param int|string $siteId Destination child_site ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param string $sourceFilePath Local source file path.
     * @param array $params API parameters for the operation.
     * @param array $options Request options and an optional progress callback.
     * @return null
     */
    public static function uploadToChildSite($siteId, $destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        return self::uploadFile(self::underscoreDestinationPath('Sites', $siteId, $destinationPath), $sourceFilePath, $params, $options);
    }


    /**
     * Upload a string or readable stream to a Files.com path.
     *
     * Uploads $data, a string or a stream read from where it stands, to $destinationPath as uploadFile does, and
     * returns nothing; provided_mtime defaults to now, and progress gives total_bytes for a string only (null for a
     * stream). A caller's stream is closed once every part is acknowledged and progress has returned, before the upload
     * ends, so a failure of the end leaves it closed; any earlier failure leaves it open. A string's own stream is
     * closed on every path. With transfer, the upload runs adaptively (uploadAdaptively), a stream's parts concurrently
     * only when transfer gives its size.
     *
     * @param string $destinationPath Destination path on Files.com.
     * @param string|resource $data Source bytes, or a stream read from its current position.
     * @param array $params Upload parameters, including provided_mtime and custom_metadata.
     * @param array $options Request options, an optional progress callback and adaptive transfer options.
     * @return null
     */
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

    /**
     * Get a temporary URL for downloading a file.
     *
     * @param string $remoteFilePath Path on Files.com.
     * @return string
     */
    public static function getDownloadUrl($remoteFilePath)
    {
        $file = new File();
        $file->path = $remoteFilePath;

        $response = $file->download();
        return $response->download_uri;
    }

    /**
     * Download a file to a stream or local path.
     *
     * @param string $remoteFilePath Path on Files.com.
     * @param resource|string $destinationStream Writable stream or local destination path.
     * @param int|null $startOffset First byte to download, inclusive.
     * @param int|null $endOffset Last byte to download, inclusive.
     * @return \stdClass Object with received and total byte counts.
     */
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

    /**
     * Download an inclusive byte range to a local file.
     *
     * @param string $remoteFilePath Path on Files.com.
     * @param string $destinationPath Local destination path.
     * @param int|null $startOffset First byte to download, inclusive.
     * @param int|null $endOffset Last byte to download, inclusive.
     * @return \stdClass Object with received and total byte counts.
     */
    public static function partialDownloadToFile($remoteFilePath, $destinationPath, $startOffset = null, $endOffset = null)
    {
        return self::downloadToStream($remoteFilePath, $destinationPath, $startOffset, $endOffset);
    }

    /**
     * Resume a download after the existing local file contents.
     *
     * @param string $remoteFilePath Path on Files.com.
     * @param string $destinationPath Local file to append to.
     * @return \stdClass Object with received and total byte counts.
     */
    public static function resumeDownloadToFile($remoteFilePath, $destinationPath)
    {
        $readStream = fopen($destinationPath, 'r');
        fseek($readStream, 0, SEEK_END);
        $rangeStart = ftell($readStream);
        fclose($readStream);

        // The append handle is the SDK's own, closed before this returns or throws, even while the caller keeps the
        // exception (Guzzle usually closes it itself once the download completes). A failure to close it is thrown only
        // when the download succeeded: it never replaces the download's own failure, nor passes for success.
        $stream = fopen($destinationPath, 'a');
        $completed = false;
        try {
            $result = self::downloadToStream($remoteFilePath, $stream, $rangeStart);
            $completed = true;
        } finally {
            if (is_resource($stream)) {
                if ($completed) {
                    fclose($stream);
                } else {
                    try {
                        fclose($stream);
                    } catch (\Exception $closeFailure) {
                        // The download's own failure is reported instead.
                    } catch (\Throwable $closeFailure) {
                        // As above, for an error from PHP 7 or later.
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Download to a local file, optionally using adaptive transfers.
     *
     * Downloads the file at $remoteFilePath to $destinationPath, retrying a failed download (when $enableRetry) by
     * resuming from the bytes already written. With $options['transfer'] (the options of a Files\Transfer\TransferJob),
     * the download runs adaptively instead: version-bound ranges fetched concurrently and verified, or a whole response
     * restarted from its first byte, written to a staging file that replaces $destinationPath only once complete, on
     * 64-bit PHP with a native monotonic clock only. Its other options are progress and the generated methods' options
     * for its API request; $enableRetry false allows no retries. Returns the bytes received and the total.
     *
     * @param string $remoteFilePath Path on Files.com.
     * @param string $destinationPath Local destination path.
     * @param bool $enableRetry Enable retries; false disables them, including adaptive transfers.
     * @param array $options Adaptive transfer and request options; requires a transfer array when nonempty.
     * @return \stdClass Object with received and total byte counts.
     */
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

    /**
     * Delete a Files.com path.
     *
     * @param string $path Path on Files.com.
     * @return void
     */
    public static function deletePath($path)
    {
        if (!$path) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        $file = new File();
        return $file->delete(['path' => $path]);
    }

    /**
     * Start copying this file to another Files.com path.
     *
     * @param string $destinationFilePath Destination path on Files.com.
     * @return \stdClass
     */
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

    /**
     * Copy this file to RemoteServers.
     *
     * @param int|string $remoteServerId Destination remote_server ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function copyToRemoteServer($remoteServerId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath);
        return $this->copy($params);
    }

    /**
     * Copy this file to Snapshots.
     *
     * @param int|string $snapshotId Destination snapshot ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function copyToSnapshot($snapshotId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath);
        return $this->copy($params);
    }

    /**
     * Copy this file to Sites.
     *
     * @param int|string $siteId Destination child_site ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function copyToChildSite($siteId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Sites', $siteId, $destinationPath);
        return $this->copy($params);
    }


    /**
     * Start moving this file to another Files.com path.
     *
     * @param string $destinationFilePath Destination path on Files.com.
     * @return \stdClass
     */
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

    /**
     * Move this file to RemoteServers.
     *
     * @param int|string $remoteServerId Destination remote_server ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function moveToRemoteServer($remoteServerId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('RemoteServers', $remoteServerId, $destinationPath);
        return $this->move($params);
    }

    /**
     * Move this file to Snapshots.
     *
     * @param int|string $snapshotId Destination snapshot ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function moveToSnapshot($snapshotId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Snapshots', $snapshotId, $destinationPath);
        return $this->move($params);
    }

    /**
     * Move this file to Sites.
     *
     * @param int|string $siteId Destination child_site ID.
     * @param string $destinationPath Relative path inside the destination.
     * @param array $params API parameters for the operation.
     * @return \Files\Model\FileAction
     */
    public function moveToChildSite($siteId, $destinationPath, $params = [])
    {
        $params['destination'] = self::underscoreDestinationPath('Sites', $siteId, $destinationPath);
        return $this->move($params);
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
     * The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
     *
     * @return string|null
     */
    public function getAction()
    {
        return isset($this->attributes['action']) ? $this->attributes['action'] : null;
    }

    /**
     * The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setAction($value)
    {
        return $this->attributes['action'] = $value;
    }
    /**
     * Length of file.
     *
     * @return int|null
     */
    public function getLength()
    {
        return isset($this->attributes['length']) ? $this->attributes['length'] : null;
    }

    /**
     * Length of file.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setLength($value)
    {
        return $this->attributes['length'] = $value;
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
     * Part if uploading a part.
     *
     * @return int|null
     */
    public function getPart()
    {
        return isset($this->attributes['part']) ? $this->attributes['part'] : null;
    }

    /**
     * Part if uploading a part.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setPart($value)
    {
        return $this->attributes['part'] = $value;
    }
    /**
     * How many parts to fetch?
     *
     * @return int|null
     */
    public function getParts()
    {
        return isset($this->attributes['parts']) ? $this->attributes['parts'] : null;
    }

    /**
     * How many parts to fetch?
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setParts($value)
    {
        return $this->attributes['parts'] = $value;
    }
    /**
     * Read ref.
     *
     * @return string|null
     */
    public function getRef()
    {
        return isset($this->attributes['ref']) ? $this->attributes['ref'] : null;
    }

    /**
     * Set ref locally.
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setRef($value)
    {
        return $this->attributes['ref'] = $value;
    }
    /**
     * File byte offset to restart from.
     *
     * @return int|null
     */
    public function getRestart()
    {
        return isset($this->attributes['restart']) ? $this->attributes['restart'] : null;
    }

    /**
     * File byte offset to restart from.
     *
     * @param int|null $value
     * @return int|null The assigned value.
     */
    public function setRestart($value)
    {
        return $this->attributes['restart'] = $value;
    }
    /**
     * If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
     *
     * @return bool|null
     */
    public function getCopyBehaviors()
    {
        return isset($this->attributes['copy_behaviors']) ? $this->attributes['copy_behaviors'] : null;
    }

    /**
     * If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setCopyBehaviors($value)
    {
        return $this->attributes['copy_behaviors'] = $value;
    }
    /**
     * If copying folder, copy just the structure?
     *
     * @return string|null
     */
    public function getStructure()
    {
        return isset($this->attributes['structure']) ? $this->attributes['structure'] : null;
    }

    /**
     * If copying folder, copy just the structure?
     *
     * @param string|null $value
     * @return string|null The assigned value.
     */
    public function setStructure($value)
    {
        return $this->attributes['structure'] = $value;
    }
    /**
     * Allow file rename instead of overwrite?
     *
     * @return bool|null
     */
    public function getWithRename()
    {
        return isset($this->attributes['with_rename']) ? $this->attributes['with_rename'] : null;
    }

    /**
     * Allow file rename instead of overwrite?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setWithRename($value)
    {
        return $this->attributes['with_rename'] = $value;
    }
    /**
     * If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
     *
     * @return bool|null
     */
    public function getBufferedUpload()
    {
        return isset($this->attributes['buffered_upload']) ? $this->attributes['buffered_upload'] : null;
    }

    /**
     * If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setBufferedUpload($value)
    {
        return $this->attributes['buffered_upload'] = $value;
    }
    /**
     * Include optional direct connection information for a direct Agent transfer attempt?
     *
     * @return bool|null
     */
    public function getWithDirectConnectionInfo()
    {
        return isset($this->attributes['with_direct_connection_info']) ? $this->attributes['with_direct_connection_info'] : null;
    }

    /**
     * Include optional direct connection information for a direct Agent transfer attempt?
     *
     * @param bool|null $value
     * @return bool|null The assigned value.
     */
    public function setWithDirectConnectionInfo($value)
    {
        return $this->attributes['with_direct_connection_info'] = $value;
    }

    /**
     * Download File
     *
     * GET /files/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `action` (string, optional): Can be blank, `redirect` or `stat`.  If set to `stat`, we will return file information but without a download URL, and without logging a download.  If set to `redirect` we will serve a 302 redirect directly to the file.  This is used for integrations with Zapier, and is not recommended for most integrations.
     *     - `preview_size` (string, optional): Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
     *     - `with_previews` (bool, optional): Include file preview information?
     *     - `with_priority_color` (bool, optional): Include file priority color information?
     *     - `with_direct_connection_info` (bool, optional): Include optional direct connection information for a direct Agent transfer attempt?
     * @return \Files\Model\File
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function download($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (isset($params['preview_size']) && !is_string($params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        if (isset($params['with_previews']) && !is_scalar($params['with_previews'])) {
            throw new \Files\Exception\InvalidParameterException('$with_previews must be of type boolean; received ' . gettype(@$params['with_previews']));
        }

        if (isset($params['with_priority_color']) && !is_scalar($params['with_priority_color'])) {
            throw new \Files\Exception\InvalidParameterException('$with_priority_color must be of type boolean; received ' . gettype(@$params['with_priority_color']));
        }

        if (isset($params['with_direct_connection_info']) && !is_scalar($params['with_direct_connection_info'])) {
            throw new \Files\Exception\InvalidParameterException('$with_direct_connection_info must be of type boolean; received ' . gettype(@$params['with_direct_connection_info']));
        }

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $this->options);
        return new File(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    // Saves the changes, then refreshes this object with the saved attributes, which it also returns as a separate
    // File with this object's options. A response that is not a JSON object raises
    // ApiBadResponseException and leaves this object unchanged.
    //
    /**
     * Update File/Folder Metadata
     *
     * PATCH /files/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `custom_metadata` (array|object, optional): Custom metadata map. Replaces existing metadata; an empty map clears it. Requires Full or Admin permission. Uploaders with Write permission can supply custom metadata when completing an upload instead.
     *     - `provided_mtime` (string, optional): Modified time of file.
     *     - `priority_color` (string, optional): Priority/Bookmark color of file.
     * @return \Files\Model\File
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function update($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['provided_mtime']) && !is_string($params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        if (isset($params['priority_color']) && !is_string($params['priority_color'])) {
            throw new \Files\Exception\InvalidParameterException('$priority_color must be of type string; received ' . gettype(@$params['priority_color']));
        }

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'PATCH', $params, $this->options);
        $updated = new File(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
        $this->attributes = $updated->attributes;
        return $updated;
    }

    /**
     * Delete File/Folder
     *
     * DELETE /files/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `recursive` (bool, optional): If true, will recursively delete folders.  Otherwise, will error on non-empty folders.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function delete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'DELETE', $params, $this->options);
        return;
    }

    /**
     * Delete File/Folder
     *
     * DELETE /files/{path}
     *
     * Alias of delete().
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `recursive` (bool, optional): If true, will recursively delete folders.  Otherwise, will error on non-empty folders.
     * @return void
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function destroy($params = [])
    {
        $this->delete($params);
        return;
    }

    /**
     * List the contents of a ZIP file
     *
     * GET /file_actions/zip_list/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     * @return \Files\Model\ZipListEntry[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function zipListContents($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        $response = Api::sendRequest('/file_actions/zip_list/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $this->options);
        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new ZipListEntry(Api::modelAttributes($obj, $response->status), $this->options);
        }

        return $return_array;
    }

    /**
     * Delete File/Folder and return its File Action.  A pending or processing deletion continues in the background after this returns; check its File Migration for completion.  Waiting for the File Migration does not cancel the deletion
     *
     * POST /file_actions/delete/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `recursive` (bool, optional): If true, will recursively delete folders.  Otherwise, will error on non-empty folders.
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function beginDelete($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['recursive']) && !is_scalar($params['recursive'])) {
            throw new \Files\Exception\InvalidParameterException('$recursive must be of type boolean; received ' . gettype(@$params['recursive']));
        }

        $response = Api::sendRequest('/file_actions/delete/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Copy File/Folder
     *
     * POST /file_actions/copy/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `destination` (string, required): Copy destination path.
     *     - `copy_behaviors` (bool, optional): If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
     *     - `structure` (bool, optional): Copy structure only?
     *     - `overwrite` (bool, optional): Overwrite existing file(s) in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function copy($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['copy_behaviors']) && !is_scalar($params['copy_behaviors'])) {
            throw new \Files\Exception\InvalidParameterException('$copy_behaviors must be of type boolean; received ' . gettype(@$params['copy_behaviors']));
        }

        if (isset($params['structure']) && !is_scalar($params['structure'])) {
            throw new \Files\Exception\InvalidParameterException('$structure must be of type boolean; received ' . gettype(@$params['structure']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/copy/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Move File/Folder
     *
     * POST /file_actions/move/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `destination` (string, required): Move destination path.
     *     - `overwrite` (bool, optional): Overwrite existing file(s) in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function move($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/move/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Transform a file and save the output to a destination path
     *
     * POST /file_actions/transform/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `destination` (string, required): Destination file path for the transformed output.
     *     - `transform_type` (string, required): Transform type. Supported values are `image_convert`, `document_convert`, and `files_transform_script_execute`.
     *     - `target_format` (string, required): Destination format to create.
     *     - `script` (string, optional): Files TransformScript source. Required when transform_type is `files_transform_script_execute`.
     *     - `width` (int, optional): Maximum output width for image_convert.
     *     - `height` (int, optional): Maximum output height for image_convert.
     *     - `overwrite` (bool, optional): Overwrite existing file in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function transform($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (empty($params['transform_type'])) {
            if (@$this->transform_type) {
                $params['transform_type'] = $this->transform_type;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: transform_type');
            }
        }

        if (empty($params['target_format'])) {
            if (@$this->target_format) {
                $params['target_format'] = $this->target_format;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: target_format');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['transform_type']) && !is_string($params['transform_type'])) {
            throw new \Files\Exception\InvalidParameterException('$transform_type must be of type string; received ' . gettype(@$params['transform_type']));
        }

        if (isset($params['target_format']) && !is_string($params['target_format'])) {
            throw new \Files\Exception\InvalidParameterException('$target_format must be of type string; received ' . gettype(@$params['target_format']));
        }

        if (isset($params['script']) && !is_string($params['script'])) {
            throw new \Files\Exception\InvalidParameterException('$script must be of type string; received ' . gettype(@$params['script']));
        }

        if (isset($params['width']) && !is_int($params['width'])) {
            throw new \Files\Exception\InvalidParameterException('$width must be of type int; received ' . gettype(@$params['width']));
        }

        if (isset($params['height']) && !is_int($params['height'])) {
            throw new \Files\Exception\InvalidParameterException('$height must be of type int; received ' . gettype(@$params['height']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/transform/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Decrypt a GPG-encrypted file and save it to a destination path
     *
     * POST /file_actions/gpg_decrypt/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `destination` (string, required): Destination file path for the decrypted file.
     *     - `gpg_key_ids` (int[], optional): GPG Key IDs to decrypt with. If omitted, every accessible private GPG key in the source workspace is used.
     *     - `gpg_key_partner_id` (int, optional): Partner ID whose GPG keys should be used for decryption.
     *     - `use_all_private_keys` (bool, optional): Use every accessible private GPG key in the source workspace for decryption.
     *     - `ignore_mdc_error` (bool, optional): Ignore errors from the MDC (modification detection code) check.
     *     - `overwrite` (bool, optional): Overwrite existing file in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function gpgDecrypt($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['gpg_key_ids']) && !is_array($params['gpg_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_ids must be of type array; received ' . gettype(@$params['gpg_key_ids']));
        }

        if (isset($params['gpg_key_partner_id']) && !is_int($params['gpg_key_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_partner_id must be of type int; received ' . gettype(@$params['gpg_key_partner_id']));
        }

        if (isset($params['use_all_private_keys']) && !is_scalar($params['use_all_private_keys'])) {
            throw new \Files\Exception\InvalidParameterException('$use_all_private_keys must be of type boolean; received ' . gettype(@$params['use_all_private_keys']));
        }

        if (isset($params['ignore_mdc_error']) && !is_scalar($params['ignore_mdc_error'])) {
            throw new \Files\Exception\InvalidParameterException('$ignore_mdc_error must be of type boolean; received ' . gettype(@$params['ignore_mdc_error']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/gpg_decrypt/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Encrypt a file with GPG and save it to a destination path
     *
     * POST /file_actions/gpg_encrypt/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `destination` (string, required): Destination file path for the encrypted file.
     *     - `gpg_key_ids` (int[], optional): GPG Key IDs to encrypt with.
     *     - `gpg_key_partner_id` (int, optional): Partner ID whose GPG keys should be used for encryption.
     *     - `signing_key_id` (int, optional): Optional GPG Key ID to sign with.
     *     - `armor` (bool, optional): Output ASCII-armored encrypted data.
     *     - `overwrite` (bool, optional): Overwrite existing file in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function gpgEncrypt($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['gpg_key_ids']) && !is_array($params['gpg_key_ids'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_ids must be of type array; received ' . gettype(@$params['gpg_key_ids']));
        }

        if (isset($params['gpg_key_partner_id']) && !is_int($params['gpg_key_partner_id'])) {
            throw new \Files\Exception\InvalidParameterException('$gpg_key_partner_id must be of type int; received ' . gettype(@$params['gpg_key_partner_id']));
        }

        if (isset($params['signing_key_id']) && !is_int($params['signing_key_id'])) {
            throw new \Files\Exception\InvalidParameterException('$signing_key_id must be of type int; received ' . gettype(@$params['signing_key_id']));
        }

        if (isset($params['armor']) && !is_scalar($params['armor'])) {
            throw new \Files\Exception\InvalidParameterException('$armor must be of type boolean; received ' . gettype(@$params['armor']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/gpg_encrypt/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Extract a ZIP file to a destination folder
     *
     * POST /file_actions/unzip
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): ZIP file path to extract.
     *     - `destination` (string, required): Destination folder path for extracted files.
     *     - `filename` (string, optional): Optional single entry filename to extract.
     *     - `overwrite` (bool, optional): Overwrite existing files in the destination?
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function unzip($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (empty($params['destination'])) {
            if (@$this->destination) {
                $params['destination'] = $this->destination;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['filename']) && !is_string($params['filename'])) {
            throw new \Files\Exception\InvalidParameterException('$filename must be of type string; received ' . gettype(@$params['filename']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/unzip', 'POST', $params, $this->options);
        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $this->options);
    }

    /**
     * Begin File Upload
     *
     * POST /file_actions/begin_upload/{path}
     *
     * @param array $params Request parameters.
     *     - `path` (string, optional when set on this model): Path to operate on.
     *     - `mkdir_parents` (bool, optional): Create parent directories if they do not exist?
     *     - `part` (int, optional): Part if uploading a part.
     *     - `parts` (int, optional): How many parts to fetch?
     *     - `ref` (string, optional):
     *     - `restart` (int, optional): File byte offset to restart from.
     *     - `size` (int, optional): Total bytes of file being uploaded (include bytes being retained if appending/restarting).
     *     - `with_rename` (bool, optional): Allow file rename instead of overwrite?
     *     - `buffered_upload` (bool, optional): If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
     *     - `with_direct_connection_info` (bool, optional): Include optional direct connection information for a direct Agent transfer attempt?
     * @return \Files\Model\FileUploadPart[]
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public function beginUpload($params = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        if (empty($params['path'])) {
            if (@$this->path) {
                $params['path'] = $this->path;
            } else {
                throw new \Files\Exception\MissingParameterException('Parameter missing: path');
            }
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['mkdir_parents']) && !is_scalar($params['mkdir_parents'])) {
            throw new \Files\Exception\InvalidParameterException('$mkdir_parents must be of type boolean; received ' . gettype(@$params['mkdir_parents']));
        }

        if (isset($params['part']) && !is_int($params['part'])) {
            throw new \Files\Exception\InvalidParameterException('$part must be of type int; received ' . gettype(@$params['part']));
        }

        if (isset($params['parts']) && !is_int($params['parts'])) {
            throw new \Files\Exception\InvalidParameterException('$parts must be of type int; received ' . gettype(@$params['parts']));
        }

        if (isset($params['ref']) && !is_string($params['ref'])) {
            throw new \Files\Exception\InvalidParameterException('$ref must be of type string; received ' . gettype(@$params['ref']));
        }

        if (isset($params['restart']) && !is_int($params['restart'])) {
            throw new \Files\Exception\InvalidParameterException('$restart must be of type int; received ' . gettype(@$params['restart']));
        }

        if (isset($params['size']) && !is_int($params['size'])) {
            throw new \Files\Exception\InvalidParameterException('$size must be of type int; received ' . gettype(@$params['size']));
        }

        if (isset($params['with_rename']) && !is_scalar($params['with_rename'])) {
            throw new \Files\Exception\InvalidParameterException('$with_rename must be of type boolean; received ' . gettype(@$params['with_rename']));
        }

        if (isset($params['buffered_upload']) && !is_scalar($params['buffered_upload'])) {
            throw new \Files\Exception\InvalidParameterException('$buffered_upload must be of type boolean; received ' . gettype(@$params['buffered_upload']));
        }

        if (isset($params['with_direct_connection_info']) && !is_scalar($params['with_direct_connection_info'])) {
            throw new \Files\Exception\InvalidParameterException('$with_direct_connection_info must be of type boolean; received ' . gettype(@$params['with_direct_connection_info']));
        }

        $response = Api::sendRequest('/file_actions/begin_upload/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $this->options);
        $return_array = [];

        foreach (Api::listEntries(isset($response->data) ? $response->data : null, $response->status) as $obj) {
            $return_array[] = new FileUploadPart(Api::modelAttributes($obj, $response->status), $this->options);
        }

        return $return_array;
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
     * Upload File
     *
     * POST /files/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `action` (string, optional): The action to perform.  Can be `append`, `attachment`, `end`, `upload`, `put`, or may not exist
     *     - `custom_metadata` (array|object, optional): Custom metadata map to save when `action=end` completes the upload.  Replaces existing metadata; an empty map clears it.  No separate metadata-edit permission is required.  Supported on native files and configured remote mounts, excluding remote server automount paths.  Limited to 32 keys, 256 characters per key and 1024 characters per value.
     *     - `etags` (array[], optional): Array of maps. Each item contains:
     *         - `etag` (string, required): etag identifier.
     *         - `part` (int, required): Part number.
     *     - `length` (int, optional): Length of file.
     *     - `mkdir_parents` (bool, optional): Create parent directories if they do not exist?
     *     - `part` (int, optional): Part if uploading a part.
     *     - `parts` (int, optional): How many parts to fetch?
     *     - `provided_mtime` (string, optional): User provided modification time.
     *     - `ref` (string, optional):
     *     - `restart` (int, optional): File byte offset to restart from.
     *     - `size` (int, optional): Size of file.
     *     - `copy_behaviors` (bool, optional): If copying a folder, also copy supported behaviors, email notification subscriptions, and per-folder branding to the destination folder tree?
     *     - `structure` (string, optional): If copying folder, copy just the structure?
     *     - `with_rename` (bool, optional): Allow file rename instead of overwrite?
     *     - `buffered_upload` (bool, optional): If true, and the path refers to a destination not stored on Files.com (such as a remote server mount), the upload will be uploaded first to Files.com before being sent to the remote server mount. This can allow clients to upload using parallel parts to a remote server destination that does not offer parallel parts support natively.
     *     - `with_direct_connection_info` (bool, optional): Include optional direct connection information for a direct Agent transfer attempt?
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

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['action']) && !is_string($params['action'])) {
            throw new \Files\Exception\InvalidParameterException('$action must be of type string; received ' . gettype(@$params['action']));
        }

        if (isset($params['length']) && !is_int($params['length'])) {
            throw new \Files\Exception\InvalidParameterException('$length must be of type int; received ' . gettype(@$params['length']));
        }

        if (isset($params['mkdir_parents']) && !is_scalar($params['mkdir_parents'])) {
            throw new \Files\Exception\InvalidParameterException('$mkdir_parents must be of type boolean; received ' . gettype(@$params['mkdir_parents']));
        }

        if (isset($params['part']) && !is_int($params['part'])) {
            throw new \Files\Exception\InvalidParameterException('$part must be of type int; received ' . gettype(@$params['part']));
        }

        if (isset($params['parts']) && !is_int($params['parts'])) {
            throw new \Files\Exception\InvalidParameterException('$parts must be of type int; received ' . gettype(@$params['parts']));
        }

        if (isset($params['provided_mtime']) && !is_string($params['provided_mtime'])) {
            throw new \Files\Exception\InvalidParameterException('$provided_mtime must be of type string; received ' . gettype(@$params['provided_mtime']));
        }

        if (isset($params['ref']) && !is_string($params['ref'])) {
            throw new \Files\Exception\InvalidParameterException('$ref must be of type string; received ' . gettype(@$params['ref']));
        }

        if (isset($params['restart']) && !is_int($params['restart'])) {
            throw new \Files\Exception\InvalidParameterException('$restart must be of type int; received ' . gettype(@$params['restart']));
        }

        if (isset($params['size']) && !is_int($params['size'])) {
            throw new \Files\Exception\InvalidParameterException('$size must be of type int; received ' . gettype(@$params['size']));
        }

        if (isset($params['copy_behaviors']) && !is_scalar($params['copy_behaviors'])) {
            throw new \Files\Exception\InvalidParameterException('$copy_behaviors must be of type boolean; received ' . gettype(@$params['copy_behaviors']));
        }

        if (isset($params['structure']) && !is_string($params['structure'])) {
            throw new \Files\Exception\InvalidParameterException('$structure must be of type string; received ' . gettype(@$params['structure']));
        }

        if (isset($params['with_rename']) && !is_scalar($params['with_rename'])) {
            throw new \Files\Exception\InvalidParameterException('$with_rename must be of type boolean; received ' . gettype(@$params['with_rename']));
        }

        if (isset($params['buffered_upload']) && !is_scalar($params['buffered_upload'])) {
            throw new \Files\Exception\InvalidParameterException('$buffered_upload must be of type boolean; received ' . gettype(@$params['buffered_upload']));
        }

        if (isset($params['with_direct_connection_info']) && !is_scalar($params['with_direct_connection_info'])) {
            throw new \Files\Exception\InvalidParameterException('$with_direct_connection_info must be of type boolean; received ' . gettype(@$params['with_direct_connection_info']));
        }

        $response = Api::sendRequest('/files/' . rawurlencode(strval(@$params['path'])) . '', 'POST', $params, $options);

        return new File(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }

    /**
     * Find File/Folder by Path
     *
     * GET /file_actions/metadata/{path}
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `preview_size` (string, optional): Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
     *     - `with_previews` (bool, optional): Include file preview information?
     *     - `with_priority_color` (bool, optional): Include file priority color information?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\File
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function find($path, $params = [], $options = [])
    {
        if (!is_array($params)) {
            throw new \Files\Exception\InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $params['path'] = $path;

        if (empty($params['path'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: path');
        }

        if (isset($params['path']) && !is_string($params['path'])) {
            throw new \Files\Exception\InvalidParameterException('$path must be of type string; received ' . gettype(@$params['path']));
        }

        if (isset($params['preview_size']) && !is_string($params['preview_size'])) {
            throw new \Files\Exception\InvalidParameterException('$preview_size must be of type string; received ' . gettype(@$params['preview_size']));
        }

        if (isset($params['with_previews']) && !is_scalar($params['with_previews'])) {
            throw new \Files\Exception\InvalidParameterException('$with_previews must be of type boolean; received ' . gettype(@$params['with_previews']));
        }

        if (isset($params['with_priority_color']) && !is_scalar($params['with_priority_color'])) {
            throw new \Files\Exception\InvalidParameterException('$with_priority_color must be of type boolean; received ' . gettype(@$params['with_priority_color']));
        }

        $response = Api::sendRequest('/file_actions/metadata/' . rawurlencode(strval(@$params['path'])) . '', 'GET', $params, $options);

        return new File(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
    /**
     * Find File/Folder by Path
     *
     * GET /file_actions/metadata/{path}
     *
     * Alias of find().
     *
     * @param string $path Path to operate on.
     * @param array $params Request parameters.
     *     - `preview_size` (string, optional): Request a preview size.  Can be `small` (default), `large`, `xlarge`, or `pdf`.
     *     - `with_previews` (bool, optional): Include file preview information?
     *     - `with_priority_color` (bool, optional): Include file priority color information?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\File
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function get($path, $params = [], $options = [])
    {
        return self::find($path, $params, $options);
    }

    /**
     * Create a ZIP from one or more paths and save it to a destination path
     *
     * POST /file_actions/zip
     *
     * @param array $params Request parameters.
     *     - `paths` (string[], required): Paths to include in the ZIP.
     *     - `destination` (string, required): Destination file path for the ZIP.
     *     - `archive_format` (string, optional): Archive format to create. Allowed values: zip, tar, tar.gz, tgz.
     *     - `overwrite` (bool, optional): Overwrite existing file in the destination?
     * @param array $options Request options, including api_key, session_id, workspace_id and Guzzle options.
     * @return \Files\Model\FileAction
     * @throws \Files\Exception\FilesException When validation, configuration or an API request fails.
     */
    public static function zip($params = [], $options = [])
    {
        if (empty($params['paths'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: paths');
        }

        if (empty($params['destination'])) {
            throw new \Files\Exception\MissingParameterException('Parameter missing: destination');
        }

        if (isset($params['paths']) && !is_array($params['paths'])) {
            throw new \Files\Exception\InvalidParameterException('$paths must be of type array; received ' . gettype(@$params['paths']));
        }

        if (isset($params['destination']) && !is_string($params['destination'])) {
            throw new \Files\Exception\InvalidParameterException('$destination must be of type string; received ' . gettype(@$params['destination']));
        }

        if (isset($params['archive_format']) && !is_string($params['archive_format'])) {
            throw new \Files\Exception\InvalidParameterException('$archive_format must be of type string; received ' . gettype(@$params['archive_format']));
        }

        if (isset($params['overwrite']) && !is_scalar($params['overwrite'])) {
            throw new \Files\Exception\InvalidParameterException('$overwrite must be of type boolean; received ' . gettype(@$params['overwrite']));
        }

        $response = Api::sendRequest('/file_actions/zip', 'POST', $params, $options);

        return new FileAction(Api::modelAttributes(isset($response->data) ? $response->data : null, $response->status), $options);
    }
}
