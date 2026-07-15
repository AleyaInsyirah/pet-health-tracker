<?php
include 'db_connect.php';

$pet_id = $_GET['pet_id'];

$sql = "SELECT * FROM weight_logs WHERE pet_id = ? ORDER BY log_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

echo json_encode(["success" => true, "weight_logs" => $logs]);

$stmt->close();
$conn->close();
?>