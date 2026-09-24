<?php
require_once 'config.php';
$page_title  = 'Terms & Conditions | DriveElite';
$active_page = '';
include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Terms & Conditions</span>
        </nav>
        <h1>Terms & Conditions</h1>
        <p>Last updated: <?php echo date('F Y'); ?></p>
    </div>
</section>

<section class="legal-section">
    <div class="container">
        <div class="legal-content">

            <h2>1. Rental Agreement</h2>
            <p>By hiring a vehicle from DriveElite, you agree to comply with all terms outlined in this agreement. The primary driver must be at least 23 years of age and hold a valid driver's license for a minimum of 2 years.</p>

            <h2>2. Driver Requirements</h2>
            <ul>
                <li>Valid driver's license (local or international).</li>
                <li>National ID or Passport.</li>
                <li>Minimum age: 23 years.</li>
                <li>Drivers under 25 may attract a young driver surcharge.</li>
            </ul>

            <h2>3. Payment & Deposit</h2>
            <p>A refundable security deposit is required at the time of pickup. The deposit amount depends on the vehicle category. Payment can be made via M-Pesa, bank transfer, or credit card.</p>

            <h2>4. Fuel Policy</h2>
            <p>Vehicles are provided with a full tank and must be returned with a full tank. Failure to do so will result in a refueling charge plus a service fee.</p>

            <h2>5. Mileage</h2>
            <p>All rentals include unlimited mileage within Kenya. Cross-border travel requires prior written approval and may attract additional fees.</p>

            <h2>6. Insurance & Damage</h2>
            <p>All vehicles are comprehensively insured. However, the hirer is liable for the insurance excess in case of damage, theft, or loss. The excess amount varies by vehicle.</p>

            <h2>7. Cancellation Policy</h2>
            <ul>
                <li><strong>Free cancellation</strong> up to 24 hours before pickup.</li>
                <li>50% refund for cancellations between 24 and 6 hours before pickup.</li>
                <li>No refund for cancellations within 6 hours of pickup or no-shows.</li>
            </ul>

            <h2>8. Prohibited Uses</h2>
            <ul>
                <li>Driving under the influence of alcohol or drugs.</li>
                <li>Using the vehicle for racing, off-road driving (unless 4x4), or any illegal activity.</li>
                <li>Allowing an unauthorized person to drive the vehicle.</li>
                <li>Towing or carrying passengers for hire.</li>
            </ul>

            <h2>9. Late Returns</h2>
            <p>Late returns will be charged at an hourly rate. Returns more than 3 hours late will be charged as a full day's hire.</p>

            <h2>10. Breakdown & Accidents</h2>
            <p>In case of breakdown or accident, contact our 24/7 support immediately. Do not attempt repairs without authorization. All accidents must be reported to the police and to DriveElite within 24 hours.</p>

            <h2>11. Governing Law</h2>
            <p>This agreement is governed by the laws of Kenya. Any disputes shall be resolved in the courts of Nairobi.</p>

            <h2>12. Contact</h2>
            <p>For questions about these terms, contact us at <a href="mailto:info@michael.com">info@michael.com</a> or call +254 746 674 121.</p>

        </div>
    </div>
</section>

<?php include 'footer.php'; ?>