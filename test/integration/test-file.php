<?php

///////////////////////////////////////////////////////////////////////////////
// To run this test suite, first set environment variables:
//
// always set:
//   FILES_SESSION_ENV=development
//
// required:
//   FILES_API_KEY - set to your API key
//   FILES_API_DOMAIN - set to your Files.com subdomain (e.g. mysite.files.com)
//
// required only if testSession() is run, otherwise can be omitted:
//   FILES_SESSION_USERNAME - username to login with
//   FILES_SESSION_PASSWORD - password to login with
//
// optional:
//   USER_COUNT_TO_TRIGGER_PAGINATION - defaults to 1, set to a number that will
//     require multiple page requests to fetch all users, but don't set it too low;
//     if you have many users, then "1" will trigger a fetch for every single user
//
///////////////////////////////////////////////////////////////////////////////
//
// Next, in the ../../ directory, install dependencies:
//
// curl -sS https://getcomposer.org/installer | php
// php composer.phar install
//
// Finally, execute the current file:
//
// php test-file.php
//
///////////////////////////////////////////////////////////////////////////////
// Note: you can comment out at the bottom of this file any tests you don't want to run.
///////////////////////////////////////////////////////////////////////////////

declare(strict_types=1);

namespace Files;

error_reporting(E_ALL);

use Files\Model\ApiKey;
use Files\Model\File;
use Files\Model\Folder;
use Files\Model\Session;
use Files\Model\User;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

// With no argument, every test below runs against the Files.com site that FILES_API_KEY and FILES_API_DOMAIN name.
// api-scenarios.sh also runs the four tests sharedSimulatorTests() names against a simulator it starts with the mock
// server's launcher, with-scenario.rb, and then checks that run:
//
//   php test-file.php --shared-simulator
//   php test-file.php --check-shared-simulator OUT
//
// --shared-simulator reads neither FILES_API_KEY nor FILES_API_DOMAIN and never falls back to a site; see
// runOnSharedSimulator and checkSharedSimulatorRun at the end of this file.
$arguments = array_slice($argv, 1);
$mode = $arguments ? $arguments[0] : 'live';
$argumentCounts = ['live' => 0, '--shared-simulator' => 1, '--check-shared-simulator' => 2];
if (!isset($argumentCounts[$mode]) || count($arguments) !== $argumentCounts[$mode]) {
    fwrite(STDERR, "usage: php test-file.php [--shared-simulator | --check-shared-simulator OUT]\n");
    exit(2);
}

// any user count that will require multiple page requests to fetch all users
define('USER_COUNT_TO_TRIGGER_PAGINATION', getenv('USER_COUNT_TO_TRIGGER_PAGINATION') ?: 1);

if ($mode === 'live') {
    // name of an existing folder in your root to create/delete test files and folders (a --shared-simulator run makes
    // one of its own; see runOnSharedSimulator)
    define('SDK_TEST_ROOT_FOLDER', 'sdk-test');

    $api_key = getenv('FILES_API_KEY');
    $api_domain = getenv('FILES_API_DOMAIN');

    if (!$api_key) {
        trigger_error('ENV variable "FILES_API_KEY" was not found', E_USER_ERROR);
        exit;
    }

    if (!$api_domain) {
        trigger_error('ENV variable "FILES_API_DOMAIN" was not found', E_USER_ERROR);
        exit;
    }

    //
    // initialize
    //

    Files::setApiKey($api_key);
    Files::setBaseUrl('https://' . $api_domain);
}

//
// logging/debugging options
//

Files::$logLevel = LogLevel::INFO;
// Files::$logLevel = LogLevel::DEBUG;

// Files::$debugRequest = true;
// Files::$debugResponseHeaders = true;

//
// utilities
//

// assert() was deprecated in PHP 8.0, so prefer Exception, which was
// introduced in PHP 7.0
function assert_or_throw($condition, $message = 'Assertion failed')
{
    if (!$condition) {
        throw new \Exception($message);
    }
}

// Whether a file or folder is at $path: looking up a missing one raises NotFoundException.
function pathExists($path)
{
    try {
        File::find($path);
    } catch (Exception\NotFoundException $error) {
        return false;
    }

    return true;
}

function assertUserCreatedAndDelete($user, $name)
{
    assert_or_throw($user->isLoaded() === true, 'User should be loaded');

    $saved_user = User::find($user->id);

    assert_or_throw($saved_user->isLoaded() === true, 'Saved user should be loaded');
    assert_or_throw($saved_user->name === $name);

    $saved_user->delete();

    $userNoLongerExists = false;

    try {
        User::find($user->id);
    } catch (Exception\NotFoundException $error) {
        assert_or_throw(stripos($error->getTitle(), "Not Found") !== false);
        assert_or_throw(stripos($error->getError(), "Not Found") !== false);
        assert_or_throw($error->getType() === "not-found");
        assert_or_throw($error->getHttpCode() === 404);
        $userNoLongerExists = true;
    }

    assert_or_throw($userNoLongerExists === true);
}

