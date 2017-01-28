<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$ddocode = '27020104001';
$hoa = '8448001200002000000NVN';
$chequeno = '030564';
$amt = 16940;
$confirmdate = '2014-12-22';
$result = pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddocode' AND hoa='$hoa'"));

if($result['account_type'] == 2) {

	$newloc = $result['loc']-$amt;

} else {

	$newloc = $result['loc'];
}

$newbal = $result['balance'] - $amt;

//pg_query("UPDATE pdaccountinfo SET loc=$newloc, balance=$newbal WHERE ddocode='$ddocode' AND hoa='$hoa' ");

//pg_query("UPDATE transactions SET transstatus=3, confirmdate='$confirmdate' WHERE issueuser='$ddocode' AND chequeno='$chequeno' AND transtype='1' ");
echo "Done";