<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$chqarr = json_decode($_POST['chqarr']);
$ddocode = $_POST['ddocode'];
$hoa = $_POST['hoa'];


foreach ($chqarr as $chqno) {

	$result = pg_query("SELECT * FROM transactions WHERE chequeno='$chqno' AND issueuser='$ddocode' AND transtype='2' AND impactflag IS NULL ");
	if(pg_num_rows($result) > 0) {

		$row = pg_fetch_array($result);
		$id=$row['id'];
		$amount = $row['partyamount'];
		pg_query("DELETE FROM transactions WHERE id=$id ") or die(pg_last_error());
		pg_query("UPDATE pdaccountinfo SET balance=balance-$amount WHERE ddocode='$ddocode' AND hoa='$hoa' ") or die(pg_last_error());
		echo 1;
	} else {

		echo "0";
	}

}

?>