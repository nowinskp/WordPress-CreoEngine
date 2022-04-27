<?php

namespace Wpce\WordPress\Query;

use Wpce\Utils\Get;

class Utils {

  /**
   * Returns post type of the current query
   *
   * PLEASE NOTE: this assumes that categories (built-in & custom)
   * use only a single post type per taxonomy as only the 1st post type
   * is returned for them.
   */
  static function getQueriedPostType() {
    if (is_singular()) {
      return get_post_type();
    }

    if (is_tax() || is_category() || is_tag()) {
      $taxonomy = get_queried_object()->taxonomy;
      $taxonomyObject = get_taxonomy($taxonomy);
      return $taxonomyObject->object_type[0];
    }

    return get_queried_object() ? get_queried_object()->name : null;
  }

  /**
   * Returns post type meta key value w/ default value
   * Operates on single meta values only.
   */
  static function getPostMeta($id, $metaKey, $default = null) {
    $metaValue = get_post_meta($id, $metaKey, true);
    return Get::notEmpty($metaValue, $default);
  }

}
