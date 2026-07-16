<?php
include 'db_connect.php';

$user_id = (int)($_GET['user_id'] ?? 0);
$pet_id = (int)($_GET['pet_id'] ?? 0);
$category = trim($_GET['category'] ?? '');

if ($user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit;
}

$pet_stmt = $conn->prepare("SELECT pet_id, pet_name FROM pets WHERE user_id = ? ORDER BY pet_name");
$pet_stmt->bind_param("i", $user_id);
$pet_stmt->execute();
$pet_result = $pet_stmt->get_result();
$pets = [];
$pet_ids = [0];
$pet_names = ['All Pets'];
while ($pet = $pet_result->fetch_assoc()) {
    $pets[] = $pet;
    $pet_ids[] = (int)$pet['pet_id'];
    $pet_names[] = $pet['pet_name'];
}
$pet_stmt->close();

$sql = "SELECT hl.log_id, hl.pet_id, p.pet_name, hl.category_id, c.category_name,
               hl.log_title, hl.description, hl.status, hl.log_date,
               CONCAT(hl.log_title, ' | ', p.pet_name, ' | ', c.category_name, ' | ', hl.log_date) AS display_text
        FROM health_logs hl
        JOIN pets p ON hl.pet_id = p.pet_id
        JOIN categories c ON hl.category_id = c.category_id
        WHERE p.user_id = ?";
$types = "i";
$params = [$user_id];

if ($pet_id > 0) {
    $sql .= " AND hl.pet_id = ?";
    $types .= "i";
    $params[] = $pet_id;
}
if ($category !== '' && $category !== 'All Categories') {
    $sql .= " AND c.category_name = ?";
    $types .= "s";
    $params[] = $category;
}
$sql .= " ORDER BY hl.log_date DESC, hl.log_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$logs = [];
$log_ids = [];
$log_texts = [];
while ($log = $result->fetch_assoc()) {
    $logs[] = $log;
    $log_ids[] = (int)$log['log_id'];
    $log_texts[] = $log['display_text'];
}

$active_sql = "SELECT CONCAT(hl.log_title, ' | ', p.pet_name, ' | ', c.category_name, ' | ', hl.log_date) AS display_text
               FROM health_logs hl
               JOIN pets p ON hl.pet_id = p.pet_id
               JOIN categories c ON hl.category_id = c.category_id
               WHERE p.user_id = ? AND hl.status = 'Pending'
               ORDER BY hl.log_date ASC, hl.log_id ASC
               LIMIT 1";
$active_stmt = $conn->prepare($active_sql);
$active_stmt->bind_param("i", $user_id);
$active_stmt->execute();
$active_row = $active_stmt->get_result()->fetch_assoc();
$active_timeline = $active_row['display_text'] ?? 'No active treatment';
$active_stmt->close();

echo json_encode([
    "success" => true,
    "pets" => $pets,
    "pet_ids" => $pet_ids,
    "pet_names" => $pet_names,
    "logs" => $logs,
    "log_ids" => $log_ids,
    "log_texts" => $log_texts,
    "active_timeline" => $active_timeline
]);

$stmt->close();
$conn->close();
?>
