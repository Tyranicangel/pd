<?php
include('connect.php');
$tre=$_GET['userid'];
echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>LOC Rejected Report</title></head>";
    $result = pg_query("SELECT requestuser,reqamount,hoa,requestdate,refno,userid,requestflag,ddtime FROM locrequest WHERE requestflag='1' AND conf_flag='2' AND userid='$tre' AND requestdate >= '2015-04-01' ORDER BY requestuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>LOC's Report</h3></br></br></br></br>";
	echo "<table class='each_table' style='margin-left:20px;'>
	<tr class='heading_row'>";
	$i=1;
	echo "<th>S.No</th><th>DDO code</th><th>HOA</th><th>Refno</th><th>Request date</th><th>Requested Amount(in Rs.)</th>";

	echo "</tr>";

	while($row=pg_fetch_array($result)) {
			$rdate=explode("-",$row['requestdate']);
			$reqdate=$rdate[2]."-".$rdate[1]."-".$rdate[0];
				echo "<tr>";
						echo "<td>".$i."</td><td>".$row['requestuser']."</td><td>".$row['hoa']."</td><td>".$row['refno']."</td><td>".$reqdate."</td><td>".$row['reqamount']."</td>";
			echo "</tr>";
			
		$i++;

	}

	echo "</table></div>";

 ?>