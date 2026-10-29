<?php

namespace Files;

use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Exception\NotImplementedException;
use Files\Model\File;

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
        $validate = function ($response) use ($start, $end, $length, &$acceptBody) {
            if (in_array($response->getStatusCode(), [301, 302, 303, 307, 308], true)) {
                $acceptBody = false;
                return;
            }
            $this->validateRange($response->getStatusCode(), $response->getHeaders(), $start, $end, $length);
            $acceptBody = true;
        };
        try {
            $response = Api::sendRequest($this->url, 'GET', null, [
                'headers' => $headers,
                'sink' => $sink,
                'decode_content' => false,
                'on_headers' => $validate,
            ]);
            // Custom SDK handlers may not implement on_headers. Validate their response as well.
            $this->validateRange($response->status, $response->headers, $start, $end, $length);
            if ($received !== $length) {
                throw new ApiBadResponseException('The Files stream received an incomplete download range');
            }
            $buffer->rewind();
            $bytes = $buffer->getContents();
            $this->position += strlen($bytes);
            return $bytes;
        } finally {
            $sink->close();
        }
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
            $retries = 0;
            while (true) {
                $body->rewind();
                try {
                    $this->requestContext->options();
                    $response = Api::sendFile($this->part->getUploadUri(), $method, $body, $headers, false);
                    break;
                } catch (\Files\Exception\ApiConnectException $error) {
                    if ($this->part->getRetryParts() === false || $retries >= Files::$maxNetworkRetries) {
                        throw $error;
                    }
                    $retries++;
                    usleep((int) (min(($retries + 1) * Files::$minNetworkRetryDelay, Files::$maxNetworkRetryDelay) * 1000000));
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
                File::create($this->path, $params + ['action' => 'end', 'ref' => $this->ref, 'size' => $this->position, 'etags' => $this->etags], $this->requestContext->options());
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
