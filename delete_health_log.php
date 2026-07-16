<?php
include 'db_connect.php';

$log_id = (int)($_POST['log_id'] ?? 0);
$user_id = (int)($_POST['user_id'] ?? 0);

if ($log_id <= 0 || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid log ID or user ID"]);
    exit;
}

$sql = "DELETE hl FROM health_logs hl
        JOIN pets p ON hl.pet_id = p.pet_id
        WHERE hl.log_id = ? AND p.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $log_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    echo json_encode(["success" => true, "message" => "Health log deleted"]);
} else {
    echo json_encode(["success" => false, "message" => "Health log not found or not authorized"]);
}

$stmt->close();
$conn->close();
?>
