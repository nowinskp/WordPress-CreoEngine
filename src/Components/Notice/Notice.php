<?php

namespace Wpce\Components\Notice;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\Utils\Get;

class Notice extends MustacheComponent {

  /**
   * @param array $props
   * @property string messageHtml optionally html-formatted message
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'jshandle' => 'notice',
      'messageHtml' => null,
      'type' => null,
    ]);

    $resolver->addAllowedValues('type', [
      null, 'info', 'warning', 'error', 'success'
    ]);
  }

  protected function parseProps(array $props) {
    $this->style = Get::stringIf($this->type === null, 'style="display: none;"');
    $this->jshandle = Get::stringIf($this->jshandle, 'data-jshandle="'.$this->jshandle.'"');
    $this->addToRootClassesIf($this->type, 'type-'.$this->type);

  }

}
