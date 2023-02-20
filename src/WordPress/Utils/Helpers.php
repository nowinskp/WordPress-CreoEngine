<?php

namespace Wpce\WordPress\Utils;

class Helpers {

  /**
   * Returns image url for given image id
   *
   * @param int $imageId
   * @param string $size
   * @return ?string image url or null if there's none
   */
  static function getImageUrlById(int $imageId, $size = 'large') {
    if (!$imageId) {
      return null;
    }
    $imageData = wp_get_attachment_image_src($imageId, $size);
    return $imageData ? $imageData[0] : null;
  }

  /**
   * Returns URL to current directory in WordPress theme dir.
   *
   * @param string $currentDir current dir path, usually the value of __DIR__
   * @return string
   */
  static function getUrlToCurrentThemeDir(string $currentDir) {
    $themeDirName = wp_get_theme()->get_template();
    $themeUrl = get_bloginfo('template_url');
    $themeDirPath = 'themes/'.$themeDirName;
    $pathFromRoot = substr($currentDir, strlen($_SERVER['DOCUMENT_ROOT']));
    $themeDirPos = strpos($pathFromRoot, $themeDirPath);
    $pathToCurrentDirFromThemeRoot = substr($pathFromRoot, strlen($themeDirPath) + $themeDirPos);
    return $themeUrl.$pathToCurrentDirFromThemeRoot;
  }

}
