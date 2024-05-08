<?php

declare(strict_types=1);

namespace Files;

error_reporting(E_ERROR);

use Files\Model\ApiKey;
use Files\Model\File;
use Files\Model\Folder;
use Files\Model\Session;
use Files\Model\User;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

// name of an existing folder in your root to create/delete test files and folders
define('SDK_TEST_ROOT_FOLDER', 'sdk-test');

// any user count that will require multiple page requests to fetch all users
define('USER_COUNT_TO_TRIGGER_PAGINATION', 1);

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
Files::$logLevel = LogLevel::INFO;

// Files::$logLevel = LogLevel::DEBUG;
// Files::$debugRequest = true;
// Files::$debugResponseHeaders = true;

//
// utilities
//

function assertUserCreatedAndDelete($user, $name)
{
    assert($user->isLoaded() === true);

    $saved_user = User::find($user->id);

    assert($saved_user->isLoaded() === true);
    assert($saved_user->name === $name);

    $saved_user->delete();

    $userNoLongerExists = false;

    try {
        User::find($user->id);
    } catch (Exception\NotFoundException $error) {
        assert($error->getTitle() === "Not Found");
        assert($error->getError() === "Not Found");
        assert($error->getType() === "not-found");
        assert($error->getHttpCode() === 404);
        $userNoLongerExists = true;
    }

    assert($userNoLongerExists === true);
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

    assert($caughtExpectedException === true);

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

    assert($caughtExpectedException === true);

    //
    // deleting a non-existent path
    //

    $nonExistentFile->path = '_fake_path_so_this_should_404_';

    $caughtExpectedException = false;

    try {
        $nonExistentFile->delete();
    } catch (Exception\NotFoundException $error) {
        assert($error->getTitle() === "Not Found");
        assert($error->getError() === "Not Found");
        assert($error->getType() === "not-found");
        assert($error->getHttpCode() === 404);
        $caughtExpectedException = true;
    }

    assert($caughtExpectedException === true);

    //
    // create the root folder path
    //

    $caughtExpectedException = false;

    try {
        $folder = Folder::create('.');
    } catch (Exception\ProcessingFailure\DestinationExistsException $error) {
        assert($error->getTitle() === "Destination Exists");
        assert($error->getError() === "The destination exists.");
        assert($error->getType() === "processing-failure/destination-exists");
        assert($error->getHttpCode() === 422);
        $caughtExpectedException = true;
    }

    assert($caughtExpectedException === true);

    Logger::info('***** testErrors() succeeded! *****');
}

function testAutoPaginate()
{
    $params = ['per_page' => constant('USER_COUNT_TO_TRIGGER_PAGINATION')];

    $savedAutoPaginate = Files::$autoPaginate;

    Files::$autoPaginate = true;
    $response = Api::sendRequest('/users', 'GET', $params);

    assert($response->autoPaginateRequests > 1);

    Files::$autoPaginate = false;
    $response = Api::sendRequest('/users', 'GET', $params);

    assert(!isset($response->autoPaginateRequests));

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

    assert($updated_user->isLoaded());
    assert($old_name !== $new_name);
    assert($updated_user->name === $new_name);

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
    assert($foundFolder->isLoaded() === true);

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
    assert($foundFile->isLoaded());

    $copyResponse = $foundFile->copyTo(RemoteTestEnv::$workingFolderPath . 'copied-file.txt');

    assert(!!$copyResponse->status && !!$copyResponse->file_migration_id);

    $foundFile->delete(); // if this fails an unhandled exception will be thrown

    $fileNoLongerExists = false;

    try {
        File::find(RemoteTestEnv::$workingFolderPath . $tempName);
    } catch (Exception\NotFoundException $error) {
        assert($error->getTitle() === "Not Found");
        assert($error->getError() === "Not Found");
        assert($error->getType() === "not-found");
        assert($error->getHttpCode() === 404);
        $fileNoLongerExists = true;
    }

    assert($fileNoLongerExists === true);

    $foundFile = File::find(RemoteTestEnv::$workingFolderPath . 'testFileUploadFindCopyAndDelete-data.txt');
    assert($foundFile->isLoaded());

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

    assert(file_get_contents($tempPath) === substr($fileData, 0, 10));
    assert($result->received === 10);
    assert($result->total === strlen($fileData));

    // then download the rest
    $result = File::resumeDownloadToFile($remoteFilePath, $tempPath);

    assert(file_get_contents($tempPath) === $fileData);
    assert($result->received === ($result->total - 10));
    assert($result->total === strlen($fileData));

    unlink($tempPath);

    $file = new File();
    $file->path = $remoteFilePath;

    Logger::debug('Getting download URL for file at ' . $remoteFilePath);

    $response = $file->download([
        'with_previews' => true,
        'with_priority_color' => true
    ]);

    assert($response->path === $remoteFilePath);
    assert(!!$response->download_uri);

    Logger::debug('Updating file mtime and color at ' . $remoteFilePath);

    $response = $file->update([
        'provided_mtime' => '2000-01-01T01:00:00Z',
        'priority_color' => 'red',
    ]);

    assert($response->provided_mtime === '2000-01-01T01:00:00Z');
    assert($response->priority_color === 'red');

    Logger::debug('Deleting file at ' . $remoteFilePath);

    $file->delete(); // if this fails an unhandled exception will be thrown

    Logger::info('***** testUploadDownloadFileAndDelete() succeeded! *****');
}

function testFileObjectMethods()
{
    Logger::debug('Loading a remote file path into a File object');

    $file = File::get(RemoteTestEnv::$remoteFilePath);

    assert($file->path === RemoteTestEnv::$remoteFilePath);

    Logger::debug('Setting priority_color metadata for File object');

    $response = $file->update([
        'priority_color' => 'yellow',
    ]);

    Logger::debug('Fetching metadata for File object');

    $metadata = File::find($file->path, [
        'with_previews' => true,
        'with_priority_color' => true,
    ]);

    assert(!!$metadata->preview_id);
    assert(!!$metadata->preview);
    assert($metadata->priority_color === 'yellow');

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

    assert(!!$session->id);

    // Tests list method alias on PHP7+, and standard list on PHP5
    $list_method = (version_compare(PHP_VERSION, '7.0.0') >= 0) ? 'list' : 'all';
    ApiKey::$list_method(['user_id' => 0]);

    Session::destroy();
    Files::setSessionId(null);

    Logger::info('***** testSession() succeeded! *****');
}

//
// run tests
//

assert_options(ASSERT_BAIL, 1);

RemoteTestEnv::init();

testErrors();
testAutoPaginate();
testUserListAndUpdate();
testUserCreateAndDelete();
testUserStaticCreateAndDelete();
testFolderCreateListAndDelete();
testFileUploadFindCopyAndDelete();
testUploadDownloadFileAndDelete();
testFileObjectMethods();
testSession();

RemoteTestEnv::deinit();
