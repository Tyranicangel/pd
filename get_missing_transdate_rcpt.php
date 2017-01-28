<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$q1 = pg_query("SELECT * FROM transactions WHERE transdate IS NULL AND transtype='2' AND issueuser LIKE '0301%' ");
echo pg_num_rows($q1);
pg_close($db1);

$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

$recptarr = array();
 while($row = pg_fetch_array($q1)) {

 	$ddo = $row['issueuser'];
 	$hoa = $row['hoa'];
 	$transid = $row['chequeno'];
 	if($hoa=='8443008000009000000NVN')
        {
            $addQuery = "hoa = '".$hoa."'";
        } else if($hoa == '8448001090003001000NVN' || $hoa == '8448001090003006000NVN' || $hoa == '8448001090003008000NVN' || $hoa == '8338001040001000000NVN') {
            $addQuery = "hoa = '".$hoa."'";
        } else if($hoa == '8448001200003000000NVN') {
            $addQuery = "hoa = '".$hoa."'";
        }
        else if($hoa == '8011001050001000000NVN') {
            $addQuery = "hoa = '".$hoa."'";
        } else {

        	$addQuery = "ddocode= '".$ddo."' AND hoa = '".$hoa."'";
        }

 	$treceipts = pg_fetch_array(pg_query("SELECT * FROM treceipts WHERE ".$addQuery." AND transid='$transid' AND status='9' "));

 	$recptarr[$row['id']] = $treceipts['scrolldate'];
 }
pg_close($db2);

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
 foreach ($recptarr as $key => $value) {
    echo $value."<br>";
 	
 	//pg_query("UPDATE transactions SET transdate='$value', confirmdate='$value' WHERE id=$key ") or die(pg_last_error());
 }

?>