<?php

namespace Wpce\WordPress\ThemeConfig;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class for disabling WordPress classic and/or Gutenberg editor
 * for given post ids and/or used templates.
 *
 * Set desired options in constructor then run setFilters to
 * automatically set required WordPress filters.
 */
class DisableEditor {

  protected $ids = [];
  protected $templates = [];

  /**
   * @param array $options
   * An array that may contain list of ids and templates for which
   * the editor should be disabled. It may contain the following keys:
   * `ids` - an array of post ids
   * `templates` - an array of template file names, eg. `page-template.php`
   * `frontPage` - if to disable editor for front page post edit screen
   */
  public function __construct(array $options) {
    $optionsResolver = new OptionsResolver;
    $optionsResolver->setDefaults([
      'ids' => [],
      'templates' => [],
      'frontPage' => false,
    ]);
    $options = $optionsResolver->resolve($options);

    $this->ids = $options['ids'];
    $this->templates = $options['templates'];
    $this->frontPage = $options['frontPage'];
  }

  /**
   * Checks if editor shall be disabled for a given post id
   *
   * @param integer $id
   * @return boolean
   */
  protected function isEditorDisabledForPostId(int $id): bool {
    if (empty($id)){
      return false;
    }

    if ($this->frontPage && $id === intval(get_option('page_on_front'))) {
      return true;
    }

    $template = get_page_template_slug($id);

    return in_array($id, $this->ids) || in_array($template, $this->templates);
  }

  /**
   * Handles disable action for Gutenberg editor
   */
  public function disableGutenbergEditorFilter($can_edit) {
    $postId = isset($_GET['post']) ? $_GET['post'] : null;

    if(!(is_admin() && !empty($postId))) {
      return $can_edit;
    }

    if ($this->isEditorDisabledForPostId($postId) ) {
      $can_edit = false;
    }

    return $can_edit;
  }

  /**
   * Handles disable action for classic editor
   */
  public function disableClassicEditorAction() {
    $currentScreen = get_current_screen();
    $postId = isset($_GET['post']) ? $_GET['post'] : null;
    $postType = $currentScreen->id;

    if (!$postId) {
      return;
    }

    if ($this->isEditorDisabledForPostId($postId)) {
      remove_post_type_support($postType, 'editor');
    }
  }

  /**
   * Sets filters to run on proper hooks
   *
   * @return void
   */
  public function setFilters() {
    add_filter('gutenberg_can_edit_post_type', [$this, 'disableGutenbergEditorFilter'], 10, 1);
    add_filter('use_block_editor_for_post_type', [$this, 'disableGutenbergEditorFilter'], 10, 1);
    add_action('admin_head', [$this, 'disableClassicEditorAction']);
  }
}
