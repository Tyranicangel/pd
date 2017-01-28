<?php

if(!isset($_POST['ddocode']) || !isset($_POST['hoa'])) {

		echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >

			DDOCODE: <input type='text' name='ddocode' /> &nbsp; &nbsp; HOA: <input type='text' name='hoa' /> &nbsp; &nbsp; <button type='submit'>SUBMIT</button>
		</form>";

	} else {

		$ddocode = $_POST['ddocode'];
		$hoa = $_POST['hoa'];

		$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

		$tpayrow=pg_fetch_array(pg_query("SELECT SUM(gross) FROM tpayments WHERE hoa ='$hoa' AND billstatus='9' AND formno='chq' AND ddocode = '$ddocode' AND scrolldate >= '2014-06-02' "));
		$paysum = round($tpayrow['sum']); 

		$trecptrow=pg_fetch_array(pg_query("SELECT SUM(amount) FROM treceipts WHERE hoa ='$hoa' AND status='9' AND ddocode = '$ddocode' AND scrolldate >= '2014-06-02' "));
		$recptsum = round($trecptrow['sum']);

		$obalrow = pg_fetch_array(pg_query("SELECT obamt FROM mpdddohoa WHERE ddocode='$ddocode' AND hoa='$hoa' "));
		$obal = round($obalrow['obamt']); 

		$balpre = $obal+$recptsum;

		echo $mainbal = $balpre - $paysum;

		$locrow = pg_fetch_all(pg_query("SELECT * FROM mpdloc WHERE ddocode='$ddocode' AND hoa='$hoa' "));

		echo "<pre>";
		print_r($locrow);
		echo "</pre>";

	}