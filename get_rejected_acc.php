<?php
//$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());


$pdac = pg_query("SELECT * FROM pdaccountinfo where status=2 ") or die(pg_last_error());
// echo "<pre>";
// print_r(pg_fetch_all($pdac));
// echo "</pre>";

pg_close($db1);
$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
$pdarr = array();
$i=0;
while($row = pg_fetch_array($pdac)) {

	$result = pg_fetch_array(pg_query("SELECT * FROM mpdddohoa WHERE ddocode='".$row['ddocode']."' AND hoa='".$row['hoa']."' "));
	$pdarr[$i]['ddocode'] = $row['ddocode'];
	$pdarr[$i]['hoa'] = $row['hoa'];
	$pdarr[$i]['obal'] = $result['obamt'];
	$i++;
}
pg_close($db2);

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

foreach ($pdarr as $value) {

	$ddo = $value['ddocode'];
	$hoa = $value['hoa'];
	$obal = round($value['obal']);

	//pg_query("UPDATE pdaccountinfo SET impact_obalance=$obal WHERE ddocode='$ddo' AND hoa='$hoa' AND status=2") or die(pg_last_error());
	echo $ddo."===".$obal."<br>";
}
?>