<?php

namespace Wpce\Utils;

class Get {

  /**
   * Returns a variable if it's not empty.
   * Otherwise, returns default value.
   *
   * @param mixed $variable variable to check
   * @param mixed $defaultValue value to return if variable is not set
   *
   * @return mixed
   */
	static function notEmpty($variable, $defaultValue = null) {
    return !empty($variable) ? $variable : $defaultValue;
  }

  /**
   * Get the key value from the end of a given path.
   * If it's not available, returns default value.
   *
   * @param array $array array to go through
   * @param array|string $path path to target key
   * @param mixed $defaultValue value to return if unable to go through path
   *
   * @return mixed
   */
	static function in($array, $path, $defaultValue = null) {
		if (is_string($path)) {
      return isset($array[$path]) ? $array[$path] : $defaultValue;
    }
    if (is_array($path)) {
      $currentChild = $array;
      foreach ($path as $pathStep) {
        if (isset($currentChild[$pathStep])) {
          $currentChild = $currentChild[$pathStep];
        } else {
          return $defaultValue;
        }
      }
      return $currentChild;
    }
  }

  /**
   * Returns set string if requirement is met.
   * Otherwise returns default value.
   *
   * @param boolean $requirement condition to check
   * @param string $outputString string to return if condition is met
   * @param string $defaultValue string to return if condition is not met
   *
   * @return string
   */
  static function stringIf($requirement, $outputString, $defaultValue = '') {
    $isConditionMet = $requirement == false ? false : isset($requirement);
    return $isConditionMet ? $outputString : $defaultValue;
  }

}
