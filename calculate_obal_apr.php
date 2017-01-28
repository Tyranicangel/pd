<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
//$q = pg_fetch_all(pg_query("SELECT * FROM users WHERE username='secif' "));
$q = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode LIKE '2702%' ");
// echo "<pre>";
// print_r(pg_fetch_all($q));
// echo "</pre>";
//
$i=0;
while($row=pg_fetch_array($q)) {

	$ddocode = $row['ddocode'];
	$hoa = $row['hoa'];
	$obalance = $row['obalance'];
	// $transitamt = $row['transitamount'];
	// $balance = $row['balance'];
	// $resultrcpt = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' AND confirmdate < '2015-04-01' "));
	// $totrcpt = $resultrcpt['sum'];

	// $resultexp = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='1' AND transstatus='3' AND confirmdate < '2015-04-01' "));

	// $totexp = $resultexp['sum'];
	// $prebal1 = $obalance+$totrcpt;
	// $prebal2 = $totexp;

	// $finalbal = $prebal1-$prebal2;
	
	$resultarr[$i]['ddocode'] = $ddocode;
	$resultarr[$i]['hoa'] = $hoa;
	$resultarr[$i]['bal'] = $obalance;

	// pg_query("UPDATE pdaccountinfo SET obalance=$finalbal WHERE ddocode='$ddocode' AND hoa='$hoa' ") or die(pg_last_error());

	$i++;	
}
pg_close($db1);
$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

foreach ($resultarr as $key => $value) {
	
	$resultim = pg_fetch_array(pg_query("SELECT * FROM mpdddohoa WHERE ddocode='".$value['ddocode']."' AND hoa='".$value['hoa']."' "));

	echo $value['ddocode']." ====> our obal:".$value['bal'].",  Impact obal:".$resultim['obamt']."<br>";
}

 


?>