/* =====================================================================
   DriveElite — script.js
   =====================================================================
   SPA router ONLY runs when index.php's viewContainer is present.
   On every other page, links behave as plain HTML links.
   ===================================================================== */

// 1. Detect SPA context ONCE at the top.
var IS_SPA_PAGE = false;
document.addEventListener('DOMContentLoaded', function () {
    IS_SPA_PAGE = !!document.getElementById('viewContainer');
    initSPA();
});

/* ===================== SLIDESHOW ===================== */
let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const dotsContainer = document.querySelector('.slide-dots');
let autoSlideInterval;

if (slides.length > 0 && dotsContainer) {
    dotsContainer.innerHTML = '';
    slides.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.onclick = () => goToSlide(i);
        dotsContainer.appendChild(dot);
    });
}

const dots = document.querySelectorAll('.dot');

function showSlide(index) {
    if (slides.length === 0) return;
    if (index >= slides.length) currentSlide = 0;
    else if (index < 0) currentSlide = slides.length - 1;
    else currentSlide = index;

    slides.forEach((s, i) => s.classList.toggle('active', i === currentSlide));
    dots.forEach((d, i) => d.classList.toggle('active', i === currentSlide));
}

function changeSlide(direction) {
    showSlide(currentSlide + direction);
    resetAutoSlide();
}

function goToSlide(index) {
    showSlide(index);
    resetAutoSlide();
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    if (slides.length > 0) {
        autoSlideInterval = setInterval(() => showSlide(currentSlide + 1), 4000);
    }
}
resetAutoSlide();

/* ===================== NAVBAR SHADOW ===================== */
window.addEventListener('scroll', function () {
    var nav = document.getElementById('mainNavbar');
    if (!nav) return;
    nav.classList.toggle('scrolled', window.scrollY > 10);
});

/* ===================== MOBILE MENU ===================== */
function toggleMenu() {
    var menu = document.getElementById('navMenu');
    if (menu) menu.classList.toggle('open');
}
window.toggleMenu = toggleMenu;

/* =====================================================================
   SPA ROUTER — runs ONLY on index.php
   ===================================================================== */
function initSPA() {
    if (!IS_SPA_PAGE) return; // ← Guard: no SPA behavior on other pages

    var viewDirections = {
        home:    'left',
        about:   'top',
        contact: 'right',
        fleet:   'bottom'
    };

    var currentView = 'home';
    var isTransitioning = false;

    function getBasePath() {
        var path = window.location.pathname;
        var lastSegment = path.split('/').pop();
        if (lastSegment.indexOf('.') !== -1) {
            path = path.substring(0, path.lastIndexOf('/') + 1);
        }
        if (['about', 'contact', 'fleet', 'home'].indexOf(lastSegment) !== -1) {
            path = path.substring(0, path.lastIndexOf('/') + 1);
        }
        if (path.charAt(path.length - 1) !== '/') path += '/';
        return path;
    }

    function getViewFromURL() {
        var base = getBasePath();
        var tail = window.location.pathname.substring(base.length);
        tail = tail.replace(/\/$/, '');
        if (!tail) return 'home';
        var segment = tail.split('/').pop();
        if (segment === 'index.php' || segment === 'index' || segment === '') return 'home';
        if (['home', 'about', 'contact', 'fleet'].indexOf(segment) !== -1) return segment;
        return 'home';
    }

    function goToView(targetView) {
        if (isTransitioning) return;

        var currentEl = document.getElementById('view-' + currentView);
        var targetEl  = document.getElementById('view-' + targetView);
        if (!currentEl || !targetEl) return;

        if (targetView === currentView) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        isTransitioning = true;
        window.scrollTo({ top: 0, behavior: 'auto' });

        var enterDir = viewDirections[targetView] || 'right';
        targetEl.setAttribute('data-enter-dir', enterDir);
        targetEl.classList.add('view-enter');
        currentEl.classList.add('view-exit');

        void targetEl.offsetWidth;

        requestAnimationFrame(function () {
            currentEl.classList.add('view-exit-active');
            targetEl.classList.add('view-enter-active');
        });

        setTimeout(function () {
            currentEl.classList.remove('active', 'view-exit', 'view-exit-active');
            targetEl.classList.remove('view-enter', 'view-enter-active');
            targetEl.removeAttribute('data-enter-dir');
            targetEl.classList.add('active');

            currentView = targetView;

            document.querySelectorAll('.nav-menu a[data-nav]').forEach(function (a) {
                a.classList.toggle('active', a.getAttribute('data-nav') === targetView);
            });

            var basePath = getBasePath();
            var newPath = targetView === 'home' ? basePath : basePath + targetView;
            history.pushState({ view: targetView }, '', newPath);

            setTimeout(function () { isTransitioning = false; }, 60);
        }, 720);
    }

    window.goToView = goToView;

    // Wire the SPA nav links
    document.querySelectorAll('a[data-nav]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = this.getAttribute('data-nav');
            if (!target) return;

            // Only intercept if the target view actually exists here
            var targetEl = document.getElementById('view-' + target);
            if (!targetEl) return; // ← falls through to normal navigation

            e.preventDefault();
            goToView(target);

            var menu = document.getElementById('navMenu');
            if (menu) menu.classList.remove('open');
        });
    });

    // Handle back/forward
    window.addEventListener('popstate', function () {
        var targetView = getViewFromURL();
        if (targetView !== currentView) {
            goToView(targetView);
        }
    });

    // On page load, respect the URL
    var targetView = getViewFromURL();
    if (targetView !== 'home') {
        goToView(targetView);
    }
}

