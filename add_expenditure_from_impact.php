<?php

$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

$imexp = pg_query("SELECT * FROM tpayments WHERE ddocode='09012202003' AND hoa='8448001090003001000NVN' AND billstatus = '9' AND transid='0000003896' AND trasidslno='001' AND stocode='0901' AND transtype='22' ");

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

while($rowimp = pg_fetch_array($imexp)) {
	$qdate = $rowimp['scrolldate'];
	$chqno = $rowimp['transid'];
	$imptxt=$rowimp['stocode'].$rowimp['transid'].$rowimp['transtype'];
	$ddo=$rowimp['ddocode'];
	$hoa=$rowimp['hoa'];
	$amt=$rowimp['gross'];
	$flg = 0;
	
	// $q233=pg_query_params($db1,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());
	var_dump($q233);
}

?>
