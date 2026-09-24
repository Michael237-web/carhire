<?php
// ===================== HEADER.PHP =====================
$active_page = !empty($active_page) ? $active_page : 'home';
$page_title  = !empty($page_title)  ? $page_title  : 'DriveElite | Premium Car Hire';

$brand_logos = [
    'Toyota'     => 'https://cdn.simpleicons.org/toyota/1a3a5c',
    'Honda'      => 'https://cdn.simpleicons.org/honda/1a3a5c',
    'BMW'        => 'https://cdn.simpleicons.org/bmw/1a3a5c',
    'Mercedes'   => 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Mercedes-Benz_free_logo.svg',
    'Nissan'     => 'https://cdn.simpleicons.org/nissan/1a3a5c',
    'Hyundai'    => 'https://cdn.simpleicons.org/hyundai/1a3a5c',
    'Kia'        => 'https://cdn.simpleicons.org/kia/1a3a5c',
    'Mazda'      => 'https://cdn.simpleicons.org/mazda/1a3a5c',
    'Audi'       => 'https://cdn.simpleicons.org/audi/1a3a5c',
    'Volkswagen' => 'https://cdn.simpleicons.org/volkswagen/1a3a5c',
];

$brand_icons = [
    'Toyota'     => 'fa-car',
    'Honda'      => 'fa-car-side',
    'BMW'        => 'fa-car',
    'Mercedes'   => 'fa-car-side',
    'Nissan'     => 'fa-truck-pickup',
    'Hyundai'    => 'fa-car',
    'Kia'        => 'fa-car-side',
    'Mazda'      => 'fa-car',
    'Audi'       => 'fa-car-side',
    'Volkswagen' => 'fa-car',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?></title>
<meta name="description" content="DriveElite — premium car hire services. Book luxury sedans, SUVs and executive vehicles at unbeatable daily rates.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="icon" type="images/png" href="businesslogo.png">
</head>
<body>

<!-- TOP INFO BAR -->
<div class="topbar">
    <div class="topbar-container">
        <div class="topbar-left">
            <a href="tel:+254746674121"><i class="fas fa-phone-alt"></i> +254 746674121</a>
            <a href="mailto:info@michael.com"><i class="fas fa-envelope"></i> info@michael.com</a>
            <span class="topbar-item"><i class="fas fa-clock"></i> Mon–Sun: 8AM – 8PM</span>
        </div>
        <div class="topbar-right">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar" id="mainNavbar">
    <div class="nav-container">
     <a href="/carhire/" class="logo" data-nav="home">
    <img src="businesslogo.png" alt="DriveElite" class="logo-img">
</a>
        <ul class="nav-menu" id="navMenu">
            <li><a href="/carhire/"        data-nav="home"    class="<?php echo $active_page === 'home'    ? 'active' : ''; ?>">Home</a></li>
            <li><a href="/carhire/about"   data-nav="about"   class="<?php echo $active_page === 'about'   ? 'active' : ''; ?>">About</a></li>
            <li><a href="/carhire/faq"     data-nav="faq"    class="<?php echo $active_page === 'faq'    ? 'active' : ''; ?>">FAQ</a></li>
            <li><a href="/carhire/contact" data-nav="contact" class="<?php echo $active_page === 'contact' ? 'active' : ''; ?>">Contact</a></li>
            <li><a href="/carhire/fleet"   data-nav="fleet"   class="nav-cta">
                <span class="nav-cta-inner">
                    <i class="fas fa-key"></i>
                    <span>Book Now</span>
                </span>
            </a></li>
        </ul>
        <button class="hamburger" onclick="toggleMenu()" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>