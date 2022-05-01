<?php

namespace Wpce\Components\Link;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\Utils\Get;

class Link extends MustacheComponent {

  /**
   * @param array $props
   * @property string 'label' button label
   * @property string 'target' target
   * @property string 'url' href value
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'target' => null,
    ]);
    $resolver->setRequired(['label', 'url']);
    $resolver->setAllowedTypes('label', 'string');
    $resolver->setAllowedValues('target', [null, '_blank', '_self', '_parent', '_top']);
    $resolver->setAllowedTypes('url', 'string');
  }

  protected function parseProps(array $props) {
    $this->target = Get::stringIf($this->target, 'target="'.$this->target.'"');
  }

}
