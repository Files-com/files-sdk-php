<?php

declare(strict_types=1);

namespace Files\Transfer;

/**
 * How many content requests (upload parts, download ranges) a group of transfers that learn together may have in
 * flight: its target. The transfer engine asks it before each attempt (admits), tells it when each attempt starts and
 * how it ended, and tells it why fewer attempts than the target were in flight (shortfall). Times are seconds of the
 * job's monotonic clock; events are serialized, as the engine runs on one thread.
 *
 * It follows verification/adaptive-upload-spec.md. Accepted throughput is a window's delivered bytes over its wall time,
 * never over summed overlapping durations. Seconds per byte (L) is the window's accepted attempt durations over its
 * bytes, the lowest positive window L ever seen is Lmin, and the queue estimate is average in-flight * (1 - Lmin / L).
 * An ordinary window closes after max(32, target) accepted answers and at least their mean duration; it is judged only
 * when attempts filled the target for at least 75% of its time, and otherwise records the dominant shortfall and
 * neither earns nor rejects anything. Completions during a cooldown are not measured. A failure or throttle shrinks the
 * target only when the attempt began in the current epoch, which it then advances; every throttle restarts measurement
 * and extends the cooldown to the later of its end and the server's Retry-After.
 *
 * The scaled S3 policy is the reference's (start, minimum, soft ceiling 150 / 8 / 150 within a maximum of 1024)
 * mapped to a request cap C: S = min(150, C) and every count in it scaled by S / 150 (minimum, floors, growth step,
 * plateau and queue limits), so that a smaller cap keeps the same decisions relative to its scale; percentages, the
 * 32-answer window, the 96 MiB/s fast-link threshold and the 64 GiB unlock are unchanged. Default startup (warm) starts
 * at one and probes as the reference's warm start does; Aggressive starts at S. The generic policy, for destinations
 * with direct connection information, starts at min(C, 4), within 1 and min(C, 16). These are unqualified starting
 * choices, not measured performance.
 */
final class ConcurrencyController
{
    const FAST_LINK_BYTES_PER_SECOND = 100663296;
    const SOFT_CEILING_UNLOCK_BYTES = 68719476736;
    const WINDOW_ATTEMPTS = 32;
    const OCCUPANCY = 0.75;
    const PROBE_GAIN = 0.01;
    const PLATEAU_GAIN_PER_PART = 0.0015;
    const TOLERATED_LOSS = 0.02;
    const PROBE_WINDOWS = 2;
    const FAILURE_SHRINK = 0.35;
    const THROTTLE_SHRINK = 0.10;
    const LATENCY_SHRINK = 0.08;
    const REJECTED_PROBE_SHRINK = 0.08;
    const HOLD_WINDOWS = 1;
    const GROWTH_INTERVAL_WINDOWS = 1;
    // A warm raise from n by d must deliver baseline * (1 + WARM_GAIN * d / n), with a queue estimate below
    // WARM_QUEUE * d.
    const WARM_GAIN = 0.5;
    const WARM_QUEUE = 0.5;

    const FAILURE = 'failure';
    const THROTTLE = 'throttle';
    // An attempt that ended without telling anything of capacity: cancelled, aborted, or failed locally.
    const ENDED = 'ended';

    private $policy;
    private $target;
    private $peakTarget;
    private $epoch = 0;
    private $cooldownUntil = 0.0;
    private $sequence = 0;
    private $running = [];
    private $lastEvent;
    private $shortfall = null;
    private $acceptedBytes = 0;
    private $lowestSecondsPerByte = null;
    private $decisions = [];

    private $windowStart;
    private $windowBytes = 0;
    private $windowDurations = 0.0;
    private $windowAnswers = 0;
    private $windowInFlight = 0.0;
    private $windowFull = 0.0;
    private $windowShortfall = [];
    private $windowSamples = [];

    private $hold = 0;
    private $growthCountdown = self::GROWTH_INTERVAL_WINDOWS;
    private $probe = null;
    private $levels = [];
    private $unlocked = false;
    private $fastLink = false;

    private $warm = false;
    private $warmPhase = null;
    private $warmBaseline = null;
    private $warmFrom = null;
    private $warmAdded = 0;
    private $cohort = null;
    private $answersSinceRaise = 0;
    private $restRemaining = 0;
    private $afterRest = false;
    private $fastestMedian = [];
    private $relativeSquares = 0.0;
    private $degreesOfFreedom = 0;

