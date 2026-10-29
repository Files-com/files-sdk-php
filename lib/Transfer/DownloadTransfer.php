<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Api;
use Files\Exception\ApiBadResponseException;
use Files\Exception\InvalidParameterException;
use Files\Logger;
use GuzzleHttp\Psr7\FnStream;

/**
 * One download a TransferJob runs, of the file at $remote to a local file or to a caller's stream.
 *
 * Its URL comes from File#download's request (GET /files/{path}), with the job's copied configuration. Its first
 * request asks for the first part's bytes (Range: bytes=0-, download_part_bytes of them). A 206 whose Content-Range
 * gives exactly that range and the file's total, with a strong ETag, binds the download to that version: every other
 * range is asked for with If-Match: that ETag, and must come back 206 with exactly its range and the same total, with
 * that ETag or none; a weak or different ETag, a different total or HTTP 412 fails the download, as the file changed.
 * Bound ranges are fetched concurrently under the job's download controller, and each must bring exactly its bytes
 * before it is accepted; one that brings fewer or more, or another range, is fetched again from its start.
 *
 * Storage that does not bind a version (no strong ETag), answers the first request whole (200), or later ignores a
 * range (200), is downloaded whole instead, as one response, restarted from its first byte when it is interrupted. A
 * response carrying X-Files-Frontend-App came from the Files.com web app, not storage, and fails the download before
 * any of its body is written. Redirects are followed; their bodies are not the file. An expired URL is renewed before
 * it is used (at most three times for a range). Each attempt keeps the URL it was sent with, that URL's generation (one
 * more for each URL the API gives) and its expiry: a 403 for an attempt whose own URL is known to have expired is
 * retried as one of the range's retries, with the newer URL already given when there is one and otherwise after
 * renewing it; a 403 for any other attempt fails the download.
 *
 * A local file is written to a staging file beside it (named .<name>.files-download-<random>), each range at its own
 * offset through a handle of its own; a caller's stream is written in order and never sought or closed, and a write it
 * takes only part of is continued from the byte where it stopped, while one it takes nothing of fails the download.
 * Once every byte is verified the download is published (the staging file moved into place, or a held whole response
 * written to the stream) only after its own progress reports have been delivered: one that throws, cancels or fails the
 * job leaves the destination as it was, and one that pauses the job keeps what was verified, to be published on
 * resuming without being fetched again. A failure or cancellation removes the staging file and leaves the destination
 * as it was.
 *
 * Bytes the download holds stay charged to the job's buffer for as long as they are held (see holdingShares). Ranges
 * completed out of order for a caller's stream are held in memory, each reserved before it is fetched, until the bytes
 * before them are written. A whole response for a caller's stream is held until it is complete and published, so a
 * restart never writes bytes to the stream twice: in a temporary stream whose memory, with each chunk copied out of it,
 * stays within its attempt's reservation, the rest spilling to a temporary file, and that reservation stays charged from
 * the response through its final reports, a pause and its publication until the held bytes are written or given up. A
 * body a custom handler gave instead of writing it is copied one chunk at a time within the same charges, as far as its
 * range or length declares, and accepted only once it shows its end there: a byte more is refused, as it would be from
 * storage. A range for a caller's stream from one is staged in a temporary file until then, and an error response's
 * body from one is kept as storage's is, to classify it. Storage that stops honoring ranges after bytes reached a
 * caller's stream fails the download rather than writing them again.
 *
 * Each attempt's measured duration is its request's time, including the reading of a body a custom handler gave,
 * without the time spent writing to the destination. Progress counts each byte of the file once: a whole response
 * restarted from the first byte adds only the bytes beyond those already accepted, and a call's rate only its own.
 */
final class DownloadTransfer
{
    const RENEWALS = 3;
    const RECEIVE_BUFFER_BYTES = 65536;

    const FILE = 'file';
    const STREAM = 'stream';

    private $engine;
    private $id;
    private $remote;
    private $kind;
    private $destination;
    private $apiOptions;
    private $progress;
    private $partBytes = 8388608;
    private $state = 'queued';
    private $error = null;
    private $url = null;
    private $urlExpiresAt = null;
    private $generation = 0;
    private $metadataInFlight = false;
    private $renewalWanted = false;
    private $controller = null;
    private $controllerKey = null;
    private $mode = null;
    private $probe = ['state' => 'ready', 'failures' => 0, 'renewals' => 0, 'ready_at' => 0.0];
    private $whole = null;
    private $etag = null;
    private $total = null;
    private $ranges = [];
    private $nextWrite = 0;
    private $staging = null;
    private $holding = null;
    private $holdingCharge = 0;
    private $verified = false;
    private $written = 0;
    private $received = 0;
    private $callReceived = 0;
    private $callStarted = 0.0;
    private $reports = [];
    private $attempts = [];
    private $nextAttempt = 0;
    private $result = null;

    // $kind is FILE ($destination a local path) or STREAM ($destination a caller's writable stream).
    public function __construct($remote, $kind, $destination, array $apiOptions, $progress)
    {
        $this->remote = $remote;
        $this->kind = $kind;
        $this->destination = $destination;
        $this->apiOptions = $apiOptions;
        $this->progress = $progress;
    }

    public function setEngine(TransferEngine $engine, $id)
    {
        $this->engine = $engine;
        $this->id = $id;
        $this->partBytes = $engine->downloadPartBytes();
    }

    public function id()
    {
        return $this->id;
    }

    public function direction()
    {
        return 'download';
    }

    public function remotePath()
    {
        return $this->remote;
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
        return $this->total;
    }

    public function completedBytes()
    {
        return $this->received;
    }

    public function result()
    {
        return $this->result;
    }

    public function controllerKey()
    {
        return $this->controllerKey;
    }

    public function serialBlocked()
    {
        return false;
    }

    public function checkpoint()
    {
        $completed = [];
        foreach ($this->ranges as $range) {
            if ($range['state'] === 'done' || $range['state'] === 'written') {
                $completed[] = [$range['start'], $range['end']];
            }
        }

        return [
            'direction' => 'download', 'path' => $this->remote, 'destination' => $this->kind === self::FILE ? $this->destination : 'stream',
            'status' => $this->state, 'mode' => $this->mode === null ? 'probing' : $this->mode, 'version_bound' => $this->etag !== null,
            'total_bytes' => $this->total, 'completed_bytes' => $this->received, 'completed_ranges' => $completed,
            'stream_bytes_written' => $this->kind === self::STREAM ? $this->written : null,
        ];
    }

