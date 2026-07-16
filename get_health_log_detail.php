<?php
include 'db_connect.php';

$log_id = (int)($_GET['log_id'] ?? 0);
$user_id = (int)($_GET['user_id'] ?? 0);

if ($log_id <= 0 || $user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid log ID or user ID"]);
    exit;
}

$sql = "SELECT hl.log_id, hl.pet_id, p.pet_name, hl.category_id, c.category_name,
               hl.log_title, hl.description, hl.status, hl.log_date
        FROM health_logs hl
        JOIN pets p ON hl.pet_id = p.pet_id
        JOIN categories c ON hl.category_id = c.category_id
        WHERE hl.log_id = ? AND p.user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $log_id, $user_id);
$stmt->execute();
$log = $stmt->get_result()->fetch_assoc();

if ($log) {
    echo json_encode(["success" => true, "log" => $log]);
} else {
    echo json_encode(["success" => false, "message" => "Health log not found"]);
}

$stmt->close();
$conn->close();
?>