/* ===================== SMOOTH SCROLL TO FLEET ===================== */
function scrollToFleet() {
    var fleet = document.getElementById('fleet');
    if (!fleet) return;

    setTimeout(function () {
        var navHeight = (document.querySelector('.navbar') || {}).offsetHeight || 80;
        var topOffset = fleet.getBoundingClientRect().top + window.pageYOffset - navHeight - 20;

        window.scrollTo({
            top: topOffset,
            behavior: 'smooth'
        });

        var header = document.getElementById('listingHeader');
        if (header) {
            header.classList.remove('highlight');
            void header.offsetWidth;
            header.classList.add('highlight');
            setTimeout(function () { header.classList.remove('highlight'); }, 1300);
        }
    }, 200);
}
window.scrollToFleet = scrollToFleet;

/* ===================== BRAND FILTER ===================== */
var activeBrand = null;

function filterByBrand(brand, el) {
    if (activeBrand === brand) {
        clearFilter();
        return;
    }
    activeBrand = brand;

    document.querySelectorAll('.brand-card').forEach(function (c) { c.classList.remove('active'); });
    if (el) el.classList.add('active');

    fetch('get_cars.php?brand=' + encodeURIComponent(brand))
        .then(function (res) { return res.json(); })
        .then(function (cars) { renderCars(cars, brand); })
        .catch(function (err) { console.error('Fetch error:', err); });
}
window.filterByBrand = filterByBrand;

function clearFilter() {
    activeBrand = null;
    document.querySelectorAll('.brand-card').forEach(function (c) { c.classList.remove('active'); });

    fetch('get_cars.php')
        .then(function (res) { return res.json(); })
        .then(function (cars) { renderCars(cars, null); })
        .catch(function (err) { console.error('Fetch error:', err); });
}
window.clearFilter = clearFilter;

