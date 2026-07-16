<?php
header('Content-Type: application/json; charset=utf-8');
$host = "localhost";
$dbname = "petapp_db"; // tukar ikut nama database you
$username = "root"; // default XAMPP username
$password = ""; // default XAMPP password (kosong)

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$conn->set_charset("utf8mb4");

// MIT App Inventor may send URL-encoded PostText without PHP populating $_POST.
// Parse the raw request body as a fallback so all POST endpoints work reliably.
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && empty($_POST)) {
    $raw_input = file_get_contents('php://input');
    if ($raw_input !== false && $raw_input !== '') {
        parse_str($raw_input, $_POST);
    }
}
?>
