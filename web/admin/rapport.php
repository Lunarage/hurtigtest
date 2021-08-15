<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders rapport.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../../include/init.php";
$arguments["title"] = "Hurtigtest";

$query = "
  SELECT *,
  (SELECT COUNT(*) FROM paameldinger WHERE tidspunkt_id = tidspunkter.id) AS paameldte
  FROM tidspunkter
  ORDER BY start_tid
";
$result = pg_query($query);
$arguments["tidspunkter"] = [];
while($row = pg_fetch_array($result)){
  $row["printTime"] = date("Y-m-d", strtotime($row["start_tid"]));
  $row["printTime"] .= " " . date("H:i", strtotime($row["start_tid"]));
  $row["printTime"] .= " - " . date("H:i", strtotime($row["slutt_tid"]));
  $arguments["tidspunkter"][] = $row;
  if($row["id"] == $_GET["tidspunkt"]){
    $arguments["thisTidspunkt"] = $row;
  }
}

$query = "
  SELECT * FROM paameldinger WHERE tidspunkt_id = $1
";
$params = [$_GET["tidspunkt"]];
$result = pg_query_params($query, $params);

$arguments["people"] = [];
while($row = pg_fetch_array($result)){
  $row["paameldingstidspunkt"] = date("Y-m-d H:i",strtotime($row["paameldingstidspunkt"]));
  $arguments["people"][] = $row;
}

$twig->load("rapport.twig")->display($arguments);
?>
