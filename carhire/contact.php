<?php
require_once 'config.php';

$is_overlay = isset($_GET['overlay']) && $_GET['overlay'] == '1';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $success = 'Thank you, ' . htmlspecialchars($name) . '! Your message has been received. We will get back to you shortly.';
    } else {
        $error = 'Please fill in all required fields.';
    }
}

$page_title  = 'Contact Us | DriveElite';
$active_page = 'contact';

if (!$is_overlay) {
    include 'header.php';
} else {
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
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Reach out anytime.</p>
    </div>
</section>

<!-- CONTACT SECTION -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">

            <!-- INFO -->
            <div class="contact-info">
                <h3>Contact Information</h3>

                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>Visit Us</h4>
                        <p>123 Kenyatta Avenue<br>Nairobi, Kenya</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Call Us</h4>
                        <p>+254 746674121</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email Us</h4>
                        <p>info@michael.com</p>
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Working Hours</h4>
                        <p>Mon - Fri: 8:00 AM - 8:00 PM<br>Sat - Sun: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="contact-form">
                <h3>Send Us a Message</h3>

                <?php if ($success): ?>
                    <div class="alert success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php<?php echo $is_overlay ? '?overlay=1' : ''; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject">
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
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