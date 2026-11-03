<?php

declare(strict_types=1);

// The default suite's bootstrap: no inherited Files.com configuration, the test dependencies in test/vendor, and the
// guarded SDK state (support/SuiteGuard.php) before the first test. api-scenarios.sh gives the generated scenario
// tests test/vendor/autoload.php instead, so that the settings its launcher gives them are kept.

require __DIR__ . '/support/UnboundRequestException.php';
require __DIR__ . '/support/SuiteGuard.php';
require __DIR__ . '/support/UnboundTransportGuard.php';
require __DIR__ . '/support/Fixtures.php';

try {
    \Files\Test\SuiteGuard::clearInheritedConfiguration();
} catch (\UnexpectedValueException $unclear) {
    fwrite(STDERR, 'The default suite cannot clear its inherited Files.com settings: ' . $unclear->getMessage() . "\n");
    exit(1);
}

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "The default suite needs test/vendor/autoload.php: install the test dependencies first.\n");
    exit(1);
}
require $autoload;

\Files\Test\SuiteGuard::recordDefaults();
\Files\Test\SuiteGuard::resetSdkState();
