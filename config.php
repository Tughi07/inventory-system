<?php
$servername = "localhost";
$username = "inventory_system_user";  // MAMP default username
$password = "password";  // MAMP default password
$dbname = "inventory_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>