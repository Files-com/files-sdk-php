<?php

/**
 * Checks one api-scenarios.sh run against the generated cases (ApiScenarioTest::cases()) and writes OUT/account.json:
 *
 *   php account.php OUT MOCK_GENERATION_JSON
 *
 * It fails unless the simulator was generated from the schema these tests were (MOCK_GENERATION_JSON, the mock server's
 * lib/simulation/generation.json), the launcher's run.json shows a run with no setup error, interruption, missing
 * evidence or unsettled process and a complete journal, and every generated case is reported once in OUT's JUnit report
 * with its own record (OUT/scenarios), which shows exactly the steps the case carries out passed, with this SDK loaded.
 * A case with steps this binding does not carry out must end incomplete (PHPUnit reports it skipped), naming exactly
 * those steps; any other case must pass. A failed, errored, skipped, missing, repeated or extra test fails, and so does
 * a run that executed no case.
 */

declare(strict_types=1);

namespace Files\ApiScenarios;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/ApiScenarioTest.php';

$out = isset($argv[1]) ? $argv[1] : '';
$generationFile = isset($argv[2]) ? $argv[2] : '';
if ($out === '' || $generationFile === '') {
    fwrite(STDERR, "usage: php account.php OUT MOCK_GENERATION_JSON\n");
    exit(2);
}

$problems = [];
$cases = ApiScenarioTest::cases();
$class = ApiScenarioTest::class;

$generation = json_decode((string) @file_get_contents($generationFile), true);
$generatedFrom = isset($generation['swagger']['sha256']) ? $generation['swagger']['sha256'] : null;
if ($generatedFrom !== ApiScenarioTest::SCHEMA_SHA256) {
    $problems[] = 'the simulator was generated from the schema ' . json_encode($generatedFrom) . ', not these tests\' ' . ApiScenarioTest::SCHEMA_SHA256;
}

