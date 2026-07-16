<?php
include 'db_connect.php';

$user_id = (int)($_POST['user_id'] ?? 0);
$pet_name = trim($_POST['pet_name'] ?? '');
$breed = trim($_POST['breed'] ?? '');
$age = (int)($_POST['age'] ?? 0);
$weight = (float)($_POST['weight'] ?? 0);
$pet_photo = trim($_POST['pet_photo'] ?? '');

if ($user_id <= 0 || $pet_name === '') {
    echo json_encode(["success" => false, "message" => "Missing user ID or pet name"]);
    exit;
}

$sql = "INSERT INTO pets (user_id, pet_name, breed, age, weight, pet_photo) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("issids", $user_id, $pet_name, $breed, $age, $weight, $pet_photo);

if ($stmt->execute()) {
    $new_pet_id = $stmt->insert_id;

    if ($weight > 0) {
        $weight_stmt = $conn->prepare("INSERT INTO weight_logs (pet_id, weight, log_date) VALUES (?, ?, CURDATE())");
        $weight_stmt->bind_param("id", $new_pet_id, $weight);
        $weight_stmt->execute();
        $weight_stmt->close();
    }

    echo json_encode(["success" => true, "message" => "Pet added successfully", "pet_id" => $new_pet_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add pet: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
