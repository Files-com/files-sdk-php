<?php

declare(strict_types=1);

// Installed-copy consumer of the users-find scenario: finds the scenario's expected user through the public
// User::find of the SDK as test/vendor installs it, against the generated simulator, and checks the returned User's
// id, username and name. api-scenarios.sh runs it, from test/, through the mock server's launcher with
// FILES_SESSION_ENV unset, so that the SDK loads through test/vendor/autoload.php as an installed copy does:
//
//   env -u FILES_SESSION_ENV ruby with-scenario.rb --scenario users-find --out DIR -- php shared-mock-users-find.php
//
// It takes only what the launcher gives it, each checked before the SDK loads:
//   FILES_MOCK_SERVER_URL     the simulator's address, http://127.0.0.1:PORT, set with Files::setBaseUrl()
//   FILES_MOCK_RUN_ID         the run's ID
//   FILES_MOCK_API_KEY        filesmock and the run ID's letters and digits, a synthetic key set with setApiKey()
//   FILES_MOCK_SCENARIO       the scenario's name
//   FILES_MOCK_SCENARIO_FILE  that scenario, whose expected_user (id, username, name) is the user to find
//   FILES_MOCK_OUT            the launcher's output directory, which receives users-find-receipt.json
//
// The receipt records the run, the files the SDK's classes loaded from, and the expected and actual user; never the
// API key. Exit status: 0 when the User matches; 1 when it differs, the call fails, the SDK did not load from
// test/vendor or the receipt cannot be written; 2 when an input is missing or unusable.

$usageError = function ($message) {
    fwrite(STDERR, "shared-mock users-find: $message\n");
    exit(2);
};

if (getenv('FILES_SESSION_ENV') !== false) {
    $usageError('FILES_SESSION_ENV is set; unset it, so that the SDK loads as an installed copy does.');
}

$inputs = [];
$names = [
    'FILES_MOCK_SERVER_URL',
    'FILES_MOCK_RUN_ID',
    'FILES_MOCK_API_KEY',
    'FILES_MOCK_SCENARIO',
    'FILES_MOCK_SCENARIO_FILE',
    'FILES_MOCK_OUT',
];
foreach ($names as $name) {
    $value = getenv($name);
    if (!is_string($value) || $value === '') {
        $usageError("$name is not set; run this through the mock server's launcher.");
    }
    $inputs[$name] = $value;
}

if (
    !preg_match('@\Ahttp://127\.0\.0\.1:([1-9][0-9]{0,4})\z@', $inputs['FILES_MOCK_SERVER_URL'], $port)
    || (int) $port[1] > 65535
) {
    $usageError('FILES_MOCK_SERVER_URL is not a loopback address of the form http://127.0.0.1:PORT.');
}
if (!preg_match('/\A[A-Za-z0-9._-]{1,64}\z/', $inputs['FILES_MOCK_RUN_ID'])) {
    $usageError('FILES_MOCK_RUN_ID is not a run ID the launcher makes.');
}
if ($inputs['FILES_MOCK_API_KEY'] !== 'filesmock' . preg_replace('/[^A-Za-z0-9]/', '', $inputs['FILES_MOCK_RUN_ID'])) {
    $usageError('FILES_MOCK_API_KEY is not the synthetic key the launcher makes for this run.');
}

$scenarioFile = $inputs['FILES_MOCK_SCENARIO_FILE'];
$scenarioJson = is_file($scenarioFile) ? file_get_contents($scenarioFile) : false;
$scenario = is_string($scenarioJson) ? json_decode($scenarioJson, true) : null;
$expected = is_array($scenario) && isset($scenario['expected_user']) ? $scenario['expected_user'] : null;
if (
    !is_array($scenario) || !isset($scenario['name']) || $scenario['name'] !== $inputs['FILES_MOCK_SCENARIO']
    || !is_array($expected) || !isset($expected['id'], $expected['username'], $expected['name'])
    || !is_int($expected['id']) || $expected['id'] < 1
    || !is_string($expected['username']) || $expected['username'] === '' || !is_string($expected['name'])
) {
    $usageError('FILES_MOCK_SCENARIO_FILE is not this scenario with an expected_user id, username and name.');
}

