// <?php
//     $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

//     $q1=pg_query("SELECT DISTINCT(hoa) FROM pdaccountinfo ORDER BY hoa");
//     $txt="(";
//     while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
//     {
//         $txt=$txt."'".$r1['hoa']."',";
//     }
//     $txt=substr($txt,0,-1);
//     $txt=$txt.")";

//     $q2=pg_fetch_array(pg_query("SELECT MAX(update) FROM datecheck"),null,PGSQL_ASSOC);
//     $date=$q2['max'];
  
//     pg_close($db1);

//     $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

//     $qt=pg_fetch_all(pg_query("SELECT MAX(utimestamp) FROM treceipts"));
//     $mt=$qt[0]['max'];

    

//     $q3=pg_query("SELECT * FROM treceipts WHERE hoa in $txt AND status='9' AND utimestamp>'$date' AND utimestamp<='$mt' AND (ddocode LIKE '2702%' OR ddocode LIKE '2201%' OR ddocode LIKE '2213%') AND hoa !='8443008000009000000NVN' ");
//     $q4=pg_query("SELECT * FROM tpayments WHERE hoa in $txt AND billstatus='9' AND formno!='chq' AND utimestamp>'$date' AND utimestamp<='$mt' AND (ddocode LIKE '2702%' OR ddocode LIKE '2201%' OR ddocode LIKE '2213%')");
//     $q5=pg_query("SELECT * FROM treceipts WHERE hoa ='8443008000009000000NVN' AND status='9' AND utimestamp>'$date' AND utimestamp<='$mt'");
//     pg_close($db2);
//     $db3=  pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

//     $qtu=pg_query("INSERT INTO datecheck (update) VALUES ('$mt')");
//     while($r3=pg_fetch_array($q5,null,PGSQL_ASSOC))
//     {
//         $qdate=$r3['scrolldate'];
//         $ddo=$r3['ddocode'];
//         $hoa=$r3['hoa'];
//         if($hoa=='8443008000009000000NVN')
//         {
//                 $ddo='27022304001';
//         }
//         $amt=$r3['amount'];
//         $chqno = $r3['transid'];
//         $names=$r3['remittersname'];
//         $imptxt=$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
//         $q55=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
//         $bal=$q55['balance'];
//         if(pg_num_rows($q6)==0)
//         {
//             $nbal=$bal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));         
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];
            
//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal-$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");

//             $nbal=$fbal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));
//         }
//     }
//     while($r3=pg_fetch_array($q3,null,PGSQL_ASSOC))
//     {
//         $qdate=$r3['scrolldate'];
//         $ddo=$r3['ddocode'];
//         $hoa=$r3['hoa'];
//         if($hoa=='8443008000009000000NVN')
//         {
//                 $ddo='27022304001';
//         }
//         $amt=$r3['amount'];
//         $chqno = $r3['transid'];
//         $names=$r3['remittersname'];
//         $imptxt=$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
//         $q5=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
//         $bal=$q5['balance'];
//         if(pg_num_rows($q6)==0)
//         {
//             $nbal=$bal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));         
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];
            
//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal-$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");

//             $nbal=$fbal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));
//         }
//     }
//    while($r4=pg_fetch_array($q4,null,PGSQL_ASSOC))
//     {
//         $qdate=$r4['scrolldate'];
//         $ddo=$r4['ddocode'];
//         $hoa=$r4['hoa'];
//         $amt=$r4['gross'];
//         $chqno = $r4['transid'];
//         $imptxt=$r4['stocode'].$r4['transid'].$r4['transtype'];
//         $flg = 0;
//         $q5=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='1'");
//         $bal=$q5['balance'];
//         if(pg_num_rows($q6)==0)
//         {
         
//          	$nbal=$bal-$amt;
//        		$q71=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//         	$q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());   
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];

//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal+$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");
//             $nbal=$fbal-$amt;
//        		$q71=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//         	$q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());

//         }
//     }

//     pg_close($db3);

//     // $date = "2015-04-01";



//     $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

//     // $qt=pg_fetch_all(pg_query("SELECT MAX(utimestamp) FROM treceipts"));
//     // $mt=$qt[0]['max'];

    

