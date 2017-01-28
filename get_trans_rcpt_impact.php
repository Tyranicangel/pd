<?php
    $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

    $hoas=pg_query("SELECT DISTINCT(ddocode), hoa FROM pdaccountinfo WHERE status='1' AND ddocode NOT LIKE '2702%' AND ddocode NOT LIKE '2201%' AND ddocode NOT LIKE '2213%' ") or die(pg_last_error());
    while($rowhoa = pg_fetch_array($hoas)) {

        if(substr($rowhoa['ddocode'], 2, 2) == '01') {

        	$hoaarr[] = $rowhoa['hoa'];
            $ddoarr[] = $rowhoa['ddocode'];
        }
    }

    $ddocodess = implode("','", $ddoarr);
    $ddocodess = "('".$ddocodess."')";

    $hoass = implode("','", $hoaarr);
    $hoass = "('".$hoass."')";

    pg_close($db1);

    $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());


    $impreceipt=pg_query("SELECT * FROM treceipts WHERE hoa IN $hoass AND ddocode IN $ddocodess AND status='9' AND hoa !='8443008000009000000NVN' ");
    $impexp=pg_query("SELECT * FROM tpayments WHERE hoa IN $hoass AND ddocode IN $ddocodess AND billstatus='9' ");

    pg_close($db2);


    $db3= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
 
    while($r3=pg_fetch_array($impreceipt,null,PGSQL_ASSOC))
    {
        $qdate=$r3['scrolldate'];
        $ddo=$r3['ddocode'];
        $hoa=$r3['hoa'];
        
        $amt=$r3['amount'];
        $chqno = $r3['transid'];
        $names=$r3['remittersname'];
        $imptxt="201516".$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
        if(trim($imptxt) != "") {

            $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
            $bal=0;
            if(pg_num_rows($q6)==0)
            {

                echo $ddo."-".$r3['stocode']."=".$r3['transid']."<br>";
                $nbal=$bal+$amt;

                //$q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));         
            }
            else
            {
               echo $imptxt."<br>";
            }
        }
    } 

    while($r4=pg_fetch_array($impexp,null,PGSQL_ASSOC))
    {
        $qdate=$r4['scrolldate'];
        $ddo=$r4['ddocode'];
        $hoa=$r4['hoa'];
        $amt=$r4['gross'];
        $chqno = $r4['transid'];
        $imptxt="201516".$r4['stocode'].$r4['transid'].$r4['transtype'];
        $flg = 0;
        $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='1'");
        $bal=0;
        if(pg_num_rows($q6)==0)
        {
            echo $ddo."-".$r4['stocode']."<br>";
         
            $nbal=$bal-$amt;
            //$q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());   
        }
        else
        {
            echo $imptxt."<br>";
        }
    }
            
?>