<?php

namespace Wpce\Components\WordPress\Pagination;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;

class Pagination extends MustacheComponent {

  /**
   * @param array $props
   * @property null|WP_Query 'customQuery' custom WP_Query for which pagination should be generated
   * @property array 'paginateLinksAdditionalArgs' paginate_links WP function args to add/overwrite default ones
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'customQuery' => null,
      'paginateLinksAdditionalArgs' => [],
    ]);

    $resolver->setAllowedTypes('customQuery', ['null', 'WP_Query']);
    $resolver->setAllowedTypes('paginateLinksAdditionalArgs', 'array');
  }

  protected function parseProps(array $props) {
    global $wp_query, $wp_rewrite;

    // set pagination values in $wp_query for custom queries
    if ($this->customQuery) {
      $globalsBackup = [
        'wp_query' => @$GLOBALS['wp_query'],
        'max_page' => @$GLOBALS['max_page'],
        'paged' => @$GLOBALS['paged'],
      ];
      $GLOBALS['wp_query'] = $this->customQuery;
      $GLOBALS['max_page'] = $this->customQuery->max_num_pages;
      $GLOBALS['paged'] = max( $this->customQuery->get( 'paged' ), 1 );
    }

    /**
     * This is a simple hack to get base URL from ajax call.
     * When given inexisting ID, base page URL is returned.
     */
    $unreachableId = 999999999;

    $paginationArgs = [
      'base' => str_replace( $unreachableId, '%#%', esc_url(get_pagenum_link($unreachableId))),
      'format' => '?paged=%#%',
      'total' => $wp_query->max_num_pages,
      'current' => max(1, $wp_query->query_vars['paged']),
      'show_all' => false,
      'prev_text' => 'Previous page',
      'next_text' => 'Next page',
      'type' => 'plain',
    ];

    if ($wp_rewrite->using_permalinks()) {
      $paginationArgs['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))).'page/%#%/', 'paged');
    }

    $links = paginate_links(array_merge($paginationArgs, $this->paginateLinksAdditionalArgs));
    $this->showPagination = $links;
    $this->pageLinks = urldecode($links);

    // reset $wp_query to original values
    if ($this->customQuery) {
      foreach ($globalsBackup as $var => $val)
      $GLOBALS[ $var ] = $val;
    }
  }

}
