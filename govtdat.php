<?php
	$dbc=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123");
	$q=pg_query("SELECT c.ddocode,c.hoa,c.balance,c.loc,c.transitamount,c.obalance,c.userdesc,d.schemename FROM (SELECT * FROM (SELECT * FROM pdaccountinfo WHERE ddocode LIKE '2702%') AS a INNER JOIN (SELECT username,userdesc FROM users WHERE username LIKE '2702%') AS b ON a.ddocode=b.username) AS c INNER JOIN (SELECT * FROM schemes) AS d ON c.hoa=d.hoa");
	$r=pg_fetch_all($q);
	header('Access-Control-Allow-Origin: *');
	echo json_encode($r);
?>