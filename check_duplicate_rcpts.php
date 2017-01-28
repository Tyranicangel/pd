<?php
set_time_limit(0);
if(!isset($_POST['ddocode']) && !isset($_POST['hoa'])) {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>
	DDOCODE - <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
	HOA - <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp;
	<button type='submit'>CHECK</button>
	</form> ";
} else {

	echo "<script src='front/scripts/jquery.js'></script>
	
	<form method='post' action='".$_SERVER['PHP_SELF']."'>
	DDOCODE - <input type='text' name='ddocode' /> &nbsp; &nbsp; &nbsp; &nbsp;
	HOA - <input type='text' name='hoa' /> &nbsp; &nbsp; &nbsp; &nbsp;
	<button type='submit'>CHECK</button>
	</form>";
	$ddocode = $_POST['ddocode'];
	$hoa = $_POST['hoa'];

	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	//SELECT * FROM (SELECT COUNT(chequeno) as ccount,chequeno,ddocode FROM transactions GROUP BY chequeno,ddocode ) as a WHERE a.ccount >= 2
	$result = pg_query("SELECT * FROM transactions WHERE issueuser='$ddocode' AND hoa='$hoa' AND transtype='2' AND transstatus='3' AND transdate >= '2015-03-01' AND transdate <= '2015-03-31' ");
	$chqArr = array();
	$chqDuplicateArr = array();
    echo pg_num_rows($result);
	echo "<table border='1'>
	<tr>
	<th>S.No</th>
	<th>DDOCODE</th>
	<th>HOA</th>
	<th>Chq/Trans</th>
	<th>AMOUNT</th>
	<th>DATE</th>
	</tr>";
	$i=1;
	$totpartyamount = 0;
	while($row=pg_fetch_array($result)) {

		$checkstring = $row['chequeno']."-".$row['issueuser'];
		
		if(!in_array($checkstring, $chqArr)) {

			$chqArr[] = $checkstring;
		} else {

			$chqDuplicateArr[] = $row['chequeno'];

			echo "<tr>
			<td>".$i."</td>
			<td>".$row['issueuser']."</td>
			<td>".$row['hoa']."</td>
			<td>".$row['chequeno']."</td>
			<td>".$row['partyamount']."</td>
			<td>".$row['transdate']."</td>
			</tr>";

			$i++;
			$totpartyamount = $totpartyamount+$row['partyamount'];


		}
	}
	
	if(count($chqDuplicateArr) > 0) {
	echo "</table>
	<h4>Total amount: Rs. ".$totpartyamount."</h4>
	<input type='hidden' id='duparr' value='".json_encode($chqDuplicateArr)."' />
	<input type='hidden' id='ddocode' value='".$ddocode."' />
	<input type='hidden' id='hoa' value='".$hoa."' />

	<button id='removeduplicate' style='padding:5px;margin:10px;'>REMOVE ALL DUPLICATES</button>";
	}
	?>


	<script>

	$(document).ready(function(){

		$('#removeduplicate').click(function(){
			
			$.ajax({
				type:'POST',
				url:'remove_duplicate_rcpts_action.php',
				data:{chqarr:$('#duparr').val(), ddocode:$('#ddocode').val(),hoa:$('#hoa').val()},
				success:function(result){
					alert("deleted.");
					window.location.reload();
					console.log(result);
				}
			});
		});
	});



	</script>


<?php }

?>