<?php
require_once "../include/init_twig.php";
$arguments["title"] = "Hurtigtest";

$twig->load("index.twig")->display($arguments);
?>
