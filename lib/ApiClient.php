<?php
# frozen_string_literal: true

namespace Files;

class AuthenticationException extends Exception { }
class Exception extends Exception { }
class InvalidRequestError extends Exception { }
class PermissionError extends Exception { }
class RateLimitError extends Exception { }

class ApiClient {
  private $conn = null;

  function __construct($conn = null) {
    $this->conn = $conn;
    if (!$this->conn) {
      $this->conn = $this->defaultConnection();
    }
  }

  public function activeClient() {
  }

  public static function defaultClient() {
  }

  public static function defaultConn() {
  }

  private static function apiUrl($url = '', $baseUrl = null) {
    return $baseUrl ?: Files\baseUrl() . '/api/rest/v1' . $url;
  }

  private function checkApiKey($apiKey) {
    # raise 
    if (!$apiKey) {
      throw new AuthenticationException('No Files.com API key provided. Set your API key using "Files.apiKey = <YOUR-API-KEY>;". You can generate API keys from the Files.com\'s web interface.');
    }

    if (preg_match('/\\s/', $apiKey)) {
      throw new AuthenticationException('Your API key is invalid (it contains whitespace)');
    }
  }

  private function checkSessionId($sessionId) {
    if (preg_match('/\\s/', $sessionId)) {
      throw new AuthenticationException('The provided Session ID is invalid (it contains whitespace)');
    }
  }
}
