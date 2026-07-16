<?php
include 'db_connect.php';

$log_id = (int)($_POST['log_id'] ?? 0);
$user_id = (int)($_POST['user_id'] ?? 0);
$category_id = (int)($_POST['category_id'] ?? 0);
$log_title = trim($_POST['log_title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = trim($_POST['status'] ?? 'Pending');
$log_date = trim($_POST['log_date'] ?? '');

$allowed_statuses = ['Pending', 'Completed', 'Administered'];
if ($log_id <= 0 || $user_id <= 0 || $category_id <= 0 || $log_title === '' || $log_date === '' || !in_array($status, $allowed_statuses, true)) {
    echo json_encode(["success" => false, "message" => "Invalid or incomplete data"]);
    exit;
}

$sql = "UPDATE health_logs hl
        JOIN pets p ON hl.pet_id = p.pet_id
        SET hl.category_id = ?, hl.log_title = ?, hl.description = ?, hl.status = ?, hl.log_date = ?
        WHERE hl.log_id = ? AND p.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssii", $category_id, $log_title, $description, $status, $log_date, $log_id, $user_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => $stmt->affected_rows > 0 ? "Health log updated" : "No changes were made"
]);

$stmt->close();
$conn->close();
?>
