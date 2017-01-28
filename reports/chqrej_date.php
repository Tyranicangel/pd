<?php
include('connect.php');
$ddo=$_GET['ddodate'];
?>

<head>
<link rel='stylesheet' type='text/css' href='../front/styles/style_common.css'>
<link rel='stylesheet' type='text/css' href='../front/styles/calender.css'>
<title>Cheques Rejected Date-wise Report</title>
<script type='text/javascript' src='../front/scripts/jquery.js'></script>
<script type='text/javascript'src='../front/scripts/jquery_ui.js'></script>
<script>
	$(document).ready(function() {
		$('#date').datepicker({dateFormat: 'dd-mm-yy'});
		$('#show_ddo').click(function() {
			if($('#date').val()=='') {
				alert('Please select the date.');
			} else {
			var date = $('#date').val();
			var ddo = "<?php echo $ddo; ?>";
			window.open('chqrej_date_action.php?date=' +date + '&ddo=' +ddo, '_blank');
		}
	});
	});
	</script>
</head>
<?php
echo "<div style='margin-left:25px;margin-top:20px;font-family:arial;'><p class='each_desc'><span style='color:red;'>* </span>Please select the date of Cheque issue made.</p></div></br></br></br>";
echo "<input type='text' class='each_box' id='date' style='margin-left:150px;width:165px;' placeholder='Select Cheque issue date'></br></br></br>";
echo "<div class='wrap_indi send_req_cls'>
		<input type='submit' id='show_ddo' class='main_button' style='width:100px;margin:0px;' />
	</div>";
?>