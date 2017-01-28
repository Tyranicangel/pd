<?php
set_time_limit(0);
//
// if(!isset($_POST['stocode'])) {

// 	echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >
// 	STOCODE: <input type='text' name='stocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
// 	<button type='submit'>CHECK</button>
// 	</form>";
// } else {

	//$stocode = $_POST['stocode'];

	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	$respd = pg_fetch_array(pg_query("SELECT MAX(accountno) FROM pdaccountinfo "));
	$accountno = $respd['max'];

	
	pg_close($db1);
	$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

	

	$result = pg_query("SELECT * FROM mpdddohoa WHERE ddocode='06011603024' AND hoa='8443001160003000000NVN' ") or die(pg_last_error());
	$modify_date = "2015-04-22";
	$addQuery = array();
	$i=1;
	echo "count-".pg_num_rows($result); echo "<br>";
	while($row=pg_fetch_array($result)) {

			$stocode = $row['stocode'];

			$accountno++;

			$ddocode = $row['ddocode'];
			$hoa = $row['hoa'];
			
			$category = $row['catg'];
			$obamt = $row['obamt'];
			$recamt = $row['recamt'];
			$expamt = $row['expamt'];
			$transitamt = $row['trasitamt'];
			$areacode = substr($stocode, 0,2);

			$totalbal = $obamt+$recamt;
			$totalexp = $expamt;

			$balnow = $totalbal-$totalexp;
			

			$resultloccount = pg_fetch_array(pg_query("SELECT count(*) FROM mpdloc WHERE ddocode='$ddocode' AND hoa='$hoa' "));

			if($resultloccount['count'] > 0) {


				$account_type = 2;
				$locnow = 0;
			} else {
				

				$account_type = 1;
				$locnow = 0;
			}

			$addQuery[$i]['hoa'] = $hoa;
			$addQuery[$i]['ddocode'] = $ddocode;
			$addQuery[$i]['accountno'] = $accountno;
			$addQuery[$i]['balnow'] = $balnow;
			$addQuery[$i]['account_type'] = $account_type;
			$addQuery[$i]['stocode'] = $stocode;
			$addQuery[$i]['obamt'] = $obamt;
			$addQuery[$i]['areacode'] = $areacode;
			$addQuery[$i]['activation'] = 2;
			$addQuery[$i]['locnow'] = $locnow;
			$addQuery[$i]['category'] = $category;
			$i++;

	}

	echo "<pre>";
	print_r($addQuery);
	echo "</pre>";

pg_close($db2);
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$addQueryInsert = array();
foreach ($addQuery as $value) {

	$ddocode = $value['ddocode'];
	$hoa = $value['hoa'];
	$balnow = $value['balnow'];
	$locnow = $value['locnow'];
	$accountno = $value['accountno'];
	$account_type = $value['account_type'];
	$category = $value['category'];
	$obamt = $value['obamt'];
	$areacode = $value['areacode'];
	$stocode = $value['stocode'];

	// $checkuser = pg_query("SELECT * FROM users WHERE username='$stocode' ");
	// if(pg_num_rows($checkuser) == 0) {

	// 	$refreshtoken = md5(microtime());

	// 	pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc,active_flag) VALUES ('$stocode', 'e10adc3949ba59abbe56e057f20f883e', '$stocode', 8, '$refreshtoken', 'STO/DTO',1) ");
	// } else {

	// 	echo "<h4>user already exists!</h4>";
	// }
	

	$pdcheck = pg_fetch_array(pg_query("SELECT count(*) FROM pdaccountinfo WHERE ddocode='$ddocode' AND hoa='$hoa' "));

	if($pdcheck['count'] > 0) {
		
		//pg_query("UPDATE pdaccountinfo SET balance=$balnow,obalance=$obamt,loc=$locnow WHERE ddocode='$ddocode' AND hoa='$hoa' ");

	} else {
		

		$addQueryInsert[] = "'".$accountno."','".$hoa."','".$ddocode."',".$balnow.",".$account_type.",'".$stocode."',".$obamt.",'".$areacode."',2,'".$locnow."','".$category."'";				
	}
}
	if(count($addQueryInsert) > 0) {
	 	$joinquery = implode("),(", $addQueryInsert);

		echo $mainQuery = "(".$joinquery.")";

		//pg_query("INSERT INTO pdaccountinfo (accountno, hoa, ddocode, balance, account_type, userid, obalance, areacode, activation, loc, category) VALUES ".$mainQuery." ") or die(pg_last_error());
	}

//}



?>