<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * A collection of useful functions.
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */

use PHPMailer\PHPMailer\PHPMailer;
require_once "libphp-phpmailer/autoload.php";
require_once "init_config.php";

/**
 * Converts ISO-8601 numeric representation of the day of the week
 * to the corresponding Norwegian name.
 *
 * 1 (for Monday) through 7 (for Sunday)
 *
 * @author maghal (Magne "Peach" Halvorsen)
 *
 * @param int $day ISO-8601 numeric representation of the day of the week.
 *
 * @return string Name of the day of the week.
 */
function weekdayToNorwegian(int $day): string
{
  switch ($day) {
    case 1:
      return "mandag";
    case 2:
      return "tirsdag";
    case 3:
      return "onsdag";
    case 4:
      return "torsdag";
    case 5:
      return "fredag";
    case 6:
      return "lørdag";
    case 7:
      return "søndag";
  }
}

/**
 * Converts month number to the corresponding Norwegian name.
 *
 * @author maghal (Magne "Peach" Halvorsen)
 *
 * @param int $month Number of the month
 *
 * @return string Norwegian name of the month
 */
function monthToNorwegian(int $month): string
{
  switch ($month) {
    case 1:
      return "januar";
    case 2:
      return "februar";
    case 3:
      return "mars";
    case 4:
      return "april";
    case 5:
      return "mai";
    case 6:
      return "juni";
    case 7:
      return "juli";
    case 8:
      return "august";
    case 9:
      return "september";
    case 10:
      return "oktober";
    case 11:
      return "november";
    case 12:
      return "desember";
  }
}

/**
 * Sets up an PHPMailer object with some default configuration.
 *
 * @TODO Use smtp to send
 *
 * @author maghal (Magne "Peach" Halvorsen)
 *
 * @return \PHPMailer\PHPMailer\PHPMailer [TODO:description]
 */
function setupMail(): PHPMailer
{
  // Parameter true to enable usage of exceptions
  $mail = new PHPMailer(true);
  // UTF-8 encoding
  $mail->CharSet = PHPMailer::CHARSET_UTF8;

  // TODO: Sette denne til riktig sender
  $mail->setFrom(SENDER_MAIL, "Hurtigtest");

  return $mail;
}

?>
