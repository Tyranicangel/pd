<?php
set_time_limit(0);
include('connect.php');
$tre=$_GET['userid'];
echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Opening Balances Report</title></head>";
echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Opening Balances Report</h3><br><br><br><p style='margin-left:20px;'><span style='color:red;'>*</span>Opening balances as of 1st June 2015.</p></br>";
$arr=array();
$n=0;
$res=pg_query("SELECT ddocode,hoa,obalance FROM pdaccountinfo WHERE ddocode LIKE '$tre%' ORDER BY ddocode");
while ($row=pg_fetch_array($res)) {
	$ddo=$row['ddocode'];
	$hoa=$row['hoa'];
	$obal=$row['obalance'];
	$receipts=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND confirmdate >= '2015-04-01' AND confirmdate < '2015-06-01' AND transtype='2' AND transstatus='3'"));
	$cheques=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND confirmdate >= '2015-04-01' AND confirmdate < '2015-06-01' AND transtype='1' AND transstatus='3'"));
	$totr=$receipts['sum'];
	$totc=$cheques['sum'];
	$rec=$obal+$totr;
	$result=$rec-$totc;
	$arr[$n]['ddocode']=$ddo;
	$arr[$n]['hoa']=$hoa;
	$arr[$n]['obal']=$result;
	$n++;
}
echo "<table class='each_table' style='margin-left:20px;'>
	<tr class='heading_row'>";
	echo "<th>S.No</th><th>DDO Code</th><th>HOA</th><th>Opening Balance(in Rs.)</th>";
	echo "</tr>";
	
	$i=1;
	foreach ($arr as $key => $value) {
			echo "<tr>";
			echo "<td>".$i."</td>";
			echo "<td>".$value['ddocode']."</td>";
			echo "<td>".$value['hoa']."</td>";
			echo "<td>".$value['obal']."</td>";
			echo "</tr>";
			$i++;
	}

	echo "</table></div>";
?>