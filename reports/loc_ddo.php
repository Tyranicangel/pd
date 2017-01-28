<?php
	set_time_limit(0);
		include('connect.php');
		$ddo=$_GET['ddo'];
		echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>LOC Granted DDO-wise Report</title></head>";
    $result = pg_query("SELECT reqamount,grantamount,userid,requestflag,hoa,requestuser,requestdate FROM locrequest WHERE requestflag='1' AND conf_flag='6' AND requestdate >= '2015-04-01' AND ddtime IS NOT NULL AND requestuser LIKE '$ddo%' ORDER BY requestuser ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>LOC's Report</h3></br></br></br></br><p class='each_desc' style='margin:0px 0px 20px 25px;'>Report on LOC's which are granted and are shown according to Treasury-wise, DDO-wise, and Head of Account-wise.</p>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {

	$tre = $row['requestuser'];
	$hoa= $row['hoa'];
	$ddo=substr($tre,0,4);
		if(isset($arr[$tre]['reqamount'])) {
				$arr[$tre]['reqamount']= $arr[$tre]['reqamount']+$row['reqamount'];
				$arr[$tre]['grantamount']= $arr[$tre]['grantamount']+$row['grantamount'];
				$arr[$tre]['num'] = $arr[$tre]['num']+1;
		} else {
				$arr[$tre]['reqamount'] = $row['reqamount'];
				$arr[$tre]['grantamount'] = $row['grantamount'];
				$arr[$tre]['num'] = 1;				
		}
		$n++;
}

echo "<table class='each_table' style='margin-left:20px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>DDO Code</th>
<th>No. of LOC's</th>
<th>Requested Amount(in Rs.)</th>
<th>Granted Amount(in Rs.)</th>
</tr>";
$k = 1;
$rtot=0;
$gtot=0;
ksort($arr);
foreach ($arr as $key => $value) {

		echo "<tr>";
		echo "<td>".$k."</td>
		<td>".$key."</td>";
		if((isset($value['reqamount']))) { 
		
		echo "
		
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/loc_ddo_action.php?ddo=".$key."'>".$value['num']."</a></td>
		<td>".moneyFormatIndia($value['reqamount'])."</td><td>".moneyFormatIndia($value['grantamount'])."</td>";
	    } 
	    if(!isset($value['num'])){

	    	$value['num'] = 0;
	    }
	   if(!isset($value['reqamount'])) {
	    	$value['reqamount']=0;
	    }
	     if(!isset($value['grantamount'])) {
	    	$value['grantamount']=0;
	    }
	  echo " </tr>";
	  $rtot=$value['reqamount']+$rtot;
	    $gtot=$value['grantamount']+$gtot;
	  		 $k++;
	}
echo "<tr><td colspan='3'>GRAND TOTAL</td>
<td>".moneyFormatIndia($rtot)."</td>
<td>".moneyFormatIndia($gtot)."</td>
</tr></table></div>";

 ?>