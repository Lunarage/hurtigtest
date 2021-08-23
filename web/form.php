<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders form.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";
$arguments["lang"] = isset($_GET["lang"]) ? $_GET["lang"] : "no-nb";

$query = "
  SELECT *, (SELECT COUNT(*) FROM paameldinger WHERE tidspunkt_id = tidspunkter.id) AS paameldte
  FROM tidspunkter
  WHERE slutt_tid > NOW()
  ORDER BY start_tid
";
$result = pg_query($query);

$arguments["tidspunkter"] = [];
$arguments["free"] = 0;
while ($row = pg_fetch_array($result)) {
  if ($row["paameldte"] < $row["plasser"]) {
    $arguments["free"]++;
  }
  $start_tid = strtotime($row["start_tid"]);
  $slutt_tid = strtotime($row["slutt_tid"]);
  $row["weekday"] = $_GET["lang"] == "en" ? date("l", $start_tid): weekdayToNorwegian((int) date("N", $start_tid));
  $row["day"] = date("j", $start_tid);
  $row["month"] = $_GET["lang"] == "en" ? date("F", $start_tid) : monthToNorwegian((int) date("n", $start_tid));
  $row["startTime"] = date("H:i", $start_tid);
  $row["endTime"] = date("H:i", $slutt_tid);
  $arguments["tidspunkter"][] = $row;
}

$twig->load("form.twig")->display($arguments);
?>
