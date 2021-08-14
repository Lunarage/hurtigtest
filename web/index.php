<?php
require_once "../include/init.php";
$arguments["title"] = "Hurtigtest";

$twig->load("index.twig")->display($arguments);
?>