    public function open($now)
    {
        $limit = $this->engine->bufferLimit();
        $needed = $this->kind === self::STREAM ? $this->partBytes : min($this->partBytes, self::RECEIVE_BUFFER_BYTES);
        if ($needed > $limit) {
            throw new InvalidParameterException('Downloading ' . $this->remote . ' needs ' . $needed . ' bytes of buffer, more than max_buffer_bytes (' . $limit . ')');
        }

        if ($this->kind === self::FILE) {
            $this->staging = dirname($this->destination) . DIRECTORY_SEPARATOR . '.' . basename($this->destination) . '.files-download-' . md5(uniqid('', true) . mt_rand());
            $handle = @fopen($this->staging, 'xb');
            if ($handle === false) {
                $this->staging = null;
                throw new InvalidParameterException('The download of ' . $this->remote . ' could not create its staging file beside ' . $this->destination);
            }

            fclose($handle);
        } elseif (!is_resource($this->destination)) {
            throw new InvalidParameterException('The download\'s destination stream is not open');
        }

        $this->state = 'active';
        $this->callStarted = (float) $now;
        $this->callReceived = $this->received;
    }

    // Publishes a verified download, or reserves the job's buffer for a caller's stream's next ranges, in order, before
    // they are fetched. The job prepares its transfers only while it runs, after delivering every report: a verified
    // download is published once its own reports have returned without stopping the job, and a job that stopped
    // instead pauses it (keeping what was verified) or cancels it (giving that up) without publishing.
    public function prepare($now)
    {
        if ($this->state !== 'active') {
            return;
        }

        if ($this->verified) {
            if (!$this->reports) {
                $this->verified = false;
                $this->finish();
            }

            return;
        }

        if ($this->kind !== self::STREAM || $this->mode !== 'ranged') {
            return;
        }

        $limit = max(1, $this->controller->target());
        $held = 0;
        foreach ($this->ranges as $range) {
            $held += $range['reserved'] > 0 ? 1 : 0;
        }

        foreach ($this->ranges as $index => $range) {
            if ($range['state'] !== 'ready' || $range['reserved'] > 0) {
                continue;
            }

            $bytes = $range['end'] - $range['start'] + 1;
            if ($held >= $limit || !$this->engine->reserve($bytes, $this->id)) {
                return;
            }

            $this->ranges[$index]['reserved'] = $bytes;
            $held++;
        }
    }

    public function nextApi($now)
    {
        if ($this->state !== 'active' || $this->metadataInFlight || ($this->url !== null && !$this->renewalWanted)) {
            return null;
        }

        $this->metadataInFlight = true;
        return [
            'type' => 'api', 'transfer' => $this, 'tag' => 'metadata', 'method' => 'GET', 'path' => '/files/' . rawurlencode($this->remote),
            'params' => ['path' => $this->remote], 'options' => $this->apiOptions, 'retry' => 'idempotent', 'attempt' => 0,
            'describe' => 'the download URL of ' . $this->remote, 'number' => 0, 'count' => 0,
        ];
    }

    public function apiDone(array $item, $data, $now)
    {
        $this->metadataInFlight = false;
        if ($this->state !== 'active') {
            return;
        }

        $uri = is_array($data) && isset($data['download_uri']) && is_string($data['download_uri']) ? $data['download_uri'] : '';
        if (!preg_match('@\Ahttps?://@i', $uri)) {
            throw new ApiBadResponseException('The API gave no download_uri for ' . $this->remote);
        }

        $this->url = $uri;
        $this->urlExpiresAt = RetryPolicy::expiresAt($uri, null);
        $this->generation++;
        $this->renewalWanted = false;
        if ($this->controller === null) {
            list($this->controllerKey, $this->controller) = $this->engine->downloadController((string) parse_url($uri, PHP_URL_HOST));
        }
    }

    public function apiAborted(array $item, $now)
    {
        $this->metadataInFlight = false;
    }

    public function nextContent($now)
    {
        if ($this->state !== 'active' || $this->url === null || $this->metadataInFlight || $this->renewalWanted || $this->controller === null) {
            return null;
        }

        if ($this->mode === null) {
            return $this->offer('probe', $now);
        }

        if ($this->mode === 'whole') {
            return $this->offer('whole', $now);
        }

        foreach ($this->ranges as $index => $range) {
            if ($range['state'] !== 'ready' || ($this->kind === self::STREAM && $range['reserved'] === 0)) {
                continue;
            }

            $item = $this->offer($index, $now);
            if ($item !== null || $this->renewalWanted) {
                return $item;
            }
        }

        return null;
    }

    public function begin(array $item, $now)
    {
        $unit = $item['unit'];
        $attempt = ++$this->nextAttempt;
        $config = $this->engine->config();
        $headers = ['User-Agent' => 'Files.com PHP SDK v' . Api::VERSION];
        if ($config['language']) {
            $headers['Accept-Language'] = (string) $config['language'];
        }

        $start = 0;
        $expected = null;
        if ($unit === 'probe') {
            $headers['Range'] = 'bytes=0-' . ($this->partBytes - 1);
        } elseif (is_int($unit)) {
            $range = $this->ranges[$unit];
            $start = $range['start'];
            $expected = $range['end'] - $range['start'] + 1;
            $headers['Range'] = 'bytes=' . $range['start'] . '-' . $range['end'];
            $headers['If-Match'] = $this->etag;
        }

        // The attempt keeps the URL it is sent with, that URL's generation and expiry, and the bytes the job reserved for
        // it (its charge, released when it settles): what it holds while it runs stays within that charge. Its body's
        // end is the transport's to show (proven), but a custom handler's own body must show it (copyUnwritten).
        $sink = $this->sink($attempt);
        $this->attempts[$attempt] = [
            'unit' => $unit, 'mode' => 'pending', 'start' => $start, 'expected' => $expected, 'written' => 0, 'write_seconds' => 0.0,
            'handle' => null, 'buffer' => '', 'capture' => '', 'capture_complete' => true, 'status' => null, 'headers' => [], 'etag' => null,
            'total' => null, 'validated' => false, 'sink' => $sink, 'url' => $this->url, 'expires_at' => $this->urlExpiresAt,
            'generation' => $this->generation, 'charge' => (int) $item['bytes'], 'proven' => true,
        ];
        $this->setUnit($unit, 'state', 'inflight');
        return [
            'method' => 'GET', 'url' => $this->url, 'attempt' => $attempt,
            'options' => [
                'headers' => $headers, 'http_errors' => false, 'decode_content' => false, 'sink' => $sink,
                'on_headers' => function ($response) use ($attempt) {
                    $this->validate($attempt, $response);
                },
            ],
        ];
    }

