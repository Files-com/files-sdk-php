<?php

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Exception\NotImplementedException;
use Files\Model\File;
use Files\Util\UploadFinalization;

/** @internal The bounded file engine used by StreamWrapper. */
class NativeFile
{
    const READ_SIZE = 1048576;
    const MEMORY_SIZE = 1048576;

    private $path;
    private $requestContext;
    private $uploadParams;
    private $writing;
    private $position = 0;
    private $metadata;
    private $url;
    private $etag;
    private $downloadDate;
    private $buffer;
    private $part;
    private $partBytes = 0;
    private $partNumber = 0;
    private $ref;
    private $etags = [];
    private $failed = false;
    private $closed = false;

    public function __construct($path, $mode, array $options = [], array $uploadParams = [])
    {
        if (!in_array($mode, ['r', 'rb', 'w', 'wb'], true)) {
            throw new NotImplementedException('Files streams support read (r) and sequential overwrite (w) modes only');
        }

        $this->path = StreamWrapper::apiPath($path);
        $checkModificationTime = Files::downloadModificationTimeCheck($options);
        unset($options['check_download_modification_time']);
        $this->requestContext = new Stream\RequestContext($options);
        $this->uploadParams = $uploadParams;
        $this->writing = $mode[0] === 'w';

        if ($this->writing) {
            foreach ($uploadParams as $name => $value) {
                if (!in_array($name, ['provided_mtime', 'custom_metadata', 'buffered_upload'], true)) {
                    throw new InvalidParameterException('Unsupported Files stream upload parameter: ' . $name);
                }
            }
            $this->beginPart();
        } else {
            $this->downloadDate = new Util\DownloadDate($checkModificationTime);
            $this->metadata = (new File(['path' => $this->path], $this->requestContext->options()))->download();
            if ($this->metadata->getType() !== 'file') {
                throw new InvalidParameterException('The Files stream path is not a file');
            }
            $size = $this->metadata->getSize();
            if (!is_int($size) || $size < 0) {
                throw new ApiBadResponseException('The download grant has no representable nonnegative file size');
            }
            $this->url = $this->metadata->getDownloadUri();
            self::requireHttpUrl($this->url);
        }
    }

    private static function requireHttpUrl($url)
    {
        $parts = is_string($url) ? parse_url($url) : false;
        if (!$parts || !isset($parts['host'], $parts['scheme']) || !in_array(strtolower($parts['scheme']), ['http', 'https'], true) || isset($parts['user']) || isset($parts['pass'])) {
            throw new ApiBadResponseException('The transfer grant does not contain an HTTP download or upload URL');
        }
    }

    public function read($count)
    {
        if ($this->writing) {
            throw new NotImplementedException('A Files write stream cannot be read');
        }
        if ($this->position >= $this->metadata->getSize() || $count === 0) {
            return '';
        }
        $this->requestContext->options();

        $start = $this->position;
        $length = min($count, self::READ_SIZE, $this->metadata->getSize() - $start);
        // Storage's word that the URL expired renews it (renewDownloadUrl) and asks for the same range again, its
        // connection retries and renewals sharing Files::$maxNetworkRetries; the position moves only once a range is read.
        $renewal = new Util\ExpiryRenewal(true, $this->downloadDate);
        while (($bytes = $this->readRange($start, $length, $renewal)) === null) {
            $this->renewDownloadUrl();
            $renewal->renewed($this->url);
        }

        $this->position += strlen($bytes);
        return $bytes;
    }

