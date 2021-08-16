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

$query = "
  SELECT * FROM hurtigtest.paameldinger WHERE id = $1;
";
$params = [$_GET["id"]];
$result = pg_query_params($query, $params);

$arguments["person"] = pg_fetch_array($result);

$twig->load("person.twig")->display($arguments);
?>
