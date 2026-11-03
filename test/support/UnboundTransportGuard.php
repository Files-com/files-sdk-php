<?php

declare(strict_types=1);

namespace Files\Test;

/**
 * Gives each test of a class the suite's guarded SDK state (SuiteGuard), and fails a test that sent a request
 * that no handler of its own answered.
 *
 * PHPUnit runs a class's own before and after methods and these in different orders in different versions. A class
 * whose own before method keeps or changes the SDK's settings calls bindRefusingTransport() first, which binds once a
 * test, and one whose own after method restores them calls assertNoUnboundRequest() last; each may then run twice.
 */
trait UnboundTransportGuard
{
    private $unboundTransportBound = false;

    /**
     * @before
     */
    public function bindRefusingTransport()
    {
        if (!$this->unboundTransportBound) {
            $this->unboundTransportBound = true;
            SuiteGuard::resetSdkState();
        }
    }

    /**
     * @after
     */
    public function assertNoUnboundRequest()
    {
        try {
            $requests = SuiteGuard::takeUnboundRequests();
            if ($requests !== []) {
                $this->fail('A request reached no test handler: ' . implode(', ', $requests));
            }
        } finally {
            $this->unboundTransportBound = false;
        }
    }
}
