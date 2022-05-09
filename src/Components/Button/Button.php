<?php

namespace Wpce\Components\Button;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Utils\Get;
use Wpce\Components\Abstracts\MustacheComponent;

class Button extends MustacheComponent {

  /**
   * @param array $props
   * @property string 'color' button color theme
   * @property string 'display' button display mode
   * @property string 'jshandle' data-jshandle attr value
   * @property string 'iconHtml' icon html
   * @property string 'label' button label
   * @property string 'sublabel' button sublabel
   * @property string 'tag' `a` or `button`
   * @property string 'target' target
   * @property string 'type' button type
   * @property string 'url' href value
   *
   * @return void
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'color' => null,
      'display' => null,
      'iconHtml' => null,
      'jshandle' => null,
      'label' => null,
      'sublabel' => null,
      'tag' => 'a',
      'target' => null,
      'type' => null,
      'url' => null,
    ]);

    $resolver->setAllowedValues('tag', ['a', 'button']);
    $resolver->setAllowedValues('target', [null, '_blank', '_self', '_parent', '_top']);
    $resolver->setAllowedValues('type', [null, 'button', 'submit', 'reset']);
  }

  protected function parseProps(array $props) {
    $this->addToRootClassesIf($this->color, 'color-'.$this->color);
    $this->addToRootClassesIf($this->display, 'display-'.$this->display);
    $this->addToRootClassesIf($this->iconHtml, 'hasIcon');

    $this->hasLabels = $this->label || $this->sublabel;
    $this->href = Get::stringIf($this->url, 'href="'.$this->url.'"');
    $this->jshandle = Get::stringIf($this->jshandle, 'data-jshandle="'.$this->jshandle.'"');
    $this->target = Get::stringIf($this->target, 'target="'.$this->target.'"');
    $this->type = Get::stringIf($this->type, 'type="'.$this->type.'"');
  }

}
