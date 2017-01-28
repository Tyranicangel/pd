<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$query = pg_query("SELECT * FROM ofctype ");

while($row=pg_fetch_array($query)) {

	$ddo = $row['code'];

	if(substr($ddo, 0,2) == '01') {

        $ddo = '01010704001';
    } else if(substr($ddo, 0,2) == '02') {

        $ddo = '02010704001';
    } else if(substr($ddo, 0,2) == '03') {

        $ddo = '03010704001';
    } else if(substr($ddo, 0,2) == '04') {

        $ddo = '04010704001';
    } else if(substr($ddo, 0,2) == '05') {

        $ddo = '05010704001';
    } else if(substr($ddo, 0,2) == '06') {

        $ddo = '06010704001';
    } else if(substr($ddo, 0,2) == '07') {

        $ddo = '07010704001';
    } else if(substr($ddo, 0,2) == '08') {

        $ddo = '08010704001';
    } else if(substr($ddo, 0,2) == '09') {

        $ddo = '09010704001';
    } else if(substr($ddo, 0,2) == '10') {

        $ddo = '10010704001';
    } else if(substr($ddo, 0,2) == '11') {

        $ddo = '11010704001';
    } else if(substr($ddo, 0,2) == '12') {

        $ddo = '12010704001';
    } else if(substr($ddo, 0,2) == '22') {

        $ddo = '22010704001';
    }

    $checkuser = pg_query("SELECT * FROM users WHERE username='$ddo' AND (user_role=20 OR user_role=2) ");

    if(pg_num_rows($checkuser) == 0) {

    	// echo "<pre>";
    	// print_r(pg_fetch_all($checkuser));
    	// echo "</pre>";


    	$checkpd = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='8011001050001000000NVN' ");
    	


    } else {

        echo "<pre>";
        print_r(pg_fetch_all($checkuser));
        echo "</pre>";


    	$checkpd = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddo' AND hoa='8011001050001000000NVN' ");

    	// if(pg_num_rows($checkpd) == 0) {

    	// 	echo "<pre>";
	    // 	print_r(pg_fetch_all($checkpd));
	    // 	echo "</pre>";
    	// }
    	
    }


}

		