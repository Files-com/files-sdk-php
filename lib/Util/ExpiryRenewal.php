<?php

namespace Files\Util;

use Files\Files;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * The attempts one storage request of a transfer without adaptive transfers has, and what it does with a refusal that
 * storage says came because the request's signed URL expired (StorageExpiry). File's upload and download helpers and the
 * files:// stream wrapper (NativeFile) make one for each part, download or range, pass it to Api::sendFile or
 * Api::sendSingleRequest, which return null when the URL is to be renewed, and renew the URL themselves, by the path
 * and context they began with.
 *
 * Files::$maxNetworkRetries retries are shared by the request's connection retries, the renewals of its URL (at most
 * RENEWALS) and any retry of a whole download its owner makes, so that none of them starts a count of its own. A
 * renewal needs a retry left, and none is made when $renew is false: for a part whose grant says retry_parts false, or
 * a download its caller asked not to retry.
 *
 * Nothing of a refusal storage says came from an expired URL reaches the request's sink, whether or not it can be
 * renewed; any other response's body reaches it as it always did, a refusal's before its error is made from it. Each
 * transfer's body is written as it arrives once the handler gives its status to on_headers, as Guzzle's own handlers do,
 * except a 400's or 403's, of which at most StorageExpiry::SCAN_BYTES and a byte more are held, in memory, until it is
 * judged. Bytes a handler writes before giving any status are kept in a temporary stream (in memory up to STAGE_MEMORY
 * bytes, then on disk) until its response gives it. A handler that answers with a body of its own, writing none, is
 * judged by that body, and its body stays its own. A destination path is the SDK's own file (DownloadFileSink), with
 * every handler: each call, and each transfer within one that gives its headers, starts a new file, and a file nothing
 * is written to is never opened, so a refusal held back leaves the path as it was. A caller's stream resource is written
 * through one stream of the SDK's for every transfer of a request, let go of once the request ends: it stays open, the
 * caller's to close.
 *
 * Files.com JSON counts as all of a refusal only when every byte its Content-Length gave arrived (a handler, such as
 * Guzzle's StreamHandler, can end a short body normally); provider text counts in whatever part of it arrived, as it does
 * when a handler's own body fails part way.
 *
 * @internal
 */
final class ExpiryRenewal
{
    const RENEWALS = 3;
    // How much of a body written before its status is known is kept in memory; the rest goes to a temporary file.
    const STAGE_MEMORY = 1048576;
    const COPY_BYTES = 65536;

    private $renew;
    private $retries = 0;
    private $renewals = 0;
    // The current transfer: its status; what its body's bytes do (pending until the status is known, then pass, capture
    // or discard); the bytes held back, and whether they were all of the body; whether any reached the sink; bytes
    // written before the status was known; the sink as the request was given it, and this attempt's view of it.
    private $status = null;
    private $mode = 'none';
    private $held = '';
    private $complete = false;
    private $exposed = false;
    private $staged = null;
    private $inner = null;
    private $sink = null;
    private $view = null;
    private $viewBytes = null;
    // The bytes of the current transfer's refusal that reached the sink, and how many its headers said it has (null when
    // they do not say, or say it of an encoded body).
    private $received = 0;
    private $expected = null;
    // Whether the last attempt ended in storage's word that the URL expired, which could not be renewed.
    private $refusedExpired = false;
    // Whether any transfer of this owner's requests has written a byte of a success's body (2xx) to the sink: what a
    // download has made of its destination itself, as against what the destination held before it.
    private $wroteContent = false;
    // A caller's stream resource, made one stream for every transfer of a request (Guzzle would wrap it, and close it,
    // each time) and let go of, never closed, once the request ends (finish).
    private $resource = null;
    private $resourceStream = null;

    public function __construct($renew = true)
    {
        $this->renew = (bool) $renew;
    }

    public function renewable()
    {
        return $this->renew && $this->renewals < self::RENEWALS && $this->retries < Files::$maxNetworkRetries;
    }

    public function retries()
    {
        return $this->retries;
    }

    // Whether the last attempt ended in storage's word that its URL expired, with no renewal left: a retry of the whole
    // download would only renew it once more than RENEWALS allow.
    public function refusedExpired()
    {
        return $this->refusedExpired;
    }

    // Whether a success's body has reached the sink in any request this owner made, so that bytes now in a destination
    // path are the download's own, and it can resume after them.
    public function wroteContent()
    {
        return $this->wroteContent;
    }

    // Once a request has ended, its error made: lets go of a caller's stream resource without closing it, and of what
    // this request's transfers held.
    public function finish()
    {
        if ($this->resourceStream !== null) {
            $this->resourceStream->detach();
        }

        if ($this->staged !== null) {
            $this->staged->close();
        }

        $this->resource = null;
        $this->resourceStream = null;
        $this->staged = null;
        $this->sink = null;
        $this->view = null;
    }

    // Counts a retry when one is left, and says whether it was.
    public function retry()
    {
        if ($this->retries >= Files::$maxNetworkRetries) {
            return false;
        }

        $this->retries++;
        return true;
    }

    // Counts the renewal that gave $url, with the API's $expires for it, as one of the retries. A URL whose own query or
    // $expires says it has already expired is refused unsent.
    public function renewed($url, $expires = null)
    {
        $this->renewals++;
        $this->retries++;
        $expiresAt = StorageExpiry::expiresAt($url, $expires);
        if ($expiresAt !== null && time() >= $expiresAt) {
            throw new \Files\Exception\ApiBadResponseException('The transfer URL the API returned has expired, and renewing it did not give one still valid', 403);
        }
    }

    // $handler, with each of its calls (each transfer of a request: a retry or a redirect) holding back a refusal's body.
    public function transport(callable $handler)
    {
        return function ($request, array $options) use ($handler) {
            return $handler($request, $this->divert($options))->then(
                function ($response) {
                    $this->decide($response, true);
                    return $response;
                },
                function ($reason) {
                    $this->decide($reason instanceof RequestException ? $reason->getResponse() : null, false);
                    return new RejectedPromise($reason);
                }
            );
        };
    }

    // What the owner does about $error, this attempt's failure: null to renew the URL and send again, or the error to
    // report, after a refusal's body held back has been written to the sink, unless storage said the URL expired.
    public function settle(\Exception $error)
    {
        $this->refusedExpired = false;
        $response = $error instanceof BadResponseException ? $error->getResponse() : null;
        // A handler that answered with a body of its own, writing none of it to the sink, is judged by that body below.
        $ownBody = $response !== null && $response->getBody() !== $this->sink;
        if ($this->mode === 'capture' && ($this->received > 0 || !$ownBody)) {
            // The body held is all of it only when it ended normally, its error the response's own, within what is held,
            // and with every byte its headers said it has.
            $this->complete = $response !== null && strlen($this->held) <= StorageExpiry::SCAN_BYTES
                && ($this->expected === null || $this->received === $this->expected);
            if (!StorageExpiry::isExpiryResponse($this->status, $this->held, $this->complete)) {
                $this->release();
                return $error;
            }

            $this->mode = 'discard';
        }

        if ($this->mode === 'discard') {
            if ($this->renewable()) {
                return null;
            }

            // Storage's word stays out of the sink: the error is made from the bytes held instead.
            $this->refusedExpired = true;
            return $response === null ? $error : RequestException::create($error->getRequest(), $response->withBody(self::streamOf($this->held)));
        }

        $status = $response === null ? null : $response->getStatusCode();
        if (($status !== 400 && $status !== 403) || $this->exposed || !$ownBody) {
            return $error;
        }

        list($text, $complete) = self::prefixOf($response->getBody());
        $expected = self::expectedLength($response);
        if (!StorageExpiry::isExpiryResponse($status, $text, $complete && ($expected === null || strlen($text) === $expected))) {
            return $error;
        }

        if ($this->renewable()) {
            return null;
        }

        $this->refusedExpired = true;
        return $error;
    }

    private function divert(array $options)
    {
        $this->status = null;
        $this->mode = 'none';
        $this->held = '';
        $this->complete = false;
        $this->exposed = false;
        $this->staged = null;
        $this->inner = null;
        $this->sink = null;
        $this->view = null;
        $this->viewBytes = null;
        $this->received = 0;
        $this->expected = null;
        $this->refusedExpired = false;
        $sink = isset($options['sink']) ? $options['sink'] : null;
        if (is_resource($sink)) {
            if ($this->resource !== $sink) {
                $this->resource = $sink;
                $this->resourceStream = new Stream($sink);
            }

            $sink = $this->resourceStream;
        }

        if (!$sink instanceof StreamInterface) {
            return $options;
        }

        $this->mode = 'pending';
        $this->inner = $sink;
        $methods = [
            'write' => function ($data) {
                return $this->write(is_string($data) ? $data : (string) $data);
            },
            // The sink's owner closes it, never this attempt's view of it.
            'close' => function () {
            },
        ];
        // While a refusal is held back, what reads the response's body (Guzzle summarizing it for its error, the error
        // object read from it) reads what is held, and never opens or reads the sink, which may be a path not yet opened.
        foreach (['__toString', 'getContents', 'getSize', 'isReadable', 'isSeekable', 'read', 'eof', 'tell', 'seek', 'rewind', 'getMetadata'] as $method) {
            $methods[$method] = function () use ($method) {
                $held = !$this->exposed && ($this->mode === 'capture' || $this->mode === 'discard');
                return call_user_func_array([$held ? $this->heldView() : $this->inner, $method], func_get_args());
            };
        }
        $this->sink = FnStream::decorate($sink, $methods);
        $options['sink'] = $this->sink;
        $previous = isset($options['on_headers']) ? $options['on_headers'] : null;
        // Guzzle's MockHandler gives on_headers whatever it was queued to answer with, an exception included.
        $options['on_headers'] = function ($response) use ($previous) {
            if ($response instanceof ResponseInterface) {
                $this->headers($response);
            }
            if ($previous !== null) {
                call_user_func($previous, $response);
            }
        };

        return $options;
    }

    // A transfer's status, as its headers arrive: a new file for a path, and its body written as it comes, unless it is
    // a 400 or 403, whose body is held back until it is judged.
    private function headers(ResponseInterface $response)
    {
        $this->flushStaged();
        $status = $response->getStatusCode();
        $this->status = $status;
        $this->held = '';
        $this->received = 0;
        $this->expected = self::expectedLength($response);
        $this->mode = $status === 400 || $status === 403 ? 'capture' : 'pass';
        if ($this->inner instanceof DownloadFileSink) {
            $this->inner->nextTransfer();
        }
    }

    // The status of a transfer whose handler gave none to on_headers, once its response does (or none, when it failed
    // without one): what it wrote meanwhile is storage's word that the URL expired, and dropped, or written to the sink.
    private function decide($response, $ended)
    {
        if ($this->mode !== 'pending') {
            return;
        }

        $status = $response instanceof ResponseInterface ? $response->getStatusCode() : null;
        $this->status = $status;
        if (($status === 400 || $status === 403) && $this->staged !== null) {
            list($text, $complete) = self::prefixOf($this->staged);
            $expected = self::expectedLength($response);
            $complete = $complete && $ended && ($expected === null || strlen($text) === $expected);
            if (StorageExpiry::isExpiryResponse($status, $text, $complete)) {
                $this->held = $text;
                $this->complete = $complete;
                $this->mode = 'discard';
                $this->staged = null;
                return;
            }
        }

        $this->flushStaged();
        if ($status !== 400 && $status !== 403) {
            $this->mode = 'pass';
        }
    }

    private function write($data)
    {
        if ($this->mode === 'discard') {
            return strlen($data);
        }

        if ($this->mode === 'pending') {
            if ($this->staged === null) {
                $this->staged = new Stream(fopen('php://temp/maxmemory:' . self::STAGE_MEMORY, 'w+b'));
            }

            return $this->staged->write($data);
        }

        if ($this->mode !== 'capture') {
            $this->exposed = true;
            $this->noteContent($data);
            return $this->inner->write($data);
        }

        $this->received += strlen($data);
        $room = StorageExpiry::SCAN_BYTES + 1 - strlen($this->held);
        $this->held .= (string) substr($data, 0, $room);
        if (strlen($data) > $room) {
            // More came than is held: judged by its start, the rest is dropped, or written after what is held.
            if (StorageExpiry::isExpiryResponse($this->status, $this->held, false)) {
                $this->mode = 'discard';
            } else {
                $this->release();
                $this->mode = 'pass';
                $this->inner->write((string) substr($data, $room));
            }
        }

        return strlen($data);
    }

    // The bytes held back, as a stream of their own.
    private function heldView()
    {
        if ($this->view === null || $this->viewBytes !== $this->held) {
            $this->view = self::streamOf($this->held);
            $this->viewBytes = $this->held;
        }

        return $this->view;
    }

    // Writes what was held back to the sink, as it would have been written.
    private function release()
    {
        if ($this->held !== '' && $this->inner !== null) {
            $this->exposed = true;
            $this->inner->write($this->held);
        }

        $this->held = '';
    }

    // Writes what was written before the status was known to the sink, in order, a slice at a time.
    private function flushStaged()
    {
        if ($this->staged === null) {
            return;
        }

        $staged = $this->staged;
        $this->staged = null;
        $staged->rewind();
        while (!$staged->eof()) {
            $chunk = $staged->read(self::COPY_BYTES);
            if ($chunk === '') {
                break;
            }

            $this->exposed = true;
            $this->noteContent($chunk);
            $this->inner->write($chunk);
        }

        $staged->close();
    }

    private function noteContent($data)
    {
        if ($data !== '' && $this->status >= 200 && $this->status < 300) {
            $this->wroteContent = true;
        }
    }

    // The first SCAN_BYTES and a byte more of $body, read from its start, and whether that was all of it: what was read
    // before a read failed, as not all of it, and nothing from a body that cannot be read.
    private static function prefixOf(StreamInterface $body)
    {
        $text = '';
        try {
            if (!$body->isReadable()) {
                return ['', false];
            }

            if ($body->isSeekable()) {
                $body->rewind();
            }

            while (strlen($text) <= StorageExpiry::SCAN_BYTES && !$body->eof()) {
                $chunk = $body->read(StorageExpiry::SCAN_BYTES + 1 - strlen($text));
                if ($chunk === '') {
                    break;
                }

                $text .= $chunk;
            }

            $complete = strlen($text) <= StorageExpiry::SCAN_BYTES && $body->eof();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            return [$text, $complete];
        } catch (\Exception $e) {
            try {
                if ($body->isSeekable()) {
                    $body->rewind();
                }
            } catch (\Exception $ignored) {
                // The body stays where the failed read left it.
            }

            return [$text, false];
        }
    }

    // How many bytes $response's body has, as its headers say, or null when they do not say, or say it of a body that is
    // encoded or chunked (what reaches the sink then is decoded, or framed otherwise).
    private static function expectedLength($response)
    {
        if (!$response instanceof ResponseInterface || $response->hasHeader('Transfer-Encoding')) {
            return null;
        }

        $encoding = strtolower(trim($response->getHeaderLine('Content-Encoding')));
        $length = trim($response->getHeaderLine('Content-Length'));
        return ($encoding === '' || $encoding === 'identity') && preg_match('/\A[0-9]+\z/', $length) ? (int) $length : null;
    }

    private static function streamOf($bytes)
    {
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($bytes);
        $stream->rewind();
        return $stream;
    }
}
