<?php

namespace Files;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\Uri;

function middlewareRemoveHeader($header)
{
    return function ($handler) use ($header) {
        return function ($request, $options) use ($handler, $header) {
            $request = $request->withoutHeader($header);
            return $handler($request, $options);
        };
    };
}

/**
 * HTTP transport used by the generated model operations.
 */
class Api
{
    const VERSION = "2.0.748";

    const REPEATED_CURSOR_MESSAGE = 'The list returned a cursor it already returned, so following it would repeat pages.';

    private static function urlOrigin($url)
    {
        if (!is_string($url)) {
            return null;
        }
        $parts = parse_url($url);
        if (!$parts || !isset($parts['scheme']) || !isset($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        $port = isset($parts['port']) ? $parts['port'] : ($scheme === 'https' ? 443 : 80);
        return $scheme . '://' . strtolower($parts['host']) . ':' . $port;
    }

    private static function redirectAuthMiddleware($baseUrl, $operation = null, $observeRequest = true)
    {
        $baseOrigin = self::urlOrigin($baseUrl);
        return function ($handler) use ($baseOrigin, $operation, $observeRequest) {
            $authHeadersStripped = false;
            return function ($request, $options) use ($handler, $baseOrigin, $operation, $observeRequest, &$authHeadersStripped) {
                $origin = $operation ? $operation->origin : $baseOrigin;
                if ($authHeadersStripped || ($operation && $operation->crossedOrigin) || self::urlOrigin((string) $request->getUri()) !== $origin) {
                    $authHeadersStripped = true;
                    if ($operation) {
                        $operation->crossedOrigin = true;
                    }
                    foreach (['X-FilesAPI-Key', 'X-FilesAPI-Auth', 'X-Files-Workspace-Id'] as $header) {
                        $request = $request->withoutHeader($header);
                    }
                }
                if ($operation) {
                    if (!$operation->crossedOrigin && $operation->relocationAuth !== null) {
                        foreach ($operation->relocationAuth as $name => $values) {
                            $request = $values ? $request->withHeader($name, $values) : $request->withoutHeader($name);
                        }
                    }
                }
                if ($operation && $observeRequest) {
                    $body = $request->getBody();
                    if ($operation->body !== $body || $operation->bodyMethod !== $request->getMethod()) {
                        $operation->body = $body;
                        $operation->bodyMethod = $request->getMethod();
                        $operation->bodyPosition = $body->isSeekable() ? $body->tell() : null;
                    } else {
                        self::restoreApiBody($operation, $request);
                    }
                    $operation->request = $request;
                    return $handler($request, $options)->then(function ($response) {
                        // Enter before Guzzle constructs an error, so classification and the final error see the same bytes.
                        return $response->getStatusCode() !== 403 || $response->getBody()->isSeekable()
                            ? $response
                            : $response->withBody(new CachingStream($response->getBody()));
                    });
                }
                return $handler($request, $options);
            };
        };
    }

    private static function regionDestination($host)
    {
        if (!is_string($host) || $host === '' || preg_match('/[\\\\\s]/', $host)) {
            return null;
        }
        $parts = parse_url(strpos($host, '://') === false ? 'https://' . $host : $host);
        if (
            !$parts || !isset($parts['scheme'], $parts['host']) || strtolower($parts['scheme']) !== 'https'
            || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])
            || (isset($parts['path']) && $parts['path'] !== '' && $parts['path'] !== '/')
        ) {
            return null;
        }
        try {
            return new Uri('https://' . strtolower($parts['host']) . (isset($parts['port']) ? ':' . $parts['port'] : ''));
        } catch (\InvalidArgumentException $error) {
            return null;
        }
    }

    private static function regionHost($response)
    {
        if ($response->getStatusCode() !== 403 || !$response->getBody()->isSeekable()) {
            return null;
        }
        $body = $response->getBody();
        $position = $body->tell();
        try {
            $data = (string) $body;
        } finally {
            $body->seek($position);
        }
        $error = json_decode($data);
        if (is_array($error)) {
            $error = isset($error[0]) ? $error[0] : null;
        }
        if (is_object($error) && isset($error->type) && $error->type === 'not-authenticated/lockout-region-mismatch') {
            $details = property_exists($error, 'data') ? $error->data : null;
            if ($details !== null && !is_object($details)) {
                return null;
            }
            if (is_object($details) && property_exists($details, 'host')) {
                $host = $details->host;
                if ($host !== null && $host !== '') {
                    return $host;
                }
            }
        } elseif (strpos($data, 'You have connected to a URL') !== 0) {
            return null;
        }
        $hosts = $response->getHeader('X-Files-Host');
        return count($hosts) === 1 ? $hosts[0] : null;
    }

