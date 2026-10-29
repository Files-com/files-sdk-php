<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Exception\NotImplementedException;
use Files\Logger;
use GuzzleHttp\Psr7\LimitStream;
use GuzzleHttp\Psr7\Stream;

/**
 * One upload a TransferJob runs: a local file, a string, or a caller's stream, to $destination.
 *
 * It asks the API for parts with File#beginUpload's request (POST /file_actions/begin_upload/{path}), every time with
 * with_direct_connection_info: first part 1 alone, with the caller's first-part parameters and the size when it is
 * known, and then five parts at a time by the upload's ref. Each answer must grant exactly the parts asked for, in
 * order, of the upload's ref, each with a partsize and an http(s) upload_uri; their http_method and headers are sent
 * as given (Content-Type application/octet-stream and the part's Content-Length are added only when the grant names no
 * such header, and a granted Content-Length must be the part's own); a grant asking for form parameters, or a send
 * instruction other than S3's ordinary one (the part's size as its Content-Length and its bytes as the body), is
 * refused. Valid version 1 direct_connection_info makes the destination direct (the generic policy); every other upload
 * is classified S3. Classification alone does not let the upload size its parts: only an answer whose every part is
 * one ordinary multipart upload straight to S3 (UploadPlan::route) does, within UploadPlan's limits; otherwise the API's
 * partsize sizes each part.
 *
 * Each part keeps its offset, size and instructions through retries, URL renewals (begin_upload by ref and part number,
 * which must give the same instructions; at most three for a part, each counting as one of its retries when the URL
 * expired after it was sent) and pauses. A part counts as acknowledged only for a 2xx response with exactly one
 * nonempty ETag, once, and is never sent again. A malformed acknowledgment, a changed source or changed instructions
 * fail the upload; a part whose grant says retry_parts false is not retried. The upload ends (POST /files/{path},
 * action end, as File::create) only after every part is acknowledged and every progress report has returned, never
 * after a failure.
 *
 * Sources: a local file is opened once to fix its identity (device, inode, size and modification time), which must
 * still hold before each part and before the end; each attempt streams its part from a handle of its own, reserving
 * at most 1 MiB of buffer while it runs. A string's part is copied for its attempt. A caller's stream is read from where
 * it stands, part by part in order, each part only once the job's buffer holds it; its bytes stay held, through
 * retries, renewals and a pause, until the part is acknowledged or the upload stops. Without its size a stream is sent
 * one part at a time, as File::uploadData sends it: each part filled to its partsize from as many reads as it takes,
 * the upload ending with the part during which the stream ends. A caller's stream is closed once every part is
 * acknowledged and progress has returned, before the end; a failure, cancellation or pause before that leaves it open.
 */
final class UploadTransfer
{
    const BATCH_PARTS = 5;
    const RENEWALS = 3;
    // The most an attempt holds of a part it sends: Guzzle sends a body under 1,000,000 bytes as one string, and
    // streams a larger one through curl's buffer.
    const SEND_BUFFER_BYTES = 1048576;

    const FILE = 'file';
    const DATA = 'data';
    const STREAM = 'stream';

    private $engine;
    private $id;
    private $kind;
    private $source;
    private $destination;
    private $first;
    private $end;
    private $apiOptions;
    private $progress;
    private $size;
    private $state = 'queued';
    private $error = null;
    private $handle = null;
    private $identity = null;
    private $ref = null;
    private $controller = null;
    private $controllerKey = null;
    private $plan = null;
    private $serial = false;
    private $parts = [];
    private $plannedBytes = 0;
    private $nextGrant = 1;
    private $negotiating = false;
    private $renewals = [];
    private $renewing = [];
    private $nextRead = 1;
    private $sourceEnded = false;
    private $finalizing = false;
    private $accepted = 0;
    private $callAccepted = 0;
    private $callStarted = 0.0;
    private $reports = [];

    // $kind is FILE ($source a path), DATA ($source a string) or STREAM ($source a caller's stream, of $size bytes or,
    // with $size null, of a size unknown).
    public function __construct($kind, $source, $destination, array $first, array $end, array $apiOptions, $progress, $size)
    {
        $this->kind = $kind;
        $this->source = $source;
        $this->destination = $destination;
        $this->first = $first;
        $this->end = $end;
        $this->apiOptions = $apiOptions;
        $this->progress = $progress;
        $this->size = $size;
    }

