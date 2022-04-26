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

}