    private static function retryApiRequest($operation, $request, $options, $handler)
    {
        ++$operation->retries;
        Logger::info('Retrying request (retry #' . $operation->retries . ')');
        $options['delay'] = min(
            ($operation->retries + 1) * $operation->minDelay * 1000,
            $operation->maxDelay * 1000
        );
        return self::sendApiAttempt($handler, $operation, $request, $options);
    }

    private static function restoreApiBody($operation, $request)
    {
        if ($operation->body === $request->getBody() && $operation->bodyPosition !== null) {
            try {
                $operation->body->seek($operation->bodyPosition);
            } catch (\Exception $error) {
                throw new Exception\FilesException($error->getMessage(), (int) $error->getCode(), $error);
            }
        }
    }

    private static function continueRegion($operation, $response, $options, $handler)
    {
        if ($operation->policy === false || $operation->crossedOrigin || $operation->retries >= $operation->maxRetries) {
            return $response;
        }
        // A response may already have written to this caller-owned sink. Do not close it or append a second answer. The
        // SDK's own file for a destination path is not the caller's: every transfer, a relocated one too, starts it anew.
        $sink = isset($options['sink']) ? $options['sink'] : null;
        if (!($sink instanceof Util\DownloadFileSink) && (is_resource($sink) || $sink instanceof \Psr\Http\Message\StreamInterface)) {
            return $response;
        }
        $target = self::regionDestination(self::regionHost($response));
        if ($target === null || self::urlOrigin((string) $target) === $operation->origin) {
            return $response;
        }
        $request = $operation->request;
        $body = $request->getBody();
        if ($operation->bodyPosition === null && $body->getSize() !== 0) {
            return $response;
        }
        if (is_callable($operation->policy) && call_user_func($operation->policy, (string) $target, (string) $request->getUri()->withUserInfo('')->withPath('')->withQuery('')->withFragment('')) !== true) {
            return $response;
        }
        $uri = $request->getUri()->withScheme('https')->withHost($target->getHost())->withPort($target->getPort());
        $request = $request->withUri($uri);
        self::restoreApiBody($operation, $request);
        $operation->relocationAuth = [];
        foreach (['X-FilesAPI-Key', 'X-FilesAPI-Auth', 'X-Files-Workspace-Id'] as $name) {
            $operation->relocationAuth[$name] = $request->getHeader($name);
        }
        $operation->origin = self::urlOrigin((string) $target);
        $response->getBody()->close();
        return self::retryApiRequest($operation, $request, $options, $handler);
    }

    private static function sendApiAttempt($handler, $operation, $request, $options)
    {
        return $handler($request, $options)->then(
            function ($response) use ($handler, $operation, $options) {
                return self::continueRegion($operation, $response, $options, $handler);
            },
            function ($error) use ($handler, $operation, $request, $options) {
                if ($error instanceof \GuzzleHttp\Exception\ConnectException && $operation->retries < $operation->maxRetries) {
                    $request = $operation->request ?: $request;
                    self::restoreApiBody($operation, $request);
                    return self::retryApiRequest($operation, $request, $options, $handler);
                }
                // A caller-supplied HandlerStack may already have converted the response to an error.
                if ($error instanceof \GuzzleHttp\Exception\RequestException && $error->hasResponse()) {
                    $response = $error->getResponse();
                    $continued = self::continueRegion($operation, $response, $options, $handler);
                    if ($continued !== $response) {
                        return $continued;
                    }
                }
                return new RejectedPromise($error);
            }
        );
    }

    private static function pushApiRetryHandler($handlerStack, $operation)
    {
        $handlerStack->before('allow_redirects', function ($handler) use ($operation) {
            return function ($request, $options) use ($handler, $operation) {
                return self::sendApiAttempt($handler, $operation, $request, $options);
            };
        }, 'files_api_retry');
    }

