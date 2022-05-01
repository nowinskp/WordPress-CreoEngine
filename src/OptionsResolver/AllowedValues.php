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
        throw new \Error('Used type is not of a string or a Wpce\Components\Abstracts\DefaultComponent type');
        return false;
      }
    }
    return true;
  }

}
