<?php
// ===================== DEBUG (remove after it works) =====================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';

// ===================== AUTH CHECK =====================
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// ===================== FETCH STATS =====================
$conn = getDBConnection();

$stats = [
    'total_cars'       => (int) $conn->query("SELECT COUNT(*) FROM car_listings")->fetch_row()[0],
    'available_cars'   => (int) $conn->query("SELECT COUNT(*) FROM car_listings WHERE status='available'")->fetch_row()[0],
    'total_bookings'   => (int) $conn->query("SELECT COUNT(*) FROM car_bookings")->fetch_row()[0],
    'pending_bookings' => (int) $conn->query("SELECT COUNT(*) FROM car_bookings WHERE booking_status='active'")->fetch_row()[0],
    'total_revenue'    => (float) $conn->query("SELECT COALESCE(SUM(total_amount),0) FROM car_bookings WHERE booking_status IN ('active','completed')")->fetch_row()[0],
];

// ===================== RECENT BOOKINGS =====================
$recent_bookings = $conn->query("
    SELECT b.*, c.full_name, cl.brand, cl.model 
    FROM car_bookings b
    JOIN car_customers c ON b.customer_id = c.id
    JOIN car_listings cl ON b.car_id = cl.id
    ORDER BY b.created_at DESC 
    LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DriveElite</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f9fafb; }

        /* ===================== ADMIN HEADER ===================== */
        .admin-header {
            background: var(--navy-800);
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 12px rgba(15, 36, 56, 0.15);
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h1 {
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .admin-header h1 i { color: var(--blue-500); }
        .admin-header .admin-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .admin-header .welcome {
            color: #cbd5e1;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }
        .admin-header a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .admin-header a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--gold-400);
        }
        .admin-header a.logout:hover {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
        }

        /* ===================== ADMIN STATS ===================== */
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2.5rem 0;
        }
        .stat-card {
            background: #fff;
            padding: 1.6rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 36, 56, 0.06);
            border: 1px solid var(--gray-200);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(15, 36, 56, 0.1);
        }
        .stat-card i {
            font-size: 1.8rem;
            color: var(--blue-500);
            margin-bottom: 0.6rem;
            display: block;
        }
        .stat-card h3 {
            font-size: 2rem;
            color: var(--navy-800);
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.3rem;
        }
        .stat-card p {
            color: var(--gray-500);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* ===================== ADMIN TABLE ===================== */
        .section-heading {
            color: var(--navy-800);
            margin: 2.5rem 0 1rem;
            font-size: 1.3rem;
            font-weight: 700;
        }
        .admin-table {
            width: 100%;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 36, 56, 0.06);
            border: 1px solid var(--gray-200);
            border-collapse: collapse;
        }
        .admin-table th {
            background: var(--gray-100);
            padding: 1rem 1.2rem;
            text-align: left;
            font-size: 0.78rem;
            text-transform: uppercase;
            color: var(--gray-600);
            letter-spacing: 0.5px;
            font-weight: 700;
        }
        .admin-table td {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.92rem;
            color: var(--gray-700);
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: var(--gray-50); }

        /* ===================== BADGES ===================== */
        .badge-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .badge-active    { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }

        /* ===================== EMPTY STATE ===================== */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray-500);
        }
        .empty-state i {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
            display: block;
        }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 640px) {
            .admin-table { display: block; overflow-x: auto; white-space: nowrap; }
            .admin-header .container { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<!-- ADMIN HEADER -->
<div class="admin-header">
    <div class="container">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <div class="admin-nav">
            <span class="welcome">
                <i class="fas fa-user-circle"></i>
                <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
            </span>
            <a href="manage_cars.php"><i class="fas fa-car"></i> Cars</a>
            <a href="manage_bookings.php"><i class="fas fa-calendar"></i> Bookings</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container">

    <!-- STATS -->
    <div class="admin-stats">
        <div class="stat-card">
            <i class="fas fa-car"></i>
            <h3><?php echo number_format($stats['total_cars']); ?></h3>
            <p>Total Vehicles</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle" style="color: var(--green-500);"></i>
            <h3><?php echo number_format($stats['available_cars']); ?></h3>
            <p>Available</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <h3><?php echo number_format($stats['total_bookings']); ?></h3>
            <p>Total Bookings</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-clock" style="color: var(--gold-400);"></i>
            <h3><?php echo number_format($stats['pending_bookings']); ?></h3>
            <p>Active / Pending</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-dollar-sign" style="color: var(--green-500);"></i>
            <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
            <p>Revenue</p>
        </div>
    </div>

    <!-- RECENT BOOKINGS -->
    <h2 class="section-heading"><i class="fas fa-history"></i> Recent Bookings</h2>

    <?php if (empty($recent_bookings)): ?>
        <div class="admin-table">
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No bookings yet. Once customers start reserving cars, they'll appear here.</p>
            </div>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_bookings as $b): ?>
                <tr>
                    <td><strong>#<?php echo str_pad($b['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($b['full_name'] ?? 'Guest'); ?></td>
                    <td><?php echo htmlspecialchars(($b['brand'] ?? '') . ' ' . ($b['model'] ?? '')); ?></td>
                    <td>
                        <?php echo htmlspecialchars($b['hire_date'] ?? ''); ?>
                        &rarr;
                        <?php echo htmlspecialchars($b['return_date'] ?? ''); ?>
                    </td>
                    <td><strong>$<?php echo number_format((float)($b['total_amount'] ?? 0), 2); ?></strong></td>
                    <td>
                        <?php
                        $status = $b['booking_status'] ?? 'active';
                        $label  = ucfirst($status);
                        ?>
                        <span class="badge-status badge-<?php echo htmlspecialchars($status); ?>">
                            <?php echo htmlspecialchars($label); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>