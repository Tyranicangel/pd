<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=postgres") or die('Could not connect:'.pg_last_error());

$result = pg_query("SELECT DISTINCT(userid),lapsableflag,chqflag FROM users WHERE user_role=2 ");
while($row=pg_fetch_array($result)) {

	$uid = $row['userid'];
	$uname = $row['userid']."checker";
	$lapsableflag = $row['lapsableflag'];
	$chqflag = $row['chqflag'];
	$rtoken = md5(microtime());

	//pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc,lapsableflag,chqflag) VALUES ('$uname','e10adc3949ba59abbe56e057f20f883e','$uid',20,'$rtoken','PD Admin checker','$lapsableflag', '$chqflag') ");
}




?>