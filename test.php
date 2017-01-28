<?php
$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
// $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
// $qq=pg_query("SELECT * FROM users WHERE user_role=20 AND (username LIKE '2201%' OR username LIKE '2213%' OR username LIKE '2702%') ");
// 	$darr= array();
// 	echo pg_num_rows($qq);
// $q1=pg_query("ALTER TABLE ofctype ADD COLUMN book_pass_auth character varying(100)");
// $qqq=pg_query("UPDATE pdaccountinfo SET balance=15552581 WHERE ddocode='09012202003' AND hoa='8448001090003001000NVN'");
//$rtoken = md5(microtime()."govt");
//pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc) VALUES ('osdifrbi','e10adc3949ba59abbe56e057f20f883e','govt8',7,'$rtoken','Secretary') ");
//pg_query("ALTER TABLE locrequest ADD COLUMN purpose character varying(500) ");
//pg_query("UPDATE pdaccountinfo SET loc=9999999 WHERE ddocode='27029009008' AND hoa='9999999999999999999NVN' ");
// $q=pg_query("UPDATE transactions SET impstring='2015162702000000242511001' WHERE id=3735326");
// $q1=(pg_query("SELECT * FROM (SELECT COUNT(username),username FROM users GROUP BY username) AS a WHERE a.count>1"));8443-00-116-00-08-000-000-NVN
// $q1=pg_fetch_all(pg_query("SELECT * FROM transactions WHERE issueuser LIKE '06%' AND hoa='8011001050001000000NVN' AND transtype=2 AND confirmdate>='2015-04-01'"));
// $qqq=pg_query("INSERT INTO pdaccountinfo (accountno,hoa,ddocode,balance,account_type,userid,obalance,areacode,activation,loc,category,transitamount,status) VALUES (123333,'8443001040001000000NVN','08010702001',0,'2','0801',0,'08',2,0,'A',0,0)") or die(pg_last_error());
//$q1=pg_fetch_all(pg_query("SELECT * FROM treceipts WHERE amount='133854784.00' AND ddocode='27020317001' AND hoa='8443001230001000000NVN' AND status='9' "));
// pg_query("UPDATE pdaccountinfo SET obalance=0 WHERE ddocode='10010704001'");
// pg_query("UPDATE pdaccountinfo SET balance=461296 WHERE ddocode ='10011603017'");
// $q1 = pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE ddocode LIKE'1101%' AND transid='0000004981'"));
// echo '<pre>';
// print_r($q1);
// echo '</pre>';4456, 4457, 4452, 4451, 4449, 4464, 4453, 4454, 4455
// $qqqq=pg_query("UPDATE tpayments SET billstatus=7 WHERE ddocode LIKE '2201%' AND transid='0000004455'");
// $q1=pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE transid='0000004535'"));
// $rr=pg_query("UPDATE transactions SET partyamount=2142858 WHERE id=4656369");
// $rr2=pg_query("UPDATE transactions SET partyamount=0 WHERE id=4656634");
// $q1=pg_fetch_all(pg_query("SELECT * FROM mpdddohoa WHERE ddocode='08010702001'"));
// $q1=pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo where ddocode='10011603017'"));
// $fp=fopen('pdl.csv','r');
// while($data=fgetcsv($fp))
// {
// 	$ddo=$data[0];
// 	$hoa=$data[1];
// 	$q=pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='$hoa'");
// 	var_dump($q);
// }
// $q1=pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE formno='chq' AND billstatus='7' AND banksentdate > '2015-04-01'"));
$q1=pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE transid='0000004464' AND stocode = '2201'"));
echo '<pre>';
print_r($q1);
echo '</pre>';
// while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
// {
// 	$dd=$r1['username'].'%';
// 	$q2=pg_fetch_array(pg_query("SELECT COUNT(*) FROM pdaccountinfo WHERE ddocode LIKE '$dd' AND status != '0'"),null,PGSQL_ASSOC);
// 	if($q2['count']==0)
// 	{
// 		echo '<pre>';
// 		print_r(array($r1['username'],$r1['userdesc']));
// 		echo '</pre>';
// 	}
// }
// echo '<pre>';
// print_r($q1);
// echo '</pre>';
// pg_close($db1);
// $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());
// $q1=pg_fetch_all(pg_query("SELECT * FROM tpayments WHERE ddocode = '27022304001' AND hoa LIKE '8%' AND billstatus='9' AND scrolldate>='2015-04-01' AND formno='chq'"));
// echo '<pre>';
// print_r($q1);
// echo '</pre>';
// var_dump($qqq);
// $q1 = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode LIKE '06%'AND hoa='8011001050001000000NVN'"));
//$q1 = pg_fetch_all(pg_query("SELECT * FROM transactions WHERE issueuser LIKE '2702%' AND transtype='2' AND transdate>='2015-06-01' LIMIT 10"));
//$q1=pg_query("SELECT * FROM (SELECT COUNT(ddocode),ddocode,hoa FROM pdaccountinfo GROUP BY ddocode,hoa) AS a WHERE count>1");
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