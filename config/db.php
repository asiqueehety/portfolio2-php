<?php
$servername = "localhost";
$username = "root";   // default XAMPP username
$password = "";       // default XAMPP password (empty)
$database = "portfolio-template-php";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
