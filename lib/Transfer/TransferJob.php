<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\EmptyPropertyException;
use Files\Exception\InvalidParameterException;
use Files\Exception\MissingParameterException;
use Files\Files;

/**
 * Adaptive uploads and downloads: concurrent requests, sized by measured throughput, sharing one set of budgets and
 * one learned target across the transfers of a job. They run only on 64-bit PHP with a native monotonic clock (PHP
 * 7.3's hrtime, or the hrtime extension's HRTime\PerformanceCounter on older PHP); making a job anywhere else throws a
 * ConfigurationException before anything is read, sent or written, and File's helpers without the transfer option keep
 * their sequential behavior. isSupported says which a PHP is.
 *
 *     $job = new TransferJob(['startup' => 'default']);
 *     $job->uploadFile('reports/a.csv', '/tmp/a.csv');
 *     $job->uploadFile('reports/b.csv', '/tmp/b.csv', ['mkdir_parents' => true], ['progress' => $report]);
 *     $job->downloadToFile('reports/c.csv', '/tmp/c.csv');
 *     $job->wait();
 *
 * Adding a transfer starts nothing; wait runs every transfer added so far and returns when all have completed or
 * paused, or throws what stopped the job: the first failure, or the first exception a progress or diagnostics callback
 * threw, as it was (no later transfer starts, those running are cancelled, and no upload is ended after a failure), or a
 * TransferCancelledException after cancel. A job that stopped takes no more transfers. Callbacks run on the caller's
 * thread, between the job's steps, one at a time; they may call pause or cancel, never wait.
 *
 * pause, from a callback or between waits, stops admitting work, cancels the parts and ranges in flight (keeping
 * everything accepted, and a caller's stream's bytes already read), lets API requests in flight finish, and makes wait
 * return. resume then wait continue every paused transfer in this job without sending accepted bytes again. A pause
 * lives in this job and this process only: the files, streams and job must stay open and unchanged until it resumes; a
 * changed local file fails its upload. Nothing is kept for another process or after a crash.
 *
 * Options (all optional):
 *
 *   startup                  default (an eligible S3 upload starts with one part in flight and earns more by measured
 *                            throughput and response times) or aggressive (it starts at the policy's start). Both share
 *                            one eventual policy and its limits. Uploads only; downloads start at the policy's start.
 *   concurrency              A fixed number of content requests in flight, instead of adaptive concurrency.
 *   initial_target           Where adaptive uploads start, without the Default warm start.
 *   max_concurrent_requests  At most this many requests in flight, API requests included: 1 to 1024, default 64.
 *   max_concurrent_files     At most this many transfers started at once, default 128.
 *   max_buffer_bytes         At most this many bytes held for parts and ranges, default 64 MiB or half the memory PHP
 *                            reports free under its memory_limit, when that is less.
 *   max_retries              Retries of each request, default Files::$maxNetworkRetries.
 *   download_part_bytes      The size of a download's ranges, default 8 MiB.
 *   diagnostics              A callable given each concurrency decision (its label, target, epoch, attempts in flight
 *                            and measurements, with units); it never sees a credential, header or URL.
 *   api_key, session_id, workspace_id
 *                            The API credentials and workspace of the job's API requests, as the generated methods'
 *                            options give them; each transfer's own options can give them too.
 *
 * The job copies the SDK's configuration when it is made (Files::setApiKey, setBaseUrl, setWorkspaceId, setLanguage,
 * setHandler, timeouts and retries): later changes do not reach it. Its requests are pooled in a curl multi handle of
 * its own, or in the GuzzleHttp\Handler\CurlMultiHandler set with Files::setHandler; any other handler set there is
 * used one request at a time, as statistics()['transport'] reports.
 *
 * The defaults are starting choices of this SDK, not measured performance.
 */
final class TransferJob
{
    const DEFAULT_MAX_REQUESTS = 64;
    const MAX_REQUESTS = 1024;
    const DEFAULT_MAX_FILES = 128;
    const DEFAULT_MAX_BUFFER_BYTES = 67108864;
    const DEFAULT_DOWNLOAD_PART_BYTES = 8388608;

    private static $optionNames = [
        'startup', 'concurrency', 'initial_target', 'max_concurrent_requests', 'max_concurrent_files', 'max_buffer_bytes', 'max_retries',
        'download_part_bytes', 'diagnostics', 'api_key', 'session_id', 'workspace_id',
    ];

    private $engine;

    public function __construct($options = [])
    {
        $clock = MonotonicClock::detect();
        $options = self::validate($options);
        $config = Files::snapshot();
        foreach (['api_key', 'session_id', 'workspace_id'] as $name) {
            if (array_key_exists($name, $options['api'])) {
                $config[$name] = $options['api'][$name];
            }
        }

        if ($options['max_retries'] === null) {
            $options['max_retries'] = max(0, (int) $config['max_network_retries']);
        }

        unset($options['api']);
        $this->engine = new TransferEngine($clock, $config, $options);
    }

