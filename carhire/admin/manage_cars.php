<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();

// ===================== HANDLE DELETE =====================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM car_listings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_cars.php?msg=deleted');
    exit;
}

// ===================== HANDLE STATUS TOGGLE =====================
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $conn->query("UPDATE car_listings SET status = IF(status='available','hired','available') WHERE id = $id");
    header('Location: manage_cars.php?msg=toggled');
    exit;
}

// ===================== FETCH CARS =====================
$cars = $conn->query("SELECT * FROM car_listings ORDER BY brand, model")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars | Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f9fafb; }
        .admin-header { background: var(--navy-800); color: #fff; padding: 1rem 0; box-shadow: 0 2px 12px rgba(15,36,56,.15); }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .admin-header h1 { font-size: 1.3rem; display: flex; align-items: center; gap: 0.6rem; }
        .admin-header h1 i { color: var(--blue-500); }
        .admin-header .admin-nav { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .admin-header a { color: #cbd5e1; text-decoration: none; padding: 0.5rem 1rem; border-radius: 30px; font-size: 0.88rem; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; }
        .admin-header a:hover { background: rgba(255,255,255,.1); color: var(--gold-400); }
        .admin-header a.active { background: rgba(59,130,246,.2); color: #fff; }
        .admin-header a.logout:hover { background: rgba(239,68,68,.2); color: #fca5a5; }

        .page-actions { display: flex; justify-content: space-between; align-items: center; margin: 2rem 0 1rem; flex-wrap: wrap; gap: 1rem; }
        .page-actions h2 { color: var(--navy-800); font-size: 1.4rem; }
        .btn-add { background: linear-gradient(135deg, var(--navy-700), var(--navy-600)); color: #fff; padding: 0.7rem 1.5rem; border-radius: 30px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.25s; }
        .btn-add:hover { background: linear-gradient(135deg, var(--blue-500), var(--blue-600)); transform: translateY(-2px); box-shadow: 0 10px 22px rgba(59,130,246,.35); }

        .admin-table { width: 100%; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(15,36,56,.06); border: 1px solid var(--gray-200); border-collapse: collapse; }
        .admin-table th { background: var(--gray-100); padding: 1rem 1.2rem; text-align: left; font-size: 0.78rem; text-transform: uppercase; color: var(--gray-600); letter-spacing: 0.5px; font-weight: 700; }
        .admin-table td { padding: 1rem 1.2rem; border-bottom: 1px solid var(--gray-200); font-size: 0.92rem; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: var(--gray-50); }
        .car-thumb { width: 60px; height: 40px; object-fit: cover; border-radius: 8px; background: var(--gray-100); }

        .badge { padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; display: inline-block; }
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-hired { background: #fef3c7; color: #92400e; }
        .badge-maintenance { background: #fee2e2; color: #991b1b; }

        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none; transition: all 0.2s; font-size: 0.85rem; }
        .action-btn.edit { background: var(--blue-50); color: var(--blue-600); }
        .action-btn.edit:hover { background: var(--blue-500); color: #fff; }
        .action-btn.toggle { background: #fef3c7; color: #92400e; }
        .action-btn.toggle:hover { background: var(--gold-400); color: #fff; }
        .action-btn.delete { background: #fee2e2; color: #991b1b; }
        .action-btn.delete:hover { background: var(--red-500); color: #fff; }

        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; padding: 1rem 1.2rem; border-radius: 12px; margin-bottom: 1rem; font-weight: 500; }
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray-500); background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); }
        .empty-state i { font-size: 3rem; color: var(--gray-300); margin-bottom: 1rem; display: block; }

        @media (max-width: 768px) {
            .admin-table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="container">
        <h1><i class="fas fa-car"></i> Manage Cars</h1>
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_cars.php" class="active"><i class="fas fa-car"></i> Cars</a>
            <a href="manage_bookings.php"><i class="fas fa-calendar"></i> Bookings</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success" style="margin-top: 1.5rem;">
            <i class="fas fa-check-circle"></i>
            <?php
            $messages = [
                'deleted' => 'Car deleted successfully.',
                'toggled' => 'Car status updated.',
                'saved'   => 'Car saved successfully.',
            ];
            echo $messages[$_GET['msg']] ?? 'Action completed.';
            ?>
        </div>
    <?php endif; ?>

    <div class="page-actions">
        <h2><i class="fas fa-list"></i> All Vehicles (<?php echo count($cars); ?>)</h2>
        <a href="car_form.php" class="btn-add"><i class="fas fa-plus"></i> Add New Car</a>
    </div>

    <?php if (empty($cars)): ?>
        <div class="empty-state">
            <i class="fas fa-car"></i>
            <p>No cars in the system yet.</p>
            <a href="car_form.php" class="btn-add" style="margin-top: 1rem;"><i class="fas fa-plus"></i> Add First Car</a>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Plate</th>
                    <th>Price/Day</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                <tr>
                   <td>
    <?php
    // If the URL starts with http:// or https://, use it directly.
    // Otherwise, prefix with ../ (we're in /admin/ and images are at /carhire/).
    $imgSrc = $car['image_url'];
    if (strpos($imgSrc, 'http://') !== 0 && strpos($imgSrc, 'https://') !== 0) {
        $imgSrc = '../' . ltrim($imgSrc, '/');
    }
    ?>
    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
         class="car-thumb"
         loading="lazy"
         referrerpolicy="no-referrer"
         onerror="this.onerror=null; this.src='https://via.placeholder.com/60x40/1a3a5c/ffffff?text=Car'">
</td>
                    <td><strong><?php echo htmlspecialchars($car['brand']); ?></strong></td>
                    <td><?php echo htmlspecialchars($car['model']); ?></td>
                    <td><?php echo (int) $car['year']; ?></td>
                    <td><code><?php echo htmlspecialchars($car['plate_number']); ?></code></td>
                    <td><strong>$<?php echo number_format((float) $car['price_per_day'], 2); ?></strong></td>
                    <td><span class="badge badge-<?php echo htmlspecialchars($car['status']); ?>"><?php echo ucfirst($car['status']); ?></span></td>
                    <td>
                        <a href="car_form.php?id=<?php echo (int) $car['id']; ?>" class="action-btn edit" title="Edit"><i class="fas fa-pen"></i></a>
                        <a href="manage_cars.php?toggle=<?php echo (int) $car['id']; ?>" class="action-btn toggle" title="Toggle status" onclick="return confirm('Change this car\'s availability status?')"><i class="fas fa-sync-alt"></i></a>
                        <a href="manage_cars.php?delete=<?php echo (int) $car['id']; ?>" class="action-btn delete" title="Delete" onclick="return confirm('Delete this car permanently?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>