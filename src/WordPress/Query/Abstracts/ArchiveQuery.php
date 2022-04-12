<?php

namespace Wpce\WordPress\Query\Abstracts;

use \WP_Query;
use Wpce\WordPress\Post\Abstracts\DefaultType;

abstract class ArchiveQuery {

  protected $postType;
  protected $posts = [];

  /**
   * Returns post type handled by the class
   *
   * @return string
   */
  abstract protected function getPostType(): string;

  /**
   * Wraps every post object with a DefaultType compatible class
   *
   * @param int|WP_Post $post WordPress post object or post id
   * @return DefaultType
   */
  abstract protected function wrapPost($post): DefaultType;

  /**
   * Initialize object and assign post type
   *
   * @param [type] $postType
   */
  public function __construct() {
    $this->postType = $this->getPostType();
  }

  /**
   * Wrapper for WP_Query call that automatically wraps and assigns found
   * posts to $post class property. After that, it resets global post data.
   *
   * @param array $wpQueryParams WP_Query params (excluding `post_type`)
   *
   * @return void
   */
  public function runQuery(array $wpQueryParams) {
    $query = new WP_Query(array_merge(
      $wpQueryParams,
      [
        'post_type' => $this->getPostType(),
      ],
    ));

    foreach ($query->posts as $postObject) {
      $this->posts[] = $this->wrapPost($postObject);
    }

    wp_reset_postdata();
  }

  /**
  * Runs default query for published posts.
  *
  * @param int $limit - number of posts to return
  * @param array $additionalParams - additional WP_Query params to be merged with base query
  *
  * @return void
  */
  public function queryPosts(int $limit, array $additionalParams = []) {
    $query = [
      'posts_per_page' => $limit,
      'post_status' => 'publish',
    ];

    $this->runQuery(array_merge($query, $additionalParams));
  }

  /**
  * Runs default query for published posts, filtered by taxonomy terms
  *
  * @param array $taxTermPairs - associative array of `taxonomy => term slug` pairs, eg.
  * [ 'sector' => 'research' ]. Term slugs can also be passed as array.
  * @param int $limit - number of posts to return
  * @param array $additionalParams - additional WP_Query params to be merged with base query
  *
  * @return void
  */
  public function queryPostsByTaxonomyTermPairs(array $taxTermPairs, int $limit, array $additionalParams = []) {
    $taxonomyParams = [
      'tax_query' => [
        'relation' => 'AND',
      ],
    ];

    foreach ($taxTermPairs as $taxonomy => $term) {
      $taxonomyParams['tax_query'][] = [
        'taxonomy' => $taxonomy,
        'field'    => 'slug',
        'terms'    => $term,
      ];
    }

    $this->queryPosts($limit, array_merge($taxonomyParams, $additionalParams));
  }

  /**
   * Returns number of found posts
   *
   * @return int
   */
  public function getPostsCount(): int {
    return count($this->posts);
  }

  /**
   * Returns information if current query has any posts
   *
   * @return boolean
   */
  public function hasPosts() {
    return $this->getPostsCount() > 0;
  }

  /**
   * Returns found posts
   *
   * @return DefaultType[]
   */
  public function getPosts(): array {
    return $this->posts;
  }

}
