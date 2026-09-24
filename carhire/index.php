<?php
require_once 'config.php';
$conn = getDBConnection();

$sql = "SELECT * FROM car_listings WHERE status = 'available' ORDER BY brand, model";
$result = $conn->query($sql);
$cars = [];
while($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

$brands_sql = "SELECT DISTINCT brand FROM car_listings ORDER BY brand";
$brands_result = $conn->query($brands_sql);
$brands = [];
while($row = $brands_result->fetch_assoc()) {
    $brands[] = $row['brand'];
}
$conn->close();

$page_title  = 'DriveElite | Premium Car Hire Services';
$active_page = 'home';

include 'header.php';
?>

<main class="view-container" id="viewContainer">

    <!-- ============================================= -->
    <!-- VIEW: HOME                                     -->
    <!-- ============================================= -->
    <section class="view active" id="view-home" data-view="home">

        <section class="hero-slideshow">
            <div class="slideshow-container">
                <div class="slide active">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1600&auto=format&fit=crop&q=80" alt="Luxury Car">
                    <div class="slide-content">
                        <span class="slide-badge">Premium Selection</span>
                        <h2>Drive the Extraordinary</h2>
                        <p>Experience luxury and performance with our premium fleet of vehicles.</p>
                        <a href="#fleet" class="btn-primary" data-nav="fleet">
                            <i class="fas fa-car"></i> Explore Fleet
                        </a>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1594502184342-2e12f877aa73?w=1600&auto=format&fit=crop&q=80" alt="Porsche SUV">
                    <div class="slide-content">
                        <span class="slide-badge">4x4 Adventure</span>
                        <h2>Porsche Cayenne</h2>
                        <p>Rugged SUV, 5 seats, and legendary reliability for any terrain.</p>
                        <a href="#fleet" class="btn-primary" data-nav="fleet">
                            <i class="fas fa-truck-pickup"></i> View SUVs
                        </a>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=1600&auto=format&fit=crop&q=80" alt="Mercedes C-Class">
                    <div class="slide-content">
                        <span class="slide-badge">Business Class</span>
                        <h2>Mercedes C-Class</h2>
                        <p>Arrive in style with our executive sedan collection.</p>
                        <a href="#fleet" class="btn-primary" data-nav="fleet">
                            <i class="fas fa-car-side"></i> Book Sedan
                        </a>
                    </div>
                </div>
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1555215695-3004980ad54e?w=1600&auto=format&fit=crop&q=80" alt="BMW X5">
                    <div class="slide-content">
                        <span class="slide-badge">Luxury SUV</span>
                        <h2>BMW X5</h2>
                        <p>Combining power, elegance, and cutting-edge technology.</p>
                        <a href="#fleet" class="btn-primary" data-nav="fleet">
                            <i class="fas fa-arrow-right"></i> Discover More
                        </a>
                    </div>
                </div>
                <button class="slide-nav prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
                <button class="slide-nav next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
                <div class="slide-dots"></div>
            </div>
        </section>

        <section class="brand-filter">
            <div class="container">
                <h2 class="section-title">Choose Your Brand</h2>
                <p class="section-subtitle">Click a brand to view available models</p>
                <div class="brand-grid">
                    <?php foreach ($brands as $brand): 
                        $logo = $brand_logos[$brand] ?? null;
                        $icon = $brand_icons[$brand] ?? 'fa-car';
                    ?>
                    <a href="#fleet" 
                       class="brand-card" 
                       data-brand="<?php echo htmlspecialchars($brand); ?>" 
                       onclick="event.preventDefault(); goToView('fleet'); setTimeout(() => { filterByBrand('<?php echo htmlspecialchars($brand); ?>', this); scrollToFleet(); }, 550);">
                        <?php if ($logo): ?>
                            <img src="<?php echo $logo; ?>" 
                                 alt="<?php echo htmlspecialchars($brand); ?> logo" 
                                 loading="lazy"
                                 referrerpolicy="no-referrer"
                                 onerror="this.outerHTML='<div class=\'brand-icon-fallback\'><i class=\'fas <?php echo $icon; ?>\'></i></div>';">
                        <?php else: ?>
                            <div class="brand-icon-fallback"><i class="fas <?php echo $icon; ?>"></i></div>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($brand); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </section>

    <!-- ============================================= -->
    <!-- VIEW: ABOUT                                    -->
    <!-- ============================================= -->
    <section class="view" id="view-about" data-view="about">
        <section class="page-header">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="#home" data-nav="home">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>About</span>
                </nav>
                <h1>About DriveElite</h1>
                <p>Your trusted partner for premium car hire since 2015</p>
            </div>
        </section>

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

        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-item"><i class="fas fa-car"></i><h3>150+</h3><p>Vehicles in Fleet</p></div>
                    <div class="stat-item"><i class="fas fa-smile"></i><h3>10,000+</h3><p>Happy Customers</p></div>
                    <div class="stat-item"><i class="fas fa-award"></i><h3>9+</h3><p>Years of Experience</p></div>
                    <div class="stat-item"><i class="fas fa-headset"></i><h3>24/7</h3><p>Customer Support</p></div>
                </div>
            </div>
        </section>

        <section class="values-section">
            <div class="container">
                <h2 class="section-title">Why Choose Us</h2>
                <p class="section-subtitle">What makes DriveElite different from the rest</p>
                <div class="values-grid">
                    <div class="value-card"><i class="fas fa-shield-alt"></i><h3>Fully Insured</h3><p>Every vehicle is comprehensively insured so you can drive with complete peace of mind.</p></div>
                    <div class="value-card"><i class="fas fa-dollar-sign"></i><h3>Best Prices</h3><p>Competitive rates with no hidden fees. What you see is what you pay — always.</p></div>
                    <div class="value-card"><i class="fas fa-tools"></i><h3>Well Maintained</h3><p>Our fleet is serviced regularly and inspected before every single hire.</p></div>
                    <div class="value-card"><i class="fas fa-clock"></i><h3>Flexible Terms</h3><p>Daily, weekly, or monthly — choose the rental duration that suits you best.</p></div>
                    <div class="value-card"><i class="fas fa-map-marked-alt"></i><h3>Nationwide Coverage</h3><p>Pick up and drop off at multiple locations across the country.</p></div>
                    <div class="value-card"><i class="fas fa-phone-volume"></i><h3>24/7 Support</h3><p>Our team is always just a phone call away, day or night.</p></div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container">
                <div class="cta-card">
                    <div>
                        <h2>Ready to hit the road?</h2>
                        <p>Browse our premium fleet and book your perfect car in minutes.</p>
                    </div>
                    <a href="#fleet" class="btn-primary cta-btn" data-nav="fleet">
                        <i class="fas fa-key"></i> Book Now
                    </a>
                </div>
            </div>
        </section>
    </section>

    <!-- ============================================= -->
    <!-- VIEW: CONTACT                                  -->
    <!-- ============================================= -->
    <section class="view" id="view-contact" data-view="contact">
        <section class="page-header">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="#home" data-nav="home">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Contact</span>
                </nav>
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you. Reach out anytime.</p>
            </div>
        </section>

        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <div class="contact-info">
                        <h3>Contact Information</h3>
                        <div class="info-item"><i class="fas fa-map-marker-alt"></i><div><h4>Visit Us</h4><p>123 Kenyatta Avenue<br>Nairobi, Kenya</p></div></div>
                        <div class="info-item"><i class="fas fa-phone"></i><div><h4>Call Us</h4><p>+254 746674121</p></div></div>
                        <div class="info-item"><i class="fas fa-envelope"></i><div><h4>Email Us</h4><p>info@michael.com</p></div></div>
                        <div class="info-item"><i class="fas fa-clock"></i><div><h4>Working Hours</h4><p>Mon - Fri: 8:00 AM - 8:00 PM<br>Sat - Sun: 9:00 AM - 6:00 PM</p></div></div>
                    </div>

                    <div class="contact-form">
                        <h3>Send Us a Message</h3>
                        <form id="contactForm" onsubmit="handleContactSubmit(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="c_name">Your Name *</label>
                                    <input type="text" id="c_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="c_email">Your Email *</label>
                                    <input type="email" id="c_email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="c_subject">Subject</label>
                                <input type="text" id="c_subject">
                            </div>
                            <div class="form-group">
                                <label for="c_message">Your Message *</label>
                                <textarea id="c_message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                        <div id="contactFeedback"></div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- ============================================= -->
    <!-- VIEW: FLEET                                    -->
    <!-- ============================================= -->
    <section class="view" id="view-fleet" data-view="fleet">
        <section class="page-header">
            <div class="container">
                <nav class="breadcrumb">
                    <a href="#home" data-nav="home">Home</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Our Fleet</span>
                </nav>
                <h1>Our Fleet</h1>
                <p>Choose from over 100 vehicles across 10 premium brands</p>
            </div>
        </section>

        <section class="brand-filter">
            <div class="container">
                <h2 class="section-title">Filter by Brand</h2>
                <p class="section-subtitle">Click a brand to narrow down the list below</p>
                <div class="brand-grid">
                    <?php foreach ($brands as $brand): 
                        $logo = $brand_logos[$brand] ?? null;
                        $icon = $brand_icons[$brand] ?? 'fa-car';
                    ?>
                    <a href="#" 
                       class="brand-card" 
                       data-brand="<?php echo htmlspecialchars($brand); ?>" 
                       onclick="event.preventDefault(); filterByBrand('<?php echo htmlspecialchars($brand); ?>', this); scrollToFleet();">
                        <?php if ($logo): ?>
                            <img src="<?php echo $logo; ?>" 
                                 alt="<?php echo htmlspecialchars($brand); ?> logo" 
                                 loading="lazy"
                                 referrerpolicy="no-referrer"
                                 onerror="this.outerHTML='<div class=\'brand-icon-fallback\'><i class=\'fas <?php echo $icon; ?>\'></i></div>';">
                        <?php else: ?>
                            <div class="brand-icon-fallback"><i class="fas <?php echo $icon; ?>"></i></div>
                        <?php endif; ?>
                        <span><?php echo htmlspecialchars($brand); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="car-listings" id="fleet">
            <div class="container">
                <div class="listing-header" id="listingHeader">
                    <h2 id="listingTitle">All Vehicles <span id="carCount">(<?php echo count($cars); ?> models)</span></h2>
                    <button class="clear-filter" onclick="clearFilter()">
                        <i class="fas fa-times"></i> Clear Filter
                    </button>
                </div>
                <div class="car-grid" id="carGrid">
                    <?php foreach ($cars as $car): ?>
                    <div class="car-card" data-brand="<?php echo htmlspecialchars($car['brand']); ?>">
                        <div class="car-image">
                            <img src="<?php echo htmlspecialchars($car['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" 
                                 loading="lazy"
                                 referrerpolicy="no-referrer"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/800x500/1a3a5c/ffffff?text=<?php echo urlencode($car['brand'] . ' ' . $car['model']); ?>';">
                            <span class="car-badge"><?php echo htmlspecialchars($car['brand']); ?></span>
                            <span class="availability available">Available</span>
                        </div>
                        <div class="car-info">
                            <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                            <div class="car-specs">
                                <span><i class="fas fa-calendar"></i> <?php echo $car['year']; ?></span>
                                <span><i class="fas fa-chair"></i> <?php echo $car['seats']; ?> Seats</span>
                                <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                                <span><i class="fas fa-palette"></i> <?php echo htmlspecialchars($car['color']); ?></span>
                            </div>
                            <div class="car-price">
                                <?php echo formatCurrency($car['price_per_day']); ?> <small>/ day</small>
                            </div>
                            <button class="btn-hire" onclick='openBookingModal(<?php echo json_encode($car, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                <i class="fas fa-key"></i> Hire Now
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </section>

</main>

<!-- BOOKING MODAL -->
<div class="modal" id="bookingModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeBookingModal()">&times;</span>
        <h2><i class="fas fa-file-signature"></i> Complete Your Booking</h2>
        <form id="bookingForm" action="hire_car.php" method="POST">
            <input type="hidden" id="car_id" name="car_id">
            <input type="hidden" id="price_per_day" name="price_per_day">

            <div class="form-section">
                <h3><i class="fas fa-car"></i> Car Details</h3>
                <div class="car-summary" id="carSummary"></div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-user"></i> Personal Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="full_name">Full Name *</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="id_number">ID / Passport Number *</label>
                        <input type="text" id="id_number" name="id_number" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="driver_license">Driver's License Number *</label>
                    <input type="text" id="driver_license" name="driver_license" required>
                </div>
                <div class="form-group">
                    <label for="address">Physical Address</label>
                    <textarea id="address" name="address" rows="2"></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3><i class="fas fa-calendar-alt"></i> Rental Period</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="hire_date">Hire Date *</label>
                        <input type="date" id="hire_date" name="hire_date" required min="<?php echo date('Y-m-d'); ?>" onchange="calculateTotal()">
                    </div>
                    <div class="form-group">
                        <label for="return_date">Return Date *</label>
                        <input type="date" id="return_date" name="return_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" onchange="calculateTotal()">
                    </div>
                </div>
            </div>

            <div class="booking-total">
                <span><i class="fas fa-money-bill-wave"></i> Total Amount:</span>
                <span id="totalAmount">$0.00</span>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check-circle"></i> Confirm Booking
            </button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>