function findFile($targetFile, $fileList)
{
    $matches = array_filter($fileList, function ($element) use ($targetFile) {
        return $element->path === $targetFile->path;
    });
    return $matches ? current($matches) : new File();
}

function createTestFolder()
{
    $rootDir = constant('SDK_TEST_ROOT_FOLDER');
    $dirName = '/created-folder_' . date('Ymd-His');
    $path = $rootDir . $dirName;

    $folder = Folder::create($path);
    $folder->path = $path;

    return [
        $rootDir,
        $dirName,
        $folder,
    ];
}

function createTestFile()
{
    $rootDir = constant('SDK_TEST_ROOT_FOLDER');
    $dirName = '/created-file_' . date('Ymd-His');

    $folder = Folder::create($rootDir . $dirName);

    return [
        $rootDir,
        $dirName,
        $folder,
    ];
}

//
// define tests
//

class RemoteTestEnv
{
    public static $remoteFilePath;
    public static $testFolder;
    public static $workingFolderPath;

    public static function init()
    {
        list($rootDir, $dirName, static::$testFolder) = createTestFolder();

        self::$workingFolderPath = $rootDir . $dirName . '/';

        $tempName = 'RemoteTestEnv-' . date('Ymd_His') . '.txt';
        $tempPath = tempnam(sys_get_temp_dir(), $tempName);

        file_put_contents($tempPath, date('Y-m-d H:i:s'));

        self::$remoteFilePath = self::$workingFolderPath . $tempName;

        File::uploadData(self::$remoteFilePath, rand() . "\n" . date('Y-m-d H:i:s'));

        unlink($tempPath);
    }

    public static function deinit()
    {
        File::deletePath(self::$remoteFilePath);

        static::$testFolder->delete(['recursive' => true]);
    }
}


function testErrors()
{
    global $api_key;
    global $api_domain;

    //
    // invalid configuration
    //

    Files::setApiKey(null);
    Files::setBaseUrl(null);

    $caughtExpectedException = false;

    try {
        User::all();
    } catch (Exception\ConfigurationException $error) {
        $caughtExpectedException = true;
    }

    assert_or_throw($caughtExpectedException === true);

    Files::setApiKey($api_key);
    Files::setBaseUrl('https://' . $api_domain);

    //
    // deleting a folder with no path specified
    //

    $nonExistentFile = new File();

    $caughtExpectedException = false;

    try {
        $nonExistentFile->delete();
    } catch (Exception\MissingParameterException $error) {
        $caughtExpectedException = true;
    }

    assert_or_throw($caughtExpectedException === true);

    //
    // deleting a non-existent path
    //

    $nonExistentFile->path = '_fake_path_so_this_should_404_';

    $caughtExpectedException = false;

    try {
        $nonExistentFile->delete();
    } catch (Exception\NotFoundException $error) {
        assert_or_throw($error->getType() === "not-found");
        assert_or_throw($error->getHttpCode() === 404);
        $caughtExpectedException = true;
    }

    assert_or_throw($caughtExpectedException === true);

    //
    // create the root folder path
    //

    $caughtExpectedException = false;

    try {
        $folder = Folder::create('.');
    } catch (Exception\ProcessingFailure\DestinationExistsException $error) {
        assert_or_throw($error->getTitle() === "Destination Exists");
        assert_or_throw($error->getError() === "The destination exists.");
        assert_or_throw($error->getType() === "processing-failure/destination-exists");
        assert_or_throw($error->getHttpCode() === 422);
        $caughtExpectedException = true;
    }

    assert_or_throw($caughtExpectedException === true);

    Logger::info('***** testErrors() succeeded! *****');
}

function testAutoPaginate()
{
    $params = ['per_page' => constant('USER_COUNT_TO_TRIGGER_PAGINATION')];

    $savedAutoPaginate = Files::$autoPaginate;

    Files::$autoPaginate = true;
    $response = Api::sendRequest('/users', 'GET', $params);

    assert_or_throw($response->autoPaginateRequests > 1);

    Files::$autoPaginate = false;
    $response = Api::sendRequest('/users', 'GET', $params);

    assert_or_throw(!isset($response->autoPaginateRequests));

    Files::$autoPaginate = $savedAutoPaginate;

    Logger::info('***** testAutoPaginate() succeeded! *****');
}

