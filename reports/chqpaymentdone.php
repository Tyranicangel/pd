<?php
	set_time_limit(0);
		include('connect.php');
	echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Cheques Paymentdone Report</title></head>";
    $result = pg_query("SELECT issueuser,partyamount,transstatus,transdate FROM transactions WHERE transstatus='3' AND transdate >= '2015-04-01' AND transtype='1' AND issueuser IS NOT NULL ORDER BY issueuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>Cheques Report</h3></br></br></br></br><p class='each_desc' style='margin:0px 0px 20px 25px;'>Report on payment done cheques and is shown according to Treasury-wise, DDO-wise, Head of Account-wise, and Date-wise.</p>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {

	$tre = $row['issueuser'];
	$ddo=substr($tre,0,4);
$area = pg_fetch_array(pg_query("SELECT * FROM users WHERE user_role='8' AND username='$ddo'"));
		if(isset($arr[$ddo]['partyamount'])) {
				$arr[$ddo]['area']=$area['userdesc'];
				$arr[$ddo]['partyamount']= $arr[$ddo]['partyamount']+$row['partyamount'];
				$arr[$ddo]['num'] = $arr[$ddo]['num']+1;
		} else {
				$arr[$ddo]['area']=$area['userdesc'];
				$arr[$ddo]['partyamount'] = $row['partyamount'];
				$arr[$ddo]['num'] = 1;			
		}
		$n++;
}

echo "<table class='each_table1' style='margin-left:20px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>District Treasury</th>
<th>No. of Cheques</th>
<th>Total Amount(in Rs.)</th>
<th></th>
<th></th>
<th></th>
</tr>";
//width:1100px;
$k = 1;
$tot=0;
ksort($arr);
foreach ($arr as $key => $value) {

		echo "<tr>";
		echo "<td>".$k."</td>
		<td>".$key." (".$value['area'].")</td>";
		if(isset($value['partyamount'])) { 
		
		echo "
		
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/chqpd_action.php?userid=".$key."'>".$value['num']."</a></td>
		<td>".moneyFormatIndia($value['partyamount'])."</td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/chqpd_ddo.php?ddo=".$key."'>DDO-wise report</a></button></td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/chqpd_hoa.php?hddo=".$key."'>HOA-wise report</a></button></td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/chqpd_date.php?ddodate=".$key."'>Date-wise report</a></button></td>";
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
<td colspan='3'></td>
</tr></table></div>";

 ?>