    private static function pushRetryHandler($handlerStack)
    {
        $shouldRetry = function ($retries, $request, $response, $exception) {
            if ($retries >= Files::$maxNetworkRetries) {
                Logger::info('Retries exhausted - giving up on this request');
                return false;
            }

            if (is_a($exception, 'GuzzleHttp\\Exception\\ConnectException')) {
                Logger::info('Retrying request (retry #' . ($retries + 1) . ')');
                return true;
            }

            return false;
        };

        $getRetryDelay = function ($retries) {
            return min(
                ($retries + 1) * Files::$minNetworkRetryDelay * 1000,
                Files::$maxNetworkRetryDelay * 1000
            );
        };

        $handlerStack->push(Middleware::retry($shouldRetry, $getRetryDelay));
    }

    // A new handler stack for one request, and its redirects, through $handler: Files.com credentials stripped once a
    // redirect leaves $baseUrl's origin, a retry of a request that cannot connect when $retryConnectErrors, and no
    // Content-Length on a GET. Each request gets its own stack, so the stripping one redirect chain starts never reaches
    // another request, even when they share $handler, as a transfer job's requests share its curl multi handle.
    public static function requestStack($handler, $baseUrl, $verb, $retryConnectErrors = false)
    {
        $handlerStack = HandlerStack::create($handler);
        $handlerStack->after(
            'allow_redirects',
            self::redirectAuthMiddleware($baseUrl),
            'files_auth_redirects'
        );
        if ($retryConnectErrors) {
            self::pushRetryHandler($handlerStack);
        }

        // for security, Content-Length is disallowed on GET requests
        if (strtoupper($verb) === 'GET') {
            $handlerStack->push(middlewareRemoveHeader('Content-Length'));
        }

        return $handlerStack;
    }

    // $headers with the credential of an API request to $path: a session_id that $options gives, else an api_key that
    // it gives (in place of the ambient session too), else the ambient session, else the ambient API key. Options that
    // PHP reads as false (null, '', 0, '0', false) leave the ambient ones in place. Given a credential option,
    // $headers lose any X-FilesAPI-Auth or X-FilesAPI-Key, in any case, so that the chosen credential is the only one;
    // without one, they are kept as they are. Creating a session sends no API key of the SDK's own, configured or
    // given as an option.
    private static function withCredential($headers, $options, $ambientSessionId, $ambientApiKey, $path, $verb)
    {
        $sessionId = !empty($options['session_id']) ? $options['session_id'] : null;
        $apiKey = !empty($options['api_key']) ? $options['api_key'] : null;
        if ($sessionId || $apiKey) {
            foreach (array_keys($headers) as $name) {
                if (in_array(strtolower((string) $name), ['x-filesapi-auth', 'x-filesapi-key'], true)) {
                    unset($headers[$name]);
                }
            }
        } else {
            $sessionId = $ambientSessionId;
        }

        if ($sessionId) {
            $headers['X-FilesAPI-Auth'] = $sessionId;
        } elseif ($path !== '/sessions' || strtoupper($verb) !== 'POST') {
            $apiKey = $apiKey ?: $ambientApiKey;
            if (!$apiKey) {
                throw new Exception\ConfigurationException('No Files.com API key provided. Set your API key using Files::setApiKey(<API-KEY>). You can generate API keys from the Files.com web interface.');
            }

            $headers['X-FilesAPI-Key'] = $apiKey;
        }

        return $headers;
    }

