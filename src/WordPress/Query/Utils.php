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
   * Returns title for archive pages
   */
  static function getArchiveTitle() {
    $queriedObject = @get_queried_object();

    if (is_category() || is_tax() || is_tag()):
      return $queriedObject->name;
    elseif (is_day()):
			echo __('Day archive:', 'wpce-getArchiveTitle').' '.get_the_date();
    elseif (is_month()):
      echo __('Month archive:', 'wpce-getArchiveTitle').' '.get_the_date('F Y');
    elseif (is_year()):
      echo __('Year archive:', 'wpce-getArchiveTitle').' '.get_the_date('Y');
    elseif (is_archive()):
      return $queriedObject->labels->archive_name ?: $queriedObject->label;
    else:
      return __('Archive', 'wpce-getArchiveTitle');
    endif;
  }

}
