<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "smart_queue";
$port = 3307; // Matches your exact XAMPP port config

// Establish the connection object
$conn = new mysqli($host, $username, $password, $database, $port);

// Verify if the connection works smoothly
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}
?>