<?php
require_once 'config.php';
header('Content-Type: application/json');

$conn = getDBConnection();

$brand = isset($_GET['brand']) ? sanitize($_GET['brand']) : '';

if ($brand) {
    $stmt = $conn->prepare("SELECT * FROM car_listings WHERE status = 'available' AND brand = ? ORDER BY model");
    $stmt->bind_param("s", $brand);
} else {
    $stmt = $conn->prepare("SELECT * FROM car_listings WHERE status = 'available' ORDER BY brand, model");
}

$stmt->execute();
$result = $stmt->get_result();

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($cars);
?>