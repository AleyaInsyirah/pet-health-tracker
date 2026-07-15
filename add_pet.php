<?php
include 'db_connect.php';

$user_id = $_POST['user_id'];
$pet_name = $_POST['pet_name'];
$breed = $_POST['breed'];
$age = $_POST['age'];
$weight = $_POST['weight'];
$pet_photo = $_POST['pet_photo']; // for now, just a text/URL, image upload we'll handle later

$sql = "INSERT INTO pets (user_id, pet_name, breed, age, weight, pet_photo) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]));
}

$stmt->bind_param("issdis", $user_id, $pet_name, $breed, $age, $weight, $pet_photo);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Pet added successfully", "pet_id" => $stmt->insert_id]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to add pet: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>