    // The scaled S3 policy for uploads, starting warm (at one) for Default startup and at S for Aggressive.
    public static function storageUpload($cap, $startup)
    {
        $policy = self::scaled($cap);
        $policy['name'] = $startup === 'aggressive' ? 's3-aggressive' : 's3-default';
        $policy['warm'] = $startup !== 'aggressive';
        return $policy;
    }

    // The scaled S3 policy as downloads use it: starting at S, never warm. Upload startup does not apply.
    public static function download($cap)
    {
        $policy = self::scaled($cap);
        $policy['name'] = 'download';
        return $policy;
    }

    // The scaled S3 policy started where the caller chose (initial_target), without the warm start.
    public static function explicit($cap, $initialTarget)
    {
        $policy = self::scaled($cap);
        $policy['name'] = 'explicit';
        $policy['start'] = max(1, min($initialTarget, $cap));
        return $policy;
    }

    // Uploads to destinations with direct connection information: start min(cap, 4), within 1 and min(cap, 16).
    public static function generic($cap)
    {
        $maximum = min($cap, 16);
        $scale = $maximum / 150;
        return [
            'name' => 'generic', 'adaptive' => true, 'warm' => false, 'start' => min($cap, 4), 'min' => 1, 'max' => $maximum,
            'soft' => $maximum, 'plateau' => $maximum, 'step' => 1, 'queue_suppress' => 96 * $scale, 'queue_backoff' => 160 * $scale,
            'latency_floor' => 1, 'throughput_floor' => 1, 'probe_floor' => min($cap, 4), 'fast_link' => false,
        ];
    }

    // A fixed target: concurrency as the caller set it, or one for a transport that sends one request at a time.
    public static function fixed($target)
    {
        $target = max(1, $target);
        return [
            'name' => 'fixed', 'adaptive' => false, 'warm' => false, 'start' => $target, 'min' => $target, 'max' => $target,
            'soft' => $target, 'plateau' => $target, 'step' => 0, 'queue_suppress' => 0, 'queue_backoff' => 0,
            'latency_floor' => $target, 'throughput_floor' => $target, 'probe_floor' => $target, 'fast_link' => false,
        ];
    }

    private static function scaled($cap)
    {
        $start = min(150, $cap);
        $scale = $start / 150;
        $minimum = max(1, min($start, (int) round(8 * $scale)));
        $floor = max($minimum, (int) round(50 * $scale));
        return [
            'name' => 'scaled', 'adaptive' => true, 'warm' => false, 'start' => $start, 'min' => $minimum, 'max' => $cap,
            'soft' => $start, 'plateau' => max($start, (int) round(200 * $scale)), 'step' => max(1, (int) round(4 * $scale)),
            'queue_suppress' => 96 * $scale, 'queue_backoff' => 160 * $scale, 'latency_floor' => $floor,
            'throughput_floor' => $floor, 'probe_floor' => $start, 'fast_link' => true,
        ];
    }

    public function __construct(array $policy, $now)
    {
        $this->policy = $policy;
        $this->warm = !empty($policy['warm']) && $policy['start'] > 1;
        $this->warmPhase = $this->warm ? 'connect' : null;
        $this->target = $this->warm ? 1 : $policy['start'];
        $this->peakTarget = $this->target;
        $this->lastEvent = (float) $now;
        $this->openWindow($now);
    }

    public function target()
    {
        return $this->target;
    }

    public function epoch()
    {
        return $this->epoch;
    }

    public function inFlight()
    {
        return count($this->running);
    }

    public function cooldownUntil()
    {
        return $this->cooldownUntil;
    }

    public function isWarm()
    {
        return $this->warm;
    }

    public function policy()
    {
        return $this->policy;
    }

    // Whether another attempt may start now: fewer in flight than the target, and no cooldown.
    public function admits($now)
    {
        return count($this->running) < $this->target && $now >= $this->cooldownUntil;
    }

    // Why fewer attempts than the target are in flight from $now on: shared_limiter (the job's request, byte or file
    // limits), no_part_ready, serial_parts or local_writes; null when the target is filled or only it limits them.
    public function shortfall($reason, $now)
    {
        $this->advance($now);
        $this->shortfall = $reason;
    }