    // The exact range of $length bytes from $start, or null when storage said the URL expired and $renewal may renew it.
    private function readRange($start, $length, $renewal)
    {
        $end = $start + $length - 1;
        $headers = ['Range' => 'bytes=' . $start . '-' . $end, 'Accept-Encoding' => 'identity'];
        if ($this->etag !== null) {
            $headers['If-Match'] = $this->etag;
        }
        $resource = fopen('php://temp/maxmemory:' . self::MEMORY_SIZE, 'w+b');
        if ($resource === false) {
            throw new InvalidParameterException('Could not open the Files stream range buffer');
        }
        $buffer = new \GuzzleHttp\Psr7\Stream($resource);
        $received = 0;
        $acceptBody = true;
        $sink = \GuzzleHttp\Psr7\FnStream::decorate($buffer, [
            'write' => function ($bytes) use ($buffer, &$received, &$acceptBody, $length) {
                if (!$acceptBody) {
                    return strlen($bytes);
                }
                if (strlen($bytes) > $length - $received) {
                    throw new ApiBadResponseException('The Files stream received more bytes than its requested range');
                }
                $written = $buffer->write($bytes);
                $received += $written;
                return $written;
            },
        ]);
        $validate = function ($response) use ($start, $end, $length, &$acceptBody, $renewal) {
            $status = $response->getStatusCode();
            // A redirect's body, and a refusal that may be renewed, judged by its body once it has arrived (Api), are no
            // part of the range.
            if (in_array($status, [301, 302, 303, 307, 308], true) || (($status === 400 || $status === 403) && $renewal->renewable())) {
                $acceptBody = false;
                return;
            }
            $this->validateRange($status, $response->getHeaders(), $start, $end, $length);
            $acceptBody = true;
        };
        try {
            $response = Api::sendSingleRequest($this->url, 'GET', null, [
                'headers' => $headers,
                'sink' => $sink,
                'decode_content' => false,
                'on_headers' => $validate,
            ], $renewal);
            if ($response === null) {
                return null;
            }
            // Custom SDK handlers may not implement on_headers. Validate their response as well.
            $this->validateRange($response->status, $response->headers, $start, $end, $length);
            if ($received !== $length) {
                throw new ApiBadResponseException('The Files stream received an incomplete download range');
            }
            $buffer->rewind();
            return $buffer->getContents();
        } finally {
            $sink->close();
        }
    }

    // A new download URL for the open file after storage said its URL expired, looked up with the handle's pinned
    // context. The file must still be one of the size the handle opened; the version the handle bound, if any, is still
    // asked for (If-Match), so a changed file is refused before any of its bytes are returned.
    private function renewDownloadUrl()
    {
        $metadata = (new File(['path' => $this->path], $this->requestContext->options()))->download();
        if ($metadata->getType() !== 'file' || $metadata->getSize() !== $this->metadata->getSize()) {
            throw new ApiBadResponseException('The remote file changed while its Files stream was open');
        }
        $url = $metadata->getDownloadUri();
        self::requireHttpUrl($url);
        $this->url = $url;
    }

    private function validateRange($status, $headers, $start, $end, $length)
    {
        $range = Api::headerValues($headers, 'Content-Range');
        $size = Api::headerValues($headers, 'Content-Length');
        $encoding = Api::headerValues($headers, 'Content-Encoding');
        $etag = Api::headerValues($headers, 'ETag');
        if (
            $status !== 206 || $range !== ['bytes ' . $start . '-' . $end . '/' . $this->metadata->getSize()]
            || ($size !== [] && $size !== [(string) $length])
            || ($encoding !== [] && $encoding !== ['identity'])
            || Api::headerValues($headers, 'X-Files-Frontend-App') !== []
        ) {
            throw new ApiBadResponseException('The Files stream requires the exact unencoded download range');
        }
        if ($this->etag !== null && $etag !== [] && $etag !== [$this->etag]) {
            throw new ApiBadResponseException('The remote file changed while its Files stream was open');
        }
        if ($this->etag === null && count($etag) === 1 && preg_match('/\A"[^"\x00-\x20\x7f]*"\z/', $etag[0])) {
            $this->etag = $etag[0];
        }
    }

