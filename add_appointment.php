<?php
include 'db_connect.php';

$pet_id = $_POST['pet_id'];
$title = $_POST['title'];
$appointment_date = $_POST['appointment_date']; // format: YYYY-MM-DD HH:MM:SS
$location = $_POST['location'];
$notes = $_POST['notes'];

$sql = "INSERT INTO appointments (pet_id, title, appointment_date, location, notes) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("issss", $pet_id, $title, $appointment_date, $location, $notes);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Appointment added", "appointment_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add appointment: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>