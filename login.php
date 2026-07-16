<?php
include 'db_connect.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Email and password are required"]);
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, email, phone_number, profile_picture, password FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    unset($user['password']);
    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user_id" => (int)$user['user_id'],
        "username" => $user['username'],
        "email" => $user['email'],
        "user" => $user
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
}

$stmt->close();
$conn->close();
?>
