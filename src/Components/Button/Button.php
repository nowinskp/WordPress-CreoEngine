<?php

namespace Wpce\Components\Button;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Utils\Get;
use Wpce\Components\Abstracts\MustacheComponent;

class Button extends MustacheComponent {

  /**
   * @param array $props
   * @property string 'class' additional CSS classes
   * @property string 'color' button color theme
   * @property string 'iconHtml' icon html
   * @property string 'label' button label
   * @property string 'sublabel' button sublabel
   * @property string 'target' target
   * @property string 'url' href value
   *
   * @return string
   */
  public function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'class' => null,
      'color' => null,
      'iconHtml' => null,
      'label' => null,
      'sublabel' => null,
      'target' => null,
      'url' => null,
    ]);
  }

  public function parseProps(array $props) {
    $this->addToRootClasses($this->class, false);
    $this->target = Get::stringIf($this->target, 'target="'.$this->target.'"');
    $this->colorClass = $this->addToRootClassesIf($this->color, 'color-'.$this->color);
    $this->iconClass = $this->addToRootClassesIf($this->iconHtml, 'hasIcon');
  }

}
