<?php

namespace Wpce\OptionsResolver;

use Error;

/**
 * Helpers for setAllowedTypes OptionsResolver method.
 *
 * @example Code example usage
 * ```
 * $resolver->setAllowedTypes(
 *   'entries', AllowedTypes::nullOrComponentOrIntOrString()
 * )
 * ```
 */
class AllowedTypes {

  static function nullOrComponentOrIntOrString() {
    return [
      'null',
      'Wpce\Components\Abstracts\DefaultComponent',
      'int',
      'string',
    ];
  }

}
