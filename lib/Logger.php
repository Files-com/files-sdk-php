<?php

declare(strict_types=1);

namespace Files;

class LogLevel {
  const NONE = 0;
  const ERROR = 1;
  const WARN = 2;
  const INFO = 3;
  const DEBUG = 4;
}

class Logger {
  private static $stream;
  private static $isPaused = false;

  public static function getOutputStream() {
    return $stream ?: fopen('php://stdout', 'w');
  }

  public static function setOutputStream($stream) {
    self::$stream = $stream;
  }

  private static function getLogLevelName($level) {
    switch ($level) {
      case LogLevel::ERROR: return 'error';
      case LogLevel::WARN: return 'warn';
      case LogLevel::INFO: return 'info';
      case LogLevel::DEBUG: return 'debug';
    }

    return $level;
  }

  public static function error($message) {
    self::log($message, LogLevel::ERROR);
  }

  public static function warn($message) {
    self::log($message, LogLevel::WARN);
  }

  public static function info($message) {
    self::log($message, LogLevel::INFO);
  }

  public static function debug($message) {
    self::log($message, LogLevel::DEBUG);
  }

  public static function log($message, $level = LogLevel::INFO) {
    if (self::$isPaused) {
      return;
    }

    if (Files::$logLevel < $level) {
      return;
    }

    $stream = self::getOutputStream();

    $prefix = date('Y-m-d H:i:s') . ' [' . self::getLogLevelName($level) . ']: ';

    if (is_scalar($message)) {
      fwrite($stream, $prefix . $message . "\n");
    } else {
      fwrite($stream, $prefix . print_r($message, true));
    }
  }

  public static function pause() {
    self::$isPaused = true;
  }

  public static function unpause() {
    self::$isPaused = false;
  }
}
