<?php

namespace Wpce\Utils;

/**
 * Util for determining what environment the website is currently run in.
 * Checks the site's home url against predefined settings.
 * Optionally can be changed to use wp_get_environment_type call result insted.
 */
class Environment {

  static $localhostUrlPart = '//localhost';
  static $stagingUrlPart = null;
  static $preferNativeWpCheck = false;

  const ENVIRONMENT_TYPE_LOCAL = 'local';
  const ENVIRONMENT_TYPE_STAGING = 'staging';
  const ENVIRONMENT_TYPE_PRODUCTION = 'production';

  static private function getAvailableEnvironments() {
    return [
      self::ENVIRONMENT_TYPE_LOCAL,
      self::ENVIRONMENT_TYPE_STAGING,
      self::ENVIRONMENT_TYPE_PRODUCTION,
    ];
  }

  static function setLocalhostUrlPart($url) {
    self::$localhostUrlPart = $url;
  }

  static function setStagingUrlPart($url) {
    self::$stagingUrlPart = $url;
  }

  static function preferNativeWpCheck($switch) {
    self::$preferNativeWpCheck = $switch;
  }

  static function getEnvironment() {
    if (self::$preferNativeWpCheck && function_exists('wp_get_environment_type')) {
      $configEnvironment = wp_get_environment_type();
      if (in_array($configEnvironment, self::getAvailableEnvironments())) {
        return $configEnvironment;
      }
    }

    return self::determineEnvironmentByHomeUrlDatabaseEntry();
  }

  static function determineEnvironmentByHomeUrlDatabaseEntry() {
    $homeUrl = home_url();
    if (isset(self::$localhostUrlPart) && strpos($homeUrl, self::$localhostUrlPart) !== false) {
      return self::ENVIRONMENT_TYPE_LOCAL;
    }
    if (isset(self::$stagingUrlPart) && strpos($homeUrl, self::$stagingUrlPart) !== false) {
      return self::ENVIRONMENT_TYPE_STAGING;
    }
    return self::ENVIRONMENT_TYPE_PRODUCTION;
  }

  static function isLocal() {
    return self::getEnvironment() === self::ENVIRONMENT_TYPE_LOCAL;
  }

  static function isRemote() {
    return self::getEnvironment() !== self::ENVIRONMENT_TYPE_LOCAL;
  }

  static function isStaging() {
    return self::getEnvironment() === self::ENVIRONMENT_TYPE_STAGING;
  }

  static function isProduction() {
    return self::getEnvironment() === self::ENVIRONMENT_TYPE_PRODUCTION;
  }

}
