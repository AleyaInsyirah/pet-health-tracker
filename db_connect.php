<?php
$host = "localhost";
$dbname = "petapp_db"; // tukar ikut nama database you
$username = "root"; // default XAMPP username
$password = ""; // default XAMPP password (kosong)

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}
?>