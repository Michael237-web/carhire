<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'car_hire_db');

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

// Helper to get base URL (for AJAX and assets)
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    $script = str_replace('\\', '/', $script);
    if ($script === '/') $script = '';
    return $protocol . "://" . $host . $script . '/' . ltrim($path, '/');
}
?>