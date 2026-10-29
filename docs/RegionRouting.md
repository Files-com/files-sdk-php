# API region routing

Ordinary API calls automatically continue when the current API origin answers with a recognized lockout region mismatch. The SDK accepts the typed HTTP 403 error and the legacy HTTP 403 response beginning `You have connected to a URL`. The destination comes from `data.host`, or one `X-Files-Host` header when the typed response has an absent, null or exactly empty host. A present non-object `data` value or another invalid host type does not authorize fallback. Destinations must be HTTPS origins without credentials, a path, a query or a fragment.

A continuation preserves the request method, escaped path, query, body, authentication, workspace and Guzzle options. Bodies must already support seeking to their original position; forward-only bodies are not replayed. An API request with a caller-owned response sink (a resource or PSR stream) also leaves the original response or error for the caller: continuing could append another response or close the caller's handle. String path sinks retain Guzzle's fresh-file behavior for each attempt. The destination is local to that request. `Files::getBaseUrl()` stays unchanged, so later independent calls start at the configured API URL.

Connection retries and region continuations share `Files::$maxNetworkRetries`: the default is three additional retry attempts, for at most four attempts. Guzzle's configured HTTP redirects retain their separate redirect budget and do not consume this connection/region retry count. Zero disables both. Existing PHP retry delays apply. This does not add retries for other HTTP errors or failures after a request may have been written.

Disable automatic continuation globally or for one call to handle the original mismatch yourself:

```php
Files::setRegionRelocation(false);
$keys = ApiKey::all([], ['region_relocation' => false]);
```

A callback can observe the proposed destination and decide whether to continue. Return exactly `true` to approve it. Returning another value leaves the original response or error for the caller. The per-call option overrides the global setting.

```php
Files::setRegionRelocation(function ($targetOrigin, $currentOrigin) {
    return $targetOrigin === 'https://approved.example.com';
});
```

An unrelated HTTP 403, a response after leaving the API origin, an absolute external URL and a `sendFile` request never authorize region continuation. HTTP redirects retain their existing origin-based Files authentication stripping. Custom handlers and TLS verification options remain in use.
