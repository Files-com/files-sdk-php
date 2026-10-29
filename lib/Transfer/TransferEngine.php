<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Api;
use Files\Exception\ConfigurationException;
use Files\Exception\InvalidParameterException;
use Files\Logger;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Psr7\FnStream;

/**
 * The single-threaded loop a TransferJob runs its transfers in. Each request is sent asynchronously through one curl
 * multi handle, which this loop drives (tick) between its own steps: settling what finished, delivering callbacks,
 * admitting transfers, letting them prepare (reading a caller's stream), and sending what the budgets allow. The multi
 * handle is the job's own, unless Files::setHandler gave a GuzzleHttp\Handler\CurlMultiHandler, which the job then
 * drives without closing it. Any other handler set with Files::setHandler is used as it is, one request at a time (the
 * serial transport): it cannot overlap requests, so the job refuses explicit concurrency or Aggressive startup with it,
 * and statistics report its transport as serial.
 *
 * Every request gets its own Guzzle handler stack (Api::requestStack), so redirect credential stripping stays with the
 * redirect chain that started it, while connections are pooled in the multi handle. API requests use the configuration
 * the job copied when it was made (Files::snapshot, with the job's and each transfer's api_key, session_id and
 * workspace_id), never the configuration as it is later.
 *
 * Budgets, for the whole job: max_concurrent_requests requests in flight (API requests, content attempts, their retries
 * and URL renewals alike; at most four of them API requests), max_concurrent_files transfers started at once (paced as
 * the reference's directory uploads are; Aggressive startup starts every upload at once, never a download), and
 * max_buffer_bytes of reserved content bytes. A request's permit is held for one attempt only. Bytes are reserved before
 * they are read or received: a caller's stream's part and an ordered download range from before they are read until
 * they are given up (through retries, renewals and pauses), each attempt's send or receive buffer while it runs, and
 * what an attempt leaves held when it settles (a whole response kept for a caller's stream) from its own reservation,
 * until that is written or given up (retain). A reservation that does not fit holds back other new reservations until it
 * does, but never what already holds its bytes, so a due retry cannot wait behind new work.
 *
 * API requests take the job's captured connection and read timeouts unless their own options give others, and a read
 * timeout ends a connected request that stops answering (a stall limit), as it ends content requests. Such a request
 * reached its server, so when it begins or ends an upload it is not sent again (RetryPolicy::isConnectFailure).
 *
 * The first failure, or the first exception a callback throws, stops the job: no new request is admitted, those in
 * flight are cancelled and settled, no upload is finalized, and wait throws that exception as it was. cancel stops the
 * job the same way and throws a TransferCancelledException unless something failed first. pause stops admitting work,
 * cancels content requests in flight (their transfers keep their completed work, and a caller's stream's part keeps
 * its bytes and reservation), lets API requests in flight finish, and returns from wait; resume and wait continue.
 */
final class TransferEngine
{
    const API_LIMIT = 4;
    const INITIAL_FILE_TARGET = 4;
    const FILE_PACING_SECONDS = 0.1;
    const SELECT_TIMEOUT = 0.05;
    const MAX_SLEEP_SECONDS = 0.25;

    private $clock;
    private $config;
    private $options;
    private $handler;
    private $concurrent;
    private $transfers = [];
    private $requests = [];
    private $events = [];
    private $delayedApi = [];
    private $controllers = [];
    private $diagnostics = [];
    private $reserved = 0;
    private $peakReserved = 0;
    private $byteWaiter = null;
    private $inFlight = 0;
    private $apiInFlight = 0;
    private $contentInFlight = 0;
    private $peakInFlight = 0;
    private $peakApiInFlight = 0;
    private $peakContentInFlight = 0;
    private $attempts = 0;
    private $retries = 0;
    private $renewals = 0;
    private $acceptedBytes = 0;
    private $nextRequest = 0;
    private $rotation = 0;
    private $preparation;
    private $peakActiveFiles = 0;
    private $stopping = null;
    private $error = null;
    private $running = false;
    private $paused = false;
    private $closed = false;

    // $config is Files::snapshot() with the job's own API settings; $options the job's validated options.
    public function __construct(MonotonicClock $clock, array $config, array $options)
    {
        $this->clock = $clock;
        $this->config = $config;
        $this->options = $options;
        $handler = $config['handler'];
        if ($handler === null) {
            $this->handler = new CurlMultiHandler(['select_timeout' => self::SELECT_TIMEOUT]);
            $this->concurrent = true;
        } elseif ($handler instanceof CurlMultiHandler) {
            $this->handler = $handler;
            $this->concurrent = true;
        } else {
            $concurrency = $options['concurrency'] !== null && $options['concurrency'] > 1;
            $initial = $options['initial_target'] !== null && $options['initial_target'] > 1;
            if ($concurrency || $initial || $options['startup'] === 'aggressive') {
                throw new InvalidParameterException('The handler set with Files::setHandler is not a GuzzleHttp\\Handler\\CurlMultiHandler, so a transfer job sends one request at a time through it; it cannot take concurrency, initial_target or Aggressive startup');
            }

            $this->handler = $handler;
            $this->concurrent = false;
        }

        $this->preparation = self::preparationMemory($options['max_concurrent_files']);
    }

