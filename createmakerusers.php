<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

// $result = pg_query("SELECT DISTINCT(ddocode) FROM pdaccountinfo WHERE ddocode NOT LIKE '2702%' AND ddocode NOT LIKE '2213%' AND ddocode NOT LIKE '2201%' ");

// while($row=pg_fetch_array($result)) {

	// if(substr($row['ddocode'], 2, 2) == '01') {

		$uid = '10011603017';//$row['ddocode'];

		$uname = '10011603017auth';//$row['ddocode']."auth";
		$userdesc = "PD Administrator";
		$rtoken = md5(microtime()."10011603017maker");
		$uname1 = '10011603017';

		$checkres = pg_query("SELECT * FROM users WHERE userid='$uid' ");

		if(pg_num_rows($checkres) == 0) {

			// echo $uid."<br>";

			 pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc) VALUES ('$uname1','e10adc3949ba59abbe56e057f20f883e','$uid',2,'$rtoken','$userdesc') ");

			$rtoken1 = md5(microtime()."10011603017auth");

			 pg_query("INSERT INTO users (username,password,userid,user_role,refreshtoken,userdesc) VALUES ('$uname','e10adc3949ba59abbe56e057f20f883e','$uid',20,'$rtoken1','$userdesc') ");
		}
	// }
// }




?>