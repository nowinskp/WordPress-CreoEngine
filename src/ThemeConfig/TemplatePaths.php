<?php

namespace Wpce\ThemeConfig;

/**
 * Changes WordPress default template files paths so that template files can be resolved from different folder.
 *
 * References:
 * https://stackoverflow.com/questions/44261944/wordpress-template-files-in-subdirectorys
 * https://www.reddit.com/r/Wordpress/comments/ffhjvw/moving_wordpress_theme_template_files_to/
 *
 * Related posts with other solutions:
 * https://stackoverflow.com/questions/60589503/moving-wordpress-theme-template-files-to-subdirectory
 * https://wordpress.stackexchange.com/questions/17385/custom-post-type-templates-from-plugin-folder
 * https://wordpress.stackexchange.com/questions/291725/store-page-template-files-in-a-subfolder
 * https://wordpress.stackexchange.com/questions/312159/how-to-move-page-template-files-like-page-slug-php-to-a-sub-directory/312611#312611
 *
 * Template hierarchy info:
 * https://developer.wordpress.org/reference/hooks/template_hierarchy/
 * https://core.trac.wordpress.org/browser/tags/5.8.1/src/wp-includes/template.php
 * https://developer.wordpress.org/themes/basics/organizing-theme-files/#page-templates-folder
 */
class TemplatePaths {

  private $templatesDirectory;

  /**
   * On init, sets directory that will store a given subset of template files
   *
   * @param string $templatesDirectory - path to directory with template files
   * @return  void
   */
  public function __construct($templatesDirectory = 'templates') {
    $this->templatesDirectory = $templatesDirectory;
  }

  /**
   * Used in an add_filter call to change template paths for a given tempate hierarchy
   *
   * @param array $templates A list of candidates template files.
   * @return string Full path to template file.
   */
  public function changeTemplatePath($templates) {
    // don't use the custom template directory in unexpected cases
    if (empty($templates) || !is_array($templates)) {
      return $templates;
    }

    $pageTemplateId = 0;
    $templatesCount = count($templates);
    if ($templates[0] === get_page_template_slug()) {
      // if there is a custom template, then our page-{slug}.php template is at the next index
      $pageTemplateId = 1;
    }

    /**
     * the last one in $templates is page.php, single.php, or archives.php depending
     * on the type of template hierarchy being read. Paths of all items starting
     * from $pageTemplateId will get updated.
     */
    for ($i = $pageTemplateId; $i < $templatesCount ; $i++) {
      $templates[$i] = $this->templatesDirectory.'/'.$templates[$i];
    }

    return $templates;
  }


  /**
   * Add filters to override the path for given set of WordPRess template hierarchies.
   * By default, overwrites paths for all available hierarchies.
   *
   * If you override the index hierarchy, be sure to add an index.php template in used
   * custom template folder.
   *
   * @param array $templateHierarchies - array of hierarchies to overwrite
   * @return void
   */
  public function changePathsForTemplates($templateHierarchies = [
    '404',
    'archive',
    'attachment',
    'author',
    'category',
    'date',
    'embed',
    'frontpage',
    'home',
    'index',
    'page',
    'paged',
    'privacypolicy',
    'search',
    'single',
    'singular',
    'tag',
    'taxonomy',
    'type',
  ]) {
    foreach ($templateHierarchies as $templateHierarchyType) {
      add_filter($templateHierarchyType.'_template_hierarchy', [$this, 'changeTemplatePath']);
    }
  }
}
