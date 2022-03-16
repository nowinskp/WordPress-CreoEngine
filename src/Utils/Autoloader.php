<?php

namespace Wpce\Utils;

class Autoloader {

  /**
   * Returns default PSR-4 compatible autoloader.
   * Setting $skippedDirectChildDirName breaks PSR-4 compatibility.
   *
   * @param string $rootDir directory that should be searched for classes
   * @param string|null $skippedDirectChildDirName if set, skips left part of classname-to-directory coversion,
   * allowing user to put classes one step higher in directory hierarchy. Eg. setting it to `components` will
   * make autoloader search for any classes with namespaces starting from `components/` directly in the `$rootDir`
   * instead of `$rootDir/components`.
   * @return null
   */
  static function getDefaultAutoloader(string $rootDir, string $skippedDirectChildDirName = null) {

    return function ($className) use ($rootDir, $skippedDirectChildDirName) {
      $className = ltrim($className, '\\');

      if ($skippedDirectChildDirName) {
        $className = ltrim($className, $skippedDirectChildDirName . '\\');
      }

      $folderName = $rootDir . '/';
      $fileName  = '';
      $namespace = '';

      if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
      }

      $fileName .= $className . '.php';

      if (file_exists($folderName.$fileName)) {
        require $folderName.$fileName;
      }

      return null;
    };
  }

}
