<?php

namespace Files;

use GuzzleHttp\Handler\CurlHandler;

if (!version_compare(PHP_VERSION, '5.5.0') < 0) {
    trigger_error('Minimum PHP version required is 5.5.0', E_USER_ERROR);
    exit;
}

$isDevelopment = getenv('FILES_SESSION_ENV') === 'development';
$autoloadPath = $isDevelopment ? '/../vendor/autoload.php' : '/../../../autoload.php';

if (!is_file(__DIR__ . $autoloadPath)) {
    trigger_error('Required dependencies not installed. See packagist.org for instructions.', E_USER_ERROR);
    exit;
}

require_once __DIR__ . $autoloadPath;
require_once __DIR__ . '/Errors.php';
require_once __DIR__ . '/Api.php';
require_once __DIR__ . '/Logger.php';

/**
 * Global Files.com API authentication, request and logging configuration.
 */
class Files
{
    private static $apiKey = null;
    private static $baseUrl = 'https://app.files.com';
    private static $endpointPrefix = '/api/rest/v1';
    private static $sessionId = null;
    private static $workspaceId = null;
    private static $language = null;
    private static $handler = null;
    private static $regionRelocation = true;

    /** @var int Logging verbosity; defaults to LogLevel::WARN. */
    public static $logLevel = LogLevel::WARN;
    /** @var bool Log request metadata when enabled. */
    public static $debugRequest = false;
    /** @var bool Log response headers when enabled. */
    public static $debugResponseHeaders = false;

    /** @var int Maximum retries after the initial network request; defaults to 3. */
    public static $maxNetworkRetries = 3;
    /** @var float Linear network retry delay factor in seconds; defaults to 0.5, capped by maxNetworkRetryDelay. */
    public static $minNetworkRetryDelay = 0.5;
    /** @var float Maximum network retry delay in seconds; defaults to 1.5. */
    public static $maxNetworkRetryDelay = 1.5;
    /** @var float Connection timeout in seconds; defaults to 30.0. */
    public static $connectTimeout = 30.0;
    /** @var float Read timeout in seconds; defaults to 60.0. */
    public static $readTimeout = 60.0;
    /** @var bool Fetch and combine subsequent list pages automatically; defaults to true. */
    public static $autoPaginate = true;

    /**
     * Get the global API key.
     *
     * @return string|null
     */
    public static function getApiKey()
    {
        return self::$apiKey;
    }

    /**
     * Get the API origin URL.
     *
     * @return string
     */
    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    /**
     * Get the API endpoint prefix.
     *
     * @return string
     */
    public static function getEndpointPrefix()
    {
        return self::$endpointPrefix;
    }

    /**
     * Get the global session ID.
     *
     * @return string|null
     */
    public static function getSessionId()
    {
        return self::$sessionId;
    }

    /**
     * Get the global workspace context.
     *
     * @return int|string|null
     */
    public static function getWorkspaceId()
    {
        return self::$workspaceId;
    }

    /**
     * Get the preferred response language.
     *
     * @return string|null
     */
    public static function getLanguage()
    {
        return self::$language;
    }

    /**
     * Get the configured Guzzle handler, or a new CurlHandler by default.
     *
     * @return callable
     */
    public static function getHandler()
    {
        return self::$handler === null ? new CurlHandler() : self::$handler;
    }

    // The configuration as it stands, for Api::sendRequest and for a transfer job to keep: the API key, site URL,
    // endpoint prefix, session, workspace and language, the network retries and timeouts, and the handler setHandler
    // gave, or null when none was given (getHandler then makes a new CurlHandler for each request).
    public static function snapshot()
    {
        return [
            'api_key' => self::$apiKey,
            'base_url' => self::$baseUrl,
            'endpoint_prefix' => self::$endpointPrefix,
            'session_id' => self::$sessionId,
            'workspace_id' => self::$workspaceId,
            'language' => self::$language,
            'handler' => self::$handler,
            'max_network_retries' => self::$maxNetworkRetries,
            'min_network_retry_delay' => self::$minNetworkRetryDelay,
            'max_network_retry_delay' => self::$maxNetworkRetryDelay,
            'connect_timeout' => self::$connectTimeout,
            'read_timeout' => self::$readTimeout,
        ];
    }

    /**
     * Set the global API key.
     *
     * @param string|null $apiKey
     * @return void
     */
    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Set the API origin URL.
     *
     * @param string $baseUrl
     * @return void
     */
    public static function setBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    /**
     * Set the API endpoint prefix.
     *
     * @param string $endpointPrefix
     * @return void
     */
    public static function setEndpointPrefix($endpointPrefix)
    {
        self::$endpointPrefix = $endpointPrefix;
    }

    /**
     * Set the global session ID.
     *
     * @param string|null $apiKey
     * @return void
     */
    public static function setSessionId($apiKey)
    {
        self::$sessionId = $apiKey;
    }

    /**
     * Set the global workspace context; null clears it.
     *
     * @param int|string|null $workspaceId
     * @return void
     */
    public static function setWorkspaceId($workspaceId)
    {
        self::$workspaceId = $workspaceId;
    }

    /**
     * Set the preferred response language.
     *
     * @param string|null $language
     * @return void
     */
    public static function setLanguage($language)
    {
        self::$language = $language;
    }

    /**
     * Get the default policy for recognized API region mismatches.
     *
     * @return bool|callable
     */
    public static function getRegionRelocation()
    {
        return self::$regionRelocation;
    }

    /**
     * Set the default API region policy; a callback approves only by returning true.
     *
     * @param bool|callable $policy True follows, false refuses, or a callback receives target and current origins.
     * @return void
     * @throws \Files\Exception\ConfigurationException When the policy is neither boolean nor callable.
     */
    public static function setRegionRelocation($policy)
    {
        if (!is_bool($policy) && !is_callable($policy)) {
            throw new Exception\ConfigurationException('Region relocation must be a boolean or callable.');
        }
        self::$regionRelocation = $policy;
    }

    /**
     * Set a Guzzle handler; null restores the default CurlHandler.
     *
     * @param callable|null $handler
     * @return void
     */
    public static function setHandler($handler)
    {
        self::$handler = $handler;
    }
}
