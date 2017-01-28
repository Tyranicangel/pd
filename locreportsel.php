<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$locrow = pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode = '22010313001' "));

$loc = $locrow['loc'];
$obal = $locrow['obalance'];

?>