$run = json_decode((string) @file_get_contents($out . '/run.json'), true);
if (!is_array($run)) {
    $problems[] = 'the launcher wrote no run.json';
} else {
    foreach (['setup_error', 'interrupted'] as $field) {
        if ($run[$field] !== null) {
            $problems[] = 'the launcher reports ' . $field . ' ' . json_encode($run[$field]);
        }
    }
    foreach (['missing_evidence', 'unsettled_process_groups'] as $field) {
        if ($run[$field]) {
            $problems[] = 'the launcher reports ' . $field . ' ' . json_encode($run[$field]);
        }
    }
    if ($run['journal']['complete'] !== true) {
        $problems[] = 'the simulator\'s journal is not complete';
    }
    if ($run['consumer']['status'] !== 0) {
        $problems[] = 'PHPUnit exited ' . json_encode($run['consumer']['status']) . ($run['consumer']['timed_out'] ? ', timed out' : '');
    }
}
// The launcher's own records must show one fresh simulator under the API scenarios contract: readiness at its boot
// epoch 0 with the default profile, its one reset to epoch 1 under exactly the identity of the definition these tests
// were generated from, and that epoch's complete journal, each read from the file the launcher wrote, all on run.json's
// instance and agreeing with run.json. An identity is compared member by member, whatever order its JSON gives them.
// A missing, mistyped or different value is a problem, and the receipt names the contract only when every value joined.
// The records are decoded without the associative flag, so a JSON object stays an object (stdClass) and a JSON list an
// array: a list never passes where an object is required, nor an object where a list is.
$joinProblems = [];
$jsonObject = function ($name) use ($out, &$joinProblems) {
    $value = json_decode((string) @file_get_contents($out . '/' . $name), false);
    if ($value instanceof \stdClass) {
        return $value;
    }
    $joinProblems[] = 'the launcher wrote no ' . $name . ' object';
    return null;
};
// The value of $key in $object, a JSON object, when it has the JSON type $type; otherwise null, and a problem.
$typed = function ($object, $label, $key, $type) use (&$joinProblems) {
    $present = $object instanceof \stdClass && property_exists($object, $key);
    $value = $present ? $object->{$key} : null;
    $types = ['string' => is_string($value), 'integer' => is_int($value), 'boolean' => is_bool($value), 'object' => $value instanceof \stdClass, 'list' => is_array($value)];
    if ($present && $types[$type]) {
        return $value;
    }
    $joinProblems[] = $label . ' has no ' . $type . ' ' . $key . ($present ? ' (it has ' . json_encode($value) . ')' : '');
    return null;
};
$same = function ($actual, $expected, $label) use (&$joinProblems) {
    if ($actual !== null && $expected !== null && $actual !== $expected) {
        $joinProblems[] = $label . ' is ' . json_encode($actual) . ', not ' . json_encode($expected);
    }
};
$memberNames = function ($object) {
    $names = array_map('strval', array_keys(get_object_vars($object)));
    sort($names, SORT_STRING);
    return $names;
};
// This binding's identity when $value is exactly it: its three members, each of its own JSON type and value.
$definitionIdentity = function ($value, $label) use (&$joinProblems, $memberNames) {
    if (!($value instanceof \stdClass)) {
        $joinProblems[] = $label . ' is not a JSON object';
        return null;
    }
    if ($memberNames($value) !== ['definition_sha256', 'format', 'format_version']) {
        $joinProblems[] = $label . ' has the members ' . json_encode($memberNames($value)) . ', not exactly definition_sha256, format and format_version';
        return null;
    }
    if ($value->format !== 'files-api-scenarios' || $value->format_version !== 1 || $value->definition_sha256 !== ApiScenarioTest::DEFINITION_SHA256) {
        $joinProblems[] = $label . ' is ' . json_encode($value) . ', not the files-api-scenarios format 1 definition ' . ApiScenarioTest::DEFINITION_SHA256;
        return null;
    }
    return ['format' => 'files-api-scenarios', 'format_version' => 1, 'definition_sha256' => ApiScenarioTest::DEFINITION_SHA256];
};

$runRecord = $jsonObject('run.json');
$ready = $jsonObject('ready.json');
$reset = $jsonObject('reset.json');
$journal = $jsonObject('journal.json');
$server = $typed($runRecord, 'run.json', 'server', 'object');
$runJournal = $typed($runRecord, 'run.json', 'journal', 'object');

// run.json and readiness report the simulator's normalized schema digest, the schema_sha256 its generation.json
// records; it is not the public Swagger input digest checked above (generation.swagger.sha256). The expected value must
// be a SHA-256, exactly 64 lowercase hexadecimal characters: anything else is a problem here, never a skipped comparison.
$simulatorSchema = is_array($generation) && array_key_exists('schema_sha256', $generation) ? $generation['schema_sha256'] : null;
if (!is_string($simulatorSchema) || preg_match('/\A[0-9a-f]{64}\z/', $simulatorSchema) !== 1) {
    $joinProblems[] = 'the simulator\'s generation.json has no schema_sha256 of 64 lowercase hexadecimal characters (it has ' . json_encode($simulatorSchema) . ')';
    $simulatorSchema = null;
}

$same($typed($runRecord, 'run.json', 'scenario', 'string'), 'api-scenarios-canonical', 'run.json scenario');
$origin = $typed($server, 'run.json server', 'url', 'string');
$instance = $typed($server, 'run.json server', 'instance', 'string');
foreach (['url' => $origin, 'instance' => $instance] as $key => $value) {
    if ($value === '') {
        $joinProblems[] = 'run.json server ' . $key . ' is empty';
    }
}
$same($typed($server, 'run.json server', 'schema_sha256', 'string'), $simulatorSchema, 'run.json server schema_sha256');
$same($typed($server, 'run.json server', 'contract_version', 'integer'), 3, 'run.json server contract_version');

