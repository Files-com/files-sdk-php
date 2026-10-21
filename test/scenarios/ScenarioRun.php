<?php

declare(strict_types=1);

namespace Files\ApiScenarios;

use Files\Files;
use Files\Model\File;
use Files\Model\Folder;

/**
 * One run of a shared API scenario (verification/scenarios/api.json) against the simulator api-scenarios.sh starts
 * (FILES_MOCK_SERVER_URL, through the mock server's with-scenario.rb), with this SDK configured for it and every
 * response the SDK receives recorded by the handler it sends through. The run's own lifecycle (its folder, made in a
 * parent folder that is left in place, its local folder, and its cleanup) is not one of the scenario's steps. Each run
 * writes what it did to FILES_MOCK_OUT/scenarios/<test>.json: the steps that passed, the steps this binding does not
 * carry out, the SDK it loaded, what each upload, download and migration observed, and every request the simulator
 * journaled for it. account.php checks those records.
 *
 * The client actions (intents) are this SDK's own public calls, which the generated test passes in by role, each bound
 * to an operation the definition gives the intent: an upload is File::uploadFile or File::uploadData, which begin each
 * part with File#beginUpload and end the upload with File::create; a download is File::downloadToFile; a directory's
 * folders are made with Folder::create and listed with Folder::listFor; a copy or move is waited for with
 * FileMigration::find.
 */
class ScenarioRun
{
    const API_PATH = '/api/rest/v1/';
    // The folder each run's own folder is made in; it is left in place.
    const PARENT = 'php_api_scenarios';
    // How many lookups, a second apart, the run's own cleanup waits for a deleted folder to be absent.
    const CLEANUP_LOOKUPS = 5;
    const USER_AGENT = '/^Files\\.com PHP SDK v\\S+$/';

    private $test;
    private $name;
    private $case;
    private $server;
    private $out;
    private $responses = [];
    private $passed = [];
    private $after = 0;
    private $failure = null;
    private $journal = [];
    private $savedBaseUrl;
    private $savedApiKey;
    private $folder;
    private $namespace;
    // The run's local folder (made on first use, removed when the run ends), and how many paths it has given.
    private $local = null;
    private $localPaths = 0;
    private $recipes = [];
    // The step running now, the entries the listings kept returned (by path), and what each transfer observed.
    private $current = null;
    private $listed = [];
    private $transfers = [];

    private function __construct($test, $name, array $case)
    {
        $this->test = $test;
        $this->name = $name;
        $this->case = $case;
    }

    /**
     * Runs body as the scenario of test $name ($case is its entry in ApiScenarioTest::cases()), in a new run folder
     * that is deleted afterwards. A failure of the cleanup after a failed scenario does not hide the scenario's own.
     * A scenario with steps this binding does not carry out ends incomplete, naming them, once every other step passed.
     */
    public static function run($test, $name, array $case, $body)
    {
        $run = new ScenarioRun($test, $name, $case);
        $failure = null;
        try {
            $run->start();
            $body($run);
        } catch (\Exception $e) {
            $failure = $e;
        } catch (\Throwable $e) {
            $failure = $e;
        }
        try {
            if ($run->folder !== null) {
                $run->cleanup();
            }
        } catch (\Exception $e) {
            $failure = $failure ?: $e;
        } catch (\Throwable $e) {
            $failure = $failure ?: $e;
        }
        $run->finish($failure);
        if ($failure !== null) {
            throw $failure;
        }
        if ($case['unimplemented']) {
            $test->markTestIncomplete(self::incompleteMessage($case));
        }
    }

    /** The message of a scenario that ends incomplete: each step this binding does not carry out, and why. */
    public static function incompleteMessage(array $case)
    {
        $parts = [];
        foreach ($case['unimplemented'] as $step) {
            $part = 'step ' . $step['step'] . ' (' . $step['label'] . '): ' . $step['reason'];
            if ($step['dependents']) {
                $part .= '; steps ' . implode(', ', $step['dependents']) . ' need what it would have made';
            }
            $parts[] = $part;
        }
        return 'Not carried out by this PHP binding: ' . implode('. ', $parts) . '.';
    }

    private function start()
    {
        $this->server = rtrim((string) getenv('FILES_MOCK_SERVER_URL'), '/');
        $this->out = (string) getenv('FILES_MOCK_OUT');
        $apiKey = (string) getenv('FILES_MOCK_API_KEY');
        if ($this->server === '' || $this->out === '' || $apiKey === '') {
            $this->test->fail(
                'FILES_MOCK_SERVER_URL, FILES_MOCK_OUT and FILES_MOCK_API_KEY must name the simulator these scenarios run '
                . 'against; run them with api-scenarios.sh, which starts one.'
            );
        }
        $this->savedBaseUrl = Files::getBaseUrl();
        $this->savedApiKey = Files::getApiKey();
        Files::setBaseUrl($this->server);
        Files::setApiKey($apiKey);
        $inner = new \GuzzleHttp\Handler\CurlHandler();
        $run = $this;
        Files::setHandler(function ($request, array $options) use ($inner, $run) {
            return $inner($request, $options)->then(function ($response) use ($request, $run) {
                $run->record($request, $response);
                return $response;
            });
        });
        $this->after = $this->lastSeq();
        if (!$this->exists(self::PARENT)) {
            Folder::create(self::PARENT, ['mkdir_parents' => false]);
        }
        $this->folder = self::PARENT . '/' . md5(uniqid('', true) . mt_rand());
        Folder::create($this->folder, ['mkdir_parents' => false]);
        $this->namespace = $this->folder . '/scenario';
    }

