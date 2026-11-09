<?php

declare(strict_types=1);

// The shared failure-case matrix (the mock server's scenarios/failure-cases.json) through this SDK's public calls.
// api-scenarios.sh runs it, from test/, through the mock server's launcher, which resets the simulator to the
// scenario's users and adds its fault rules before this starts:
//
//   ruby with-scenario.rb --scenario failure-cases --out DIR -- php failure-cases.php
//
// Each case makes its calls with the SDK's own CurlHandler, as User::find and User->update, with
// Files::$maxNetworkRetries set to the definition's max_network_retries, and compares what each call returned or threw
// with the case's expectation: an API error's status, type and message (the validation errors and the seeded random
// HTTP 422 faults), a dropped connection that got no answer once the retries are spent (ApiConnectionException), or the
// user found. FILES_MOCK_OUT/failure-cases.json records each case's outcome;
// the launcher's failure cases check then requires every case to have passed and compares the journal and the fault
// rules' decisions with each case's expected wire outcome. Exit status: 0 when every case passed, 1 otherwise, 2 when
// an input is missing.

namespace Files;

use Files\Model\User;

require_once __DIR__ . '/vendor/autoload.php';

$inputs = [];
foreach (['FILES_MOCK_SERVER_URL', 'FILES_MOCK_API_KEY', 'FILES_MOCK_SCENARIO_FILE', 'FILES_MOCK_OUT'] as $name) {
    $value = getenv($name);
    if (!is_string($value) || $value === '') {
        fwrite(STDERR, "failure cases: $name is not set; run this through the mock server's launcher\n");
        exit(2);
    }
    $inputs[$name] = $value;
}
$scenario = json_decode((string) file_get_contents($inputs['FILES_MOCK_SCENARIO_FILE']), true);
$definition = is_array($scenario) && isset($scenario['failure_cases']) ? $scenario['failure_cases'] : null;
if (!is_array($definition) || !isset($definition['cases'], $definition['max_network_retries']) || !is_int($definition['max_network_retries'])) {
    fwrite(STDERR, "failure cases: FILES_MOCK_SCENARIO_FILE defines no failure_cases with max_network_retries\n");
    exit(2);
}

Files::setBaseUrl($inputs['FILES_MOCK_SERVER_URL']);
Files::setApiKey($inputs['FILES_MOCK_API_KEY']);
Files::$maxNetworkRetries = $definition['max_network_retries'];

// What one call did, in the expectation's terms.
function failureCaseCall(array $case)
{
    try {
        if ($case['call'] === 'users.update') {
            $user = new User(['id' => $case['user_id']]);
            $user->update($case['params']);
            return ['updated' => true];
        }
        if ($case['call'] === 'users.find') {
            $user = User::find($case['user_id']);
            return ['user' => ['id' => $user->getId(), 'username' => $user->getUsername()]];
        }
        return ['unknown call' => $case['call']];
    } catch (Exception\ApiConnectionException $error) {
        return ['error' => ['network' => true], 'class' => get_class($error)];
    } catch (Exception\ApiException $error) {
        return ['error' => ['status' => $error->getHttpCode(), 'type' => $error->getType(), 'message' => $error->getError()], 'class' => get_class($error)];
    } catch (\Exception $error) {
        return ['unexpected' => get_class($error) . ': ' . $error->getMessage()];
    }
}

$results = [];
$failed = false;
foreach ($definition['cases'] as $case) {
    $problems = [];
    foreach ($case['expect'] as $index => $expected) {
        $actual = failureCaseCall($case);
        $compared = array_intersect_key($actual, ['user' => 1, 'error' => 1, 'updated' => 1, 'unknown call' => 1, 'unexpected' => 1]);
        if ($compared != $expected) {
            $problems[] = 'call ' . ($index + 1) . ' gave ' . json_encode($actual) . ', not ' . json_encode($expected);
        }
    }
    $failed = $failed || $problems;
    $results[] = ['id' => $case['id'], 'outcome' => $problems ? 'failed' : 'passed', 'failure' => $problems ? implode('; ', $problems) : null];
    echo 'failure cases: ', $case['id'], ' ', $problems ? 'failed: ' . implode('; ', $problems) : 'passed', "\n";
}

$report = ['format' => 'files-failure-cases/1', 'sdk' => 'php', 'runtime' => PHP_VERSION, 'cases' => $results];
if (file_put_contents($inputs['FILES_MOCK_OUT'] . '/failure-cases.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n") === false) {
    fwrite(STDERR, "failure cases: could not write the results\n");
    exit(1);
}
exit($failed ? 1 : 0);
