<?php
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";
$arguments["lang"] = $_GET["lang"];

$twig->load("index.twig")->display($arguments);
?>
