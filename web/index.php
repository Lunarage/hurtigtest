<?php
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";

$query = "
  SELECT *, (SELECT COUNT(*) FROM paameldinger WHERE tidspunkt_id = tidspunkter.id) AS paameldte
  FROM tidspunkter
  WHERE slutt_tid > NOW()
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
  $row["weekday"] = weekdayToNorwegian((int) date("N", $start_tid));
  $row["day"] = date("j", $start_tid);
  $row["month"] = monthToNorwegian((int) date("n", $start_tid));
  $row["startTime"] = date("H:i", $start_tid);
  $row["endTime"] = date("H:i", $slutt_tid);
  $arguments["tidspunkter"][] = $row;
}

$twig->load("index.twig")->display($arguments);
?>
