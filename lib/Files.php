<?php

declare(strict_types=1);

namespace Files;

if (!version_compare(PHP_VERSION, '5.5.0') < 0) {
  trigger_error('Minimum PHP version required is 5.5.0', E_USER_ERROR);
  exit;
}

if (!is_file(dirname(__FILE__) . '/../vendor/autoload.php')) {
  trigger_error('Required dependencies not installed. See packagist.org for instructions.', E_USER_ERROR);
  exit;
}

require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once dirname(__FILE__) . '/Api.php';
require_once dirname(__FILE__) . '/Logger.php';

require_once dirname(__FILE__) . '/Files/AccountLineItem.php';
require_once dirname(__FILE__) . '/Files/Action.php';
require_once dirname(__FILE__) . '/Files/ApiKey.php';
require_once dirname(__FILE__) . '/Files/As2Key.php';
require_once dirname(__FILE__) . '/Files/Auto.php';
require_once dirname(__FILE__) . '/Files/Automation.php';
require_once dirname(__FILE__) . '/Files/Behavior.php';
require_once dirname(__FILE__) . '/Files/Bundle.php';
require_once dirname(__FILE__) . '/Files/DnsRecord.php';
require_once dirname(__FILE__) . '/Files/Errors.php';
require_once dirname(__FILE__) . '/Files/File.php';
require_once dirname(__FILE__) . '/Files/FileAction.php';
require_once dirname(__FILE__) . '/Files/FileComment.php';
require_once dirname(__FILE__) . '/Files/FileCommentReaction.php';
require_once dirname(__FILE__) . '/Files/FilePartUpload.php';
require_once dirname(__FILE__) . '/Files/Folder.php';
require_once dirname(__FILE__) . '/Files/Group.php';
require_once dirname(__FILE__) . '/Files/GroupUser.php';
require_once dirname(__FILE__) . '/Files/History.php';
require_once dirname(__FILE__) . '/Files/HistoryExport.php';
require_once dirname(__FILE__) . '/Files/Image.php';
require_once dirname(__FILE__) . '/Files/Invoice.php';
require_once dirname(__FILE__) . '/Files/InvoiceLineItem.php';
require_once dirname(__FILE__) . '/Files/IpAddress.php';
require_once dirname(__FILE__) . '/Files/Lock.php';
require_once dirname(__FILE__) . '/Files/Message.php';
require_once dirname(__FILE__) . '/Files/MessageComment.php';
require_once dirname(__FILE__) . '/Files/MessageCommentReaction.php';
require_once dirname(__FILE__) . '/Files/MessageReaction.php';
require_once dirname(__FILE__) . '/Files/Notification.php';
require_once dirname(__FILE__) . '/Files/Payment.php';
require_once dirname(__FILE__) . '/Files/PaymentLineItem.php';
require_once dirname(__FILE__) . '/Files/Permission.php';
require_once dirname(__FILE__) . '/Files/Preview.php';
require_once dirname(__FILE__) . '/Files/Project.php';
require_once dirname(__FILE__) . '/Files/PublicKey.php';
require_once dirname(__FILE__) . '/Files/RemoteServer.php';
require_once dirname(__FILE__) . '/Files/Request.php';
require_once dirname(__FILE__) . '/Files/Session.php';
require_once dirname(__FILE__) . '/Files/Site.php';
require_once dirname(__FILE__) . '/Files/SsoStrategy.php';
require_once dirname(__FILE__) . '/Files/Status.php';
require_once dirname(__FILE__) . '/Files/Style.php';
require_once dirname(__FILE__) . '/Files/UsageDailySnapshot.php';
require_once dirname(__FILE__) . '/Files/UsageSnapshot.php';
require_once dirname(__FILE__) . '/Files/User.php';
require_once dirname(__FILE__) . '/Files/UserCipherUse.php';

class Files {
  private static $apiKey = null;
  private static $baseUrl = 'https://app.files.com';
  private static $endpointPrefix = '/api/rest/v1';
  private static $sessionId = null;

  public static $logLevel = LogLevel::INFO;
  public static $debugRequest = false;
  public static $debugResponseHeaders = false;

  public static $maxNetworkRetries = 3;
  public static $minNetworkRetryDelay = 0.5;
  public static $maxNetworkRetryDelay = 1.5;
  public static $connectTimeout = 30.0;
  public static $readTimeout = 90.0;

  public static function getApiKey() {
    return self::$apiKey;
  }

  public static function getBaseUrl() {
    return self::$baseUrl;
  }

  public static function getEndpointPrefix() {
    return self::$endpointPrefix;
  }

  public static function getSessionId() {
    return self::$sessionId;
  }

  public static function setApiKey($apiKey) {
    self::$apiKey = $apiKey;
  }

  public static function setBaseUrl($baseUrl) {
    self::$baseUrl = $baseUrl;
  }

  public static function setEndpointPrefix($endpointPrefix) {
    self::$endpointPrefix = $endpointPrefix;
  }

  public static function setSessionId($apiKey) {
    self::$sessionId = $apiKey;
  }
}
