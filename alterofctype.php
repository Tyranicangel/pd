<?php
	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	// $q1=pg_query("ALTER TABLE ofctype ADD COLUMN cheque_pass_auth character varying(100) DEFAULT '2' NOT NULL  ");

	// $q1=pg_query("ALTER TABLE ofctype ADD COLUMN loc_pass_auth character varying(100) DEFAULT '2' NOT NULL ");

	$q1=pg_fetch_all(pg_query("SELECT * FROM ofctype "));
	
	echo '<pre>';
	print_r($q1);
	echo '</pre>';
?>