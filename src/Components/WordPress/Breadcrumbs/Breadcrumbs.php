<?php

namespace Wpce\Components\WordPress\Breadcrumbs;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\Utils\Text;
use Wpce\WordPress\Query\Utils;

/**
 * Breadcrumbs generator
 *
 */
class Breadcrumbs extends MustacheComponent {

  /**
   * @param array $params
   * @property array $crumbs custom breadcrumbs array
   * @property array $homeCrumb custom home breadcrumb item
   * @property string $separator separator inserted between crumbs
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver
      ->define('separator')
      ->allowedTypes('string')
      ->default('>');

    $resolver
      ->define('crumbs')
      ->allowedTypes('array[]')
      ->allowedValues(static function(array &$elements): bool {
        $subResolver = new OptionsResolver;
        self::configureCrumbTypeOptions($subResolver);
        $elements = array_map([$subResolver, 'resolve'], $elements);
        return true;
      });

    $resolver
      ->define('homeCrumb')
      ->default(function (OptionsResolver $resolver) {
        self::configureCrumbTypeOptions($resolver);
        $resolver->setDefaults([
          'title' => __('Home', 'wpce-Breadcrumbs'),
          'url' => home_url(),
        ]);
      });
  }

  protected function parseProps(array $props) {
    if (!$this->crumbs) {
      $crumbs = [];
      $currentItem = self::getCurrentItemCrumb();
      $crumbs[] = $currentItem;
      $previousCrumbs = self::getPreviousCrumbsArray($currentItem);

      if (count($previousCrumbs) > 0) {
        $crumbs = array_merge($previousCrumbs, $crumbs);
      }

      $this->crumbs = $crumbs;
    }
  }

  /**
   * Configures crumb type options
   *
   * @param OptionsResolver $resolver
   * @return void
   */
  static function configureCrumbTypeOptions(OptionsResolver $resolver) {
    $resolver
      ->define('title')
      ->required()
      ->allowedTypes('string');

    $resolver
      ->define('url')
      ->default(null)
      ->allowedTypes('string');
  }

  /**
   * Get currently displayed page's crumb
   *
   * @return array
   */
  public static function getCurrentItemCrumb(): array {
    $title = null;
    $type = null;
    $meta = [];

		if (is_search()) {
			$title = __('Search', 'wpce-Breadcrumbs');
      $type = 'search';

		} else if (is_author()) {
      $user = get_queried_object();

			$title = $user->display_name;
      $type = 'author';
      $meta['userId'] = $user->ID;

		} else if (is_home()) {
      $postType = 'post';
      $postTypeObject = get_post_type_object($postType);

      $title = (isset($postTypeObject->labels->archive_name)) ? $postTypeObject->labels->archive_name : $postTypeObject->labels->name;
      $type = 'archive';
			$meta['postType'] = get_post_type();

		} else if (is_post_type_archive()) {
      $postType = Utils::getQueriedPostType();
      $postTypeObject = get_post_type_object($postType);

      $title = (isset($postTypeObject->labels->archive_name)) ? $postTypeObject->labels->archive_name : $postTypeObject->labels->name;
      $type = 'archive';
			$meta['postType'] = get_post_type();

		} else if (is_archive()) {
      $term = get_queried_object();

			$title = single_term_title('', false);
      $type = 'taxonomy';
			$meta['taxonomy'] = $term->taxonomy;
			$meta['termId'] = $term->term_id;

		} else if (is_page()) {
      $title = get_the_title();
			$type = 'page';
      $meta['pageId'] = get_the_ID();

    } else if (is_single()) {
      $title = get_the_title();
			$type = 'single';
			$meta['postId'] = get_the_ID();
			$meta['postType'] = get_post_type();
    }

    return [
      'title' => Text::trimByWordsCount($title, 5),
      'type' => $type,
      'meta' => $meta,
    ];
  }

  /**
   * Gets crumbs parent to currently displayed one
   *
   * @param array $currentItem
   * @return array array of crumbs
   */
  public static function getPreviousCrumbsArray($currentItem): array {
    $crumbs = [];

    if ($currentItem['type'] === 'page') {
      $ancestorIds = get_post_ancestors($currentItem['meta']['pageId']);
      foreach ($ancestorIds as $ancestorId) {
        array_unshift($crumbs, [
          'title' => get_the_title($ancestorId),
          'url' => get_permalink($ancestorId),
        ]);
      }

    } else if ($currentItem['type'] === 'taxonomy') {
      $taxonomyObject = get_taxonomy($currentItem['meta']['taxonomy']);
      $postType = $taxonomyObject->object_type[0];

      $crumbs[] = [
        'title' => $taxonomyObject->labels->archive_name ?? $taxonomyObject->labels->name,
        'url' => get_post_type_archive_link($postType),
      ];

    } else if ($currentItem['type'] === 'single') {
      $postTypeObject = get_post_type_object($currentItem['meta']['postType']);
      array_unshift($crumbs, [
        'title' => $postTypeObject->labels->archive_name ?? $postTypeObject->labels->name,
        'url' => get_post_type_archive_link($currentItem['meta']['postType']),
      ]);
    }

    return $crumbs;
  }

}