    // Whether this PHP can run adaptive transfers: 64-bit integers and a native monotonic clock.
    public static function isSupported()
    {
        return MonotonicClock::isSupported();
    }

    // Adds an upload of the local file at $sourceFilePath to $destinationPath. $params are File::uploadFile's (the
    // first part's parameters, and provided_mtime and custom_metadata for its end; provided_mtime defaults to the file's
    // modification time). $options are the generated methods' options for its API requests, and progress: a callable
    // given total_bytes, acknowledged_bytes, part_number, part_bytes, elapsed_seconds and bytes_per_second (this wait's
    // acknowledged bytes over its time) after each part is acknowledged, in the order they are.
    public function uploadFile($destinationPath, $sourceFilePath, $params = [], $options = [])
    {
        if (!$destinationPath) {
            throw new MissingParameterException('Parameter missing: destinationPath');
        }

        if (!$sourceFilePath) {
            throw new MissingParameterException('Parameter missing: sourceFilePath');
        }

        if (!file_exists($sourceFilePath)) {
            throw new EmptyPropertyException('Empty sourceFilePath: No such file or directory');
        }

        list($first, $end) = UploadTransfer::splitParams($params);
        list($apiOptions, $progress) = UploadTransfer::splitOptions($options, false);
        return $this->engine->add(new UploadTransfer(UploadTransfer::FILE, $sourceFilePath, $destinationPath, $first, $end, $apiOptions, $progress, null));
    }

    // Adds an upload of $data, a string or a stream read from where it stands, to $destinationPath, as uploadFile;
    // provided_mtime defaults to now. A stream's size, in $options['size'], lets its parts go concurrently; without it,
    // they go one at a time, as File::uploadData sends them. A caller's stream is closed once every part is acknowledged
    // and progress has returned, before the upload ends; a failure, cancellation or pause before that leaves it open.
    public function uploadData($destinationPath, $data, $params = [], $options = [])
    {
        if (!$destinationPath) {
            throw new MissingParameterException('Parameter missing: destinationPath');
        }

        if (!$data) {
            throw new MissingParameterException('Parameter missing: data');
        }

        list($first, $end) = UploadTransfer::splitParams($params);
        list($apiOptions, $progress, $size) = UploadTransfer::splitOptions($options, true);
        if (is_resource($data)) {
            $transfer = new UploadTransfer(UploadTransfer::STREAM, $data, $destinationPath, $first, $end, $apiOptions, $progress, $size);
        } else {
            $data = (string) $data;
            if ($size !== null && $size !== strlen($data)) {
                throw new InvalidParameterException('$options[\'size\'] is ' . $size . ', but the data has ' . strlen($data) . ' bytes');
            }

            $transfer = new UploadTransfer(UploadTransfer::DATA, $data, $destinationPath, $first, $end, $apiOptions, $progress, null);
        }

        return $this->engine->add($transfer);
    }

    // Adds a download of the file at $remoteFilePath to the local file $destinationPath, which is replaced only once
    // every byte is verified. $options are the generated methods' options for its API request, and progress: a callable
    // given total_bytes, received_bytes, range_start, range_bytes, elapsed_seconds and bytes_per_second after each
    // range is received and verified. A completed download's handle's result() gives its received and total bytes.
    public function downloadToFile($remoteFilePath, $destinationPath, $options = [])
    {
        if (!$remoteFilePath) {
            throw new MissingParameterException('Parameter missing: remoteFilePath');
        }

        if (!$destinationPath) {
            throw new MissingParameterException('Parameter missing: destinationPath');
        }

        list($apiOptions, $progress) = UploadTransfer::splitOptions($options, false);
        return $this->engine->add(new DownloadTransfer($remoteFilePath, DownloadTransfer::FILE, $destinationPath, $apiOptions, $progress));
    }

    // Adds a download of the file at $remoteFilePath to $stream, a writable stream the caller keeps open and closes,
    // written in order from where it stands, as downloadToFile.
    public function downloadToStream($remoteFilePath, $stream, $options = [])
    {
        if (!$remoteFilePath) {
            throw new MissingParameterException('Parameter missing: remoteFilePath');
        }

        if (!is_resource($stream)) {
            throw new InvalidParameterException('$stream must be an open stream; received ' . gettype($stream));
        }

        list($apiOptions, $progress) = UploadTransfer::splitOptions($options, false);
        return $this->engine->add(new DownloadTransfer($remoteFilePath, DownloadTransfer::STREAM, $stream, $apiOptions, $progress));
    }

    // Runs the job's transfers until each has completed or paused; throws what stopped the job. Returns at once while
    // the job is paused.
    public function wait()
    {
        $this->engine->run();
    }

