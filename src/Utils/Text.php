<?php

namespace Wpce\Utils;

class Text {

  /**
   * Trims string by characters count
   *
   * @param string $string string to trim
   * @param integer $maxLength max length of the resulted string
   * @param string $end what to end string with if had to be trimmed
   *
   * @return void
   */
  static function trimByCharactersCount(string $string, int $maxLength = 30, string $end = '[...]') {
    $string = strip_tags($string);
    $string = trim($string);
    $output = mb_substr($string, 0, $maxLength);
    $length = strlen($string);
    if ($length > $maxLength) {
      $output .= $end;
    }
    return $output;
  }

  /**
   * Trims string by words count
   *
   * @param string $string string to trim
   * @param integer $maxLength max amount of words in the resulted string
   * @param string $end what to end string with if had to be trimmed
   *
   * @return void
   */
  static function trimByWordsCount(string $string, int $maxWordsCount = 5, string $end = '[...]') {
    $string = strip_tags($string);
    $string = trim($string);
    $stringParts = explode(' ', $string);

    $count = count($stringParts);
    if ($count > $maxWordsCount) {
      $stringParts = array_slice($stringParts, 0, $maxWordsCount);
      $stringParts[] = $end;
    }
    return implode(' ', $stringParts);
  }

  /**
   * Generates random string of a given length
   * out of alphanumeric characters
   *
   * @param integer $length
   *
   * @return string
   */
  static function generateRandomString(int $length = 8): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

}