    // Takes a transfer (UploadTransfer or DownloadTransfer) into the job; it starts when the job is waited on.
    public function add($transfer)
    {
        if ($this->closed) {
            throw new InvalidParameterException('This TransferJob has stopped (it was cancelled or something failed); start a new one');
        }

        $id = count($this->transfers);
        $transfer->setEngine($this, $id);
        $this->transfers[$id] = $transfer;
        return new TransferHandle($transfer);
    }

    // Runs the job until every transfer has ended or paused, or the job stops; throws what stopped it.
    public function run()
    {
        if ($this->running) {
            throw new InvalidParameterException('TransferJob::wait cannot be called from inside one of its own callbacks');
        }

        if ($this->closed) {
            if ($this->error !== null) {
                throw $this->error;
            }

            return;
        }

        if ($this->paused) {
            return;
        }

        $this->running = true;
        try {
            $this->loop();
        } catch (\Exception $e) {
            $this->abandon($e);
        } catch (\Throwable $e) {
            $this->abandon($e);
        } finally {
            $this->running = false;
        }

        if ($this->error !== null) {
            throw $this->error;
        }
    }

    public function pause()
    {
        if ($this->closed || $this->paused) {
            return;
        }

        if ($this->running) {
            $this->stop('pause');
            return;
        }

        foreach ($this->transfers as $transfer) {
            if ($transfer->state() === 'active') {
                $transfer->pause();
            }
        }

        $this->paused = true;
    }

    public function resume()
    {
        if ($this->closed || !$this->paused || $this->running) {
            return;
        }

        $this->paused = false;
        $now = $this->clock->now();
        foreach ($this->transfers as $transfer) {
            if ($transfer->state() === 'paused') {
                $transfer->resume($now);
            }
        }
    }

    public function cancel()
    {
        if ($this->closed) {
            return;
        }

        $this->stop('cancel');
        if (!$this->running) {
            $this->settleStop();
        }
    }

    public function isPaused()
    {
        return $this->paused;
    }

    public function statistics()
    {
        $controllers = [];
        foreach ($this->controllers as $key => $controller) {
            $controllers[$key] = $controller->snapshot();
        }

        $active = 0;
        foreach ($this->transfers as $transfer) {
            $active += $transfer->state() === 'active' ? 1 : 0;
        }

        return [
            'transport' => $this->concurrent ? 'concurrent' : 'serial', 'clock' => $this->clock->provided(),
            'max_concurrent_requests' => $this->requestLimit(), 'max_concurrent_files' => $this->options['max_concurrent_files'],
            'max_buffer_bytes' => $this->options['max_buffer_bytes'], 'in_flight' => $this->inFlight, 'peak_in_flight' => $this->peakInFlight,
            'peak_api_in_flight' => $this->peakApiInFlight, 'peak_content_in_flight' => $this->peakContentInFlight, 'attempts' => $this->attempts,
            'retries' => $this->retries, 'renewals' => $this->renewals, 'accepted_bytes' => $this->acceptedBytes, 'reserved_bytes' => $this->reserved,
            'peak_reserved_bytes' => $this->peakReserved, 'active_files' => $active, 'peak_active_files' => $this->peakActiveFiles,
            'controllers' => $controllers,
        ];
    }

    // For transfers.

    public function now()
    {
        return $this->clock->now();
    }

    public function config()
    {
        return $this->config;
    }

    public function maxRetries()
    {
        return $this->options['max_retries'];
    }

    public function downloadPartBytes()
    {
        return $this->options['download_part_bytes'];
    }

    public function bufferLimit()
    {
        return $this->options['max_buffer_bytes'];
    }

    public function countRetry()
    {
        $this->retries++;
    }

    public function countRenewal()
    {
        $this->renewals++;
    }

    // Reserves $bytes of the job's buffer for transfer $owner; false when they do not fit now.
    public function reserve($bytes, $owner)
    {
        $bytes = (int) $bytes;
        if ($bytes <= 0) {
            return true;
        }

        $headroom = $this->options['max_buffer_bytes'] - $this->reserved;
        $waiter = $this->byteWaiter;
        if ($waiter !== null && $waiter['owner'] !== $owner) {
            $headroom -= $waiter['bytes'];
        }

        if ($bytes > $headroom) {
            if ($waiter === null) {
                $this->byteWaiter = ['owner' => $owner, 'bytes' => $bytes];
            }

            return false;
        }

        if ($waiter !== null && $waiter['owner'] === $owner) {
            $this->byteWaiter = null;
        }

        $this->reserved += $bytes;
        $this->peakReserved = max($this->peakReserved, $this->reserved);
        return true;
    }

    public function release($bytes)
    {
        $this->reserved = max(0, $this->reserved - (int) $bytes);
    }

    // Charges again $bytes a transfer goes on holding as its attempt settles: never more than that attempt's own
    // reservation, which the job released just before (processEvents) with nothing reserved in between. The bytes stay
    // owned throughout, so this always fits and waits behind nothing. The transfer releases them when it gives them up.
    public function retain($bytes)
    {
        $bytes = (int) $bytes;
        if ($bytes > 0) {
            $this->reserved += $bytes;
            $this->peakReserved = max($this->peakReserved, $this->reserved);
        }
    }

