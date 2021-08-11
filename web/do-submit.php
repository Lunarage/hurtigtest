<?php
require_once "../include/init.php";

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
if (false) {
  $arguments["errorMessage"] = "Ugyldig telefonnummer.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate e-mail address
if (false) {
  $arguments["errorMessage"] = "Ugyldig e-postadresse.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate fødselsnummer
if (false) {
  $arguments["errorMessage"] = "Ugyldig fødselsnummer.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate time selection
if (false) {
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
  if(strpos($error, "Tidspunktet er fullt")){
    $arguments["errorMessage"] = "Tidspunktet er fullbooket.";
    $twig->load("submit.twig")->display($arguments);
    die();
  } else if(strpos($error, "paameldinger_unique_tlf_per_tidspunkt")){
    $arguments["errorMessage"] = "Du kan bare melde deg på en gang i hvert tidsrom.";
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
