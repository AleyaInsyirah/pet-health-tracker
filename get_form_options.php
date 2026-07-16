<?php
include 'db_connect.php';

$user_id = (int)($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit;
}

$stmt = $conn->prepare("SELECT pet_id, pet_name FROM pets WHERE user_id = ? ORDER BY pet_name");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$pets = [];
$pet_ids = [];
$pet_names = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
    $pet_ids[] = (int)$row['pet_id'];
    $pet_names[] = $row['pet_name'];
}
$stmt->close();

$result = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_id");
$categories = [];
$category_ids = [];
$category_names = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
    $category_ids[] = (int)$row['category_id'];
    $category_names[] = $row['category_name'];
}

echo json_encode([
    "success" => true,
    "pets" => $pets,
    "pet_ids" => $pet_ids,
    "pet_names" => $pet_names,
    "categories" => $categories,
    "category_ids" => $category_ids,
    "category_names" => $category_names
]);
$conn->close();
?>
