<?php

declare(strict_types=1);

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

class Files
{
    private static $apiKey = null;
    private static $baseUrl = 'https://app.files.com';
    private static $endpointPrefix = '/api/rest/v1';
    private static $sessionId = null;
    private static $handler = null;

    public static $logLevel = LogLevel::WARN;
    public static $debugRequest = false;
    public static $debugResponseHeaders = false;

    public static $maxNetworkRetries = 3;
    public static $minNetworkRetryDelay = 0.5;
    public static $maxNetworkRetryDelay = 1.5;
    public static $connectTimeout = 30.0;
    public static $readTimeout = 60.0;
    public static $autoPaginate = true;

    public static function getApiKey()
    {
        return self::$apiKey;
    }

    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    public static function getEndpointPrefix()
    {
        return self::$endpointPrefix;
    }

    public static function getSessionId()
    {
        return self::$sessionId;
    }

    public static function getHandler()
    {
        return self::$handler === null ? new CurlHandler() : self::$handler;
    }

    public static function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    public static function setBaseUrl($baseUrl)
    {
        self::$baseUrl = $baseUrl;
    }

    public static function setEndpointPrefix($endpointPrefix)
    {
        self::$endpointPrefix = $endpointPrefix;
    }

    public static function setSessionId($apiKey)
    {
        self::$sessionId = $apiKey;
    }

    public static function setHandler($handler)
    {
        self::$handler = $handler;
    }
}
