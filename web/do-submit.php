<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * do-submit.php
 * Validates the form data, sends confirmation mail
 * and inserts into database.
 * Renders submit.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../include/init.php";

use PHPMailer\PHPMailer\PHPMailer;
require_once "libphp-phpmailer/autoload.php";

$arguments["title"] = "Hurtigtest";
$arguments["lang"] = $_GET["lang"];

// Check that all inputs are set
if (
  !(
    isset($_POST["name"]) &&
    isset($_POST["tlf"]) &&
    isset($_POST["mail"]) &&
    (isset($_POST["fødselsnummer"]) || $_POST["no_fødselsnummer"]) &&
    isset($_POST["time"])
  )
) {
  $arguments["errorMessage"] =
    $_GET["lang"] == "en"
      ? "The form is missing an input"
      : "Skjemaet mangler et feldt.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate phone number
if (!preg_match('/^(\+[0-9]+)? *([0-9] *){5,}$/', $_POST["tlf"])) {
  $arguments["errorMessage"] =
    $_GET["lang"] == "en" ? "Invalid phone number" : "Ugyldig telefonnummer.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate e-mail address
if (!PHPMailer::validateAddress($_POST["mail"])) {
  $arguments["errorMessage"] =
    $_GET["lang"] == "en" ? "Invalid e-mail address" : "Ugyldig e-postadresse.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// Validate fødselsnummer
// https://no.wikipedia.org/wiki/Fødselsnummer
// Should work for D-numbers as well
if (!$_POST["no_fødselsnummer"]) {
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

  if ($k1 === 11) {
    $k1 = 0;
  }
  if ($k2 === 11) {
    $k2 = 0;
  }

  if (!($k1 < 10 && $k2 < 10 && $k1 == $number[9] && $k2 == $number[10])) {
    $arguments["errorMessage"] =
      $_GET["lang"] == "en"
        ? "Invalid personal ID number."
        : "Ugyldig fødselsnummer.";
    $twig->load("submit.twig")->display($arguments);
    die();
  }
}

// Validate time selection
if (!is_numeric($_POST["time"])) {
  $arguments["errorMessage"] =
    $_GET["lang"] == "en"
      ? "Invalid timeframe chosen"
      : "Ugyldig valg av tidspunkt.";
  $twig->load("submit.twig")->display($arguments);
  die();
}

// All is good, attempt to insert into database
$query = "
  INSERT INTO paameldinger
    (navn, tlf, epost, foedselsnummer, tidspunkt_id, self_declaration)
  VALUES
    ($1, $2, $3, $4, $5, $6)
";
$params = [
  $_POST["name"],
  $_POST["tlf"],
  $_POST["mail"],
  $_POST["no_fødselsnummer"] ? null : $_POST["fødselsnummer"],
  $_POST["time"],
  true,
];
$result = pg_query_params($query, $params);

// Handle database error
if (!$result) {
  $error = pg_last_error();
  if (strpos($error, "Tidspunktet er fullt")) {
    $arguments["errorMessage"] =
      $_GET["lang"] == "en"
        ? "The timeframe is fully booked."
        : "Tidspunktet er fullbooket.";
    $twig->load("submit.twig")->display($arguments);
    die();
  } elseif (strpos($error, "paameldinger_unique_tlf_per_tidspunkt")) {
    // Assuming phone numbers are unique
    $arguments["errorMessage"] =
      $_GET["lang"] == "en"
        ? "You can only sign up once in the same timeframe"
        : "Du kan bare melde deg på en gang i hvert tidsrom.";
    $twig->load("submit.twig")->display($arguments);
    die();
  } elseif (strpos($error, "Tidspunktet er forbipassert")) {
    $arguments["errorMessage"] =
      $_GET["lang"] == "en"
        ? "You cannot sign up for a past timeframe."
        : "Du kan ikke melde deg på et forbipassert tidspunkt.";
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

    $arguments["errorMessage"] =
      $_GET["lang"] == "en" ? "Database error" : "Databasefeil";
    $twig->load("submit.twig")->display($arguments);
    die();
  }
}

// Send confirmation e-mail
$query = "SELECT start_tid, slutt_tid FROM tidspunkter WHERE id = $1";
$params = [$_POST["time"]];
$result = pg_query_params($query, $params);
$row = pg_fetch_array($result);

$recipient = $_POST["mail"];
// Create a string like "mandag 16.08.2021 kl. 12:00 - 12:30"
// https://www.php.net/manual/en/datetime.format.php
$time =
  $_GET["lang"] == "en"
    ? date("L", strtotime($row["start_tid"]))
    : weekdayToNorwegian((int) date("N", strtotime($row["start_tid"]))) .
      " " .
      date("d.m.Y", strtotime($row["start_tid"])) .
      " " .
      date("H:i", strtotime($row["start_tid"])) .
      " - " .
      date("H:i", strtotime($row["slutt_tid"]));

$name = $_POST["name"];

$mail = setupMail();
$mail->Subject =
  $_GET["lang"] == "en" ? "Sign up for rapid test" : "Bestilling av Hurtigtest";
$mail->addAddress($recipient);
$mail->msgHTML(
  $_GET["lang"] == "en"
    ? <<<EOF
    <html><head><title>Sign up for rapid test</title></head>
    <body>
      <p>Hello, $name.</p>
      <p>This is a confirmation of your sign up for a rapid test:</p>
      <p>Time: $time<br/>
      Place: Test station beside Trafon, Høyskoleparken, Klæbuveien 1</p>
      <p>Remember to:</p>
      <ul>
        <li>Be precise</li>
        <li>Wear a face mask</li>
        <li>Bring ID</li>
      </ul>
      <p>If you wish to cancel, reply to this e-mail.</p>
      <p>Vennlig hilsen<br />
      Hurtigteststasjonen
      </p>
    </body>
    </html>
    EOF
    : <<<EOF
    <html><head><title>Bestilling av Hurtigtest</title></head>
    <body>
      <p>Hei, $name.</p>
      <p>Vi bekrefter din bestilling av følgende time for hurtigtest:</p>
      <p>Tid: $time<br/>
      Sted: Teststasjon utenfor Trafon, Høyskoleparken, Klæbuveien 1</p>
      <p>Husk:</p>
      <ul>
        <li>Møt presis</li>
        <li>Bruk munnbind</li>
        <li>Ta med legitimasjon</li>
      </ul>
      <p>Hvis du ønsker å avbestille, svar på denne e-posten.</p>
      <p>Vennlig hilsen<br />
      Hurtigteststasjonen
      </p>
    </body>
    </html>
    EOF,
);
$mail->send();

// Render page
$arguments["time"] = $time;

$twig->load("submit.twig")->display($arguments);

?>
