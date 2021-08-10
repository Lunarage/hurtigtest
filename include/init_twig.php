<?php
require_once "Twig/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader(
  dirname(__FILE__) . "/../templates",
);
$twig = new \Twig\Environment($loader, ["debug" => true]);

$arguments = [];

?>