    // A caller's upload parameters, split as File::uploadFile splits them: provided_mtime and custom_metadata go to the
    // end; the names the helpers own (path, part, parts, ref, restart, action, size, length, etags and
    // with_direct_connection_info) are not sent; everything else goes with the request for the first part.
    public static function splitParams($params)
    {
        if (!is_array($params)) {
            throw new InvalidParameterException('$params must be of type array; received ' . gettype($params));
        }

        $first = [];
        $end = [];
        foreach ($params as $name => $value) {
            if (in_array($name, ['provided_mtime', 'custom_metadata'], true)) {
                $end[$name] = $value;
            } elseif (!in_array($name, ['path', 'part', 'parts', 'ref', 'restart', 'action', 'size', 'length', 'etags', 'with_direct_connection_info'], true)) {
                $first[$name] = $value;
            }
        }

        return [$first, $end];
    }

    // A caller's upload options, split into progress (a callable, which only the job calls), size (the bytes of a
    // stream, when $sizeAllowed) and the rest, which the upload's API requests take as the generated methods do.
    public static function splitOptions($options, $sizeAllowed)
    {
        if (!is_array($options)) {
            throw new InvalidParameterException('$options must be of type array; received ' . gettype($options));
        }

        $progress = isset($options['progress']) ? $options['progress'] : null;
        if ($progress !== null && !is_callable($progress)) {
            throw new InvalidParameterException('$options[\'progress\'] must be callable; received ' . gettype($progress));
        }

        $size = null;
        if (array_key_exists('size', $options)) {
            if (!$sizeAllowed) {
                throw new InvalidParameterException('$options[\'size\'] gives the size of a stream uploaded with uploadData');
            }

            $size = $options['size'];
            if ($size !== null && (!is_int($size) || $size < 0)) {
                throw new InvalidParameterException('$options[\'size\'] must be an integer of at least 0; received ' . gettype($size));
            }
        }

        unset($options['progress'], $options['size']);
        return [$options, $progress, $size];
    }

    public function setEngine(TransferEngine $engine, $id)
    {
        $this->engine = $engine;
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }

    public function direction()
    {
        return 'upload';
    }

    public function remotePath()
    {
        return $this->destination;
    }

    public function state()
    {
        return $this->state;
    }

    public function error()
    {
        return $this->error;
    }

    public function totalBytes()
    {
        return $this->size;
    }

    public function completedBytes()
    {
        return $this->accepted;
    }

    public function result()
    {
        return null;
    }

    public function controllerKey()
    {
        return $this->controllerKey;
    }

    public function checkpoint()
    {
        $parts = [];
        foreach ($this->parts as $number => $part) {
            $parts[] = [
                'part' => $number, 'offset' => $part['offset'], 'bytes' => $part['length'], 'acknowledged' => $part['state'] === 'acked',
                'held_bytes' => $part['data'] === null ? 0 : strlen($part['data']),
            ];
        }

        $position = null;
        if ($this->kind === self::STREAM && is_resource($this->source)) {
            $told = @ftell($this->source);
            $position = $told === false ? null : $told;
        }

        return [
            'direction' => 'upload', 'path' => $this->destination, 'source' => $this->kind, 'status' => $this->state, 'total_bytes' => $this->size,
            'completed_bytes' => $this->accepted, 'part_sizing' => $this->plan === null ? 'api' : 'local', 'parts' => $parts,
            'stream_position' => $position,
        ];
    }

    public function open($now)
    {
        if ($this->kind === self::FILE) {
            $handle = @fopen($this->source, 'rb');
            if ($handle === false) {
                throw new InvalidParameterException('The upload\'s source file could not be opened');
            }

            $this->handle = $handle;
            $this->identity = self::identity($handle);
            $this->size = $this->identity['size'];
        } elseif ($this->kind === self::DATA) {
            $this->size = strlen($this->source);
        }

        $this->serial = $this->size === null;
        $this->state = 'active';
        $this->callStarted = (float) $now;
        $this->callAccepted = $this->accepted;
    }

    // Reads a caller's stream's next parts, in order, each once the job's buffer holds it: as many as the target may
    // send, or one at a time without the stream's size.
    public function prepare($now)
    {
        if ($this->state !== 'active' || $this->kind !== self::STREAM) {
            return;
        }

        while (isset($this->parts[$this->nextRead])) {
            $number = $this->nextRead;
            $part = $this->parts[$number];
            if ($this->serial && $this->unacknowledgedBefore($number)) {
                return;
            }

            if ($this->heldParts() >= $this->readAhead()) {
                return;
            }

            $reservation = $this->reservationFor($part['length'] === null ? $part['granted'] : $part['length']);
            if (!$this->engine->reserve($reservation, $this->id)) {
                return;
            }

            try {
                $bytes = $this->readPart($part);
            } catch (\Exception $e) {
                $this->engine->release($reservation);
                throw $e;
            }

            $length = strlen($bytes);
            $held = $this->reservationFor($length);
            if ($held < $reservation) {
                $this->engine->release($reservation - $held);
            }

            $this->parts[$number]['data'] = $bytes;
            $this->parts[$number]['reserved'] = $held;
            if ($part['length'] === null) {
                $this->parts[$number]['length'] = $length;
                $this->parts[$number]['offset'] = $this->plannedBytes;
                $this->plannedBytes += $length;
                if (feof($this->source)) {
                    $this->sourceEnded = true;
                    foreach (array_keys($this->parts) as $later) {
                        if ($later > $number) {
                            unset($this->parts[$later]);
                        }
                    }
                }
            }

            $this->nextRead++;
        }
    }

