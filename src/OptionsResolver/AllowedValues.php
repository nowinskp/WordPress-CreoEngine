<?php

namespace Wpce\OptionsResolver;

use Error;

/**
 * Helpers for setAllowedValue(s) OptionsResolver method.
 *
 * @example Code example usage
 * ```
 * $resolver->setAllowedValues('entries', function($value) {
 *    return AllowedValues::arrayOfComponentsOrStrings($value);
 * }))
 * ```
 */
class AllowedValues {

  static function arrayOfComponentsOrStrings($value) {
    foreach ($value as $arrayItem) {
      if (!is_a($arrayItem, 'Wpce\Components\Abstracts\DefaultComponent') && !is_string($arrayItem)) {
        return false;
      }
    }
    return true;
  }

  static function nullOrStringOrArrayOfComponentsOrStrings($value) {
    if (is_array($value)) {
      return self::arrayOfComponentsOrStrings($value);
    }
    return $value === null || is_string($value) || is_a($value, 'Wpce\Components\Abstracts\DefaultComponent');
  }

}
