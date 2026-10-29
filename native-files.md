# Native PHP files

Register the SDK's optional stream wrapper to use Files.com paths with PHP file
functions and SPL. Registration does not replace PHP's local `file://` wrapper.
PHP's URL-wrapper settings apply.

```php
use Files\Files;
use Files\StreamWrapper;

Files::setApiKey('your-api-key');
Files::setBaseUrl('https://your-subdomain.files.com');
StreamWrapper::register();

$stream = fopen('files://reports/daily.csv', 'rb');
try {
    $line = fgets($stream);
    fseek($stream, 0);
    $size = fstat($stream)['size'];
} finally {
    fclose($stream);
}

$file = new SplFileObject('files:///reports/daily.csv', 'rb');
echo $file->fgets();
```

Both path forms above refer to `reports/daily.csv` on the configured site.
Everything after `files://` is a literal Files.com path, with leading slashes
ignored. Spaces, `?`, `#`, `%`, and `@` are filename characters; do not URL-encode
them. A URL host, query, or fragment never changes the site, authentication, or
request options. Keep credentials out of paths.

`fopen`, `fread`, `fgets`, `stream_get_contents`, `stream_copy_to_stream`,
`file_get_contents`, and `SplFileObject` can read a file. Read streams support
`ftell`, `fseek`, `rewind`, `feof`, and `fstat`. Each transport read requests at
most 1 MiB and validates the returned range and byte count. A backend that ignores
Range requests fails with an SDK exception before returning file bytes; the
wrapper does not preload the entire file. A strong storage ETag, when provided,
is sent in `If-Match` on later reads. Backends without that validator do not
provide a snapshot guarantee during concurrent changes. Download URLs are not
renewed after expiration; reopen the stream to obtain a new grant.

Use `w` or `wb` for a sequential overwrite:

```php
$context = stream_context_create(['files' => [
    'options' => ['api_key' => 'request-api-key', 'workspace_id' => 123],
    'upload_params' => ['custom_metadata' => ['source' => 'export']],
]]);
$stream = fopen('files://reports/export.csv', 'wb', false, $context);
try {
    fwrite($stream, "name,total\n");
    fwrite($stream, "example,42\n");
} finally {
    fclose($stream);
}
```

Opening a write stream obtains upload permission and begins an upload. It does
not immediately truncate or publish the destination. Complete multipart parts
are sent as they fill. Only one granted part is retained locally, using at most
1 MiB of in-memory temporary storage before spilling to an owned temporary file.
The API grant controls the part's maximum size. `fflush` flushes that temporary
storage; it does not complete the remote upload. Further writes remain supported.
`fclose` sends the final part, including an empty final part when needed, and
completes the upload. Close is required to publish the result and observe a
completion error. Closing an empty write stream creates an empty file. A failed
write sends no upload completion request; remote staging and cleanup follow the
backend's upload semantics. The SDK removes its owned local temporary storage.

The `files.options` context passes the SDK's ordinary per-call options to API
requests without changing global configuration. API keys, session IDs, workspace
IDs, and caller headers are not forwarded to storage transfer URLs. The supported
`files.upload_params` are `provided_mtime`, `custom_metadata`, and
`buffered_upload`. Native writes require the parent folder to exist.
An open handle captures its effective API/session credentials and workspace, so
later global API-key or workspace changes do not repoint it. Changing the global
site or endpoint, or adding a global session that would override a captured API
key, raises `ConfigurationException` before the next request. Close and reopen
the handle to use that configuration. An explicit captured session remains
usable when unrelated global credentials change.
The region-continuation policy is also captured when that SDK capability is available.

`stat`, `filesize`, `is_file`, `is_dir`, `file_exists`, and `SplFileInfo` use API
metadata. Permission bits represent the current API user's read/write/list
capabilities; Unix ownership and access time are unavailable and represented by
zero.

Append, update, exclusive-create, text-translation modes, seeking or truncating a
write stream, locks, `touch`, `chmod`, ownership changes, `unlink`, `rmdir`, and native `rename` raise
`Files\Exception\NotImplementedException`. Use the existing File model APIs for
remote metadata changes and asynchronous moves. The wrapper exposes no local
descriptor for `stream_select` or native descriptor consumers. Transports remain
blocking and use the SDK's configured timeouts and retry policy.
The default `maxNetworkRetries` of 3 allows 4 total attempts for a retryable
connection failure; a grant with `retry_parts=false` permits only one attempt.
PHP rejects some unsupported arguments before invoking a wrapper:
`file_put_contents` with `LOCK_EX` warns and returns false before opening a file.

When the application no longer needs the scheme, call
`StreamWrapper::unregister()`. Close existing handles first.