$same($typed($ready, 'ready.json', 'status', 'string'), 'ready', 'ready.json status');
$same($typed($ready, 'ready.json', 'mode', 'string'), 'simulation', 'ready.json mode');
$same($typed($ready, 'ready.json', 'contract_version', 'integer'), 3, 'ready.json contract_version');
$same($typed($ready, 'ready.json', 'schema_sha256', 'string'), $simulatorSchema, 'ready.json schema_sha256');
$same($typed($ready, 'ready.json', 'epoch', 'integer'), 0, 'ready.json epoch');
$readyProfile = $typed($ready, 'ready.json', 'profile', 'object');
if ($readyProfile instanceof \stdClass && property_exists($readyProfile, 'api_scenarios')) {
    $joinProblems[] = 'ready.json shows its boot epoch already under an API scenarios contract';
}

if ($reset instanceof \stdClass && $memberNames($reset) !== ['api_scenarios', 'epoch', 'instance']) {
    $joinProblems[] = 'reset.json has the members ' . json_encode($memberNames($reset)) . ', not exactly api_scenarios, epoch and instance';
}
$same($typed($reset, 'reset.json', 'epoch', 'integer'), 1, 'reset.json epoch');
$same($typed($journal, 'journal.json', 'epoch', 'integer'), 1, 'journal.json epoch');
foreach (['ready.json' => $ready, 'reset.json' => $reset, 'journal.json' => $journal] as $label => $record) {
    $same($typed($record, $label, 'instance', 'string'), $instance, $label . ' instance');
}
$contractIdentity = $definitionIdentity($reset instanceof \stdClass && property_exists($reset, 'api_scenarios') ? $reset->api_scenarios : null, 'reset.json api_scenarios');
$definitionIdentity($journal instanceof \stdClass && property_exists($journal, 'api_scenarios') ? $journal->api_scenarios : null, 'journal.json api_scenarios');

$complete = $typed($journal, 'journal.json', 'complete', 'boolean');
$dropped = $typed($journal, 'journal.json', 'dropped', 'integer');
$entries = $typed($journal, 'journal.json', 'entries', 'list');
$same($complete, true, 'journal.json complete');
$same($dropped, 0, 'journal.json dropped');
$same($typed($runJournal, 'run.json journal', 'complete', 'boolean'), $complete, 'run.json journal complete');
$same($typed($runJournal, 'run.json journal', 'dropped', 'integer'), $dropped, 'run.json journal dropped');
$same($typed($runJournal, 'run.json journal', 'entries', 'integer'), is_array($entries) ? count($entries) : null, 'run.json journal entries');

$contract = $joinProblems ? null : ['origin' => $origin, 'instance' => $instance, 'epoch' => 1, 'mode' => 'api_scenarios', 'api_scenarios' => $contractIdentity];
$problems = array_merge($problems, $joinProblems);

// Every test case the JUnit report holds, with its outcome: failed, errored, skipped (also an incomplete test) or passed.
$reported = [];
$junit = @simplexml_load_file($out . '/phpunit.junit.xml');
if ($junit === false) {
    $problems[] = 'PHPUnit wrote no JUnit report';
} else {
    foreach ($junit->xpath('//testcase') as $testcase) {
        $name = (string) $testcase['class'] . '::' . (string) $testcase['name'];
        $outcome = 'passed';
        if (count($testcase->failure)) {
            $outcome = 'failed: ' . (string) $testcase->failure;
        } elseif (count($testcase->error)) {
            $outcome = 'errored: ' . (string) $testcase->error;
        } elseif (count($testcase->skipped)) {
            $outcome = 'skipped';
        }
        $reported[$name][] = $outcome;
    }
}

