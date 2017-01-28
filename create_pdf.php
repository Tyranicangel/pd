<?php
include "phpToPDF.php";
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

$result = pg_query("SELECT * FROM transactions trans, users usr WHERE usr.userid=trans.issueuser AND trans.transstatus=1 ORDER BY trans.id") or die(pg_last_error());

$htm = '<table style="float: left;width: 900px;font-size: 12px;line-height: 25px;margin-bottom: 20px;" border="1">
			<tr style="background:#000;color:#FFF;">
				<th>S.No</th>
				<th>Cheque No</th>
				<th>Issue Authority</th>
				<th>DDO Code</th>
				<th>Head of Account</th>
				<th>Issue Date</th>
				<th>Amount<br><span class="rs">(in Rs)</span></th>
				<th>Purpose</th>
				<th>Party Name</th>
			</tr>';
			$i = 1;
while($row=pg_fetch_array($result)) {

	if($row['multiflag'] == 2) {

		$row['partyname'] = "Multiple party";
	}
	$row['partyamount'] = moneyFormatIndia($row['partyamount']);

	$explodedate = explode("-", $row['transdate']);
	$row['transdate'] = $explodedate[2]."-".$explodedate[1]."-".$explodedate[0];

	$htm .= '<tr>
	<td>'.$i.'</td>
	<td>'.$row['chequeno'].'</td>
	<td>'.$row['userdesc'].'</td>
	<td>'.$row['issueuser'].'</td>
	<td>'.$row['hoa'].'</td>
	<td>'.$row['transdate'].'</td>
	<td>'.$row['partyamount'].'</td>
	<td>'.$row['purpose'].'</td>
	<td>'.$row['partyname'].'</td>
	</tr>';
	$i++;

}
$htm .= '</table>';


$filename = "chequelist".date("d-m-Y").".pdf";
$pdf_options = array(
      "source_type" => 'html',
      "source" => $htm,
      "action" => 'save',
      "file_name" => $filename);

//Code to generate PDF file from options above
phptopdf($pdf_options);

echo $filename;
?>