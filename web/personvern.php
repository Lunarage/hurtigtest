<?php
/**
 * Sign up for rapid test of COVID-19.
 *
 * Renders personvern.twig
 *
 * @author maghal (Magne "Peach" Halvorsen)
 */
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";
$arguments["lang"] = $_GET["lang"];

$twig->load("personvern.twig")->display($arguments);
?>
