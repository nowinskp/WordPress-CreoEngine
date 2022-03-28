<?php

namespace Wpce\Components\Abstracts;

abstract class StaticComponent {

  /**
   * Echoes component's HTML code.
   *
   * @param array $props
   * @return void
   */
  static function renderHtml(array $props) {
    echo static::getHtml($props);
  }

  /**
    * Gets component's HTML code
    *
    * @param array $props
    * @return string|null
    */
  abstract static function getHtml(array $props);
}