    // The request path and Guzzle options of an API request for $path (under the endpoint prefix, or an external URL),
    // as sendRequest sends it: the headers, with the language and workspace of $config (Files::snapshot(), or a
    // transfer job's copy of it) unless $options gives its own and the credential withCredential chooses, and $params
    // as the query of a GET or the JSON body of anything else. Throws a ConfigurationException for an API request
    // without a credential.
    public static function prepareApiRequest($config, $path, $verb, $params = null, $options = [])
    {
        $options = $options ?: [];
        $headers = array_merge(
            $config['language']
              ? ['Accept-Language' => $config['language']]
              : [],
            @$options['headers'] ?: [],
            ['User-Agent' => self::userAgentWithMetadata("Files.com PHP SDK v" . self::VERSION)]
        );

        $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

        if (!$isExternal) {
            $headers['Accept'] = 'application/json';
            $headers = self::withCredential(
                $headers,
                $options,
                $config['session_id'],
                $config['api_key'],
                $path,
                $verb
            );

            $workspaceId = array_key_exists('workspace_id', $options)
              ? $options['workspace_id']
              : $config['workspace_id'];

            if ($workspaceId !== null && $workspaceId !== '') {
                $headers['X-Files-Workspace-Id'] = (string) $workspaceId;
            }
        }

        $requestPath = $path;

        if ($params) {
            $headers['Content-Type'] = 'application/json';

            if (strtoupper($verb) === 'GET') {
                $requestPath .= (parse_url($path, PHP_URL_QUERY) ? '&' : '?') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            } else {
                $options['body'] = json_encode($params);
            }
        }

        $options['headers'] = $headers;

        return [$requestPath, $options];
    }

    private static function sendVerbatim($path, $verb, $options, $retryConnectErrors = true, $apiRequest = true)
    {
        if (!is_string($path) || !is_string($verb)) {
            throw new Exception\InvalidParameterException('Request paths and HTTP methods must be strings.');
        }
        $isExternal = preg_match('@^[a-zA-Z]+://@', $path);
        $baseUrl = Files::getBaseUrl();

        if (!$isExternal && (!is_string($baseUrl) || !$baseUrl)) {
            throw new Exception\ConfigurationException('Site URL has not been set. Set your site URL using Files::setBaseUrl(<BASE-URL>).');
        }

        $url = $isExternal
          ? $path
          : $baseUrl . Files::getEndpointPrefix() . $path;

        Logger::debug("Sending request: " . $verb . " $url");

        $operation = $isExternal || !$apiRequest ? null : (object) [
            'origin' => self::urlOrigin($baseUrl),
            'crossedOrigin' => false,
            'request' => null,
            'body' => null,
            'bodyMethod' => null,
            'bodyPosition' => null,
            'relocationAuth' => null,
            'retries' => 0,
            'maxRetries' => Files::$maxNetworkRetries,
            'minDelay' => Files::$minNetworkRetryDelay,
            'maxDelay' => Files::$maxNetworkRetryDelay,
            'policy' => array_key_exists('region_relocation', $options) ? $options['region_relocation'] : Files::getRegionRelocation(),
        ];
        if ($operation && !is_bool($operation->policy) && !is_callable($operation->policy)) {
            throw new Exception\ConfigurationException('Region relocation must be a boolean or callable.');
        }
        $handler = Files::getHandler();
        // A destination path given as the sink is opened by the SDK rather than by Guzzle (Util\DownloadFileSink), so
        // that the file is closed before this returns or throws, even while the caller keeps the exception. $options
        // keeps the caller's sink, which still decides whether the result includes the response data. A request that
        // names its own Guzzle handler option is sent by that handler, so its path stays Guzzle's, opened anew for each
        // of that handler's transfers.
        $requestOptions = $options;
        $ownedSink = null;
        if (
            isset($options['sink']) && !array_key_exists('handler', $options)
            && Util\DownloadFileSink::canOwn($options['sink'], $handler)
        ) {
            $ownedSink = new Util\DownloadFileSink($options['sink']);
            $requestOptions['sink'] = $ownedSink;
            $handler = Util\DownloadFileSink::newFilePerTransfer($handler);
        }
        if ($operation && $handler instanceof HandlerStack) {
            $handler = clone $handler;
            // Observe and scope the request after the caller's middleware, immediately before its transport.
            $handler->push(self::redirectAuthMiddleware($baseUrl, $operation), 'files_api_origin');
        }
        $handlerStack = HandlerStack::create($handler);
        $handlerStack->after(
            'allow_redirects',
            self::redirectAuthMiddleware($baseUrl, $operation, !($handler instanceof HandlerStack)),
            'files_auth_redirects'
        );
        if ($operation) {
            self::pushApiRetryHandler($handlerStack, $operation);
        } elseif ($retryConnectErrors) {
            self::pushRetryHandler($handlerStack);
        }

        // for security, Content-Length is disallowed on GET requests
        if (strtoupper($verb) === 'GET') {
            $handlerStack->push(middlewareRemoveHeader('Content-Length'));
        }

        $client = new Client([
            'connect_timeout' => Files::$connectTimeout,
            'handler' => $handlerStack,
            'read_timeout' => Files::$readTimeout,
        ]);

        // An owned file is released once the request has ended, after any error body or response data has been read from
        // it. A failure to release it is thrown only when the request succeeded: it never replaces the request's own
        // failure, nor passes for success.
        $completed = false;
        try {
            try {
                $response = $client->request($verb, $url, $requestOptions);
            } catch (\Exception $error) {
                handleErrorResponse($error);
            }

            $statusCode = $response->getStatusCode();
            $statusReason = $response->getReasonPhrase();

            Logger::debug("Status: $statusCode $statusReason");

            if (Files::$debugResponseHeaders) {
                Logger::debug('Response Headers: ');
                Logger::debug($response->getHeaders());
            }

            $result = (object) [
                'status' => $statusCode,
                'reason' => $statusReason,
                'headers' => $response->getHeaders(),
            ];

            // if the response wasn't redirected into a stream or file, add it to the result
            if (empty($options['sink'])) {
                $result->data = json_decode((string) $response->getBody());
            }
            $completed = true;
        } finally {
            if ($ownedSink) {
                $ownedSink->release(!$completed);
            }
        }

        return $result;
    }

