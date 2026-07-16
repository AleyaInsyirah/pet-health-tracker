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

$appointment_sql = "SELECT a.title, a.appointment_date, p.pet_name
                    FROM appointments a
                    JOIN pets p ON a.pet_id = p.pet_id
                    WHERE p.user_id = ?
                      AND a.status = 'Upcoming'
                      AND a.appointment_date >= NOW()
                    ORDER BY a.appointment_date ASC
                    LIMIT 1";
$appointment_stmt = $conn->prepare($appointment_sql);
$appointment_stmt->bind_param("i", $user_id);
$appointment_stmt->execute();
$appointment = $appointment_stmt->get_result()->fetch_assoc();
$appointment_stmt->close();

$next_appointment = $appointment
    ? $appointment['title'] . " | " . $appointment['pet_name'] . " | " . $appointment['appointment_date']
    : "No upcoming appointment";

echo json_encode([
    "success" => true,
    "pets" => $pets,
    "next_appointment" => $next_appointment
]);

$stmt->close();
$conn->close();
?>