    public function nextApi($now)
    {
        if ($this->state !== 'active') {
            return null;
        }

        if ($this->renewals) {
            $number = array_shift($this->renewals);
            $this->renewing[$number] = true;
            return $this->beginUploadItem('renew', ['ref' => $this->ref, 'part' => $number, 'parts' => 1], 'idempotent', $number, 1);
        }

        if ($this->wantsGrants()) {
            $this->negotiating = true;
            if ($this->ref === null) {
                $params = $this->first + ['part' => 1, 'parts' => 1];
                if ($this->size !== null) {
                    $params['size'] = $this->size;
                }

                return $this->beginUploadItem('grants', $params, 'connect', 1, 1);
            }

            $params = ['ref' => $this->ref, 'part' => $this->nextGrant, 'parts' => self::BATCH_PARTS];
            return $this->beginUploadItem('grants', $params, 'idempotent', $this->nextGrant, self::BATCH_PARTS);
        }

        if ($this->readyToFinish()) {
            return $this->finishItem();
        }

        return null;
    }

    public function apiDone(array $item, $data, $now)
    {
        if ($this->state !== 'active') {
            return;
        }

        if ($item['tag'] === 'grants') {
            $this->negotiating = false;
            $this->acceptGrants($item, $data);
        } elseif ($item['tag'] === 'renew') {
            unset($this->renewing[$item['number']]);
            $this->acceptRenewal($item['number'], $data);
        } else {
            $this->finalizing = false;
            $this->state = 'completed';
            $this->releaseResources();
        }
    }

    public function apiAborted(array $item, $now)
    {
        if ($item['tag'] === 'grants') {
            $this->negotiating = false;
        } elseif ($item['tag'] === 'renew') {
            unset($this->renewing[$item['number']]);
            $this->renewals[] = $item['number'];
        } else {
            $this->finalizing = false;
        }
    }

    public function nextContent($now)
    {
        if ($this->state !== 'active' || $this->controller === null || ($this->serial && $this->inFlightParts() > 0)) {
            return null;
        }

        foreach ($this->parts as $number => $part) {
            if ($part['state'] === 'acked') {
                continue;
            }

            $sendable = $part['state'] === 'ready' && $part['length'] !== null && !isset($this->renewing[$number])
                && !in_array($number, $this->renewals, true) && ($this->kind !== self::STREAM || $part['data'] !== null)
                && $part['ready_at'] <= $now;
            if ($sendable && RetryPolicy::isExpiring($part['grant']['expires_at'])) {
                $this->requestRenewal($number);
                $sendable = false;
            }

            if ($sendable) {
                return [
                    'type' => 'content', 'transfer' => $this, 'controller' => $this->controller, 'controller_key' => $this->controllerKey,
                    'number' => $number, 'bytes' => $this->attemptBytes($part),
                ];
            }

            if ($this->serial) {
                return null;
            }
        }

        return null;
    }

    public function serialBlocked()
    {
        return $this->serial && $this->inFlightParts() > 0;
    }

    public function begin(array $item, $now)
    {
        $number = $item['number'];
        $part = $this->parts[$number];
        $grant = $part['grant'];
        $length = $part['length'];
        $headers = $grant['headers'];
        $declared = self::header($headers, 'Content-Length');
        if ($declared !== null && $declared !== (string) $length) {
            throw new ApiBadResponseException('Part ' . $number . ' is granted a Content-Length other than its ' . $length . ' bytes');
        }

        if ($declared === null) {
            $headers['Content-Length'] = (string) $length;
        }

        if (self::header($headers, 'Content-Type') === null) {
            $headers['Content-Type'] = 'application/octet-stream';
        }

        $body = $this->body($part);
        $this->parts[$number]['state'] = 'inflight';
        return [
            'method' => $grant['method'], 'url' => $grant['url'],
            'options' => ['headers' => $headers, 'body' => $body, 'expect' => false, 'http_errors' => false, 'allow_redirects' => false],
        ];
    }