    private function beginPart()
    {
        $number = $this->partNumber + 1;
        $params = $this->ref === null
            ? ['part' => $number, 'parts' => 1, 'mkdir_parents' => false] + array_intersect_key($this->uploadParams, ['buffered_upload' => true])
            : ['part' => $number, 'parts' => 1, 'ref' => $this->ref];
        $parts = (new File(['path' => $this->path], $this->requestContext->options()))->beginUpload($params);
        $part = count($parts) === 1 ? $parts[0] : null;
        if (!$part || $part->getPartNumber() !== $number || !is_int($part->getPartsize()) || $part->getPartsize() <= 0 || !is_string($part->getRef()) || $part->getRef() === '' || ($this->ref !== null && $this->ref !== $part->getRef())) {
            throw new ApiBadResponseException('The Files stream was not granted the requested upload part');
        }
        self::requireHttpUrl($part->getUploadUri());
        foreach (['parameters' => $part->getParameters(), 'send' => $part->getSend()] as $name => $value) {
            if ($value !== null && $value !== '' && (array) $value !== []) {
                throw new NotImplementedException('The upload grant requires ' . $name . ', which Files streams do not support');
            }
        }
        $this->part = $part;
        $this->ref = $part->getRef();
        $this->partNumber = $number;
        $this->partBytes = 0;
        $this->buffer = fopen('php://temp/maxmemory:' . self::MEMORY_SIZE, 'w+b');
        if ($this->buffer === false) {
            throw new InvalidParameterException('Could not open the Files stream upload buffer');
        }
    }

    public function write($bytes)
    {
        if (!$this->writing) {
            throw new NotImplementedException('A Files read stream cannot be written');
        }
        if ($this->failed) {
            throw new InvalidParameterException('The Files upload stream has already failed');
        }
        $written = 0;
        $completed = false;
        try {
            $this->requestContext->options();
            if (!is_string($bytes)) {
                throw new InvalidParameterException('Files stream writes require string bytes');
            }
            while ($written < strlen($bytes)) {
                if ($this->part === null) {
                    $this->beginPart();
                }
                $count = min(strlen($bytes) - $written, $this->part->getPartsize() - $this->partBytes, self::MEMORY_SIZE);
                if ($this->position > PHP_INT_MAX - $count) {
                    throw new InvalidParameterException('The Files stream size exceeds this PHP build\'s integer limit');
                }
                $accepted = fwrite($this->buffer, substr($bytes, $written, $count));
                if ($accepted === false || $accepted === 0) {
                    throw new InvalidParameterException('Writing the Files stream upload buffer failed');
                }
                $written += $accepted;
                $this->position += $accepted;
                $this->partBytes += $accepted;
                if ($this->partBytes === $this->part->getPartsize()) {
                    $this->sendPart();
                }
            }
            $completed = true;
        } finally {
            if (!$completed) {
                $this->failed = true;
            }
        }
        return $written;
    }

    private function sendPart()
    {
        $headers = $this->part->getHeaders();
        if ($headers !== null && !is_array($headers) && !is_object($headers)) {
            throw new ApiBadResponseException('The Files upload grant headers are not a map');
        }
        $headers = (array) $headers;
        foreach ($headers as $name => $value) {
            if (!is_string($value)) {
                throw new ApiBadResponseException('The Files upload grant contains a non-string header');
            }
            if (strcasecmp($name, 'Content-Length') === 0 && $value !== (string) $this->partBytes) {
                throw new ApiBadResponseException('The Files upload grant Content-Length does not match its part');
            }
        }
        if (Api::headerValues($headers, 'Content-Length') === []) {
            $headers['Content-Length'] = (string) $this->partBytes;
        }
        if (Api::headerValues($headers, 'Content-Type') === []) {
            $headers['Content-Type'] = 'application/octet-stream';
        }
        $method = $this->part->getHttpMethod();
        if ($method === null || $method === '') {
            $method = 'PUT';
        }
        if (!is_string($method) || !preg_match('/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/', $method)) {
            throw new ApiBadResponseException('The Files upload grant contains an invalid HTTP method');
        }
        rewind($this->buffer);
        $body = new \GuzzleHttp\Psr7\Stream($this->buffer);
        try {
            // A connection failure, and storage's word that the part's URL expired (renewPart), send the part's same
            // bytes again, unless its grant says retry_parts false; both share Files::$maxNetworkRetries.
            $renewal = new Util\ExpiryRenewal($this->part->getRetryParts() !== false);
            while (true) {
                $body->rewind();
                try {
                    $this->requestContext->options();
                    $response = Api::sendFile($this->part->getUploadUri(), $method, $body, $headers, false, $renewal);
                    if ($response !== null) {
                        break;
                    }
                    $this->renewPart();
                    $renewal->renewed($this->part->getUploadUri(), $this->part->getExpires());
                } catch (\Files\Exception\ApiConnectException $error) {
                    if ($this->part->getRetryParts() === false || !$renewal->retry()) {
                        throw $error;
                    }
                    usleep((int) (min(($renewal->retries() + 1) * Files::$minNetworkRetryDelay, Files::$maxNetworkRetryDelay) * 1000000));
                }
            }
        } finally {
            // The engine owns the temporary file, including when the transport throws.
            $body->detach();
        }
        $etag = Api::headerValues($response->headers, 'ETag');
        if ($response->status < 200 || $response->status >= 300 || count($etag) !== 1 || $etag[0] === '') {
            throw new ApiBadResponseException('The Files upload part was not acknowledged with one ETag');
        }
        $this->etags[] = ['etag' => $etag[0], 'part' => $this->partNumber];
        fclose($this->buffer);
        $this->buffer = null;
        $this->part = null;
    }