    // An attempt starting at $now; its token goes back with how it ended.
    public function started($now)
    {
        $this->advance($now);
        $sequence = ++$this->sequence;
        $this->running[$sequence] = true;
        if ($this->warm && $this->warmPhase === 'probe' && $this->cohort === null && count($this->running) >= $this->target) {
            $this->cohort = $this->running;
        }

        return ['sequence' => $sequence, 'epoch' => $this->epoch, 'started' => (float) $now];
    }

    // An attempt accepted with $bytes delivered, its network work taking $duration seconds (through response headers for
    // an upload part; body reads without sink writes for a download range).
    public function accepted(array $token, $bytes, $duration, $now)
    {
        $this->advance($now);
        $this->acceptedBytes += $bytes;
        $measured = $now >= $this->cooldownUntil;
        if ($measured) {
            // The first answer after a cooldown opens a new window: the cooldown's time measures nothing.
            if ($this->windowStart < $this->cooldownUntil) {
                $this->openWindow($now);
            }

            if ($this->warm && $this->warmPhase === 'probe') {
                $this->answersSinceRaise++;
            }

            $duration = max(0.0, (float) $duration);
            $this->windowBytes += $bytes;
            $this->windowDurations += $duration;
            $this->windowAnswers++;
            if ($bytes > 0) {
                $this->windowSamples[] = $duration / $bytes;
            }
        }

        if ($this->forget($token)) {
            $this->openWindow($now);
            return;
        }

        if ($measured) {
            $this->closeWhenDue($now);
        }
    }

    // An attempt that ended without being accepted: FAILURE (a transient network or storage failure), THROTTLE (429 or
    // 503, with the server's Retry-After seconds) or ENDED.
    public function failed(array $token, $kind, $retryAfter, $now)
    {
        $this->advance($now);
        if ($this->forget($token)) {
            $this->openWindow($now);
        }

        if ($kind === self::THROTTLE) {
            $this->extendCooldown($now + max(0.0, (float) $retryAfter), $now);
        }

        if (!$this->policy['adaptive'] || ($kind !== self::FAILURE && $kind !== self::THROTTLE)) {
            return;
        }

        // Every failure or throttle restarts measurement. A warm probe is abandoned whatever epoch the attempt began in;
        // an ordinary probe only with the shrink of a failure from the current epoch: one from an earlier epoch says
        // nothing of the raised target, which goes on being judged until it earns its gain or is rejected.
        $this->openWindow($now);
        $current = $token['epoch'] === $this->epoch;
        if ($this->warm) {
            $this->warmPhase = 'rest';
            $this->restRemaining = self::HOLD_WINDOWS + self::GROWTH_INTERVAL_WINDOWS;
            $this->afterRest = true;
            $this->cohort = null;
        } elseif ($current) {
            $this->probe = null;
        }

        if (!$current) {
            $this->decide($kind === self::THROTTLE ? 'throttle_from_earlier_epoch' : 'failure_from_earlier_epoch', $now);
            return;
        }

        $shrink = $kind === self::THROTTLE ? self::THROTTLE_SHRINK : self::FAILURE_SHRINK;
        $minimum = $this->warm ? 1 : $this->policy['min'];
        $this->setTarget(min($this->target, max($minimum, (int) floor($this->target * (1 - $shrink)))));
        $this->epoch++;
        $this->hold = self::HOLD_WINDOWS;
        $this->decide($kind === self::THROTTLE ? 'throttle_shrink' : 'failure_shrink', $now);
    }

    // The decisions made since the last call, oldest first, each with its label, target, epoch, attempts in flight and
    // time, and the measurements it rests on.
    public function takeDecisions()
    {
        $decisions = $this->decisions;
        $this->decisions = [];
        return $decisions;
    }

    public function snapshot()
    {
        return [
            'policy' => $this->policy['name'], 'target' => $this->target, 'peak_target' => $this->peakTarget, 'epoch' => $this->epoch,
            'in_flight' => count($this->running), 'warm' => $this->warm, 'cooldown_until' => $this->cooldownUntil,
            'accepted_bytes' => $this->acceptedBytes,
        ];
    }

