<?php

declare(strict_types=1);

namespace Files;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class Api {
  private static function pushRetryHandler($handlerStack) {
    $shouldRetry = function($retries, $request, $response, $exception) {
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

    $getRetryDelay = function($retries) {
      return min(
        ($retries + 1) * Files::$minNetworkRetryDelay * 1000,
        Files::$maxNetworkRetryDelay * 1000
    ) ;
    };

    $handlerStack->push(Middleware::retry($shouldRetry, $getRetryDelay));
  }

  private static function sendVerbatim($path, $verb, $options) {
    $isExternal = preg_match('@^[a-zA-Z]+://@', $path);
    $baseUrl = Files::getBaseUrl();

    if (!$isExternal && !$baseUrl) {
      trigger_error('Base URL has not been set - use Files::setBaseUrl() to set it', E_USER_ERROR);
      exit;
    }

    $url = $isExternal
      ? $path
      : $baseUrl . Files::getEndpointPrefix() . $path;

    Logger::debug("Sending request: " . $verb . " $url");

    $handlerStack = HandlerStack::create(new CurlHandler());
    self::pushRetryHandler($handlerStack);

    $client = new Client([
      'connect_timeout' => Files::$connectTimeout,
      'handler' => $handlerStack,
      'read_timeout' => Files::$readTimeout,
    ]);

    try {
      $response = $client->request($verb, $url, $options);
    } catch (\Exception $error) {
      Logger::error(get_class($error) . ' > ' . $error->getMessage());
      return null;
    }

    $statusCode = $response->getStatusCode();
    $statusReason = $response->getReasonPhrase();

    Logger::debug("Status: $statusCode $statusReason");

    if (Files::$debugResponseHeaders) {
      Logger::debug('Response Headers: ');
      Logger::debug($response->getHeaders());
    }

    return (object)[
      'status' => $statusCode,
      'reason' => $statusReason,
      'headers' => $response->getHeaders(),
      'data' => json_decode((string)$response->getBody()),
    ];
  }

  public static function sendFile($url, $verb, $body, $headers = []) {
    $params = ['body' => $body];

    if ($headers) {
      $params['headers'] = $headers;
    }

    return self::sendVerbatim($url, $verb, $params);
  }

  public static function sendRequest($path, $verb, $params = null, $options = []) {
    $options = $options ?: [];
    $headers = array_merge($options['headers'] ?: [], [
      'Accept' => 'application/json',
      'User-Agent' => 'Files-PHP-SDK',
    ]);

    $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

    if (!$isExternal) {
      $sessionId = Files::getSessionId();

      if ($sessionId) {
        $headers['X-FilesAPI-Auth'] = $sessionId;
      } else {
        $isCreatingSession = $path === '/sessions' && strtoupper($verb) === 'POST';

        // api key cannot be used when creating a session
        if (!$isCreatingSession) {
          $apiKey = Files::getApiKey();

          if (!$apiKey) {
            trigger_error('API key has not been set - use Files::setApiKey() to set it', E_USER_ERROR);
            exit;
          }

          $headers['X-FilesAPI-Key'] = $apiKey;
        }
      }
    }

    if ($params) {
      $options['body'] = json_encode($params);
      $headers['Content-Type'] = 'application/json';
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
            'headers' => array_merge($headers, ['X-FilesAPI-Key' => '<redacted>'])
          ]
        )
      );
    }

    return self::sendVerbatim($path, $verb, $options);
  }
}
