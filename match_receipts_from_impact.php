<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	
	$sdate = "2014-10-26";
	$resultour = pg_query("SELECT * FROM transactions WHERE issueuser='22010302001' AND hoa='8443001230001000000NVN' AND transstatus='3' AND transtype='1' AND transdate >= '$sdate' ");

	while($rowour=pg_fetch_array($resultour)) {

		echo $rowour['chequeno']."===".$rowour['partyamount']."<br>";
	}

	pg_close($db1);

	$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

	echo "<hr>";
	$q=pg_query("SELECT * FROM tpayments WHERE hoa ='8443001230001000000NVN' AND billstatus='9' AND formno='chq' AND ddocode = '22010302001' AND scrolldate >= '$sdate' ");
	while($rowour=pg_fetch_array($q)) {

		echo $rowour['transid']."===".$rowour['gross']."===".$rowour['scrolldate']."<br>";
	}



?>