function testUserListAndUpdate()
{
    $all_users = User::all();
    $first_user = $all_users[0];

    $old_name = $first_user->name;
    $new_name = 'edited name - ' . date('Y-m-d H:i:s');

    $first_user->setName($new_name);
    $first_user->save();

    $updated_user = User::find($first_user->id);

    assert_or_throw($updated_user->isLoaded());
    assert_or_throw($old_name !== $new_name);
    assert_or_throw($updated_user->name === $new_name);

    Logger::info('***** testUserListAndUpdate() succeeded! *****');
}

function testUserCreateAndDelete()
{
    $name = 'created-user_' . date('Ymd-His');

    $user = new User([
        'name' => $name,
        'username' => $name,
    ]);

    $user->save();

    assertUserCreatedAndDelete($user, $name);

    Logger::info('***** testUserCreateAndDelete() succeeded! *****');
}

function testUserStaticCreateAndDelete()
{
    $name = 'created-user_' . date('Ymd-His');

    $user = User::create([
        'name' => $name,
        'username' => $name,
    ]);

    assertUserCreatedAndDelete($user, $name);

    Logger::info('***** testUserStaticCreateAndDelete() succeeded! *****');
}

function testFolderCreateListAndDelete()
{
    list($rootDir, $dirName, $testFolder) = createTestFolder();

    $dirFiles = Folder::listFor($rootDir);

    $foundFolder = findFile($testFolder, $dirFiles);
    assert_or_throw($foundFolder->isLoaded() === true);

    $foundFolder->delete(); // if this fails an unhandled exception will be thrown

    Logger::info('***** testFolderCreateListAndDelete() succeeded! *****');
}

function testFileUploadFindCopyAndDelete()
{
    $tempName = 'testFileUploadFindCopyAndDelete-' . date('Ymd_His') . '.txt';
    $tempPath = tempnam(sys_get_temp_dir(), $tempName);

    file_put_contents($tempPath, date('Y-m-d H:i:s'));

    Logger::debug('Uploading file at ' . $tempPath . ' which has contents:' . "\n" . substr(file_get_contents($tempPath), 0, 200));

    File::uploadFile(RemoteTestEnv::$workingFolderPath . $tempName, $tempPath);
    File::uploadData(RemoteTestEnv::$workingFolderPath . 'testFileUploadFindCopyAndDelete-data.txt', rand() . "\n" . date('Y-m-d H:i:s'));

    $foundFile = File::find(RemoteTestEnv::$workingFolderPath . $tempName);
    assert_or_throw($foundFile->isLoaded());

    $copyResponse = $foundFile->copyTo(RemoteTestEnv::$workingFolderPath . 'copied-file.txt');

    assert_or_throw(!!$copyResponse->status && !!$copyResponse->file_migration_id);

    $foundFile->delete(); // if this fails an unhandled exception will be thrown

    $fileNoLongerExists = false;

    try {
        File::find(RemoteTestEnv::$workingFolderPath . $tempName);
    } catch (Exception\NotFoundException $error) {
        assert_or_throw(stripos($error->getTitle(), "Not Found") !== false);
        assert_or_throw(stripos($error->getError(), "Not Found") !== false);
        assert_or_throw($error->getType() === "not-found");
        assert_or_throw($error->getHttpCode() === 404);
        $fileNoLongerExists = true;
    }

    assert_or_throw($fileNoLongerExists === true);

    $foundFile = File::find(RemoteTestEnv::$workingFolderPath . 'testFileUploadFindCopyAndDelete-data.txt');
    assert_or_throw($foundFile->isLoaded());

    unlink($tempPath);

    Logger::info('***** testFileUploadFindCopyAndDelete() succeeded! *****');
}