    public function contentDone(array $item, $ok, $value, array $timing, $now)
    {
        $attempt = $item['request']['attempt'];
        if (!isset($this->attempts[$attempt])) {
            return [ConcurrencyController::ENDED];
        }

        if ($ok && !$this->attempts[$attempt]['validated']) {
            // A handler that calls no on_headers (a custom handler of the serial transport).
            try {
                $this->validate($attempt, $value);
            } catch (\RuntimeException $e) {
                // The mode it set says what is wrong.
            }
        }

        $ended = $timing['ended'];
        if ($ok && $this->copyUnwritten($attempt, $value)) {
            // A custom handler's body was read only now: that reading is part of the attempt, and what it accepted is
            // reported as of its end.
            $now = $this->engine->now();
            $ended = $now;
        }

        $state = $this->attempts[$attempt];
        unset($this->attempts[$attempt]);
        $this->closeAttempt($state);
        $unit = $state['unit'];
        $mode = $state['mode'];
        $complete = $ok && ($mode === 'range' || $mode === 'whole') && $state['proven'] && ($state['expected'] === null || $state['written'] === $state['expected']);
        $active = $this->state === 'active' && $this->unitInFlight($unit);
        if ($state['handle'] !== null && $state['handle'] === $this->holding && !($complete && $active)) {
            // A whole response that did not complete is not kept, as it restarts from its first byte: what it held goes
            // with its attempt's charge, which the job released as the attempt settled.
            $this->dropHolding();
        }

        if (!$active) {
            return [ConcurrencyController::ENDED];
        }

        $this->setUnit($unit, 'state', 'ready');
        $duration = max(0.0, ($ended - $timing['started']) - $state['write_seconds']);
        $what = is_int($unit) ? 'Bytes ' . $state['start'] . '-' . ($state['start'] + $state['expected'] - 1) . ' of the download of ' . $this->remote : 'The download of ' . $this->remote;
        if ($mode === 'range' || $mode === 'whole') {
            if ($complete) {
                return $this->completed($unit, $state, $duration, $now);
            }

            if ($ok) {
                $short = $state['expected'] !== null && $state['written'] !== $state['expected'];
                $detail = $short ? 'ended after ' . $state['written'] . ' of its ' . $state['expected'] . ' bytes' : 'did not show its end after ' . $state['written'] . ' bytes';
                return $this->retryOrFail($unit, ConcurrencyController::FAILURE, RetryPolicy::STORAGE_DELAY, new ApiBadResponseException($what . ' ' . $detail), $now);
            }

            if (!RetryPolicy::networkRetryable($value)) {
                $this->engine->failTransfer($this, RetryPolicy::networkError($value, $what));
                return [ConcurrencyController::ENDED];
            }

            return $this->retryOrFail($unit, ConcurrencyController::FAILURE, RetryPolicy::STORAGE_DELAY, RetryPolicy::networkError($value, $what), $now);
        }

        if ($mode === 'frontend') {
            $this->engine->failTransfer($this, new ApiBadResponseException('The download of ' . $this->remote . ' was answered by the Files.com web app (X-Files-Frontend-App), not by its storage'));
            return [ConcurrencyController::ENDED];
        }

        if ($mode === 'identity') {
            $this->engine->failTransfer($this, new ApiBadResponseException('The file ' . $this->remote . ' changed during its download'));
            return [ConcurrencyController::ENDED];
        }

        if ($mode === 'unbound') {
            $this->restartWhole($now);
            return [ConcurrencyController::ENDED];
        }

        if ($mode === 'retry' || $mode === 'oversize') {
            return $this->retryOrFail($unit, ConcurrencyController::FAILURE, RetryPolicy::STORAGE_DELAY, new ApiBadResponseException('Storage answered ' . lcfirst($what) . ' with other bytes than those asked for'), $now);
        }

        if ($mode === 'local') {
            $this->engine->failTransfer($this, new InvalidParameterException('Writing the download of ' . $this->remote . ' to its destination failed'));
            return [ConcurrencyController::ENDED];
        }

        if ($mode === 'discard') {
            $status = $state['status'];
            $error = RetryPolicy::storageError($what, $status);
            if ($status === 403 && RetryPolicy::isExpired($state['expires_at'])) {
                // The URL this attempt was sent with had expired: the range is retried with the newer URL already given,
                // when there is one, and otherwise with the URL a renewal gives.
                if ($this->retryLater($unit, 0.0, $error, $now) && $state['generation'] === $this->generation) {
                    $this->wantRenewal($unit);
                }

                return [ConcurrencyController::ENDED];
            }

            // The kept body is all of it only when the response ended normally and nothing past the kept bytes came.
            $captured = $ok && $state['capture_complete'];
            list($decision, $delay) = RetryPolicy::storageResponse($status, $state['headers'], $state['capture'], $captured);
            if ($decision === 'fail') {
                $this->engine->failTransfer($this, $error);
                return [ConcurrencyController::ENDED];
            }

            $kind = $decision === 'throttle' ? ConcurrencyController::THROTTLE : ConcurrencyController::FAILURE;
            return $this->retryOrFail($unit, $kind, $delay, $error, $now);
        }

        if ($ok) {
            $this->engine->failTransfer($this, new ApiBadResponseException($what . ' was answered without a usable response'));
            return [ConcurrencyController::ENDED];
        }

        if (!RetryPolicy::networkRetryable($value)) {
            $this->engine->failTransfer($this, RetryPolicy::networkError($value, $what));
            return [ConcurrencyController::ENDED];
        }

        return $this->retryOrFail($unit, ConcurrencyController::FAILURE, RetryPolicy::STORAGE_DELAY, RetryPolicy::networkError($value, $what), $now);
    }

    public function contentAborted(array $item, $now)
    {
        $attempt = $item['request']['attempt'];
        if (isset($this->attempts[$attempt])) {
            $state = $this->attempts[$attempt];
            $this->closeAttempt($state);
            unset($this->attempts[$attempt]);
            if ($state['handle'] !== null && $state['handle'] === $this->holding) {
                // An aborted whole response is fetched again from its first byte: what it held goes with its charge.
                $this->dropHolding();
            }
        }

        if ($this->unitInFlight($item['unit'])) {
            $this->setUnit($item['unit'], 'state', 'ready');
        }
    }

    public function readyCount($now)
    {
        $ready = 0;
        foreach ($this->units() as $unit => $state) {
            $reserved = !is_int($unit) || $this->kind !== self::STREAM || $state['reserved'] > 0;
            if ($state['state'] === 'ready' && $state['ready_at'] <= $now && $reserved) {
                $ready++;
            }
        }

        return $ready;
    }

