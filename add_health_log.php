<?php
include 'db_connect.php';

$pet_id = $_POST['pet_id'];
$category_id = $_POST['category_id'];
$log_title = $_POST['log_title'];
$description = $_POST['description'];
$status = $_POST['status']; // 'Pending', 'Completed', or 'Administered'
$log_date = $_POST['log_date']; // format: YYYY-MM-DD

$sql = "INSERT INTO health_logs (pet_id, category_id, log_title, description, status, log_date) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("iissss", $pet_id, $category_id, $log_title, $description, $status, $log_date);

if ($stmt->execute()) {
    if ((int)$category_id === 3 && $status === 'Pending') {
        $appointment_date = $log_date . ' 09:00:00';
        $appointment_status = 'Upcoming';
        $appointment_stmt = $conn->prepare(
            "INSERT INTO appointments (pet_id, title, appointment_date, notes, status) VALUES (?, ?, ?, ?, ?)"
        );
        $appointment_stmt->bind_param(
            "issss",
            $pet_id,
            $log_title,
            $appointment_date,
            $description,
            $appointment_status
        );
        $appointment_stmt->execute();
        $appointment_stmt->close();
    }

    echo json_encode(["success" => true, "message" => "Health log added", "log_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add log: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