    /**
     * Deletes what the run made. A scenario that stopped before its own deletes leaves its owned folder, which is
     * deleted with everything in it; then the run's folder goes, and the parent must stay. Last, the API requests the
     * run sent each carried the same API key (the journal numbers keys and never records them), no session and the
     * SDK's User-Agent.
     */
    private function cleanup()
    {
        if ($this->exists($this->namespace)) {
            (new File(['path' => $this->namespace]))->delete(['recursive' => true]);
            $this->confirmAbsent($this->namespace);
        }
        (new File(['path' => $this->folder]))->delete(['recursive' => false]);
        $this->confirmAbsent($this->folder);
        $this->test->assertTrue($this->exists(self::PARENT), 'Deleting ' . $this->folder . ' also removed ' . self::PARENT . '.');

        $this->journal = $this->journalAfter($this->after);
        $keys = [];
        foreach ($this->journal as $entry) {
            if (strpos((string) $entry['path'], self::API_PATH) !== 0) {
                continue;
            }
            $credentials = isset($entry['credentials']) ? $entry['credentials'] : [];
            $this->test->assertTrue(isset($credentials['api_key']) && is_int($credentials['api_key']), 'Request ' . $entry['seq'] . ' carried no journaled API key.');
            $this->test->assertTrue(!isset($credentials['session']), 'Request ' . $entry['seq'] . ' carried a session.');
            $this->test->assertSame(1, preg_match(self::USER_AGENT, (string) $entry['user_agent']), 'Request ' . $entry['seq'] . ' came with the User-Agent ' . $entry['user_agent'] . ', not the SDK\'s.');
            $keys[$credentials['api_key']] = true;
        }
        $this->test->assertSame(1, count($keys), 'The SDK\'s API requests carried ' . count($keys) . ' API keys, not one.');
    }

