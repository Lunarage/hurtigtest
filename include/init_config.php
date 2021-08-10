<?php
if(file_exists(dirname(__FILE__)."/config-local.php")){
  include('config-local.php');
} else {
  $db_host="foo";
  $db_name="bar";
  $db_user="baz";
  $db_password="foz";
}
?>
