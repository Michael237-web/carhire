<?php
require_once 'config.php';

$is_overlay = isset($_GET['overlay']) && $_GET['overlay'] == '1';

$page_title  = 'About Us | DriveElite';
$active_page = 'about';

if (!$is_overlay) {
    include 'header.php';
} else {
    // Minimal head for iframe mode
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="overlay-mode">
    <?php
}
?>

<!-- PAGE HEADER -->
<section class="page-header">
    <div class="container">
        <h1>About DriveElite</h1>
        <p>Your trusted partner for premium car hire since 2015</p>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2>Driven by Quality, Powered by Trust</h2>
                <p>DriveElite is a leading car hire company dedicated to providing premium vehicles and exceptional customer service. For nearly a decade, we've helped thousands of travelers, businesses, and families find the perfect ride for every occasion.</p>
                <p>From executive sedans for business trips to rugged 4x4 SUVs for safaris and adventures, our diverse fleet is meticulously maintained and inspected before every hire. We believe that a reliable car should never be a compromise.</p>
                <p>Our commitment goes beyond the vehicle — we offer transparent pricing, flexible rental terms, 24/7 roadside assistance, and a dedicated support team that genuinely cares about your journey.</p>
            </div>
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=800&auto=format&fit=crop&q=80" alt="About DriveElite">
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <i class="fas fa-car"></i>
                <h3>150+</h3>
                <p>Vehicles in Fleet</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-smile"></i>
                <h3>10,000+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-award"></i>
                <h3>9+</h3>
                <p>Years of Experience</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-headset"></i>
                <h3>24/7</h3>
                <p>Customer Support</p>
            </div>
        </div>
    </div>
</section>

<!-- VALUES -->
<section class="values-section">
    <div class="container">
        <h2 class="section-title">Why Choose Us</h2>
        <p class="section-subtitle">What makes DriveElite different from the rest</p>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Fully Insured</h3>
                <p>Every vehicle is comprehensively insured so you can drive with complete peace of mind.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>Best Prices</h3>
                <p>Competitive rates with no hidden fees. What you see is what you pay — always.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-tools"></i>
                <h3>Well Maintained</h3>
                <p>Our fleet is serviced regularly and inspected before every single hire.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-clock"></i>
                <h3>Flexible Terms</h3>
                <p>Daily, weekly, or monthly — choose the rental duration that suits you best.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Nationwide Coverage</h3>
                <p>Pick up and drop off at multiple locations across the country.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-phone-volume"></i>
                <h3>24/7 Support</h3>
                <p>Our team is always just a phone call away, day or night.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!$is_overlay): ?>
    <?php include 'footer.php'; ?>
<?php else: ?>
    </body>
    </html>
<?php endif; ?>