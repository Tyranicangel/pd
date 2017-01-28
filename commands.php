<?php
$dbhost = 'localhost';
$dbuser = 'postgres';
$dbpass = 'noobtard123';
$dbname = 'pdac';
$date = date('d-m-Y');
$backup_file = $dbname.$date.'.sql';
// $command = "service apache2 restart";
//$command = "pg_dump -U $dbuser $dbname > ".$backup_file;
$command = "sudo chmod -R 777 back";
$newdb = "pdacnew";

system($command);
echo "<br>";
echo '123';
?>