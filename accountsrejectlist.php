<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
$q=pg_query("SELECT * FROM pdaccountinfo WHERE status='2' ORDER BY ddocode");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>PD Accounts Rejected</title>
	<style>
		table{
			border-collapse: collapse;
			float:left;
			width:100%;
			font-size:12px;
			font-family: arial;
		}
		table th{
			border:1px solid #bababa;
			background: #474646;
			text-align: center;
			padding:5px;
			color:#f5f5f5;
		}
		table td{
			border:1px solid #bababa;;
			text-align: center;
			padding:5px;
		}

	</style>
</head>
<body>
	<table>
		<tr>
			<th>SNo</th>
			<th>DDOCODE</th>
			<th>HOA</th>
			<th>Opening Bal</th>
			<th>Balance</th>
			<th>Reason</th>
		</tr>
		<?php
		$c=0;
		while($r=pg_fetch_array($q,null,PGSQL_ASSOC))
		{
			$c++;
		?>
		<tr>
			<td><?php echo $c;?></td>
			<td><?php echo $r['ddocode']?></td>
			<td><?php echo $r['hoa']?></td>
			<td><?php echo $r['obalance']?></td>
			<td><?php echo $r['balance']?></td>
			<td><?php echo $r['remarks']?></td>
		</tr>
		<?php
		}
		?>
	</table>
</body>
</html>