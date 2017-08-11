<?php


//$connection = mysqli_connect('HOSTNAME','DB_USERNAME','DB_PASSWORD','DATABASE_NAME');
$connection = mysqli_connect('ctf-mysql:3306', 'root', 'my-secret-pw', 'ctff'); 

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>