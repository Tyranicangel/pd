<?php

    $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

    $result = pg_query("SELECT * FROM locrequest WHERE requestuser='27022304001' AND requestflag='1' ");

	$result2 = pg_query("SELECT * FROM locrequest LIMIT 1");
	echo "<h3>Locrequest</h3>";
	echo "<table border='1'>
	<tr>";
	$i=1;
	$j=1;

	while($row2=pg_fetch_array($result2)) {
      
			foreach ($row2 as $key => $value) {
				
				if($i % 2 == 0) {

					echo "<th>".$key."</th>";
				}
				$i++;
			}

	}

	echo "</tr>";

	while($row=pg_fetch_array($result)) {

		echo "<tr>";
      
			foreach ($row as $key => $value) {

				if($j % 2 == 0) {

					echo "<td>".$value."</td>";
				}
				$j++;
			}

		echo "</tr>";

	}

	echo "</table>";

	$result = pg_query("SELECT * FROM transactions WHERE issueuser='27022304001' AND chqflag='1' ");

	$result2 = pg_query("SELECT * FROM transactions LIMIT 1");
	echo "<h3>Transaction</h3>";
	echo "<table border='1'>
	<tr>";
	$i=1;
	$j=1;

	while($row2=pg_fetch_array($result2)) {
      
			foreach ($row2 as $key => $value) {
				
				if($i % 2 == 0) {

					echo "<th>".$key."</th>";
				}
				$i++;
			}

	}

	echo "</tr>";

	while($row=pg_fetch_array($result)) {

		echo "<tr>";
      
			foreach ($row as $key => $value) {

				if($j % 2 == 0) {

					echo "<td>".$value."</td>";
				}
				$j++;
			}

		echo "</tr>";

	}

	echo "</table>";

 ?>