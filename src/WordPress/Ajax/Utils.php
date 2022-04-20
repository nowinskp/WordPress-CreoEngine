<?php

namespace Wpce\WordPress\Ajax;

class Utils {

  /**
   * Register AJAX function handler
   *
   * @param string $handler unique id
   * @param callable $callback function or method to be called
   * @param boolean $allowForLoggedInUsers
   * @param boolean $allowForLoggedOutUsers
   *
   * @return void
   */
  static function registerAjaxCall(string $handler, callable $callback, $allowForLoggedInUsers = true, $allowForLoggedOutUsers = true) {
    if ($allowForLoggedInUsers) {
      add_action('wp_ajax_'.$handler, $callback);
    }
    if ($allowForLoggedOutUsers) {
      add_action('wp_ajax_nopriv_'.$handler, $callback);
    }
  }

}

