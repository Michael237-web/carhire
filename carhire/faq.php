<?php
require_once 'config.php';
$page_title  = 'FAQ | DriveElite';
$active_page = 'faq';
include 'header.php';

$faqs = [
    ['q' => 'What documents do I need to hire a car?', 'a' => 'You need a valid driver\'s license (held for at least 2 years), a national ID or passport, and a credit/debit card for the security deposit.'],
    ['q' => 'What is the minimum age to hire a car?', 'a' => 'The minimum age is 23 years. Drivers aged 23-24 may attract a young driver surcharge of $20/day.'],
    ['q' => 'Is insurance included in the rental price?', 'a' => 'Yes, all our vehicles come with comprehensive insurance including third-party liability, theft protection, and 24/7 roadside assistance. However, an insurance excess applies in case of damage.'],
    ['q' => 'Can I take the car outside Nairobi?', 'a' => 'Yes, you can drive anywhere within Kenya. For cross-border travel (e.g., Tanzania, Uganda), prior written approval is required.'],
    ['q' => 'What is your fuel policy?', 'a' => 'Vehicles are provided with a full tank and must be returned full. If not, a refueling charge plus a service fee will apply.'],
    ['q' => 'Do you offer airport pickup?', 'a' => 'Yes! We offer free pickup and drop-off at JKIA (Jomo Kenyatta International Airport) and Wilson Airport. Just mention it when booking.'],
    ['q' => 'Can I cancel my booking?', 'a' => 'Yes. Free cancellation up to 24 hours before pickup. Between 24-6 hours, 50% refund. Less than 6 hours, no refund.'],
    ['q' => 'What happens if the car breaks down?', 'a' => 'Call our 24/7 support immediately. We will arrange a replacement vehicle or roadside assistance at no extra cost.'],
    ['q' => 'Do you offer long-term rentals?', 'a' => 'Yes, we offer weekly and monthly rates with significant discounts. Contact us for a custom quote.'],
    ['q' => 'Is there a mileage limit?', 'a' => 'No, all rentals within Kenya include unlimited mileage.'],
];
?>

