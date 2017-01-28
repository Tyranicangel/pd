<?php
//	include('../connect.php');
	 $db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

// 	$data=$_POST['list'];
 //	$data =array();
        $data=json_decode($_GET['list'],true);
 	$j = 1;
// 	var_dump($data);
 	for($i=0;$i<count($data);$i++)
 	{
 		$ddocode=$data[$i][0];
 		$stocode=substr($ddocode,0,4);
 		$transid=$data[$i][1];

 		$q1=pg_query("UPDATE tpayments SET billstatus='7' WHERE ddocode='$ddocode' AND transid='$transid'"); 
	//	 pg_close($db2);
	//	var_dump($q1);
 		if($q1)
 		{
			
 		}else
 		{
 			$j = 0;
 		}
 	}
 	pg_close($db2);
 	if($j==1)
 	{
 		echo 'success';
 	}else
 	{
 		echo 'fail';
 	}
?>
