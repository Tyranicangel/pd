<?php

	if(!isset($_POST['stocode'])) {

		echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >

			STOCODE: <input type='text' name='stocode' /> &nbsp; &nbsp; &nbsp; &nbsp; <button type='submit'>SUBMIT</button>
		</form>";

	} else {

		echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >

		STOCODE: <input type='text' name='stocode' /> &nbsp; &nbsp; &nbsp; &nbsp; <button type='submit'>SUBMIT</button>
		</form>";

		echo "<table border='1' cellpadding='5' >
		<tr>
		<th>S.No</th>
		<th>DDOCODE</th>
		<th>HOA</th>
		<th>OUR BALANCE</th>
		<th>IMPACT BALANCE</th>
		<th>ACTION</th>
		</tr>";

		$stocode = $_POST['stocode'];

		$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

		$resultour = pg_query("SELECT * FROM pdaccountinfo WHERE hoa != '8443008000009000000NVN' AND ddocode LIKE '$stocode%' ");
		$balArr = array();
		while($rowour = pg_fetch_array($resultour)) {

			$balArr[$rowour['ddocode']][$rowour['hoa']] = $rowour['balance'];
		}

		$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

		$k=1;

		foreach ($balArr as $key => $value) {
			
			foreach ($value as $inkey => $invalue) {
				
				$ddocode = $key;
				$hoa = $inkey;
				$balance = $invalue;

				$tpayrow=pg_fetch_array(pg_query("SELECT SUM(gross) FROM tpayments WHERE hoa ='$hoa' AND billstatus='9' AND ddocode = '$ddocode' AND scrolldate >= '2014-06-02' "));
				$paysum = round($tpayrow['sum']);

				$trecptrow=pg_fetch_array(pg_query("SELECT SUM(amount) FROM treceipts WHERE hoa ='$hoa' AND status='9' AND ddocode = '$ddocode' AND scrolldate >= '2014-06-02' "));
				$recptsum = round($trecptrow['sum']);

				$obalrow = pg_fetch_array(pg_query("SELECT obamt FROM mpdddohoa WHERE ddocode='$ddocode' AND hoa='$hoa' "));
				$obal = round($obalrow['obamt']);

				$balpre = $obal+$recptsum;

				$mainbal = $balpre - $paysum;

				if($mainbal < $balance) {

					$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

					$resultour = pg_query("SELECT * FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transstatus='3' ");
					$chqArr = array();
					while($rowour = pg_fetch_array($resultour)) {

						if(strlen($rowour['chequeno']) > 6) {

							$chqArr[] = $rowour['chequeno'];
						}
					}

					$implodechq = implode("','", $chqArr);
					$chqArrQuery = "('".$implodechq."')";

					$db2= pg_connect("host=10.10.24.16 dbname=ap_impact1415_n user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

					$q=pg_query("SELECT * FROM tpayments WHERE hoa ='$hoa' AND billstatus='9' AND formno='chq' AND ddocode = '$ddocode' AND transid NOT IN $chqArrQuery ") or die(pg_last_error());
					$checkmanual = 0;
					if(pg_num_rows($q) > 0) {

						$checkmanual = 1;
					}

					if($checkmanual == 1) {

						echo "<tr>
						<td>".$k."</td>
						<td>".$ddocode."</td>
						<td>".$hoa."</td>
						<td>".$balance."</td>
						<td>".$mainbal."</td>
						<td><form method='post' action='check_one_one_chq_in_both.php' target='_blank' >
							<input type='hidden' name='ddocode' value='$ddocode' />
							<input type='hidden' name='hoa' value='$hoa' />
							<button type='submit'>Check Manual Cheques</button>
							</form>
						</td>
						</tr>";
						$k++;
					}

					//echo $k.") DDOCODE=> ".$ddocode.",  HOA=> ".$hoa.",  Our Balance=>".$balance.",  Impact Balance=> ".$mainbal."<br>";
					
				}

			}
		}

		echo "</table>";
		
	}

?>