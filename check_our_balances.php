<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
if(!isset($_POST['ddocode'])) {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>

		DDOCODE: <input type='text' name='ddocode' />
		<button type='submit'>UPDATE BALANCE</button>
	</form>";
} else {
$ddoc = $_POST['ddocode'];
//$q = pg_fetch_all(pg_query("SELECT * FROM users WHERE username='secif' "));
$q = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode LIKE '09012202003'") or die(pg_last_error());
// echo "<pre>";
// print_r(pg_fetch_all($q));
// echo "</pre>";

while($row=pg_fetch_array($q)) {

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
		
			// echo "tddocode>> ".$ddocode."======hoa>> ".$hoa."========Pdacinfo balance>> ".$balance."======calculated balance>> ".$finalbal."=======transitamt>> ".$transitamt."======obalance>> ".$obalance."<br>";

		if($balance != $finalbal) {
			// pg_query("UPDATE pdaccountinfo SET balance='$finalbal' WHERE ddocode='$ddocode' AND hoa='$hoa' ");
			echo "ddocode>> ".$ddocode."======hoa>> ".$hoa."========Pdacinfo balance>> ".$balance."======calculated balance>> ".$finalbal."=======transitamt>> ".$transitamt."======obalance>> ".$obalance."<br>";
		}
	//}
}

}
?>