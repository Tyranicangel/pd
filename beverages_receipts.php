<?php
// set_time_limit(0);
 $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$q2=pg_fetch_array(pg_query("SELECT MAX(update) FROM datecheck"),null,PGSQL_ASSOC);
$date=$q2['max'];
pg_close($db1);

$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
$q5=pg_query("SELECT * FROM treceipts WHERE hoa ='8011001050001000000NVN' AND ddocode LIKE '06%' AND status='9' AND utimestamp<='$date'") or die(pg_last_error());

// // $x = pg_fetch_all($q5);
// // echo "<pre>";
// // print_r($x);
// // echo "</pre>";

pg_close($db2);
$db3= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
while($r3=pg_fetch_array($q5,null,PGSQL_ASSOC))
{
    $qdate=$r3['scrolldate'];
    $ddo='06010704001';//$r3['ddocode'];
    $hoa=$r3['hoa'];
//     if($hoa=='8443008000009000000NVN')
//     {
//             $ddo='27022304001';
//     }
    $amt=$r3['amount'];
    $chqno = $r3['transid'];
    $names=$r3['remittersname'];
    $imptxt="201516".$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
    if(trim($imptxt) != "201516") {
        $q55=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
        $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
        $bal=$q55['balance'];
        if(pg_num_rows($q6)==0)
        {
            $nbal=$bal+$amt;
//            // $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");
            echo "Done<br>";
           // $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'9')); 
            var_dump($q233);
        }
        // else
        // {

//         	echo "dasdasd<br>";
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];
            
//             // $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             // $tbal = $qt['balance'];
//             // $fbal=$tbal-$r6['partyamount'];
//             // $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             // $id=$r6['id'];
//             // $q7=pg_query("DELETE FROM transactions WHERE id=$id");

//             // $nbal=$fbal+$amt;
//             // $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             // $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));
//         }
    }
}
?>