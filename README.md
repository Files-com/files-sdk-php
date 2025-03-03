# Files.com PHP SDK

The Files.com PHP SDK provides convenient Files.com API access to applications written in PHP.

The content included here should be enough to get started, but please visit our
[Developer Documentation Website](https://developers.files.com/php/) for the complete documentation.

## Introduction

The Files.com PHP SDK provides convenient access to all of Files.com from applications written in PHP.

You can use it to directly work with files and folders as well as perform management tasks such as adding/removing users, onboarding counterparties, retrieving information about automations and more.

### Installation

The Files.com PHP SDK is installed using Composer. See https://packagist.org for more info.

First, install Composer if necessary:

```shell
curl -sS https://getcomposer.org/installer | php
```

Then use Composer to install the Files.com SDK:

```shell
php composer.phar require files.com/files-php-sdk
```

#### Requirements

* PHP 5.5+
* php-curl extension

Explore the [files-sdk-php](https://github.com/Files-com/files-sdk-php) code on GitHub.

### Getting Support

The Files.com Support team provides official support for all of our official Files.com integration tools.

To initiate a support conversation, you can send an [Authenticated Support Request](https://www.files.com/docs/overview/requesting-support) or simply send an E-Mail to support@files.com.

## Authentication

There are two ways to authenticate: API Key authentication and Session-based authentication.

### Authenticate with an API Key

Authenticating with an API key is the recommended authentication method for most scenarios, and is
the method used in the examples on this site.

To use an API Key, first generate an API key from the [web
interface](https://www.files.com/docs/sdk-and-apis/api-keys) or [via the API or an
SDK](/php/resources/developers/api-keys).

Note that when using a user-specific API key, if the user is an administrator, you will have full
access to the entire API. If the user is not an administrator, you will only be able to access files
that user can access, and no access will be granted to site administration functions in the API.

```php title="Example Request"
\Files\Files::setApiKey('YOUR_API_KEY');

try {
  # Alternatively, you can specify the API key on a per-object basis in the second parameter to a model constructor.
  $user = new \Files\Model\User($params, array('api_key' => 'YOUR_API_KEY'));

  # You may also specify the API key on a per-request basis in the final parameter to static methods.
  \Files\Model\User::find($id, $params, array('api_key' => 'YOUR_API_KEY'));
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

Don't forget to replace the placeholder, `YOUR_API_KEY`, with your actual API key.

### Authenticate with a Session

You can also authenticate by creating a user session using the username and
password of an active user. If the user is an administrator, the session will have full access to
all capabilities of Files.com. Sessions created from regular user accounts will only be able to access files that
user can access, and no access will be granted to site administration functions.

Sessions use the exact same session timeout settings as web interface sessions. When a
session times out, simply create a new session and resume where you left off. This process is not
automatically handled by our SDKs because we do not want to store password information in memory without
your explicit consent.

#### Logging In

To create a session, the `create` method is called on the `\Files\Model\Session` object with the user's username and
password.

This returns a session object that can be used to authenticate SDK method calls.

```php title="Example Request"
try {
  $session = \Files\Model\Session::create(['username' => 'motor', 'password' => 'vroom']);
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

#### Using a Session

Once a session has been created, you can store the session globally, use the session per object, or use the session per request to authenticate SDK operations.

```php title="Example Request"
## You may set the returned session ID to be used by default for subsequent requests.
\Files\Files::setSessionId($session->id);

try {
  # Alternatively, you can specify the session ID on a per-object basis in the second parameter to a model constructor.
  $user = new \Files\Model\User($params, array('session_id' => $session->id));

  # You may also specify the session ID on a per-request basis in the final parameter to static methods.
  \Files\Model\User::find($id, $params, array('session_id' => $session->id));
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

#### Logging Out

User sessions can be ended by calling the `Session::destroy` method.

```php title="Example Request"
try {
  \Files\Model\Session::destroy();
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

## Configuration

Global configuration is performed by setting properties directly on the `\Files\Files` class.

### Configuration Options

#### Auto Paginate

Auto-fetch all pages when results span multiple pages. The default value is `true`.
```php title="Example setting"
\Files\Files::$autoPaginate = false
```

#### Base URL

Setting the base URL for the API is required if your site is configured to disable global acceleration.
This can also be set to use a mock server in development or CI.

```php title="Example setting"
\Files\Files::setBaseUrl('https://MY-SUBDOMAIN.files.com');
```

#### Log Level

Supported values:

* `\Files\LogLevel::NONE`
* `\Files\LogLevel::ERROR`
* `\Files\LogLevel::WARN`
* `\Files\LogLevel::INFO` (default)
* `\Files\LogLevel::DEBUG`

```php title="Example setting"
\Files\Files::$logLevel = \Files\LogLevel::DEBUG
```

#### Debug Requests

Enable debug logging of API requests. The default value is `false`.

```php title="Example setting"
\Files\Files::$debugRequest = true
```

#### Debug Response Headers

Enable debug logging of API response headers. The default value is `false`.

```php title="Example setting"
\Files\Files::$debugResponseHeaders = true
```

#### Connect Timeout

Network connect timeout in seconds. The default value is 30.0.
```php title="Example setting"
\Files\Files::$connectTimeout = 20.0
```

#### Read Timeout

Network read timeout in seconds. The default value is 60.0.

```php title="Example setting"
\Files\Files::$readTimeout = 60
```

#### Minimum Retry Delay

Minimum network delay in seconds before retrying. The default value is 0.5.

```php title="Example setting"
\Files\Files::$minNetworkRetryDelay = 1.0
```

#### Maximum Retry Delay

Maximum network delay in seconds before retrying. The default value is 1.5.

```php title="Example setting"
\Files\Files::$maxNetworkRetryDelay = 3.0
```

#### Maximum Network Retries

Maximum number of retries. The default value is 3.

```php title="Example setting"
\Files\Files::$maxNetworkRetries = 5
```

## Sort and Filter

Several of the Files.com API resources have list operations that return multiple instances of the
resource. The List operations can be sorted and filtered.

### Sorting

To sort the returned data, pass in the ```sort_by``` method argument.

Each resource supports a unique set of valid sort fields and can only be sorted by one field at a
time.

The argument value is a Php associative array that has a key of the resource field name sort on and
a value of either ```"asc"``` or ```"desc"``` to specify the sort order.

```php title="Sort Example"
try {
  // users sorted by username
  $users = \Files\Model\User::list(array(
    'sort_by' => array("username" => "asc")
  ));
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Filtering

Filters apply selection criteria to the underlying query that returns the results. They can be
applied individually or combined with other filters, and the resulting data can be sorted by a
single field.

Each resource supports a unique set of valid filter fields, filter combinations, and combinations of
filters and sort fields.

The passed in argument value is a Php associative array that has a key of the resource field name to
filter on and a passed in value to use in the filter comparison.

#### Filter Types

| Filter | Type | Description |
| --------- | --------- | --------- |
| `filter` | Exact | Find resources that have an exact field value match to a passed in value. (i.e., FIELD_VALUE = PASS_IN_VALUE). |
| `filter_prefix` | Pattern | Find resources where the specified field is prefixed by the supplied value. This is applicable to values that are strings. |
| `filter_gt` | Range | Find resources that have a field value that is greater than the passed in value.  (i.e., FIELD_VALUE > PASS_IN_VALUE). |
| `filter_gteq` | Range | Find resources that have a field value that is greater than or equal to the passed in value.  (i.e., FIELD_VALUE >=  PASS_IN_VALUE). |
| `filter_lt` | Range | Find resources that have a field value that is less than the passed in value.  (i.e., FIELD_VALUE < PASS_IN_VALUE). |
| `filter_lteq` | Range | Find resources that have a field value that is less than or equal to the passed in value.  (i.e., FIELD_VALUE \<= PASS_IN_VALUE). |

```php title="Exact Filter Example"
try {
  // non admin users
  $users = \Files\Model\User::list(array(
    'filter' => array("not_site_admin" => true)
  ));

  foreach ($users as $value) {
    print("User username: " . $value->getUserName() . "\n");
  }
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

```php title="Range Filter Example"
try {
  // users who haven't logged in since 2024-01-01
  $users = \Files\Model\User::list(array(
    'filter_gteq' => array("last_login_at" => "2024-01-01")
  ));

  foreach ($users as $value) {
    print("User username: " . $value->getUserName() . "\n");
  }
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

```php title="Pattern Filter Example"
try {
  // users whose usernames start with 'test'
  $users = \Files\Model\User::list(array(
    'filter_prefix' => array("username" => "test")
  ));

  foreach ($users as $value) {
    print("User username: " . $value->getUserName() . "\n");
  }
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

```php title="Combination Filter with Sort Example"
try {
  // users whose usernames start with 'test' and are not admins
  $users = \Files\Model\User::list(array(
    'filter_prefix' => array("username" => "test"),
    'filter' => array("not_site_admin" => true),
    'sort_by' => array("last_login_at" => "asc")
  ));

  foreach ($users as $value) {
    print("User username: " . $value->getUserName() . "\n");
  }
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

## Errors

The Files.com PHP SDK will return errors by raising exceptions. There are many exception classes defined in the Files SDK that correspond
to specific errors.

The raised exceptions come from two categories:

1.  SDK Exceptions - errors that originate within the SDK
2.  API Exceptions - errors that occur due to the response from the Files.com API.  These errors are grouped into common error types.

There are several types of exceptions within each category.  Exception classes indicate different types of errors and are named in a
fashion that describe the general premise of the originating error.  More details can be found in the exception object message using the
`php getMessage()` method call.

Use standard PHP exception handling to detect and deal with errors.  It is generally recommended to catch specific errors first, then
catch the general `Files\FilesException` exception as a catch-all.

```php title="Example Error Handling"
try {
  $session = Files\Model\Session::create(['username' => 'USERNAME', 'password' => 'BADPASSWORD']);
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Error Types

#### SDK Errors

SDK errors are general errors that occur within the SDK code.  These errors generate exceptions.  Each of these
exception classes inherit from a standard `Exception` base class.

```shell title="Example SDK Exception Class Inheritance Structure"
Files\Exception\ApiConnectException ->
Files\Exception\FilesException ->
Exception
```
##### SDK Exception Classes

| Exception Class Name| Description |
| --------------- | ------------ |
| `ApiBadResponseException`| A bad formed response came back from the API |
| `ApiConnectException`| The Files.com API cannot be reached |
| `ApiRequestException`| There was an issue with the API request itself |
| `ApiServerException`| The API service responded with a bad response (ie, 5xx) |
| `ApiTooManyRedirectsException`| The API service redirected too many times |
| `ConfigurationException`| Invalid SDK configuration parameters |
| `EmptyPropertyException`| An required property was empty |
| `InvalidParameterException`| A passed in parameter is invalid |
| `MissingParameterException`| A method parameter is missing |
| `NotImplementedException`| The called method has not be implemented by the SDK |

#### API Errors

API errors are errors returned by the Files.com API.  Each exception class inherits from an error group base class.
The error group base class indicates a particular type of error.

```shell title="Example API Exception Class Inheritance Structure"
Files\Exception\NotAuthorizedException\FolderAdminPermissionRequiredException ->
Files\Exception\NotAuthorizedException ->
Files\Exception\ApiException ->
Files\Exception\FilesException ->
Exception
```
##### API Exception Classes

| Exception Class Name | Error Group |
| --------- | --------- |
|`AgentUpgradeRequiredException`|  `BadRequestException` |
|`AttachmentTooLargeException`|  `BadRequestException` |
|`CannotDownloadDirectoryException`|  `BadRequestException` |
|`CantMoveWithMultipleLocationsException`|  `BadRequestException` |
|`DatetimeParseException`|  `BadRequestException` |
|`DestinationSameException`|  `BadRequestException` |
|`DoesNotSupportSortingException`|  `BadRequestException` |
|`FolderMustNotBeAFileException`|  `BadRequestException` |
|`FoldersNotAllowedException`|  `BadRequestException` |
|`InvalidBodyException`|  `BadRequestException` |
|`InvalidCursorException`|  `BadRequestException` |
|`InvalidCursorTypeForSortException`|  `BadRequestException` |
|`InvalidEtagsException`|  `BadRequestException` |
|`InvalidFilterAliasCombinationException`|  `BadRequestException` |
|`InvalidFilterFieldException`|  `BadRequestException` |
|`InvalidFilterParamException`|  `BadRequestException` |
|`InvalidFilterParamFormatException`|  `BadRequestException` |
|`InvalidFilterParamValueException`|  `BadRequestException` |
|`InvalidInputEncodingException`|  `BadRequestException` |
|`InvalidInterfaceException`|  `BadRequestException` |
|`InvalidOauthProviderException`|  `BadRequestException` |
|`InvalidPathException`|  `BadRequestException` |
|`InvalidReturnToUrlException`|  `BadRequestException` |
|`InvalidSortFieldException`|  `BadRequestException` |
|`InvalidSortFilterCombinationException`|  `BadRequestException` |
|`InvalidUploadOffsetException`|  `BadRequestException` |
|`InvalidUploadPartGapException`|  `BadRequestException` |
|`InvalidUploadPartSizeException`|  `BadRequestException` |
|`MethodNotAllowedException`|  `BadRequestException` |
|`MultipleSortParamsNotAllowedException`|  `BadRequestException` |
|`NoValidInputParamsException`|  `BadRequestException` |
|`PartNumberTooLargeException`|  `BadRequestException` |
|`PathCannotHaveTrailingWhitespaceException`|  `BadRequestException` |
|`ReauthenticationNeededFieldsException`|  `BadRequestException` |
|`RequestParamsContainInvalidCharacterException`|  `BadRequestException` |
|`RequestParamsInvalidException`|  `BadRequestException` |
|`RequestParamsRequiredException`|  `BadRequestException` |
|`SearchAllOnChildPathException`|  `BadRequestException` |
|`UnrecognizedSortIndexException`|  `BadRequestException` |
|`UnsupportedCurrencyException`|  `BadRequestException` |
|`UnsupportedHttpResponseFormatException`|  `BadRequestException` |
|`UnsupportedMediaTypeException`|  `BadRequestException` |
|`UserIdInvalidException`|  `BadRequestException` |
|`UserIdOnUserEndpointException`|  `BadRequestException` |
|`UserRequiredException`|  `BadRequestException` |
|`AdditionalAuthenticationRequiredException`|  `NotAuthenticatedException` |
|`ApiKeySessionsNotSupportedException`|  `NotAuthenticatedException` |
|`AuthenticationRequiredException`|  `NotAuthenticatedException` |
|`BundleRegistrationCodeFailedException`|  `NotAuthenticatedException` |
|`FilesAgentTokenFailedException`|  `NotAuthenticatedException` |
|`InboxRegistrationCodeFailedException`|  `NotAuthenticatedException` |
|`InvalidCredentialsException`|  `NotAuthenticatedException` |
|`InvalidOauthException`|  `NotAuthenticatedException` |
|`InvalidOrExpiredCodeException`|  `NotAuthenticatedException` |
|`InvalidSessionException`|  `NotAuthenticatedException` |
|`InvalidUsernameOrPasswordException`|  `NotAuthenticatedException` |
|`LockedOutException`|  `NotAuthenticatedException` |
|`LockoutRegionMismatchException`|  `NotAuthenticatedException` |
|`OneTimePasswordIncorrectException`|  `NotAuthenticatedException` |
|`TwoFactorAuthenticationErrorException`|  `NotAuthenticatedException` |
|`TwoFactorAuthenticationSetupExpiredException`|  `NotAuthenticatedException` |
|`ApiKeyIsDisabledException`|  `NotAuthorizedException` |
|`ApiKeyIsPathRestrictedException`|  `NotAuthorizedException` |
|`ApiKeyOnlyForDesktopAppException`|  `NotAuthorizedException` |
|`ApiKeyOnlyForMobileAppException`|  `NotAuthorizedException` |
|`ApiKeyOnlyForOfficeIntegrationException`|  `NotAuthorizedException` |
|`BillingOrSiteAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`BillingPermissionRequiredException`|  `NotAuthorizedException` |
|`BundleMaximumUsesReachedException`|  `NotAuthorizedException` |
|`CannotLoginWhileUsingKeyException`|  `NotAuthorizedException` |
|`CantActForOtherUserException`|  `NotAuthorizedException` |
|`ContactAdminForPasswordChangeHelpException`|  `NotAuthorizedException` |
|`FilesAgentFailedAuthorizationException`|  `NotAuthorizedException` |
|`FolderAdminOrBillingPermissionRequiredException`|  `NotAuthorizedException` |
|`FolderAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`FullPermissionRequiredException`|  `NotAuthorizedException` |
|`HistoryPermissionRequiredException`|  `NotAuthorizedException` |
|`InsufficientPermissionForParamsException`|  `NotAuthorizedException` |
|`InsufficientPermissionForSiteException`|  `NotAuthorizedException` |
|`MustAuthenticateWithApiKeyException`|  `NotAuthorizedException` |
|`NeedAdminPermissionForInboxException`|  `NotAuthorizedException` |
|`NonAdminsMustQueryByFolderOrPathException`|  `NotAuthorizedException` |
|`NotAllowedToCreateBundleException`|  `NotAuthorizedException` |
|`PasswordChangeNotRequiredException`|  `NotAuthorizedException` |
|`PasswordChangeRequiredException`|  `NotAuthorizedException` |
|`ReadOnlySessionException`|  `NotAuthorizedException` |
|`ReadPermissionRequiredException`|  `NotAuthorizedException` |
|`ReauthenticationFailedException`|  `NotAuthorizedException` |
|`ReauthenticationFailedFinalException`|  `NotAuthorizedException` |
|`ReauthenticationNeededActionException`|  `NotAuthorizedException` |
|`RecaptchaFailedException`|  `NotAuthorizedException` |
|`SelfManagedRequiredException`|  `NotAuthorizedException` |
|`SiteAdminRequiredException`|  `NotAuthorizedException` |
|`SiteFilesAreImmutableException`|  `NotAuthorizedException` |
|`TwoFactorAuthenticationRequiredException`|  `NotAuthorizedException` |
|`UserIdWithoutSiteAdminException`|  `NotAuthorizedException` |
|`WriteAndBundlePermissionRequiredException`|  `NotAuthorizedException` |
|`WritePermissionRequiredException`|  `NotAuthorizedException` |
|`ApiKeyNotFoundException`|  `NotFoundException` |
|`BundlePathNotFoundException`|  `NotFoundException` |
|`BundleRegistrationNotFoundException`|  `NotFoundException` |
|`CodeNotFoundException`|  `NotFoundException` |
|`FileNotFoundException`|  `NotFoundException` |
|`FileUploadNotFoundException`|  `NotFoundException` |
|`FolderNotFoundException`|  `NotFoundException` |
|`GroupNotFoundException`|  `NotFoundException` |
|`InboxNotFoundException`|  `NotFoundException` |
|`NestedNotFoundException`|  `NotFoundException` |
|`PlanNotFoundException`|  `NotFoundException` |
|`SiteNotFoundException`|  `NotFoundException` |
|`UserNotFoundException`|  `NotFoundException` |
|`AlreadyCompletedException`|  `ProcessingFailureException` |
|`AutomationCannotBeRunManuallyException`|  `ProcessingFailureException` |
|`BehaviorNotAllowedOnRemoteServerException`|  `ProcessingFailureException` |
|`BundleOnlyAllowsPreviewsException`|  `ProcessingFailureException` |
|`BundleOperationRequiresSubfolderException`|  `ProcessingFailureException` |
|`CouldNotCreateParentException`|  `ProcessingFailureException` |
|`DestinationExistsException`|  `ProcessingFailureException` |
|`DestinationFolderLimitedException`|  `ProcessingFailureException` |
|`DestinationParentConflictException`|  `ProcessingFailureException` |
|`DestinationParentDoesNotExistException`|  `ProcessingFailureException` |
|`ExceededRuntimeLimitException`|  `ProcessingFailureException` |
|`ExpiredPrivateKeyException`|  `ProcessingFailureException` |
|`ExpiredPublicKeyException`|  `ProcessingFailureException` |
|`ExportFailureException`|  `ProcessingFailureException` |
|`ExportNotReadyException`|  `ProcessingFailureException` |
|`FailedToChangePasswordException`|  `ProcessingFailureException` |
|`FileLockedException`|  `ProcessingFailureException` |
|`FileNotUploadedException`|  `ProcessingFailureException` |
|`FilePendingProcessingException`|  `ProcessingFailureException` |
|`FileProcessingErrorException`|  `ProcessingFailureException` |
|`FileTooBigToDecryptException`|  `ProcessingFailureException` |
|`FileTooBigToEncryptException`|  `ProcessingFailureException` |
|`FileUploadedToWrongRegionException`|  `ProcessingFailureException` |
|`FilenameTooLongException`|  `ProcessingFailureException` |
|`FolderLockedException`|  `ProcessingFailureException` |
|`FolderNotEmptyException`|  `ProcessingFailureException` |
|`HistoryUnavailableException`|  `ProcessingFailureException` |
|`InvalidBundleCodeException`|  `ProcessingFailureException` |
|`InvalidFileTypeException`|  `ProcessingFailureException` |
|`InvalidFilenameException`|  `ProcessingFailureException` |
|`InvalidPriorityColorException`|  `ProcessingFailureException` |
|`InvalidRangeException`|  `ProcessingFailureException` |
|`InvalidSiteException`|  `ProcessingFailureException` |
|`ModelSaveErrorException`|  `ProcessingFailureException` |
|`MultipleProcessingErrorsException`|  `ProcessingFailureException` |
|`PathTooLongException`|  `ProcessingFailureException` |
|`RecipientAlreadySharedException`|  `ProcessingFailureException` |
|`RemoteServerErrorException`|  `ProcessingFailureException` |
|`ResourceBelongsToParentSiteException`|  `ProcessingFailureException` |
|`ResourceLockedException`|  `ProcessingFailureException` |
|`SubfolderLockedException`|  `ProcessingFailureException` |
|`TwoFactorAuthenticationCodeAlreadySentException`|  `ProcessingFailureException` |
|`TwoFactorAuthenticationCountryBlacklistedException`|  `ProcessingFailureException` |
|`TwoFactorAuthenticationGeneralErrorException`|  `ProcessingFailureException` |
|`TwoFactorAuthenticationMethodUnsupportedErrorException`|  `ProcessingFailureException` |
|`TwoFactorAuthenticationUnsubscribedRecipientException`|  `ProcessingFailureException` |
|`UpdatesNotAllowedForRemotesException`|  `ProcessingFailureException` |
|`DuplicateShareRecipientException`|  `RateLimitedException` |
|`ReauthenticationRateLimitedException`|  `RateLimitedException` |
|`TooManyConcurrentLoginsException`|  `RateLimitedException` |
|`TooManyConcurrentRequestsException`|  `RateLimitedException` |
|`TooManyLoginAttemptsException`|  `RateLimitedException` |
|`TooManyRequestsException`|  `RateLimitedException` |
|`TooManySharesException`|  `RateLimitedException` |
|`AgentUnavailableException`|  `ServiceUnavailableException` |
|`AutomationsUnavailableException`|  `ServiceUnavailableException` |
|`MigrationInProgressException`|  `ServiceUnavailableException` |
|`SiteDisabledException`|  `ServiceUnavailableException` |
|`UploadsUnavailableException`|  `ServiceUnavailableException` |
|`AccountAlreadyExistsException`|  `SiteConfigurationException` |
|`AccountOverdueException`|  `SiteConfigurationException` |
|`NoAccountForSiteException`|  `SiteConfigurationException` |
|`SiteWasRemovedException`|  `SiteConfigurationException` |
|`TrialExpiredException`|  `SiteConfigurationException` |
|`TrialLockedException`|  `SiteConfigurationException` |
|`UserRequestsEnabledRequiredException`|  `SiteConfigurationException` |

## {frontmatter.title}

Certain API operations return lists of objects. When the number of objects in the list is large,
the API will paginate the results.

The Files.com PHP SDK automatically paginates through lists of objects by default.

```php title="Example Request" hasDataFormatSelector
// true by default
Files::$autoPaginate = true;

try {
  $files = \Files\Model\Folder::listFor($path, [
    'search' => "some-partial-filename"
  ]);
  foreach ($files as $file) {
    // Operate on $file
  }
} catch (\Files\NotAuthenticated\InvalidUsernameOrPasswordException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

## Case Sensitivity

The Files.com API compares files and paths in a case-insensitive manner.
 For related documentation see [Case Sensitivity Documentation](https://www.files.com/docs/files-and-folders/file-system-semantics/case-sensitivity).

The `PathUtil::same` function in the Files.com SDK is designed to help you determine if two paths on
your native file system would be considered the same on Files.com. This is particularly important
when handling errors related to duplicate file names and when developing tools for folder
synchronization.

```php title="Compare Case-Insensitive Files and Paths"
if(\Files\Util\PathUtil::same("Fïłèńämê.Txt", "filename.txt")) {
    echo "Paths are the same\n";
}
```

## Mock Server

Files.com publishes a Files.com API server, which is useful for testing your use of the Files.com
SDKs and other direct integrations against the Files.com API in an integration test environment.

It is a Ruby app that operates as a minimal server for the purpose of testing basic network
operations and JSON encoding for your SDK or API client. It does not maintain state and it does not
deeply inspect your submissions for correctness.

Eventually we will add more features intended for integration testing, such as the ability to
intentionally provoke errors.

Download the server as a Docker image via [Docker Hub](https://hub.docker.com/r/filescom/files-mock-server).

The Source Code is also available on [GitHub](https://github.com/Files-com/files-mock-server).

A README is available on the GitHub link.

## Upgrading

### Upgrading to Version 2.0 from previous versions

In Version 2.0, the Files.com PHP SDK was updated to comply with both the
[PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard and the
[PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading standard. No new
classes were added or any existing classes removed, but some were moved to
comply with the PSR-4 standard. If a client of the sdk references the moved
classes, the client code will need to be updated to reference the new location
of these classes.

#### Exception Classes

The affected classes were primarily Exception classes. Exceptions were moved
into their own namespace (and source files).

The following table shows the classes that were changed for compliance

###### Base Exceptions

The Base exception were moved from the `\Files` namespace to the `\Files\Exception` namespace.

Examples of Base Exceptions Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\ApiException` | `Files\Exception\ApiException`  |
| `\Files\FilesException` | `Files\Exception\FilesException`  |
| `\Files\ConfigurationException` | `Files\Exception\ConfigurationException`  |

##### BadRequest Exceptions

The BadRequest group of exceptions were moved from the `\Files\BadRequest`
namespace to the `\Files\Exception\BadRequest` namespace.

Example of BadRequest Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\BadRequest\AgentUpgradeRequiredException` | `Files\Exception\BadRequest\AgentUpgradeRequiredException`  |

##### NotAuthenticated Exceptions

The NotAuthenticated group of exceptions were moved from the
`\Files\NotAuthenticated` namespace to the `\Files\Exception\NotAuthenticated`
namespace.

Example of NotAuthenticated Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\NotAuthenticated\AdditionalAuthenticationRequiredException` | `Files\Exception\NotAuthenticated\AdditionalAuthenticationRequiredException`  |

##### NotAuthorized Exceptions

The NotAuthorized group of exceptions were moved from the `\Files\NotAuthorized`
namespace to the `\Files\Exception\NotAuthorized` namespace.

Example of NotAuthorized Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\NotAuthorized\ApiKeyIsDisabledException` | `Files\Exception\NotAuthorized\ApiKeyIsDisabledException`  |

##### NotFound Exceptions

The NotFound group of exceptions were moved from the `\Files\NotFound` namespace to the `\Files\Exception\NotFound` namespace.

Example of NotFound Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\NotFound\ApiKeyNotFoundException` | `Files\Exception\NotFound\ApiKeyNotFoundException`  |

##### ProcessingFailure Exceptions

The ProcessingFailure group of exceptions were moved from the
`\Files\ProcessingFailure` namespace to the `\Files\Exception\ProcessingFailure`
namespace.

Example of ProcessingFailure Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\ProcessingFailure\AlreadyCompletedException` | `Files\Exception\ProcessingFailure\AlreadyCompletedException`  |

##### RateLimited Exceptions

The ProcessingFailure group of exceptions were moved from the
`\Files\RateLimited` namespace to the `\Files\Exception\RateLimited` namespace.

Example of RateLimited Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\RateLimited\DuplicateShareRecipientException` | `Files\Exception\RateLimited\DuplicateShareRecipientException`  |

##### ServiceUnavailable Exceptions

The ServiceUnavailable group of exceptions were moved from the
`\Files\ServiceUnavailable` namespace to the
`\Files\Exception\ServiceUnavailable` namespace.

Example of ServiceUnavailable Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\ServiceUnavailable\AgentUnavailableException` | `Files\Exception\ServiceUnavailable\AgentUnavailableException`  |

##### SiteConfiguration Exceptions

The SiteConfiguration group of exceptions were moved from the
`\Files\SiteConfiguration` namespace to the `\Files\Exception\SiteConfiguration`
namespace.

Example of SiteConfiguration Classes moved.

| SDK < 2.0 Class Location   | SDK >= 2.0 Class Location |
|------------------------------|------------------|
| `\Files\SiteConfiguration\AccountAlreadyExistsException` | `Files\Exception\SiteConfiguration\AccountAlreadyExistsException`  |
