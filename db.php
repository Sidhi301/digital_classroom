<?php
// db.php

$host = 'localhost';  // Database host
$dbname = 'register'; // Database name
$username = 'root';   // Database username (default for XAMPP is 'root')
$password = '';       // Database password (default for XAMPP is empty)

$con = mysqli_connect($host, $username, $password, $dbname); // Establish connection

// Check if the connection is successful
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
