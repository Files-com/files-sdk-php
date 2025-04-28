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

// name of an existing folder in your root to create/delete test files and folders
define('SDK_TEST_ROOT_FOLDER', 'sdk-test');

// any user count that will require multiple page requests to fetch all users
define('USER_COUNT_TO_TRIGGER_PAGINATION', getenv('USER_COUNT_TO_TRIGGER_PAGINATION') ?: 1);

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

    file_put_contents($tempPath, date('Y-m-d H:i:s'));

    Logger::debug('Uploading file at ' . $tempPath . ' which has contents:' . "\n" . substr(file_get_contents($tempPath), 0, 200));

    File::uploadFile(RemoteTestEnv::$workingFolderPath . 'mkdir_parent/' . $tempName, $tempPath, ['mkdir_parents' => true]);
    File::uploadData(RemoteTestEnv::$workingFolderPath . 'mkdir_parent/' . 'testFileUploadFindCopyAndDelete-data.txt', rand() . "\n" . date('Y-m-d H:i:s'), ['mkdir_parents' => true]);

    $foundFile = File::find(RemoteTestEnv::$workingFolderPath . 'mkdir_parent/' . $tempName);
    assert_or_throw($foundFile->isLoaded());
    $foundFile = File::find(RemoteTestEnv::$workingFolderPath . 'mkdir_parent/' . 'testFileUploadFindCopyAndDelete-data.txt');
    assert_or_throw($foundFile->isLoaded());

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
// run tests
//

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
