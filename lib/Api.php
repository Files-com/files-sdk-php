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
  private const VERSION = "1.0.389";
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

    $result = (object)[
      'status' => $statusCode,
      'reason' => $statusReason,
      'headers' => $response->getHeaders(),
    ];

    // if the response wasn't redirected into a stream or file, add it to the result
    if (!@$options['sink']) {
      $result->data = json_decode((string)$response->getBody());
    }

    return $result;
  }

  public static function sendFile($url, $verb, $body, $headers = []) {
    $params = ['body' => $body];

    if ($headers) {
      $params['headers'] = $headers;
    }

    return self::sendVerbatim($url, $verb, $params);
  }

  private static function autoPaginate($path, $verb, $params, $options, $response, $metadata = []) {
    if (Files::$autoPaginate) {
      $nextCursor = current(@$response->headers['X-Files-Cursor'] ?: []);

      $autoPaginateCount = @$metadata['autoPaginateCount'];
      $previousAutoPaginateData = @$metadata['previousAutoPaginateData'];

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
      } else if ($previousAutoPaginateData) {
        Logger::debug('Auto-pagination is enabled the final cursor was processed - pagination complete.');

        return (object)array_merge([
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

  public static function sendRequest($path, $verb, $params = null, $options = [], $metadata = []) {
    $options = $options ?: [];
    $headers = array_merge(@$options['headers'] ?: [], [
      'User-Agent' => "Files.com PHP SDK v" . self::VERSION,
    ]);

    $isExternal = preg_match('@^[a-zA-Z]+://@', $path);

    if (!$isExternal) {
      $headers['Accept'] = 'application/json';

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
            'headers' => array_merge($headers, ['X-FilesAPI-Key' => '<redacted>'])
          ]
        )
      );
    }

    $response = self::sendVerbatim($requestPath, $verb, $options);

    return $isExternal
      ? $response
      : self::autoPaginate($path, $verb, $params, $options, $response, $metadata);
  }
}
