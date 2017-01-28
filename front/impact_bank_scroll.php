<?php

$data = $_GET['list'];
foreach ($data as $val) {
	
	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	$rowtrans = pg_fetch_array(pg_query("SELECT * FROM transactions WHERE id='".$val['id']."' "));
	$transid = 0;
	$ddocode = $rowtrans['issueuser'];
	$stocode = substr($ddocode, 0,4);
	$transtype = "20";
	$chequeno = $rowtrans['chequeno'];
	$scrolldate = date("Y-m-d");
	$amount = $rowtrans['partyamount'];
	$updflag = 0;
	$year = date("Y");
	$ifsccode = $rowtrans['partyifsc'];
	$hoa = $rowtrans['hoa'];
	pg_close($db1);

	$db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
	//getting userid from bankusers table using ifsc code
	$row = pg_fetch_array(pg_query("SELECT userid FROM bankusers WHERE ifsccode='$ifsccode' "));
	$userid = $row['userid'];

	$impinsert = pg_query("INSERT INTO onlinebankscroll (transid, ddocode, stocode, transtype, chequeno, scrolldate, amount, updflag, year, ifsccode, userid, hoa) VALUES ('$transid', '$ddocode', '$transtype', '$chequeno', '$scrolldate', '$amount', '$updflag', '$year', '$ifsccode', '$userid', '$hoa') ");
	if(!$impinsert) {

		$fo = fopen('obsfailure.txt', "w");
		
	}

	pg_close($db2);

}




?>