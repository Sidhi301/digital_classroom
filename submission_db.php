<?php
// Database connection details
$host = 'localhost';
$dbname = 'register';  // Replace with your database name
$dbusername = 'root';  // Replace with your database username
$dbpassword = '';  // Replace with your database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>
