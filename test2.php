<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$out=pg_fetch_all(pg_query("SELECT * FROM users WHERE username='2201SA01'"));
// $q=pg_query("SELECT * FROM transactions WHERE  transtype=2 AND confirmdate IS NULL AND impstring LIKE '201516%'") or die(pg_last_error());
// pg_close($db1);
// $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
// $out=array();
// while($r=pg_fetch_array($q,null,PGSQL_ASSOC))
// {
// 	$imp=$r['impstring'];
// 	$stocode=substr($imp,6,4);
// 	$transid=substr($imp,10,10);
// 	$transtype=substr($imp,20,2);
// 	$translsno=substr($imp,22,3);
// 	$qq=pg_fetch_array(pg_query("SELECT * FROM treceipts WHERE transid='$transid' AND stocode='$stocode' AND transtype='$transtype' AND transidslno='$translsno'"));
// 	$qdate=$qq['scrolldate'];
// 	// if($qdate)
// 	// {
// 		array_push($out,array($r['id'],$qdate));
// 	// }
// }
// pg_close($db2);
// $db3= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
echo '<pre>';
print_r($out);
echo '</pre>';
// for($i=0;$i<count($out);$i++)
// {
// 	$ids=$out[$i][0];
// 	$dates=$out[$i][1];
// 	$fr=pg_query("UPDATE transactions SET transdate='$dates' WHERE id=$ids");
// 	$fr1=pg_fetch_all(pg_query("SELECT * FROM transactions WHERE id=$ids"));
// }
?>