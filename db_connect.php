<?php
// Database connection parameters
$servername = "localhost:3306";
$username = "admin1234";
$password = "Ha1@2004";
$dbname = "admin1234_school";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
