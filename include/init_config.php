<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */

if (file_exists(dirname(__FILE__) . "/config-local.php")) {
  include "config-local.php";
  define("ERROR_MAIL", $config["error-mail"]);
  define("SENDER_MAIL", $config["sender-mail"]);
} else {
  $db_host = "foo";
  $db_name = "bar";
  $db_user = "baz";
  $db_password = "foz";
  define("ERROR_MAIL", "itk-hurtigtest@samfundet.no");
  define("SENDER_MAIL", "hurtigtest@samfundet.no");
}
?>
