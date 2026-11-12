# Files.com PHP SDK

The Files.com PHP SDK provides a direct, high performance integration to Files.com from applications written in PHP.

Files.com is the cloud-native, next-gen MFT, SFTP, and secure file-sharing platform that replaces brittle legacy servers with one always-on, secure fabric. Automate mission-critical file flows—across any cloud, protocol, or partner—while supporting human collaboration and eliminating manual work.

With universal SFTP, AS2, HTTPS, and 50+ native connectors backed by military-grade encryption, Files.com unifies governance, visibility, and compliance in a single pane of glass.

The content included here should be enough to get started, but please visit our
[Developer Documentation Website](https://developers.files.com/php/) for the complete documentation.

## Introduction

For integration choices and credential scope, start with [SDKs & APIs](https://www.files.com/docs/clients-and-protocols/sdks-and-apis). [Scripts, APIs & Application Workflows](https://www.files.com/docs/automated-workflows/scripts-apis-and-application-workflows) explains how to coordinate Files.com operations with the work your application owns.

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

To initiate a support conversation, you can send an [Authenticated Support Request](https://www.files.com/docs/support-and-services/requesting-support) or simply send an E-Mail to support@files.com.

## Authentication

There are two ways to authenticate: API Key authentication and Session-based authentication.

### Authenticate with an API Key

Authenticating with an API key is the recommended authentication method for most scenarios, and is
the method used in the examples on this site.

To use an API Key, first generate an API key from the [web
interface](https://www.files.com/docs/security/authentication-and-credentials/api-keys) or [via the API or an
SDK](https://developers.files.com/php/resources/developers/api-keys).

A user key follows its account's permissions, narrowed by the key's permission scope, Workspace, and any path restriction on file operations. It can perform administrative operations only when both the account and key permit them. [API Keys](https://www.files.com/docs/security/authentication-and-credentials/api-keys) explains credential scope and lifecycle; [Workspaces](https://developers.files.com/php/overview/workspaces) explains request context and delegated administration.

```php title="Example Request"
\Files\Files::setApiKey('YOUR_API_KEY');

try {
  # Alternatively, you can specify the API key on a per-object basis in the second parameter to a model constructor.
  $user = new \Files\Model\User($params, array('api_key' => 'YOUR_API_KEY'));

  # You may also specify the API key on a per-request basis in the final parameter to static methods.
  \Files\Model\User::find($id, $params, array('api_key' => 'YOUR_API_KEY'));

  # An api_key given either way is used instead of a session set with setSessionId().
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

Don't forget to replace the placeholder, `YOUR_API_KEY`, with your actual API key.

### Authenticate with a Session

Create a session with the username and password of an active user. The session uses that user's permissions, including any delegated administrative access. [Workspaces](https://developers.files.com/php/overview/workspaces) describes account scope and administrative access.

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
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
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

  # A session_id given either way is used before an api_key given with it, and an api_key given either way instead of
  # the session set with setSessionId(); without either, that session is used before the API key set with setApiKey().
  # One that PHP reads as false (null, '', 0, '0', false) is ignored. A request given a session_id or api_key sends only
  # that credential, and no X-FilesAPI-Auth or X-FilesAPI-Key header of its 'headers' option, in any letter case.
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

#### Logging Out

User sessions can be ended by calling the `Session::destroy` method.

```php title="Example Request"
try {
  \Files\Model\Session::destroy();
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

## Configuration

Global configuration is performed by setting properties directly on the `\Files\Files` class.

### Configuration Options

#### Auto Paginate

When `true`, list methods such as `Folder::listFor()` fetch every page and return one array; when `false`, they
return only the first page. The default value is `true`. Page methods such as `Folder::listForPage()` fetch one page
at a time, whatever this setting is; see Pagination.
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
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
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
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

```php title="Range Filter Example"
try {
  // users who have logged in on or after 2024-01-01
  $users = \Files\Model\User::list(array(
    'filter_gteq' => array("last_login_at" => "2024-01-01")
  ));

  foreach ($users as $value) {
    print("User username: " . $value->getUserName() . "\n");
  }
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
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
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
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
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
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

See also: [Case Sensitivity Documentation](https://www.files.com/docs/files-and-folders/file-system-semantics/case-sensitivity)

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
fashion that describe the general premise of the originating error.  The message of an exception for an HTTP error
response, from `getMessage()`, names the request's method and HTTP status, and for an API error its type; it never
quotes the response body or the request URL, which can contain credentials.  The message of a transport error has
the request URL replaced with `[redacted]`.  API exceptions provide the API's own error message and details through
`getError()`, `getTitle()` and the other getters listed below.

Use standard PHP exception handling to detect and deal with errors.  It is generally recommended to catch specific errors first, then
catch the general `Files\Exception\FilesException` exception as a catch-all.

```php title="Example Error Handling"
try {
  $session = Files\Model\Session::create(['username' => 'USERNAME', 'password' => 'BADPASSWORD']);
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Error Types

#### SDK Errors

SDK errors are general errors that occur within the SDK code.  These errors generate exceptions.  Each of these
exception classes inherit from a standard `Exception` base class.

The `Api*` exceptions below extend `ApiConnectionException`, which classifies how a request the SDK sent failed,
apart from an API error: the API could not be reached, the HTTP client failed, the API answered with a 5xx status,
or the SDK could not use the response.

```shell title="Example SDK Exception Class Inheritance Structure"
Files\Exception\ApiConnectException ->
Files\Exception\ApiConnectionException ->
Files\Exception\FilesException ->
Exception
```
##### SDK Exception Classes

| Exception Class Name| Description |
| --------------- | ------------ |
| `ApiConnectionException`| The parent of the `Api*` exceptions below |
| `ApiBadResponseException`| The SDK could not use the response, such as an HTTP error status below 500 without a readable Files.com error object, a model that is not a JSON object, a list that is not a JSON array, or a list cursor that was already followed |
| `ApiConnectException`| The Files.com API cannot be reached, or the request timed out |
| `ApiRequestException`| The HTTP client could not complete the request, for example because of a TLS error |
| `ApiServerException`| The API service answered with a 5xx status, whatever the response body holds |
| `ApiTooManyRedirectsException`| The API service redirected too many times |
| `ApiTransferException`| The HTTP client failed to transfer the request |
| `ConfigurationException`| Invalid SDK configuration parameters |
| `EmptyPropertyException`| An required property was empty |
| `InvalidParameterException`| A passed in parameter is invalid |
| `MissingParameterException`| A method parameter is missing |
| `NotImplementedException`| The called method has not be implemented by the SDK |

#### API Errors

API errors are errors returned by the Files.com API.  Each exception class inherits from an error group base class.
The error group base class indicates a particular type of error.

An HTTP error response with a status below 500 whose body is a Files.com error object raises the exception class its
`type` names, or `ApiException` itself for a `type` the SDK does not recognize.  The getters `getType()`,
`getError()`, `getTitle()`, `getHttpCode()`, `getDetail()`, `getErrors()` and `getData()` return the error object's
fields, or `null` for a field it does not include.

```shell title="Example API Exception Class Inheritance Structure"
Files\Exception\NotAuthorized\FolderAdminPermissionRequiredException ->
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
|`FilesNativeStorageOverloadedException`|  `RateLimitedException` |
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

Each list method that pages through its results with a cursor has a page counterpart that fetches
one page at a time: `Folder::listFor()` has `Folder::listForPage()`, `User::all()` has
`User::listPage()`, and so on. A page method takes the same parameters and options as its list
method and returns a `\Files\Page`.

### Automatic Pagination

`autoPagingIterator()` yields every item from a page onward. It requests each following page only
when the loop reaches it, so a `break` sends no further requests. Pages can be empty and still be
followed by more items; the iterator keeps going until the API reports the last page.

```php title="Example Request"
try {
  $files = \Files\Model\Folder::listForPage($path, ['per_page' => 1000])->autoPagingIterator();
  foreach ($files as $file) {
    // Operate on $file
  }
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Manual Pagination

A `foreach` over a page visits only the items on that page. `nextPage()` fetches the next page and
returns `null`, without a request, once `hasNextPage()` is `false`. A page never changes after it is
fetched, so if fetching the next page fails, the current page is still usable and `nextPage()` can be
called again. If the API returns a cursor that already led to that page or an earlier one,
`nextPage()` raises `\Files\Exception\ApiBadResponseException` instead of repeating pages.

Every page is requested with the parameters and options given to the page method, without an added
page number. Settings those options do not override, such as the API key set with
`\Files\Files::setApiKey()`, are read as each page is requested.

```php title="Example Request"
try {
  $page = \Files\Model\Folder::listForPage($path, ['per_page' => 1000]);
  while ($page !== null) {
    foreach ($page as $file) {
      // Operate on $file
    }
    $page = $page->nextPage();
  }
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Resuming and Restarting

`getNextCursor()` returns the opaque cursor for the next page, or `null` on the last page, where
there is nothing to continue. Pass a cursor back unchanged, as the `cursor` parameter of the same
page method, to continue from there later, for example in another process.

`restart()` fetches the first page again with the original parameters and options, without any
cursor.

```php title="Example Request"
try {
  $page = \Files\Model\Folder::listForPage($path, ['per_page' => 1000]);
  $cursor = $page->getNextCursor();

  // Later: continue with the page after $page, if there is one
  if ($cursor !== null) {
    $next = \Files\Model\Folder::listForPage($path, ['per_page' => 1000, 'cursor' => $cursor]);
  }

  // At any time: start over from the first page
  $first = $page->restart();
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

### Lists as Arrays

The list methods themselves return an array. With `\Files\Files::$autoPaginate` set to `true`, the
default, they fetch every page before returning, holding the entire list in memory. With `false`,
they return only the first page. Page methods are not affected by this setting.

A list method stops with `\Files\Exception\ApiBadResponseException` when a page is not a list, or
when the API returns a cursor the list already sent, instead of requesting pages without end.

```php title="Example Request" hasDataFormatSelector
// true by default
\Files\Files::$autoPaginate = true;

try {
  $files = \Files\Model\Folder::listFor($path, [
    'search' => "some-partial-filename"
  ]);
  foreach ($files as $file) {
    // Operate on $file
  }
} catch (\Files\Exception\NotAuthenticatedException $e) {
  echo 'Authentication Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
} catch (\Files\Exception\FilesException $e) {
  echo 'Unknown Error Occurred (' . get_class($e) . '): ', $e->getMessage(), "\n";
}
```

## Waiting for File Actions

File operations such as copy and move can finish in the background. Their default methods return a `FileAction` as soon as the API accepts the operation. Call `waitForCompletion()` on that action when subsequent work depends on it finishing.

```php
$file = new \Files\Model\File(['path' => 'reports/source.csv']);
$action = $file->copy(['destination' => 'reports/destination.csv']);
$migration = $action->waitForCompletion(['timeout' => 60]);
```

The wait checks the action's File Migration immediately, then waits one second between polls while it is `pending` or `processing`. It returns the completed `FileMigration`. An action already marked `completed` without a migration returns `null`. An action without an observable completion status raises `\Files\Exception\FileActionException`.

Set `interval` to change the delay between polls, in seconds. Set `timeout` to limit the polling loop, also in seconds. Omitting it waits without a polling deadline. The timeout is checked before and after each poll, and limits the delay between polls. An in-flight API request retains its configured HTTP timeouts and retries, so it can finish after the polling timeout.

A `progress` callback receives each `FileMigration`. Throwing from the callback stops the wait and propagates the same exception. Stopping or timing out the wait leaves the operation running on Files.com.

```php
$migration = $action->waitForCompletion([
    'interval' => 0.5,
    'timeout' => 60,
    'progress' => function ($migration) {
        echo $migration->getFilesMoved() . " files processed\n";
    },
]);
```

A failed or cancelled migration raises `\Files\Exception\FileActionException` with its status and any available failure message. The exception's `getFileAction()` and `getFileMigration()` methods return the action and the latest observed migration. API request failures propagate as their existing SDK exceptions. Each poll uses the action's request options.

## Mock Server

Files.com publishes a mock Files.com API server, which is useful for testing your use of the Files.com
SDKs and other direct integrations against the Files.com API in an integration test environment.
It never checks credentials: send any placeholder API key, such as `YOUR_API_KEY`, and never use
real Files.com credentials with it.

The server has two modes, chosen when it starts:

* **Legacy mode** (the default) checks required parameters and parameter types, then returns a fixed
  example response for each API endpoint. It does not maintain state and it does not deeply inspect
  your submissions for correctness, which makes it useful for testing basic network operations and
  JSON encoding for your SDK or API client.
* **Simulation mode** keeps records, files and folders in memory, so a test can create, list, update
  and delete resources, upload a file and download the same bytes, and make chosen requests fail,
  stall or lose their connection on purpose. Requests it does not simulate fail with a clear error
  instead of returning an example response.

### Quick Start with Docker

With [Docker](https://docs.docker.com/get-docker/) installed, start the published image from
[Docker Hub](https://hub.docker.com/r/filescom/files-mock-server) with the command shown. It runs
legacy mode at `http://127.0.0.1:4041`, reachable from your machine only: point your integration's
Files.com API base URL there and use a placeholder API key. Stop it with Ctrl+C; `--rm` removes its
container.

The published image is built for `linux/amd64` only, so the command asks for that platform;
without it, Docker on a machine of another architecture, such as an Apple silicon Mac, looks for
an image the tag does not have. The `latest` tag moves to whichever server was published last,
and `--pull=always` fetches it again each time, so it need not include simulation mode. To run
simulation mode, build the image from the source as below.

```shell title="Start the Mock Server with Docker"
docker run --platform linux/amd64 --rm --pull=always -p 127.0.0.1:4041:4041 filescom/files-mock-server:latest

## In another terminal, with a placeholder API key:
curl -H "X-FilesAPI-Key: YOUR_API_KEY" http://127.0.0.1:4041/api/rest/v1/users
```

### Simulation Mode in Docker

To run exactly the server the source's README describes, simulation mode included, build the image
from a checkout of the source on [GitHub](https://github.com/Files-com/files-mock-server) and run
it by the image ID you record with your test results. Only `linux/amd64` builds of the source have
been checked, so both commands ask for that platform.

Simulation mode listens only on loopback unless told otherwise, so the command binds it to the
container's interfaces with `-b tcp://0.0.0.0:4041` and publishes the port on your machine's
loopback address only. `FILES_MOCK_TRANSFER_ORIGIN` tells it the address your tests use, so the
upload and download URLs it returns point at the published port.

Simulation mode keeps its state only in the server process. Its control endpoints under
`/__files_mock/v1` report when the server is ready, reset it with your fixtures, add fault rules
and return the journal of the requests it received. It refuses work over its limits instead of
truncating it. The README in the source describes all of these, the operations it simulates and
how to configure its limits.

```shell title="Simulation Mode from the Source"
## In a checkout of the source:
docker build --platform linux/amd64 -t files-mock-server:local .
image=$(docker image inspect --format '{{.Id}}' files-mock-server:local)
echo "$image"   # record this ID with your test results
docker run --platform linux/amd64 --rm -e FILES_MOCK_MODE=simulation \
  -e FILES_MOCK_TRANSFER_ORIGIN=http://127.0.0.1:40410 \
  -p 127.0.0.1:40410:4041 "$image" -b tcp://0.0.0.0:4041

## In another terminal:
curl http://127.0.0.1:40410/__files_mock/v1/ready
```

### From the Source with Ruby

Start the server from its source with Ruby and Bundler. `FILES_MOCK_MODE` is read once at startup:
leaving it unset or setting it to `legacy` starts legacy mode, `simulation` starts simulation mode,
and any other value stops startup with an error. Legacy mode listens on port 4041 on all IPv4
interfaces, and simulation mode on `127.0.0.1:4041`.

```shell title="Start the Mock Server with Ruby"
bundle install

## Legacy mode
bundle exec puma

## Simulation mode
FILES_MOCK_MODE=simulation bundle exec puma
```

## Upgrading

### Errors, Models and Lists

Every 5xx response still raises `ApiServerException`, whatever its body, and transport failures raise the same
exception classes as before. These responses and calls behave differently now:

| Response or call | Earlier releases | Now |
|----------|------------------|-----|
| A 4xx response without a Files.com error object, such as `null`, `[]`, HTML or malformed JSON | `FilesException` or `ApiException` | `ApiBadResponseException` |
| A 4xx error object whose `type` is unknown, malformed, not a string, or names an SDK exception | A PHP `Error` or `TypeError`, or `FilesException` | `ApiException` |
| A 4xx response written to a stream that cannot be read | A `RuntimeException` | `ApiBadResponseException` |
| An `ApiException` getter for a field the error object does not include | A PHP notice or warning, and `null` | `null` |

`ApiBadResponseException`, `ApiServerException` and the other `Api*` exceptions now extend the new
`ApiConnectionException`, which extends `FilesException`, so code that catches them needs no change.

The message of an exception for an HTTP error response now names the request, as in
`The GET request failed with HTTP 404 Not Found (not-found).`, instead of repeating the HTTP client's
message with the start of the response body. Read the API's own error message with `getError()`.

A method that returns a model, such as `create()`, `find()` or `update()`, now raises
`ApiBadResponseException` when the response is not a JSON object, where earlier releases returned an
empty model. `save()` then leaves the object as it was, instead of replacing its attributes and
returning `true`. A list response that is not a JSON array, or an entry of a list of models that is not
a JSON object, raises it too.

`update()` now also refreshes the object it is called on with the saved attributes, and still returns
them as a separate model.

A method without parameters, such as `ApiKey::findCurrent()`, now uses its options, such as `api_key`
and `workspace_id`, for its request, where earlier releases sent them as request parameters.

An auto-paginated list method now reads the next page's cursor from `X-Files-Cursor-Next` whenever the
response has that header, and from `X-Files-Cursor` otherwise, and follows a cursor of `0`. A list that
returns a cursor it already sent raises `ApiBadResponseException` instead of requesting pages without end.
List methods that page with a cursor also have page counterparts, such as `Folder::listForPage()`; see
Pagination.

### Credentials and Parameters

An `api_key` option, given to a model object or a method, now takes the place of a session set with
`Files::setSessionId()` as well as the API key set with `Files::setApiKey()`; earlier releases sent that
session instead. A `session_id` option is still used before an `api_key` option, and without either the
configured session is still used before the configured API key. An option that PHP reads as false, such
as `null`, `''` or `'0'`, is ignored, as before.

A request given a `session_id` or `api_key` option no longer also sends an `X-FilesAPI-Auth` or
`X-FilesAPI-Key` header of its `headers` option, in any letter case. Without such an option, the `headers`
option is sent as before. Creating a session still sends no API key of the SDK's own; given an `api_key`
option, it no longer sends the configured session either.

A `TransferJob`'s `api_key`, `session_id` and `workspace_id` options still replace the job's copy of those
settings whenever they are given, `null` and `false` included. A job's `api_key` that PHP reads as true now
also takes the place of the session the job copied from `Files::setSessionId()`, unless the job is given
such a `session_id` as well. A transfer's own options choose as a request's options do.

A model method now raises `InvalidParameterException`, before it sends anything, for an integer, string
or array parameter given `false`, or another empty value of the wrong type such as `''` for an integer,
where earlier releases sent it. A required parameter other than a boolean that is missing or empty still
raises `MissingParameterException`. A required boolean parameter now accepts `false`, including from the
model object's own attribute, and a boolean parameter raises `InvalidParameterException` for an array or
object.

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

## Upload Completion

The upload helpers (`File::uploadFile`, `File::uploadData`, the `uploadTo...` helpers, adaptive transfers and
`files://` write streams) return only once Files.com has completed the upload. For a remote server mount or a
Files.com Agent, Files.com can finish an upload in the background after its last part. The helper then asks again each
second until the upload is complete or has failed. A `progress` callback gets one more report for each answer: the last
part's report with `part_bytes` 0, `finalizing` set to true and `finalization_progress`, Files.com's percentage or null
when it gave none. Byte counts do not change while it waits, and a report at 100 does not mean the upload is complete.

Stopping the wait (a callback that throws, or cancelling a transfer job) does not stop Files.com, which may still
complete the upload. Neither does a wait that ends because Files.com's answer could not be used or the requests for it
kept failing; the SDK's errors for those cases say so. An error that Files.com answers with is the upload's outcome.

## Adaptive Transfers (64-bit PHP)

The upload and download helpers send one request at a time unless they are given the `transfer` option. With it, they
run adaptively: upload parts and download ranges go concurrently, as many as measured throughput and response times
justify, within the limits you set.

Adaptive transfers need 64-bit PHP integers (`PHP_INT_SIZE` 8) and a native monotonic clock: PHP 7.3 or later (its
`hrtime`), or, on older 64-bit PHP, the hrtime extension (PECL hrtime 0.6.0 or later, `HRTime\PerformanceCounter`).
Anywhere else, asking for them throws a `ConfigurationException` before anything is read, sent or written; the helpers
without the `transfer` option work as before. `\Files\Transfer\TransferJob::isSupported()` tells you which a PHP is.
Linux and macOS clocks stop while the computer sleeps; Windows' keeps counting.

```php
// One upload or download, adaptively:
\Files\Model\File::uploadFile('reports/a.csv', '/tmp/a.csv', [], ['transfer' => ['startup' => 'default']]);
\Files\Model\File::uploadData('reports/b.csv', $stream, [], ['transfer' => ['size' => $bytes]]);
$result = \Files\Model\File::downloadToFile('reports/a.csv', '/tmp/copy.csv', true, ['transfer' => []]);

// Several transfers sharing one set of limits and what they learn:
$job = new \Files\Transfer\TransferJob(['max_concurrent_requests' => 32]);
$job->uploadFile('reports/c.csv', '/tmp/c.csv', [], ['progress' => function ($report) {
    echo $report['acknowledged_bytes'] . ' bytes at ' . round($report['bytes_per_second']) . " bytes/s\n";
}]);
$job->downloadToStream('reports/d.csv', $writableStream);
$job->wait();
```

Options of a `TransferJob` (and of the `transfer` option):

* `startup`: `default` starts an upload to Amazon S3 with one part in flight and adds more only as measurements
  justify; `aggressive` starts at the policy's start, for a link known to be fast. Both remain adaptive and share the
  same eventual limits. Uploads only: downloads start at the policy's start.
* `concurrency`: a fixed number of content requests in flight. `initial_target`: an explicit starting target.
* `max_concurrent_requests` (default 64, at most 1024): every request in flight counts, API requests included.
* `max_concurrent_files` (default 128) and `max_buffer_bytes` (default 64 MiB, or half the memory PHP reports free
  under its `memory_limit` when that is less): a hard limit on the bytes held for parts and ranges.
* `max_retries` (default `Files::$maxNetworkRetries`), `download_part_bytes` (default 8 MiB), `diagnostics` (a
  callable given each concurrency decision), and `api_key`, `session_id` and `workspace_id`.

A job copies the SDK's configuration when it is made; later changes do not reach it. Its requests are pooled in its own
curl multi handle, or in the `GuzzleHttp\Handler\CurlMultiHandler` you set with `Files::setHandler`. Any other handler
set there is used one request at a time (`statistics()['transport']` is then `serial`), and a job given `concurrency`,
`initial_target` or Aggressive startup with such a handler is refused.

Uploads: every part is acknowledged by exactly one ETag, retried with its exact bytes when it fails (never when its grant
says `retry_parts` is false), and never sent again once acknowledged; the upload ends only after every part and every
progress report, and completes only once Files.com has completed it (see Upload Completion). Where the API's first answer shows an ordinary multipart upload straight to Amazon S3, the SDK sizes
the parts itself (8 MiB, larger when a file needs more than 10,000 parts); everywhere else the API sizes them. A stream
without a known size is sent one part at a time.

Downloads: a download is bound to the file's version (a strong ETag, sent back as `If-Match`). A range that comes back
with other bytes than its own (fewer, more, or another range) is never accepted: it is fetched again, within
`max_retries`, and the download fails once those are spent. A changed version (another or a weak ETag, another total,
or a 412), a response from the web app, and a failure that a retry cannot fix fail the download at once, so versions
never mix. Storage that does not serve
version-bound ranges is downloaded whole, restarted from its first byte when interrupted. A response from the Files.com
web app (`X-Files-Frontend-App`) fails the download before anything is written. A file is written to a staging file
beside its destination, which replaces the destination only once every byte is verified; a stream is written in order,
never twice.

Expiry: a signed URL due to expire within 15 seconds is renewed before it is sent, and one that storage refuses with
a 403 once it has expired, or with a 400 or 403 that says it has expired whatever expiry it states, is renewed and
sent again as one of its retries, at most three times for each part or range (an upload's part only when its grant
allows retries). Storage says so with S3's `Request has expired`, `<Code>ExpiredToken</Code>` (Amazon and Google) or
Azure's `Signature not valid in the specified time frame`, found without regard to case in the first 4 KiB of its
body, or with a Files.com JSON error of at most 4 KiB whose `type` is `download_request_expired` or
`upload_request_expired`; a body that begins as JSON (`{`, `[` or `"`) is judged by that `type` alone, never by text
inside it. Any other 403 fails the transfer.

`wait()` throws the first failure, or the first exception a callback throws, as it was, after stopping everything in
flight; `cancel()` makes it throw a `TransferCancelledException`. `pause()`, from a callback or between waits, stops the
job keeping everything accepted, and `resume()` then `wait()` continue it without sending accepted bytes again. A paused
job lives in its process: keep the job, its files and streams open and unchanged until it resumes. Nothing is saved for
another process or after a crash.

These defaults are starting choices of this SDK, not measured performance.

## Expired transfer URLs without adaptive transfers

The upload and download helpers without the `transfer` option (`uploadFile`, `uploadData` and the `uploadTo...`
helpers, `downloadToStream`, `partialDownloadToFile`, `resumeDownloadToFile` and `downloadToFile`) and `files://` handles
treat storage's word that a signed URL has expired, as described above, the same way, on every PHP and Guzzle they
support and with any handler set through `Files::setHandler`. The URL is renewed by the file's path, with the request
context the call (or the handle) began with, and the same part is sent again with its same bytes, by the upload's `ref`
and the part's number, or the same range is asked for again: at most three renewals for each part, download or range,
each one of the `Files::$maxNetworkRetries` retries that its connection retries, and `downloadToFile`'s retries, also
take. Nothing is renewed when no retry is left, for `downloadToFile` with `$enableRetry` false, or for a part whose grant
says `retry_parts` is false; a renewed grant that changes the part (its `ref`, number, size, method, headers, parameters
or send instructions) or says `retry_parts` is false fails the upload before the part is sent again, and a renewed URL
that has already expired by its own query is not sent. A `files://` read must find the file of the size it opened, and
of its version once it has one. Nothing of a refusal that says the URL expired reaches a download's destination, renewed
or not; another refusal's body reaches it as before. Only the first 4 KiB and a byte more of a refusal are held in
memory, and bytes a handler writes before giving the response's status (to `on_headers`) wait in a temporary stream that
spills to disk past 1 MiB. A Files.com JSON refusal counts only when all of it arrived, every byte its `Content-Length`
gave; storage's text counts in whatever part of it arrived. Once `downloadToFile` has spent its three renewals, it does
not retry that refusal as it retries other failures, and it resumes only after bytes it wrote itself: until a response's
body has reached the destination, a retry starts again from the first byte, whatever the file held before. A stream resource given as a download's destination stays open,
yours to close, whatever handler sends the download. A URL alone, as `Api::sendFile` or `getDownloadUrl` gives it, has no
path to renew it by.

## Native file and directory handles

Use PHP file and SPL interfaces through the `files://` stream wrapper. See [native files](native-files.md) and [native directories](native-directories.md) for registration, supported operations and limitations.

## API region routing

See [API region routing](docs/RegionRouting.md) for automatic region continuation and its policy override.

## Native PHP API reference

The generated classes include PHPDoc for model properties, accessors and API
operations, including request parameters and return types. IDEs can read these
comments directly. The generated Markdown reference remains in `docs/Model/`.

To build a browsable phpDocumentor reference, install the separate documentation
tools using PHP 8.1.2 or later, then run the documentation build from this SDK's
root:

```sh
composer --working-dir=tools/phpdoc install
composer docs
```

Open `docs/api/index.html` to browse the result. The documentation tool's
requirements do not change the PHP versions or Guzzle versions supported by the
SDK. The builder analyzes source files without loading the SDK or making API
requests.

`./build.sh --docs` runs the same build without Composer's script wrapper. To use
an existing phpDocumentor 3 executable or PHAR, set `PHPDOC_BIN` to its path. Set
`PHPDOC_PHP` to the PHP executable used by the builder when it differs from
`php`. A plain `./build.sh` checks PHP source syntax and reports how to build the
reference. To generate and build the reference in one SDK Generator command,
set `PHPDOC_BIN` to an absolute tool path outside `generated/php` and run
`bundle exec ruby files-sdk-generator.rb --target php --build-args=--docs`.
Regeneration replaces the generated SDK, including locally installed tools.
