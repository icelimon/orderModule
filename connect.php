<?php
//SET DEFAULT TIME ZONE
date_default_timezone_set('Asia/Dhaka');
$dbhost = 'DATABASE_IP_ADDRESS';
$dbuser = 'DATABASE_USERNAME';
$dbpass = 'DATABASE_PASSWORD';
$dbname = 'DATABASE_NAME';

$conn = oci_connect($dbuser,$dbpass, $dbhost.":1521/".$dbname);
if( $conn ) {
	//CHECK CONNECTION 
	//echo 'database connected!';
}else{
	//die('Could not connect: ' . mysql_error());
	die('Could not connect: ');

}

?>
