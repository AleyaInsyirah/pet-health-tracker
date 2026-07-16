<?php
include 'db_connect.php';

$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit;
}

$summary_sql = "SELECT
    (SELECT COUNT(*)
     FROM health_logs hl
     JOIN pets p ON hl.pet_id = p.pet_id
     WHERE p.user_id = ?) AS total_logs,
    (SELECT COUNT(*)
     FROM health_logs hl
     JOIN pets p ON hl.pet_id = p.pet_id
     JOIN categories c ON hl.category_id = c.category_id
     WHERE p.user_id = ?
       AND c.category_name = 'Vaccination'
       AND hl.status = 'Pending') AS upcoming_boosters,
    (SELECT COUNT(*)
     FROM appointments a
     JOIN pets p ON a.pet_id = p.pet_id
     WHERE p.user_id = ?
       AND a.status = 'Upcoming') AS upcoming_appointments";
$stmt = $conn->prepare($summary_sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

$weight_sql = "SELECT p.weight FROM pets p WHERE p.user_id = ? ORDER BY p.pet_id ASC LIMIT 1";
$stmt = $conn->prepare($weight_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$weight_row = $stmt->get_result()->fetch_assoc();
$stmt->close();

$category_sql = "SELECT c.category_name, COUNT(*) AS total
                 FROM health_logs hl
                 JOIN pets p ON hl.pet_id = p.pet_id
                 JOIN categories c ON hl.category_id = c.category_id
                 WHERE p.user_id = ?
                 GROUP BY c.category_id, c.category_name
                 ORDER BY total DESC";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

$weight_sql = "SELECT wl.log_date, wl.weight
               FROM weight_logs wl
               JOIN pets p ON wl.pet_id = p.pet_id
               WHERE p.user_id = ?
               ORDER BY wl.log_date ASC, wl.weight_id ASC";
$stmt = $conn->prepare($weight_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$weight_trend = [];
while ($row = $result->fetch_assoc()) {
    $weight_trend[] = $row;
}
$stmt->close();

$recent_sql = "SELECT CONCAT(hl.log_title, ' | ', p.pet_name, ' | ', hl.log_date) AS display_text
               FROM health_logs hl
               JOIN pets p ON hl.pet_id = p.pet_id
               WHERE p.user_id = ?
               ORDER BY hl.log_date DESC, hl.log_id DESC
               LIMIT 5";
$stmt = $conn->prepare($recent_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_activity = [];
while ($row = $result->fetch_assoc()) {
    $recent_activity[] = $row['display_text'];
}

echo json_encode([
    "success" => true,
    "summary" => [
        "total_logs" => (int)($summary['total_logs'] ?? 0),
        "upcoming_boosters" => (int)($summary['upcoming_boosters'] ?? 0),
        "current_weight" => $weight_row['weight'] ?? 0,
        "upcoming_appointments" => (int)($summary['upcoming_appointments'] ?? 0)
    ],
    "categories" => $categories,
    "weight_trend" => $weight_trend,
    "recent_activity" => $recent_activity
]);

$stmt->close();
$conn->close();
?>
