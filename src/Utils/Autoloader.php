<?php

namespace Wpce\Utils;

class Autoloader {

  static function getDefaultPsr4Autoloader(string $rootDir) {

    return function ($className) use ($rootDir) {
      $className = ltrim($className, '\\');
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
        return true;
      } else {
        return false;
      }
    };
  }

}
