<?php
include 'db_connect.php';

$pet_id = (int)($_POST['pet_id'] ?? 0);
$user_id = (int)($_POST['user_id'] ?? 0);
$pet_name = trim($_POST['pet_name'] ?? '');
$breed = trim($_POST['breed'] ?? '');
$age = (int)($_POST['age'] ?? 0);
$weight = (float)($_POST['weight'] ?? 0);
$pet_photo = trim($_POST['pet_photo'] ?? '');

if ($pet_id <= 0 || $user_id <= 0 || $pet_name === '' || $age < 0 || $weight < 0) {
    echo json_encode(["success" => false, "message" => "Invalid or incomplete pet data"]);
    exit;
}

$old_stmt = $conn->prepare("SELECT weight FROM pets WHERE pet_id = ? AND user_id = ? LIMIT 1");
$old_stmt->bind_param("ii", $pet_id, $user_id);
$old_stmt->execute();
$old_pet = $old_stmt->get_result()->fetch_assoc();
$old_stmt->close();

$stmt = $conn->prepare("UPDATE pets SET pet_name = ?, breed = ?, age = ?, weight = ?, pet_photo = ? WHERE pet_id = ? AND user_id = ?");
$stmt->bind_param("ssidsii", $pet_name, $breed, $age, $weight, $pet_photo, $pet_id, $user_id);
$stmt->execute();

if ($old_pet && (float)$old_pet['weight'] !== $weight && $weight > 0) {
    $weight_stmt = $conn->prepare("INSERT INTO weight_logs (pet_id, weight, log_date) VALUES (?, ?, CURDATE())");
    $weight_stmt->bind_param("id", $pet_id, $weight);
    $weight_stmt->execute();
    $weight_stmt->close();
}

echo json_encode(["success" => true, "message" => $stmt->affected_rows > 0 ? "Pet profile updated" : "No changes were made"]);

$stmt->close();
$conn->close();
?>
