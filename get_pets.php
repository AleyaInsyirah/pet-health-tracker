<?php
include 'db_connect.php';

$user_id = $_GET['user_id'];

$sql = "SELECT * FROM pets WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}

echo json_encode(["success" => true, "pets" => $pets]);

$stmt->close();
$conn->close();
?>