    // A new grant for the part being sent, by the upload's ref and the part's number, after storage said its URL expired.
    // It must be for the same part of the same upload, with the same size, method, headers, parameters and send
    // instructions, and must not say retry_parts false, or the upload fails before the part is sent again.
    private function renewPart()
    {
        $parts = (new File(['path' => $this->path], $this->requestContext->options()))->beginUpload(['part' => $this->partNumber, 'parts' => 1, 'ref' => $this->ref]);
        $part = count($parts) === 1 ? $parts[0] : null;
        $identity = function ($grant) {
            return json_encode([
                $grant->getRef(), $grant->getPartNumber(), $grant->getPartsize(), $grant->getHttpMethod(),
                $grant->getHeaders(), $grant->getParameters(), $grant->getSend(),
            ]);
        };
        if (!$part || $identity($part) !== $identity($this->part) || $part->getRetryParts() === false) {
            throw new ApiBadResponseException('The renewed Files stream upload grant changed');
        }
        self::requireHttpUrl($part->getUploadUri());
        $this->part = $part;
    }

    public function tell()
    {
        return $this->position;
    }

    public function eof()
    {
        return !$this->writing && $this->position >= $this->metadata->getSize();
    }

    public function seek($offset, $whence)
    {
        if ($this->writing) {
            throw new NotImplementedException('Files upload streams are sequential and cannot seek');
        }
        $base = $whence === SEEK_SET ? 0 : ($whence === SEEK_CUR ? $this->position : ($whence === SEEK_END ? $this->metadata->getSize() : null));
        if ($base === null || !is_int($offset) || !is_int($base + $offset) || $base + $offset < 0) {
            return false;
        }
        $this->position = $base + $offset;
        return true;
    }

    public function stat()
    {
        if ($this->writing) {
            return StreamWrapper::statForFile(new File(['type' => 'file', 'size' => $this->position, 'permissions' => 'w']));
        }
        return StreamWrapper::statForFile($this->metadata);
    }

    public function flush()
    {
        if ($this->failed) {
            return false;
        }
        if (is_resource($this->buffer) && !fflush($this->buffer)) {
            $this->failed = true;
            throw new InvalidParameterException('Flushing the Files stream upload buffer failed');
        }
        return true;
    }

    public function close()
    {
        if ($this->closed) {
            return;
        }
        $this->closed = true;
        try {
            if ($this->writing && !$this->failed) {
                if ($this->part === null) {
                    $this->beginPart();
                }
                $this->sendPart();
                $params = array_intersect_key($this->uploadParams, ['provided_mtime' => true, 'custom_metadata' => true]);
                // Returns once Files.com has completed the upload, for a destination it completes in the background too.
                UploadFinalization::finish($this->path, $params + ['action' => 'end', 'ref' => $this->ref, 'size' => $this->position, 'etags' => $this->etags], $this->requestContext->options(), null);
            }
        } finally {
            if (is_resource($this->buffer)) {
                fclose($this->buffer);
            }
            $this->buffer = null;
        }
    }

    public function __destruct()
    {
        if (is_resource($this->buffer)) {
            fclose($this->buffer);
        }
    }
}