    // The controller, and its key, of an upload to a destination of $classification (s3 or direct) at $host.
    public function uploadController($classification, $host)
    {
        $cap = $this->requestLimit();
        if (!$this->concurrent) {
            return $this->controller('serial', ConcurrencyController::fixed(1));
        }

        if ($this->options['concurrency'] !== null) {
            return $this->controller('upload:fixed:' . $host, ConcurrencyController::fixed(min($cap, $this->options['concurrency'])));
        }

        if ($classification === 'direct') {
            return $this->controller('upload:direct:' . $host, ConcurrencyController::generic($cap));
        }

        if ($this->options['initial_target'] !== null) {
            return $this->controller('upload:explicit:' . $host, ConcurrencyController::explicit($cap, $this->options['initial_target']));
        }

        $startup = $this->options['startup'];
        return $this->controller('upload:s3:' . $startup . ':' . $host, ConcurrencyController::storageUpload($cap, $startup));
    }

    // The controller, and its key, of downloads from $host. Upload startup does not apply.
    public function downloadController($host)
    {
        $cap = $this->requestLimit();
        if (!$this->concurrent) {
            return $this->controller('serial', ConcurrencyController::fixed(1));
        }

        if ($this->options['concurrency'] !== null) {
            return $this->controller('download:fixed:' . $host, ConcurrencyController::fixed(min($cap, $this->options['concurrency'])));
        }

        return $this->controller('download:' . $host, ConcurrencyController::download($cap));
    }

    // Fails $transfer with $error, which stops the job.
    public function failTransfer($transfer, $error)
    {
        $state = $transfer->state();
        if ($state !== 'completed' && $state !== 'failed' && $state !== 'cancelled') {
            $transfer->fail($error);
        }

        $this->stop('error', $error);
    }

    // Cancels the content requests $transfer has in flight; each settles as aborted.
    public function abortContent($transfer)
    {
        foreach ($this->requests as $id => $record) {
            if ($record['kind'] === 'content' && $record['item']['transfer'] === $transfer && !$record['aborted']) {
                $this->requests[$id]['aborted'] = true;
                if ($record['promise'] !== null) {
                    $record['promise']->cancel();
                }
            }
        }
    }

    // A stream of $bytes, which an attempt sends without copying them again.
    public static function memoryStream($bytes)
    {
        $bytes = (string) $bytes;
        $length = strlen($bytes);
        $position = 0;
        return new FnStream([
            '__toString' => function () use (&$bytes) {
                return $bytes;
            },
            'close' => function () {
            },
            'detach' => function () {
                return null;
            },
            'getSize' => function () use ($length) {
                return $length;
            },
            'tell' => function () use (&$position) {
                return $position;
            },
            'eof' => function () use (&$position, $length) {
                return $position >= $length;
            },
            'isSeekable' => function () {
                return true;
            },
            'seek' => function ($offset, $whence = SEEK_SET) use (&$position, $length) {
                if ($whence === SEEK_CUR) {
                    $offset += $position;
                } elseif ($whence === SEEK_END) {
                    $offset += $length;
                }

                if ($offset < 0 || $offset > $length) {
                    throw new \RuntimeException('Cannot seek to byte ' . $offset . ' of ' . $length);
                }

                $position = $offset;
            },
            'rewind' => function () use (&$position) {
                $position = 0;
            },
            'isWritable' => function () {
                return false;
            },
            'write' => function () {
                throw new \RuntimeException('An upload part\'s body cannot be written');
            },
            'isReadable' => function () {
                return true;
            },
            'read' => function ($count) use (&$bytes, &$position, $length) {
                $chunk = (string) substr($bytes, $position, max(0, (int) $count));
                $position += strlen($chunk);
                return $chunk;
            },
            'getContents' => function () use (&$bytes, &$position, $length) {
                $chunk = (string) substr($bytes, $position);
                $position = $length;
                return $chunk;
            },
            'getMetadata' => function ($key = null) {
                return $key === null ? [] : null;
            },
        ]);
    }

    private function loop()
    {
        while (true) {
            $this->processEvents();
            $this->deliver();
            if ($this->stopping !== null) {
                $this->abortRequests();
                if ($this->requests) {
                    $this->drive();
                    continue;
                }

                $this->settleStop();
                return;
            }

            $now = $this->clock->now();
            $this->openTransfers($now);
            $this->prepareTransfers($now);
            $this->dispatch($now);
            if ($this->events || $this->stopping !== null) {
                continue;
            }

            if (!$this->requests) {
                if ($this->allSettled()) {
                    return;
                }

                $wake = $this->nextWake($now);
                if ($wake === null) {
                    $this->stop('error', new InvalidParameterException('The transfer job cannot continue: no transfer has work it can start within the job\'s limits'));
                    continue;
                }

                usleep((int) ceil(max(0.001, min(self::MAX_SLEEP_SECONDS, $wake - $now)) * 1000000));
                continue;
            }

            $this->drive();
        }
    }