    // How an attempt ended, for the controller: accepted (with its bytes and its duration through response headers),
    // a failure, a throttle (with Retry-After), or ended.
    public function contentDone(array $item, $ok, $value, array $timing, $now)
    {
        $number = $item['number'];
        if ($this->state !== 'active' || !isset($this->parts[$number])) {
            return [ConcurrencyController::ENDED];
        }

        if ($this->parts[$number]['state'] === 'inflight') {
            $this->parts[$number]['state'] = 'ready';
        }

        $part = $this->parts[$number];
        $what = 'Part ' . $number . ' of the upload to ' . $this->destination;
        $headersAt = $timing['headers_at'] !== null ? $timing['headers_at'] : $timing['ended'];
        $duration = max(0.0, $headersAt - $timing['started']);
        if (!$ok) {
            $error = RetryPolicy::networkError($value, $what);
            if (!RetryPolicy::networkRetryable($value)) {
                $this->engine->failTransfer($this, $error);
                return [ConcurrencyController::ENDED];
            }

            $this->retryLater($number, RetryPolicy::STORAGE_DELAY, $error, $now);
            return [ConcurrencyController::FAILURE];
        }

        $status = $value->getStatusCode();
        if ($status >= 200 && $status < 300) {
            $etag = self::etag($value);
            if ($etag === null) {
                $this->engine->failTransfer($this, new ApiBadResponseException('The response to part ' . $number . ' (status ' . $status . ') did not acknowledge it with exactly one ETag'));
                return [ConcurrencyController::ENDED];
            }

            $this->accept($number, $etag, $now);
            return ['accepted', $part['length'], $duration];
        }

        $error = RetryPolicy::storageError($what, $status);
        if ($status === 403 && RetryPolicy::isExpired($part['grant']['expires_at'])) {
            if ($this->retryLater($number, 0.0, $error, $now)) {
                $this->requestRenewal($number);
            }

            return [ConcurrencyController::ENDED];
        }

        list($body, $complete) = self::limitedBody($value);
        list($decision, $delay) = RetryPolicy::storageResponse($status, $value->getHeaders(), $body, $complete);
        if ($decision === 'fail') {
            $this->engine->failTransfer($this, $error);
            return [ConcurrencyController::ENDED];
        }

        $this->retryLater($number, $delay, $error, $now);
        return $decision === 'throttle' ? [ConcurrencyController::THROTTLE, $delay] : [ConcurrencyController::FAILURE];
    }

    public function contentAborted(array $item, $now)
    {
        $number = $item['number'];
        if (isset($this->parts[$number]) && $this->parts[$number]['state'] === 'inflight') {
            $this->parts[$number]['state'] = 'ready';
        }
    }

    public function readyCount($now)
    {
        $ready = 0;
        foreach ($this->parts as $part) {
            if ($part['state'] === 'ready' && $part['length'] !== null && $part['ready_at'] <= $now && ($this->kind !== self::STREAM || $part['data'] !== null)) {
                $ready++;
            }
        }

        return $ready;
    }

    public function wakeAt($now)
    {
        $wake = null;
        foreach ($this->parts as $part) {
            if ($part['state'] === 'ready' && $part['ready_at'] > $now && ($wake === null || $part['ready_at'] < $wake)) {
                $wake = $part['ready_at'];
            }
        }

        return $wake;
    }

    public function takeReports()
    {
        $reports = $this->reports;
        $this->reports = [];
        return $reports;
    }

    public function pause()
    {
        foreach ($this->parts as $number => $part) {
            if ($part['state'] === 'inflight') {
                $this->parts[$number]['state'] = 'ready';
            }
        }

        $this->state = 'paused';
    }

    public function resume($now)
    {
        $this->state = 'active';
        $this->callStarted = (float) $now;
        $this->callAccepted = $this->accepted;
    }

    public function fail($error)
    {
        $this->state = 'failed';
        $this->error = $error;
        $this->releaseResources();
    }

    public function cancel()
    {
        $this->state = 'cancelled';
        $this->releaseResources();
    }

    private function moreSource()
    {
        if ($this->size === null) {
            return !$this->sourceEnded;
        }

        return $this->plannedBytes < $this->size || ($this->size === 0 && !$this->parts);
    }

    private function wantsGrants()
    {
        if ($this->negotiating || !$this->moreSource()) {
            return false;
        }

        if ($this->ref === null) {
            return !$this->parts;
        }

        $waiting = 0;
        foreach ($this->parts as $part) {
            if ($part['state'] === 'ready') {
                $waiting++;
            }
        }

        return $waiting < $this->readAhead();
    }

    private function readAhead()
    {
        return $this->serial ? 1 : max(1, $this->controller === null ? 1 : $this->controller->target());
    }

    private function heldParts()
    {
        $held = 0;
        foreach ($this->parts as $part) {
            if ($part['data'] !== null) {
                $held++;
            }
        }

        return $held;
    }

    private function inFlightParts()
    {
        $inFlight = 0;
        foreach ($this->parts as $part) {
            if ($part['state'] === 'inflight') {
                $inFlight++;
            }
        }

        return $inFlight;
    }

