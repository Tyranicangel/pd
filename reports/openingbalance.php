<?php
	set_time_limit(0);
		include('connect.php');
	echo "<head><link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'><title>Opening Balances Report</title></head>";
    $result = pg_query("SELECT ddocode FROM pdaccountinfo WHERE ddocode IS NOT NULL ORDER BY ddocode ");
	echo "<div style='font-family:arial;'><p class='each_desc' style='margin:30px 0px 10px 25px;'><span style='color:red;'>*</span>Select treasuries to view the opening balances of pd accounts.</p></br></br></br></br>";
	$n=0;
	$arr = array();

while($row = pg_fetch_array($result)) {
	$tre = $row['ddocode'];
	$ddo=substr($tre,0,4);
$area = pg_fetch_array(pg_query("SELECT * FROM users WHERE user_role='8' AND username='$ddo'"));
		if(isset($arr[$ddo]['partyamount'])) {
				$arr[$ddo]['area']=$area['userdesc'];
		} else {
				$arr[$ddo]['area']=$area['userdesc'];		
		}
		$n++;
}

echo "<table class='each_table1' style='margin-left:20px;'>
<tr class='heading_row'>
<th>S.No</th>
<th>District Treasury</th>
</tr>";
$k = 1;
$tot=0;
ksort($arr);
foreach ($arr as $key => $value) {

		echo "<tr>";
		echo "<td>".$k."</td>
		<td><a target='_blank' href='http://125.21.84.129/pd/reports/obal.php?userid=".$key."'>".$key." (".$value['area'].")</a></td>";
	
	  echo " </tr>";
	  		 $k++;
	}

echo "</tr></table></div>";

 ?>