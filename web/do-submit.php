<?php
require_once "../include/init.php";

use PHPMailer\PHPMailer\PHPMailer;
require_once "libphp-phpmailer/autoload.php";

$arguments["title"] = "Hurtigtest";

// Check that all inputs are set
if (
  !(
    isset($_POST["name"]) &&
    isset($_POST["tlf"]) &&
    isset($_POST["mail"]) &&
    isset($_POST["fødselsnummer"]) &&
    isset($_POST["time"])
  )
) {
  $arguments["errorMessage"] = "Feil på innsendt skjema.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate phone number
if (!preg_match('/^(\+[0-9]+)? *([0-9] *){5,}$/', $_POST["tlf"])) {
  $arguments["errorMessage"] = "Ugyldig telefonnummer.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate e-mail address
if (!PHPMailer::validateAddress($_POST["mail"])) {
  $arguments["errorMessage"] = "Ugyldig e-postadresse.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate fødselsnummer
$number = $_POST["fødselsnummer"];
$k1 =
  11 -
  ((3 * $number[0] +
    7 * $number[1] +
    6 * $number[2] +
    1 * $number[3] +
    8 * $number[4] +
    9 * $number[5] +
    4 * $number[6] +
    5 * $number[7] +
    2 * $number[8]) %
    11);
$k2 =
  11 -
  ((5 * $number[0] +
    4 * $number[1] +
    3 * $number[2] +
    2 * $number[3] +
    7 * $number[4] +
    6 * $number[5] +
    5 * $number[6] +
    4 * $number[7] +
    3 * $number[8] +
    2 * $number[9]) %
    11);
if (!(strlen($number) == 11 && $number[9] == $k1 && $number[10] == $k2)) {
  $arguments["errorMessage"] = "Ugyldig fødselsnummer.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate time selection
if (!is_numeric($_POST["time"])) {
  $arguments["errorMessage"] = "Ugyldig valg av tidspunkt.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// All is good, attempt to insert into database
$query = "
  INSERT INTO paameldinger
    (navn, tlf, epost, foedselsnummer, tidspunkt_id)
  VALUES
    ($1, $2, $3, $4, $5)
";
$params = [
  $_POST["name"],
  $_POST["tlf"],
  $_POST["mail"],
  $_POST["fødselsnummer"],
  $_POST["time"],
];
$result = pg_query_params($query, $params);

// Handle database error
if (!$result) {
  $error = pg_last_error();
  if (strpos($error, "Tidspunktet er fullt")) {
    $arguments["errorMessage"] = "Tidspunktet er fullbooket.";
    $twig->load("submit.twig")->display($arguments);
    die();
  } elseif (strpos($error, "paameldinger_unique_tlf_per_tidspunkt")) {
    $arguments["errorMessage"] =
      "Du kan bare melde deg på en gang i hvert tidsrom.";
    $twig->load("submit.twig")->display($arguments);
    die();
  } else {
    $mail = setupMail();
    $mail->Subject = "[Hurtigtest][Error]";
    $mail->addAddress(ERROR_MAIL);
    $mail->msgHTML(
      "<html><head><title>" .
        "[Hurtigtest][Error]" .
        "</title></head><body style='white-space: pre-wrap;'>" .
        pg_last_error() .
        "</body></html>",
    );
    $mail->send();

    $arguments["errorMessage"] = "Databasefeil.";
    $twig->load("submit.twig")->display($arguments);
    die();
  }
}

$twig->load("submit.twig")->display($arguments);

?>
