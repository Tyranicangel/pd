<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$id = $_POST['id'];

$result = pg_query("SELECT * FROM transactions WHERE id='$id' ");
	if(pg_num_rows($result) > 0) {

		$row = pg_fetch_array($result);
		$amount = $row['partyamount'];
		$ddocode = $row['issueuser'];
		$hoa = $row['hoa'];
		pg_query("DELETE FROM transactions WHERE id=$id ") or die(pg_last_error());
		pg_query("UPDATE pdaccountinfo SET balance=balance-$amount WHERE ddocode='$ddocode' AND hoa='$hoa' ") or die(pg_last_error());
		echo 1;
	} else {

		echo 0;
	}

?>