function testUploadDownloadFileAndDelete()
{
    Logger::debug('Uploading file data...');

    $fileData = rand() . "\n" . date('Y-m-d H:i:s');

    $remoteFilePath = RemoteTestEnv::$workingFolderPath . 'testUploadDownloadFileAndDelete-data.txt';
    File::uploadData($remoteFilePath, $fileData);

    $tempName = 'testUploadDownloadFileAndDelete-' . date('Ymd_His') . '.txt';
    $tempPath = tempnam(sys_get_temp_dir(), $tempName);

    // fetch only the first 10 bytes
    $result = File::partialDownloadToFile($remoteFilePath, $tempPath, 0, 9);

    assert_or_throw(file_get_contents($tempPath) === substr($fileData, 0, 10));
    assert_or_throw($result->received === 10);
    assert_or_throw($result->total === strlen($fileData));

    // then download the rest
    $result = File::resumeDownloadToFile($remoteFilePath, $tempPath);

    assert_or_throw(file_get_contents($tempPath) === $fileData);
    assert_or_throw($result->received === ($result->total - 10));
    assert_or_throw($result->total === strlen($fileData));

    unlink($tempPath);

    $file = new File();
    $file->path = $remoteFilePath;

    Logger::debug('Getting download URL for file at ' . $remoteFilePath);

    $response = $file->download([
        'with_previews' => true,
        'with_priority_color' => true
    ]);

    assert_or_throw($response->path === $remoteFilePath);
    assert_or_throw(!!$response->download_uri);

    Logger::debug('Updating file mtime and color at ' . $remoteFilePath);

    $response = $file->update([
        'provided_mtime' => '2000-01-01T01:00:00Z',
        'priority_color' => 'red',
    ]);

    assert_or_throw($response->provided_mtime === '2000-01-01T01:00:00Z');
    assert_or_throw($response->priority_color === 'red');

    Logger::debug('Deleting file at ' . $remoteFilePath);

    $file->delete(); // if this fails an unhandled exception will be thrown

    Logger::info('***** testUploadDownloadFileAndDelete() succeeded! *****');
}

function testUploadWithParams()
{
    $tempName = 'testFileUploadFindCopyAndDelete-' . date('Ymd_His') . '.txt';
    $tempPath = tempnam(sys_get_temp_dir(), $tempName);

    try {
        file_put_contents($tempPath, date('Y-m-d H:i:s'));

        Logger::debug('Uploading file at ' . $tempPath . ' which has contents:' . "\n" . substr(file_get_contents($tempPath), 0, 200));

        // Each upload goes into a missing folder of its own, so neither can pass on a folder the other made: where the
        // site does not always create parent folders, each one's own mkdir_parents must reach the API.
        $fileParent = RemoteTestEnv::$workingFolderPath . 'mkdir_parent_file';
        $dataParent = RemoteTestEnv::$workingFolderPath . 'mkdir_parent_data';

        assert_or_throw(!pathExists($fileParent), $fileParent . ' should not exist before uploadFile');
        File::uploadFile($fileParent . '/' . $tempName, $tempPath, ['mkdir_parents' => true]);
        assert_or_throw(!pathExists($dataParent), $dataParent . ' should not exist before uploadData');
        File::uploadData($dataParent . '/testFileUploadFindCopyAndDelete-data.txt', rand() . "\n" . date('Y-m-d H:i:s'), ['mkdir_parents' => true]);

        $foundFile = File::find($fileParent . '/' . $tempName);
        assert_or_throw($foundFile->isLoaded());
        $foundFile = File::find($dataParent . '/testFileUploadFindCopyAndDelete-data.txt');
        assert_or_throw($foundFile->isLoaded());
    } finally {
        unlink($tempPath);
    }

    Logger::info('***** testUploadWithParams() succeeded! *****');
}

function testFileObjectMethods()
{
    Logger::debug('Loading a remote file path into a File object');

    $file = File::get(RemoteTestEnv::$remoteFilePath);

    assert_or_throw($file->path === RemoteTestEnv::$remoteFilePath);

    Logger::debug('Setting priority_color metadata for File object');

    $response = $file->update([
        'priority_color' => 'yellow',
    ]);

    Logger::debug('Fetching metadata for File object');

    $metadata = File::find($file->path, [
        'with_priority_color' => true,
    ]);

    assert_or_throw($metadata->priority_color === 'yellow', "'priority_color' not 'yellow' in file metadata");

    Logger::info('***** testFileObjectMethods() succeeded! *****');
}

function testSession()
{
    $username = getenv('FILES_SESSION_USERNAME');
    $password = getenv('FILES_SESSION_PASSWORD');

    if (!$username || !$password) {
        Logger::info('Bypassing testSession() - ENV variables "FILES_SESSION_USERNAME" and "FILES_SESSION_PASSWORD" are both required');
        return;
    }

    $session = Session::create(['username' => $username, 'password' => $password]);
    Files::setSessionId($session->id);

    assert_or_throw(!!$session->id);

    // Tests list method alias on PHP7+, and standard list on PHP5
    $list_method = (version_compare(PHP_VERSION, '7.0.0') >= 0) ? 'list' : 'all';
    ApiKey::$list_method(['user_id' => 0]);

    Session::destroy();
    Files::setSessionId(null);

    Logger::info('***** testSession() succeeded! *****');
}

