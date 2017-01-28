<?php
if(!isset($_POST['ddocode']) && !isset($_POST['hoa'])) {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>
	DDOCODE - <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
	HOA - <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp;
	<button type='submit'>CHECK</button>
	</form> ";
} else {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>
	DDOCODE - <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
	HOA - <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp;
	<button type='submit'>CHECK</button>
	</form>";
	$ddocode = $_POST['ddocode'];
	$hoa = $_POST['hoa'];

	if(strpos($ddocode, '2702') == 0) {

		$sdate = "2014-10-26";
	}
	else {

		$sdate = "2014-06-02";
	}

	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	$resultour = pg_fetch_array(pg_query("SELECT COUNT(*) FROM transactions WHERE  hoa='8443008000009000000NVN' AND transstatus='3' AND transtype='1' AND confirmdate >= '2015-03-31' "));
	
	echo "Total cheques in PD portal: ".$resultour['count']; echo "<br>";

	$resultourrec = pg_fetch_array(pg_query("SELECT COUNT(*) FROM transactions WHERE hoa='8443008000009000000NVN' AND transstatus='3' AND transtype='2' AND transdate >= '2015-03-31' "));
	
	echo "Total receipts in PD portal: ".$resultourrec['count']; echo "<br>";

	pg_close($db1);

	$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

	$q=pg_fetch_array(pg_query("SELECT COUNT(*) FROM tpayments WHERE hoa ='8443008000009000000NVN' AND billstatus='9' AND formno='chq' "));

	// echo "<pre>";
	// print_r($q);
	// echo "</pre>";

	echo "Total cheques in Impact: ".$q['count']; echo "<br>";

	$q=pg_fetch_array(pg_query("SELECT COUNT(*) FROM treceipts WHERE hoa ='8443008000009000000NVN' AND status='9' "));

	echo "Total receipts in Impact: ".$q['count'];

}

?>