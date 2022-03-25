<?php

namespace Wpce\Components\Abstracts;

abstract class StaticComponent {

  /**
   * Echoes component's HTML code.
   *
   * @param array $options
   * @return void
   */
  static function renderHtml($options = []) {
    echo static::getHtml($options);
  }

  /**
    * Gets component's HTML code
    *
    * @param array $options
    * @return string|null
    */
  abstract static function getHtml($options = []);
}
