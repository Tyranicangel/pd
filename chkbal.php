<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$q = pg_query("SELECT * FROM pdaccountinfo WHERE activation='2' AND ddocode LIKE '2702%'");
$out=array();
while($row=pg_fetch_array($q)) {

	$ddocode = $row['ddocode'];
	$hoa = $row['hoa'];
	$obalance = $row['obalance'];
	$transitamt = $row['transitamount'];
	$balance = $row['balance'];
	$resultrcpt = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' AND transdate >= '2014-06-02' AND confirmdate<'2015-04-01'"));
	$totrcpt = $resultrcpt['sum'];
	$resultexp = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='1' AND transstatus='3' AND transdate >= '2014-06-02' AND confirmdate<'2015-04-01'"));
	$totexp = $resultexp['sum'];
	$prebal1 = $obalance+$totrcpt;
	$prebal2 = $totexp;
	$finalbal = $prebal1-$prebal2;
	$finalbal=$finalbal.'.00';
	// if($balance != $finalbal) {
	// 	echo "ddocode>> ".$ddocode."======hoa>> ".$hoa."========Pdacinfo balance>> ".$balance."======calculated balance>> ".$finalbal."=======transitamt>> ".$transitamt."======obalance>> ".$obalance."<br>";
	// }
	array_push($out, array($ddocode,$hoa,$finalbal));
}
pg_close($db1);
$db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
for($i=0;$i<count($out);$i++)
{
	$dd=$out[$i][0];
	$hh=$out[$i][1];
	$qq=pg_fetch_array(pg_query("SELECT * FROM mpdddohoa WHERE ddocode='$dd' AND hoa='$hh'"),null,PGSQL_ASSOC);
	$out[$i][3]=$qq['obamt'];
	if($qq['obamt']!=$out[$i][2])
	{
		echo '<pre>';
		print_r($out[$i]);
		echo '</pre>';		
	}
}
// $out=pg_fetch_all(pg_query("SELECT * FROM mpdddohoa LIMIT 100"));


?>