    // Removes an attempt from those in flight; true when it was the last of a warm raise's cohort.
    private function forget(array $token)
    {
        unset($this->running[$token['sequence']]);
        if (is_array($this->cohort)) {
            unset($this->cohort[$token['sequence']]);
            if (!$this->cohort) {
                $this->cohort = 'settled';
                return true;
            }
        }

        return false;
    }

    private function advance($now)
    {
        $elapsed = $now - $this->lastEvent;
        if ($elapsed <= 0) {
            return;
        }

        $running = count($this->running);
        $this->windowInFlight += $running * $elapsed;
        if ($running >= $this->target) {
            $this->windowFull += $elapsed;
        } else {
            $reason = $this->shortfall === null ? 'no_part_ready' : $this->shortfall;
            $this->windowShortfall[$reason] = (isset($this->windowShortfall[$reason]) ? $this->windowShortfall[$reason] : 0.0) + $elapsed;
        }

        $this->lastEvent = (float) $now;
    }

    private function openWindow($now)
    {
        $this->windowStart = (float) $now;
        $this->windowBytes = 0;
        $this->windowDurations = 0.0;
        $this->windowAnswers = 0;
        $this->windowInFlight = 0.0;
        $this->windowFull = 0.0;
        $this->windowShortfall = [];
        $this->windowSamples = [];
    }

    private function closeWhenDue($now)
    {
        if ($this->windowAnswers === 0) {
            return;
        }

        $elapsed = $now - $this->windowStart;
        if ($elapsed <= 0 || $elapsed < $this->windowDurations / $this->windowAnswers) {
            return;
        }

        if ($this->windowAnswers < $this->requiredAnswers() && !$this->fastLinkWindow($elapsed)) {
            return;
        }

        $bytes = $this->windowBytes;
        $secondsPerByte = $bytes > 0 ? $this->windowDurations / $bytes : 0.0;
        if ($secondsPerByte > 0 && ($this->lowestSecondsPerByte === null || $secondsPerByte < $this->lowestSecondsPerByte)) {
            $this->lowestSecondsPerByte = $secondsPerByte;
        }

        $inFlight = $this->windowInFlight / $elapsed;
        $queue = $secondsPerByte > 0 ? max(0.0, $inFlight * (1 - $this->lowestSecondsPerByte / $secondsPerByte)) : 0.0;
        $reason = null;
        foreach ($this->windowShortfall as $name => $seconds) {
            if ($reason === null || $seconds > $this->windowShortfall[$reason]) {
                $reason = $name;
            }
        }

        $window = [
            'rate' => $bytes / $elapsed, 'queue' => $queue, 'in_flight' => $inFlight, 'judged' => $this->windowFull / $elapsed >= self::OCCUPANCY,
            'reason' => $reason, 'samples' => $this->windowSamples, 'seconds' => $elapsed,
        ];
        $this->openWindow($now);
        if (!$this->policy['adaptive']) {
            return;
        }

        if ($this->warm) {
            $this->warmWindow($window, $now);
        } else {
            $this->ordinaryWindow($window, $now);
        }
    }

    // Whether the open warm window, $elapsed seconds long, already shows the fast link: as many answers as the target,
    // at least 96 MiB/s, with the target filled for 75% of its time. It closes then without the extra samples a raise or
    // a rest needs, as the warm start ends at that rate; whether it closed at least its answers' mean duration after it
    // opened is the caller's to check.
    private function fastLinkWindow($elapsed)
    {
        return $this->warm && $this->policy['fast_link'] && $this->windowAnswers >= $this->target
            && $this->windowBytes / $elapsed >= self::FAST_LINK_BYTES_PER_SECOND && $this->windowFull / $elapsed >= self::OCCUPANCY;
    }

    // The accepted answers the open window needs before it may close.
    private function requiredAnswers()
    {
        if (!$this->warm) {
            return max(self::WINDOW_ATTEMPTS, $this->target);
        }

        switch ($this->warmPhase) {
            case 'connect':
                return $this->target;
            case 'baseline':
                $added = $this->nextAddition();
                return $this->sampledAnswers($added, $this->target + $added);
            case 'probe':
                return $this->cohort === 'settled' ? $this->sampledAnswers($this->warmAdded, $this->target) : $this->target;
            default:
                return max(self::WINDOW_ATTEMPTS, $this->target);
        }
    }