    private function unacknowledgedBefore($number)
    {
        foreach ($this->parts as $other => $part) {
            if ($other < $number && $part['state'] !== 'acked') {
                return true;
            }
        }

        return false;
    }

    private function readyToFinish()
    {
        if ($this->finalizing || $this->negotiating || $this->renewing || $this->renewals || $this->reports || !$this->parts || $this->moreSource()) {
            return false;
        }

        foreach ($this->parts as $part) {
            if ($part['state'] !== 'acked') {
                return false;
            }
        }

        return true;
    }

    private function beginUploadItem($tag, array $params, $retry, $number, $count)
    {
        $params['with_direct_connection_info'] = true;
        $params['path'] = $this->destination;
        $describe = ($tag === 'renew' ? 'renewing part ' : 'beginning part ') . $number . ' of the upload to ' . $this->destination;
        return [
            'type' => 'api', 'transfer' => $this, 'tag' => $tag, 'method' => 'POST', 'path' => '/file_actions/begin_upload/' . rawurlencode($this->destination),
            'params' => $params, 'options' => $this->apiOptions, 'retry' => $retry, 'attempt' => 0, 'describe' => $describe, 'number' => $number,
            'count' => $count,
        ];
    }

    private function finishItem()
    {
        $this->checkSource();
        if ($this->kind === self::STREAM && is_resource($this->source)) {
            fclose($this->source);
        }

        $etags = [];
        ksort($this->parts);
        foreach ($this->parts as $number => $part) {
            $etags[] = ['etag' => $part['etag'], 'part' => $number];
        }

        $params = array_merge([
            'action' => 'end',
            'ref' => $this->ref,
            'etags' => $etags,
            'provided_mtime' => date('c', $this->kind === self::FILE ? $this->identity['mtime'] : time()),
            'size' => $this->kind === self::FILE ? $this->identity['size'] : $this->accepted,
        ], $this->end);
        $params['path'] = $this->destination;
        $this->finalizing = true;
        return [
            'type' => 'api', 'transfer' => $this, 'tag' => 'finish', 'method' => 'POST', 'path' => '/files/' . rawurlencode($this->destination),
            'params' => $params, 'options' => $this->apiOptions, 'retry' => 'connect', 'attempt' => 0,
            'describe' => 'the end of the upload to ' . $this->destination, 'number' => 0, 'count' => 0,
        ];
    }

    private function acceptGrants(array $item, $data)
    {
        $first = $item['number'];
        $count = is_array($data) ? count($data) : 0;
        if (!is_array($data) || $count === 0 || $count > $item['count'] || array_keys($data) !== range(0, $count - 1)) {
            throw new ApiBadResponseException('Beginning part ' . $first . ' of the upload to ' . $this->destination . ' granted ' . $count . ' parts, not 1 to ' . $item['count'] . ' parts in order from that part');
        }

        $grants = [];
        foreach ($data as $index => $raw) {
            if (!is_array($raw)) {
                throw new ApiBadResponseException('Beginning part ' . $first . ' of the upload to ' . $this->destination . ' granted a part that is not an object');
            }

            $grants[] = $this->grantFrom($raw, $first + $index);
        }

        if ($this->ref === null) {
            $this->bind($grants[0]);
            $this->choosePlan($data, $first);
            $this->ref = $grants[0]['ref'];
        }

        foreach ($grants as $index => $grant) {
            if (!$this->moreSource()) {
                break;
            }

            if ($this->plan !== null && UploadPlan::route($data[$index], $grant['number']) !== $this->plan['route']) {
                throw new ApiBadResponseException('The instructions for part ' . $grant['number'] . ' of the upload to ' . $this->destination . ' changed during the upload');
            }

            $this->addPart($grant);
        }

        $this->nextGrant = $first + $count;
    }

    // A renewal must give the part the instructions it had: its ref, number, size (the planned one, for an upload sizing
    // its own parts), method, headers and retry rules, and the same S3 upload.
    private function acceptRenewal($number, $data)
    {
        if (!isset($this->parts[$number])) {
            return;
        }

        if (!is_array($data) || count($data) !== 1 || !isset($data[0]) || !is_array($data[0])) {
            throw new ApiBadResponseException('Renewing part ' . $number . ' of the upload to ' . $this->destination . ' did not grant just that part');
        }

        $grant = $this->grantFrom($data[0], $number);
        $planned = $this->plan !== null ? $this->plan['part_size'] : $grant['partsize'];
        $grant['fingerprint'] = $this->fingerprint($grant, $planned);
        $sameRoute = $this->plan === null || UploadPlan::route($data[0], $number) === $this->plan['route'];
        if (!$sameRoute || $grant['fingerprint'] !== $this->parts[$number]['grant']['fingerprint']) {
            throw new ApiBadResponseException('The renewed instructions for part ' . $number . ' of the upload to ' . $this->destination . ' changed');
        }

        $this->parts[$number]['grant'] = $grant;
        $this->parts[$number]['ready_at'] = 0.0;
    }

