# Files.com PHP SDK

The Files.com PHP SDK provides a direct, high performance integration to Files.com from applications written in PHP.

Files.com is the cloud-native, next-gen MFT, SFTP, and secure file-sharing platform that replaces brittle legacy servers with one always-on, secure fabric. Automate mission-critical file flows—across any cloud, protocol, or partner—while supporting human collaboration and eliminating manual work.

With universal SFTP, AS2, HTTPS, and 50+ native connectors backed by military-grade encryption, Files.com unifies governance, visibility, and compliance in a single pane of glass.

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

Set this to the full https:// URL of your Files.com subdomain (e.g. `https://MY-SUBDOMAIN.files.com`).
This is not required in most cases, but one benefit of setting it is that it ensures that authentication failures will be logged to your site's API logs.  Without setting this, we won't know which site to associate the authentication failure with, and it won't be logged to your site's API logs.
This is always required if your site is configured to disable global acceleration.
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

#### Special note about the List Folder Endpoint

For historical reasons, and to maintain compatibility
with a variety of other cloud-based MFT and EFSS services, Folders will always be listed before Files
when listing a Folder.  This applies regardless of the sorting parameters you provide.  These *will* be
used, after the initial sort application of Folders before Files.

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

## Paths

Files.com preserves the spelling of file and folder paths while comparing them using shared case and Unicode rules. Use the SDK comparison helpers when matching paths locally.
<div></div>

### Capitalization

Files.com uses case-insensitive path matching based on its fixed Unicode comparison map.

For example, the following paths have the same comparison key:

| Path Variant                          | Comparison Key              |
|---------------------------------------|------------------------------|
| `Documents/Reports/Q1.pdf`            | `documents/reports/q1.pdf`  |
| `documents/reports/q1.PDF`            | `documents/reports/q1.pdf`  |
| `DOCUMENTS/REPORTS/Q1.PDF`            | `documents/reports/q1.pdf`  |

This behavior applies across:
- API requests
- Folder and file lookup operations
- Automations and workflows

