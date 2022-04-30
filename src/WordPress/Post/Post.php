<?php

namespace Wpce\WordPress\Post;

use Wpce\Content\Html;
use Wpce\Utils\Get;

class Post {

  function __construct($postToLoad = null) {
    if ($postToLoad === null) {
      global $post;
      $postToLoad = $post;
    }

    if (is_int($postToLoad)) {
      $this->post = get_post($postToLoad);
    } else if (is_a($postToLoad, '\WP_Post')) {
      $this->post = $postToLoad;
    } else {
      throw new \Exception('Invalid param used in constructor for '.get_called_class());
    }

    if (!$this->post || !$this->post->ID) {
      throw new \Exception('Post was deleted or is a valid WP_post object.');
    }

    if ($this->getValidPostType() && $this->post->post_type !== $this->getValidPostType()) {
      throw new \Exception('Invalid post type used in constructor for '.get_called_class());
    }

    $this->loadMeta();
  }


  // *************************************
  // Constructor auto-executed methods
  // *************************************

  /**
   * Loads post meta to class properties on object init
   *
   * @return void
   */
  protected function loadMeta() {}

  /**
   * Returns valid post type that the class is meant to handle
   * If null is returned, it means the class can handle any post type.
   *
   * @return null|string post type
   */
  protected function getValidPostType() {
    return null;
  }


  // *************************************
  // Meta
  // *************************************

  /**
   * Wrapper for get_post_meta - returns post type meta key value w/ default value.
   *
   * @param string $metaKey optional. The meta key to retrieve. By default,
   *  returns data for all keys. Default empty.
   * @param mixed $default default value to return if no meta record is found.
   * @param bool $single optional. Whether to return a single value.
   * This parameter has no effect if `$key` is not specified. Default true.
   *
   * @return mixed an array of values if `$single` is false. The value of the meta
   * field if `$single` is true. False for an invalid `$post_id` (non-numeric, zero,
   * or negative value).
   */
  public function getPostMeta(string $metaKey, $default = null, $single = true) {
    $metaValue = get_post_meta($this->getId(), $metaKey, $single);
    return Get::notEmpty($metaValue, $default);
  }

  /**
   * Wrapper for update_post_meta - updates post meta field.
   *
   * @param string $metaKey metadata key
   * @param mixed $metaValue metadata value. Must be serializable if non-scalar.
   * @param mixed $prevValue optional. Previous value to check before updating.
   * If specified, only update existing metadata entries with this value. Otherwise,
   * update all entries. Default empty.
   *
   * @return int|bool meta ID if the key didn't exist, true on successful update,
   * false on failure or if the value passed to the function is the same as the one
   * that is already in the database.
   */
  public function updatePostMeta(string $metaKey, $metaValue, $prevValue = '') {
    return update_post_meta($this->getId(), $metaKey, $metaValue, $prevValue);
  }


  // *************************************
  // Terms & taxonomies
  // *************************************

  /**
   * Get post terms by taxonomy.
   *
   * @param string $taxonomy taxonomy
   * @return WP_Term[]|false|WP_Error array of WP_Term objects on success, false if
   * there are no terms or the post does not exist, WP_Error on failure.
   */
  public function getTerms(string $taxonomy) {
    return get_the_terms($this->getId(), $taxonomy);
  }

  /**
   * Returns first term for a given taxonomy
   *
   * @param string $taxonomy
   * @return WP_Term|null
   */
  public function getFirstTermForTaxonomy(string $taxonomy) {
    $terms = $this->getTerms($taxonomy);
    if ($terms && count($terms) > 0) {
      return $terms[0];
    }
    return null;
  }


  // *************************************
  // ACF
  // *************************************

  protected $fieldKeysByNames = [];

  /**
   * Gets ACF field key by that field's name.
   * Uses protected $fieldKeysByNames property to match field name with its ACF key.
   *
   * @param string $fieldName
   *
   * @return string ACF field key, eg. field_3f9379a7890f1
   */
  protected function getAcfFieldKeyByFieldName(string $fieldName): string {
    if (isset($this->fieldKeysByNames[$fieldName])) {
      return $this->fieldKeysByNames[$fieldName];
    } else {
      throw new \Exception('Invalid ACF field name: "'.$fieldName.'".');
    }
  }

  /**
   * Gets ACF field value by that field's name.
   * Uses protected $fieldKeysByNames property to match field name with its ACF key.
   *
   * @param string $fieldName
   *
   * @return mixed
   */
  protected function getAcfFieldByFieldName(string $fieldName) {
    return get_field($this->getAcfFieldKeyByFieldName($fieldName), $this->getId());
  }

  /**
   * Updates ACF field of a given name with new value.
   * Uses protected $fieldKeysByNames property to match field name with its ACF key.
   *
   * @param string $fieldName
   * @param mixed $fieldValue
   *
   * @return boolean true if updated, false if not (or same content)
   */
  protected function updateAcfFieldByFieldName(string $fieldName, $fieldValue) {
    return update_field(
      $this->getAcfFieldKeyByFieldName($fieldName),
      $fieldValue,
      $this->getId()
    );
  }


  // *************************************
  // Ancestors
  // *************************************

  /**
   * Returns top level parent post id or false if there are no parents
   *
   * @return int|false
   */
  public function getTopLevelParentId() {
    return end(get_post_ancestors($this->getId()));
  }


  // *************************************
  // Default post data getters
  // *************************************

  /**
   * Returns post ID
   *
   * @return int
   */
  public function getId(): int {
    return $this->post->ID;
  }

  /**
   * Wrapper for has_post_thumbnail
   *
   * @return boolean
   */
  public function hasFeaturedImage(): bool {
    return has_post_thumbnail($this->getId());
  }

  /**
   * Wrapper for get_the_post_thumbnail_url.
   * Returns null if there's no image set.
   *
   * @param string $size
   * @return string|null
   */
  public function getFeaturedImageUrl(string $size): ?string {
    return has_post_thumbnail($this->getId()) ? get_the_post_thumbnail_url($this->getId(), $size) : null;
  }

	/**
	 * Returning style's background-image attr, in a format:
	 * `"background-image: url('{imageUrl');"`
   *
   * @param string $size image size
   * @return string
   */
  public function getFeaturedImageCssBackgroundStyle(string $size): string {
    return Html::getBackgroundImageAttr($this->getFeaturedImageUrl($size));
  }

  /**
   * Wrapper for get_the_permalink
   */
  public function getPermalink(): string {
    return get_the_permalink($this->getId());
  }

  /**
   * Returns post title
   *
   * @return string
   */
  public function getTitle(): string {
    return apply_filters('the_title', $this->post->post_title);
  }

  /**
   * Returns post excerpt
   *
   * @return string
   */
  public function getExcerpt(): string {
    return $this->post->post_excerpt ? apply_filters('get_the_excerpt', $this->post->post_excerpt) : '';
  }

  /**
   * Returns the date the post was written
   *
   * @return string
   */
  public function getDate($format = 'j F Y') {
    return get_the_date($format, $this->getId());
  }

  /**
   * Returns post publication status
   *
   * @return string
   */
  public function getStatus(): string {
    return $this->post->post_status;
  }

  /**
   * Returns true if post is published
   *
   * @return boolean
   */
  public function isPublished() {
    return $this->getStatus() === 'publish';
  }

  /**
   * Returns raw (unfiltered) post_content content
   *
   * @return void
   */
  public function getRawContent() {
    return trim($this->post->post_content);
  }

  /**
   * Returns filtered post_content content
   *
   * @return void
   */
  public function getContent() {
    return apply_filters('the_content', $this->getRawContent());
  }

}
