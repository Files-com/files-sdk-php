# Files.com PHP SDK

The Files.com PHP SDK provides convenient Files.com API access to applications written in PHP.

## Installation

Install Composer. See https://packagist.org for more info.

If `composer.phar` is already available, skip this step.

    curl -sS https://getcomposer.org/installer | php

Install the SDK

    php composer.phar require files.com/files-php-sdk

### Requirements

* PHP 5.5+
* php-curl extension

## Usage

### Import and initialize
```php
    require 'vendor/autoload.php';

    // set your subdomain or custom domain
    \Files\Files::setBaseUrl('https://MY-SUBDOMAIN.files.com');
```

### Authentication

There are multiple ways to authenticate to the API.

#### Global API Key

You can set an API key globally like this:
```php
    \Files\Files::setApiKey('my-api-key');
```

#### Per-Request API Key

Or, you can pass an API key per-request, in the options array at the end of every method like this:
```php
    $user = new \Files\Model\User($params, array('api_key' => 'my-api-key'));
```

#### User Session

Or, you can open a user session by calling `\Files\Model\Session::create()`
```php
    $session = \Files\Model\Session::create(['username' => $username, 'password' => $password]);
```

Then use it globally for all subsequent API calls like this:
```php
    \Files\Files::setSessionId($session->id);
```

Or, you can pass the session ID per-request, in the options array at the end of every method like this:
```php
    $user = new \Files\Model\User($params, array('session_id' => $session->id));
```

##### Session example
```php
    $session = \Files\Model\Session::create(['username' => $myUsername, 'password' => $myPassword]);
    \Files\Files::setSessionId($session->id);

    // do something
    \Files\Model\ApiKey::all(['user_id' => 0]);

    // clean up when done
    \Files\Model\Session::destroy();
    \Files\Files::setSessionId(null);
```

### Setting Global Options

You can set the following global properties directly on the `\Files\Files` class:

* `\Files\Files::$logLevel` - set to one of the following:
  * `\Files\LogLevel::NONE`
  * `\Files\LogLevel::ERROR`
  * `\Files\LogLevel::WARN`
  * `\Files\LogLevel::INFO` (default)
  * `\Files\LogLevel::DEBUG`
* `\Files\Files::$debugRequest` - enable debug logging of API requests (default: `false`)
* `\Files\Files::$debugResponseHeaders` - enable debug logging of API response headers (default: `false`)
* `\Files\Files::$connectTimeout` - network connect timeout in seconds (default: `30.0`)
* `\Files\Files::$readTimeout` - network read timeout in seconds (default: `90.0`)
* `\Files\Files::$maxNetworkRetries` - max retries (default: `3`)
* `\Files\Files::$minNetworkRetryDelay` - minimum delay in seconds before retrying (default: `0.5`)
* `\Files\Files::$maxNetworkRetryDelay` - max delay in seconds before retrying (default: `1.5`)
* `\Files\Files::$autoPaginate` - auto-fetch all pages when results span multiple pages (default: `true`)

### Static File Operations

#### List files in root folder
```php
    $rootFiles = \Files\Model\Folder::listFor('/');
```

#### Uploading a file on disk
```php
    \Files\Model\File::uploadFile($destinationFileName, $sourceFilePath);
```

#### Uploading raw file data
```php
    \Files\Model\File::uploadData($destinationFileName, $fileData);
```

#### Download a file to stream
```php
    \Files\Model\File::downloadToStream($remoteFilePath, $outputStream);
```
#### Download a file to disk
```php
    // download entire file - with retries enabled
    \Files\Model\File::downloadToFile($remoteFilePath, $localFilePath);

    // partially download - just the first KB
    \Files\Model\File::partialDownloadToFile($remoteFilePath, $localFilePath, 0, 1023);

    // resume an incomplete download
    \Files\Model\File::resumeDownloadToFile($remoteFilePath, $localFilePath);
```

#### Getting a file record by path
```php
    $foundFile = \Files\Model\File::find($remoteFilePath);
```

### File Object Operations

#### Getting a file record by path
```php
    $file = new \Files\Model\File();
    $file->get($remoteFilePath);
```

##### Updating metadata
```php
    $file->update([
      'provided_mtime' => '2000-01-01T01:00:00Z',
      'priority_color' => 'red',
    ]);
```

##### Retrieving metadata
```php
    $file->metadata([
      'with_previews' => true,
      'with_priority_color' => true,
    ]);
```

#### Comparing Case insensitive files and paths
For related documentation see [Case Sensitivity Documentation](https://www.files.com/docs/files-and-folders/file-system-semantics/case-sensitivity).
```php
    if(\Files\Util\PathUtil::same("Fïłèńämê.Txt", "filename.txt")) {
        echo "Paths are the same\n";
    }
```

### Additional Documentation

Additional docs are available at https://developers.files.com

## Getting Support

The Files.com team is happy to help with any SDK Integration challenges you may face.

Just email support@files.com and we'll get the process started.

