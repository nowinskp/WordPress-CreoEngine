<?php

namespace Wpce\Logging;

/**
 * Logs messages to a set log file.
 */
class LogFile {
  protected $logFile;
  protected $logFilePointer;
  protected $timeFormat = '[Y-m-d@H:i:s]';

  /**
   * Sets path to log file on instance creation
   *
   * @param string $filepath path to log file (inc. filename)
   * @return void
   */
  function __construct(string $filepath) {
    $this->logFile = $filepath;
  }


  /**
   * Sets time format for log entries
   *
   * @param string $format wp_date compatible time format
   * @return void
   */
  public function setTimeFormat(string $format) {
    $this->timeFormat = $format;
  }


  /**
   * Writes message to the log file
   *
   * @param string $messsage log message
   */
  public function writeLog(string $message) {
    if (!is_resource($this->logFilePointer)) {
      $this->openLogFile();
    }

    $scriptName = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);

    // define current time and suppress E_WARNING if using the system TZ settings
    // (don't forget to set the INI setting date.timezone)
    $time = @date($this->timeFormat);

    // write current time, script name and message to the log file
    fwrite($this->logFilePointer, "$time ($scriptName) $message" . PHP_EOL);
  }

  /**
   * Closes log file
   *
   * @return void
   */
  public function closeLogFile() {
    fclose($this->logFilePointer);
  }

  /**
   * Opens log file
   * Sets fallback if
   *
   * @return void
   */
  protected function openLogFile() {
    // in case of Windows set default log file
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $logFileFallback = 'c:/php/logfile.txt';
    }
    // set default log file for Linux and other systems
    else {
      $logFileFallback = '/tmp/logfile.txt';
    }

    // define log file from setLogFile method or use previously set default fallback
    $logFile = $this->logFile ?: $logFileFallback;

    // open log file for writing only and place file pointer at the end of the file
    // (if the file does not exist, try to create it)
    $this->logFilePointer = fopen($logFile, 'a') or exit("Can't open $logFile!");
  }
}
