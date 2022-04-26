<?php

namespace Wpce\Components\EditorContent;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;

class EditorContent extends MustacheComponent {

  /**
   * @param array $props
   * @property string 'content' HTML content of WP WYSIWYG editor
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'content' => null,
    ]);
  }

  protected function parseProps(array $props) {
    $this->content = apply_filters('the_content', $this->content);
  }

}