    // The answers a warm window judging a raise of $added parts to $raised needs: at least the target, plus the
    // reference's bounded sampling heuristic, from the relative variation of the judged windows so far and this one.
    private function sampledAnswers($added, $raised)
    {
        list($squares, $freedom) = self::relativeVariation($this->windowSamples);
        $squares += $this->relativeSquares;
        $freedom += $this->degreesOfFreedom;
        if ($freedom <= 0) {
            $requested = 2;
        } else {
            $variation = 0.0;
            if ($squares > 0) {
                $a = 2 / (9 * $freedom);
                $h = 1 - $a - 2 * sqrt($a);
                $variation = $h > 0 ? $squares / ($freedom * pow($h, 3)) : INF;
            }

            $value = is_infinite($variation) ? INF : 2 * $variation * pow($raised * 2 / (0.5 * max(1, $added)), 2);
            $requested = $value >= self::WINDOW_ATTEMPTS ? self::WINDOW_ATTEMPTS : max(2, (int) ceil($value));
        }

        return max($this->target, min(self::WINDOW_ATTEMPTS, $requested));
    }

    private function nextAddition()
    {
        return $this->afterRest ? 1 : max(1, (int) ceil($this->target / 2));
    }

    private function warmWindow(array $window, $now)
    {
        if (!$window['judged']) {
            $this->decide('window_not_judged', $now, ['reason' => $window['reason'], 'bytes_per_second' => $window['rate']]);
            return;
        }

        if ($this->policy['fast_link'] && $window['rate'] >= self::FAST_LINK_BYTES_PER_SECOND) {
            $this->setTarget($this->policy['start']);
            $this->endWarm();
            $this->fastLink = true;
            $this->decide('warm_fast_link', $now, ['bytes_per_second' => $window['rate']]);
            return;
        }

        list($squares, $freedom) = self::relativeVariation($window['samples']);
        $this->relativeSquares += $squares;
        $this->degreesOfFreedom += $freedom;
        $median = self::median($window['samples']);
        if ($median !== null && (!isset($this->fastestMedian[$this->target]) || $median < $this->fastestMedian[$this->target])) {
            $this->fastestMedian[$this->target] = $median;
        }

        if ($this->warmPhase === 'connect') {
            $this->warmPhase = 'baseline';
        } elseif ($this->warmPhase === 'baseline') {
            $this->tryRaise($window, $median, $now);
        } elseif ($this->warmPhase === 'probe') {
            if ($this->cohort === 'settled' || $this->answersSinceRaise >= max(self::WINDOW_ATTEMPTS, $this->target)) {
                $this->judgeRaise($window, $median, $now);
            }
        } elseif (--$this->restRemaining <= 0) {
            $this->tryRaise($window, $median, $now);
        }
    }

    // From a baseline or the last rest window: rest again when it was queued, or raise.
    private function tryRaise(array $window, $median, $now)
    {
        $added = $this->nextAddition();
        $queue = $this->warmQueue($window, $median, $this->target);
        if ($queue >= self::WARM_QUEUE * $added) {
            $this->warmPhase = 'rest';
            $this->restRemaining = self::HOLD_WINDOWS + self::GROWTH_INTERVAL_WINDOWS;
            $this->afterRest = true;
            $this->decide('warm_queued', $now, ['bytes_per_second' => $window['rate'], 'queue_estimate' => $queue]);
            return;
        }

        $this->raise($added, $window['rate'], $now, $queue);
    }

    private function raise($added, $baseline, $now, $queue)
    {
        $to = min($this->policy['start'], $this->target + $added);
        if ($to <= $this->target) {
            $this->endWarm();
            $this->decide('warm_settled', $now);
            return;
        }

        $this->warmFrom = $this->target;
        $this->warmAdded = $to - $this->target;
        $this->warmBaseline = $baseline;
        $this->warmPhase = 'probe';
        $this->cohort = null;
        $this->answersSinceRaise = 0;
        $this->afterRest = false;
        $this->setTarget($to);
        $this->decide('warm_raised', $now, ['from' => $this->warmFrom, 'baseline_bytes_per_second' => $baseline, 'queue_estimate' => $queue]);
    }