    // After an exception the loop itself did not expect: cancels what is in flight, without waiting, and stops the job.
    private function abandon($error)
    {
        if ($this->error === null) {
            $this->error = $error;
        }

        foreach ($this->requests as $record) {
            if ($record['promise'] !== null) {
                try {
                    $record['promise']->cancel();
                } catch (\Exception $e) {
                    // The request is abandoned either way.
                }
            }

            if ($record['kind'] === 'content') {
                $this->release($record['bytes']);
            }
        }

        $this->requests = [];
        $this->events = [];
        $this->inFlight = $this->apiInFlight = $this->contentInFlight = 0;
        $this->stopping = 'error';
        $this->settleStop();
    }

    private function drive()
    {
        if ($this->concurrent) {
            $this->handler->tick();
        }

        self::runQueue();
    }

    private static function runQueue()
    {
        if (class_exists('GuzzleHttp\\Promise\\Utils') && method_exists('GuzzleHttp\\Promise\\Utils', 'queue')) {
            \GuzzleHttp\Promise\Utils::queue()->run();
        } else {
            \GuzzleHttp\Promise\queue()->run();
        }
    }

    private function requestLimit()
    {
        return $this->concurrent ? $this->options['max_concurrent_requests'] : 1;
    }

    private function stop($mode, $error = null)
    {
        if ($mode === 'error') {
            if ($this->error === null) {
                $this->error = $error;
            }

            $this->stopping = 'error';
        } elseif ($mode === 'cancel') {
            if ($this->stopping !== 'error') {
                $this->stopping = 'cancel';
            }
        } elseif ($this->stopping === null) {
            $this->stopping = 'pause';
        }
    }

    // Once nothing is in flight: pauses the active transfers, or ends every transfer that has not ended.
    private function settleStop()
    {
        $mode = $this->stopping;
        $this->stopping = null;
        $this->byteWaiter = null;
        if ($mode === 'pause') {
            foreach ($this->transfers as $transfer) {
                if ($transfer->state() === 'active') {
                    $transfer->pause();
                }
            }

            $this->paused = true;
            return;
        }

        $now = $this->clock->now();
        foreach ($this->delayedApi as $entry) {
            $entry[0]['transfer']->apiAborted($entry[0], $now);
        }

        $this->delayedApi = [];
        foreach ($this->transfers as $transfer) {
            $state = $transfer->state();
            if ($state === 'queued' || $state === 'active' || $state === 'paused') {
                $transfer->cancel();
            }
        }

        if ($this->error === null) {
            $this->error = new TransferCancelledException('The transfer job was cancelled before every transfer ended');
        }

        $this->paused = false;
        $this->closed = true;
    }

    private function abortRequests()
    {
        foreach ($this->requests as $id => $record) {
            if ($record['aborted'] || ($this->stopping === 'pause' && $record['kind'] === 'api')) {
                continue;
            }

            $this->requests[$id]['aborted'] = true;
            if ($record['promise'] !== null) {
                $record['promise']->cancel();
            }
        }

        self::runQueue();
    }

    private function allSettled()
    {
        if ($this->requests || $this->delayedApi) {
            return false;
        }

        foreach ($this->transfers as $transfer) {
            $state = $transfer->state();
            if ($state === 'queued' || $state === 'active') {
                return false;
            }
        }

        return true;
    }

    private function nextWake($now)
    {
        $times = [];
        foreach ($this->delayedApi as $entry) {
            $times[] = $entry[1];
        }

        $active = false;
        $queued = false;
        foreach ($this->transfers as $transfer) {
            $state = $transfer->state();
            if ($state === 'active') {
                $active = true;
                $wake = $transfer->wakeAt($now);
                if ($wake !== null) {
                    $times[] = $wake;
                }
            } elseif ($state === 'queued') {
                $queued = true;
            }
        }

        foreach ($this->controllers as $controller) {
            if ($controller->cooldownUntil() > $now) {
                $times[] = $controller->cooldownUntil();
            }
        }

        // Queued transfers wait for the paced file limit's next step only while a transfer is active. With none
        // active, the limit (at least one) has room now: the last active transfers may have ended after openTransfers
        // ran, as a download that prepareTransfers publishes does.
        if ($queued) {
            $times[] = $active ? max($now, $this->preparation['changed_at']) + self::FILE_PACING_SECONDS : $now;
        }

        return $times ? min($times) : null;
    }

    private function processEvents()
    {
        while ($this->events) {
            list($id, $ok, $value, $ended) = array_shift($this->events);
            if (!isset($this->requests[$id])) {
                continue;
            }

            $record = $this->requests[$id];
            unset($this->requests[$id]);
            $this->inFlight--;
            $now = $this->clock->now();
            try {
                if ($record['kind'] === 'api') {
                    $this->apiInFlight--;
                    $this->apiSettled($record, $ok, $value, $now);
                } else {
                    $this->contentInFlight--;
                    $this->release($record['bytes']);
                    $this->contentSettled($record, $ok, $value, $ended, $now);
                }
            } catch (\Exception $e) {
                $this->failTransfer($record['item']['transfer'], $e);
            } catch (\Throwable $e) {
                $this->failTransfer($record['item']['transfer'], $e);
            }
        }
    }

