<?php
  $db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

if(!isset($_POST['addQuery'])) {


	echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >

	Query: <input type='text' name='addQuery' style='width:300px;' /> &nbsp; &nbsp; 
	<button type='submit' >SUBMIT</button> 
	</form>";
} else {

	echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >

	Query: <input type='text' name='addQuery' style='width:300px;' /> &nbsp; &nbsp; 
	<button type='submit' >SUBMIT</button> 
	</form>";


	$addQuery = $_POST['addQuery'];
	if(stripos($addQuery, 'update') === false && stripos($addQuery, 'insert') === false && stripos($addQuery, 'delete') === false && stripos($addQuery, 'drop') === false && stripos($addQuery, 'truncate') === false) 		{

	$result = pg_query("$addQuery");

	$result2 = pg_query("$addQuery LIMIT 1");
	}

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

}

?>
