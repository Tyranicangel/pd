<?php
$timezone="Asia/Calcutta";
	date_default_timezone_set($timezone);
	$dbc=pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

	function moneyFormatIndia($num){
		$explrestunits = "" ;
		if(strlen($num)>3){
			$lastthree = substr($num, strlen($num)-3, strlen($num));
	        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
	        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
	        $expunit = str_split($restunits, 2);
	        for($i=0; $i<sizeof($expunit); $i++){
	            // creates each of the 2's group and adds a comma to the end
	        	if($i==0)
	        	{
	                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
	            }else{
	            	$explrestunits .= $expunit[$i].",";
	            }
	        }
		        $thecash = $explrestunits.$lastthree;
	    } else {
	    	$thecash = $num;
	    }
	    return $thecash; // writes the final format where $currency is the currency symbol.
	}
?>