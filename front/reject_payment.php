<?php
	//include('../connect.php');
	$data=$_POST['list'];
	  $db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
 //      $data =array();
         $data=json_decode($_GET['list'],true);
	 $j = 1;
	 for($i=0;$i<count($data);$i++)
	 {
	 	$ddocode=$data[$i][0];
	 	$stocode=substr($ddocode,0,4);
	 	$transid=$data[$i][1];
	 	$q1=pg_query("UPDATE tpayments SET billstatus='12' WHERE ddocode='$ddocode' and stocode='$stocode' and transid ='$transid'") or die(pg_last_error());
	 	if($q1)
	 	{
			
	 	}else
	 	{
	 		$j=0;
	 	}
	 }
	 pg_close($db2);
	 if($j==1)
	 {
	 	echo 'success';
	 }
	 else
	 {	
	 	echo 'fail';
	 }
	
?>
