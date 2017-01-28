<?php
	set_time_limit(0);
    include('connect.php');
$hoa=$_GET['hoa'];
$ddo=$_GET['ddo'];
echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Cheques Rejected HOA-wise Report</title></head>";
    $result = pg_query("SELECT chequeno,partyname,issueuser,hoa,partyamount,transdate FROM transactions WHERE transstatus='21' AND transtype='1' AND transdate >= '2015-04-01' AND hoa='$hoa' AND issueuser LIKE '$ddo%' ORDER BY issueuser ");
    // AND transdate >= '2015-04-01' 
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Cheques Report</h3></br></br></br></br>";
	echo "<table class='each_table' style='margin-left:20px;'>
	<tr class='heading_row'>";
	$i=1;
	echo "<th>S.No</th><th>DDO Code</th><th>HOA</th><th>Chequeno</th><th>Party Name</th><th>Total Amount(in Rs.)</th><th>Issue date</th>";

	echo "</tr>";

	while($row=pg_fetch_array($result)) {
		$date=explode("-",$row['transdate']);
	$transdate = $date[2]."-".$date[1]."-".$date[0];
		echo "<tr>";
					echo "<td>".$i."</td><td>".$row['issueuser']."</td><td>".$row['hoa']."</td><td>".$row['chequeno']."</td><td>".$row['partyname']."</td><td>".moneyFormatIndia($row['partyamount'])."</td><td>".$transdate."</td>";
		echo "</tr>";
		$i++;

	}

	echo "</table></div>";

 ?>