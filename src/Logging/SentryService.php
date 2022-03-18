<?php

namespace Wpce\Logging;

use Wpce\Utils\Environment;

class SentryService {

  static $isInstalled = false;

  static public function initSentry() {
    if (defined('WPCE_SENTRY_DSN') && function_exists('\Sentry\init') && \WPCE_SENTRY_DSN) {
      \Sentry\init([
        'dsn' => \WPCE_SENTRY_DSN,
        'environment' => Environment::getEnvironment(),
      ]);

      self::$isInstalled = true;
    }
  }

  static public function captureException(\Exception $exception) {
    if (self::$isInstalled) {
      \Sentry\captureException($exception);
    }
  }

  static public function setExtra($name, $value) {
    if (self::$isInstalled) {
      \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($name, $value) {
        $scope->setExtra($name, $value);
      });
    }
  }

}