    private function grantFrom(array $raw, $number)
    {
        $ref = self::field($raw, 'ref');
        $size = self::field($raw, 'partsize');
        $url = self::field($raw, 'upload_uri');
        if (
            !is_string($ref) || $ref === '' || ($this->ref !== null && $ref !== $this->ref) || self::field($raw, 'part_number') !== $number
            || !is_int($size) || $size < 0 || !is_string($url) || !preg_match('@\Ahttps?://@i', $url)
        ) {
            throw new ApiBadResponseException('Beginning part ' . $number . ' of the upload to ' . $this->destination . ' did not grant that part of the upload with a partsize and an upload_uri');
        }

        $method = self::field($raw, 'http_method');
        if ($method === null || $method === '') {
            $method = 'PUT';
        }

        if (!is_string($method) || !preg_match('/\A[!#$%&\'*+.^_`|~0-9A-Za-z-]+\z/', $method)) {
            throw new ApiBadResponseException('Part ' . $number . ' is granted an http_method that is not an HTTP method');
        }

        $parameters = self::field($raw, 'parameters');
        if ($parameters !== null && $parameters !== '' && $parameters !== []) {
            throw new NotImplementedException('Part ' . $number . ' asks for parameters, which the upload helpers do not send');
        }

        $send = self::field($raw, 'send');
        if ($send !== null && $send !== '' && $send !== [] && !UploadPlan::ordinarySend($send)) {
            throw new NotImplementedException('Part ' . $number . ' asks for send, which the upload helpers do not send');
        }

        $headers = self::field($raw, 'headers');
        $headers = $headers === null ? [] : $headers;
        if (!is_array($headers)) {
            throw new ApiBadResponseException('Part ' . $number . ' is granted headers that are not a map of names to values');
        }

        foreach ($headers as $name => $value) {
            if (!is_string($value)) {
                throw new ApiBadResponseException('Part ' . $number . ' is granted the header ' . $name . ' with a value that is not a string');
            }
        }

        return [
            'ref' => $ref, 'number' => $number, 'partsize' => $size, 'method' => $method, 'url' => $url, 'headers' => $headers,
            'expires_at' => RetryPolicy::expiresAt($url, self::field($raw, 'expires')), 'retry' => self::field($raw, 'retry_parts') !== false,
            'parallel' => self::field($raw, 'parallel_parts') !== false, 'direct' => self::directServer(self::field($raw, 'direct_connection_info')),
            'fingerprint' => null,
        ];
    }

    // The direct server a valid version 1 direct_connection_info names (server_name, addresses, direct_uri and ca_pem),
    // or null.
    private static function directServer($info)
    {
        if (!is_array($info)) {
            return null;
        }

        $addresses = self::field($info, 'addresses');
        $valid = self::field($info, 'version') === 1 && self::text(self::field($info, 'server_name')) && is_array($addresses) && $addresses;
        if ($valid) {
            foreach ($addresses as $address) {
                $valid = $valid && self::text($address);
            }
        }

        $uri = self::field($info, 'direct_uri');
        $certificate = self::field($info, 'ca_pem');
        $valid = $valid && is_string($uri) && preg_match('@\Ahttps?://@i', $uri) && self::text($certificate) && strpos($certificate, 'BEGIN CERTIFICATE') !== false;
        return $valid ? $info['server_name'] : null;
    }

    private function bind(array $grant)
    {
        $direct = $grant['direct'] !== null;
        $host = $direct ? $grant['direct'] : (string) parse_url($grant['url'], PHP_URL_HOST);
        list($this->controllerKey, $this->controller) = $this->engine->uploadController($direct ? 'direct' : 's3', $host);
    }

    // From the API's first answer: the upload sizes its own parts when every part the answer gives is for one and the
    // same ordinary S3 upload and UploadPlan can plan the file in the parts available, no larger than the job's buffer
    // holds unless the plan needs it to be. Otherwise, and for an empty file or a stream of unknown size, the API sizes
    // them. A file no plan could send within the buffer is refused here, before the upload keeps a plan or a ref.
    private function choosePlan(array $data, $first)
    {
        $this->plan = null;
        if ($this->size === null || $this->size <= 0) {
            return;
        }

        $routes = [];
        foreach ($data as $index => $raw) {
            $routes[] = UploadPlan::route($raw, $first + $index);
        }

        if ($routes[0] === null || count(array_unique($routes)) !== 1) {
            return;
        }

        $partSize = UploadPlan::partSize($this->size, self::field($data[0], 'available_parts'), $this->largestPart());
        if ($partSize !== null) {
            $this->plan = ['route' => $routes[0], 'part_size' => $partSize];
        }
    }

