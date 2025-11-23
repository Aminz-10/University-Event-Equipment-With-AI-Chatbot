<?php
$user = "root"; 
$pass = ""; 
$host = ""; 
$dbname= "if0_39265998_system";

$connect = mysqli_connect($host,$user,$pass,$dbname);

if(isset($connect)) 
		echo("");  
	//connection is established
else
	echo("Connection failed");


?>