    /**
     * Send a body with only the supplied headers, such as an upload part to its upload_uri.
     *
     * A connection failure is retried unless retryConnectErrors is false.
     *
     * @param string $url Destination URL.
     * @param string $verb HTTP method.
     * @param string|resource $body Request body.
     * @param array $headers Request headers.
     * @param bool $retryConnectErrors Retry connection failures when true.
     * @return \stdClass Object containing status, reason, headers and decoded data.
     * @throws \Files\Exception\FilesException When the request fails.
     */
    public static function sendFile($url, $verb, $body, $headers = [], $retryConnectErrors = true)
    {
        $params = ['body' => $body];

        if ($headers) {
            $params['headers'] = $headers;
        }

        return self::sendVerbatim($url, $verb, $params, $retryConnectErrors, false);
    }

    /**
     * Read response header values by case-insensitive name.
     *
     * @param array $headers Response headers.
     * @param string $name Header name.
     * @return string[] An empty array when the header is absent.
     */
    public static function headerValues($headers, $name)
    {
        foreach ($headers as $key => $values) {
            if (strcasecmp((string) $key, $name) === 0) {
                return $values;
            }
        }

        return [];
    }

    // The User-Agent sendRequest sends with the SDK's own requests: $identity, the SDK's name and version, followed by
    // one comment naming the coarse family of the operating system $phpOs (PHP_OS) names and the PHP runtime, with the
    // version $phpVersion (PHP_VERSION) gives, such as "Files.com PHP SDK v1.0 (files-sdk-meta/1; os=linux;
    // runtime=php/8.3.4)". The comment carries nothing else: no host or user name, path, architecture or build. A version
    // it cannot carry as given is left out rather than changed. When the whole User-Agent would be longer than 190 bytes,
    // the version is left out first and then the whole comment, so $identity is always sent as it is; an identity that
    // is not printable ASCII gets no comment. sendFile sends only the headers it is given.
    public static function userAgentWithMetadata($identity, $phpVersion = PHP_VERSION, $phpOs = PHP_OS)
    {
        $identity = (string) $identity;
        if (preg_match('/^[\x20-\x7e]+$/D', $identity) !== 1) {
            return $identity;
        }

        $runtimes = ['php'];
        if (is_string($phpVersion) && preg_match('/^[0-9][A-Za-z0-9.+_-]{0,47}$/D', $phpVersion) === 1) {
            $runtimes = ['php/' . $phpVersion, 'php'];
        }
        $os = self::userAgentOs($phpOs);
        foreach ($runtimes as $runtime) {
            $metadata = ' (files-sdk-meta/1; os=' . $os . '; runtime=' . $runtime . ')';
            if (strlen($metadata) <= 128 && strlen($identity) + strlen($metadata) <= 190) {
                return $identity . $metadata;
            }
        }

        return $identity;
    }

