<?php
//$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
// $qq=pg_query("SELECT * FROM users WHERE user_role=20 AND (username LIKE '2201%' OR username LIKE '2213%' OR username LIKE '2702%') ");
// 	$darr= array();
// 	echo pg_num_rows($qq);
//$q1=pg_query("ALTER TABLE locrequest ADD COLUMN admin_remarks character varying(500)  ");
//$qqq=pg_query("UPDATE pdaccountinfo SET balance=9999999999,loc=9999999999 WHERE ddocode='27029009008'");
//$rtoken = md5(microtime()."govt");
//pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc) VALUES ('osdifrbi','e10adc3949ba59abbe56e057f20f883e','govt',7,'$rtoken','Secretary') ");
//pg_query("ALTER TABLE locrequest ADD COLUMN purpose character varying(500) ");
//pg_query("UPDATE pdaccountinfo SET loc=9999999 WHERE ddocode='27029009008' AND hoa='9999999999999999999NVN' ");
// $q=pg_query("DELETE * FROM transactions WHERE issueuser='27029009008'");
// $q=pg_query("UPDATE transactions SET impstring='2015162702000000242511001' WHERE id=3735326");
// $q1=(pg_query("SELECT * FROM (SELECT COUNT(username),username FROM users GROUP BY username) AS a WHERE a.count>1"));
// $q1=pg_fetch_all(pg_query("SELECT * FROM transactions WHERE issueuser LIKE '06%' AND hoa='8011001050001000000NVN' AND transtype=2 AND confirmdate>='2015-04-01'"));
$ddo='22010704001';
// $qqq=pg_query("INSERT INTO pdaccountinfo (accountno,hoa,ddocode,balance,account_type,userid,obalance,areacode,activation,loc,category,transitamount,status) VALUES (123333,'8011001050001000000NVN','22010704001',0,'2','2201',0,'22',2,0,'A',0,1)") or die(pg_last_error());
//$q1=pg_fetch_all(pg_query("SELECT * FROM treceipts WHERE amount='133854784.00' AND ddocode='27020317001' AND hoa='8443001230001000000NVN' AND status='9' "));
// pg_query("UPDATE pdaccountinfo SET obalance=0 WHERE ddocode='10010704001'");
//pg_query("UPDATE pdaccountinfo SET loc=0 WHERE ddocode NOT LIKE '2702%' AND ddocode NOT LIKE '2213%' AND ddocode NOT LIKE '2201%' ");
//$q1 = pg_fetch_all(pg_query("SELECT * FROM transactions WHERE id=4702543 "));
//pg_query("UPDATE transactions SET transstatus='2' WHERE issueuser LIKE '0501%' AND chequeno = '010279'");
//pg_query("UPDATE pdaccountinfo SET account_type='1' WHERE ddocode NOT LIKE '2702%' AND hoa='8448001200022000000NVN' ");
//$q1 = pg_fetch_all(pg_query("UPDATE tpayments SET billstatus='7' WHERE hoa='8448001200022000000NVN' AND ddocode='22012210002' AND gross='113770' "));
//$q1 = pg_query("UPDATE transactions SET confirmdate=transdate WHERE confirmdate IS NULL AND transtype='2' ") or die(pg_last_error());
//$q1 = pg_fetch_all(pg_query("UPDATE transactions SET confirmdate=transdate WHERE confirmdate IS NULL AND transdate IS NOT NULL AND  transtype='2' AND transdate>='2015-04-01' "));
//$q1=pg_query("ALTER TABLE pdaccountinfo ADD COLUMN created_at TIMESTAMP ") or die(pg_last_error());
//$q1=pg_query("ALTER TABLE pdaccountinfo ADD COLUMN deleted_at TIMESTAMP ") or die(pg_last_error());
//pg_query("UPDATE tpayments SET billstatus='8' where transid='0000006121' AND ddocode='11012202011' ");
//pg_query("ALTER TABLE pdaccountinfo ADD COLUMN impact_obalance numeric ");
//$q1 = pg_fetch_all(pg_query("SELECT * FROM transactions where chequeno='030564'"));
$q1 = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo where ddocode='01020702001' "));
echo "<pre>";
print_r($q1);
echo "</pre>";
// pg_close();
// $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

// while($row = pg_fetch_array($q1)) {

// 	$transid = $row['transid'];
// 	$ddocode = $row['issueuser'];
// 	$hoa=$row['hoa'];
// 	$s = pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE ddocode='$ddocode' AND transid='$transid' AND hoa='$hoa'"));
// 	//pg_query("UPDATE tpayments SET billstatus='7' where ddocode='$ddocode' AND transid='$transid' ");
// 	echo "<pre>";
// 	print_r($s);
// 	echo "</pre>";
// }
// echo '<pre>';
// print_r($q1);
// echo '</pre>';
// $q1=pg_fetch_all(pg_query("SELECT * FROM transactions WHERE issueuser='22012202013' AND transtype='1' ORDER BY transdate DESC "));
// echo '<pre>';
// print_r($q1);
// echo '</pre>';
// var_dump($qqq);
// $q1 = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode LIKE '06%'AND hoa='8011001050001000000NVN'"));
//$q1 = pg_fetch_all(pg_query("SELECT * FROM transactions WHERE issueuser LIKE '2702%' AND transtype='2' AND transdate>='2015-06-01' LIMIT 10"));
//$q1=pg_query("SELECT * FROM (SELECT COUNT(ddocode),ddocode,hoa FROM pdaccountinfo GROUP BY ddocode,hoa) AS a WHERE count>1");
// $qq=pg_query("DELETE FROM transactions WHERE ddocode LIKE '06%' AND hoa='8011001050001000000NVN'");
// echo "<table border='1'>
// <tr>
// <th>id</th>
// <th>ddocode</th>
// <th>hoa</th>1
// <th>bal</th>
// <th>obal</th>
// <th>loc</th>
// <th>status</th>
// <th></th>
// </tr>";
// while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
// {
// 	$ddo=$r1['ddocode'];
// 	$hoa=$r1['hoa'];
// 	// echo '<table><tr><th>id</th><th>ddocode</th><th>hoa</th><th>obalance</th><th>balance</th><th>loc</th></tr><tr>';
// 	// echo '<td>'.$r1['id'].'</td>'
// 	$q2=pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$hoa'");
// 	while($row=pg_fetch_array($q2)) {

// 		echo "<tr>
// 		<td>".$row['id']."</td>
// 		<td>".$row['ddocode']."</td>
// 		<td>".$row['hoa']."</td>
// 		<td>".$row['balance']."</td>
// 		<td>".$row['obalance']."</td>
// 		<td>".$row['loc']."</td>
// 		<td>".$row['status']."</td>
// 		<td><form method='post' action='deletepdaccount.php' target='_blank'><input type='hidden' name='pdid' value='".$row['id']."'><button type='submit'>Delete</button></form></td>
// 		</tr>";
// 	}

		
// }
// echo "</table>";
?>