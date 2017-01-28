<?php

//
if(!isset($_POST['stocode'])) {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >
	STOCODE: <input type='text' name='stocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
	<button type='submit'>CHECK</button>
	</form>";
} else {

	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	$respd = pg_fetch_array(pg_query("SELECT MAX(accountno) FROM pdaccountinfo "));
	$accountno = $respd['max'];

	pg_close($db1);
	$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

	$stocode = $_POST['stocode'];

	$result = pg_query("SELECT * FROM mpdddohoa WHERE stocode='$stocode' ");
	$modify_date = "2015-03-20";
	$addQuery = array();
	$i=1;

	while($row=pg_fetch_array($result)) {


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
		

		$resultloc = pg_fetch_array(pg_query("SELECT * FROM mpdloc WHERE ddocode='$ddocode' AND hoa='$hoa' "));
		$resultloccount = pg_fetch_array(pg_query("SELECT count(*) FROM mpdloc WHERE ddocode='$ddocode' AND hoa='$hoa' "));

		if($resultloccount['count'] > 0) {


			$account_type = 2;
			$loc = $resultloc['locamt'];

			$locnow = $loc-$resultloc['expamt'];
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

	pg_close($db2);
	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	$addQueryInsert = array();
	echo "<h3>".$stocode."</h3>";
	echo "<table border='1' cellpadding='5'>
	<tr>
	<th>S.No</th>
	<th>DDOCODE</th>
	<th>HOA</th>
	<th>Opening Balance</th>
	<th>Balance</th>
	<th>LOC Balance</th>
	</tr>";
	$x=1;
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

		

		$pdcheck = pg_fetch_array(pg_query("SELECT count(*) FROM pdaccountinfo WHERE ddocode='$ddocode' AND hoa='$hoa' "));

		if($pdcheck['count'] > 0) {
			
			//pg_query("UPDATE pdaccountinfo SET balance=$balnow, loc=$locnow WHERE ddocode='$ddocode' AND hoa='$hoa' ");

		} else {

			echo "<tr>
			<td>".$x."</td>
			<td>".$ddocode."</td>
			<td>".$hoa."</td>
			<td>".$obamt."</td>
			<td>".$balnow."</td>
			<td>".$locnow."</td>
			</tr>";	
			$x++;

			//$addQueryInsert[] = "'".$accountno."','".$hoa."','".$ddocode."',".$balnow.",".$account_type.",'".$stocode."',".$obamt.",'".$areacode."',2,'".$locnow."','".$category."'";				
		}
	}

}



?>