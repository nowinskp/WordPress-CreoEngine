<?php

namespace Wpce\Components\Form\CheckboxField;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Form\Abstracts\Field\Field;
use Wpce\Content\Html;
use Wpce\Content\Template;

class CheckboxField extends Field {

  public function getHtml() {
    return Template::renderComponent('CheckboxField', __DIR__, $this);
  }

  /**
   * @param array $props
   * @property string 'jshandle' data-jshandle attr value
   * @property boolean 'isChecked'
   * @property string 'label'
   * @property boolean 'disabled'
   * @property string 'name'
   * @property string 'placeholder'
   * @property boolean 'readonly'
   * @property boolean 'required'
   * @property array 'fieldAttibutes'
   * any additional attributes allowed for the field,
   * written as `'attributeName' => attributeValue` pairs.
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    parent::configureProps($resolver, $props);
    $resolver->setDefaults([
      'description' => null,
      'disabled' => false,
      'fieldAttibutes' => [],
      'isChecked' => false,
      'name' => null,
      'readonly' => false,
      'required' => false,
      'type' => 'checkbox',
    ]);
  }

  protected function parseProps(array $props) {
    parent::parseProps($props);
    $this->fieldHtml = Html::getElement('input', array_merge([
      'disabled' => $this->disabled,
      'name' => $this->name,
      'id' => $this->label ? $this->name : null,
      'readonly' => $this->readonly,
      'required' => $this->required,
      'type' => 'checkbox',
      'checked' => $this->isChecked,
    ], $this->fieldAttibutes));
  }

}
