<?php
	set_time_limit(0);
    include('connect.php');
$ddo=$_GET['ddo'];
$dt=$_GET['date'];
$d=explode("-", $dt);
$date=$d[2]."-".$d[1]."-".$d[0];
    echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Cheques Paymentdone Date-wise Report</title></head>";
    $result = pg_query("SELECT chequeno,partyname,issueuser,hoa,partyamount,transdate,confirmdate FROM transactions WHERE transstatus='3' AND transtype='1' AND transdate >= '2015-04-01' AND issueuser LIKE '$ddo%' AND transdate='$date' ORDER BY issueuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Cheques Report</h3></br></br></br></br>";
	echo "<table class='each_table' style='margin-left:20px;'>
	<tr class='heading_row'>";
	$i=1;
	echo "<th>S.No</th><th>DDO Code</th><th>HOA</th><th>Chequeno</th><th>Party Name</th><th>Total Amount(in Rs.)</th><th>Issue date</th><th>Confirmed date</th>";

	echo "</tr>";

	while($row=pg_fetch_array($result)) {
		$date=explode("-",$row['transdate']);
	$transdate = $date[2]."-".$date[1]."-".$date[0];
	$cdate=substr($row['confirmdate'],0,10);
	$codate=explode("-",$cdate);
	$confdate=$codate[2]."-".$codate[1]."-".$codate[0];
		echo "<tr>";
					echo "<td>".$i."</td><td>".$row['issueuser']."</td><td>".$row['hoa']."</td><td>".$row['chequeno']."</td><td>".$row['partyname']."</td><td>".moneyFormatIndia($row['partyamount'])."</td><td>".$transdate."</td><td>".$confdate."</td>";
		echo "</tr>";
		$i++;

	}

	echo "</table></div>";

 ?>