function testLanguage()
{
    Files::setLanguage('es');
    assert_or_throw(Files::getLanguage() === 'es');

    $savedOutputStream = Logger::getOutputStream();
    $tempStream = fopen('php://temp', 'w+');
    Logger::setOutputStream($tempStream);

    $savedLogLevel = Files::$logLevel;
    $savedDebugRequest = Files::$debugRequest;
    Files::$logLevel = LogLevel::DEBUG;
    Files::$debugRequest = true;

    // Tests list method alias on PHP7+, and standard list on PHP5
    $list_method = (version_compare(PHP_VERSION, '7.0.0') >= 0) ? 'list' : 'all';
    ApiKey::$list_method(['user_id' => 0]);

    Files::$logLevel = $savedLogLevel;
    Files::$debugRequest = $savedDebugRequest;

    rewind($tempStream);
    $debugOutput = stream_get_contents($tempStream);
    fclose($tempStream);
    Logger::setOutputStream($savedOutputStream);

    assert_or_throw(stripos($debugOutput, '[Accept-Language] => es') !== false, '"Accept-Language: es" header not found in debug output');

    Logger::info('***** testLanguage() succeeded! *****');
}

//
// shared simulator
//

// The tests a --shared-simulator run carries out, each once, in this order.
function sharedSimulatorTests()
{
    return [
        'testUserCreateAndDelete',
        'testUserStaticCreateAndDelete',
        'testFolderCreateListAndDelete',
        'testUploadWithParams',
    ];
}

// --shared-simulator: runs each of sharedSimulatorTests() once, through the SDK's own CurlHandler, against the
// simulator with-scenario.rb started for this run, which the FILES_MOCK_* variables name, and writes the run's report
// to FILES_MOCK_OUT/integration.json. Before each test the simulator is reset to the launcher's preset with only the
// site's "always create parent folders" setting turned off, so a folder or upload whose parent is missing needs
// mkdir_parents, and the run's own folder is made, outside the test. After it, whatever is left of that folder is
// deleted. A test passes only when it, that cleanup and its epoch's complete journal did. Returns the exit status.
function runOnSharedSimulator()
{
    $environment = [];
    foreach (['FILES_MOCK_SERVER_URL', 'FILES_MOCK_INSTANCE', 'FILES_MOCK_SCHEMA_SHA256', 'FILES_MOCK_SCENARIO_FILE', 'FILES_MOCK_API_KEY', 'FILES_MOCK_OUT', 'FILES_MOCK_RUN_ID'] as $name) {
        $environment[$name] = (string) getenv($name);
    }
    $scenarioFile = $environment['FILES_MOCK_SCENARIO_FILE'];
    $preset = is_file($scenarioFile) ? json_decode((string) file_get_contents($scenarioFile), true) : null;

    // Nothing is sent anywhere unless each of these is what with-scenario.rb gives its command.
    $problems = [];
    $port = preg_match('#\Ahttp://127\.0\.0\.1:([0-9]{1,5})\z#', $environment['FILES_MOCK_SERVER_URL'], $match) === 1 ? (int) $match[1] : 0;
    if ($port < 1 || $port > 65535) {
        $problems[] = 'FILES_MOCK_SERVER_URL is not http://127.0.0.1:PORT';
    }
    if ($environment['FILES_MOCK_INSTANCE'] === '') {
        $problems[] = 'FILES_MOCK_INSTANCE is empty';
    }
    if (preg_match('/\A[0-9a-f]{64}\z/', $environment['FILES_MOCK_SCHEMA_SHA256']) !== 1) {
        $problems[] = 'FILES_MOCK_SCHEMA_SHA256 is not a SHA-256';
    }
    if (!isset($preset['reset']['profile']['api_scenarios']) || !is_array($preset['reset']['profile']['api_scenarios'])) {
        $problems[] = 'FILES_MOCK_SCENARIO_FILE is not a scenario whose reset chooses the API scenarios profile';
    }
    if (preg_match('/\A[0-9A-Za-z]+\z/', $environment['FILES_MOCK_API_KEY']) !== 1) {
        $problems[] = 'FILES_MOCK_API_KEY is not a synthetic key of letters and digits';
    }
    if (!is_dir($environment['FILES_MOCK_OUT'])) {
        $problems[] = 'FILES_MOCK_OUT is not a directory';
    }
    if (preg_match('/[0-9A-Za-z]/', $environment['FILES_MOCK_RUN_ID']) !== 1) {
        $problems[] = 'FILES_MOCK_RUN_ID has no letters or digits';
    }
    if ($problems) {
        fwrite(STDERR, 'shared simulator: ' . implode('; ', $problems) . "; api-scenarios.sh runs this under with-scenario.rb, which sets them\n");
        return 1;
    }

    $launcher = [
        'server' => $environment['FILES_MOCK_SERVER_URL'],
        'instance' => $environment['FILES_MOCK_INSTANCE'],
        'schema' => $environment['FILES_MOCK_SCHEMA_SHA256'],
    ];
    // The run's own folder, named for its run ID, in which the tests make their folders and files.
    $root = 'sdk-test-' . trim(preg_replace('/[^0-9A-Za-z]+/', '-', $environment['FILES_MOCK_RUN_ID']), '-');
    define('SDK_TEST_ROOT_FOLDER', $root);
    RemoteTestEnv::$workingFolderPath = $root . '/';

    $resetBody = $preset['reset'];
    $resetBody['profile']['site_policy']['always_mkdir_parents'] = false;

    $report = [
        'format' => 'files-php-shared-simulator/1',
        'result' => 'fail',
        'instance' => $launcher['instance'],
        'schema_sha256' => $launcher['schema'],
        'site_policy' => $resetBody['profile']['site_policy'],
        'max_network_retries' => Files::$maxNetworkRetries,
        'setup_failure' => null,
        'tests' => [],
    ];
    try {
        readySharedSimulator($launcher);
    } catch (\Exception $e) {
        $report['setup_failure'] = $e->getMessage();
    }
    if ($report['setup_failure'] === null) {
        Files::setBaseUrl($launcher['server']);
        Files::setApiKey($environment['FILES_MOCK_API_KEY']);
        Files::setSessionId(null);
        Files::setWorkspaceId(null);
        Files::setHandler(null);
        foreach (sharedSimulatorTests() as $test) {
            $report['tests'][] = runSharedSimulatorTest($launcher, $resetBody, $root, $test);
        }
    }

    $failures = $report['setup_failure'] === null ? [] : ['setup: ' . $report['setup_failure']];
    foreach ($report['tests'] as $result) {
        if ($result['outcome'] !== 'passed') {
            $failures[] = $result['test'] . ': ' . $result['failure'];
        }
    }
    $report['result'] = $failures ? 'fail' : 'pass';
    $reportFile = $environment['FILES_MOCK_OUT'] . '/integration.json';
    if (file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n") === false) {
        $failures[] = 'could not write ' . $reportFile;
    }
    foreach ($failures as $failure) {
        fwrite(STDERR, 'shared simulator: ' . $failure . "\n");
    }
    echo 'shared simulator: ', $failures ? 'fail' : 'pass', '; ', $reportFile, "\n";
    return $failures ? 1 : 0;
}

