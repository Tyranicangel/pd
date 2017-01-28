<?php
$db1= pg_connect("host=localhost dbname=pdac user=pdac password=noobtard123") or die('Could not connect:'.pg_last_error());

$result = pg_query("SELECT areacode FROM arealist WHERE areacode NOT IN ('27','22','59','12') ");
while($row=pg_fetch_array($result)) {

	$areacode = $row['areacode'];
	$dto1 = $areacode."01";
	$dto2 = $areacode."02";
	$dto3 = $areacode."03";
	$rtoken = md5(microtime().$dto1);
	// pg_query("UPDATE users SET refreshtoken = '$rtoken' WHERE username='$dto1' ");
	// $rtoken = md5(microtime().$dto2);
	// pg_query("UPDATE users SET refreshtoken = '$rtoken' WHERE username='$dto2' ");
	// $rtoken = md5(microtime().$dto3);
	// pg_query("UPDATE users SET refreshtoken = '$rtoken' WHERE username='$dto3' ");

}




?>