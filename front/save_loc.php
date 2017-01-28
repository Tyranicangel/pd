<?php
	// include('../connect.php');
	 $db2=pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
        
	$data =array();

	$data=json_decode($_GET['list'],true);
	for($i=0;$i<count($data);$i++)
	{
		$ddocode=$data[$i]['requestuser'];
		$stocode=substr($ddocode,0,4);
		$dept=substr($ddocode,4,4);
		$hoa=$data[$i]['hoa'];
		$amt=str_replace(',','',$data[$i]['grantamount']);
		$ref=$data[$i]['refno'];
		$q1=pg_fetch_array(pg_query("SELECT * FROM mpdloc WHERE ddocode='$ddocode' AND hoa='$hoa' and stocode='$stocode'"),null,PGSQL_ASSOC);
		$loc=intval($q1['locamt']);
		$nloc=$loc+intval($amt);
		$cat=$q1['catg'];
		$sub=$q1['subcatg'];
		$q2=pg_query("UPDATE mpdloc SET locamt=$nloc WHERE ddocode='$ddocode' AND hoa='$hoa' AND stocode='$stocode'") or die(pg_last_error());
		$q3=pg_query("INSERT INTO mpdlocdet (ddocode,stocode,deptcode,hoa,locamt,locletterno,locdate,subcatg,catg) VALUES ('$ddocode','$stocode','$dept','$hoa',$amt,'$ref','NOW()','$sub','$cat')") or die(pg_last_error());
		var_dump($q2);
		var_dump($q3);
	}
	pg_close($db2);
?>