    private function apiSettled(array $record, $ok, $value, $now)
    {
        $item = $record['item'];
        $transfer = $item['transfer'];
        if ($ok) {
            $body = (string) $value->getBody();
            $transfer->apiDone($item, $body === '' ? null : json_decode($body, true), $now);
            return;
        }

        if ($record['aborted']) {
            $transfer->apiAborted($item, $now);
            return;
        }

        $error = $value;
        $status = null;
        if ($error instanceof \GuzzleHttp\Exception\RequestException && $error->hasResponse()) {
            $status = $error->getResponse()->getStatusCode();
        }

        $attempt = $item['attempt'];
        $retry = false;
        $delay = 0.0;
        if ($attempt < $this->options['max_retries'] && $this->stopping === null && $transfer->state() === 'active') {
            if ($item['retry'] === 'idempotent') {
                $retry = $status !== null ? RetryPolicy::apiStatusRetryable($status) : RetryPolicy::networkRetryable($error);
                $delay = RetryPolicy::apiDelay($attempt + 1);
            } else {
                // Only a request shown never to have reached a server: by curl, and by no hop of its redirect chain
                // having been answered (startRequest).
                $retry = $status === null && RetryPolicy::isConnectFailure($error, $record['answered']);
                $delay = RetryPolicy::connectDelay($attempt + 1, $this->config['min_network_retry_delay'], $this->config['max_network_retry_delay']);
            }
        }

        if ($retry) {
            $item['attempt'] = $attempt + 1;
            $this->retries++;
            Logger::info('Retrying ' . $item['describe'] . ' (retry #' . $item['attempt'] . ', waiting ' . round($delay, 3) . ' s)');
            $this->delayedApi[] = [$item, $now + $delay];
            return;
        }

        if ($attempt > 0) {
            Logger::info('Retries exhausted - giving up on ' . $item['describe']);
        }

        $this->failTransfer($transfer, self::apiError($error));
    }

    // The SDK's exception for a failed API request, as the generated methods throw it.
    private static function apiError($error)
    {
        if (!($error instanceof \Exception)) {
            return new \Files\Exception\ApiRequestException('The API request failed: ' . (is_object($error) ? get_class($error) : (string) $error));
        }

        try {
            \Files\handleErrorResponse($error);
        } catch (\Exception $mapped) {
            return $mapped;
        }

        return $error;
    }

    private function contentSettled(array $record, $ok, $value, $ended, $now)
    {
        $item = $record['item'];
        $transfer = $item['transfer'];
        $this->attempts++;
        $body = $record['body'];
        if (is_object($body) && method_exists($body, 'close')) {
            try {
                $body->close();
            } catch (\Exception $e) {
                // The attempt is over; its body is no longer read.
            }
        }

        $signal = [ConcurrencyController::ENDED];
        try {
            if (!$ok && $record['aborted']) {
                $transfer->contentAborted($item, $now);
            } else {
                $timing = ['started' => $record['started'], 'headers_at' => $record['headers_at'], 'ended' => $ended];
                $signal = $transfer->contentDone($item, $ok, $value, $timing, $now);
            }
        } finally {
            $this->signal($item['controller'], $record['token'], $signal, $now);
        }
    }

    // $signal is how an attempt ended (contentDone). An accepted one gives the bytes its attempt delivered, which the
    // controller measures, and its duration; and, when they differ, the bytes it newly accepted for its transfer, which
    // the job counts: a whole response restarted from the first byte delivers again bytes already accepted.
    private function signal($controller, array $token, array $signal, $now)
    {
        if ($signal[0] === 'accepted') {
            $controller->accepted($token, $signal[1], $signal[2], $now);
            $this->acceptedBytes += isset($signal[3]) ? $signal[3] : $signal[1];
        } elseif ($signal[0] === ConcurrencyController::FAILURE) {
            $controller->failed($token, ConcurrencyController::FAILURE, 0.0, $now);
        } elseif ($signal[0] === ConcurrencyController::THROTTLE) {
            $controller->failed($token, ConcurrencyController::THROTTLE, $signal[1], $now);
        } else {
            $controller->failed($token, ConcurrencyController::ENDED, 0.0, $now);
        }

        foreach ($controller->takeDecisions() as $decision) {
            $this->diagnostics[] = $decision;
        }
    }

    // Progress reports and diagnostics, in order, while the job is running or pausing; none after it failed or was
    // cancelled. The first exception a callback throws stops the job.
    private function deliver()
    {
        foreach ($this->transfers as $transfer) {
            foreach ($transfer->takeReports() as $report) {
                if ($this->stopping === 'error' || $this->stopping === 'cancel') {
                    continue;
                }

                try {
                    call_user_func($report[0], $report[1]);
                } catch (\Exception $e) {
                    $this->stop('error', $e);
                } catch (\Throwable $e) {
                    $this->stop('error', $e);
                }
            }
        }

        $decisions = $this->diagnostics;
        $this->diagnostics = [];
        $callback = $this->options['diagnostics'];
        foreach ($decisions as $decision) {
            if ($callback === null || $this->stopping === 'error' || $this->stopping === 'cancel') {
                continue;
            }

            try {
                call_user_func($callback, $decision);
            } catch (\Exception $e) {
                $this->stop('error', $e);
            } catch (\Throwable $e) {
                $this->stop('error', $e);
            }
        }
    }