    // Writes the run's record, removes its local folder and restores the SDK's configuration.
    private function finish($failure)
    {
        Files::setBaseUrl($this->savedBaseUrl);
        Files::setApiKey($this->savedApiKey);
        Files::setHandler(null);
        if ($this->local !== null) {
            self::removeLocal($this->local);
        }
        if ($this->out === null || $this->out === '') {
            return;
        }
        $outcome = $failure === null ? ($this->case['unimplemented'] ? 'incomplete' : 'passed') : 'failed';
        $record = [
            'test' => $this->name,
            'scenario' => $this->case['scenario'],
            'outcome' => $outcome,
            'steps_passed' => $this->passed,
            'failure' => $failure === null ? null : get_class($failure) . ': ' . $failure->getMessage(),
            'failed_step' => $this->failure,
            'unimplemented' => $this->case['unimplemented'],
            'folder' => $this->folder,
            'sdk' => self::sdkIdentity(),
            'transfers' => $this->transfers,
            'journal' => $this->journal,
        ];
        if (!is_dir($this->out . '/scenarios')) {
            mkdir($this->out . '/scenarios', 0777, true);
        }
        file_put_contents($this->out . '/scenarios/' . $this->name . '.json', json_encode($record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    /** The SDK this run loaded: where Files\Files came from, the SHA-256 of every file of its lib folder, and versions. */
    public static function sdkIdentity()
    {
        $lib = dirname((new \ReflectionClass('Files\\Files'))->getFileName());
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($lib, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            $files[substr($file->getPathname(), strlen($lib) + 1)] = hash_file('sha256', $file->getPathname());
        }
        ksort($files, SORT_STRING);
        $listing = '';
        foreach ($files as $relative => $sha256) {
            $listing .= $sha256 . '  ' . $relative . "\n";
        }
        return [
            'lib' => realpath($lib),
            'lib_files' => count($files),
            'lib_listing_sha256' => hash('sha256', $listing),
            'php' => PHP_VERSION,
            'os' => PHP_OS . ' ' . php_uname('m'),
            'guzzle' => defined('GuzzleHttp\\ClientInterface::MAJOR_VERSION') ? \GuzzleHttp\ClientInterface::MAJOR_VERSION : (defined('GuzzleHttp\\ClientInterface::VERSION') ? constant('GuzzleHttp\\ClientInterface::VERSION') : null),
            'phpunit' => class_exists('PHPUnit\\Runner\\Version') ? \PHPUnit\Runner\Version::id() : (class_exists('PHPUnit_Runner_Version') ? \PHPUnit_Runner_Version::id() : null),
        ];
    }

    /** Records a response the SDK received: its request, its status, its next cursor and, for an API request, its body. */
    public function record($request, $response)
    {
        $path = $request->getUri()->getPath();
        $body = null;
        if (strpos($path, self::API_PATH) === 0) {
            $stream = $response->getBody();
            $body = (string) $stream;
            if ($stream->isSeekable()) {
                $stream->rewind();
            }
        }
        $this->responses[] = [
            'method' => $request->getMethod(),
            'path' => $path,
            'status' => $response->getStatusCode(),
            'cursor' => $response->getHeaderLine('X-Files-Cursor'),
            'body' => $body,
        ];
    }

    /** A fixture's path: the owned folder for "", or a path inside it. */
    public function path($relative)
    {
        return $relative === '' ? $this->namespace : $this->namespace . '/' . $relative;
    }

    /** Runs one step, naming it in any failure. A step that fails ends the scenario. */
    public function step($number, $label, $step)
    {
        $this->responses = [];
        $this->current = $number;
        $error = null;
        try {
            $step();
        } catch (\Exception $e) {
            $error = $e;
        } catch (\Throwable $e) {
            $error = $e;
        }
        if ($error !== null) {
            $assertion = $error instanceof \PHPUnit\Framework\AssertionFailedError || $error instanceof \PHPUnit_Framework_AssertionFailedError;
            $message = $assertion ? $error->getMessage() : get_class($error) . ': ' . $error->getMessage();
            $this->failure = ['step' => $number, 'label' => $label, 'message' => $message];
            $this->test->fail('Step ' . $number . ' (' . $label . '): ' . $message);
        }
        $this->passed[] = $number;
    }

    // The last response a call received, which must have status.
    private function last($status)
    {
        $this->test->assertTrue(count($this->responses) > 0, 'The call received no response.');
        $last = $this->responses[count($this->responses) - 1];
        $this->test->assertSame($status, $last['status'], 'The response to ' . $last['method'] . ' ' . $last['path'] . ' had status ' . $last['status'] . ', not ' . $status . '.');
        return $last;
    }

    /** Calls an operation that returns an object, checks its status, and returns the object and the body it came in. */
    public function succeeds($status, $call)
    {
        $result = $call();
        $last = $this->last($status);
        $body = json_decode((string) $last['body']);
        $this->test->assertTrue(is_object($body), 'The response body is not a JSON object: ' . $last['body']);
        return [$result, $body];
    }

    /** Calls an operation that returns nothing, and checks that its response had status and no content. */
    public function noContent($status, $call)
    {
        $call();
        $last = $this->last($status);
        $this->test->assertSame('', trim((string) $last['body']), 'The response has content, not none.');
    }

    /** Calls an operation that must fail with the API error type, as the exception $class with the status. */
    public function failsWith($class, $status, $type, $call)
    {
        $error = $this->errorOf($call);
        $this->test->assertTrue($error !== null, 'The call succeeded; it should fail with ' . $type . '.');
        $this->expected($error, $class, $status, $type);
    }

    /** Calls the operation up to $attempts times, $interval seconds apart, until it fails as failsWith checks. */
    public function eventuallyFailsWith($attempts, $interval, $class, $status, $type, $call)
    {
        for ($attempt = 0; $attempt < $attempts; $attempt++) {
            if ($attempt > 0) {
                sleep($interval);
            }
            $this->responses = [];
            $error = $this->errorOf($call);
            if ($error !== null) {
                $this->expected($error, $class, $status, $type);
                return;
            }
        }
        $this->test->fail($attempts . ' lookups, ' . $interval . ' s apart, never answered ' . $type . '.');
    }

    private function errorOf($call)
    {
        try {
            $call();
            return null;
        } catch (\Files\Exception\ApiException $e) {
            return $e;
        }
    }

    private function expected($error, $class, $status, $type)
    {
        $this->test->assertTrue($error instanceof $class, 'The call failed with ' . get_class($error) . ' (' . $error->getType() . ', ' . $error->getCode() . '), not ' . $class . '.');
        $this->test->assertSame($type, $error->getType(), 'The error\'s type');
        $this->test->assertSame($status, $error->getCode(), 'The error\'s HTTP status');
        $this->last($status);
    }

    /**
     * Calls a list operation, which the SDK pages through itself, and checks every page it loaded: each answered with
     * status, at most $perPage records each, at most $maxPages pages (and $maxRecords records, unless 0), and the last
     * one with no next cursor. The simulator's journal then shows one folders.list request for each page, the first
     * sending no cursor, each later one exactly the cursor the page before it returned, and the last returning none.
     * Returns the records.
     */
    public function pages($call, $status, $perPage, $maxPages, $maxRecords)
    {
        $before = $this->lastSeq();
        $records = $call();
        $this->test->assertTrue(is_array($records), 'The listing returned no array of records.');
        $pages = $this->responses;
        $this->test->assertTrue(count($pages) > 0, 'The listing received no response.');
        $this->test->assertTrue(count($pages) <= $maxPages, 'The listing has ' . count($pages) . ' pages, more than ' . $maxPages . '.');
        foreach ($pages as $index => $page) {
            $this->test->assertSame($status, $page['status'], 'Page ' . ($index + 1) . ' had status ' . $page['status'] . '.');
            $data = json_decode((string) $page['body']);
            $this->test->assertTrue(is_array($data) && count($data) <= $perPage, 'Page ' . ($index + 1) . ' holds ' . (is_array($data) ? count($data) : 'no') . ' records, not at most ' . $perPage . '.');
        }
        $this->test->assertSame('', $pages[count($pages) - 1]['cursor'], 'The last page returned a next cursor.');
        if ($maxRecords > 0) {
            $this->test->assertTrue(count($records) <= $maxRecords, 'The listing holds ' . count($records) . ' records, more than ' . $maxRecords . '.');
        }

        $requests = [];
        foreach ($this->journalAfter($before) as $entry) {
            if ($entry['operation'] === 'folders.list') {
                $requests[] = $entry;
            }
        }
        $this->test->assertSame(count($pages), count($requests), 'The simulator journaled ' . count($requests) . ' page requests, not the ' . count($pages) . ' pages the SDK loaded.');
        // A journal entry has no cursor digest for a cursor that was not sent or returned.
        $digest = function ($entry, $name) {
            return isset($entry[$name]) ? $entry[$name] : null;
        };
        foreach ($requests as $index => $request) {
            $sent = $index === 0 ? null : $digest($requests[$index - 1], 'next_cursor_sha256');
            $this->test->assertSame($sent, $digest($request, 'cursor_sha256'), 'Page ' . ($index + 1) . ' (journal seq ' . $request['seq'] . ') sent a cursor other than the one the page before it returned.');
            $this->test->assertSame($status, $request['status'], 'Page ' . ($index + 1) . ' (journal seq ' . $request['seq'] . ') was answered ' . $request['status'] . '.');
        }
        $this->test->assertNull($digest($requests[count($requests) - 1], 'next_cursor_sha256'), 'The last page returned a next cursor.');
        return $records;
    }

    /** Checks that a listing holds each expected entry ("type path") exactly once and nothing else. */
    public function sameEntries(array $records, array $expected)
    {
        $got = [];
        foreach ($records as $record) {
            $got[] = $record->getType() . ' ' . $record->getPath();
        }
        sort($got, SORT_STRING);
        sort($expected, SORT_STRING);
        $this->test->assertSame($expected, $got, 'The listing\'s entries');
    }

    /** Checks that an entry's field, as the SDK's model gives it, is one of the values the definition allows. */
    public function oneOf($name, $got, array $allowed)
    {
        $this->test->assertTrue(in_array($got, $allowed, true), $name . ' is ' . json_encode($got) . ', not one of ' . json_encode($allowed) . '.');
    }

    /** Checks that the body gives name as the JSON integer $want. */
    public function integer($body, $name, $want)
    {
        $this->test->assertTrue(property_exists($body, $name) && $body->$name === $want, $name . ' is ' . json_encode(property_exists($body, $name) ? $body->$name : null) . ', not the JSON integer ' . $want . '.');
    }

    /** Checks that the body has no value for name: it is absent or null. */
    public function noValue($body, $name)
    {
        $this->test->assertTrue(!property_exists($body, $name) || $body->$name === null, $name . ' is ' . json_encode(property_exists($body, $name) ? $body->$name : null) . ', not absent.');
    }

    /**
     * Checks an entry's field, as the SDK's model gives it, against each of the definition's matchers: one_of (one of
     * the values, null meaning none), not (other than the value) and includes (holding each string). A date-time
     * compares as an instant.
     */
    public function field($name, $type, $got, array $matchers)
    {
        foreach ($matchers as $matcher => $operand) {
            if ($matcher === 'one_of') {
                $matched = false;
                foreach ($operand as $allowed) {
                    $matched = $matched || $this->sameValue($type, $got, $allowed);
                }
                $this->test->assertTrue($matched, $name . ' is ' . json_encode($got) . ', not one of ' . json_encode($operand) . '.');
            } elseif ($matcher === 'not') {
                $this->test->assertFalse($this->sameValue($type, $got, $operand), $name . ' is ' . json_encode($got) . ', which it must not be.');
            } elseif ($matcher === 'includes') {
                foreach ($operand as $part) {
                    $this->test->assertTrue(is_string($got) && strpos($got, $part) !== false, $name . ' is ' . json_encode($got) . ', which does not include ' . json_encode($part) . '.');
                }
            } else {
                $this->test->fail('The definition matches ' . $name . ' by ' . $matcher . ', which this binding does not know.');
            }
        }
    }

    // Whether a field's value is the definition's: null only as null, a date-time as the same instant, anything else
    // exactly.
    private function sameValue($type, $got, $want)
    {
        if ($got === null || $want === null) {
            return $got === $want;
        }
        if ($type !== 'date-time') {
            return $got === $want;
        }
        $instant = function ($value) {
            try {
                return is_string($value) ? (new \DateTime($value))->format('U.u') : null;
            } catch (\Exception $e) {
                return null;
            }
        };
        return $instant($got) !== null && $instant($got) === $instant($want);
    }

    /**
     * A recipe's bytes: byte index is (index * multiplier + offset) % modulus, for each index below length. They must
     * have the length and SHA-256 the definition records.
     */
    public function recipe($number, $multiplier, $offset, $modulus, $length, $sha256)
    {
        if (!isset($this->recipes[$number])) {
            $bytes = '';
            for ($index = 0; $index < $length; $index++) {
                $bytes .= chr(($index * $multiplier + $offset) % $modulus);
            }
            $this->test->assertSame([$length, $sha256], [strlen($bytes), hash('sha256', $bytes)], 'Recipe ' . $number . '\'s bytes');
            $this->recipes[$number] = $bytes;
        }
        return $this->recipes[$number];
    }

    /** Keeps the entries a listing returned, by path, for a later download of a listed entry. */
    public function listed(array $records)
    {
        foreach ($records as $record) {
            $this->listed[$record->getPath()] = $record;
        }
    }

    /**
     * Uploads data to path with the SDK's upload helper ($calls['upload']: File::uploadFile for a local file,
     * File::uploadData for a stream) from a source of the kind given (uploadSource), with the step's parameters. With
     * $finalProgress, the helper's progress option records each report: one for each part, the last giving data's
     * length as both its total and its acknowledged bytes. A stream must be closed once the upload ends. Then the
     * upload must be as uploaded checks, with at least $minParts parts.
     */
    public function upload($path, $data, $source, $maxRead, array $params, $minParts, $finalProgress, array $calls)
    {
        $reports = [];
        $options = $finalProgress ? ['progress' => function ($report) use (&$reports) {
            $reports[] = $report;
        }] : [];
        list($before, $first) = [$this->lastSeq(), count($this->responses)];
        list($argument, $stream) = $this->uploadSource($path, $data, $source, $maxRead);
        try {
            $calls['upload']($path, $argument, $params, $options);
            $closed = $stream === null || !is_resource($stream);
        } finally {
            if ($stream !== null && is_resource($stream)) {
                fclose($stream);
            }
        }
        $this->test->assertTrue($closed, 'File::uploadData left the stream it uploaded open.');

        $parts = $this->uploaded($path, $data, $before, $first, true);
        $accepted = count($parts);
        $this->test->assertTrue($accepted >= $minParts, 'The simulator accepted ' . $accepted . ' parts of the upload, fewer than ' . $minParts . '.');
        $transfer = ['step' => $this->current, 'upload' => $path, 'source' => $source, 'size' => strlen($data), 'sha256' => hash('sha256', $data), 'parts' => $parts];
        if ($finalProgress) {
            $this->test->assertSame(count($parts), count($reports), 'The upload reported progress ' . count($reports) . ' times, not once for each of its ' . count($parts) . ' parts.');
            $acknowledged = 0;
            foreach ($reports as $index => $report) {
                $acknowledged += $parts[$index]['bytes'];
                $want = ['total_bytes' => strlen($data), 'acknowledged_bytes' => $acknowledged, 'part_number' => $index + 1, 'part_bytes' => $parts[$index]['bytes']];
                $this->test->assertSame($want, $report, 'Progress report ' . ($index + 1) . ' of the upload');
            }
            $last = $reports[count($reports) - 1];
            $this->test->assertSame([strlen($data), strlen($data)], [$last['total_bytes'], $last['acknowledged_bytes']], 'The final progress (total and acknowledged bytes)');
            $transfer['progress_final'] = $last;
        }
        $this->transfers[] = $transfer;
    }

    /**
     * Uploads data to path as upload does, with a progress option that cancels it at the first acknowledged part by
     * throwing: the helper must throw that same exception after that one report, having begun one part only, which the
     * simulator accepted holding exactly data's first bytes, and having ended nothing.
     */
    public function cancelledUpload($path, $data, $source, $maxRead, array $params, array $calls)
    {
        $cancel = new \RuntimeException('Cancelled after the first acknowledged part');
        $reports = [];
        $options = ['progress' => function ($report) use ($cancel, &$reports) {
            $reports[] = $report;
            throw $cancel;
        }];
        list($before, $first) = [$this->lastSeq(), count($this->responses)];
        list($argument, $stream) = $this->uploadSource($path, $data, $source, $maxRead);
        $thrown = null;
        try {
            $calls['upload']($path, $argument, $params, $options);
        } catch (\Exception $e) {
            $thrown = $e;
        } finally {
            if ($stream !== null && is_resource($stream)) {
                fclose($stream);
            }
        }
        $this->test->assertSame($cancel, $thrown, 'The cancelled upload threw ' . ($thrown === null ? 'nothing' : get_class($thrown) . ': ' . $thrown->getMessage()) . ', not its cancellation.');
        $this->test->assertCount(1, $reports, 'The cancelled upload reported progress');

        $parts = $this->uploaded($path, $data, $before, $first, false);
        $this->test->assertSame(1, count($parts), 'The cancelled upload sent ' . count($parts) . ' parts, not one.');
        $this->test->assertSame($parts[0]['bytes'], $reports[0]['acknowledged_bytes'], 'The bytes the cancelled upload reported acknowledged');
        $this->transfers[] = ['step' => $this->current, 'upload' => $path, 'source' => $source, 'cancelled' => true, 'parts' => $parts, 'progress' => $reports[0]];
    }

    /**
     * The source of an upload of data to path: a new local file holding it (local-file), given by its path, or a stream
     * of it, which the helper reads and is to close: a php://temp stream (seekable-stream), or a pipe from cat reading
     * that local file (forward-only-stream), which cannot seek and gives at most $maxRead bytes a read, its chunk size.
     * Returns [ the helper's source, the stream or null ].
     */
    private function uploadSource($path, $data, $source, $maxRead)
    {
        $local = $this->localPath(basename($path));
        file_put_contents($local, $data);
        if ($source === 'local-file') {
            return [$local, null];
        }
        if ($source === 'seekable-stream') {
            $stream = fopen('php://temp', 'r+');
            fwrite($stream, $data);
            rewind($stream);
            return [$stream, $stream];
        }
        $stream = popen('cat ' . escapeshellarg($local), 'r');
        $this->test->assertTrue(is_resource($stream), 'Reading ' . $local . ' through cat');
        stream_set_chunk_size($stream, $maxRead);
        $meta = stream_get_meta_data($stream);
        $this->test->assertFalse($meta['seekable'], 'The forward-only stream can seek.');
        return [$stream, $stream];
    }

    /**
     * Checks the upload of data to path the SDK sent after journal sequence $before, with its responses from index
     * $first on. Each begin_upload must have granted one part, in order from part 1, all of one ref; the simulator must
     * have journaled one accepted part for each, in order, holding exactly the next bytes of data, each but the last
     * filled to its grant's partsize (a source that ends where a part ends sends one more, empty, part); and, when the
     * upload is $completed, one finalize holding exactly data, and else none. Returns the parts.
     */
    private function uploaded($path, $data, $before, $first, $completed)
    {
        $grants = [];
        foreach (array_slice($this->responses, $first) as $response) {
            if ($response['method'] === 'POST' && strpos($response['path'], self::API_PATH . 'file_actions/begin_upload/') === 0) {
                $granted = json_decode((string) $response['body'], true);
                $this->test->assertTrue(is_array($granted) && count($granted) === 1, 'A begin_upload of ' . $path . ' granted ' . (is_array($granted) ? count($granted) : 'no') . ' parts, not one.');
                $grants[] = $granted[0];
            }
        }
        $journaled = $this->journalOf($before, 'transfers.upload_part');
        $this->test->assertSame(count($grants), count($journaled), 'The upload of ' . $path . ' was granted ' . count($grants) . ' parts, and the simulator journaled ' . count($journaled) . '.');

        $parts = [];
        $offset = 0;
        foreach ($journaled as $index => $entry) {
            $grant = $grants[$index];
            $number = $index + 1;
            $this->test->assertSame([$number, $grants[0]['ref']], [$grant['part_number'], $grant['ref']], 'The grant of part ' . $number . ' of the upload of ' . $path . ' (number, ref)');
            $bytes = isset($entry['bytes']) ? $entry['bytes'] : null;
            $want = substr($data, $offset, (int) $bytes);
            $this->test->assertTrue($entry['status'] >= 200 && $entry['status'] < 300, 'The simulator answered part ' . $number . ' (journal seq ' . $entry['seq'] . ') ' . $entry['status'] . '.');
            $this->test->assertSame([$number, strlen($want), hash('sha256', $want)], [isset($entry['part']) ? $entry['part'] : null, $bytes, isset($entry['sha256']) ? $entry['sha256'] : null], 'Part ' . $number . ' the simulator journaled (seq ' . $entry['seq'] . ': part, bytes, SHA-256) against the next bytes of ' . $path);
            if ($index < count($journaled) - 1) {
                $this->test->assertSame($grant['partsize'], $bytes, 'Part ' . $number . ' of the upload of ' . $path . ' holds ' . $bytes . ' bytes, not its partsize ' . $grant['partsize'] . '.');
            }
            $this->test->assertTrue($bytes <= $grant['partsize'], 'Part ' . $number . ' holds more than its partsize.');
            $offset += $bytes;
            $parts[] = ['part' => $number, 'partsize' => $grant['partsize'], 'status' => $entry['status'], 'bytes' => $bytes, 'sha256' => $entry['sha256'], 'seq' => $entry['seq']];
        }

        $ends = $this->journalOf($before, 'files.finalize_upload');
        if (!$completed) {
            $this->test->assertSame([], $ends, 'The simulator journaled a finalize of the upload of ' . $path . '.');
            return $parts;
        }
        $this->test->assertSame(strlen($data), $offset, 'The parts of the upload of ' . $path . ' hold ' . $offset . ' bytes, not ' . strlen($data) . '.');
        $this->test->assertSame(1, count($ends), 'The simulator journaled ' . count($ends) . ' finalizes of the upload of ' . $path . ', not one.');
        $this->test->assertSame([true, strlen($data), hash('sha256', $data)], [$ends[0]['status'] >= 200 && $ends[0]['status'] < 300, $ends[0]['bytes'], $ends[0]['sha256']], 'The finalize the simulator journaled (seq ' . $ends[0]['seq'] . ': succeeded, bytes, SHA-256)');
        return $parts;
    }

    /**
     * Downloads the file at path into a new local file with $calls['download'] (File::downloadToFile), or, for a listed
     * entry, the file at the path of the entry a listing kept returned for it, and checks its bytes against data.
     */
    public function download($path, $data, $listedEntry, array $calls)
    {
        if ($listedEntry) {
            $this->test->assertTrue(isset($this->listed[$path]), 'No listing returned an entry for ' . $path . '.');
            $path = $this->listed[$path]->getPath();
        }
        $local = $this->localPath(basename($path));
        $this->fetch($path, $local, $calls);
        $this->sameBytes('The download of ' . $path, (string) file_get_contents($local), $data);
    }

    /**
     * Downloads the file at path to local with $calls['download'] (File::downloadToFile), which must report as received
     * and in all exactly the bytes it wrote. The simulator's journal must show one whole download of exactly those bytes.
     */
    private function fetch($path, $local, array $calls)
    {
        $before = $this->lastSeq();
        $result = $calls['download']($path, $local);
        clearstatcache(true, $local);
        $size = filesize($local);
        $this->test->assertSame(['received' => $size, 'total' => $size], ['received' => $result->received, 'total' => $result->total], 'What File::downloadToFile reported for the ' . $size . ' bytes it wrote');
        $downloads = $this->journalOf($before, 'transfers.download');
        $this->test->assertSame(1, count($downloads), 'The simulator journaled ' . count($downloads) . ' downloads, not one.');
        $this->test->assertSame([200, $size, hash_file('sha256', $local)], [$downloads[0]['status'], $downloads[0]['bytes'], $downloads[0]['sha256']], 'The download the simulator journaled (seq ' . $downloads[0]['seq'] . ': status, bytes, SHA-256)');
        $this->transfers[] = ['step' => $this->current, 'download' => $path, 'received' => $result->received, 'total' => $result->total, 'sha256' => hash_file('sha256', $local),
                              'journal' => ['seq' => $downloads[0]['seq'], 'status' => $downloads[0]['status']]];
    }

    /**
     * Checks that a copy or move completed: its file action's status when that is completed or failed, or else its
     * migration's, looked up with $find (given the migration's id, it returns its status) at once and then every second
     * until it is completed or failed, for at most a minute.
     */
    public function completed($status, $migration, $find)
    {
        $statuses = [$status];
        $deadline = microtime(true) + 60;
        while (!in_array($status, ['completed', 'failed'], true)) {
            $this->test->assertTrue(is_int($migration) && $migration > 0, 'The ' . json_encode($status) . ' file action names no migration (' . json_encode($migration) . ').');
            if (count($statuses) > 1) {
                $this->test->assertTrue(microtime(true) < $deadline, 'The migration ' . $migration . ' was still ' . json_encode($status) . ' after a minute.');
                sleep(1);
            }
            $statuses[] = $status = $find($migration);
        }
        $this->transfers[] = ['step' => $this->current, 'migration' => $migration, 'statuses' => $statuses];
        $this->test->assertSame('completed', $status, 'The migration ' . $migration . ' ended');
    }

    /**
     * Uploads a local tree to remote with this SDK's own calls: makes the tree (paths relative to it, each with its
     * recipe's bytes for a file and null for a folder) in a new local folder, walks that folder (dot entries included),
     * creates remote and then each folder found, parents first, with $calls['folder'] (Folder::create), and uploads each
     * file found with $calls['upload'] (File::uploadFile), checked as uploaded checks it. What the API returned must be
     * what the definition expects: the files uploaded exactly completed (relative paths), files of them, bytes in all;
     * the folders created exactly directories; and those holding nothing uploaded or created exactly emptyDirectories,
     * emptyFolders of them.
     */
    public function uploadDirectory($remote, array $tree, array $completed, $files, $bytes, array $directories, array $emptyDirectories, $emptyFolders, array $calls)
    {
        $local = $this->localPath('upload-directory');
        mkdir($local, 0700);
        foreach ($tree as list($relative, $data)) {
            $target = $local . '/' . $relative;
            $folder = $data === null ? $target : dirname($target);
            if (!is_dir($folder)) {
                mkdir($folder, 0700, true);
            }
            if ($data !== null) {
                file_put_contents($target, $data);
            }
        }
        $found = self::localEntries($local);
        $folders = array_values(array_filter($found, function ($relative) use ($local) {
            return is_dir($local . '/' . $relative);
        }));
        // Parents first: by depth, then by name as a string (strcmp, the byte order localEntries sorts in).
        usort($folders, function ($a, $b) {
            $depth = substr_count($a, '/') - substr_count($b, '/');
            return $depth !== 0 ? $depth : strcmp($a, $b);
        });

        $this->test->assertSame(['path' => $remote, 'type' => 'directory'], $calls['created']($calls['folder']($remote)), 'The folder the upload made first');
        $created = [];
        foreach ($folders as $relative) {
            $this->test->assertSame(['path' => $remote . '/' . $relative, 'type' => 'directory'], $calls['created']($calls['folder']($remote . '/' . $relative)), 'The folder made for ' . $relative);
            $created[] = $relative;
        }
        $done = [];
        $total = 0;
        foreach (array_diff($found, $folders) as $relative) {
            $data = (string) file_get_contents($local . '/' . $relative);
            list($before, $first) = [$this->lastSeq(), count($this->responses)];
            $calls['upload']($remote . '/' . $relative, $local . '/' . $relative, [], []);
            $parts = $this->uploaded($remote . '/' . $relative, $data, $before, $first, true);
            $this->transfers[] = ['step' => $this->current, 'upload' => $remote . '/' . $relative, 'source' => 'local-file', 'size' => strlen($data), 'sha256' => hash('sha256', $data), 'parts' => $parts];
            $done[] = $relative;
            $total += strlen($data);
        }

        $this->sameSet('The files the upload completed', $completed, $done);
        $this->test->assertSame([$files, $bytes], [count($done), $total], 'The upload completed ' . count($done) . ' files of ' . $total . ' bytes, not ' . $files . ' of ' . $bytes . '.');
        $this->sameSet('The folders the upload made', $directories, $created);
        $empty = array_values(array_filter($created, function ($folder) use ($done, $created) {
            foreach (array_merge($done, $created) as $path) {
                if (strpos($path, $folder . '/') === 0) {
                    return false;
                }
            }
            return true;
        }));
        $this->sameSet('The empty folders the upload made', $emptyDirectories, $empty);
        $this->test->assertSame($emptyFolders, count($empty), 'The number of empty folders the upload made');
    }

    /**
     * Downloads the folder at remote into a new local folder with this SDK's own calls: lists each folder, every page,
     * with $calls['list'] (Folder::listFor), makes each folder listed in it locally and downloads each file listed as
     * fetch does. Then walks the local folder (dot entries included): it must hold files files, bytes bytes in all, and
     * emptyFolders empty folders, and each of matched (a path relative to it and a recipe's bytes) exactly those bytes.
     */
    public function downloadDirectory($remote, array $matched, $files, $emptyFolders, $bytes, array $calls)
    {
        $local = $this->localPath('download-directory');
        mkdir($local, 0700);
        $folders = [[$remote, $local]];
        while ($folders) {
            list($folder, $target) = array_shift($folders);
            foreach ($calls['list']($folder) as $record) {
                $entry = $calls['entry']($record);
                $name = (string) substr((string) $entry['path'], strlen($folder) + 1);
                $this->test->assertTrue($name !== '' && strpos($name, '/') === false && $entry['path'] === $folder . '/' . $name, 'Listing ' . $folder . ' returned ' . json_encode($entry['path']) . ', which is not in it.');
                $destination = $target . '/' . $name;
                if ($entry['type'] === 'directory') {
                    mkdir($destination, 0700);
                    $folders[] = [$entry['path'], $destination];
                } else {
                    $this->fetch($entry['path'], $destination, $calls);
                }
            }
        }

        $found = self::localEntries($local);
        $got = ['files' => 0, 'bytes' => 0, 'empty_folders' => 0];
        foreach ($found as $relative) {
            $path = $local . '/' . $relative;
            if (is_dir($path)) {
                $got['empty_folders'] += count(scandir($path)) === 2 ? 1 : 0;
            } else {
                $got['files'] += 1;
                $got['bytes'] += filesize($path);
            }
        }
        $this->test->assertSame(['files' => $files, 'bytes' => $bytes, 'empty_folders' => $emptyFolders], $got, 'What the local folder holds (' . implode(', ', $found) . ')');
        foreach ($matched as list($relative, $data)) {
            $this->test->assertTrue(is_file($local . '/' . $relative), 'The download holds no file ' . $relative . '.');
            $this->sameBytes('The downloaded ' . $relative, (string) file_get_contents($local . '/' . $relative), $data);
        }
    }

    private function sameBytes($what, $got, $want)
    {
        $this->test->assertTrue($got === $want, $what . ' holds ' . strlen($got) . ' bytes with SHA-256 ' . hash('sha256', $got) . ', not the ' . strlen($want) . ' bytes with SHA-256 ' . hash('sha256', $want) . ' expected.');
    }

    private function sameSet($what, array $expected, array $got)
    {
        sort($expected, SORT_STRING);
        sort($got, SORT_STRING);
        $this->test->assertSame($expected, $got, $what);
    }

    // A new path in the run's own local folder, which the run removes when it ends.
    private function localPath($name)
    {
        if ($this->local === null) {
            $this->local = sys_get_temp_dir() . '/files-api-scenario-' . md5(uniqid('', true) . mt_rand());
            mkdir($this->local, 0700);
        }
        $this->localPaths++;
        return $this->local . '/' . $this->localPaths . '-' . $name;
    }

    // Every file and folder inside the local folder root, at any depth, relative to it: those whose names begin with a
    // dot included (SKIP_DOTS leaves out only . and ..).
    private static function localEntries($root)
    {
        $entries = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $entry) {
            $entries[] = substr($entry->getPathname(), strlen($root) + 1);
        }
        sort($entries, SORT_STRING);
        return $entries;
    }

    private static function removeLocal($root)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $entry) {
            if ($entry->isDir() && !$entry->isLink()) {
                rmdir($entry->getPathname());
            } else {
                unlink($entry->getPathname());
            }
        }
        rmdir($root);
    }

