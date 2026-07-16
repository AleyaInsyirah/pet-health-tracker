<?php
include 'db_connect.php';

$user_id = (int)($_POST['user_id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$phone_number = trim($_POST['phone_number'] ?? '');
$profile_picture = trim($_POST['profile_picture'] ?? '');

if ($user_id <= 0 || $username === '') {
    echo json_encode(["success" => false, "message" => "Username is required"]);
    exit;
}

$stmt = $conn->prepare("UPDATE users SET username = ?, phone_number = ?, profile_picture = ? WHERE user_id = ?");
$stmt->bind_param("sssi", $username, $phone_number, $profile_picture, $user_id);
$stmt->execute();

echo json_encode(["success" => true, "message" => $stmt->affected_rows > 0 ? "Profile updated" : "No changes were made"]);

$stmt->close();
$conn->close();
?>