    private function openTransfers($now)
    {
        $active = 0;
        $ready = 0;
        $queued = [];
        foreach ($this->transfers as $id => $transfer) {
            $state = $transfer->state();
            if ($state === 'active') {
                $active++;
                $ready += $transfer->readyCount($now);
            } elseif ($state === 'queued') {
                $queued[$id] = $transfer->direction();
            }
        }

        list($start, $this->preparation) = self::startable($this->options, $this->preparation, $this->controllers, $active, $ready, $queued, $now);
        foreach ($start as $id) {
            if ($this->stopping !== null) {
                break;
            }

            try {
                $this->transfers[$id]->open($now);
                $active++;
            } catch (\Exception $e) {
                $this->failTransfer($this->transfers[$id], $e);
            }
        }

        $this->peakActiveFiles = max($this->peakActiveFiles, $active);
    }

    private function prepareTransfers($now)
    {
        // A reservation that waited is asked for again below while it is still wanted.
        $this->byteWaiter = null;
        foreach ($this->transfers as $transfer) {
            if ($this->stopping !== null) {
                return;
            }

            if ($transfer->state() !== 'active') {
                continue;
            }

            try {
                $transfer->prepare($now);
            } catch (\Exception $e) {
                $this->failTransfer($transfer, $e);
            }
        }
    }

    private function dispatch($now)
    {
        $limit = $this->requestLimit();
        $apiLimit = min(self::API_LIMIT, $limit);
        foreach ($this->delayedApi as $index => $entry) {
            if ($this->inFlight >= $limit || $this->apiInFlight >= $apiLimit) {
                break;
            }

            if ($entry[1] > $now) {
                continue;
            }

            unset($this->delayedApi[$index]);
            if ($entry[0]['transfer']->state() === 'active') {
                $this->sendApi($entry[0], $now);
            } else {
                $entry[0]['transfer']->apiAborted($entry[0], $now);
            }
        }

        $active = [];
        foreach ($this->transfers as $transfer) {
            if ($transfer->state() === 'active') {
                $active[] = $transfer;
            }
        }

        $blocked = [];
        $count = count($active);
        $offset = $count > 0 ? $this->rotation++ % $count : 0;
        for ($index = 0; $index < $count; $index++) {
            $transfer = $active[($offset + $index) % $count];
            while ($this->stopping === null && $transfer->state() === 'active' && $this->inFlight < $limit && $this->apiInFlight < $apiLimit) {
                $item = $this->ask($transfer, 'nextApi', $now);
                if ($item === null) {
                    break;
                }

                $this->sendApi($item, $now);
            }
        }

        $progress = $count > 0;
        while ($progress && $this->stopping === null) {
            $progress = false;
            for ($index = 0; $index < $count; $index++) {
                $transfer = $active[($offset + $index) % $count];
                if ($transfer->state() !== 'active') {
                    continue;
                }

                $item = $this->ask($transfer, 'nextContent', $now);
                if ($item === null) {
                    $key = $transfer->controllerKey();
                    if ($key !== null && !isset($blocked[$key]) && $transfer->serialBlocked()) {
                        $blocked[$key] = 'serial_parts';
                    }

                    continue;
                }

                $key = $item['controller_key'];
                if (!$item['controller']->admits($now)) {
                    continue;
                }

                if ($this->inFlight >= $limit || ($item['bytes'] > 0 && !$this->reserve($item['bytes'], $transfer->id()))) {
                    $blocked[$key] = 'shared_limiter';
                    continue;
                }

                if ($this->sendContent($item, $now)) {
                    $progress = true;
                }
            }
        }

        foreach ($this->controllers as $key => $controller) {
            if ($controller->inFlight() >= $controller->target()) {
                $reason = null;
            } elseif (isset($blocked[$key])) {
                $reason = $blocked[$key];
            } elseif ($now < $controller->cooldownUntil()) {
                $reason = 'cooldown';
            } else {
                $reason = 'no_part_ready';
            }

            $controller->shortfall($reason, $now);
        }
    }

    // $transfer->$method($now), failing the transfer on an exception.
    private function ask($transfer, $method, $now)
    {
        try {
            return $transfer->$method($now);
        } catch (\Exception $e) {
            $this->failTransfer($transfer, $e);
            return null;
        }
    }

    private function sendApi(array $item, $now)
    {
        try {
            list($url, $options) = $this->apiRequest($item);
        } catch (\Exception $e) {
            $this->failTransfer($item['transfer'], $e);
            return;
        }

        Logger::debug('Transfer API request: ' . $item['describe']);
        $this->inFlight++;
        $this->apiInFlight++;
        $this->peakInFlight = max($this->peakInFlight, $this->inFlight);
        $this->peakApiInFlight = max($this->peakApiInFlight, $this->apiInFlight);
        $this->startRequest($item, 'api', $item['method'], $url, $options, null, 0, $now);
    }

