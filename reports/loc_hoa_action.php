<?php
	set_time_limit(0);
    include('connect.php');
$hoa=$_GET['hoa'];
$ddo=$_GET['ddoc'];
echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>LOC Granted HOA-wise Report</title></head>";
    $result = pg_query("SELECT requestuser,reqamount,grantamount,hoa,requestdate,refno,userid,requestflag,ddtime,requestdate FROM locrequest WHERE requestflag='1' AND conf_flag='6' AND requestdate >= '2015-04-01' AND hoa='$hoa' AND ddtime IS NOT NULL AND requestuser LIKE '$ddo%' ORDER BY requestuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>LOC's Report</h3></br></br></br></br>";
	echo "<table class='each_table' style='margin-left:20px;'>
	<tr class='heading_row'>";
	$i=1;
	echo "<th>S.No</th><th>DDO code</th><th>HOA</th><th>Refno</th><th>Requested date</th><th>Requested Amount(in Rs.)</th><th>Confirmed date</th><th>Granted Amount(in Rs.)</th>";

	echo "</tr>";

	while($row=pg_fetch_array($result)) {
		$rdate=explode("-",$row['requestdate']);
		$cdate=substr($row['ddtime'],0,10);
		$codate=explode("-", $cdate);
		$reqdate=$rdate[2]."-".$rdate[1]."-".$rdate[0];
		$confdate=$codate[2]."-".$codate[1]."-".$codate[0];
		echo "<tr>";
					echo "<td>".$i."</td><td>".$row['requestuser']."</td><td>".$row['hoa']."</td><td>".$row['refno']."</td><td>".$reqdate."</td><td>".$row['reqamount']."</td><td>".$confdate."</td><td>".$row['grantamount']."</td>";
		echo "</tr>";
		$i++;

	}

	echo "</table></div>";

 ?>