<?php

namespace Wpce\WordPress\Post\Abstracts;

use Wpce\Content\Html;
use Wpce\Utils\Get;

abstract class DefaultType {

  function __construct($post) {
    if (is_int($post)) {
      $this->post = get_post($post);
    } else if (is_a($post, '\WP_Post')) {
      $this->post = $post;
    } else {
      throw new \Exception('Invalid param used in constructor for '.get_called_class());
    }

    if (!$this->post || !$this->post->ID || $this->post->post_type !== $this->getValidPostType()) {
      throw new \Exception('Invalid post type used in constructor for '.get_called_class());
    }

    $this->loadMeta();
  }


  // *************************************
  // Core methods meant to be overridden
  // *************************************

  /**
   * Loads post meta to class properties on object init
   *
   * @return void
   */
  protected function loadMeta() {}

  /**
   * Returns valid post type that the class is meant to handle
   *
   * @return string post type
   */
  abstract protected function getValidPostType(): string;


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
  // Default post data getters
  // *************************************

  public function getId() {
    return $this->post->ID;
  }

  public function hasFeaturedImage() {
    return has_post_thumbnail($this->getId());
  }

  public function getFeaturedImageUrl(string $size) {
    return has_post_thumbnail($this->getId()) ? get_the_post_thumbnail_url($this->getId(), $size) : null;
  }

  public function getFeaturedImageCssBackgroundStyle(string $size) {
    return Html::getBackgroundImageAttr($this->getFeaturedImageUrl($size));
  }

  public function getPermalink() {
    return get_the_permalink($this->getId());
  }

  public function getTitle() {
    return $this->post->post_title;
  }

  public function getStatus() {
    return $this->post->post_status;
  }

  public function isPublished() {
    return $this->getStatus() === 'publish';
  }

  public function getRawContent() {
    return trim($this->post->post_content);
  }

}