    // The URL and Guzzle options of an API request, built from the job's copied configuration as Api::sendRequest builds
    // them from the current one.
    private function apiRequest(array $item)
    {
        $config = $this->config;
        if (!$config['base_url']) {
            throw new ConfigurationException('Site URL has not been set. Set your site URL using Files::setBaseUrl(<BASE-URL>).');
        }

        list($path, $options) = Api::prepareApiRequest($config, $item['path'], $item['method'], $item['params'], $item['options']);
        unset($options['api_key'], $options['session_id'], $options['workspace_id']);
        $url = preg_match('@^[a-zA-Z]+://@', $path) ? $path : $config['base_url'] . $config['endpoint_prefix'] . $path;
        // The copied connection and read timeouts, as Api::sendRequest gives them, unless the request's own options give
        // others; and the read timeout as a stall limit, which ends a connected request that stops answering, so that
        // no API request holds its permit (or a pause) for ever. Curl options of the request's own come first.
        $options += ['connect_timeout' => $config['connect_timeout'], 'read_timeout' => $config['read_timeout']];
        $own = isset($options['curl']) && is_array($options['curl']) ? $options['curl'] : [];
        $options['curl'] = $own + self::stallLimit($options['read_timeout']);
        return [$url, $options];
    }

    // The curl options that end a connected request when fewer than one byte a second has moved for $seconds (at
    // least one): what read_timeout means to the job's requests.
    private static function stallLimit($seconds)
    {
        if (!defined('CURLOPT_LOW_SPEED_LIMIT') || !defined('CURLOPT_LOW_SPEED_TIME')) {
            return [];
        }

        return [CURLOPT_LOW_SPEED_LIMIT => 1, CURLOPT_LOW_SPEED_TIME => max(1, (int) ceil((float) $seconds))];
    }

    private function sendContent(array $item, $now)
    {
        try {
            $request = $item['transfer']->begin($item, $now);
        } catch (\Exception $e) {
            $this->release($item['bytes']);
            $this->failTransfer($item['transfer'], $e);
            return false;
        }

        $item['request'] = $request;
        $options = $request['options'] + [
            'connect_timeout' => $this->config['connect_timeout'],
            'curl' => self::stallLimit($this->config['read_timeout']),
        ];
        $token = $item['controller']->started($now);
        $this->inFlight++;
        $this->contentInFlight++;
        $this->peakInFlight = max($this->peakInFlight, $this->inFlight);
        $this->peakContentInFlight = max($this->peakContentInFlight, $this->contentInFlight);
        $this->startRequest($item, 'content', $request['method'], $request['url'], $options, $token, $item['bytes'], $now);
        return true;
    }

    private function startRequest(array $item, $kind, $method, $url, array $options, $token, $bytes, $now)
    {
        $id = ++$this->nextRequest;
        $this->requests[$id] = [
            'item' => $item, 'kind' => $kind, 'started' => (float) $now, 'headers_at' => null, 'token' => $token, 'bytes' => $bytes,
            'aborted' => false, 'promise' => null, 'body' => isset($options['body']) ? $options['body'] : null, 'answered' => false,
        ];
        if ($kind === 'api') {
            // Whether a hop of its redirect chain was answered, whose server then has the request whatever becomes of a
            // later hop: answeredHops sees every hop's answer, whatever its handler reports, and a native hop's report of
            // its transfer also shows an answer whose headers came before the transfer failed.
            $onStats = isset($options['on_stats']) ? $options['on_stats'] : null;
            $options['on_stats'] = function ($stats) use ($id, $onStats) {
                if (isset($this->requests[$id]) && $stats->hasResponse()) {
                    $this->requests[$id]['answered'] = true;
                }

                if ($onStats !== null) {
                    call_user_func($onStats, $stats);
                }
            };
        }

        $onHeaders = isset($options['on_headers']) ? $options['on_headers'] : null;
        $options['on_headers'] = function ($response) use ($id, $onHeaders) {
            if (isset($this->requests[$id]) && $this->requests[$id]['headers_at'] === null) {
                $this->requests[$id]['headers_at'] = $this->clock->now();
            }

            if ($onHeaders !== null) {
                call_user_func($onHeaders, $response);
            }
        };

        try {
            $stack = Api::requestStack($this->handler, (string) $this->config['base_url'], $method);
            if ($kind === 'api') {
                $stack->after('allow_redirects', $this->answeredHops($id), 'files_answered_hops');
            }

            $client = new Client(['handler' => $stack]);
            $promise = $client->requestAsync($method, $url, $options);
        } catch (\Exception $e) {
            $this->events[] = [$id, false, $e, $this->clock->now()];
            return;
        }

        $this->requests[$id]['promise'] = $promise;
        $promise->then(
            function ($response) use ($id) {
                $this->events[] = [$id, true, $response, $this->clock->now()];
            },
            function ($reason) use ($id) {
                $this->events[] = [$id, false, $reason, $this->clock->now()];
            }
        );
        if (!$this->concurrent) {
            $promise->wait(false);
            self::runQueue();
        }
    }