See also: [Case Sensitivity Documentation](https://www.files.com/docs/files-and-folders/case-sensitivity/)

The `PathUtil::same` function in the Files.com SDK is designed to help you determine if two paths on
your native file system would be considered the same on Files.com. This is particularly important
when handling errors related to duplicate file names and when developing tools for folder
synchronization.

```php title="Compare Case-Insensitive Files and Paths"
if(\Files\Util\PathUtil::same("Fïłèńämê.Txt", "filename.txt")) {
    echo "Paths are the same\n";
}
```

### Slashes

Use `/` between folder and file names, without leading or trailing slashes. SDK normalization helpers convert backslashes to `/`, remove duplicate separators, and discard exact `.` and `..` components. Discarding `..` leaves the preceding folder name intact.

| Input | Normalized path |
|-------|-----------------|
| `folder/subfolder/file.txt` | `folder/subfolder/file.txt` |
| `/folder/subfolder/file.txt` | `folder/subfolder/file.txt` |
| `folder/subfolder/file.txt/` | `folder/subfolder/file.txt` |
| `//folder//file.txt` | `folder/file.txt` |
| `folder/../file.txt` | `folder/file.txt` |

<div></div>

### Unicode and Path Comparison

Files.com compares paths using a fixed mapping shared by the server and SDKs. It treats case and many accent differences as equivalent: `Résumé.txt` and `resume.txt` identify the same file, as do `q` followed by a combining acute accent and `q`. The mapping also handles other equivalences, such as Hiragana and Katakana. Lowercasing or applying a standard Unicode normalization form alone does not reproduce these rules.

SDK comparison helpers normalize path separators and dot segments, then apply the bundled [versioned comparison map](https://github.com/Files-com/files-sdk-javascript/blob/master/shared/path_comparison.json). The [shared examples](https://github.com/Files-com/files-sdk-javascript/blob/master/shared/comparison_examples.json) give exact comparison results for integrations that implement their own matching. The map uses hexadecimal Unicode scalar values as keys: a missing entry preserves the character, an empty replacement removes it, and other replacements may contain several characters. Apply each replacement once without normalizing or lowercasing the result again.

Use comparison results only for matching. Send the original path spelling in API requests and preserve it for display and local filenames; comparison results can have a different spelling or length.

Trailing whitespace is significant for comparison. `report.txt` and `report.txt ` are different file paths, and SDK helpers preserve spaces, tabs, and newlines. Folder names cannot end in whitespace. See [Unicode Normalization](https://www.files.com/docs/files-and-folders/file-system-semantics/unicode-normalization) for the complete path rules.

<div></div>

## Workspaces

A Workspace groups files, users, groups, Partners, integrations, and workflows within a Files.com Site. An integration can provision a Workspace for a department or project and delegate its operation to a team without making that team Site Administrators. Every Site has a Default Workspace, with ID `0`; additional Workspaces have their own IDs and root folders.

Account membership, request context, and permission grants serve different purposes. Creating an account in a Workspace determines where it belongs. Selecting a Workspace determines which resources a request operates on. A permission grant determines what the caller can do there. Selecting a Workspace never grants access to it.

### Accounts and Administrative Access

A user's or group's `workspace_id` identifies the Workspace the account belongs to. Accounts belonging to a Custom Workspace stay within it. Default Workspace users and groups can receive permissions in one or more Custom Workspaces while keeping their existing accounts in Workspace `0`.

| Account | Workspace Administrator assignment | Scope |
| --- | --- | --- |
| User belonging to a Custom Workspace | Set the user's `workspace_admin` to `true`. | That user's own Custom Workspace. |
| Default Workspace user | Create an `admin` Permission for the user on a Custom Workspace's root folder. | Each Custom Workspace with a root grant. |
| Default Workspace group | Create an `admin` Permission for the group on a Custom Workspace's root folder. | Every member inherits administration of each Workspace with a root grant. |

`workspace_admin` is not a summary of a user's effective administrative access. A Default Workspace user can administer a Custom Workspace through a direct or group root grant while their `workspace_admin` remains `false`. Groups have no `workspace_admin` field. See [Users](https://developers.files.com/php/resources/user-accounts/users) and [Groups](https://developers.files.com/php/resources/user-accounts/groups) for account fields.

An `admin` grant on the **Custom Workspace root** provides full Workspace Administrator authority over its files, users, groups, Partners, workflows, and integrations. An `admin` grant on a subfolder provides Folder Admin authority over that folder and its descendants; it does not provide Workspace administration. Other permission levels provide their corresponding folder access without Workspace administration. [Permissions](https://developers.files.com/php/resources/user-accounts/permissions) defines the levels.

Site Administrators manage cross-Workspace assignments to Default Workspace accounts. Workspace Administrators manage accounts and permissions within their own scope. Site Administrators retain access to every Workspace; adding a Workspace grant does not narrow Site Administrator authority. The [product documentation](https://www.files.com/docs/workspaces/workspace-administrators) explains the administrator's operational scope and site-wide controls.

### Request Context and API Keys

You can include the `X-Files-Workspace-Id` REST header to select a Workspace for a request. SDK request options and CLI configuration send that same selection. When a Workspace is selected, Workspace-scoped resources are listed, created, and changed within that context, and ordinary paths are relative to its root.

A resource's `workspace_id` request field describes the resource's Workspace membership. It is separate from the SDK's Workspace request option or REST header. Creating a Workspace-scoped resource in a Custom Workspace defaults its `workspace_id` to the selected Workspace; a mismatching membership value is rejected with `not-authorized/insufficient-permission-for-params`.

Selecting another Workspace with an API key requires a **Full Access key created in the Default Workspace**. A user key follows that user's current access, including group permissions. A site-wide Full Access key created in the Default Workspace has Site Administrator authority in every Workspace. A Files Only key stays in its creation Workspace, even if its user has cross-Workspace access. Any key created in a Custom Workspace stays within that Workspace. Selecting another context with these confined keys is rejected with `bad-request/invalid-workspace-id-header`.

An account belonging to a Custom Workspace is scoped there when it authenticates normally. For a Default Workspace user, explicitly select the intended Workspace for an integration rather than relying on an interactive login preference. [API Keys](https://developers.files.com/php/resources/developers/api-keys) and [Authentication](https://developers.files.com/php/overview/authentication) cover credentials.

The Files.com PHP SDK supports workspace scoping by using the `\Files\Files::setWorkspaceId` configuration method. Scope a single request by passing `workspace_id` in the request options.

The adjacent scoping example uses a credential authorized for the selected Workspace. A group member uses their own Full Access user key from the Default Workspace; the Site Administrator credential used to assign the grant is not needed for their day-to-day work.

```php title="Example Request"
\Files\Files::setWorkspaceId(123);

\Files\Model\Folder::listFor("/", [], ["workspace_id" => 456]);
```

### Delegating a Workspace to an Existing Group

An operations team already represented by a Default Workspace group can administer a Custom Workspace through one root Permission. The group and its members stay in the Default Workspace, so the same team can receive different access in other Workspaces.

First retrieve the target [Workspace](https://developers.files.com/php/resources/settings/workspaces) and [Group](https://developers.files.com/php/resources/user-accounts/groups) IDs as a Site Administrator in Workspace `0`. The examples use Workspace `123`, group `456`, and member user `789`; replace them with your own IDs. Confirm that the group belongs to Workspace `0` and that the intended user is a member.

Create the Permission using a Default Workspace Full Access site-wide key or a Full Access user key belonging to a Site Administrator. Keep the request context at `0` and use the qualified root path `_/Workspaces/123`. Set `group_id` to the group's ID, `permission` to `admin`, and `recursive` to `true`. Save the returned Permission `id` for later removal. For an individual Default Workspace user, use `user_id` instead of `group_id`.

For a Default Workspace group, a Site Administrator can also select Workspace `123` and use an empty `path` to grant access to its root. The qualified path in Workspace `0` works for both Default Workspace users and groups and keeps the account scope and target Workspace explicit. Appending a subfolder to the path would grant Folder Admin access instead of Workspace Administrator authority.

After the grant, make a request as the member, using their own credential with Workspace `123` selected, such as listing that Workspace's root folder. That member can work with the Workspace's files and perform Workspace Administrator operations, such as managing its users, Partners, and integrations. A Site Administrator's successful request does not establish that the member has the intended access.

```php title="Grant group administration"
\Files\Files::setWorkspaceId(0);
$grant = \Files\Model\Permission::create([
    "path" => "_/Workspaces/123",
    "group_id" => 456,
    "permission" => "admin",
    "recursive" => true,
], ["workspace_id" => 0]);
echo $grant->getId();
```

### Permission Inspection and Removal

List the member's Permissions with `user_id` and `include_groups=true` to include grants inherited through group membership. Listing only direct user grants can miss the Permission that provides Workspace administration. In Workspace `0`, the Custom Workspace root appears as `_/Workspaces/123`; in Workspace `123`, paths are relative to that root. Inspect the root path and `permission=admin`, rather than treating the user's `workspace_admin` field as their effective administrative access.

Permission lists show individual grants, rather than a single flag for effective administrative access. Membership in several groups combines their access. A Permission using `group_ids` instead of `group_id` requires membership in all the specified groups; it is not a shorthand for assigning the same grant to several independent groups.

Removing a member ends access received through that group. Deleting the root Permission ends the group's Workspace Administrator grant for every member. These changes leave independent direct and other group grants in place, so review all applicable grants when withdrawing access. Default Workspace user API keys follow those permission changes without being recreated.

Group membership maintained through SCIM follows the same rule. A Group Admin allowed to add members can give those users the group's existing Workspace Administrator access. Choose who manages the group with that authority in mind.

Delete the Permission by its returned `id` as the Site Administrator in Workspace `0`. The removal examples use Permission ID `9001`; replace it with the ID returned by your create request. Permissions are created and deleted, rather than updated in place. If narrower folder access is still needed, assign it explicitly; deleting a broad grant does not restore narrower grants it previously replaced.

```php title="Inspect member grants and remove the group grant"
$grants = \Files\Model\Permission::all([
    "user_id" => "789", "include_groups" => true,
], ["workspace_id" => 0]);
(new \Files\Model\Permission(["id" => 9001], ["workspace_id" => 0]))->delete();
```

## Foreign Language Support

The Files.com PHP SDK supports localized responses by using the `\Files\Files::setLanguage()` configuration method.
When configured, this guides the API in selecting a preferred language for applicable response content.

Language support currently applies to select human-facing fields only, such as notification messages
and error descriptions.

If the specified language is not supported or the value is omitted, the API defaults to English.

```shell title="Example Request"
\Files\Files::setLanguage('es');
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
|`DestinationSiteMismatchException`|  `BadRequestException` |
|`DoesNotSupportSortingException`|  `BadRequestException` |
|`FolderMustNotBeAFileException`|  `BadRequestException` |
|`FoldersNotAllowedException`|  `BadRequestException` |
|`InternalGeneralErrorException`|  `BadRequestException` |
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
|`InvalidSearchQueryException`|  `BadRequestException` |
|`InvalidSortFieldException`|  `BadRequestException` |
|`InvalidSortFilterCombinationException`|  `BadRequestException` |
|`InvalidUploadOffsetException`|  `BadRequestException` |
|`InvalidUploadPartGapException`|  `BadRequestException` |
|`InvalidUploadPartSizeException`|  `BadRequestException` |
|`InvalidWorkspaceIdHeaderException`|  `BadRequestException` |
|`MethodNotAllowedException`|  `BadRequestException` |
|`MultipleSortParamsNotAllowedException`|  `BadRequestException` |
|`NoValidInputParamsException`|  `BadRequestException` |
|`OffsetUploadNotAllowedWithMalwareScanningException`|  `BadRequestException` |
|`PartNumberTooLargeException`|  `BadRequestException` |
|`PathCannotHaveTrailingWhitespaceException`|  `BadRequestException` |
|`ReauthenticationNeededFieldsException`|  `BadRequestException` |
|`RequestBodyTooLargeException`|  `BadRequestException` |
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
|`ApiKeyOnlyForFileOperationsException`|  `NotAuthorizedException` |
|`ApiKeyOnlyForMobileAppException`|  `NotAuthorizedException` |
|`ApiKeyOnlyForOfficeIntegrationException`|  `NotAuthorizedException` |
|`BillingInformationHiddenException`|  `NotAuthorizedException` |
|`BillingPermissionRequiredException`|  `NotAuthorizedException` |
|`BundleMaximumUsesReachedException`|  `NotAuthorizedException` |
|`BundlePermissionRequiredException`|  `NotAuthorizedException` |
|`CannotAdministerHigherLevelUserException`|  `NotAuthorizedException` |
|`CannotLoginWhileUsingKeyException`|  `NotAuthorizedException` |
|`CantActForOtherUserException`|  `NotAuthorizedException` |
|`ContactAdminForPasswordChangeHelpException`|  `NotAuthorizedException` |
|`FilesAgentFailedAuthorizationException`|  `NotAuthorizedException` |
|`FolderAdminOrBillingPermissionRequiredException`|  `NotAuthorizedException` |
|`FolderAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`FullPermissionRequiredException`|  `NotAuthorizedException` |
|`HistoryPermissionRequiredException`|  `NotAuthorizedException` |
|`InAppAiAssistantUnavailableException`|  `NotAuthorizedException` |
|`InsufficientPermissionForParamsException`|  `NotAuthorizedException` |
|`InsufficientPermissionForSiteException`|  `NotAuthorizedException` |
|`MoverAccessDeniedException`|  `NotAuthorizedException` |
|`MoverPackageRequiredException`|  `NotAuthorizedException` |
|`MustAuthenticateWithApiKeyException`|  `NotAuthorizedException` |
|`NeedAdminPermissionForInboxException`|  `NotAuthorizedException` |
|`NonAdminsMustQueryByFolderOrPathException`|  `NotAuthorizedException` |
|`NotAllowedToCreateBundleException`|  `NotAuthorizedException` |
|`NotEnqueuableSyncException`|  `NotAuthorizedException` |
|`PasswordChangeNotRequiredException`|  `NotAuthorizedException` |
|`PasswordChangeRequiredException`|  `NotAuthorizedException` |
|`PaymentMethodErrorException`|  `NotAuthorizedException` |
|`PreviewOnlyPermissionCannotDownloadException`|  `NotAuthorizedException` |
|`ReadOnlySessionException`|  `NotAuthorizedException` |
|`ReadPermissionRequiredException`|  `NotAuthorizedException` |
|`ReauthenticationFailedException`|  `NotAuthorizedException` |
|`ReauthenticationFailedFinalException`|  `NotAuthorizedException` |
|`ReauthenticationNeededActionException`|  `NotAuthorizedException` |
|`RecaptchaFailedException`|  `NotAuthorizedException` |
|`RemoteDesktopDebugLoggingDisabledException`|  `NotAuthorizedException` |
|`RootFolderBehaviorSiteAdminRequiredException`|  `NotAuthorizedException` |
|`RootFolderBehaviorSkipSiteAdminRequiredException`|  `NotAuthorizedException` |
|`SelfManagedRequiredException`|  `NotAuthorizedException` |
|`SiteAdminOrPartnerAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`SiteAdminOrWorkspaceAdminOrFolderAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`SiteAdminOrWorkspaceAdminOrPartnerAdminOrFolderAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`SiteAdminOrWorkspaceAdminOrPartnerAdminPermissionRequiredException`|  `NotAuthorizedException` |
|`SiteAdminOrWorkspaceAdminPermissionRequiredException`|  `NotAuthorizedException` |
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
|`GroupNotFoundException`|  `NotFoundException` |
|`InboxNotFoundException`|  `NotFoundException` |
|`NestedNotFoundException`|  `NotFoundException` |
|`PlanNotFoundException`|  `NotFoundException` |
|`SiteNotFoundException`|  `NotFoundException` |
|`UserNotFoundException`|  `NotFoundException` |
|`AgentPushUpdateBlockedException`|  `ProcessingFailureException` |
|`AgentUnavailableException`|  `ProcessingFailureException` |
|`AiTaskCannotBeRunManuallyException`|  `ProcessingFailureException` |
|`AlreadyCompletedException`|  `ProcessingFailureException` |
|`AutomationCannotBeRunManuallyException`|  `ProcessingFailureException` |
|`BehaviorNotAllowedOnRemoteServerException`|  `ProcessingFailureException` |
|`BufferedUploadDisabledForThisDestinationException`|  `ProcessingFailureException` |
|`BundleOnlyAllowsPreviewsException`|  `ProcessingFailureException` |
|`BundleOperationRequiresSubfolderException`|  `ProcessingFailureException` |
|`ConfigurationLockedPathException`|  `ProcessingFailureException` |
|`CouldNotCreateParentException`|  `ProcessingFailureException` |
|`DestinationExistsException`|  `ProcessingFailureException` |
|`DestinationFolderLimitedException`|  `ProcessingFailureException` |
|`DestinationParentConflictException`|  `ProcessingFailureException` |
|`DestinationParentDoesNotExistException`|  `ProcessingFailureException` |
|`ExceededRuntimeLimitException`|  `ProcessingFailureException` |
|`ExpectationAlreadyHasOpenWindowException`|  `ProcessingFailureException` |
|`ExpectationNotManualTriggerException`|  `ProcessingFailureException` |
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
|`InvalidZipFileException`|  `ProcessingFailureException` |
|`MetadataNotSupportedOnRemotesException`|  `ProcessingFailureException` |
|`ModelSaveErrorException`|  `ProcessingFailureException` |
|`MultipleProcessingErrorsException`|  `ProcessingFailureException` |
|`PathTooLongException`|  `ProcessingFailureException` |
|`RecipientAlreadySharedException`|  `ProcessingFailureException` |
|`RemoteEntryReadOnlyException`|  `ProcessingFailureException` |
|`RemoteServerErrorException`|  `ProcessingFailureException` |
|`ResourceBelongsToParentSiteException`|  `ProcessingFailureException` |
|`ResourceLockedException`|  `ProcessingFailureException` |
|`SubfolderLockedException`|  `ProcessingFailureException` |
|`SyncInProgressException`|  `ProcessingFailureException` |
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
|`AutomationsUnavailableException`|  `ServiceUnavailableException` |
|`LockOperationBusyException`|  `ServiceUnavailableException` |
|`MigrationInProgressException`|  `ServiceUnavailableException` |
|`SearchUnavailableException`|  `ServiceUnavailableException` |
|`SiteDisabledException`|  `ServiceUnavailableException` |
|`UploadsUnavailableException`|  `ServiceUnavailableException` |
|`AccountAlreadyExistsException`|  `SiteConfigurationException` |
|`AccountOverdueException`|  `SiteConfigurationException` |
|`NoAccountForSiteException`|  `SiteConfigurationException` |
|`SiteWasRemovedException`|  `SiteConfigurationException` |
|`TrialExpiredException`|  `SiteConfigurationException` |
|`TrialLockedException`|  `SiteConfigurationException` |
|`UserRequestsEnabledRequiredException`|  `SiteConfigurationException` |

## Pagination

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

## Mock Server

Files.com publishes a mock Files.com API server, which is useful for testing your use of the Files.com
SDKs and other direct integrations against the Files.com API in an integration test environment.
It never checks credentials: send any placeholder API key, and never use real Files.com credentials
with it.

The server has two modes, chosen when it starts:

* **Legacy mode** (the default) checks required parameters and parameter types, then returns a fixed
  example response for each API endpoint. It does not maintain state and it does not deeply inspect
  your submissions for correctness, which makes it useful for testing basic network operations and
  JSON encoding for your SDK or API client.
* **Simulation mode** keeps records, files and folders in memory, so a test can create, list, update
  and delete resources, upload a file and download the same bytes, and make chosen requests fail,
  stall or lose their connection on purpose. Requests it does not simulate fail with a clear error
  instead of returning an example response.

Start the server from its source with Ruby and Bundler. `FILES_MOCK_MODE` is read once at startup:
leaving it unset or setting it to `legacy` starts legacy mode, `simulation` starts simulation mode,
and any other value stops startup with an error. Legacy mode listens on port 4041 on all IPv4
interfaces, and simulation mode on `127.0.0.1:4041`.

Simulation mode keeps its state only in the server process. Its control endpoints under
`/__files_mock/v1` report when the server is ready, reset it with your fixtures, add fault rules
and return the journal of the requests it received. It refuses work over its limits instead of
truncating it. The README in the source describes all of these, the operations it simulates and
how to configure its limits.

Download the server as a Docker image via [Docker Hub](https://hub.docker.com/r/filescom/files-mock-server).
The image's `latest` tag moves to whichever server was published last, so it need not include
simulation mode; to run exactly the server the README describes, build the image from the source.

The Source Code is also available on [GitHub](https://github.com/Files-com/files-mock-server).

```shell title="Start the Mock Server"
bundle install

## Legacy mode
bundle exec puma

## Simulation mode
FILES_MOCK_MODE=simulation bundle exec puma
```

## Upgrading

### Upgrading to Files.com PHP SDK 2.0? Learn about namespace changes for exception classes to comply with PSR-4 and PSR-12 standards.

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
