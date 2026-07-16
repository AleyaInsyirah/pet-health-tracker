<?php
include 'db_connect.php';

$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, email, phone_number, profile_picture, created_at FROM users WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

echo json_encode($user
    ? ["success" => true, "user" => $user]
    : ["success" => false, "message" => "User not found"]);

$stmt->close();
$conn->close();
?>
