<?php
require_once 'config.php';
$conn = getDBConnection();

$car_id = intval($_GET['id'] ?? 0);
if (!$car_id) {
    header('Location: fleet.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM car_listings WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $car_id);
$stmt->execute();
$car = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$car) {
    header('Location: error.php?code=404');
    exit;
}

// Get related cars (same brand, exclude this one)
$stmt = $conn->prepare("SELECT * FROM car_listings WHERE brand = ? AND id != ? AND status = 'available' LIMIT 3");
$stmt->bind_param("si", $car['brand'], $car_id);
$stmt->execute();
$related = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$page_title  = $car['brand'] . ' ' . $car['model'] . ' Hire | DriveElite';
$active_page = 'fleet';
include 'header.php';
?>

<section class="page-header car-detail-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="fleet.php">Fleet</a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></span>
        </nav>
        <h1><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h1>
        <p>Year <?php echo $car['year']; ?> &bull; <?php echo htmlspecialchars($car['color']); ?> &bull; <?php echo htmlspecialchars($car['transmission']); ?></p>
    </div>
</section>

<section class="car-detail-section">
    <div class="container">
        <div class="car-detail-grid">

            <!-- LEFT: IMAGE & SPECS -->
            <div class="car-detail-main">
                <div class="car-hero-image">
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>"
                         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                </div>

                <div class="car-detail-card">
                    <h2><i class="fas fa-list-check"></i> Specifications</h2>
                    <div class="specs-grid">
                        <div class="spec-item"><i class="fas fa-car"></i><div><span>Brand</span><strong><?php echo htmlspecialchars($car['brand']); ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-tag"></i><div><span>Model</span><strong><?php echo htmlspecialchars($car['model']); ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-calendar"></i><div><span>Year</span><strong><?php echo $car['year']; ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-chair"></i><div><span>Seats</span><strong><?php echo $car['seats']; ?> Persons</strong></div></div>
                        <div class="spec-item"><i class="fas fa-gas-pump"></i><div><span>Fuel</span><strong><?php echo htmlspecialchars($car['fuel_type']); ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-cog"></i><div><span>Transmission</span><strong><?php echo htmlspecialchars($car['transmission']); ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-palette"></i><div><span>Color</span><strong><?php echo htmlspecialchars($car['color']); ?></strong></div></div>
                        <div class="spec-item"><i class="fas fa-id-card"></i><div><span>Plate</span><strong><?php echo htmlspecialchars($car['plate_number']); ?></strong></div></div>
                    </div>
                </div>

                <div class="car-detail-card">
                    <h2><i class="fas fa-shield-alt"></i> Rental Includes</h2>
                    <div class="includes-grid">
                        <div class="include-item"><i class="fas fa-check-circle"></i> Comprehensive Insurance</div>
                        <div class="include-item"><i class="fas fa-check-circle"></i> 24/7 Roadside Assistance</div>
                        <div class="include-item"><i class="fas fa-check-circle"></i> Unlimited Mileage</div>
                        <div class="include-item"><i class="fas fa-check-circle"></i> Free Cancellation (24h)</div>
                        <div class="include-item"><i class="fas fa-check-circle"></i> Third-Party Liability</div>
                        <div class="include-item"><i class="fas fa-check-circle"></i> Theft Protection</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: STICKY BOOKING BOX -->
            <aside class="car-booking-sidebar">
                <div class="booking-box">
                    <div class="booking-price">
                        <span>From</span>
                        <strong><?php echo formatCurrency($car['price_per_day']); ?></strong>
                        <small>/ day</small>
                    </div>

                    <button class="btn-hire btn-hire-large"
                            onclick='openBookingModal(<?php echo json_encode($car, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                        <i class="fas fa-key"></i> Book This Car
                    </button>

                    <div class="booking-guarantee">
                        <p><i class="fas fa-lock"></i> Secure Booking</p>
                        <p><i class="fas fa-clock"></i> Instant Confirmation</p>
                        <p><i class="fas fa-undo"></i> Free Cancellation</p>
                    </div>

                    <div class="booking-contact">
                        <p>Need help? Call us:</p>
                        <a href="tel:+254746674121"><i class="fas fa-phone"></i> +254 746 674 121</a>
                    </div>
                </div>
            </aside>

        </div>

        <?php if (!empty($related)): ?>
            <div class="related-cars">
                <h2>Similar Vehicles</h2>
                <div class="car-grid">
                    <?php foreach ($related as $r): ?>
                        <div class="car-card">
                            <div class="car-image">
                                <img src="<?php echo htmlspecialchars($r['image_url']); ?>" alt="<?php echo htmlspecialchars($r['brand'] . ' ' . $r['model']); ?>" loading="lazy">
                                <span class="car-badge"><?php echo htmlspecialchars($r['brand']); ?></span>
                            </div>
                            <div class="car-info">
                                <h3><?php echo htmlspecialchars($r['brand'] . ' ' . $r['model']); ?></h3>
                                <div class="car-price"><?php echo formatCurrency($r['price_per_day']); ?> <small>/ day</small></div>
                                <a href="car_detail.php?id=<?php echo $r['id']; ?>" class="btn-details btn-full">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>