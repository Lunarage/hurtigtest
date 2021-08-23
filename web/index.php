<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders index.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */

require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";
$arguments["lang"] = isset($_GET["lang"]) ? $_GET["lang"] : "no-nb";

$twig->load("index.twig")->display($arguments);
?>