// Runs $test on an epoch of its own, as runOnSharedSimulator describes, and returns its record. The test's own failure
// is the one recorded, never replaced by a later one of its cleanup.
function runSharedSimulatorTest($launcher, $resetBody, $root, $test)
{
    $result = ['test' => $test, 'outcome' => 'failed', 'epoch' => null, 'failure' => null, 'cleanup' => null, 'journal' => null];

    $stage = 'reset';
    try {
        $result['epoch'] = resetSharedSimulator($launcher, $resetBody);
        $stage = 'making ' . $root;
        Folder::create($root);
        $stage = 'test';
        call_user_func(__NAMESPACE__ . '\\' . $test);
    } catch (\Exception $e) {
        $result['failure'] = $stage . ': ' . get_class($e) . ': ' . $e->getMessage();
    } catch (\Throwable $e) {
        $result['failure'] = $stage . ': ' . get_class($e) . ': ' . $e->getMessage();
    }

    // Nothing is cleaned up on a simulator whose reset was not confirmed. Otherwise what is left of the run's folder is
    // deleted with everything in it and must then be gone, and the epoch's journal must be complete.
    if ($result['epoch'] === null) {
        $result['cleanup'] = 'not run: the reset was not confirmed';
        return $result;
    }
    $stage = 'cleanup';
    $problem = null;
    try {
        if (pathExists($root)) {
            (new File(['path' => $root]))->delete(['recursive' => true]);
        }
        assert_or_throw(!pathExists($root), 'Deleting ' . $root . ' left it');
        $result['cleanup'] = 'passed';
        $stage = 'journal';
        $result['journal'] = sharedSimulatorJournal($launcher, $result['epoch']);
    } catch (\Exception $e) {
        $problem = $stage . ': ' . get_class($e) . ': ' . $e->getMessage();
    } catch (\Throwable $e) {
        $problem = $stage . ': ' . get_class($e) . ': ' . $e->getMessage();
    }
    if ($result['cleanup'] === null) {
        $result['cleanup'] = 'failed: ' . $problem;
    }
    $result['failure'] = $result['failure'] ?: $problem;
    $result['outcome'] = $result['failure'] === null ? 'passed' : 'failed';
    return $result;
}

