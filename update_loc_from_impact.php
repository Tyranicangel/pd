<?php
    $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

    $hoas=pg_query("SELECT DISTINCT(ddocode), hoa FROM pdaccountinfo WHERE status='1' AND ddocode NOT LIKE '2702%' AND ddocode NOT LIKE '2201%' AND ddocode NOT LIKE '2213%' ") or die(pg_last_error());
    while($rowhoa = pg_fetch_array($hoas)) {

        if(substr($rowhoa['ddocode'], 2, 2) == '01') {

        	$hoaarr[] = $rowhoa['hoa'];
            $ddoarr[] = $rowhoa['ddocode'];
        }
    }

    $ddocodess = implode("','", $ddoarr);
    $ddocodess = "('".$ddocodess."')";

    $hoass = implode("','", $hoaarr);
    $hoass = "('".$hoass."')";

    pg_close($db1);

    $db2= pg_connect("host=10.10.24.16 dbname=ap_impact1516 user=cfms password=cfms123") or die('Could not connect:'.pg_last_error());

    $locim = pg_query("SELECT * FROM mpdloc WHERE ddocode IN $ddocodess AND hoa IN $hoass ");

    pg_close($db2);
    echo "<pre>";
    print_r(pg_fetch_all($locim));
    echo "</pre>";

    $db3= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

    while($rowim=pg_fetch_array($locim)) {

        $locbal = $rowim['locamt']-$rowim['expamt'];

        pg_query("UPDATE pdaccountinfo SET loc=$locbal WHERE ddocode='".$rowim['ddocode']."' AND hoa='".$rowim['hoa']."' ");
    }





?>