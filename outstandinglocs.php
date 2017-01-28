<?php
$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

$q = pg_fetch_array(pg_query("SELECT SUM(loc) FROM pdaccountinfo WHERE ddocode LIKE '2702%' AND activation = '2' AND account_type='2' "));
//$q = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='27022304001' "));
echo "<h3>Hyderabad Total outstanding locs : Rs.".$q['sum']."</h3>";

$q2 = pg_fetch_array(pg_query("SELECT SUM(loc) FROM pdaccountinfo WHERE ddocode LIKE '2213%' OR ddocode LIKE '2201%' AND activation = '2' AND account_type='2' "));
//$q = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='27022304001' "));
echo "<h3>Vijaynagaram Total outstanding locs : Rs.".$q2['sum']."</h3>";

echo "<h3>Total outstanding locs : Rs.".($q2['sum']+$q['sum'])."</h3>";


$q3 = pg_fetch_array(pg_query("SELECT SUM(reqamount) FROM locrequest WHERE requestuser LIKE '2702%' AND requestflag='0' "));
//$q = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='27022304001' "));
echo "<h3>Hyderabad Total pending locs : Rs.".$q3['sum']."</h3>";

$q4 = pg_fetch_array(pg_query("SELECT SUM(reqamount) FROM locrequest WHERE (requestuser LIKE '2213%' OR requestuser LIKE '2201%') AND requestflag='0' "));
//$q = pg_fetch_all(pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='27022304001' "));
echo "<h3>Vijaynagaram Total pending locs : Rs.".$q4['sum']."</h3>";

echo "<h3>Total pending locs : Rs.".($q3['sum']+$q4['sum'])."</h3>";


$query1 = pg_query("SELECT * FROM locrequest WHERE requestuser LIKE '2702%' AND requestflag='0' ");

while($rows = pg_fetch_array($query1)) {

	echo $rows['requestuser']."---".$rows['reqamount']."<br>";
}
echo "<hr>";
$query2 = pg_query("SELECT * FROM locrequest WHERE (requestuser LIKE '2213%' OR requestuser LIKE '2201%') AND requestflag='0' ");

while($rows2 = pg_fetch_array($query2)) {

	echo $rows2['requestuser']."---".$rows2['reqamount']."<br>";
}

?>