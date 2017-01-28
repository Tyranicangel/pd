<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$fp=fopen('CPS_001 and 002 Balances.csv','r');
	$i=0;
	$partydata=array();
	while($data=fgetcsv($fp)){

		if($i>0){

			if(strlen($data[2]) == 10) {

				$data[2] = "0".$data[2];
			}
		
			$partydata[$i]['ddocode'] = trim($data[2]);
			$partydata[$i]['hoa'] = trim($data[3]);
			$partydata[$i]['obal'] = $data[4];
		}

		$i++;
		
	}

	// echo "<pre>";
	// print_r($partydata);
	// echo "</pre>";
	


	foreach ($partydata as $value) {
		//echo "SELECT * FROM pdaccountinfo WHERE ddocode='0".$value['ddocode']."' AND hoa='".$value['hoa']."' <br>";
		$x = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='".$value['ddocode']."' AND hoa='".$value['hoa']."' ");
		//$x = pg_query("SELECT * FROM users WHERE username='".$value['ddocode']."' ");
		if(pg_num_rows($x) == 0) {

			echo "<pre>";
			print_r($value);
			echo "</pre>";
		} else {


			while($row=pg_fetch_array($x)) {

				//if(substr($row['ddocode'], 2,2) == "01") {

					$ddocode = $row['ddocode'];
					$hoa = $row['hoa'];
					$obalance = $row['obalance'];
					$transitamt = $row['transitamount'];
					$balance = $row['balance'];
					$resultrcpt = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' AND confirmdate >= '2015-04-01' "));
					$totrcpt = $resultrcpt['sum'];

					$resultexp = pg_fetch_array(pg_query("SELECT SUM(partyamount) FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='1' AND transstatus='3' AND confirmdate >= '2015-04-01' "));

					$totexp = $resultexp['sum'];

					$prebal1 = $obalance+$totrcpt;
					$prebal2 = $transitamt+$totexp;
					
					$finalbal = $prebal1-$prebal2;
					
						echo "tddocode>> ".$ddocode."======hoa>> ".$hoa."========Pdacinfo balance>> ".$balance."======calculated balance>> ".$finalbal."=======transitamt>> ".$transitamt."======obalance>> ".$obalance."<br>";

					if($balance != $finalbal) {
						//pg_query("UPDATE pdaccountinfo SET balance='$finalbal' WHERE ddocode='$ddocode' AND hoa='$hoa' ");
						//echo "ddocode>> ".$ddocode."======hoa>> ".$hoa."========Pdacinfo balance>> ".$balance."======calculated balance>> ".$finalbal."=======transitamt>> ".$transitamt."======obalance>> ".$obalance."<br>";
					}
				//}
			}

		}
	}


?>