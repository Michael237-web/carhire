<?php
require_once 'config.php';
$conn = getDBConnection();

$booking_id = intval($_GET['booking_id'] ?? 0);
$booking = null;

if ($booking_id) {
    $sql = "SELECT b.*, c.full_name, c.email, c.phone, c.id_number, c.driver_license,
                   cl.brand, cl.model, cl.plate_number, cl.color, cl.image_url
            FROM car_bookings b
            JOIN car_customers c ON b.customer_id = c.id
            JOIN car_listings cl ON b.car_id = cl.id
            WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Confirmed | DriveElite</title>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.thankyou-wrapper {
    max-width: 800px;
    margin: 4rem auto;
    padding: 3rem;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    text-align: center;
}
.thankyou-wrapper .success-icon {
    font-size: 5rem;
    color: #10b981;
    margin-bottom: 1.5rem;
}
.thankyou-wrapper h1 {
    color: #1a3a5c;
    font-size: 2.2rem;
    margin-bottom: 0.8rem;
}
.thankyou-wrapper p.lead {
    color: #6b7280;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}
.receipt {
    text-align: left;
    background: #f9fafb;
    border-radius: 16px;
    padding: 2rem;
    margin: 2rem 0;
    border: 1px solid #e5e7eb;
}
.receipt h3 {
    color: #1a3a5c;
    margin-bottom: 1rem;
    border-bottom: 2px solid #3b82f6;
    padding-bottom: 0.5rem;
    display: inline-block;
}
.receipt .row {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    border-bottom: 1px dashed #e5e7eb;
}
.receipt .row:last-child { border-bottom: none; }
.receipt .row .label { color: #6b7280; font-weight: 500; }
.receipt .row .value { color: #1a3a5c; font-weight: 600; }
.total-row {
    background: #eff6ff;
    margin-top: 1rem;
    padding: 1rem 1.5rem !important;
    border-radius: 12px;
    border: none !important;
}
.total-row .value {
    color: #3b82f6 !important;
    font-size: 1.5rem;
}
.back-btn {
    display: inline-block;
    background: #1a3a5c;
    color: #fff;
    padding: 0.9rem 2.5rem;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s;
}
.back-btn:hover { background: #3b82f6; }
</style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><i class="fas fa-car-side"></i><span>DriveElite</span></a>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="thankyou-wrapper">
        <i class="fas fa-check-circle success-icon"></i>
        <h1>Booking Confirmed!</h1>
        <p class="lead">Thank you for choosing DriveElite. Your booking has been received successfully.</p>

        <?php if ($booking): ?>
        <div class="receipt">
            <h3>Booking Receipt #<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></h3>
            <div class="row"><span class="label">Customer Name</span><span class="value"><?php echo htmlspecialchars($booking['full_name']); ?></span></div>
            <div class="row"><span class="label">ID Number</span><span class="value"><?php echo htmlspecialchars($booking['id_number']); ?></span></div>
            <div class="row"><span class="label">Driver's License</span><span class="value"><?php echo htmlspecialchars($booking['driver_license']); ?></span></div>
            <div class="row"><span class="label">Phone</span><span class="value"><?php echo htmlspecialchars($booking['phone']); ?></span></div>
            <div class="row"><span class="label">Email</span><span class="value"><?php echo htmlspecialchars($booking['email']); ?></span></div>
            <div class="row"><span class="label">Car</span><span class="value"><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></span></div>
            <div class="row"><span class="label">Plate Number</span><span class="value"><?php echo htmlspecialchars($booking['plate_number']); ?></span></div>
            <div class="row"><span class="label">Color</span><span class="value"><?php echo htmlspecialchars($booking['color']); ?></span></div>
            <div class="row"><span class="label">Hire Date</span><span class="value"><?php echo htmlspecialchars($booking['hire_date']); ?></span></div>
            <div class="row"><span class="label">Return Date</span><span class="value"><?php echo htmlspecialchars($booking['return_date']); ?></span></div>
            <div class="row total-row"><span class="label">Total Amount</span><span class="value"><?php echo formatCurrency($booking['total_amount']); ?></span></div>
        </div>
        <?php else: ?>
        <p>We could not find your booking details. Please contact support.</p>
        <?php endif; ?>

        <a href="index.php" class="back-btn"><i class="fas fa-home"></i> Back to Home</a>
    </div>
</div>

</body>
</html>