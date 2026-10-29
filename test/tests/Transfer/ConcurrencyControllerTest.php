<?php

declare(strict_types=1);

namespace Files\Transfer;

use PHPUnit\Framework\TestCase;

/**
 * The adaptive transfers' controller, given attempts and their answers at controlled times, against the conformance
 * cases of verification/adaptive-upload-spec.md. Times are seconds; a round starts its attempts together and answers
 * each after its duration.
 */
class ConcurrencyControllerTest extends TestCase
{
    const MIB = 1048576;

    // The PHP mapping of the scaled S3 policy: S = min(150, cap), counts scaled by S / 150, the rest unchanged.
    public function testTheScaledPolicyMapsItsCountsToTheRequestCap()
    {
        $policy = ConcurrencyController::storageUpload(64, 'default');
        $this->assertSame(
            ['start' => 64, 'min' => 3, 'max' => 64, 'soft' => 64, 'plateau' => 85, 'step' => 2, 'latency_floor' => 21, 'probe_floor' => 64],
            array_intersect_key($policy, array_flip(['start', 'min', 'max', 'soft', 'plateau', 'step', 'latency_floor', 'probe_floor']))
        );
        $this->assertNear(40.96, $policy['queue_suppress'], 0.001);
        $this->assertNear(68.267, $policy['queue_backoff'], 0.001);

        $reference = ConcurrencyController::storageUpload(1024, 'aggressive');
        $this->assertSame([150, 8, 1024, 150, 200, 4, 50], [$reference['start'], $reference['min'], $reference['max'], $reference['soft'], $reference['plateau'], $reference['step'], $reference['latency_floor']]);

        $generic = ConcurrencyController::generic(64);
        $this->assertSame([4, 1, 16], [$generic['start'], $generic['min'], $generic['max']]);
        $this->assertSame(64, (new ConcurrencyController(ConcurrencyController::download(64), 0.0))->target());
        $this->assertSame(7, (new ConcurrencyController(ConcurrencyController::explicit(64, 7), 0.0))->target());
        $this->assertSame(64, (new ConcurrencyController(ConcurrencyController::storageUpload(64, 'aggressive'), 0.0))->target());
        $this->assertSame(1, (new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), 0.0))->target());
    }

    // One-part baseline 10 MiB/s; target two delivers 14 MiB/s: the probe did not earn its 50% gain, so the target
    // returns to one, in a new epoch, and rests.
    public function testAWarmProbeWithoutTheRequiredGainIsWithdrawn()
    {
        $now = 0.0;
        $controller = $this->warmedToTwo($now);

        $this->round($controller, $now, 2, 7 * self::MIB, 1.0);
        $decision = $this->lastDecision($controller, 'warm_probe_withdrawn');
        $this->assertSame([1, 1], [$controller->target(), $controller->epoch()]);
        $this->assertNear(15 * self::MIB, $decision['needed_bytes_per_second'], 1);
        $this->assertNear(14 * self::MIB, $decision['bytes_per_second'], 1);
    }

    // One-part baseline 10 MiB/s; target two delivers 16 MiB/s, past its gain, with a queue estimate of 0.6, at least
    // half the part added: withdrawn despite the gain.
    public function testAWarmProbeWithItsGainIsWithdrawnWhenRequestsQueue()
    {
        $now = 0.0;
        $controller = $this->warmedToTwo($now);

        // Three in flight for the whole window, two of them answering 8 MiB each in its one second.
        $tokens = [$controller->started($now), $controller->started($now), $controller->started($now)];
        $now += 1.0;
        $controller->accepted($tokens[0], 8 * self::MIB, 1.0, $now);
        $controller->accepted($tokens[1], 8 * self::MIB, 1.0, $now);
        $decision = $this->lastDecision($controller, 'warm_probe_withdrawn');
        $this->assertSame(1, $controller->target());
        $this->assertGreaterThanOrEqual($decision['needed_bytes_per_second'], $decision['bytes_per_second']);
        $this->assertNear(0.6, $decision['queue_estimate'], 0.0001);
    }

    // Target two baseline 20 MiB/s; target three delivers 25 MiB/s, exactly the 25% it needs, with a queue estimate of
    // 0.4: kept, and the next probe raises the target by half again, to five.
    public function testAWarmProbeEarningItsGainIsKeptAndRaisedAgain()
    {
        $now = 0.0;
        $controller = $this->warmedToTwo($now);
        $this->round($controller, $now, 2, 10 * self::MIB, 1.0);
        $this->assertSame(3, $controller->target());
        $this->lastDecision($controller, 'warm_probe_kept');

        // The cohort the raise to three filled, then the window judging it.
        $this->round($controller, $now, 3, 10 * self::MIB, 1.0);
        $perByte = (1 / (10 * self::MIB)) / (1 - 0.4 / 3);
        $tokens = [$controller->started($now), $controller->started($now), $controller->started($now)];
        $now += 1.0;
        foreach ([8738134, 8738133, 8738133] as $index => $bytes) {
            $controller->accepted($tokens[$index], $bytes, $bytes * $perByte, $now);
        }

        $decisions = $controller->takeDecisions();
        $kept = $this->find($decisions, 'warm_probe_kept');
        $this->assertNear(0.4, $kept['queue_estimate'], 0.0001);
        $this->assertNear(25 * self::MIB, $kept['needed_bytes_per_second'], 1);
        $this->assertSame(5, $controller->target());
        $this->assertNotNull($this->find($decisions, 'warm_raised'));
    }

    // The first judged window at 96 MiB/s promotes at once to the effective start: 64 under the default cap, the cap
    // itself under a smaller one.
    public function testAFastLinkPromotesToTheEffectiveStartWithinTheCap()
    {
        foreach ([64 => 64, 10 => 10, 1024 => 150] as $cap => $start) {
            $now = 0.0;
            $controller = new ConcurrencyController(ConcurrencyController::storageUpload($cap, 'default'), $now);
            $this->round($controller, $now, 1, 96 * self::MIB, 1.0);
            $this->lastDecision($controller, 'warm_fast_link');
            $this->assertSame([$start, false], [$controller->target(), $controller->isWarm()], 'cap ' . $cap);
        }
    }

    // A window crossing the fast-link rate while the job's limits kept the target unfilled is not judged: no
    // promotion, and the shortfall is reported.
    public function testAnUnderfilledWindowIsNotJudged()
    {
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $controller->shortfall('shared_limiter', $now);
        $now = 0.6;
        $token = $controller->started($now);
        $controller->shortfall(null, $now);
        $now = 1.0;
        $controller->accepted($token, 96 * self::MIB, 0.4, $now);

        $decision = $this->lastDecision($controller, 'window_not_judged');
        $this->assertSame('shared_limiter', $decision['reason']);
        $this->assertSame([1, true], [$controller->target(), $controller->isWarm()]);
    }

    // Several failures of attempts begun in one epoch shrink the target once; a failure begun in the new epoch can
    // shrink it again. Aggressive starts at the policy's start and still backs off.
    public function testFailuresShrinkTheTargetOncePerEpoch()
    {
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'aggressive'), $now);
        $this->assertSame(64, $controller->target());
        $tokens = [$controller->started($now), $controller->started($now), $controller->started($now)];
        foreach ($tokens as $token) {
            $controller->failed($token, ConcurrencyController::FAILURE, 0.0, $now);
        }

        $this->assertSame([41, 1], [$controller->target(), $controller->epoch()]);
        $this->assertCount(2, $this->all($controller->takeDecisions(), 'failure_from_earlier_epoch'));
        $controller->failed($controller->started($now), ConcurrencyController::FAILURE, 0.0, $now);
        $this->assertSame([26, 2], [$controller->target(), $controller->epoch()]);
    }

    // Throttles begun in one epoch shrink the target once, but every one restarts measurement and extends the cooldown
    // to the later of its end and its Retry-After, never shortening it; attempts wait for its end.
    public function testThrottlesShrinkOncePerEpochAndOnlyExtendTheCooldown()
    {
        $now = 1.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'aggressive'), 0.0);
        $tokens = [$controller->started($now), $controller->started($now), $controller->started($now)];
        $controller->failed($tokens[0], ConcurrencyController::THROTTLE, 2.0, $now);
        $this->assertSame([57, 3.0], [$controller->target(), $controller->cooldownUntil()]);
        $controller->failed($tokens[1], ConcurrencyController::THROTTLE, 0.5, $now);
        $this->assertSame([57, 3.0], [$controller->target(), $controller->cooldownUntil()]);
        $controller->failed($tokens[2], ConcurrencyController::THROTTLE, 5.0, $now);
        $this->assertSame([57, 6.0], [$controller->target(), $controller->cooldownUntil()]);
        $this->assertFalse($controller->admits(5.9));
        $this->assertTrue($controller->admits(6.0));
    }

    // Answers that arrive during a cooldown are not measurements of capacity: they close no window. After it, they do.
    public function testCompletionsDuringACooldownAreNotMeasured()
    {
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(2, 'aggressive'), $now);
        $controller->failed($controller->started($now), ConcurrencyController::THROTTLE, 100.0, $now);
        $this->assertSame(1, $controller->target());
        $controller->takeDecisions();

        for ($answer = 0; $answer < 40; $answer++) {
            $this->round($controller, $now, 1, self::MIB, 1.0);
        }

        $this->assertSame([], $controller->takeDecisions());
        // After it: a window of 32 answers holds after the shrink, and the next one probes.
        $now = 100.0;
        for ($answer = 0; $answer < 70; $answer++) {
            $this->round($controller, $now, 1, self::MIB, 1.0);
        }

        $this->lastDecision($controller, 'probe_started');
        $this->assertSame(2, $controller->target());
    }

    // A failure during a warm probe abandons it: one begun in the current epoch shrinks the target and rests; one begun
    // in an earlier epoch leaves the raised target resting there.
    public function testAFailureDuringAWarmProbeAbandonsItAndRests()
    {
        $now = 0.0;
        $controller = $this->warmedToTwo($now);
        $cohort = [$controller->started($now), $controller->started($now)];
        $controller->failed($cohort[0], ConcurrencyController::FAILURE, 0.0, $now);
        $this->lastDecision($controller, 'failure_shrink');
        $this->assertSame([1, 1], [$controller->target(), $controller->epoch()]);

        // An attempt of epoch 0 still running when a failure starts epoch 1; two rest windows of 32 answers later the
        // target is raised to two, and then that old attempt fails.
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $old = $controller->started($now);
        $controller->failed($controller->started($now), ConcurrencyController::FAILURE, 0.0, $now);
        $this->lastDecision($controller, 'failure_shrink');
        $this->assertSame([1, 1], [$controller->target(), $controller->epoch()]);
        for ($answer = 0; $answer < 64; $answer++) {
            $this->round($controller, $now, 1, 10 * self::MIB, 1.0);
        }

        $this->lastDecision($controller, 'warm_raised');
        $this->assertSame(2, $controller->target());
        $controller->failed($old, ConcurrencyController::FAILURE, 0.0, $now);
        $this->lastDecision($controller, 'failure_from_earlier_epoch');
        $this->assertSame([2, 1, true], [$controller->target(), $controller->epoch(), $controller->isWarm()]);
    }

    // Fixed concurrency stays where it is set, through failures and throttles, which still set a cooldown.
    public function testAFixedTargetNeverChanges()
    {
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::fixed(5), $now);
        $controller->failed($controller->started($now), ConcurrencyController::FAILURE, 0.0, $now);
        $controller->failed($controller->started($now), ConcurrencyController::THROTTLE, 1.0, $now);
        for ($answer = 0; $answer < 40; $answer++) {
            $this->round($controller, $now, 5, self::MIB, 1.0);
        }

        $this->assertSame([5, 0], [$controller->target(), $controller->epoch()]);
        $this->assertSame(1.0, $controller->cooldownUntil());
    }

    // A fast link ends the warm start as soon as a fully occupied window of target answers reaches 96 MiB/s, in a rest
    // after a withdrawn probe and in a baseline alike, without waiting for the extra samples those windows need below it.
    public function testAFastLinkPromotesFromARestOrABaselineWithoutExtraSamples()
    {
        $now = 0.0;
        $controller = $this->warmedToTwo($now);
        $this->round($controller, $now, 2, 7 * self::MIB, 1.0);
        $this->lastDecision($controller, 'warm_probe_withdrawn');
        $this->round($controller, $now, 1, 96 * self::MIB, 1.0);
        $this->lastDecision($controller, 'warm_fast_link');
        $this->assertSame([64, false], [$controller->target(), $controller->isWarm()]);

        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $this->round($controller, $now, 1, 10 * self::MIB, 1.0);
        $this->round($controller, $now, 1, 96 * self::MIB, 1.0);
        $this->lastDecision($controller, 'warm_fast_link');
        $this->assertSame([64, false], [$controller->target(), $controller->isWarm()]);

        // Below the rate, the rest still waits for its 32 answers.
        $now = 0.0;
        $controller = $this->warmedToTwo($now);
        $this->round($controller, $now, 2, 7 * self::MIB, 1.0);
        $controller->takeDecisions();
        $this->round($controller, $now, 1, 95 * self::MIB, 1.0);
        $this->assertSame([], $controller->takeDecisions());
        $this->assertSame([1, true], [$controller->target(), $controller->isWarm()]);
    }

    // A failure of an attempt begun before the last shrink leaves an ordinary probe in place: it goes on being judged,
    // and is rejected after two windows without its gain. A throttle from an earlier epoch extends the cooldown without
    // shrinking the target again.
    public function testAnOrdinaryProbeOutlivesAFailureFromAnEarlierEpoch()
    {
        $now = 0.0;
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'aggressive'), $now);
        $old = $controller->started($now);
        $older = $controller->started($now);
        $controller->failed($controller->started($now), ConcurrencyController::FAILURE, 0.0, $now);
        $this->assertSame([41, 1], [$controller->target(), $controller->epoch()]);

        // A hold window and a growth window at 41, at one rate; then the probe to 43.
        $this->round($controller, $now, 41, 43000, 1.0);
        $this->round($controller, $now, 41, 43000, 1.0);
        $this->lastDecision($controller, 'probe_started');
        $this->assertSame(43, $controller->target());

        $controller->failed($old, ConcurrencyController::FAILURE, 0.0, $now);
        $this->lastDecision($controller, 'failure_from_earlier_epoch');
        $this->assertSame([43, 1], [$controller->target(), $controller->epoch()]);

        // The same rate at 43, short of the probe's 1% gain, twice: rejected, below where it started.
        $this->round($controller, $now, 43, 41000, 1.0);
        $this->round($controller, $now, 43, 41000, 1.0);
        $rejected = $this->lastDecision($controller, 'probe_rejected');
        $this->assertSame(41, $rejected['from']);
        $this->assertSame([39, 1], [$controller->target(), $controller->epoch()]);

        $controller->failed($older, ConcurrencyController::THROTTLE, 4.0, $now);
        $this->lastDecision($controller, 'throttle_from_earlier_epoch');
        $this->assertSame([39, 1, $now + 4.0], [$controller->target(), $controller->epoch(), $controller->cooldownUntil()]);
    }

    // The job's own file-preparation decision (TransferEngine::preparationLimit), given real controllers: the warm
    // start's promotion to its start raises the remembered limit as its earlier rises did, though the scope is no longer
    // warm once promoted; a warm scope above the lowest target says nothing of it; and the remembered limit otherwise
    // moves one file each 100 ms.
    public function testFilePreparationFollowsTheLowestTargetAndItsOwnWarmStart()
    {
        // A Default upload scope warm at one, then promoted to 64 by a fast link: the remembered six become 128.
        $now = 0.0;
        $warm = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $memory = TransferEngine::preparationMemory(128);
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm], 128, 0, false, $now);
        $this->assertSame([6, 1, true], [$limit, $memory['observed_target'], $memory['observed_warm']]);
        $this->round($warm, $now, 1, 96 * self::MIB, 1.0);
        $this->assertSame([64, false], [$warm->target(), $warm->isWarm()]);
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm], 128, 0, false, $now);
        $this->assertSame([128, 64, false], [$limit, $memory['observed_target'], $memory['observed_warm']]);

        // Then the paced steps alone: one file each 100 ms back toward the derived 81 while enough attempts are ready.
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm], 128, 17, true, $now + 0.1);
        $this->assertSame(127, $limit);
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm], 128, 17, true, $now + 0.15);
        $this->assertSame(127, $limit);
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm], 128, 17, true, $now + 0.25);
        $this->assertSame(126, $limit);

        // A scope warm at two above an ordinary one at one: the ordinary scope holds the lowest target, so its rise to
        // three, which leaves the warm scope lowest, scales nothing.
        $now = 0.0;
        $warm = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $ordinary = new ConcurrencyController(ConcurrencyController::explicit(64, 1), $now);
        for ($round = 0; $round < 3; $round++) {
            $this->round($warm, $now, 1, 10 * self::MIB, 1.0);
        }

        $this->assertSame([2, true, 1, false], [$warm->target(), $warm->isWarm(), $ordinary->target(), $ordinary->isWarm()]);
        $memory = TransferEngine::preparationMemory(128);
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm, $ordinary], 128, 0, false, $now);
        $this->assertSame([6, 1, false], [$limit, $memory['observed_target'], $memory['observed_warm']]);
        for ($round = 0; $round < 32; $round++) {
            $this->round($ordinary, $now, 1, 10 * self::MIB, 1.0);
        }

        $this->assertSame(3, $ordinary->target());
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm, $ordinary], 128, 0, false, $now);
        $this->assertSame([6, 2, true], [$limit, $memory['observed_target'], $memory['observed_warm']]);

        // Fewer attempts ready than the target wants while transfers wait to start: one more file each 100 ms.
        list($limit, $memory) = TransferEngine::preparationLimit($memory, [$warm, $ordinary], 128, 0, true, $now + 0.1);
        $this->assertSame(7, $limit);
        list($limit) = TransferEngine::preparationLimit($memory, [$warm, $ordinary], 128, 0, true, $now + 0.15);
        $this->assertSame(7, $limit);
    }

    // $actual within $delta of $expected (PHPUnit 5 and 8 alike).
    private function assertNear($expected, $actual, $delta)
    {
        $this->assertTrue(abs($expected - $actual) <= $delta, $actual . ' is not within ' . $delta . ' of ' . $expected);
    }

    // A Default controller warmed to a probe at two: its connection window and a two-answer baseline at 10 MiB/s,
    // then the cohort the raise filled.
    private function warmedToTwo(&$now)
    {
        $controller = new ConcurrencyController(ConcurrencyController::storageUpload(64, 'default'), $now);
        $this->round($controller, $now, 1, 10 * self::MIB, 1.0);
        $this->round($controller, $now, 1, 10 * self::MIB, 1.0);
        $this->round($controller, $now, 1, 10 * self::MIB, 1.0);
        $this->lastDecision($controller, 'warm_raised');
        $this->assertSame(2, $controller->target());
        $this->round($controller, $now, 2, 10 * self::MIB, 1.0);
        return $controller;
    }

    // Starts $attempts at $now and answers each with $bytes after $seconds.
    private function round(ConcurrencyController $controller, &$now, $attempts, $bytes, $seconds)
    {
        $tokens = [];
        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            $tokens[] = $controller->started($now);
        }

        $now += $seconds;
        foreach ($tokens as $token) {
            $controller->accepted($token, $bytes, $seconds, $now);
        }
    }

    private function lastDecision(ConcurrencyController $controller, $label)
    {
        $decision = $this->find($controller->takeDecisions(), $label);
        $this->assertNotNull($decision, 'No ' . $label . ' decision');
        return $decision;
    }

    private function find(array $decisions, $label)
    {
        $found = $this->all($decisions, $label);
        return $found ? $found[count($found) - 1] : null;
    }

    private function all(array $decisions, $label)
    {
        return array_values(array_filter($decisions, function ($decision) use ($label) {
            return $decision['decision'] === $label;
        }));
    }
}
