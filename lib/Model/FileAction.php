<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class FileAction
 *
 * @package Files
 */
class FileAction
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
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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

    /**
     * Wait for the migration reported by this action, returning it once completed.
     * Returns null for an action already completed without a migration.
     *
     * Options: interval (seconds between polls, default 1), timeout (seconds for
     * polling loop, absent for no deadline), and progress (called with each
     * FileMigration; throwing stops the wait and preserves that exception).
     * Timeout is checked before/after polls and bounds the sleep between them.
     * In-flight requests retain their configured HTTP timeouts and retries.
     * Stopping the wait does not cancel the action on Files.com.
     *
     * @param array $options Polling options; these do not replace this action's request options.
     *     - `interval` (int|float, optional): Positive, finite seconds between polls; defaults to 1.
     *     - `timeout` (int|float|null, optional): Positive, finite polling deadline in seconds; null means no deadline.
     *     - `progress` (callable|null, optional): Called with each FileMigration; throw to stop waiting.
     * @return \Files\Model\FileMigration|null Completed migration, or null when already completed without one.
     * @throws \Files\Exception\InvalidParameterException When a polling option is invalid.
     * @throws \Files\Exception\FileActionException When completion cannot be confirmed, the action fails or the deadline expires.
     * @throws \Files\Exception\FilesException When an API poll fails; callback exceptions propagate unchanged.
     */
    public function waitForCompletion($options = [])
    {
        if (!is_array($options)) {
            throw new \Files\Exception\InvalidParameterException('$options must be of type array; received ' . gettype($options));
        }

        $interval = array_key_exists('interval', $options) ? $options['interval'] : 1.0;
        $timeout = array_key_exists('timeout', $options) ? $options['timeout'] : null;
        foreach (['interval' => $interval, 'timeout' => $timeout] as $name => $value) {
            if ($name === 'timeout' && $value === null) {
                continue;
            }

            if ((!is_int($value) && !is_float($value)) || !is_finite($value) || $value <= 0) {
                throw new \Files\Exception\InvalidParameterException('$options[\'' . $name . '\'] must be a finite number of seconds greater than zero');
            }
        }

        $progress = isset($options['progress']) ? $options['progress'] : null;
        if ($progress !== null && !is_callable($progress)) {
            throw new \Files\Exception\InvalidParameterException('$options[\'progress\'] must be callable');
        }

        $id = $this->getFileMigrationId();
        if ($id === null) {
            if ($this->getStatus() === 'completed') {
                return null;
            }

            throw new \Files\Exception\FileActionException('The action reports no file_migration_id and its status does not confirm completion', $this);
        }

        if (!is_int($id) || $id < 1 || $id > 2147483647) {
            throw new \Files\Exception\FileActionException('The action reports an invalid File Migration ID', $this);
        }

        $deadline = $timeout === null ? null : self::waitClock() + $timeout;
        $observed = null;
        $checkDeadline = function () use ($deadline, $timeout, $id, &$observed) {
            if ($deadline !== null && self::waitClock() >= $deadline) {
                throw new \Files\Exception\FileActionException('Waiting for File Migration ' . $id . ' timed out after ' . $timeout . ' seconds', $this, $observed);
            }
        };

        for (;;) {
            $checkDeadline();
            $response = Api::sendRequest('/file_migrations/' . $id, 'GET', [], $this->options);
            $checkDeadline();
            $data = isset($response->data) ? $response->data : null;
            if (!is_object($data) || (isset($data->id) && $data->id !== $id)) {
                throw new \Files\Exception\FileActionException('The API did not return File Migration ' . $id, $this, $observed);
            }

            $observed = new FileMigration((array) $data, $this->options);
            $status = $observed->getStatus();
            if ($progress !== null) {
                call_user_func($progress, $observed);
                $checkDeadline();
            }

            if ($status === 'completed') {
                return $observed;
            }

            if ($status === 'failed' || $status === 'cancelled') {
                $detail = $observed->getFailureMessage();
                throw new \Files\Exception\FileActionException('File Migration ' . $id . ' ' . $status . (is_string($detail) && $detail !== '' ? ': ' . $detail : ''), $this, $observed);
            }

            if ($status !== 'pending' && $status !== 'processing') {
                throw new \Files\Exception\FileActionException('File Migration ' . $id . ' has a status that does not confirm completion', $this, $observed);
            }

            self::waitSleep($interval, $deadline, $checkDeadline);
        }
    }

    private static function waitClock()
    {
        return function_exists('hrtime') ? hrtime(true) / 1000000000 : microtime(true);
    }

    private static function waitSleep($seconds, $deadline, $checkDeadline)
    {
        $until = self::waitClock() + $seconds;
        if ($deadline !== null) {
            $until = min($until, $deadline);
        }
        while (($remaining = $until - self::waitClock()) > 0) {
            // Keep usleep's argument within an integer on supported 32-bit PHP.
            usleep((int) min(1000000, ceil($remaining * 1000000)));
        }
        $checkDeadline();
    }
    // string # Status of file operation.
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    // int64 # If status is pending, this is the id of the File Migration to check for status updates.  Delete actions include it whenever the deletion runs as a File Migration, including while it is processing.
    public function getFileMigrationId()
    {
        return isset($this->attributes['file_migration_id']) ? $this->attributes['file_migration_id'] : null;
    }
}
