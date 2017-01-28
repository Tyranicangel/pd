<?php
	set_time_limit(0);
		include('connect.php');
		$ddo=$_GET['ddo'];
		echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Cheques Paymentdone DDO-wise Report</title></head>";
    $result = pg_query("SELECT issueuser,partyamount,transstatus,transdate FROM transactions WHERE transstatus='3' AND transtype='1' AND transdate >= '2015-04-01' AND issueuser LIKE '$ddo%' AND issueuser IS NOT NULL ORDER BY issueuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Cheques Report</h3></br></br></br></br><p class='each_desc' style='margin:0px 0px 20px 25px;'>Report on payment done cheques and are shown according to DDO-wise.</p>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {

	$tre = $row['issueuser'];
	$ddo=substr($tre,0,4);
		if(isset($arr[$tre]['partyamount'])) {

				$arr[$tre]['partyamount']= $arr[$tre]['partyamount']+$row['partyamount'];
				$arr[$tre]['num'] = $arr[$tre]['num']+1;
		} else {
				$arr[$tre]['partyamount'] = $row['partyamount'];
				$arr[$tre]['num'] = 1;			
		}
		$n++;
}

echo "<table class='each_table' style='margin-left:20px;width:1100px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>DDO Code</th>
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
		
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/chqpd_ddo_action.php?ddo=".$key."'>".$value['num']."</a></td>
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