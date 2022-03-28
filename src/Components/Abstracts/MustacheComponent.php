<?php

namespace Wpce\Components\Abstracts;

use Wpce\Components\Abstracts\DefaultComponent;
use Wpce\Content\Template;

/**
 * An extension of DeafultComponent that provides built-in
 * opinionated Mustache template rendering logic.
 */
abstract class MustacheComponent extends DefaultComponent {

  /**
    * Gets component's HTML code
    *
    * @param array $options
    * @return string|null
    */
  public function getHtml() {
    return Template::renderComponent($this->childRef->getShortName(), dirname($this->childRef->getFileName()), $this);
  }

}
