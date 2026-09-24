<?php
require_once 'config.php';

$code = intval($_GET['code'] ?? 404);

$errors = [
    400 => ['title' => 'Bad Request',      'message' => 'The server could not understand your request.'],
    401 => ['title' => 'Unauthorized',     'message' => 'You need to log in to access this page.'],
    403 => ['title' => 'Forbidden',        'message' => 'You do not have permission to access this resource.'],
    404 => ['title' => 'Page Not Found',   'message' => 'The page you are looking for does not exist or has been moved.'],
    500 => ['title' => 'Server Error',     'message' => 'Something went wrong on our end. We are working on it.'],
    503 => ['title' => 'Service Unavailable', 'message' => 'The service is temporarily unavailable. Please try again shortly.'],
];

$error = $errors[$code] ?? $errors[404];

http_response_code($code);

$page_title  = $code . ' — ' . $error['title'] . ' | DriveElite';
$active_page = '';
include 'header.php';
?>
<section class="page-header" style="min-height:60vh; display:flex; align-items:center;">
    <div class="container" style="text-align:center;">
        <div style="font-size:6rem; font-weight:800; color:#fbbf24; line-height:1; margin-bottom:1rem;">
            <?php echo $code; ?>
        </div>
        <h1 style="font-size:2.5rem; margin-bottom:1rem;"><?php echo htmlspecialchars($error['title']); ?></h1>
        <p style="font-size:1.1rem; opacity:.9; max-width:600px; margin:0 auto 2rem;">
            <?php echo htmlspecialchars($error['message']); ?>
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="index.php" class="btn-primary"><i class="fas fa-home"></i> Back to Home</a>
            <a href="index.php#fleet" class="btn-primary" style="background:transparent; color:#fff; border-color:#fff;">
                <i class="fas fa-car"></i> View Fleet
            </a>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>