<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// Sanitize inputs
$car_id          = intval($_POST['car_id'] ?? 0);
$full_name       = sanitize($_POST['full_name'] ?? '');
$id_number       = sanitize($_POST['id_number'] ?? '');
$email           = sanitize($_POST['email'] ?? '');
$phone           = sanitize($_POST['phone'] ?? '');
$driver_license  = sanitize($_POST['driver_license'] ?? '');
$address         = sanitize($_POST['address'] ?? '');
$hire_date       = sanitize($_POST['hire_date'] ?? '');
$return_date     = sanitize($_POST['return_date'] ?? '');

// Basic validation
if (!$car_id || !$full_name || !$id_number || !$email || !$phone || !$driver_license || !$hire_date || !$return_date) {
    die('Please fill in all required fields. <a href="index.php">Go back</a>');
}

// Fetch car to compute total
$stmt = $conn->prepare("SELECT price_per_day FROM car_listings WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car_result = $stmt->get_result();

if ($car_result->num_rows === 0) {
    die('Car not available. <a href="index.php">Go back</a>');
}

$car = $car_result->fetch_assoc();
$price_per_day = $car['price_per_day'];
$stmt->close();

// Calculate total days
$start = new DateTime($hire_date);
$end   = new DateTime($return_date);
$days  = $start->diff($end)->days;
if ($days < 1) $days = 1;
$total_amount = $days * $price_per_day;

// Insert customer into car_customers
$stmt = $conn->prepare("INSERT INTO car_customers (full_name, email, phone, id_number, driver_license, address) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $full_name, $email, $phone, $id_number, $driver_license, $address);

if (!$stmt->execute()) {
    die('Error saving customer: ' . $stmt->error);
}
$customer_id = $stmt->insert_id;
$stmt->close();

// Insert booking into car_bookings
$stmt = $conn->prepare("INSERT INTO car_bookings (customer_id, car_id, hire_date, return_date, total_amount) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iissd", $customer_id, $car_id, $hire_date, $return_date, $total_amount);

if (!$stmt->execute()) {
    die('Error saving booking: ' . $stmt->error);
}
$booking_id = $stmt->insert_id;
$stmt->close();

// Mark car as hired
$stmt = $conn->prepare("UPDATE car_listings SET status = 'hired' WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect to thank-you page
header("Location: thank_you.php?booking_id=" . $booking_id);
exit;
?>