$identity = ScenarioRun::sdkIdentity();
$account = [];
foreach ($cases as $method => $case) {
    $name = $class . '::' . $method;
    $outcomes = isset($reported[$name]) ? $reported[$name] : [];
    unset($reported[$name]);
    $record = json_decode((string) @file_get_contents($out . '/scenarios/' . $method . '.json'), true);
    $expected = $case['unimplemented'] ? 'incomplete' : 'passed';
    $result = [
        'scenario' => $case['scenario'],
        'reported' => $outcomes,
        'outcome' => is_array($record) ? $record['outcome'] : null,
        'steps' => $case['steps'],
        'site' => $case['site'],
        'carried_out' => $case['carried_out'],
        'steps_passed' => is_array($record) ? $record['steps_passed'] : null,
        'unimplemented' => array_map(function ($step) {
            return $step['step'];
        }, $case['unimplemented']),
        'left_out' => array_sum(array_map(function ($step) {
            return 1 + count($step['dependents']);
        }, $case['unimplemented'])),
    ];
    $account[$method] = $result;

    if (count($outcomes) !== 1) {
        $problems[] = $method . ' was reported ' . count($outcomes) . ' times, not once';
        continue;
    }
    if (!is_array($record)) {
        $problems[] = $method . ' wrote no record';
        continue;
    }
    if ($outcomes[0] !== ($expected === 'passed' ? 'passed' : 'skipped')) {
        $problems[] = $method . ' was reported ' . $outcomes[0] . ', not ' . ($expected === 'passed' ? 'passed' : 'incomplete');
    }
    if ($record['outcome'] !== $expected) {
        $problems[] = $method . ' recorded ' . $record['outcome'] . ', not ' . $expected . ($record['failure'] ? ': ' . $record['failure'] : '');
    }
    if ($record['steps_passed'] !== $case['carried_out']) {
        $problems[] = $method . ' passed steps ' . json_encode($record['steps_passed']) . ', not the ' . json_encode($case['carried_out']) . ' it carries out';
    }
    if ($record['unimplemented'] !== $case['unimplemented']) {
        $problems[] = $method . ' recorded other steps not carried out than its case names';
    }
    if ($record['sdk']['lib'] !== $identity['lib'] || $record['sdk']['lib_listing_sha256'] !== $identity['lib_listing_sha256']) {
        $problems[] = $method . ' loaded the SDK in ' . $record['sdk']['lib'] . ' (' . $record['sdk']['lib_listing_sha256'] . '), not ' . $identity['lib'] . ' (' . $identity['lib_listing_sha256'] . ')';
    }
}
foreach ($reported as $name => $outcomes) {
    $problems[] = $name . ' is not a generated case (' . implode(', ', $outcomes) . ')';
}
$executed = array_filter($account, function ($result) {
    return $result['steps_passed'];
});
if (!$executed) {
    $problems[] = 'no case executed';
}

$summary = [
    'result' => $problems ? 'fail' : 'pass',
    'problems' => $problems,
    'definition_sha256' => ApiScenarioTest::DEFINITION_SHA256,
    'schema_sha256' => ApiScenarioTest::SCHEMA_SHA256,
    'simulator' => is_array($run) ? $run['server'] : null,
    'journal' => is_array($run) ? $run['journal'] : null,
    'contract' => $contract,
    'sdk' => $identity,
    'cases' => $account,
];
file_put_contents($out . '/account.json', json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
foreach ($account as $method => $result) {
    echo 'api-scenarios: ', $result['scenario'], ': ', count((array) $result['steps_passed']), ' of ', count($result['carried_out']), ' steps carried out passed, ',
        $result['left_out'], ' not carried out (steps ', implode(', ', $result['unimplemented']) ?: 'none', ' and what needs them), ',
        count($result['site']), ' site only; ', $result['outcome'] ?: 'no record', "\n";
}
foreach ($problems as $problem) {
    fwrite(STDERR, 'api-scenarios: ' . $problem . "\n");
}
echo 'api-scenarios: ', $summary['result'], '; ', $out, "/account.json\n";
exit($problems ? 1 : 0);