    public function pause()
    {
        $this->engine->pause();
    }

    // Lets the next wait continue the paused transfers.
    public function resume()
    {
        $this->engine->resume();
    }

    public function cancel()
    {
        $this->engine->cancel();
    }

    public function isPaused()
    {
        return $this->engine->isPaused();
    }

    // The job's transport (concurrent or serial), clock, limits, current and peak requests in flight (API and content),
    // attempts, retries, renewals, accepted bytes, reserved and peak reserved buffer bytes, started transfers, and each
    // controller's policy, target, peak target, epoch, attempts in flight and warm start.
    public function statistics()
    {
        return $this->engine->statistics();
    }

    private static function validate($options)
    {
        if (!is_array($options)) {
            throw new InvalidParameterException('The transfer options must be an array; received ' . gettype($options));
        }

        foreach (array_keys($options) as $name) {
            if (!in_array($name, self::$optionNames, true)) {
                throw new InvalidParameterException('Unknown transfer option ' . $name);
            }
        }

        $startup = array_key_exists('startup', $options) ? $options['startup'] : null;
        if ($startup !== null && $startup !== 'default' && $startup !== 'aggressive') {
            throw new InvalidParameterException('The transfer option startup must be default or aggressive');
        }

        $concurrency = self::positive($options, 'concurrency', null, null);
        $initial = self::positive($options, 'initial_target', null, null);
        if ($concurrency !== null && ($startup !== null || $initial !== null)) {
            throw new InvalidParameterException('The transfer option concurrency fixes the requests in flight; it cannot be given with startup or initial_target');
        }

        if ($initial !== null && $startup === 'aggressive') {
            throw new InvalidParameterException('The transfer option initial_target sets the start itself; it cannot be given with Aggressive startup');
        }

        $diagnostics = array_key_exists('diagnostics', $options) ? $options['diagnostics'] : null;
        if ($diagnostics !== null && !is_callable($diagnostics)) {
            throw new InvalidParameterException('The transfer option diagnostics must be callable');
        }

        $retries = null;
        if (array_key_exists('max_retries', $options) && $options['max_retries'] !== null) {
            if (!is_int($options['max_retries']) || $options['max_retries'] < 0) {
                throw new InvalidParameterException('The transfer option max_retries must be an integer of at least 0');
            }

            $retries = $options['max_retries'];
        }

        $api = [];
        foreach (['api_key', 'session_id', 'workspace_id'] as $name) {
            if (array_key_exists($name, $options)) {
                $api[$name] = $options[$name];
            }
        }

        return [
            'startup' => $startup === null ? 'default' : $startup,
            'concurrency' => $concurrency,
            'initial_target' => $initial,
            'max_concurrent_requests' => self::positive($options, 'max_concurrent_requests', self::DEFAULT_MAX_REQUESTS, self::MAX_REQUESTS),
            'max_concurrent_files' => self::positive($options, 'max_concurrent_files', self::DEFAULT_MAX_FILES, null),
            'max_buffer_bytes' => self::positive($options, 'max_buffer_bytes', self::defaultBufferBytes(), null),
            'max_retries' => $retries,
            'download_part_bytes' => self::positive($options, 'download_part_bytes', self::DEFAULT_DOWNLOAD_PART_BYTES, null),
            'diagnostics' => $diagnostics,
            'api' => $api,
        ];
    }

    // The integer option $name, at least 1 and at most $maximum (when given); $default when it is absent or null.
    private static function positive(array $options, $name, $default, $maximum)
    {
        if (!array_key_exists($name, $options) || $options[$name] === null) {
            return $default;
        }

        $value = $options[$name];
        if (!is_int($value) || $value < 1 || ($maximum !== null && $value > $maximum)) {
            throw new InvalidParameterException('The transfer option ' . $name . ' must be an integer from 1' . ($maximum !== null ? ' to ' . $maximum : ''));
        }

        return $value;
    }

    // 64 MiB, or half the memory PHP reports free under its memory_limit when that is less.
    private static function defaultBufferBytes()
    {
        $limit = self::bytes((string) ini_get('memory_limit'));
        if ($limit <= 0) {
            return self::DEFAULT_MAX_BUFFER_BYTES;
        }

        $free = $limit - memory_get_usage(true);
        return max(1, min(self::DEFAULT_MAX_BUFFER_BYTES, (int) floor($free / 2)));
    }

    // A php.ini size such as 128M as bytes; -1 (no limit) or an unreadable value as 0.
    private static function bytes($value)
    {
        $value = trim($value);
        if (!preg_match('/\A([0-9]+)\s*([KMG]?)\z/i', $value, $match)) {
            return 0;
        }

        $units = ['' => 1, 'K' => 1024, 'M' => 1048576, 'G' => 1073741824];
        return (int) $match[1] * $units[strtoupper($match[2])];
    }
}