// Resets the simulator to $body and returns the new epoch, once the reset has answered with it, this run's instance
// and the API scenarios identity $body chose, and readiness, still this run's simulator, shows that epoch under the
// site policy $body chose.
function resetSharedSimulator($launcher, $body)
{
    $reset = sharedSimulatorControl($launcher['server'], 'POST', 'reset', $body);
    $epoch = isset($reset['epoch']) ? $reset['epoch'] : null;
    $instance = isset($reset['instance']) ? $reset['instance'] : null;
    $identity = isset($reset['api_scenarios']) && is_array($reset['api_scenarios']) ? $reset['api_scenarios'] : [];
    $expectedIdentity = $body['profile']['api_scenarios'];
    ksort($identity);
    ksort($expectedIdentity);
    if (!is_int($epoch) || $instance !== $launcher['instance'] || $identity !== $expectedIdentity) {
        throw new \Exception('the reset did not answer with an epoch, this run\'s instance and the preset\'s API scenarios identity');
    }

    $ready = readySharedSimulator($launcher);
    $readyEpoch = isset($ready['epoch']) ? $ready['epoch'] : null;
    $policy = isset($ready['profile']['site_policy']) ? $ready['profile']['site_policy'] : null;
    if ($readyEpoch !== $epoch || $policy !== $body['profile']['site_policy']) {
        throw new \Exception('after the reset to epoch ' . $epoch . ', readiness shows epoch ' . json_encode($readyEpoch) . ' and site policy ' . json_encode($policy));
    }
    return $epoch;
}

// The simulator's readiness, which must show the simulator the launcher started for this run: ready, simulating under
// contract 3, with the launcher's schema and instance.
function readySharedSimulator($launcher)
{
    $ready = sharedSimulatorControl($launcher['server'], 'GET', 'ready');
    $expected = ['status' => 'ready', 'mode' => 'simulation', 'contract_version' => 3, 'schema_sha256' => $launcher['schema'], 'instance' => $launcher['instance']];
    foreach ($expected as $key => $value) {
        $actual = array_key_exists($key, $ready) ? $ready[$key] : null;
        if ($actual !== $value) {
            throw new \Exception('readiness ' . $key . ' is ' . json_encode($actual) . ', not ' . json_encode($value));
        }
    }
    return $ready;
}

// Summarizes epoch $epoch's journal, which must still be the simulator's (no other reset came since) and complete,
// with nothing dropped and requests in it.
function sharedSimulatorJournal($launcher, $epoch)
{
    $journal = sharedSimulatorControl($launcher['server'], 'GET', 'journal');
    $summary = [];
    foreach (['epoch', 'complete', 'dropped'] as $key) {
        $summary[$key] = array_key_exists($key, $journal) ? $journal[$key] : null;
    }
    $summary['entries'] = isset($journal['entries']) && is_array($journal['entries']) ? count($journal['entries']) : null;
    if ($summary['epoch'] !== $epoch || $summary['complete'] !== true || $summary['dropped'] !== 0 || !$summary['entries']) {
        throw new \Exception('the journal shows ' . json_encode($summary) . ', not a complete one of epoch ' . $epoch . ' with requests in it');
    }
    return $summary;
}

