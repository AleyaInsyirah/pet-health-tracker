<?php
include 'db_connect.php';

$pet_id = $_POST['pet_id'];
$weight = $_POST['weight'];
$log_date = $_POST['log_date'];

$sql = "INSERT INTO weight_logs (pet_id, weight, log_date) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("ids", $pet_id, $weight, $log_date);

if ($stmt->execute()) {
    // also update the pet's current weight in the pets table
    $update_sql = "UPDATE pets SET weight = ? WHERE pet_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("di", $weight, $pet_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo json_encode(["success" => true, "message" => "Weight log added"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add weight log: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>