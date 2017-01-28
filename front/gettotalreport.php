<?php
$db=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$stocode = $_GET['stocode'];
$fdate = $_GET['fdate'];
$tdate = $_GET['tdate'];

$totalArr = array();
$resultlocgrant = pg_query("SELECT SUM(grantamount) FROM locrequest WHERE requestuser LIKE '$stocode%' AND requestdate >= '$fdate' AND requestdate <= '$tdate' AND (conf_flag='1' OR conf_flag='3' OR conf_flag='4' OR conf_flag='5' OR conf_flag='6') AND requestflag!='0' ") or die(pg_last_error());
$rowlocgrant = pg_fetch_array($resultlocgrant);
$totalArr['locgranted'] = $rowlocgrant['sum'];


$resultlocpending = pg_query("SELECT SUM(grantamount) FROM locrequest WHERE requestuser LIKE '$stocode%' AND requestdate >= '$fdate' AND requestdate <= '$tdate' AND requestflag='0' ") or die(pg_last_error());
$rowlocpending = pg_fetch_array($resultlocpending);
$totalArr['locpending'] = $rowlocpending['sum'];

$resultrcpt = pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser LIKE '$stocode%' AND transdate >= '$fdate' AND transdate <= '$tdate' AND transtype='2' AND transstatus='3' ") or die(pg_last_error());
$rowrcpt=pg_fetch_array($resultrcpt);
$totalArr['totalrcpt'] = $rowrcpt['sum'];

print_r($totalArr);


?>