    // Middleware for request $id's stack, inside its redirect middleware: marks the request answered as soon as any hop of
    // its redirect chain is answered, as each hop's response arrives and before the redirect middleware follows it,
    // whatever the handler reports of the transfer (a handler of its own may call no on_stats or on_headers). What it is
    // given passes on as it was.
    private function answeredHops($id)
    {
        return function ($handler) use ($id) {
            return function ($request, array $options) use ($handler, $id) {
                return $handler($request, $options)->then(function ($response) use ($id) {
                    if (isset($this->requests[$id])) {
                        $this->requests[$id]['answered'] = true;
                    }

                    return $response;
                });
            };
        };
    }

    private function controller($key, array $policy)
    {
        if (!isset($this->controllers[$key])) {
            $this->controllers[$key] = new ConcurrencyController($policy, $this->clock->now());
        }

        return [$key, $this->controllers[$key]];
    }

    // The engine's own decision of which queued transfers start at $now (internal: public only so that it can be
    // checked against real controllers). $options are the job's; $memory, $controllers and $now as preparationLimit
    // takes them; $active the transfers started and not ended, $ready their attempts ready to start, and $queued the
    // directions ('upload' or 'download') of the transfers waiting to start, by their keys in the job's order. Gives
    // [the keys of those that start now, the memory to keep]. One count of started transfers covers the whole job,
    // within the paced limit (preparationLimit); Aggressive startup, which the uploads' controllers alone use, lets an
    // upload start whenever fewer than max_concurrent_files have, and never changes when a download starts.
    public static function startable(array $options, array $memory, array $controllers, $active, $ready, array $queued, $now)
    {
        $maximum = $options['max_concurrent_files'];
        list($limit, $memory) = self::preparationLimit($memory, $controllers, $maximum, $ready, count($queued) > 0, $now);
        $aggressive = $options['startup'] === 'aggressive' && $options['concurrency'] === null && $options['initial_target'] === null;
        $start = [];
        foreach ($queued as $key => $direction) {
            if ($active < ($aggressive && $direction === 'upload' ? $maximum : $limit)) {
                $start[] = $key;
                $active++;
            }
        }

        return [$start, $memory];
    }

    // What preparationLimit remembers before its first decision: the limit derived from a target of four, and no
    // observation yet.
    public static function preparationMemory($maximum)
    {
        return [
            'remembered' => self::derivedFileLimit($maximum, self::INITIAL_FILE_TARGET), 'observed_target' => null, 'observed_warm' => false,
            'changed_at' => 0.0,
        ];
    }

    // The engine's own decision of how many transfers may be started at once (internal: public only so that it can be
    // checked against real controllers). From $memory, what the last decision kept (preparationMemory at first), the
    // job's $controllers at $now, $ready attempts ready to start and whether transfers are $queued to start, it gives
    // [the limit, the memory to keep], at most $maximum.
    //
    // The limit is the target-derived min(files, max(4, t + t / 4 + 1)) of the lowest target t among the controllers (4
    // before there is one), which a remembered limit may exceed: that grows by one each 100 ms while fewer than t / 4 + 1
    // attempts are ready and transfers wait to start, and returns by one each 100 ms toward the derived limit while
    // enough are ready. Whether t is in its warm start is told by the scopes holding t alone, ties combined; a scope
    // with a higher target says nothing of it. A rise of t from an observation that was warm raises the remembered limit
    // in proportion, so that the warm start's last rise, its promotion to the policy's start, counts as its earlier
    // rises did, though the scope is no longer warm once promoted; after that, the paced steps alone apply.
    public static function preparationLimit(array $memory, array $controllers, $maximum, $ready, $queued, $now)
    {
        $target = null;
        $warm = false;
        foreach ($controllers as $controller) {
            $current = $controller->target();
            if ($target === null || $current < $target) {
                $target = $current;
                $warm = $controller->isWarm();
            } elseif ($current === $target) {
                $warm = $warm || $controller->isWarm();
            }
        }

        if ($target === null) {
            $target = self::INITIAL_FILE_TARGET;
        }

        if ($memory['observed_warm'] && $memory['observed_target'] !== null && $target > $memory['observed_target']) {
            $memory['remembered'] = min($maximum, (int) ceil($memory['remembered'] * $target / $memory['observed_target']));
        }

        $memory['observed_target'] = $target;
        $memory['observed_warm'] = $warm;
        $derived = self::derivedFileLimit($maximum, $target);
        $threshold = (int) floor($target / 4) + 1;
        if ($now - $memory['changed_at'] >= self::FILE_PACING_SECONDS) {
            if ($ready < $threshold && $queued && $memory['remembered'] < $maximum) {
                $memory['remembered']++;
                $memory['changed_at'] = (float) $now;
            } elseif ($ready >= $threshold && $memory['remembered'] > $derived) {
                $memory['remembered']--;
                $memory['changed_at'] = (float) $now;
            }
        }

        return [max($derived, min($maximum, $memory['remembered'])), $memory];
    }

    private static function derivedFileLimit($maximum, $target)
    {
        return min($maximum, max(4, $target + (int) floor($target / 4) + 1));
    }
}
