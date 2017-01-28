<?php
	$db=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	$ddo=$_GET['ddo'];
	$hoa=$_GET['hoa'];
	$y=$_GET['y'];
	$m=$_GET['m'];
	$account=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$hoa'"),null,PGSQL_ASSOC);
	$ddoname=pg_fetch_array(pg_query("SELECT * FROM users WHERE username='$ddo'"),null,PGSQL_ASSOC);
	$hoaname=pg_fetch_array(pg_query("SELECT * FROM schemes WHERE hoa='$hoa'"),null,PGSQL_ASSOC);
	$ds=$y.'-'.$m.'-01';
	if($m=='12')
	{
		$dey=$y+1;
                $dem='01';
        }
        else
        {
        	$dey=$y;
               	$dem=$m+1;
        }
	$de=$dey.'-'.$dem.'-01';
	if($ds<'2015-04-01')
	{
		$s_r=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transstatus='3' AND transtype='2' AND confirmdate>='$ds' AND confirmdate<'2015-04-01'"),null,PGSQL_ASSOC);
		$s_p=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transstatus='3' AND transtype='1' AND confirmdate>='$ds' AND confirmdate<'2015-04-01'"),null,PGSQL_ASSOC);
		$obal=intval($account['obalance']);
		$ob=$obal-$s_r['sum']+$s_p['sum'];
	}
	else
	{
		$s_r=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transstatus='3' AND transtype='2' AND confirmdate<'$ds' AND confirmdate>='2015-04-01'"),null,PGSQL_ASSOC);
		$s_p=pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transstatus='3' AND transtype='1' AND confirmdate<'$ds' AND confirmdate>='2015-04-01'"),null,PGSQL_ASSOC);
		$obal=intval($account['obalance']);
		$ob=$obal+$s_r['sum']-$s_p['sum'];
	}
	$trans=pg_query("SELECT * FROM transactions WHERE issueuser='$ddo' AND hoa='$hoa' AND transstatus='3' AND confirmdate>='$ds' AND confirmdate<'$de' ORDER BY confirmdate");
	$filename="../back/public/uploads/AccountStatement".$account['ddocode'].'_'.$account['hoa'].'.txt';
	$fp=fopen($filename,'w');
	$lb=str_repeat('-',107);
	$wt="";
	$wt=$wt."                                        Account Statement-".$m.'/'.$y."\n";
	$wt=$wt."DDO:".$ddoname['userdesc']."(".$account['ddocode'].")                      Head of Account:".$hoaname['schemename']."(".$account['hoa'].")\n";
	$wt=$wt."                                    Opening Balance:".$ob.".00\n";
	$wt=$wt."S.No        Date            Chq/Trans              Credit            Debit          Balance\n";
	// $ob=$obal+$s_r['sum']-$s_p['sum'];
	$cb=$ob;
	$wt=$wt.$lb."\n";
	fwrite($fp,$wt);
	$count=1;
	$debittotal=0;
	$credittotal=0;
	while($t=pg_fetch_array($trans,null,PGSQL_ASSOC))
	{
		$txt='';
		$count1=str_pad($count,12,' ',STR_PAD_RIGHT);
		$txt=$txt.$count1;
		$dt12=substr($t['confirmdate'],8,2).'/'.substr($t['confirmdate'],5,2).'/'.substr($t['confirmdate'],0,4);
		$date1=str_pad($dt12,16,' ',STR_PAD_RIGHT);
		$txt=$txt.$date1;
		$chq1=str_pad($t['chequeno'],14,' ',STR_PAD_RIGHT);
		$txt=$txt.$chq1;
		if($t['transtype']=='1')
		{
			$dbt=$t['partyamount'];
			$cdt='0';
			$cb=$cb-$dbt;
			$debittotal=$debittotal+$dbt;
		}
		else
		{
			$cdt=$t['partyamount'];
			$dbt="0";
			$cb=$cb+$cdt;
			$credittotal=$credittotal+$cdt;
		}
		$cdt1=str_pad($cdt,12,' ',STR_PAD_LEFT);
		$txt=$txt.$cdt1.'.00  ';
		$dbt1=str_pad($dbt,12,' ',STR_PAD_LEFT);
		$txt=$txt.$dbt1.'.00  ';
		$ob1=str_pad($cb,12,' ',STR_PAD_LEFT);
		$txt=$txt.$ob1.'.00  ';
		$count++;
		$wt=$txt."\n";
		fwrite($fp,$wt);
	}
	$wt=$lb."\n";
	$wt=$wt."Total Credit: ".$credittotal.".00		Total Debit: ".$debittotal.".00		Closing Balance:".$cb.".00\n";
	//$wt=$wt."                                    Closing Balance:".$cb.".00\n";
	$wt=$wt.$lb."\n";
	$wt=$wt.$lb."\n";
	fwrite($fp,$wt);
	fclose($fp);
	$out=$filename;
	echo "uploads/AccountStatement".$account['ddocode'].'_'.$account['hoa'].'.txt';
?>