    public function wakeAt($now)
    {
        $wake = null;
        foreach ($this->units() as $state) {
            if ($state['state'] === 'ready' && $state['ready_at'] > $now && ($wake === null || $state['ready_at'] < $wake)) {
                $wake = $state['ready_at'];
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
        foreach ($this->units() as $unit => $state) {
            if ($state['state'] === 'inflight') {
                $this->setUnit($unit, 'state', 'ready');
            }
        }

        $this->state = 'paused';
    }

    public function resume($now)
    {
        $this->state = 'active';
        $this->callStarted = (float) $now;
        $this->callReceived = $this->received;
    }

    public function fail($error)
    {
        $this->state = 'failed';
        $this->error = $error;
        $this->cleanUp();
    }

    public function cancel()
    {
        $this->state = 'cancelled';
        $this->cleanUp();
    }

    private function offer($unit, $now)
    {
        $state = $this->unit($unit);
        if ($state === null || $state['state'] !== 'ready' || $state['ready_at'] > $now) {
            return null;
        }

        if (RetryPolicy::isExpiring($this->urlExpiresAt)) {
            $this->wantRenewal($unit);
            return null;
        }

        return [
            'type' => 'content', 'transfer' => $this, 'controller' => $this->controller, 'controller_key' => $this->controllerKey, 'unit' => $unit,
            'bytes' => $this->attemptBytes($unit),
        ];
    }

    // The bytes an attempt reserves as it starts: a stream's range holds its reservation already, and the first request
    // to a stream buffers up to its whole range; everything else, a receive buffer, which for a whole response (held
    // for a stream until it is published, see holdingShares) is at most the job's whole buffer, so that a small
    // max_buffer_bytes still lets storage that ignores ranges be downloaded whole.
    private function attemptBytes($unit)
    {
        $receive = min(self::RECEIVE_BUFFER_BYTES, $this->engine->bufferLimit());
        if ($this->kind === self::STREAM) {
            if ($unit === 'probe') {
                return $this->partBytes;
            }

            return is_int($unit) ? 0 : $receive;
        }

        if (is_int($unit)) {
            return min($this->ranges[$unit]['end'] - $this->ranges[$unit]['start'] + 1, $receive);
        }

        return $unit === 'probe' ? min($this->partBytes, $receive) : $receive;
    }

    private function wantRenewal($unit)
    {
        $state = $this->unit($unit);
        if ($state['renewals'] >= self::RENEWALS) {
            throw new ApiBadResponseException('The download of ' . $this->remote . ' could not be given a URL that has not expired');
        }

        $this->setUnit($unit, 'renewals', $state['renewals'] + 1);
        if (!$this->renewalWanted) {
            $this->renewalWanted = true;
            $this->engine->countRenewal();
        }
    }

    // Checks a response's status and headers, as they arrive and before any of its body is written: the first
    // request's, a whole response's, or a bound range's. Throws, which ends the request at once, for anything that must
    // not reach the destination.
    private function validate($attempt, $response)
    {
        if (!isset($this->attempts[$attempt])) {
            return;
        }

        $status = $response->getStatusCode();
        $this->attempts[$attempt]['validated'] = true;
        $this->attempts[$attempt]['status'] = $status;
        $this->attempts[$attempt]['headers'] = $response->getHeaders();
        $this->attempts[$attempt]['written'] = 0;
        $this->attempts[$attempt]['buffer'] = '';
        $this->attempts[$attempt]['capture'] = '';
        $this->attempts[$attempt]['capture_complete'] = true;
        $this->attempts[$attempt]['mode'] = 'discard';
        if ($response->hasHeader('X-Files-Frontend-App')) {
            $this->reject($attempt, 'frontend');
        }

        if ($status >= 300 && $status < 400) {
            return;
        }

        $unit = $this->attempts[$attempt]['unit'];
        $length = self::contentLength($response);
        if ($unit === 'probe') {
            $this->validateFirst($attempt, $response, $status, $length);
        } elseif ($unit === 'whole') {
            if ($status === 206) {
                $this->reject($attempt, 'retry');
            }

            if ($status === 200) {
                $this->acceptBody($attempt, 'whole', $length);
            }
        } else {
            $this->validateRange($attempt, $response, $status, $length);
        }
    }

    private function validateFirst($attempt, $response, $status, $length)
    {
        if ($status === 200) {
            $this->acceptBody($attempt, 'whole', $length);
            return;
        }

        if ($status === 416) {
            $this->reject($attempt, 'unbound');
        }

        if ($status !== 206) {
            return;
        }

        $range = self::contentRange($response);
        if ($range === null || $range[0] !== 0 || $range[1] !== min($this->partBytes, $range[2]) - 1 || ($length !== null && $length !== $range[1] + 1)) {
            $this->reject($attempt, 'retry');
        }

        $etag = self::strongEtag($response);
        if ($etag === null) {
            $this->reject($attempt, 'unbound');
        }

        $this->attempts[$attempt]['etag'] = $etag;
        $this->attempts[$attempt]['total'] = $range[2];
        $this->acceptBody($attempt, 'range', $range[1] + 1);
    }

    private function validateRange($attempt, $response, $status, $length)
    {
        if ($status === 412) {
            $this->reject($attempt, 'identity');
        }

        if ($status !== 200 && $status !== 206) {
            return;
        }

        $etags = $response->getHeader('ETag');
        if ($etags && (count($etags) !== 1 || trim($etags[0]) !== $this->etag)) {
            $this->reject($attempt, 'identity');
        }

        if ($status === 200) {
            $this->reject($attempt, 'unbound');
        }

        $range = self::contentRange($response);
        if ($range === null) {
            $this->reject($attempt, 'retry');
        }

        if ($range[2] !== $this->total) {
            $this->reject($attempt, 'identity');
        }

        $start = $this->attempts[$attempt]['start'];
        $expected = $this->attempts[$attempt]['expected'];
        if ($range[0] !== $start || $range[1] !== $start + $expected - 1 || ($length !== null && $length !== $expected)) {
            $this->reject($attempt, 'retry');
        }

        $this->acceptBody($attempt, 'range', $expected);
    }

    private function reject($attempt, $mode)
    {
        $this->attempts[$attempt]['mode'] = $mode;
        throw new \RuntimeException('The download response was refused (' . $mode . ')');
    }

    // Lets the response's body reach its destination: a range's buffer (for a caller's stream), or a handle at its
    // offset of the staging file, or, for a whole response, the truncated staging file or a new holding stream.
    private function acceptBody($attempt, $mode, $expected)
    {
        $this->attempts[$attempt]['mode'] = $mode;
        $this->attempts[$attempt]['expected'] = $expected;
        if ($this->kind === self::STREAM && $mode === 'range') {
            return;
        }

        if ($this->kind === self::STREAM) {
            // A whole response for a stream is held in memory only as far as its attempt's charge leaves room beside a
            // copied chunk; the rest spills to a temporary file (holdingShares).
            $this->dropHolding();
            list($memory) = self::holdingShares($this->attempts[$attempt]['charge']);
            $this->holding = @fopen('php://temp/maxmemory:' . $memory, 'w+b');
            $handle = $this->holding;
        } else {
            $handle = @fopen($this->staging, 'c+b');
            if ($handle !== false) {
                $moved = $mode === 'whole' ? ftruncate($handle, 0) && rewind($handle) : fseek($handle, $this->attempts[$attempt]['start']) === 0;
                if (!$moved) {
                    fclose($handle);
                    $handle = false;
                }
            }
        }

        if ($handle === false) {
            $this->reject($attempt, 'local');
        }

        $this->attempts[$attempt]['handle'] = $handle;
    }

    private function sink($attempt)
    {
        return new FnStream([
            '__toString' => function () {
                return '';
            },
            'close' => function () {
            },
            'detach' => function () {
                return null;
            },
            'getSize' => function () {
                return null;
            },
            'tell' => function () use ($attempt) {
                return isset($this->attempts[$attempt]) ? $this->attempts[$attempt]['written'] : 0;
            },
            'eof' => function () {
                return true;
            },
            'isSeekable' => function () {
                return false;
            },
            'seek' => function () {
                throw new \RuntimeException('A download\'s sink cannot seek');
            },
            'rewind' => function () {
                throw new \RuntimeException('A download\'s sink cannot rewind');
            },
            'isWritable' => function () {
                return true;
            },
            'write' => function ($data) use ($attempt) {
                // The bytes as they came, not cast when they are a string already: PHP 5 would copy them to cast them.
                if (!is_string($data)) {
                    $data = (string) $data;
                }

                return $this->receive($attempt, $data);
            },
            'isReadable' => function () {
                return false;
            },
            'read' => function () {
                return '';
            },
            'getContents' => function () {
                return '';
            },
            'getMetadata' => function ($key = null) {
                return $key === null ? [] : null;
            },
        ]);
    }

    // Body bytes as they arrive: kept (at most BODY_LIMIT) from a response whose status is not the file's, written to the
    // destination when validate let them through, and otherwise refused, which ends the request. Bytes beyond what the
    // response's range or length promised are refused too. The attempt's fields are read one at a time, never the attempt
    // as a whole: a copy of it would hold its range's buffer too, so that appending to the buffer could not extend it
    // where it is but would build the longer one beside it.
    private function receive($attempt, $data)
    {
        if (!isset($this->attempts[$attempt])) {
            return 0;
        }

        $length = strlen($data);
        $mode = $this->attempts[$attempt]['mode'];
        if ($mode === 'discard') {
            $room = RetryPolicy::BODY_LIMIT - strlen($this->attempts[$attempt]['capture']);
            if ($room > 0) {
                $this->attempts[$attempt]['capture'] .= (string) substr($data, 0, $room);
            }

            if ($length > $room) {
                // More came than is kept: what is kept is not the whole body.
                $this->attempts[$attempt]['capture_complete'] = false;
            }

            return $length;
        }

        if ($mode !== 'range' && $mode !== 'whole') {
            return 0;
        }

        $expected = $this->attempts[$attempt]['expected'];
        if ($expected !== null && $this->attempts[$attempt]['written'] + $length > $expected) {
            $this->attempts[$attempt]['mode'] = 'oversize';
            return 0;
        }

        $handle = $this->attempts[$attempt]['handle'];
        $started = $this->engine->now();
        if ($handle !== null) {
            // The staging file, a held whole response, or the temporary file a custom handler's range for a caller's
            // stream is staged in (copyUnwritten).
            $stored = self::writeAll($handle, $data);
        } elseif ($this->kind === self::STREAM && $mode === 'range') {
            $this->attempts[$attempt]['buffer'] .= $data;
            $stored = true;
        } else {
            $stored = false;
        }

        $this->attempts[$attempt]['write_seconds'] += $this->engine->now() - $started;
        if (!$stored) {
            $this->attempts[$attempt]['mode'] = 'local';
            return 0;
        }

        $this->attempts[$attempt]['written'] += $length;
        return $length;
    }

    // For a handler that wrote nothing to the sink but answered with a body of its own (a custom handler of the serial
    // transport). A body for the file goes through the sink as it would have, one chunk at a time (copyChunk), each
    // given up before the next is read, and only as far as the range or length it declared; a body whose own size says
    // it is longer is refused unread. It is accepted only once it shows its end there (bodyEnded), never on what it
    // declared alone. A range for a caller's stream is staged meanwhile in a temporary file and taken into memory only
    // once its end is shown (loadStaged), so that its reservation never holds its bytes and a byte more at once. The
    // body of an error response that may be S3's RequestTimeout (a 400) is kept as storage's is (captureBody), to
    // classify it. True when the body was read here, which then took the time the reading took.
    private function copyUnwritten($attempt, $response)
    {
        $state = $this->attempts[$attempt];
        $mode = $state['mode'];
        $copied = $mode === 'range' || $mode === 'whole';
        if ((!$copied && !($mode === 'discard' && $state['status'] === 400)) || $state['written'] > 0 || $state['capture'] !== '') {
            return false;
        }

        $body = $response->getBody();
        if ($body === $state['sink']) {
            return false;
        }

        if ($body->isSeekable()) {
            $body->rewind();
        }

        if (!$copied) {
            $this->captureBody($attempt, $body);
            return true;
        }

        $size = $body->getSize();
        if ($state['expected'] !== null && $size !== null && $size > $state['expected']) {
            $this->attempts[$attempt]['mode'] = 'oversize';
            return false;
        }

        $staged = $this->kind === self::STREAM && $mode === 'range';
        if ($staged) {
            // Closed by loadStaged, or by cleanUp should reading the body throw.
            $stage = @tmpfile();
            if ($stage === false) {
                $this->attempts[$attempt]['mode'] = 'local';
                return false;
            }

            $this->attempts[$attempt]['handle'] = $stage;
        }

        while (!$body->eof()) {
            $wanted = $this->copyChunk($this->attempts[$attempt]);
            if ($wanted <= 0) {
                break;
            }

            $chunk = $body->read($wanted);
            $length = strlen($chunk);
            $taken = $length > 0 ? $this->receive($attempt, $chunk) : 0;
            // The chunk is given up before another is read: no two are held at once.
            $chunk = null;
            if ($length === 0 || $taken !== $length) {
                break;
            }
        }

        $this->attempts[$attempt]['proven'] = $this->bodyEnded($attempt, $body);
        if ($staged) {
            $this->loadStaged($attempt);
        }

        return true;
    }

    // Keeps the body of an error response a custom handler gave as receive keeps storage's: at most BODY_LIMIT bytes,
    // reading at most one byte more, and whether that was all of it, which only a body that then says it has ended is.
    // Each chunk is given up before the next is read.
    private function captureBody($attempt, $body)
    {
        while (!$body->eof() && $this->attempts[$attempt]['capture_complete']) {
            $chunk = $body->read(RetryPolicy::BODY_LIMIT + 1 - strlen($this->attempts[$attempt]['capture']));
            $length = strlen($chunk);
            if ($length > 0) {
                $this->receive($attempt, $chunk);
            }

            $chunk = null;
            if ($length === 0) {
                break;
            }
        }

        if (!$body->eof()) {
            // It went on past what is kept, or stopped giving bytes without saying it had ended.
            $this->attempts[$attempt]['capture_complete'] = false;
        }
    }

    // Whether a custom handler's body, its copy stopped, has shown its end: it says it has ended, having given all its
    // range or length declared (or, declaring none, all it gave), when nothing more comes from it. One byte more is asked
    // for only then, with no chunk held, so within any attempt's charge. A byte past a declared range or length is
    // refused (oversize); a body that stops giving bytes without saying it has ended shows nothing.
    private function bodyEnded($attempt, $body)
    {
        $state = $this->attempts[$attempt];
        if (($state['mode'] !== 'range' && $state['mode'] !== 'whole') || ($state['expected'] !== null && $state['written'] !== $state['expected'])) {
            return false;
        }

        if ($body->eof()) {
            return true;
        }

        $extra = $body->read(1);
        if ($extra !== '') {
            if ($state['expected'] !== null) {
                $this->attempts[$attempt]['mode'] = 'oversize';
            }

            return false;
        }

        return $body->eof();
    }

    // Takes a caller's stream's range staged in its temporary file (copyUnwritten) into memory as one string, once its
    // end was shown and all its bytes are there: only the range's own bytes are held then, as its reservation allows,
    // with no chunk or byte more beside them. The temporary file is closed either way.
    private function loadStaged($attempt)
    {
        $state = $this->attempts[$attempt];
        $this->attempts[$attempt]['handle'] = null;
        if ($state['mode'] === 'range' && $state['proven'] && $state['written'] === $state['expected']) {
            $bytes = stream_get_contents($state['handle'], $state['expected'], 0);
            if (is_string($bytes) && strlen($bytes) === $state['expected']) {
                $this->attempts[$attempt]['buffer'] = $bytes;
            } else {
                $this->attempts[$attempt]['mode'] = 'local';
            }
        }

        fclose($state['handle']);
    }

    // The most of a custom handler's body read at once for the attempt $state, the one chunk held at a time, within what
    // is charged for it: for a stream's range (or its first request), the bytes the range still lacks, as they go to its
    // temporary file and its reservation covers the range; for a whole response held for a stream, the chunk share of
    // its charge (holdingShares), beside the memory its holding keeps; for a staging file, which the chunk goes straight
    // to, its receive charge. Nothing once the declared range or length is complete.
    private function copyChunk(array $state)
    {
        $left = $state['expected'] === null ? null : $state['expected'] - $state['written'];
        if ($left !== null && $left <= 0) {
            return 0;
        }

        if ($this->kind === self::STREAM && $state['mode'] === 'range') {
            return min(self::RECEIVE_BUFFER_BYTES, $left);
        }

        if ($this->kind === self::STREAM) {
            list(, $chunk) = self::holdingShares($state['charge']);
        } else {
            $chunk = max(1, min(self::RECEIVE_BUFFER_BYTES, $state['charge']));
        }

        return $left === null ? $chunk : min($chunk, $left);
    }

    // An attempt that brought all of its unit. $state is contentDone's own (by reference): once a range holds the bytes
    // the attempt brought, $state no longer does, so that the range is their only holder when flush writes them.
    private function completed($unit, array &$state, $duration, $now)
    {
        $bytes = $state['written'];
        if ($state['mode'] === 'whole') {
            if ($this->kind === self::STREAM) {
                // The held response stays charged as its attempt was (the job released that charge just before, as the
                // attempt settled), until it is written to the stream or given up.
                $this->engine->retain($state['charge']);
                $this->holdingCharge = $state['charge'];
            }

            // A whole response restarted from the first byte delivers again the bytes accepted before: it accepts only
            // those beyond them.
            $fresh = max(0, $bytes - $this->received);
            $this->mode = 'whole';
            $this->total = $bytes;
            $this->received = $bytes;
            $this->probe['state'] = 'done';
            $this->whole = ['state' => 'done', 'failures' => 0, 'renewals' => 0, 'ready_at' => 0.0];
            $this->report(0, $bytes, $now);
            $this->verified = true;
            return ['accepted', $bytes, $duration, $fresh];
        }

        if ($unit === 'probe') {
            $this->bind($state);
        } else {
            $this->ranges[$unit]['state'] = 'done';
            if ($this->kind === self::STREAM) {
                $this->ranges[$unit]['data'] = $state['buffer'];
            }
        }

        $state['buffer'] = '';
        $this->received += $bytes;
        $this->report($state['start'], $bytes, $now);
        if ($this->kind === self::STREAM) {
            $this->flush();
        }

        if ($this->state === 'active' && $this->rangesComplete()) {
            // Published by prepare, once this report has been delivered.
            $this->verified = true;
        }

        return ['accepted', $bytes, $duration];
    }

    // The first range bound the download to its version: plans the rest of the file in ranges of download_part_bytes.
    private function bind(array $state)
    {
        $this->mode = 'ranged';
        $this->etag = $state['etag'];
        $this->total = $state['total'];
        $this->probe['state'] = 'done';
        $first = $state['expected'];
        // A stream's first range is held until flush writes it, charged as its attempt was (released just before).
        $held = $this->kind === self::STREAM ? strlen($state['buffer']) : 0;
        $this->engine->retain($held);
        $this->ranges = [[
            'start' => 0, 'end' => $first - 1, 'state' => 'done', 'failures' => 0, 'renewals' => 0, 'ready_at' => 0.0,
            'data' => $this->kind === self::STREAM ? $state['buffer'] : null, 'reserved' => $held,
        ]];
        for ($start = $first; $start < $this->total; $start += $this->partBytes) {
            $this->ranges[] = [
                'start' => $start, 'end' => min($this->total, $start + $this->partBytes) - 1, 'state' => 'ready', 'failures' => 0, 'renewals' => 0,
                'ready_at' => 0.0, 'data' => null, 'reserved' => 0,
            ];
        }
    }

    // Writes a caller's stream's completed ranges that come next, in order, giving up what they held. A range's bytes leave
    // the range as it is written (publish).
    private function flush()
    {
        while (isset($this->ranges[$this->nextWrite]) && $this->ranges[$this->nextWrite]['state'] === 'done') {
            $index = $this->nextWrite;
            if (!$this->publish($index)) {
                $this->engine->failTransfer($this, new InvalidParameterException('Writing the download of ' . $this->remote . ' to its stream failed'));
                return;
            }

            $this->written += $this->ranges[$index]['end'] - $this->ranges[$index]['start'] + 1;
            if ($this->ranges[$index]['reserved'] > 0) {
                $this->engine->release($this->ranges[$index]['reserved']);
            }

            $this->ranges[$index]['state'] = 'written';
            $this->ranges[$index]['reserved'] = 0;
            $this->nextWrite++;
        }
    }

    private function rangesComplete()
    {
        foreach ($this->ranges as $range) {
            if ($range['state'] !== ($this->kind === self::STREAM ? 'written' : 'done')) {
                return false;
            }
        }

        return true;
    }

    // Every byte is verified: moves the staging file into place, or writes a held whole response to the caller's stream.
    private function finish()
    {
        if ($this->kind === self::FILE) {
            clearstatcache(true, $this->staging);
            if (filesize($this->staging) !== $this->total) {
                $this->engine->failTransfer($this, new ApiBadResponseException('The download of ' . $this->remote . ' holds ' . filesize($this->staging) . ' bytes, not its ' . $this->total));
                return;
            }

            if (!@rename($this->staging, $this->destination)) {
                $this->engine->failTransfer($this, new InvalidParameterException('The download of ' . $this->remote . ' could not be moved into place at ' . $this->destination));
                return;
            }

            $this->staging = null;
        } elseif ($this->mode === 'whole') {
            if (!is_resource($this->holding)) {
                $this->engine->failTransfer($this, new InvalidParameterException('The download of ' . $this->remote . ' lost the bytes it held'));
                return;
            }

            // Written out one chunk at a time, of the share its charge keeps for it beside the memory the holding keeps,
            // each read from the holding at the exact byte the stream has taken up to (writeFrom); then given up with
            // that charge.
            list(, $chunkBytes) = self::holdingShares($this->holdingCharge);
            $reached = self::writeFrom($this->holding, $this->destination, 0, $this->total, $chunkBytes);
            $this->written += $reached;
            if ($reached !== $this->total) {
                $this->engine->failTransfer($this, new InvalidParameterException('Writing the download of ' . $this->remote . ' to its stream failed'));
                return;
            }

            $this->dropHolding();
        }

        $this->state = 'completed';
        $this->result = (object) ['received' => $this->total, 'total' => $this->total];
    }

    // Storage does not serve the file in version-bound ranges: downloads it whole from its first byte instead.
    private function restartWhole($now)
    {
        if ($this->kind === self::STREAM && $this->written > 0) {
            $this->engine->failTransfer($this, new ApiBadResponseException('Storage stopped serving ' . $this->remote . ' in byte ranges after ' . $this->written . ' bytes reached its stream, which cannot be written again'));
            return;
        }

        $this->engine->abortContent($this);
        foreach ($this->ranges as $range) {
            if ($range['reserved'] > 0) {
                $this->engine->release($range['reserved']);
            }
        }

        Logger::info('Storage does not serve ' . $this->remote . ' in version-bound ranges; downloading it whole from its first byte');
        $this->ranges = [];
        $this->nextWrite = 0;
        $this->mode = 'whole';
        $this->etag = null;
        $this->total = null;
        // The bytes accepted so far stay accepted (and the call's baseline stays where it was): the whole response
        // delivers them again, and adds only those beyond them.
        $this->probe['state'] = 'done';
        $this->whole = ['state' => 'ready', 'failures' => 0, 'renewals' => 0, 'ready_at' => (float) $now];
    }

    private function report($start, $bytes, $now)
    {
        if ($this->progress === null) {
            return;
        }

        $elapsed = max(0.0, $now - $this->callStarted);
        $this->reports[] = [$this->progress, [
            'total_bytes' => $this->total, 'received_bytes' => $this->received, 'range_start' => $start, 'range_bytes' => $bytes,
            'elapsed_seconds' => $elapsed, 'bytes_per_second' => $elapsed > 0 ? ($this->received - $this->callReceived) / $elapsed : 0.0,
        ]];
    }

    // Schedules another attempt of $unit after $delay, reporting $kind to the controller; or fails the download with
    // $error when its retries are spent.
    private function retryOrFail($unit, $kind, $delay, $error, $now)
    {
        if (!$this->retryLater($unit, $delay, $error, $now)) {
            return [ConcurrencyController::ENDED];
        }

        return $kind === ConcurrencyController::THROTTLE ? [$kind, $delay] : [$kind];
    }

    private function retryLater($unit, $delay, $error, $now)
    {
        $state = $this->unit($unit);
        $failures = $state['failures'] + 1;
        $this->setUnit($unit, 'failures', $failures);
        if ($failures > $this->engine->maxRetries()) {
            if ($failures > 1) {
                Logger::info('Retries exhausted - giving up on the download of ' . $this->remote);
            }

            $this->engine->failTransfer($this, $error);
            return false;
        }

        $this->setUnit($unit, 'ready_at', $now + $delay);
        $this->engine->countRetry();
        Logger::info('Retrying the download of ' . $this->remote . ' (retry #' . $failures . ', waiting ' . round($delay, 3) . ' s)');
        return true;
    }

    private function units()
    {
        $units = [];
        if ($this->mode === null) {
            $units['probe'] = $this->probe;
        } elseif ($this->mode === 'whole' && $this->whole !== null) {
            $units['whole'] = $this->whole;
        } else {
            foreach ($this->ranges as $index => $range) {
                $units[$index] = $range;
            }
        }

        return $units;
    }

    private function unit($unit)
    {
        if ($unit === 'probe') {
            return $this->probe;
        }

        if ($unit === 'whole') {
            return $this->whole;
        }

        return isset($this->ranges[$unit]) ? $this->ranges[$unit] : null;
    }

    private function setUnit($unit, $field, $value)
    {
        if ($unit === 'probe') {
            $this->probe[$field] = $value;
        } elseif ($unit === 'whole') {
            if ($this->whole !== null) {
                $this->whole[$field] = $value;
            }
        } elseif (isset($this->ranges[$unit])) {
            $this->ranges[$unit][$field] = $value;
        }
    }

    private function unitInFlight($unit)
    {
        $state = $this->unit($unit);
        return $state !== null && $state['state'] === 'inflight';
    }

    private function closeAttempt(array $state)
    {
        if (is_resource($state['handle']) && $state['handle'] !== $this->holding) {
            fclose($state['handle']);
        }
    }

    private function cleanUp()
    {
        foreach ($this->attempts as $state) {
            $this->closeAttempt($state);
        }

        $this->attempts = [];
        foreach ($this->ranges as $index => $range) {
            if ($range['reserved'] > 0) {
                $this->engine->release($range['reserved']);
            }

            $this->ranges[$index]['reserved'] = 0;
            $this->ranges[$index]['data'] = null;
        }

        $this->dropHolding();
        $this->verified = false;
        if ($this->staging !== null && is_file($this->staging)) {
            @unlink($this->staging);
        }

        $this->staging = null;
    }

    // Gives up a held whole response: closes its temporary stream (memory and spilled file alike) and releases the
    // charge it kept, if it was still kept past its attempt.
    private function dropHolding()
    {
        if (is_resource($this->holding)) {
            fclose($this->holding);
        }

        $this->holding = null;
        if ($this->holdingCharge > 0) {
            $this->engine->release($this->holdingCharge);
            $this->holdingCharge = 0;
        }
    }

    // How a whole response held for a caller's stream shares the $charge reserved for it: [the most of it the temporary
    // stream keeps in memory before it spills to a temporary file, the most copied out of it (or into it, from a custom
    // handler's body) at once]. The chunk is at most 64 KiB and half the charge, and at least a byte; memory gets the
    // rest, nothing for a charge of one byte, whose every byte is then on disk. The memory held and the one chunk copied
    // at a time never exceed the charge together, from the response's first byte, through its final reports and a
    // pause, to its publication or its giving up: a chunk is given up before the next is read, what a short write left is
    // read again from the holding rather than copied (writeFrom), and the byte asked for past a custom handler's body is
    // read with no chunk held (bodyEnded). Bytes beyond the memory share are on disk.
    private static function holdingShares($charge)
    {
        $chunk = max(1, min(self::RECEIVE_BUFFER_BYTES, (int) floor($charge / 2)));
        return [max(0, $charge - $chunk), $chunk];
    }

    // Writes all of $data to $handle, a file the download owns (its staging file, a held whole response or a staged
    // range), copying nothing: a write that leaves any of it unwritten fails as a local write failure, as one that writes
    // nothing does. A caller's stream is written by publish and writeFrom instead, which go on from where a short write
    // stopped.
    private static function writeAll($handle, $data)
    {
        $count = fwrite($handle, $data);
        return $count !== false && $count === strlen($data);
    }

    // Writes the bytes of the range $index, a range for a caller's stream, to the stream, every byte once and in order,
    // taking them from the range: $data, a plain variable of this call, is then their only holder, and is given up before
    // anything else is read. They are not cast nor passed by reference, either of which PHP 5 would copy them for. A
    // write the stream takes only part of is continued from the exact byte where it stopped; one it takes nothing of (or
    // that fails) fails. The rest is not copied out of $data for that: $data is written whole to a temporary file and
    // given up, so that no copy of the range is left in memory, and the rest is written from there (writeFrom) one chunk
    // of at most the range's own size at a time, within the reservation the range had. True when every byte was written.
    private function publish($index)
    {
        $data = $this->ranges[$index]['data'];
        $this->ranges[$index]['data'] = null;
        if (!is_string($data)) {
            // A range is given its bytes as it completes (completed, bind): one without them has nothing to write.
            return false;
        }

        $length = strlen($data);
        $count = fwrite($this->destination, $data);
        if ($count === false || $count === 0 || $count >= $length) {
            $data = null;
            return $count === $length;
        }

        $stage = @tmpfile();
        $staged = $stage !== false && fwrite($stage, $data) === $length;
        $data = null;
        $done = $staged && self::writeFrom($stage, $this->destination, $count, $length, min(self::RECEIVE_BUFFER_BYTES, $length)) === $length;
        if ($stage !== false) {
            fclose($stage);
        }

        return $done;
    }

    // Writes bytes $offset to $length of $source, a stream the download owns (a held whole response, or a range publish
    // staged), to $destination: one chunk of at most $chunkBytes at a time, read from the exact byte the stream has taken
    // up to and given up before the next is read, so that a write the stream takes only part of goes on from there with
    // nothing copied. A write it takes nothing of, or a read or write that fails, ends it. The offset reached: $length
    // when every byte was written.
    private static function writeFrom($source, $destination, $offset, $length, $chunkBytes)
    {
        while ($offset < $length) {
            $chunk = fseek($source, $offset) === 0 ? fread($source, min($chunkBytes, $length - $offset)) : false;
            $count = is_string($chunk) && $chunk !== '' ? fwrite($destination, $chunk) : false;
            $chunk = null;
            if ($count === false || $count === 0) {
                break;
            }

            $offset += $count;
        }

        return $offset;
    }

    private static function contentLength($response)
    {
        $values = $response->getHeader('Content-Length');
        if (count($values) !== 1 || !preg_match('/\A[0-9]{1,18}\z/', trim($values[0]))) {
            return null;
        }

        return (int) trim($values[0]);
    }

    // Content-Range bytes start-end/total, as three integers, or null.
    private static function contentRange($response)
    {
        $values = $response->getHeader('Content-Range');
        if (count($values) !== 1 || !preg_match('/\Abytes ([0-9]{1,18})-([0-9]{1,18})\/([0-9]{1,18})\z/', trim($values[0]), $match)) {
            return null;
        }

        $range = [(int) $match[1], (int) $match[2], (int) $match[3]];
        return $range[0] <= $range[1] && $range[1] < $range[2] ? $range : null;
    }

    private static function strongEtag($response)
    {
        $values = $response->getHeader('ETag');
        if (count($values) !== 1) {
            return null;
        }

        $etag = trim($values[0]);
        return $etag === '' || stripos($etag, 'W/') === 0 ? null : $etag;
    }
}