function renderCars(cars, brand) {
    var grid = document.getElementById('carGrid');
    var title = document.getElementById('listingTitle');
    if (!grid || !title) return;

    title.innerHTML = (brand ? brand + ' Vehicles' : 'All Vehicles') +
                      ' <span id="carCount">(' + cars.length + ' models)</span>';

    if (cars.length === 0) {
        grid.innerHTML = '<div class="no-results"><i class="fas fa-car-crash" style="font-size:2rem;display:block;margin-bottom:0.8rem;"></i>No cars available for this brand.</div>';
        return;
    }

    grid.innerHTML = cars.map(function (car) {
        return ''
            + '<div class="car-card" data-brand="' + escapeHtml(car.brand) + '">'
            +   '<div class="car-image">'
            +     '<img src="' + escapeHtml(car.image_url) + '" '
            +          'alt="' + escapeHtml(car.brand + ' ' + car.model) + '" '
            +          'loading="lazy" referrerpolicy="no-referrer" '
            +          'onerror="this.onerror=null; this.src=\'https://via.placeholder.com/800x500/1a3a5c/ffffff?text=' + encodeURIComponent(car.brand + ' ' + car.model) + '\';">'
            +     '<span class="car-badge">' + escapeHtml(car.brand) + '</span>'
            +     '<span class="availability available">Available</span>'
            +   '</div>'
            +   '<div class="car-info">'
            +     '<h3>' + escapeHtml(car.brand + ' ' + car.model) + '</h3>'
            +     '<div class="car-specs">'
            +       '<span><i class="fas fa-calendar"></i> ' + car.year + '</span>'
            +       '<span><i class="fas fa-chair"></i> ' + car.seats + ' Seats</span>'
            +       '<span><i class="fas fa-gas-pump"></i> ' + escapeHtml(car.fuel_type) + '</span>'
            +       '<span><i class="fas fa-cog"></i> ' + escapeHtml(car.transmission) + '</span>'
            +       '<span><i class="fas fa-palette"></i> ' + escapeHtml(car.color) + '</span>'
            +     '</div>'
            +     '<div class="car-price">$' + parseFloat(car.price_per_day).toFixed(2) + ' <small>/ day</small></div>'
            +     '<button class="btn-hire" onclick=\'openBookingModal(' + JSON.stringify(car).replace(/'/g, "&#39;") + ')\'>'
            +       '<i class="fas fa-key"></i> Hire Now'
            +     '</button>'
            +   '</div>'
            + '</div>';
    }).join('');
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/* ===================== CONTACT FORM (AJAX) ===================== */
function handleContactSubmit(e) {
    e.preventDefault();
    var name = document.getElementById('c_name').value;
    var feedback = document.getElementById('contactFeedback');
    feedback.innerHTML = '<div class="alert success" style="margin-top:1rem;"><i class="fas fa-check-circle"></i> Thank you, ' + name + '! Your message has been received.</div>';
    document.getElementById('contactForm').reset();
    setTimeout(function () { feedback.innerHTML = ''; }, 5000);
}
window.handleContactSubmit = handleContactSubmit;

/* ===================== BOOKING MODAL ===================== */
var currentPricePerDay = 0;

function openBookingModal(car) {
    var modal = document.getElementById('bookingModal');
    if (!modal) return;

    modal.classList.add('active');
    document.getElementById('car_id').value = car.id;
    document.getElementById('price_per_day').value = car.price_per_day;
    currentPricePerDay = parseFloat(car.price_per_day);

    document.getElementById('carSummary').innerHTML = ''
        + '<div>'
        +   '<div class="car-name">' + escapeHtml(car.brand + ' ' + car.model) + ' (' + car.year + ')</div>'
        +   '<div class="car-plate">Plate: ' + escapeHtml(car.plate_number) + ' &bull; Color: ' + escapeHtml(car.color) + '</div>'
        + '</div>'
        + '<div class="car-price">$' + currentPricePerDay.toFixed(2) + '/day</div>';

    document.body.style.overflow = 'hidden';
}
window.openBookingModal = openBookingModal;

function closeBookingModal() {
    var modal = document.getElementById('bookingModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.getElementById('bookingForm').reset();
    document.getElementById('totalAmount').textContent = '$0.00';
    document.body.style.overflow = '';
}
window.closeBookingModal = closeBookingModal;

function calculateTotal() {
    var hireDate = document.getElementById('hire_date').value;
    var returnDate = document.getElementById('return_date').value;

    if (hireDate && returnDate && currentPricePerDay) {
        var start = new Date(hireDate);
        var end = new Date(returnDate);
        var days = Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
        var total = days * currentPricePerDay;
        document.getElementById('totalAmount').textContent = '$' + total.toFixed(2);
    }
}
window.calculateTotal = calculateTotal;

var bookingModal = document.getElementById('bookingModal');
if (bookingModal) {
    bookingModal.addEventListener('click', function (e) {
        if (e.target === this) closeBookingModal();
    });
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && bookingModal && bookingModal.classList.contains('active')) {
        closeBookingModal();
    }
});

var hireInput = document.getElementById('hire_date');
if (hireInput) {
    hireInput.addEventListener('change', function () {
        var returnInput = document.getElementById('return_date');
        if (returnInput) {
            var nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            returnInput.min = nextDay.toISOString().split('T')[0];
        }
    });
}