    // The entries of the simulator's journal for one operation, after sequence number $after.
    private function journalOf($after, $operation)
    {
        return array_values(array_filter($this->journalAfter($after), function ($entry) use ($operation) {
            return $entry['operation'] === $operation;
        }));
    }

    private function exists($path)
    {
        try {
            File::find($path);
            return true;
        } catch (\Files\Exception\NotFoundException $e) {
            return false;
        }
    }

    private function confirmAbsent($path)
    {
        for ($lookup = 0; $lookup < self::CLEANUP_LOOKUPS; $lookup++) {
            if ($lookup > 0) {
                sleep(1);
            }
            if (!$this->exists($path)) {
                return;
            }
        }
        $this->test->fail('Deleting ' . $path . ' left it.');
    }

    // The simulator's journal, which must be complete: the entries after sequence number $after.
    private function journalAfter($after)
    {
        $journal = $this->control('journal');
        $this->test->assertTrue($journal['complete'] === true, 'The simulator\'s journal did not keep every request.');
        $entries = [];
        foreach ($journal['entries'] as $entry) {
            if ($entry['seq'] > $after) {
                $entries[] = $entry;
            }
        }
        return $entries;
    }

    private function lastSeq()
    {
        $last = 0;
        foreach ($this->control('journal')['entries'] as $entry) {
            $last = max($last, $entry['seq']);
        }
        return $last;
    }

    // A read of the simulator's own control API, which is not the Files.com API and carries no credential.
    private function control($name)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->server . '/__files_mock/v1/' . $name);
        return json_decode((string) $response->getBody(), true);
    }
}
