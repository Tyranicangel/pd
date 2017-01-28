<?php
$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

$q = pg_fetch_array(pg_query("SELECT * FROM mpdddohoa WHERE ddocode='27020202001' AND hoa='8449001200009000000NVN' "));

echo $obalim = $q['obamt']; echo "<br>";

//$q=pg_query("SELECT amount FROM treceipts WHERE status='9' AND ddocode = '27020317001' AND hoa='8443001230001000000NVN' ");

// echo "<pre>";
// echo print_r(pg_fetch_all($q));
// echo "</pre>";

pg_close($db2);

echo "<hr>";

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());


	$trans = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE confirmdate < '2015-04-01' AND issueuser='27020202001' AND hoa='8449001200009000000NVN' AND transtype='1' AND transstatus='3' "));


	$transr = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE confirmdate < '2015-04-01' AND issueuser='27020202001' AND hoa='8449001200009000000NVN' AND transtype='2' AND transstatus='3' "));


	$bal = $obalim + $transr['sum'];
	echo $mainbal = $bal-$trans['sum'];

