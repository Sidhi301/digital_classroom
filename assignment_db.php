<?php
$host = 'localhost';     // Your database host (usually localhost)
$dbname = 'register';    // Your database name (adjust as per your database for assignments)
$username = 'root';      // Your database username
$password = '';          // Your database password

try {
    // Create a PDO instance to connect to the database
    $assignmentPDO = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $assignmentPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, display an error message
    die("Could not connect to the database: " . $e->getMessage());
}
?>
