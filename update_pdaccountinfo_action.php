<?php

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$ddocode = $_POST['ddocode'];
$hoa = $_POST['hoa'];
$obal = $_POST['obal'];
$bal = $_POST['bal'];
$transitamount = $_POST['transitamount'];
$status = $_POST['status'];
$loc = $_POST['loc'];

pg_query("UPDATE pdaccountinfo SET obalance=$obal, balance=$bal, transitamount=$transitamount, loc=$loc, status=$status WHERE ddocode='$ddocode' AND hoa='$hoa' ");
echo "Done";

?>