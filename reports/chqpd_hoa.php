<?php
	set_time_limit(0);
		include('connect.php');
		$dd=$_GET['hddo'];
		echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Cheques Paymentdone HOA-wise Report</title></head>";
    $result = pg_query("SELECT issueuser,partyamount,transstatus,hoa,transdate FROM transactions WHERE transstatus='3' AND transtype='1' AND transdate >= '2015-04-01' AND issueuser LIKE '$dd%' ORDER BY issueuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Cheques Report</h3></br></br></br></br><p class='each_desc' style='margin:0px 0px 20px 25px;'>Report on payment done cheques and are shown according to Head of Account-wise.</p>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {

	$tre = $row['issueuser'];
	$hoa= $row['hoa'];
	$ddo=substr($tre,0,4);
		if(isset($arr[$hoa]['partyamount'])) {
				$arr[$hoa]['ddo']=$ddo;
				$arr[$hoa]['partyamount']= $arr[$hoa]['partyamount']+$row['partyamount'];
				$arr[$hoa]['num'] = $arr[$hoa]['num']+1;
		} else {
				$arr[$hoa]['ddo']=$ddo;
				$arr[$hoa]['partyamount'] = $row['partyamount'];
				$arr[$hoa]['num'] = 1;			
		}
		$n++;
}

echo "<table class='each_table' style='margin-left:20px;width:1100px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>HOA</th>
<th>No. of Cheques</th>
<th>Total Amount(in Rs.)</th>
</tr>";
$k = 1;
$tot=0;
ksort($arr);
foreach ($arr as $key => $value) {

		echo "<tr>";
		echo "<td>".$k."</td>
		<td>".$key."</td>";
		if(isset($value['partyamount'])) { 
		
		echo "
		
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/chqpd_hoa_action.php?ddo=".$value['ddo']."&hoa=".$key."'>".$value['num']."</a></td>
		<td>".moneyFormatIndia($value['partyamount'])."</td>";
	    } 
	    if(!isset($value['num'])){

	    	$value['num'] = 0;
	    }
	     if(!isset($value['partyamount'])) {
	    	$value['partyamount']=0;
	    }
	  echo " </tr>";
	  $tot=$value['partyamount']+$tot;
	  		 $k++;
	}
echo "<tr><td colspan='3'>GRAND TOTAL</td>
<td>".moneyFormatIndia($tot)."</td>
</tr></table></div>";

 ?>