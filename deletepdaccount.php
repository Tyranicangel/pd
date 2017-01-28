<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
echo $id = $_POST['pdid'];
//pg_query("DELETE FROM pdaccountinfo where id=$id ");
?>