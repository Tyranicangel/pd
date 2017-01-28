<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$result = pg_query("SELECT DISTINCT(userid),lapsableflag,chqflag,userdesc FROM users WHERE user_role=2 ");
while($row=pg_fetch_array($result)) {

	$uid = $row['userid'];
	$uname = $row['userid']."auth";
	$lapsableflag = $row['lapsableflag'];
	$chqflag = $row['chqflag'];
	$userdesc = $row['userdesc'];
	$rtoken = md5(microtime()."auth");

	pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc,lapsableflag,chqflag) VALUES ('$uname','e10adc3949ba59abbe56e057f20f883e','$uid',20,'$rtoken','$userdesc','$lapsableflag', '$chqflag') ");
}




?>