    // The coarse family of the operating system PHP_OS names: "unknown" for none, and "other" for one the User-Agent's
    // comment does not name.
    private static function userAgentOs($phpOs)
    {
        $name = strtolower((string) $phpOs);
        if ($name === '') {
            return 'unknown';
        }
        if (strpos($name, 'win') === 0 || strpos($name, 'cygwin') === 0) {
            return 'windows';
        }

        $families = ['darwin' => 'macos', 'linux' => 'linux', 'android' => 'android', 'freebsd' => 'freebsd'];
        return isset($families[$name]) ? $families[$name] : 'other';
    }

    /**
     * The opaque cursor of the page after a list response, or null on the last page. X-Files-Cursor-Next, in any case,
     * names it whenever the response has that header, even empty; X-Files-Cursor, its older name, counts only without
     * it. Only a missing or empty cursor ends a list, so even "0" is sent back unchanged.
     *
     * @internal Page and auto-pagination follow lists with this.
     * @param object $response A result of sendSingleRequest().
     * @return string|null
     */
    public static function nextCursor($response)
    {
        $headers = isset($response->headers) && is_array($response->headers) ? $response->headers : [];
        // A present X-Files-Cursor-Next decides, even with no value at all; X-Files-Cursor counts only without it.
        $values = null;
        foreach (['X-Files-Cursor-Next', 'X-Files-Cursor'] as $name) {
            foreach ($headers as $key => $headerValues) {
                if (strcasecmp((string) $key, $name) === 0) {
                    $values = $headerValues;
                    break 2;
                }
            }
        }
        $cursor = is_array($values) ? current($values) : null;

        return is_string($cursor) && $cursor !== '' ? $cursor : null;
    }

    /**
     * The attributes of a model that the API sent as a JSON object.
     *
     * @internal Generated models and Page build models only from these.
     * @param mixed $data A value decoded from a response body.
     * @param int $status The status of that response.
     * @return array
     * @throws \Files\Exception\ApiBadResponseException When $data is anything else, such as the null of an empty or
     *     non-JSON body, so that no model is built or refreshed from it.
     */
    public static function modelAttributes($data, $status)
    {
        if (!is_object($data)) {
            throw self::unexpectedJson('a JSON object', $data, $status);
        }

        return (array) $data;
    }

    /**
     * The entries of a list that the API sent as a JSON array, whatever the entries are.
     *
     * @internal Generated list methods, Page and auto-pagination read lists only through this.
     * @param mixed $data A value decoded from a response body.
     * @param int $status The status of that response.
     * @return array
     * @throws \Files\Exception\ApiBadResponseException When $data is anything else, such as a JSON object wrapping the
     *     list or the null of an empty or non-JSON body.
     */
    public static function listEntries($data, $status)
    {
        if (!is_array($data)) {
            throw self::unexpectedJson('a JSON array', $data, $status);
        }

        return $data;
    }

    private static function unexpectedJson($expected, $data, $status)
    {
        $found = $data === null
          ? 'null or no JSON at all'
          : 'a JSON ' . (is_array($data) ? 'array' : (is_object($data) ? 'object' : gettype($data)));

        return new Exception\ApiBadResponseException(
            "The response should have held $expected, but it held $found.",
            $status
        );
    }