// A request to the simulator's own control API at $server (not the Files.com API; it takes no credential), through
// ext-curl with no proxy, 5 s to connect and 10 s in all. Anything but a 200 answer holding a JSON object fails.
function sharedSimulatorControl($server, $method, $name, $body = null)
{
    $url = $server . '/__files_mock/v1/' . $name;
    $curl = curl_init($url);
    $options = [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PROXY => '',
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
    ];
    if ($body !== null) {
        $options[CURLOPT_POSTFIELDS] = json_encode($body);
        $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    if (!is_string($response)) {
        throw new \Exception($method . ' ' . $url . ' got no answer: ' . $error);
    }
    $answer = json_decode($response, true);
    if ($status !== 200 || !is_array($answer)) {
        throw new \Exception($method . ' ' . $url . ' answered ' . $status . ': ' . substr($response, 0, 500));
    }
    return $answer;
}

// --check-shared-simulator OUT: fails unless OUT holds a --shared-simulator run's report, integration.json, showing
// each of sharedSimulatorTests() passed once, with its cleanup, on consecutive epochs after the launcher's own reset,
// each with a complete journal, and the launcher's records of the run agree: the same instance and schema, a consumer
// that exited 0 without timing out, no setup error, interruption, missing evidence or unsettled process group, and a
// complete final journal, of the last test's epoch. Returns the exit status.
function checkSharedSimulatorRun($out)
{
    $records = [];
    foreach (['integration.json', 'run.json', 'ready.json', 'reset.json', 'journal.json'] as $name) {
        $file = $out . '/' . $name;
        $records[$name] = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
    }
    $problems = [];
    // The value at $keys inside $value, or null when there is none.
    $at = function ($value, array $keys) {
        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }
            $value = $value[$key];
        }
        return $value;
    };
    $expect = function ($label, $value, array $keys, $expected) use ($at, &$problems) {
        $actual = $at($value, $keys);
        if ($actual !== $expected) {
            $problems[] = $label . ' ' . implode('.', $keys) . ' is ' . json_encode($actual) . ', not ' . json_encode($expected);
        }
    };

    foreach ($records as $name => $record) {
        if (!is_array($record)) {
            $problems[] = $out . ' holds no ' . $name;
        }
    }

    $run = $records['run.json'];
    $instance = $at($run, ['server', 'instance']);
    $schema = $at($run, ['server', 'schema_sha256']);
    if (!is_string($instance) || $instance === '' || !is_string($schema) || $schema === '') {
        $problems[] = 'run.json names no server instance and schema';
    }
    $expect('run.json', $run, ['scenario'], 'api-scenarios-canonical');
    $expect('run.json', $run, ['server', 'contract_version'], 3);
    $expect('run.json', $run, ['consumer', 'status'], 0);
    $expect('run.json', $run, ['setup_error'], null);
    $expect('run.json', $run, ['interrupted'], null);
    $expect('run.json', $run, ['journal', 'complete'], true);
    $expect('run.json', $run, ['journal', 'dropped'], 0);
    foreach ([['consumer', 'timed_out'], ['missing_evidence'], ['unsettled_process_groups']] as $keys) {
        if ($at($run, $keys)) {
            $problems[] = 'run.json ' . implode('.', $keys) . ' is ' . json_encode($at($run, $keys));
        }
    }
    foreach (['integration.json', 'ready.json', 'reset.json', 'journal.json'] as $name) {
        $expect($name, $records[$name], ['instance'], $instance);
    }
    $expect('integration.json', $records['integration.json'], ['schema_sha256'], $schema);
    $expect('ready.json', $records['ready.json'], ['schema_sha256'], $schema);

    $report = $records['integration.json'];
    $expect('integration.json', $report, ['format'], 'files-php-shared-simulator/1');
    $expect('integration.json', $report, ['result'], 'pass');
    $expect('integration.json', $report, ['setup_failure'], null);
    $tests = is_array($at($report, ['tests'])) ? $at($report, ['tests']) : [];
    $names = array_map(function ($test) {
        return is_array($test) && isset($test['test']) ? $test['test'] : null;
    }, $tests);
    if ($names !== sharedSimulatorTests()) {
        $problems[] = 'integration.json reports the tests ' . json_encode($names) . ', not ' . json_encode(sharedSimulatorTests()) . ', each once';
    }

    // The launcher's own reset made one epoch and each test's reset the next; the last test's is the final journal's.
    $epoch = $at($records['reset.json'], ['epoch']);
    if (!is_int($epoch)) {
        $problems[] = 'reset.json has no epoch';
    }
    foreach ($tests as $index => $test) {
        $label = 'integration.json tests.' . $index;
        $epoch = is_int($epoch) ? $epoch + 1 : null;
        $expect($label, $test, ['outcome'], 'passed');
        $expect($label, $test, ['cleanup'], 'passed');
        $expect($label, $test, ['epoch'], $epoch);
        $expect($label, $test, ['journal', 'epoch'], $epoch);
        $expect($label, $test, ['journal', 'complete'], true);
        $expect($label, $test, ['journal', 'dropped'], 0);
    }
    $expect('journal.json', $records['journal.json'], ['epoch'], $epoch);
    $expect('journal.json', $records['journal.json'], ['complete'], true);
    $expect('journal.json', $records['journal.json'], ['dropped'], 0);

    foreach ($problems as $problem) {
        fwrite(STDERR, 'shared simulator: ' . $problem . "\n");
    }
    echo 'shared simulator: ', $problems ? 'fail' : 'pass', '; ', $out, "/integration.json\n";
    return $problems ? 1 : 0;
}

//
// run tests
//

if ($mode === '--shared-simulator') {
    exit(runOnSharedSimulator());
}

if ($mode === '--check-shared-simulator') {
    exit(checkSharedSimulatorRun($arguments[1]));
}

RemoteTestEnv::init();
register_shutdown_function('Files\RemoteTestEnv::deinit');

testErrors();
testAutoPaginate();
testUserListAndUpdate();
testUserCreateAndDelete();
testUserStaticCreateAndDelete();
testFolderCreateListAndDelete();
testFileUploadFindCopyAndDelete();
testUploadDownloadFileAndDelete();
testUploadWithParams();
testFileObjectMethods();
testSession();
testLanguage();
