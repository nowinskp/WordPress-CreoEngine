<?php

namespace Wpce\Components\Form\Abstracts\Field;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Utils\Get;
use Wpce\Components\Abstracts\MustacheComponent;
use Wpce\Content\Template;

abstract class Field extends MustacheComponent {

  public function getHtml() {
    return Template::renderComponent('Field', __DIR__, $this);
  }

  /**
   * @param array $props
   * @property string 'jshandle' data-jshandle attr value
   * @property string 'label' button label
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    $resolver->setDefaults([
      'jshandle' => null,
      'label' => null,
    ]);
  }

  protected function parseProps(array $props) {
    $this->jshandle = Get::stringIf($this->jshandle, 'data-jshandle="'.$this->jshandle.'"');
    $this->labelRequiredClass = Get::stringIf($this->required, 'class="isRequired"');
    $this->addToRootClassesIf($this->label, 'hasLabel');
  }

}
