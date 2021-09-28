<?php

declare(strict_types=1);

namespace Files;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

function middlewareRemoveHeader($header) {
  return function ($handler) use ($header) {
    return function ($request, $options) use ($handler, $header) {
      $request = $request->withoutHeader($header);
      return $handler($request, $options);
    };
  };
}

class Api {
  private static function pushRetryHandler($handlerStack) {
    $shouldRetry = function($retries, $request, $response, $exception) {
      if ($retries >= Files::$maxNetworkRetries) {
        Logger::info('Retries exhausted - giving up on this request');
        handleErrorResponse($exception);
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
      throw new ConfigurationException('Site URL has not been set. Set your site URL using Files::setBaseUrl(<BASE-URL>).');
    }

    $url = $isExternal
      ? $path
      : $baseUrl . Files::getEndpointPrefix() . $path;

    Logger::debug("Sending request: " . $verb . " $url");

    $handlerStack = HandlerStack::create(new CurlHandler());
    self::pushRetryHandler($handlerStack);

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
    $headers = array_merge(@$options['headers'] ?: [], [
      'Accept' => 'application/json',
      'User-Agent' => 'Files.com PHP SDK v1.0',
    ]);

    $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

    if (!$isExternal) {
      $sessionId = @$options['session_id'] ?: Files::getSessionId();

      if ($sessionId) {
        $headers['X-FilesAPI-Auth'] = $sessionId;
      } else {
        $isCreatingSession = $path === '/sessions' && strtoupper($verb) === 'POST';

        // api key cannot be used when creating a session
        if (!$isCreatingSession) {
          $apiKey = @$options['api_key'] ?: Files::getApiKey();

          if (!$apiKey) {
            throw new ConfigurationException('No Files.com API key provided. Set your API key using Files::setApiKey(<API-KEY>). You can generate API keys from the Files.com web interface.');
          }

          $headers['X-FilesAPI-Key'] = $apiKey;
        }
      }
    }

    if ($params) {
      $headers['Content-Type'] = 'application/json';

      if (strtoupper($verb) === 'GET') {
        $path .= (parse_url($path, PHP_URL_QUERY) ? '&' : '?') . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
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
            'headers' => array_merge($headers, ['X-FilesAPI-Key' => '<redacted>'])
          ]
        )
      );
    }

    return self::sendVerbatim($path, $verb, $options);
  }
}