    private function judgeRaise(array $window, $median, $now)
    {
        $from = $this->warmFrom;
        $added = $this->warmAdded;
        $needed = $this->warmBaseline * (1 + self::WARM_GAIN * $added / $from);
        $queue = $this->warmQueue($window, $median, $from);
        $details = ['from' => $from, 'bytes_per_second' => $window['rate'], 'needed_bytes_per_second' => $needed, 'queue_estimate' => $queue];
        if ($window['rate'] >= $needed && $queue < self::WARM_QUEUE * $added) {
            $this->decide('warm_probe_kept', $now, $details);
            if ($this->target >= $this->policy['start']) {
                $this->endWarm();
                $this->decide('warm_settled', $now);
                return;
            }

            $this->raise(max(1, (int) ceil($this->target / 2)), $window['rate'], $now, $queue);
            return;
        }

        $this->setTarget($from);
        $this->epoch++;
        $this->warmPhase = 'rest';
        $this->restRemaining = self::HOLD_WINDOWS + self::GROWTH_INTERVAL_WINDOWS;
        $this->afterRest = true;
        $this->cohort = null;
        $this->decide('warm_probe_withdrawn', $now, $details);
    }

    // The warm queue estimate: average in flight * (1 - the fastest judged median seconds per byte at $level / this
    // window's median).
    private function warmQueue(array $window, $median, $level)
    {
        if ($median === null || $median <= 0 || !isset($this->fastestMedian[$level])) {
            return 0.0;
        }

        return max(0.0, $window['in_flight'] * (1 - $this->fastestMedian[$level] / $median));
    }

    private function endWarm()
    {
        $this->warm = false;
        $this->warmPhase = null;
        $this->cohort = null;
        $this->probe = null;
        $this->levels = [];
        $this->hold = 0;
        $this->growthCountdown = self::GROWTH_INTERVAL_WINDOWS;
    }

    private function ordinaryWindow(array $window, $now)
    {
        if (!$window['judged']) {
            $this->decide('window_not_judged', $now, ['reason' => $window['reason'], 'bytes_per_second' => $window['rate']]);
            return;
        }

        $rate = $window['rate'];
        if ($this->policy['fast_link'] && $rate >= self::FAST_LINK_BYTES_PER_SECOND) {
            $this->fastLink = true;
        }

        if (!$this->unlocked && $this->policy['soft'] < $this->policy['max'] && $this->acceptedBytes >= self::SOFT_CEILING_UNLOCK_BYTES && $rate >= self::FAST_LINK_BYTES_PER_SECOND) {
            $this->unlocked = true;
            $this->decide('soft_ceiling_unlocked', $now, ['bytes_per_second' => $rate]);
        }

        if ($this->probe !== null) {
            $this->judgeProbe($window, $now);
            return;
        }

        $previous = $this->levelMean($this->target);
        $this->recordLevel($this->target, $rate);
        if ($window['queue'] > $this->policy['queue_backoff'] && $previous !== null && $rate < $previous) {
            $to = max($this->backoffFloor('latency_floor'), (int) floor($this->target * (1 - self::LATENCY_SHRINK)));
            if ($to < $this->target) {
                $this->setTarget($to);
                $this->hold = self::HOLD_WINDOWS;
                $this->decide('latency_backoff', $now, ['bytes_per_second' => $rate, 'queue_estimate' => $window['queue']]);
                return;
            }
        }

        if ($this->hold > 0) {
            $this->hold--;
            return;
        }

        if (--$this->growthCountdown > 0) {
            return;
        }

        $this->growthCountdown = self::GROWTH_INTERVAL_WINDOWS;
        $ceiling = $this->unlocked ? $this->policy['max'] : $this->policy['soft'];
        if ($this->target >= $ceiling) {
            return;
        }

        if ($window['queue'] > $this->policy['queue_suppress']) {
            $this->decide('growth_suppressed', $now, ['queue_estimate' => $window['queue']]);
            return;
        }

        $to = min($ceiling, $this->target + $this->policy['step']);
        $this->probe = ['from' => $this->target, 'to' => $to, 'misses' => 0];
        $this->setTarget($to);
        $this->decide('probe_started', $now, ['from' => $this->probe['from'], 'bytes_per_second' => $rate]);
    }

