<?php

namespace Wpce\Filesystem;

use Wpce\Logging\SentryService;

/**
 * Handles basic operations on directories
 */
class Dir {

  /**
   * Gets path to a directory
   * Creates it w/ given mode if it doesn't exist and returns its path
   *
   * @param string $path - path to directory
   * @param integer $mode - mode in octal number, eg. 0775
   *
   * @return string - path to (created) directory
   */
  static function getDirectory($path, $mode) {
    if (is_dir($path)) {
      return $path;
    }

    $oldUmask = umask(0);
    $createdSuccessfully = false;
    if (mkdir($path, $mode) && chmod($path, $mode)) {
      $createdSuccessfully = true;
    }
    umask($oldUmask);
    if (!$createdSuccessfully) {
      throw new \Exception('Failed to create a root dir for protege files.');
    }
    return $path;
  }

  /**
   * Deletes all files an sub-directories from a directory
   *
   * @param string $dirPath - path to the directory
   * @param bool $removeParentDir - whether to remove the parent dir itself
   * @return bool true if successful
   */
  static function deleteDirectory(string $dirPath, bool $removeParentDir = true): bool {
    try {
      $directoryIterator = new \DirectoryIterator($dirPath);
      foreach ($directoryIterator as $fileinfo) {
        if ($fileinfo->isDot()) {
          continue;
        }
        if ($fileinfo->isDir()) {
          if (self::deleteDirectory($fileinfo->getPathname(), false)) {
            @rmdir($fileinfo->getPathname());
          }
        }
        if ($fileinfo->isFile()) {
          @unlink($fileinfo->getPathname());
        }
      }
      if ($removeParentDir && is_dir($dirPath)) {
        @rmdir($dirPath);
      }
    } catch (\Exception $e) {
      SentryService::captureException($e);
      return false;
    }
    return true;
  }

}