    // With Files::$autoPaginate, a list response is followed to its last page, one request at a time, with the same
    // parameters and options, and the result holds every page's entries. Each page must be a JSON array before its
    // cursor is followed. A cursor this list already sent, the caller's own starting cursor included, could only
    // repeat pages, so the list stops there with an ApiBadResponseException. For the wire compatibility of earlier
    // releases, each later request also sends the next page number. The result has the status, reason and headers
    // of the last page.
    private static function autoPaginate($path, $verb, $params, $options, $response)
    {
        $cursor = Files::$autoPaginate ? self::nextCursor($response) : null;
        if ($cursor === null) {
            return $response;
        }

        $params = $params ?: [];
        $pages = [self::listEntries(isset($response->data) ? $response->data : null, $response->status)];
        $page = isset($params['page']) ? intval($params['page']) : 0;
        $page = $page ?: 1;
        $sentCursors = [];
        if (isset($params['cursor']) && (string) $params['cursor'] !== '') {
            $sentCursors[(string) $params['cursor']] = true;
        }
        do {
            if (isset($sentCursors[$cursor])) {
                throw new Exception\ApiBadResponseException(self::REPEATED_CURSOR_MESSAGE, 0);
            }
            $sentCursors[$cursor] = true;

            Logger::debug('Auto-pagination is enabled and next cursor was received - fetching next page...');

            $params['cursor'] = $cursor;
            $params['page'] = ++$page;
            $response = self::sendSingleRequest($path, $verb, $params, $options);
            $pages[] = self::listEntries(isset($response->data) ? $response->data : null, $response->status);
            $cursor = self::nextCursor($response);
        } while ($cursor !== null);

        Logger::debug('Auto-pagination is enabled the final cursor was processed - pagination complete.');

        return (object) array_merge([
            $response,
            'status' => $response->status,
            'reason' => $response->reason,
            'headers' => $response->headers,
            'autoPaginateRequests' => count($pages),
            'data' => call_user_func_array('array_merge', $pages),
        ]);
    }

    /**
     * Send an API request with the configured authentication and pagination behavior.
     *
     * @param string $path API path or external URL.
     * @param string $verb HTTP method.
     * @param array|null $params Query parameters for GET, or the JSON request body.
     * @param array $options Guzzle options and api_key, session_id, workspace_id or region_relocation overrides.
     * @param array $metadata Unused: auto-pagination follows a list's pages within one call.
     * @return \stdClass Object containing status, reason, headers and decoded data when no sink is used.
     * @throws \Files\Exception\FilesException When configuration, validation or a request fails.
     */
    public static function sendRequest($path, $verb, $params = null, $options = [], $metadata = [])
    {
        $response = self::sendSingleRequest($path, $verb, $params, $options);

        return preg_match('@^[a-zA-Z]+://@', $path)
          ? $response
          : self::autoPaginate($path, $verb, $params, $options, $response);
    }

    /**
     * Sends one request without following list cursors, regardless of Files::$autoPaginate.
     *
     * @internal Page and native directory handles fetch one page at a time with this.
     */
    public static function sendSingleRequest($path, $verb, $params = null, $options = [])
    {
        if (!is_string($path) || !is_string($verb)) {
            throw new Exception\InvalidParameterException('Request paths and HTTP methods must be strings.');
        }
        $options = $options ?: [];
        $headers = array_merge(
            Files::getLanguage()
              ? ['Accept-Language' => Files::getLanguage()]
              : [],
            !empty($options['headers']) ? $options['headers'] : [],
            ['User-Agent' => self::userAgentWithMetadata("Files.com PHP SDK v" . self::VERSION)]
        );

        $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

        if (!$isExternal) {
            $headers['Accept'] = 'application/json';
            $headers = self::withCredential(
                $headers,
                $options,
                Files::getSessionId(),
                Files::getApiKey(),
                $path,
                $verb
            );

            $workspaceId = array_key_exists('workspace_id', $options)
              ? $options['workspace_id']
              : Files::getWorkspaceId();

            if ($workspaceId !== null && $workspaceId !== '') {
                $headers['X-Files-Workspace-Id'] = (string) $workspaceId;
            }
        }

        $requestPath = $path;

        if ($params) {
            $headers['Content-Type'] = 'application/json';

            if (strtoupper($verb) === 'GET') {
                $requestPath .= (parse_url($path, PHP_URL_QUERY) ? '&' : '?') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
            } else {
                $options['body'] = json_encode($params);
            }
        }

        $options['headers'] = $headers;

        if (Files::$debugRequest) {
            Logger::debug('Request Options:');
            Logger::debug(
                array_merge(
                    $options,
                    [
                        'body' => is_array($params)
                          ? ('payload keys: ' . ($params ? implode(', ', array_keys($params)) : '(none)'))
                          : ('<' . gettype($params) . '>'),
                    ]
                )
            );
        }

        $response = self::sendVerbatim($requestPath, $verb, $options);

        return $response;
    }
}