    private function judgeProbe(array $window, $now)
    {
        $probe = $this->probe;
        $base = $this->levelMean($probe['from']);
        $rate = $window['rate'];
        if ($base === null || ($this->fastLink && $probe['to'] <= $this->policy['probe_floor'])) {
            $earned = true;
        } elseif ($this->unlocked && $probe['from'] >= $this->policy['soft'] && $probe['to'] <= $this->policy['plateau']) {
            $earned = $rate >= $base * (1 - self::TOLERATED_LOSS);
        } else {
            $gain = self::PROBE_GAIN;
            if ($probe['to'] > $this->policy['plateau']) {
                $gain = max(self::PROBE_GAIN, self::PLATEAU_GAIN_PER_PART * ($probe['to'] - max($probe['from'], $this->policy['plateau'])));
            }

            $earned = $rate >= $base * (1 + $gain);
        }

        if ($earned) {
            $this->probe = null;
            $this->recordLevel($this->target, $rate);
            $this->growthCountdown = self::GROWTH_INTERVAL_WINDOWS;
            $this->decide('probe_kept', $now, ['from' => $probe['from'], 'bytes_per_second' => $rate, 'baseline_bytes_per_second' => $base]);
            return;
        }

        $this->probe['misses']++;
        if ($this->probe['misses'] < self::PROBE_WINDOWS) {
            return;
        }

        $shrunk = max($this->backoffFloor('throughput_floor'), (int) floor($probe['to'] * (1 - self::REJECTED_PROBE_SHRINK)));
        $this->probe = null;
        $this->setTarget(max($this->policy['min'], min($probe['from'], $shrunk)));
        $this->hold = self::HOLD_WINDOWS;
        $this->decide('probe_rejected', $now, ['from' => $probe['from'], 'bytes_per_second' => $rate, 'baseline_bytes_per_second' => $base]);
    }

    // A backoff's floor: the policy's, or its probe floor once a fast link was measured; never above the target.
    private function backoffFloor($name)
    {
        $floor = $this->fastLink ? $this->policy['probe_floor'] : $this->policy[$name];
        return max($this->policy['min'], min($floor, $this->target));
    }

    private function recordLevel($target, $rate)
    {
        if (!isset($this->levels[$target])) {
            $this->levels[$target] = [0.0, 0];
        }

        $this->levels[$target][0] += $rate;
        $this->levels[$target][1]++;
    }

    private function levelMean($target)
    {
        return isset($this->levels[$target]) && $this->levels[$target][1] > 0 ? $this->levels[$target][0] / $this->levels[$target][1] : null;
    }

    private function extendCooldown($until, $now)
    {
        if ($until > $this->cooldownUntil) {
            $this->cooldownUntil = (float) $until;
            if ($until > $now) {
                $this->decide('cooldown_extended', $now, ['until_seconds' => $until]);
            }
        }
    }

    private function setTarget($target)
    {
        $this->target = (int) $target;
        $this->peakTarget = max($this->peakTarget, $this->target);
    }

    private function decide($label, $now, array $details = [])
    {
        $this->decisions[] = [
            'decision' => $label, 'policy' => $this->policy['name'], 'target' => $this->target, 'epoch' => $this->epoch,
            'in_flight' => count($this->running), 'at_seconds' => (float) $now,
        ] + $details;
    }

    // The squared deviations of $samples from their mean over its square, and their degrees of freedom.
    private static function relativeVariation(array $samples)
    {
        $count = count($samples);
        if ($count < 2) {
            return [0.0, 0];
        }

        $mean = array_sum($samples) / $count;
        if ($mean <= 0) {
            return [0.0, $count - 1];
        }

        $squares = 0.0;
        foreach ($samples as $sample) {
            $squares += ($sample - $mean) * ($sample - $mean);
        }

        return [$squares / ($mean * $mean), $count - 1];
    }

    private static function median(array $samples)
    {
        $count = count($samples);
        if ($count === 0) {
            return null;
        }

        sort($samples);
        $middle = (int) floor($count / 2);
        return $count % 2 === 1 ? $samples[$middle] : ($samples[$middle - 1] + $samples[$middle]) / 2;
    }
}
