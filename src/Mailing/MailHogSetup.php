<?php

namespace Wpce\Mailing;

use PHPMailer\PHPMailer\PHPMailer;
use Wpce\WordPress\Utils\Environment;

/*
 * Add the SMTP routing to MailHog
 *
 * Requires PHPMailer which, at the time of writing, is natively used by WordPress.
 *
 * Usage:
 * just run MailHogSetup::init() in init script
 *
 * To run MailHog locally - assuming it's installed using default setup - just run:
 * ~/go/bin/MailHog
 *
 * MailHog reference:
 * https://github.com/mailhog/MailHog
 */

class MailHogSetup {

  /**
   * Initialize MailHog in local environment only.
   */
  static function init() {
    if (Environment::isLocal()) {
      self::addSMTP();
    }
  }

  private static function addSMTP() {
    add_action('phpmailer_init', 'Wpce\Mailing\MailHogSetup::configEmailSMTP');
  }

  static function configEmailSMTP(PHPMailer $phpmailer) {
    $phpmailer->IsSMTP();
    $phpmailer->Host='127.0.0.1';
    $phpmailer->Port=1025;
    $phpmailer->Username='';
    $phpmailer->Password='';
    $phpmailer->SMTPAuth=true;
  }

}