<!-- ============ FAQ PAGE STYLES (SELF-CONTAINED) ============ -->
<style>
    /* Page header fix — ensure it's not clipped */
    .faq-page-header {
        background: linear-gradient(135deg, #0f2438 0%, #2c5282 100%);
        color: #fff;
        padding: 4rem 0 3.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .faq-page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 560px;
        height: 560px;
        background: radial-gradient(circle, rgba(59,130,246,.28), transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .faq-page-header .breadcrumb {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
        padding: 0.4rem 1rem;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 500;
        position: relative;
    }
    .faq-page-header .breadcrumb a { color: rgba(255,255,255,.75); }
    .faq-page-header .breadcrumb a:hover { color: #fbbf24; }
    .faq-page-header .breadcrumb i { font-size: 0.6rem; color: rgba(255,255,255,.45); }
    .faq-page-header .breadcrumb span { color: #fff; font-weight: 600; }
    .faq-page-header h1 {
        font-size: clamp(1.8rem, 4vw, 3rem);
        font-weight: 800;
        margin: 0 0 0.75rem 0;
        position: relative;
        line-height: 1.15;
    }
    .faq-page-header p {
        font-size: clamp(0.95rem, 1.4vw, 1.15rem);
        opacity: 0.92;
        max-width: 640px;
        margin: 0 auto;
        position: relative;
    }

    /* ============ FAQ SECTION ============ */
    .faq-page-section {
        padding: 4rem 0 5rem;
        background: #f9fafb;
    }

    /* Header block */
    .faq-page-intro {
        text-align: center;
        max-width: 720px;
        margin: 0 auto 3rem;
    }
    .faq-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eff6ff;
        color: #2563eb;
        padding: 0.45rem 1.1rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 1rem;
        border: 1px solid #dbeafe;
    }
    .faq-page-intro h2 {
        font-size: clamp(1.6rem, 3vw, 2.3rem);
        font-weight: 800;
        color: #0f2438;
        margin: 0 0 0.75rem 0;
        line-height: 1.2;
    }
    .faq-page-intro p {
        color: #6b7280;
        font-size: 1rem;
        margin: 0;
    }

    /* FAQ list container */
    .faq-list {
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* Individual card */
    .faq-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(.4, 0, .2, 1);
        box-shadow: 0 2px 8px rgba(15, 36, 56, 0.05);
    }
    .faq-card:hover {
        border-color: #bfdbfe;
        box-shadow: 0 8px 24px rgba(15, 36, 56, 0.08);
    }
    .faq-card.open {
        border-color: #3b82f6;
        box-shadow: 0 12px 28px rgba(59, 130, 246, 0.12);
    }

    /* Question button */
    .faq-card button.faq-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 1.35rem 1.6rem;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
        gap: 1rem;
        transition: background 0.2s ease;
    }
    .faq-card button.faq-trigger:hover {
        background: #f9fafb;
    }
    .faq-card.open button.faq-trigger {
        background: linear-gradient(135deg, #eff6ff 0%, rgba(219, 234, 254, 0.4) 100%);
    }

    .faq-card .faq-question-text {
        font-size: 1.02rem;
        font-weight: 700;
        color: #0f2438;
        line-height: 1.4;
        flex: 1;
        margin: 0;
    }
    .faq-card.open .faq-question-text {
        color: #2563eb;
    }

    /* Plus / minus icon circle */
    .faq-card .faq-toggle-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        color: #4b5563;
        border-radius: 50%;
        font-size: 0.9rem;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .faq-card button.faq-trigger:hover .faq-toggle-icon {
        background: #3b82f6;
        color: #ffffff;
    }
    .faq-card.open .faq-toggle-icon {
        background: #3b82f6;
        color: #ffffff;
        transform: rotate(180deg);
    }

    /* Answer panel */
    .faq-card .faq-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    padding 0.3s ease;
        padding: 0 1.6rem;
    }
    .faq-card.open .faq-panel {
        max-height: 500px;
        padding: 0 1.6rem 1.5rem;
    }
    .faq-card .faq-panel p {
        color: #4b5563;
        font-size: 0.97rem;
        line-height: 1.75;
        margin: 0;
        padding-top: 1rem;
        border-top: 1px dashed #e5e7eb;
    }

    /* ============ CTA CARD ============ */
    .faq-page-cta {
        max-width: 860px;
        margin: 3rem auto 0;
        padding: 2.25rem 2.5rem;
        background: linear-gradient(135deg, #0f2438 0%, #2c5282 100%);
        border-radius: 24px;
        display: flex;
        align-items: center;
        gap: 1.75rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 45px -12px rgba(15, 36, 56, 0.35);
        flex-wrap: wrap;
    }
    .faq-page-cta::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 380px;
        height: 380px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.35), transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .faq-cta-icon {
        width: 64px;
        height: 64px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        font-size: 1.6rem;
        color: #fbbf24;
        position: relative;
        z-index: 1;
    }
    .faq-cta-text {
        flex: 1;
        min-width: 220px;
        position: relative;
        z-index: 1;
    }
    .faq-cta-text h3 {
        font-size: 1.4rem;
        font-weight: 800;
        margin: 0 0 0.3rem 0;
        color: #ffffff;
    }
    .faq-cta-text p {
        font-size: 0.95rem;
        opacity: 0.85;
        margin: 0;
        color: #ffffff;
    }
    .faq-cta-actions {
        display: flex;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }
    .faq-cta-actions a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.25s ease;
        white-space: nowrap;
        cursor: pointer;
    }
    .faq-cta-actions a.faq-btn-solid {
        background: #ffffff;
        color: #0f2438;
        border: 2px solid #ffffff;
    }
    .faq-cta-actions a.faq-btn-solid:hover {
        background: #fbbf24;
        border-color: #fbbf24;
        transform: translateY(-2px);
    }
    .faq-cta-actions a.faq-btn-outline {
        background: transparent;
        color: #ffffff;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    .faq-cta-actions a.faq-btn-outline:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #ffffff;
        transform: translateY(-2px);
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 768px) {
        .faq-page-section { padding: 3rem 0 4rem; }
        .faq-card button.faq-trigger { padding: 1.1rem 1.2rem; }
        .faq-card .faq-question-text { font-size: 0.95rem; }
        .faq-card .faq-toggle-icon { width: 32px; height: 32px; font-size: 0.8rem; }
        .faq-card .faq-panel { padding: 0 1.2rem; }
        .faq-card.open .faq-panel { padding: 0 1.2rem 1.25rem; }
        .faq-page-cta {
            padding: 1.75rem 1.5rem;
            flex-direction: column;
            text-align: center;
        }
        .faq-cta-icon { width: 56px; height: 56px; font-size: 1.4rem; }
        .faq-cta-text h3 { font-size: 1.2rem; }
        .faq-cta-actions { justify-content: center; width: 100%; }
        .faq-cta-actions a { flex: 1; justify-content: center; min-width: 140px; }
    }
    @media (max-width: 480px) {
        .faq-card button.faq-trigger { padding: 1rem; gap: 0.75rem; }
        .faq-card .faq-question-text { font-size: 0.9rem; }
        .faq-card .faq-panel { padding: 0 1rem; }
        .faq-card.open .faq-panel { padding: 0 1rem 1rem; }
        .faq-card .faq-panel p { font-size: 0.88rem; }
        .faq-page-cta { padding: 1.5rem 1.25rem; }
        .faq-cta-actions { flex-direction: column; }
        .faq-cta-actions a { width: 100%; }
    }
</style>

<!-- ============ PAGE HEADER ============ -->
<section class="faq-page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>FAQ</span>
        </nav>
        <h1>Frequently Asked Questions</h1>
        <p>Everything you need to know about hiring with DriveElite</p>
    </div>
</section>

<!-- ============ FAQ SECTION ============ -->
<section class="faq-page-section">
    <div class="container">

        <div class="faq-page-intro">
            <span class="faq-badge"><i class="fas fa-question-circle"></i> Help Center</span>
            <h2>Got Questions? We've Got Answers.</h2>
            <p>Find quick answers to the most common questions about our car hire service.</p>
        </div>

        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): ?>
                <div class="faq-card <?php echo $i === 0 ? 'open' : ''; ?>">
                    <button type="button" class="faq-trigger" onclick="toggleFaqCard(this)">
                        <span class="faq-question-text"><?php echo htmlspecialchars($faq['q']); ?></span>
                        <span class="faq-toggle-icon">
                            <i class="fas <?php echo $i === 0 ? 'fa-minus' : 'fa-plus'; ?>"></i>
                        </span>
                    </button>
                    <div class="faq-panel">
                        <p><?php echo htmlspecialchars($faq['a']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div class="faq-page-cta">
            <div class="faq-cta-icon"><i class="fas fa-headset"></i></div>
            <div class="faq-cta-text">
                <h3>Still have questions?</h3>
                <p>Our team is available 24/7 to help you plan the perfect rental.</p>
            </div>
            <div class="faq-cta-actions">
                <a href="contact.php" class="faq-btn-solid"><i class="fas fa-envelope"></i> Contact Us</a>
                <a href="tel:+254746674121" class="faq-btn-outline"><i class="fas fa-phone"></i> Call Now</a>
            </div>
        </div>

    </div>
</section>

<script>
function toggleFaqCard(btn) {
    const card = btn.parentElement;
    const isOpen = card.classList.contains('open');
    const icon = card.querySelector('.faq-toggle-icon i');

    // Close all
    document.querySelectorAll('.faq-card').forEach(c => {
        c.classList.remove('open');
        const i = c.querySelector('.faq-toggle-icon i');
        if (i) i.className = 'fas fa-plus';
    });

    // Open clicked one (if it was closed)
    if (!isOpen) {
        card.classList.add('open');
        if (icon) icon.className = 'fas fa-minus';
    }
}
</script>

<?php include 'footer.php'; ?>