//     $q3=pg_query("SELECT * FROM treceipts WHERE hoa in $txt AND status='9' AND utimestamp>'$date' AND utimestamp<='$mt' AND (ddocode LIKE '2702%' OR ddocode LIKE '2201%' OR ddocode LIKE '2213%') AND hoa !='8443008000009000000NVN' ");
//     $q4=pg_query("SELECT * FROM tpayments WHERE hoa in $txt AND billstatus='9' AND formno!='chq' AND utimestamp>'$date' AND utimestamp<='$mt' AND (ddocode LIKE '2702%' OR ddocode LIKE '2201%' OR ddocode LIKE '2213%')");
//     $q5=pg_query("SELECT * FROM treceipts WHERE hoa ='8443008000009000000NVN' AND status='9' AND utimestamp>'$date' AND utimestamp<='$mt'");
//     pg_close($db2);
//     $db3=  pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

//     $qtu=pg_query("INSERT INTO datecheck (update) VALUES ('$mt')");
//     while($r3=pg_fetch_array($q5,null,PGSQL_ASSOC))
//     {
//         $qdate=$r3['scrolldate'];
//         $ddo=$r3['ddocode'];
//         $hoa=$r3['hoa'];
//         if($hoa=='8443008000009000000NVN')
//         {
//                 $ddo='27022304001';
//         }
//         $amt=$r3['amount'];
//         $chqno = $r3['transid'];
//         $names=$r3['remittersname'];
//         $imptxt=$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
//         $q55=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
//         $bal=$q55['balance'];
//         if(pg_num_rows($q6)==0)
//         {
//             $nbal=$bal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));         
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];
            
//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal-$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");

//             $nbal=$fbal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));
//         }
//     }
//     while($r3=pg_fetch_array($q3,null,PGSQL_ASSOC))
//     {
//         $qdate=$r3['scrolldate'];
//         $ddo=$r3['ddocode'];
//         $hoa=$r3['hoa'];
//         if($hoa=='8443008000009000000NVN')
//         {
//                 $ddo='27022304001';
//         }
//         $amt=$r3['amount'];
//         $chqno = $r3['transid'];
//         $names=$r3['remittersname'];
//         $imptxt=$r3['stocode'].$r3['transid'].$r3['transtype'].$r3['transidslno'];
//         $q5=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='2'");
//         $bal=$q5['balance'];
//         if(pg_num_rows($q6)==0)
//         {
//             $nbal=$bal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));         
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];
            
//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal-$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");

//             $nbal=$fbal+$amt;
//             $q7=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18)",array(2,$qdate,$chqno,$names,'n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,$nbal,$imptxt,'1'));
//         }
//     }
//    while($r4=pg_fetch_array($q4,null,PGSQL_ASSOC))
//     {
//         $qdate=$r4['scrolldate'];
//         $ddo=$r4['ddocode'];
//         $hoa=$r4['hoa'];
//         $amt=$r4['gross'];
//         $chqno = $r4['transid'];
//         $imptxt=$r4['stocode'].$r4['transid'].$r4['transtype'];
//         $flg = 0;
//         $q5=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa ='$hoa'"),null,PGSQL_ASSOC);
//         $q6=pg_query("SELECT * FROM transactions WHERE impstring='$imptxt' AND transtype='1'");
//         $bal=$q5['balance'];
//         if(pg_num_rows($q6)==0)
//         {
         
//             $nbal=$bal-$amt;
//             $q71=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());   
//         }
//         else
//         {
//             $r6=pg_fetch_array($q6,null,PGSQL_ASSOC);
//             $tddo=$r6['issueuser'];
//             $thoa=$r6['hoa'];

//             $qt=pg_fetch_array(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$tddo' AND hoa = '$thoa'"));
//             $tbal = $qt['balance'];
//             $fbal=$tbal+$r6['partyamount'];
//             $q9=pg_query("UPDATE pdaccountinfo SET balance=$fbal WHERE ddocode='$tddo' AND hoa='$thoa'");

//             $id=$r6['id'];
//             $q7=pg_query("DELETE FROM transactions WHERE id=$id");
//             $nbal=$fbal-$amt;
//             $q71=pg_query("UPDATE pdaccountinfo SET balance=$nbal WHERE ddocode='$ddo' AND hoa='$hoa'");

//             $q233=pg_query_params($db3,"INSERT INTO transactions (transtype,transdate,chequeno,partyname,partyacno,partybank,partyifsc,partyamount,issueuser,hoa,multiflag,partybranch,transstatus,purpose,confirmdate,balance,chqflag,impstring,impactflag) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19)",array(1,$qdate,$chqno,'n/a','n/a','n/a','n/a',$amt,$ddo,$hoa,1,'n/a',3,'n/a',$qdate,0,$flg,$imptxt,'1')) or die(pg_last_error());

//         }
//     }
// ?>