<?php
include 'db_connect.php';

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$plain_password = $_POST['password'] ?? '';
$phone_number = trim($_POST['phone_number'] ?? '');

if ($full_name === '' || $email === '' || $plain_password === '' || $phone_number === '') {
    echo json_encode(["success" => false, "message" => "Please complete all registration fields"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address"]);
    exit;
}

$password = password_hash($plain_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password, phone_number) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}
$stmt->bind_param("ssss", $full_name, $email, $password, $phone_number);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Registration successful"]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
