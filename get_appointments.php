<?php
include 'db_connect.php';

$pet_id = $_GET['pet_id'];
$status = isset($_GET['status']) ? $_GET['status'] : null;

if ($status) {
    $sql = "SELECT * FROM appointments WHERE pet_id = ? AND status = ? ORDER BY appointment_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $pet_id, $status);
} else {
    $sql = "SELECT * FROM appointments WHERE pet_id = ? ORDER BY appointment_date ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pet_id);
}

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode(["success" => true, "appointments" => $appointments]);

$stmt->close();
$conn->close();
?>