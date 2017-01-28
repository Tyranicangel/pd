<?php
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
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$result = pg_query("SELECT * FROM transactions trans, users usr WHERE usr.username=trans.issueuser AND trans.transstatus=1 ORDER BY trans.id") or die(pg_last_error());
$filename = "chequelist".date("d-m-Y").".csv";
$fp=fopen($filename,'w');
fputcsv($fp,array('Sno','Cheque no','Issue Authority','DDO Code','Head of Account','Issue Date','Amount (in Rs)','Purpose','Party Name'));
$i = 1;
while($row=pg_fetch_array($result)) {
	if($row['multiflag'] == 2) {
		$row['partyname'] = "Multiple party";
	}
	$row['partyamount'] = moneyFormatIndia($row['partyamount']);

	$explodedate = explode("-", $row['transdate']);
	$row['transdate'] = $explodedate[2]."-".$explodedate[1]."-".$explodedate[0];
	fputcsv($fp,array($i,$row['chequeno'],$row['userdesc'],$row['issueuser'],$row['hoa'],$row['transdate'],$row['partyamount'],$row['purpose'],$row['partyname']));
	$i++;

}
fclose($fp);
echo $filename;
?>