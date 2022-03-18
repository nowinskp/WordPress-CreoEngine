<?php

namespace Wpce\Filesystem;

class Path {

  /**
   * Gets first found file that matches given pattern.
   * Assumes all files in dir are in format:
   * `filename.hash.extension`
   * Also assumes all files in dir have hashes of equal length.
   *
   *  Sample usage:
   *  $stylesheetDir = get_stylesheet_directory().'/css';
   *  $mainStylesFilename = Dir::getPathToFileWithHash($stylesheetDir, 'main', 'css', 4);
   *  if ($mainStylesFilename) {
   *    wp_enqueue_style('mainstyle', $templateUrl.'/css/'.$mainStylesFilename);
   *  }
   *
   * @param string $pathToDir
   * @param string $fileName
   * @param string $fileExtension
   * @param int $hashLength
   *
   * @return string
   */
  static function getPathToFileWithHash(string $pathToDir, string $fileName, string $fileExtension, int $hashLength): string {
    $dir = new \DirectoryIterator($pathToDir);
    $fileNameLength = strlen($fileName);
    $fileExtensionLength = strlen($fileExtension);
    foreach ($dir as $file) {

      $foundFileName = basename($file);
      $foundFileNameMatchingPart = substr($foundFileName, 0, $fileNameLength);
      $foundFileExtensionMatchingPart = substr($foundFileName, -$fileExtensionLength);
      if (
        $foundFileNameMatchingPart === $fileName
        && $foundFileExtensionMatchingPart === $fileExtension
      ) {
        // rest of filename should be equal to '.' + [string of hash size length] + '.'
        $restOfFilename = substr($foundFileName, $fileNameLength, strlen($foundFileName) - $fileNameLength - $fileExtensionLength);
        $restOfFilenameWithTrimmedDots = trim($restOfFilename, '.');
        if (strlen($restOfFilenameWithTrimmedDots) === $hashLength) {
          return $foundFileName;
        }
      }
    }
    return '';
  }

}
