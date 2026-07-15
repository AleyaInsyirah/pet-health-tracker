<?php
include 'db_connect.php';

$pet_id = $_GET['pet_id'];
$status = isset($_GET['status']) ? $_GET['status'] : null; // optional filter

if ($status) {
    $sql = "SELECT health_logs.*, categories.category_name, categories.category_icon 
            FROM health_logs 
            JOIN categories ON health_logs.category_id = categories.category_id
            WHERE pet_id = ? AND status = ? 
            ORDER BY log_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $pet_id, $status);
} else {
    $sql = "SELECT health_logs.*, categories.category_name, categories.category_icon 
            FROM health_logs 
            JOIN categories ON health_logs.category_id = categories.category_id
            WHERE pet_id = ? 
            ORDER BY log_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pet_id);
}

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode(["success" => true, "logs" => $logs]);

$stmt->close();
$conn->close();
?>