$receiptPath = $inputs['FILES_MOCK_OUT'] . '/users-find-receipt.json';
if (!is_dir($inputs['FILES_MOCK_OUT']) || file_exists($receiptPath)) {
    $usageError('FILES_MOCK_OUT is not a directory without a users-find-receipt.json.');
}

$vendor = __DIR__ . '/vendor';
$autoload = $vendor . '/autoload.php';
if (!is_file($autoload)) {
    $usageError('test/vendor/autoload.php is missing: install the test dependencies first.');
}
require $autoload;

$call = 'User::find(' . var_export($expected['id'], true) . ')';
$origins = [];
$actual = null;
$failure = null;
try {
    $vendorRoot = realpath($vendor);
    foreach (['Files\\Files', 'Files\\Model\\User'] as $class) {
        $file = (new \ReflectionClass($class))->getFileName();
        $origins[$class] = $file;
        $real = is_string($file) ? realpath($file) : false;
        if ($vendorRoot === false || $real === false || strpos($real, $vendorRoot . DIRECTORY_SEPARATOR) !== 0) {
            $failure = "$class loaded from " . var_export($file, true) . ', not from the installed copy in test/vendor';
        }
    }

    if ($failure === null) {
        $baseUrl = \Files\Files::getBaseUrl();
        $apiKey = \Files\Files::getApiKey();
        \Files\Files::setBaseUrl($inputs['FILES_MOCK_SERVER_URL']);
        \Files\Files::setApiKey($inputs['FILES_MOCK_API_KEY']);
        try {
            $user = \Files\Model\User::find($expected['id']);
            $actual = ['id' => $user->getId(), 'username' => $user->getUsername(), 'name' => $user->getName()];
        } finally {
            \Files\Files::setBaseUrl($baseUrl);
            \Files\Files::setApiKey($apiKey);
        }
    }
} catch (\Exception $error) {
    $failure = "$call failed with " . get_class($error) . ': ' . $error->getMessage();
} catch (\Throwable $error) {
    $failure = "$call failed with " . get_class($error) . ': ' . $error->getMessage();
}

if ($failure === null) {
    $differences = [];
    foreach (['id', 'username', 'name'] as $field) {
        if ($actual[$field] !== $expected[$field]) {
            $differences[] = "$field " . var_export($actual[$field], true)
                . ', expected ' . var_export($expected[$field], true);
        }
    }
    if ($differences) {
        $failure = "$call returned a different user: " . implode('; ', $differences);
    }
}

$receipt = [
    'format' => 'files-php-users-find-receipt/1',
    'scenario' => $inputs['FILES_MOCK_SCENARIO'],
    'run_id' => $inputs['FILES_MOCK_RUN_ID'],
    'instance' => getenv('FILES_MOCK_INSTANCE') === false ? null : getenv('FILES_MOCK_INSTANCE'),
    'schema_sha256' => getenv('FILES_MOCK_SCHEMA_SHA256') === false ? null : getenv('FILES_MOCK_SCHEMA_SHA256'),
    'server_url' => $inputs['FILES_MOCK_SERVER_URL'],
    'php_version' => PHP_VERSION,
    'files_session_env' => 'absent',
    'autoload' => realpath($autoload),
    'class_origins' => $origins,
    'call' => $call,
    'expected_user' => ['id' => $expected['id'], 'username' => $expected['username'], 'name' => $expected['name']],
    'actual_user' => $actual,
    'outcome' => $failure === null ? 'passed' : 'failed',
    'failure' => $failure,
];
$json = json_encode($receipt, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$written = false;
if (is_string($json)) {
    $handle = @fopen($receiptPath, 'x');
    if ($handle !== false) {
        $written = fwrite($handle, $json . "\n") === strlen($json) + 1;
        $written = fclose($handle) && $written;
    }
}
if (!$written) {
    fwrite(STDERR, "shared-mock users-find: could not write $receiptPath\n");
    exit(1);
}

if ($failure !== null) {
    fwrite(STDERR, "shared-mock users-find: $failure\n");
    exit(1);
}

echo "shared-mock users-find: $call returned the expected user (id " . var_export($actual['id'], true)
    . ', username ' . var_export($actual['username'], true) . ', name ' . var_export($actual['name'], true) . ")\n";
exit(0);
