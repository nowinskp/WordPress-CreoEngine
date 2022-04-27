<?php

namespace Wpce\Components\Form\TextareaField;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Form\Abstracts\Field\Field;
use Wpce\Content\Html;

class TextareaField extends Field {

  /**
   * @param array $props
   * @property string 'jshandle' data-jshandle attr value
   * @property string 'label' button label
   * @property boolean 'disabled'
   * @property string 'name'
   * @property string 'placeholder'
   * @property boolean 'readonly'
   * @property boolean 'required'
   * @property string 'type'
   * @property string 'value'
   * @property array 'fieldAttibutes'
   * any additional attributes allowed for the field,
   * written as `'attributeName' => attributeValue` pairs.
   *
   * @return string
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {
    parent::configureProps($resolver, $props);
    $resolver->setDefaults([
      'disabled' => false,
      'fieldAttibutes' => [],
      'name' => null,
      'placeholder' => null,
      'readonly' => false,
      'required' => false,
      'value' => '',
    ]);
  }

  protected function parseProps(array $props) {
    parent::parseProps($props);
    $this->fieldHtml = Html::getElement('textarea', array_merge([
      'disabled' => $this->disabled,
      'name' => $this->name,
      'id' => $this->label ? $this->name : null,
      'placeholder' => $this->placeholder,
      'readonly' => $this->readonly,
      'required' => $this->required,
    ], $this->fieldAttibutes), $this->value);
  }

}