    // The largest part the job's buffer holds as this upload reserves it: any, for a file, whose parts stream.
    private function largestPart()
    {
        if ($this->kind === self::FILE) {
            return PHP_INT_MAX;
        }

        $limit = $this->engine->bufferLimit();
        return $limit >= 2 * self::SEND_BUFFER_BYTES ? $limit - self::SEND_BUFFER_BYTES : (int) floor($limit / 2);
    }

    private function addPart(array $grant)
    {
        $number = $grant['number'];
        $planned = $this->plan !== null ? $this->plan['part_size'] : $grant['partsize'];
        $length = null;
        if ($this->size !== null) {
            $remaining = $this->size - $this->plannedBytes;
            if ($planned <= 0 && $remaining > 0) {
                throw new ApiBadResponseException('Part ' . $number . ' of the upload to ' . $this->destination . ' is granted no bytes, with ' . $remaining . ' bytes left to send');
            }

            $length = min($planned, $remaining);
        } elseif ($planned <= 0) {
            throw new ApiBadResponseException('Part ' . $number . ' of the upload to ' . $this->destination . ' is granted no bytes');
        }

        $grant['fingerprint'] = $this->fingerprint($grant, $planned);
        $needed = $this->reservationFor($length === null ? $planned : $length);
        if ($needed > $this->engine->bufferLimit()) {
            throw new InvalidParameterException('Part ' . $number . ' of the upload to ' . $this->destination . ' needs ' . $needed . ' bytes of buffer, more than max_buffer_bytes (' . $this->engine->bufferLimit() . ')');
        }

        $this->parts[$number] = [
            'number' => $number, 'offset' => $length === null ? null : $this->plannedBytes, 'length' => $length, 'granted' => $planned,
            'grant' => $grant, 'state' => 'ready', 'etag' => null, 'failures' => 0, 'renewals' => 0, 'ready_at' => 0.0, 'data' => null,
            'reserved' => 0,
        ];
        if ($length !== null) {
            $this->plannedBytes += $length;
        }

        if (!$grant['parallel']) {
            $this->serial = true;
        }
    }

    // A digest of what a part's instructions bind it to, without its URL, which a renewal must keep.
    private function fingerprint(array $grant, $planned)
    {
        $headers = $grant['headers'];
        ksort($headers);
        return hash('sha256', json_encode([$grant['ref'], $grant['number'], $planned, $grant['method'], $headers, $grant['retry'], $grant['parallel']]));
    }

    // The buffer bytes a part of $length bytes holds: a file's, its attempt's send buffer only; a string's or a stream's,
    // its bytes too.
    private function reservationFor($length)
    {
        $send = min($length, self::SEND_BUFFER_BYTES);
        return $this->kind === self::FILE ? $send : $length + $send;
    }

    // The bytes an attempt reserves as it starts: none for a stream's part, which holds its reservation already.
    private function attemptBytes(array $part)
    {
        return $this->kind === self::STREAM ? 0 : $this->reservationFor($part['length']);
    }

    private function requestRenewal($number)
    {
        if ($this->parts[$number]['renewals'] >= self::RENEWALS) {
            throw new ApiBadResponseException('Part ' . $number . ' of the upload to ' . $this->destination . ' could not be given a URL that has not expired');
        }

        $this->parts[$number]['renewals']++;
        $this->renewals[] = $number;
        $this->engine->countRenewal();
    }

    // Counts a failed attempt of part $number and schedules its retry after $delay seconds; false, having failed the
    // upload with $error, when its grant forbids retries or its retries are spent.
    private function retryLater($number, $delay, $error, $now)
    {
        $this->parts[$number]['failures']++;
        $failures = $this->parts[$number]['failures'];
        if (!$this->parts[$number]['grant']['retry'] || $failures > $this->engine->maxRetries()) {
            if ($this->parts[$number]['grant']['retry'] && $failures > 1) {
                Logger::info('Retries exhausted - giving up on part ' . $number . ' of the upload to ' . $this->destination);
            }

            $this->engine->failTransfer($this, $error);
            return false;
        }

        $this->parts[$number]['ready_at'] = $now + $delay;
        $this->engine->countRetry();
        Logger::info('Retrying part ' . $number . ' of the upload to ' . $this->destination . ' (retry #' . $failures . ', waiting ' . round($delay, 3) . ' s)');
        return true;
    }

