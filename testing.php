<?php
	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	$q1=pg_query("SELECT * FROM users WHERE user_role='2' AND username LIKE '2702%'");
	$fp=fopen('details.csv','w');
	fputcsv($fp,array('Sno','DDO Code','Designation','Head of Account','Opening Balance','Closing Balance','Total Credit','Total Debit'));
	$counter=0;
	while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
	{
		$ddocode=$r1['username'];
		$q2=pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddocode'");
		while($r2=pg_fetch_array($q2,null,PGSQL_ASSOC))
		{
			$counter++;
			$ddo=$r2['ddocode'];
			$hoa=$r2['hoa'];
			$obal=$r2['obalance'];
			$q3=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transtype=1 AND transstatus=3 AND confirmdate>='2014-06-01' AND confirmdate<'2015-04-01'"),null,PGSQL_ASSOC);
			$q4=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transtype=2 AND transstatus=3 AND confirmdate>='2014-06-01' AND confirmdate<'2015-04-01'"),null,PGSQL_ASSOC);
			$fbal=$obal+$q4['sum']-$q3['sum'];
			fputcsv($fp,array($counter,$ddo,$r1['userdesc'],$hoa,$obal,$fbal,$q3['sum'],$q4['sum']));
		}
	}
	fclose($fp);
?>