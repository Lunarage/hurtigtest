<?php
require_once dirname(__FILE__)."/init_config.php";

$db_connect_string =
  "host=" . $db_host . " dbname=" . $db_name . " user=" . $db_user . " password=" . $db_password;
$db = pg_connect($db_connect_string)
  or die('Could not connect: ' . pg_last_error($db));

// TODO: Error page

unset($db_connect_string, $db_host, $db_name, $db_user, $db_password);
?>
