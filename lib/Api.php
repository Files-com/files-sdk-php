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
    const VERSION = "2.0.734";

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
        // A response may already have written to this caller-owned sink. Do not close it or append a second answer.
        if (isset($options['sink']) && (is_resource($options['sink']) || $options['sink'] instanceof \Psr\Http\Message\StreamInterface)) {
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

    // The request path and Guzzle options of an API request for $path (under the endpoint prefix, or an external URL),
    // as sendRequest sends it: the headers, with the language, session, API key and workspace of $config
    // (Files::snapshot(), or a transfer job's copy of it) unless $options gives its own, and $params as the query of a
    // GET or the JSON body of anything else. Throws a ConfigurationException for an API request without an API key.
    public static function prepareApiRequest($config, $path, $verb, $params = null, $options = [])
    {
        $options = $options ?: [];
        $headers = array_merge(
            $config['language']
              ? ['Accept-Language' => $config['language']]
              : [],
            @$options['headers'] ?: [],
            ['User-Agent' => "Files.com PHP SDK v" . self::VERSION]
        );

        $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

        if (!$isExternal) {
            $headers['Accept'] = 'application/json';

            $sessionId = @$options['session_id'] ?: $config['session_id'];
            $workspaceId = array_key_exists('workspace_id', $options)
              ? $options['workspace_id']
              : $config['workspace_id'];

            if ($sessionId) {
                $headers['X-FilesAPI-Auth'] = $sessionId;
            } else {
                $isCreatingSession = $path === '/sessions' && strtoupper($verb) === 'POST';

                // api key cannot be used when creating a session
                if (!$isCreatingSession) {
                    $apiKey = @$options['api_key'] ?: $config['api_key'];

                    if (!$apiKey) {
                        throw new Exception\ConfigurationException('No Files.com API key provided. Set your API key using Files::setApiKey(<API-KEY>). You can generate API keys from the Files.com web interface.');
                    }

                    $headers['X-FilesAPI-Key'] = $apiKey;
                }
            }

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

        try {
            $response = $client->request($verb, $url, $options);
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

    private static function autoPaginate($path, $verb, $params, $options, $response, $metadata = [])
    {
        if (Files::$autoPaginate) {
            $nextCursor = current(self::headerValues($response->headers, 'X-Files-Cursor'));

            $autoPaginateCount = isset($metadata['autoPaginateCount']) ? $metadata['autoPaginateCount'] : null;
            $previousAutoPaginateData = isset($metadata['previousAutoPaginateData']) ? $metadata['previousAutoPaginateData'] : null;

            if ($nextCursor) {
                $nextPage = (intval(@$params['page']) ?: 1) + 1;

                $nextParams = $params ?: [];
                $nextParams['cursor'] = $nextCursor;
                $nextParams['page'] = $nextPage;

                $nextMetadata = [
                    'autoPaginateCount' => ($autoPaginateCount ?: 1) + 1,
                    'previousAutoPaginateData' => array_merge(
                        $previousAutoPaginateData ?: [],
                        $response->data
                    ),
                ];

                Logger::debug('Auto-pagination is enabled and next cursor was received - fetching next page...');

                return self::sendRequest($path, $verb, $nextParams, $options, $nextMetadata);
            } elseif ($previousAutoPaginateData) {
                Logger::debug('Auto-pagination is enabled the final cursor was processed - pagination complete.');

                return (object) array_merge([
                    $response,
                    'autoPaginateRequests' => $autoPaginateCount,
                    'data' => array_merge(
                        $previousAutoPaginateData,
                        $response->data
                    ),
                ]);
            }
        }

        return $response;
    }

    /**
     * Send an API request with the configured authentication and pagination behavior.
     *
     * @param string $path API path or external URL.
     * @param string $verb HTTP method.
     * @param array|null $params Query parameters for GET, or the JSON request body.
     * @param array $options Guzzle options and api_key, session_id, workspace_id or region_relocation overrides.
     * @param array $metadata Pagination state used internally by the transport.
     * @return \stdClass Object containing status, reason, headers and decoded data when no sink is used.
     * @throws \Files\Exception\FilesException When configuration, validation or a request fails.
     */
    public static function sendRequest($path, $verb, $params = null, $options = [], $metadata = [])
    {
        $response = self::sendSingleRequest($path, $verb, $params, $options);

        return preg_match('@^[a-zA-Z]+://@', $path)
          ? $response
          : self::autoPaginate($path, $verb, $params, $options, $response, $metadata);
    }

    /**
     * Sends one request without following list cursors, regardless of Files::$autoPaginate.
     *
     * @internal Native directory handles fetch one page at a time with this.
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
            ['User-Agent' => "Files.com PHP SDK v" . self::VERSION]
        );

        $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

        if (!$isExternal) {
            $headers['Accept'] = 'application/json';

            $sessionId = !empty($options['session_id']) ? $options['session_id'] : Files::getSessionId();
            $workspaceId = array_key_exists('workspace_id', $options)
              ? $options['workspace_id']
              : Files::getWorkspaceId();

            if ($sessionId) {
                $headers['X-FilesAPI-Auth'] = $sessionId;
            } else {
                $isCreatingSession = $path === '/sessions' && strtoupper($verb) === 'POST';

                // api key cannot be used when creating a session
                if (!$isCreatingSession) {
                    $apiKey = !empty($options['api_key']) ? $options['api_key'] : Files::getApiKey();

                    if (!$apiKey) {
                        throw new Exception\ConfigurationException('No Files.com API key provided. Set your API key using Files::setApiKey(<API-KEY>). You can generate API keys from the Files.com web interface.');
                    }

                    $headers['X-FilesAPI-Key'] = $apiKey;
                }
            }

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