    private function accept($number, $etag, $now)
    {
        if ($this->parts[$number]['state'] === 'acked') {
            return;
        }

        $length = $this->parts[$number]['length'];
        $this->parts[$number]['state'] = 'acked';
        $this->parts[$number]['etag'] = $etag;
        $this->parts[$number]['data'] = null;
        if ($this->parts[$number]['reserved'] > 0) {
            $this->engine->release($this->parts[$number]['reserved']);
            $this->parts[$number]['reserved'] = 0;
        }

        $this->accepted += $length;
        if ($this->progress !== null) {
            $elapsed = max(0.0, $now - $this->callStarted);
            $this->reports[] = [$this->progress, [
                'total_bytes' => $this->size, 'acknowledged_bytes' => $this->accepted, 'part_number' => $number, 'part_bytes' => $length,
                'elapsed_seconds' => $elapsed, 'bytes_per_second' => $elapsed > 0 ? ($this->accepted - $this->callAccepted) / $elapsed : 0.0,
            ]];
        }
    }

    // The next part's bytes from the caller's stream: exactly the part's size, or, without the stream's size, as many
    // reads as it takes to fill its partsize, or fewer when the stream ends. A read that fails, or that gives nothing
    // before the stream ends, stops the upload before the part is sent.
    private function readPart(array $part)
    {
        $wanted = $part['length'] === null ? $part['granted'] : $part['length'];
        $bytes = '';
        while (strlen($bytes) < $wanted) {
            $read = fread($this->source, $wanted - strlen($bytes));
            if ($read === false) {
                throw new InvalidParameterException('Reading the upload\'s source failed');
            }

            if ($read === '') {
                if (feof($this->source)) {
                    break;
                }

                throw new InvalidParameterException('The upload\'s source gave no bytes before its end');
            }

            $bytes .= $read;
        }

        if ($part['length'] !== null && strlen($bytes) !== $part['length']) {
            throw new InvalidParameterException('The upload stream ended after ' . ($part['offset'] + strlen($bytes)) . ' of its ' . $this->size . ' bytes');
        }

        return $bytes;
    }

    private function body(array $part)
    {
        if ($this->kind === self::FILE) {
            $handle = @fopen($this->source, 'rb');
            if ($handle === false || self::identity($handle) !== $this->identity) {
                if ($handle !== false) {
                    fclose($handle);
                }

                throw new InvalidParameterException('The local file changed during its upload');
            }

            return new LimitStream(new Stream($handle), $part['length'], $part['offset']);
        }

        if ($this->kind === self::DATA) {
            return TransferEngine::memoryStream((string) substr($this->source, $part['offset'], $part['length']));
        }

        return TransferEngine::memoryStream($part['data']);
    }

    private function checkSource()
    {
        if ($this->kind === self::FILE) {
            clearstatcache();
            if (!is_resource($this->handle) || self::identity($this->handle) !== $this->identity) {
                throw new InvalidParameterException('The local file changed during its upload');
            }
        }
    }

    private function releaseResources()
    {
        foreach ($this->parts as $number => $part) {
            if ($part['reserved'] > 0) {
                $this->engine->release($part['reserved']);
            }

            $this->parts[$number]['reserved'] = 0;
            $this->parts[$number]['data'] = null;
        }

        if (is_resource($this->handle)) {
            fclose($this->handle);
        }

        $this->handle = null;
    }

    private static function identity($handle)
    {
        $stat = fstat($handle);
        if (!is_array($stat)) {
            throw new InvalidParameterException('The upload\'s source file could not be read');
        }

        return ['dev' => $stat['dev'], 'ino' => $stat['ino'], 'size' => $stat['size'], 'mtime' => $stat['mtime']];
    }

    // The ETag acknowledging a part: exactly one, not empty, and not a list.
    private static function etag($response)
    {
        $values = $response->getHeader('ETag');
        if (count($values) !== 1 || !is_string($values[0]) || trim($values[0]) === '' || preg_match('/"\s*,\s*(?:W\/)?"/', $values[0])) {
            return null;
        }

        return $values[0];
    }

    // An error response's body as far as RetryPolicy::BODY_LIMIT bytes, and whether that is all of it: one byte more is
    // read to tell a body that ends there from one that goes on, and a body that cannot be read is not complete.
    private static function limitedBody($response)
    {
        try {
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $text = '';
            while (strlen($text) <= RetryPolicy::BODY_LIMIT && !$body->eof()) {
                $chunk = $body->read(RetryPolicy::BODY_LIMIT + 1 - strlen($text));
                if ($chunk === '') {
                    break;
                }

                $text .= $chunk;
            }

            $complete = strlen($text) <= RetryPolicy::BODY_LIMIT && $body->eof();
            return [(string) substr($text, 0, RetryPolicy::BODY_LIMIT), $complete];
        } catch (\Exception $e) {
            return ['', false];
        }
    }

    private static function header(array $headers, $name)
    {
        foreach ($headers as $key => $value) {
            if (strcasecmp((string) $key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }

    private static function field(array $values, $name)
    {
        return array_key_exists($name, $values) ? $values[$name] : null;
    }

    private static function text($value)
    {
        return is_string($value) && trim($value) !== '';
    }
}
