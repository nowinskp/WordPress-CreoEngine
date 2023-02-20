<?php

namespace Wpce\WordPress\Acf\Abstracts;

use Wpce\Utils\Get;

abstract class AcfOptions {

  /**
   * Array used to store (cache) option fields data
   *
   * @var array
   */
  protected static $fieldDataArray = [];

  /**
   * Gets data from the ACF field and saves it under static property
   * to cache its value for later use.
   *
   * @return void
   */
  protected static function getFieldData() {
    $fieldName = static::getFieldName();
    if (!isset(self::$fieldDataArray[$fieldName])) {
      self::$fieldDataArray[$fieldName] = get_field(static::getFieldName(), static::getOptionsId());
    }

    return Get::in(self::$fieldDataArray, $fieldName, []);
  }

  /**
   * Returns name of the meta field that contains given set
   * of saved options.
   *
   * @return string
   */
  abstract static function getFieldName(): string;


  /**
   * Returns ACF options page post_id to be used to retrieve
   * option field values.
   *
   * @return string
   */
  static function getOptionsId(): string {
    if (defined('WPCE_ACF_OPTIONS_KEY')) {
      return constant('WPCE_ACF_OPTIONS_KEY');
    }
    throw new \Exception('Missing WPCE_ACF_OPTIONS_KEY constant definition.');
  }

}
