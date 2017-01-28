<?php
//	include('../connect.php');
 	$db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
 	$data =array();	
 	//$data=json_decode($_GET['dat'],true);
	$ddocode=$_GET['issueuser'];
	
 	$hoa=$_GET['hoa'];
 	$chqno=$_GET['chequeno'];
	$stocode=substr($ddocode,0,4);
 	$gross=$_GET['partyamount'];
 	$net=$_GET['partyamount'];
 	$date=$_GET['transdate'];
 	$mmyy=substr($date,5,2).substr($date,0,4);

 	$q1=pg_fetch_array(pg_query("SELECT * FROM msto WHERE stocode='$stocode'"),null,PGSQL_ASSOC);
 	$transid=$q1['transid'];
 	$ntransid=intval($transid)+1;
 	$ntransid = str_pad($ntransid,10,"0", STR_PAD_LEFT);
// //	echo $ntransid;	
// //	echo $chqno;	
 	$q2=pg_query("UPDATE msto SET transid='$ntransid' WHERE stocode='$stocode'");
 	$q3=pg_query("INSERT INTO tpayments (ddocode,trasidslno,stocode,hoa,transtype,formno,gross,net,dedn,mmyy,tokenissuedate,billstatus,chequeno,transid) VALUES ('$ddocode','000','$stocode','$hoa','20','chq',$gross,$net,0,'$mmyy','NOW()','1','$chqno','$ntransid')");
 	pg_close($db2);
	
//	var_dump($q3);	
	
 	if($q3)
 	{
 		echo $ntransid;
 	}
 	else
 	{
 		echo 'Fail';
 	}
 	//0000041670
?>
