<?php

namespace Wpce\Components\Abstracts;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Wpce\Components\Abstracts\DefaultComponent;
use Wpce\Content\Template;

/**
 * An extension of DeafultComponent that provides built-in
 * opinionated Mustache template rendering logic.
 */
abstract class MustacheComponent extends DefaultComponent {

  /**
   * Allows configuration of required props using OptionsResolver.
   *
   * Implemented here as an empty function to allow creation of
   * propless mustache components. Overwrite as needed in child classes.
   *
   * @param array $options
   * @return string|null
   */
  protected function configureProps(OptionsResolver $resolver, array $props) {}

  /**
   * Gets component's HTML code
   *
   * @param array $options
   * @return string|null
   */
  public function getHtml() {
    return Template::renderComponent($this->childRef->getShortName(), $this->getComponentDir(), $this);
  }

}
