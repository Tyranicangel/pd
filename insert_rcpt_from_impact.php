<?php

$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

$imexp = pg_query("SELECT * FROM treceipts WHERE ddocode='27020317001' AND hoa='8443001230001000000NVN' AND status = '9' AND transid='0000002204' AND amount='133854784' ");
pg_close($db2);

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

while($rowimp = pg_fetch_array($imexp)) {
	echo $qdate = $rowimp['scrolldate']; echo "<br>";
	$chqno = $rowimp['transid'];
	$imptxt="201415".$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
	$ddo=$rowimp['ddocode'];
	$hoa=$rowimp['hoa'];
	$amt=$rowimp['amount'];
	$names=$r3['remittersname'];
	$flg = 0;
	$nbal = 0;
	
	//$q233=pg_query_params($db1,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));    
}

?>
