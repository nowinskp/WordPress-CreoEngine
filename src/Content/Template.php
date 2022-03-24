<?php

namespace Wpce\Content;

use \Mustache_Engine;

class Template {

  /**
   * Simple wrapper for Mustache's `render` method
   * providing prefilled engine config.
   *
   * @param string $template template content
   * @param array $templateData template data
   * @return string rendered template
   */
	static function render(string $template, array $templateData = []) {
    $engine = new Mustache_Engine([
      'entity_flags' => ENT_QUOTES,
    ]);
    return $engine->render($template, $templateData);
  }

}
