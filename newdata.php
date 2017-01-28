<?php
	$date='';
	$dbc=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
  //  $q1= pg_query("DELETE FROM transactions WHERE transtype = '2' AND hoa = '8443008000009000000NVN' AND transdate>='2014-08-01'");
	//var_dump($q1);

    $qw = pg_fetch_array(pg_query("SELECT MAX(update) FROM datecheck"),null,PGSQL_ASSOC);
	$qdate = $qw['max'];
	$q1=pg_query("SELECT DISTINCT(hoa) FROM pdaccountinfo WHERE ddocode like '2702%' ORDER BY hoa");
	$txt="(";
	while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
	{
		$txt=$txt."'".$r1['hoa']."',";
	}
	$txt=substr($txt,0,-1);
	$txt=$txt.")";
	pg_close($dbc);
////	//$db2=pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

    
//	$q4q="SELECT * FROM tpayments WHERE billstatus='9' AND utimestamp>'$qdate' AND ddocode like '2702%' AND hoa in ".$txt." ORDER BY transid";
	$q5q="SELECT * FROM treceipts WHERE status='9' AND hoa in ".$txt." AND ddocode like '2702%' AND hoa!= '8443008000009000000NVN' AND scrolldate>='2014-10-01' AND utimestamp<='$qdate' ORDER BY transid";
	//$q6q="SELECT * FROM treceipts WHERE status='9' AND scrolldate>='2014-11-01' AND utimestamp<'$qdate' AND hoa='8443008000009000000NVN' ORDER BY transid";
//	$qt=pg_fetch_all(pg_query("SELECT MAX(utimestamp) FROM treceipts"));
//	$mt=$qt[0]['max'];
//	$q4=pg_query($q4q);
	$q5=pg_query($q5q);
    //$q6=pg_query($q6q);
	pg_close($db2);

	$db3=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
//	$qtu=pg_query("INSERT INTO datecheck (update) VALUES ('$mt')");
	while($r5=pg_fetch_array($q5,null,PGSQL_ASSOC))
	{
		$ddo=$r5['ddocode'];
                $ucode=substr($ddo,0,4);
                $dhoa=$r5['hoa'];
                if($dhoa=='8443008000009000000NVN')
                {
                        $ddo='27022304001';
			$ucode = '2702';
                }
                $chqno=$r5['transid'];
                $amt=$r5['amount'];
                $names=$r5['remittersname'];
                $rdate=$r5['scrolldate'];
                {
                       // $q231=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$dhoa'"),null,PGSQL_ASSOC);
                        //$obal=intval($q231['balance']);
                      //  $nbal=intval($obal+$amt);
                        $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16)",array(2,$rdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$dhoa,1,'n/a',3,'n/a',$rdate,'1'));
                        //$q235=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$dhoa'");
			var_dump($q233);
                }
	}
	
    // while($r5=pg_fetch_array($q6,null,PGSQL_ASSOC))
 //        {
 //                $dhoa='8443008000009000000NVN';
 //                $ddo='27022304001';
 //                $ucode = '2702';
 //                $chqno=$r5['transid'];
 //                $amt=$r5['amount'];
 //                $names=$r5['remittersname'];
 //                $rdate=$r5['scrolldate'];
 //                {
 //                       // $q231=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$dhoa'"),null,PGSQL_ASSOC);
 //                       // $obal=intval($q231['balance']);
 //                       // $nbal=intval($obal+$amt);
 //                        $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16)",array(2,$rdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$dhoa,1,'n/a',3,'n/a',$rdate,'1'));
 //                        //$q235=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$dhoa'");
 //               		var_dump($q233);
	// 	 }
 //        }

/*	while($r13=pg_fetch_array($q4,null,PGSQL_ASSOC))
        {
                $ddo=$r13['ddocode'];
                $ucode=substr($ddo,0,4);
                $dhoa=$r13['hoa'];
                $chqno=$r13['transid'];
                $flg='0';
                if($r13['formno']=='chq')
                {
                        $flg='1';
                }
                $amt=$r13['gross'];
                $q231=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$dhoa'"),null,PGSQL_ASSOC);
                $atype=$q321['account_type'];
                $oloc=$q231['loc'];
                $nloc=$oloc-$amt;
                $obal=$q231['balance'];
                $nbal=$obal-$amt;
                $udate=$r13['scrolldate'];
                {
			if($flg=='0')
                        {

	                        $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17)",array(1,$udate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$dhoa,1,'n/a',3,'n/a',$udate,0,$flg)) or die(pg_last_error());
                        	$q235=pg_query_params($db3,"UPDATE pdaccountinfo SET balance=$1 WHERE ddocode='$ddo' AND hoa='$dhoa'",array($nbal));
                        }
                }
        }*/
?>

