<?php

declare(strict_types=1);

namespace Files\Transfer;

use Files\Exception\ConfigurationException;

/**
 * The clock adaptive transfers measure time with: seconds, as a float, since the clock was made. It reads a native
 * monotonic counter only, and adaptive transfers run only where PHP has one and 64-bit integers:
 *
 * - PHP 7.3 and later: PHP's own hrtime function.
 * - PHP 5.5 to 7.2 with 64-bit integers: the hrtime extension's HRTime\PerformanceCounter (PECL hrtime 0.6.0 or
 *   later), with its static getTicks and getFrequency.
 *
 * Neither the wall clock (microtime), a userland hrtime function (such as a polyfill's) nor a userland class of that
 * name qualifies: the function and the class must be PHP's own or the extension's. Where no provider qualifies, making
 * the clock throws a ConfigurationException, before any transfer starts; the SDK's sequential helpers, which read no
 * clock, are unaffected.
 *
 * Linux and macOS counters stop while the computer sleeps; Windows' keeps counting. A transfer that spans a sleep is
 * measured accordingly.
 */
final class MonotonicClock
{
    const HRTIME = 'hrtime';
    const PERFORMANCE_COUNTER = 'performance-counter';

    private $provider;
    private $originSeconds = 0;
    private $originNanoseconds = 0;
    private $originTicks = 0;
    private $frequency = 1;
    private $last = 0.0;

    private function __construct($provider)
    {
        $this->provider = $provider;
        if ($provider === self::HRTIME) {
            $reading = hrtime(false);
            $this->originSeconds = $reading[0];
            $this->originNanoseconds = $reading[1];
        } else {
            $this->frequency = \HRTime\PerformanceCounter::getFrequency();
            $this->originTicks = \HRTime\PerformanceCounter::getTicks();
        }
    }

    // The clock of this PHP, or a ConfigurationException naming what it lacks.
    public static function detect()
    {
        return new MonotonicClock(self::provider(PHP_INT_SIZE, PHP_VERSION));
    }

    // Whether this PHP can run adaptive transfers: 64-bit integers and a native monotonic clock.
    public static function isSupported()
    {
        try {
            self::provider(PHP_INT_SIZE, PHP_VERSION);
            return true;
        } catch (ConfigurationException $e) {
            return false;
        }
    }

    // The provider adaptive transfers would use on a PHP of version $phpVersion whose integers are $integerBytes bytes,
    // given the functions and extensions loaded in this process: hrtime or performance-counter. Anything else throws a
    // ConfigurationException.
    public static function provider($integerBytes, $phpVersion)
    {
        if ($integerBytes !== 8) {
            throw new ConfigurationException('Adaptive transfers need 64-bit PHP integers (PHP_INT_SIZE 8); this PHP has ' . $integerBytes . '-byte integers. Upload and download without the transfer option on this PHP.');
        }

        if (version_compare((string) $phpVersion, '7.3.0', '>=')) {
            if (self::nativeHrtime()) {
                return self::HRTIME;
            }

            throw new ConfigurationException('Adaptive transfers need PHP\'s own hrtime function, which this PHP does not provide (it may be disabled).');
        }

        if (self::nativePerformanceCounter()) {
            return self::PERFORMANCE_COUNTER;
        }

        throw new ConfigurationException('Adaptive transfers need PHP 7.3 or later, or on an older 64-bit PHP the hrtime extension (PECL hrtime 0.6.0 or later, HRTime\\PerformanceCounter), for a monotonic clock. Upload and download without the transfer option on this PHP.');
    }

    // Whether hrtime is PHP's own function and answers with seconds and nanoseconds.
    private static function nativeHrtime()
    {
        if (!function_exists('hrtime')) {
            return false;
        }

        $function = new \ReflectionFunction('hrtime');
        if (!$function->isInternal()) {
            return false;
        }

        $reading = @hrtime(false);
        return is_array($reading) && count($reading) === 2 && is_int($reading[0]) && is_int($reading[1]);
    }

    // Whether HRTime\PerformanceCounter is the hrtime extension's class, with a positive frequency and ticks that do
    // not go back.
    private static function nativePerformanceCounter()
    {
        $class = 'HRTime\\PerformanceCounter';
        if (!extension_loaded('hrtime') || !class_exists($class, false)) {
            return false;
        }

        $reflection = new \ReflectionClass($class);
        if (!$reflection->isInternal() || !$reflection->hasMethod('getTicks') || !$reflection->hasMethod('getFrequency')) {
            return false;
        }

        if (!$reflection->getMethod('getTicks')->isStatic() || !$reflection->getMethod('getFrequency')->isStatic()) {
            return false;
        }

        $frequency = call_user_func([$class, 'getFrequency']);
        $first = call_user_func([$class, 'getTicks']);
        $second = call_user_func([$class, 'getTicks']);
        return is_int($frequency) && $frequency > 0 && is_int($first) && is_int($second) && $second >= $first;
    }

    public function provided()
    {
        return $this->provider;
    }

    // Seconds since the clock was made. A reading lower than the one before it, which a monotonic counter does not
    // give, counts as no time passing.
    public function now()
    {
        if ($this->provider === self::HRTIME) {
            $reading = hrtime(false);
            $elapsed = ($reading[0] - $this->originSeconds) + ($reading[1] - $this->originNanoseconds) / 1e9;
        } else {
            $elapsed = (\HRTime\PerformanceCounter::getTicks() - $this->originTicks) / $this->frequency;
        }

        if ($elapsed < $this->last) {
            return $this->last;
        }

        $this->last = (float) $elapsed;
        return $this->last;
    }
}
