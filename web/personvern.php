<?php
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";

$twig->load("personvern.twig")->display($arguments);
?>
