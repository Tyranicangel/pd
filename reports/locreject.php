
<?php
include('connect.php');
echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>LOC Rejected Report</title></head>";
    $result = pg_query("SELECT reqamount,userid,requestflag,requestdate FROM locrequest WHERE requestflag='1' AND conf_flag='2' AND requestdate >= '2015-04-01' ORDER BY userid ");
	echo "<div style='font-family:arial;'><h3 class='content_heading' style='margin-left:20px;'>LOC's Report</h3></br></br></br></br><p class='each_desc' style='margin:0px 0px 20px 25px;'>Report on LOC's which are rejected and is shown according to Treasury-wise, DDO-wise, Head of Account-wise, and Date-wise.</p>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {
	$tre = $row['userid'];
	//$a=substr($tre,0,2);
$area = pg_fetch_array(pg_query("SELECT * FROM users WHERE user_role='8' AND username='$tre'"));
	

		if(isset($arr[$tre]['reqamount'])) {
				$arr[$tre]['area']=$area['userdesc'];
				$arr[$tre]['reqamount']= $arr[$tre]['reqamount']+$row['reqamount'];
				$arr[$tre]['num'] = $arr[$tre]['num']+1;
		} else {
				$arr[$tre]['area']=$area['userdesc'];
				$arr[$tre]['reqamount'] = $row['reqamount'];
				$arr[$tre]['num'] = 1;			
		}
		$n++;
}

echo "<table class='each_table1' style='margin-left:20px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>District Treasury</th>
<th>No. of LOC's</th>
<th>Requested Amount(in Rs.)</th>
<th></th>
<th></th>
<th></th>
</tr>";
$k = 1;
$rtot=0;
$gtot=0;
ksort($arr);
foreach ($arr as $key => $value) {

		echo "<tr>";
		echo "<td>".$k."</td>
		<td>".$key." (".$value['area'].")</td>";
		if(isset($value['reqamount'])) { 
		
		echo "
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/locrej_action.php?userid=".$key."'>".$value['num']."</a></td>
		<td>".moneyFormatIndia($value['reqamount'])."</td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/locrej_ddo.php?ddo=".$key."'>DDO-wise report</a></button></td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/locrej_hoa.php?hddo=".$key."'>HOA-wise report</a></button></td><td><button><a target='_blank' style='text-decoration:none;color:#000;' href='http://125.21.84.129/pd/reports/locrej_date.php?ddodate=".$key."'>Date-wise report</a></button></td>";
	    } 

	    if(!isset($value['num'])){

	    	$value['num'] = 0;
	    }
	     if(!isset($value['reqamount'])) {
	    	$value['reqamount']=0;
	    }
	  echo " </tr>";
	   $rtot=$value['reqamount']+$rtot;
		 $k++;
	}
echo "<tr><td colspan='3'>GRAND TOTAL</td>
<td>".moneyFormatIndia($rtot)."</td>
<td colspan='4'></td>
</tr></table></div>";

 ?>