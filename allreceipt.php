<?php

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

if(!isset($_POST['ddocode']) && !isset($_POST['hoa'])) {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>

	DDOCODE: <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp; HOA: <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp; <button type='submit'>GET ALL EXPENDITURES</button>
	</form> ";
} else {

	$ddocode = $_POST['ddocode'];
	$hoa = $_POST['hoa'];

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>

	DDOCODE: <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp; HOA: <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp; <button type='submit'>GET ALL EXPENDITURES</button>
	</form> ";

	echo "<table border='1'>
	<tr>
	<th>S.No.</th>
	<th>DDOCODE</th>
	<th>HOA</th>
	<th>Amount</th>
	<th>Transaction date</th>
	</tr>
	";
	$totalamt=0;
	$i=1;
	$result = pg_query("SELECT * FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' ORDER BY transdate DESC   ");
	while($row=pg_fetch_array($result)) {

		echo "<tr>
		<td>".$i."</td>
		<td>".$row['issueuser']."</td>
		<td>".$row['hoa']."</td>
		<td>".$row['partyamount']."</td>
		<td>".$row['transdate']."</td>
		</tr>";
		$totalamt = $totalamt+$row['partyamount'];
		$i++;
	}
	echo "Total: Rs. ".$totalamt;
	echo "</table>";

}