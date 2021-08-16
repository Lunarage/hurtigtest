<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders person.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../../include/init.php";
$arguments["title"] = "Hurtigtest";

$arguments["personer"] = [];
if(isset($_GET["id"])){
  $query = "
    SELECT * FROM hurtigtest.paameldinger WHERE id = $1;
  ";
  $params = [$_GET["id"]];
  $result = pg_query_params($query, $params);

  $arguments["personer"][] = pg_fetch_array($result);
} else if (isset($_GET["tidspunkt"])) {
  $query = "
    SELECT * FROM hurtigtest.paameldinger WHERE tidspunkt_id = $1;
  ";
  $params = [$_GET["tidspunkt"]];
  $result = pg_query_params($query, $params);

  while($row = pg_fetch_array($result)){
    $arguments["personer"][] = $row;
  }
} else {
  $arguments["personer"][] = "";
}

$twig->load("person.twig")->display($arguments);
?>
