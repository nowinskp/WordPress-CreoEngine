<?php

namespace Wpce\Content;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class Template {

  /**
   * Simple wrapper for Mustache's `render` method providing prefilled
   * engine config
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

  /**
   * Wrapper for Mustache's `render` method providing
   * prefilled engine config suitable for rendering class
   * components that utilizes the following structure:
   * /Component
   *  |- /partials
   *  |   |- partial1.mustache
   *  |   |- partial2.mustache
   *  |- Component.php
   *  |- Component.mustache
   * @param string $componentName name of the component
   * @param string $componentDir directory that consist of the component
   * @return string rendered template
   */
	static function renderComponent($componentName, $componentDir, $templateData = []) {
    $engineOptions = [
      'entity_flags' => ENT_QUOTES,
      'loader' => new Mustache_Loader_FilesystemLoader($componentDir),
    ];
    $partialsDir = $componentDir.'/partials';
    if (is_dir($partialsDir)) {
      $engineOptions['partials_loader'] = new Mustache_Loader_FilesystemLoader($partialsDir);
    }
    $engine = new Mustache_Engine($engineOptions);
    return $engine->render($componentName, $templateData);
  }

}
