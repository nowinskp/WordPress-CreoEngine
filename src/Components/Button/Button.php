<?php

namespace Wpce\Components\Button;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Utils\Get;
use Wpce\Components\Abstracts\MustacheComponent;

class Button extends MustacheComponent {

  /**
   * @param array $props
   * @property string 'color' button color theme
   * @property string 'iconHtml' icon html
   * @property string 'label' button label
   * @property string 'sublabel' button sublabel
   * @property string 'target' target
   * @property string 'url' href value
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'color' => null,
      'iconHtml' => null,
      'label' => null,
      'sublabel' => null,
      'target' => null,
      'url' => null,
    ]);
  }

  protected function parseProps(array $props) {
    $this->target = Get::stringIf($this->target, 'target="'.$this->target.'"');
    $this->colorClass = $this->addToRootClassesIf($this->color, 'color-'.$this->color);
    $this->iconClass = $this->addToRootClassesIf($this->iconHtml, 'hasIcon');
  }

}
