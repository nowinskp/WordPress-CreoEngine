<?php

namespace Wpce\WordPress\Utils;

use Wpce\Logging\LogFile;

/**
 * Logs messages to a set log file.
 * Adds WP-related info to logged messages.
 */
class WordPressLogFile extends LogFile {

  /**
   * Writes message to the log file
   *
   * @param string $messsage log message
   */
  public function writeLog(string $message) {
    global $userLogin;

    if (!is_resource($this->logFilePointer)) {
      $this->openLogFile();
    }

    $scriptName = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);

    // define current time and suppress E_WARNING if using the system TZ settings
    // (don't forget to set the INI setting date.timezone)
    $time = @wp_date($this->timeFormat);

    wp_get_current_user();
    if ($userLogin) {
      $user = '['.$userLogin.']';
    } else {
      $user = '[unknown]';
    }

    // write current time, script name and message to the log file
    fwrite($this->logFilePointer, "$time ($scriptName) $user $message" . PHP_EOL);
  }

}
