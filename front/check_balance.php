<?php

$ddocode = $_GET['ddocode'];
$hoa = $_GET['hoa'];

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
//$q = pg_fetch_all(pg_query("SELECT * FROM users WHERE username='secif' "));
$q = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddocode' AND hoa='$hoa' ");
// echo "<pre>";
// print_r(pg_fetch_all($q));
// echo "</pre>";

while($row=pg_fetch_array($q)) {

	$ddocode = $row['ddocode'];
	$hoa = $row['hoa'];
	$obalance = $row['obalance'];
	$transitamt = $row['transitamount'];
	$balance = $row['balance'];
	$resultrcpt = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' AND transdate >= '2015-04-01' "));
	$totrcpt = $resultrcpt['sum'];

	$resultexp = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='1' AND transstatus='3' AND transdate >= '2015-04-01' "));

	$totexp = $resultexp['sum'];

	$prebal1 = $obalance+$totrcpt;
	$prebal2 = $transitamt+$totexp;
	
	$finalbal = $prebal1-$prebal2;
	

	if($balance != $finalbal) {

		$out = 0;
	} else {

		$out = 1;
	}
}
?>