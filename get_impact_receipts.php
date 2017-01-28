<?php
    // $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

    // $lapshoa=pg_query("SELECT DISTINCT(hoa) FROM pdaccountinfo WHERE lapsableflag='1' ORDER BY hoa");

    // while($rowlapshoa = pg_fetch_array($lapshoa)) {

    // 	$lapshoaarr[] = $rowlapshoa['hoa'];
    // }

    // $lapshoas = implode("','", $lapshoaarr);
    // $lapshoas = "('".$lapshoas."')";

    // pg_close($db1);

	$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
	
    $imreceipts = pg_query("SELECT * FROM treceipts WHERE hoa='8443008000009000000NVN' AND status='9' ORDER BY scrolldate LIMIT 10000 OFFSET 120000  "); 

    pg_close($db2);

    $db3= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());


    while($r3=pg_fetch_array($imreceipts)) {

        $qdate=$r3['scrolldate'];
        //$ddo=$r3['ddocode'];
        $ddo='27022304001';
        $hoa=$r3['hoa'];
       
        $amt=$r3['amount'];
        $chqno = $r3['transid'];
        $names=$r3['remittersname'];
        $imptxt="201516".$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
        $q5=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
        $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND issueuser='$ddo' AND hoa='$hoa' AND transtype='2'");
        $bal=$q5['balance'];
        if(pg_num_rows($q6)==0)
        {
            $nbal=$bal+$amt;
            //$q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

            //$q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impactflag, impstring) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,'1', $imptxt));  
            
        }  else {

            echo $chqno."<br>";
        }   
            
    }
?>