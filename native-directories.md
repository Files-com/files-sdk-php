# Native directory functions

Register the `files://` stream wrapper to use PHP directory functions against the Files.com site in SDK configuration:

```php
use Files\Files;
use Files\StreamWrapper;

Files::setBaseUrl('https://my-site.files.com');
Files::setApiKey('my-api-key');
StreamWrapper::register();

$directory = opendir('files://reports');
try {
    while (($name = readdir($directory)) !== false) {
        if ($name !== '.' && $name !== '..') {
            echo $name . PHP_EOL;
        }
    }
    rewinddir($directory);
} finally {
    closedir($directory);
}

$names = scandir('files://reports');
foreach (new DirectoryIterator('files://reports') as $entry) {
    if (!$entry->isDot()) {
        echo $entry->getFilename() . PHP_EOL;
    }
}
```

Both `files://reports` and `files:///reports` select the same site-relative directory; `files:///` selects the root. URLs do not choose a site or carry credentials. Use SDK configuration, or the `files.options` stream context for per-call API options:

```php
$context = stream_context_create([
    'files' => ['options' => ['api_key' => 'another-api-key', 'workspace_id' => 123]],
]);
$names = scandir('files://reports', SCANDIR_SORT_ASCENDING, $context);
```

`opendir` fetches and validates the first API page before returning a resource. `readdir` returns `.` and `..`, then actual child names in API order, and returns `false` only after the listing ends. Compare it with `!== false`, because a file can be named `0`. Empty API pages with a continuation cursor are followed. Missing directories, permission failures, failed later pages, malformed responses and cursor cycles raise SDK exceptions; they do not appear as empty folders or normal EOF. A failed later page can be retried by calling `readdir` again.

Handles retain the effective API key or session and workspace options selected when opened. Later changes to unrelated global defaults do not change that account. A changed global site URL or API endpoint, or a newly set global session that would override an API key, raises `ConfigurationException` before another page request; close and reopen the handle to use the new configuration.

Handles fetch one page at a time, asking for 1,000 entries per page, independently of `Files::$autoPaginate`. This is a page size, not a total listing limit. `rewinddir` fetches the first page again, so changes since the previous read can become visible. `closedir` releases retained entries and options without fetching more pages. Close each handle in a `finally` block when iteration can stop early. `scandir` follows all pages and lets PHP perform the requested sort; SPL iterators use the same directory hooks.

`mkdir('files://reports/new')` creates a folder. Pass `0777, true` to create parents. Files.com API permissions control access; Unix permission modes other than the default `0777` raise `NotImplementedException`. `rmdir` raises `NotImplementedException` before any request. The API deletes either files or folders, with no atomic directory-only condition; a preceding metadata check cannot protect against concurrent replacement by a file. Use the explicit SDK `File` deletion API when its remote deletion semantics are appropriate.

PHP's working directory and `chdir` remain local. Use complete `files://` paths for remote operations. Native OS directory handles, local descriptor operations, and Unix ownership or permission changes have no Files.com equivalent. When finished using the wrapper, call `StreamWrapper::unregister()`; registration is explicit and never replaces another wrapper registered under `files`.
