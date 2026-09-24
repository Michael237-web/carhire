<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();

// ===================== HANDLE STATUS CHANGE =====================
if (isset($_GET['status']) && isset($_GET['id'])) {
    $allowed = ['active', 'completed', 'cancelled'];
    $newStatus = $_GET['status'];
    $id = (int) $_GET['id'];

    if (in_array($newStatus, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE car_bookings SET booking_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: manage_bookings.php?msg=updated');
    exit;
}

// ===================== HANDLE DELETE =====================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM car_bookings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_bookings.php?msg=deleted');
    exit;
}

// ===================== FETCH BOOKINGS =====================
$bookings = $conn->query("
    SELECT b.*, 
           c.full_name, c.email, c.phone, c.id_number, c.driver_license,
           cl.brand, cl.model, cl.plate_number, cl.color
    FROM car_bookings b
    JOIN car_customers c ON b.customer_id = c.id
    JOIN car_listings cl ON b.car_id = cl.id
    ORDER BY b.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings | Admin</title>
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

        .page-heading { color: var(--navy-800); font-size: 1.4rem; margin: 2rem 0 1rem; display: flex; align-items: center; gap: 0.5rem; }

        .admin-table { width: 100%; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(15,36,56,.06); border: 1px solid var(--gray-200); border-collapse: collapse; }
        .admin-table th { background: var(--gray-100); padding: 1rem 1.2rem; text-align: left; font-size: 0.78rem; text-transform: uppercase; color: var(--gray-600); letter-spacing: 0.5px; font-weight: 700; }
        .admin-table td { padding: 1rem 1.2rem; border-bottom: 1px solid var(--gray-200); font-size: 0.92rem; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: var(--gray-50); }
        .admin-table code { background: var(--gray-100); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.8rem; }

        .customer-cell { display: flex; flex-direction: column; gap: 0.15rem; }
        .customer-cell strong { color: var(--navy-800); font-size: 0.95rem; }
        .customer-cell span { color: var(--gray-500); font-size: 0.8rem; }

        .badge { padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; display: inline-block; }
        .badge-active { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge-payment-pending { background: #fef3c7; color: #92400e; }
        .badge-payment-paid { background: #d1fae5; color: #065f46; }
        .badge-payment-refunded { background: #e5e7eb; color: #4b5563; }

        .actions-cell { display: flex; gap: 0.4rem; flex-wrap: wrap; }
        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; text-decoration: none; transition: all 0.2s; font-size: 0.85rem; }
        .action-btn.complete { background: #d1fae5; color: #065f46; }
        .action-btn.complete:hover { background: var(--green-500); color: #fff; }
        .action-btn.cancel { background: #fef3c7; color: #92400e; }
        .action-btn.cancel:hover { background: var(--gold-400); color: #fff; }
        .action-btn.delete { background: #fee2e2; color: #991b1b; }
        .action-btn.delete:hover { background: var(--red-500); color: #fff; }
        .action-btn.view { background: var(--blue-50); color: var(--blue-600); }
        .action-btn.view:hover { background: var(--blue-500); color: #fff; }

        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; padding: 1rem 1.2rem; border-radius: 12px; margin-top: 1.5rem; font-weight: 500; }

        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--gray-500); background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); }
        .empty-state i { font-size: 3rem; color: var(--gray-300); margin-bottom: 1rem; display: block; }

        /* Modal for viewing details */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(10,26,47,.6); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center; padding: 2rem; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; border-radius: 20px; max-width: 560px; width: 100%; padding: 2rem; position: relative; max-height: 85vh; overflow-y: auto; }
        .modal-box h2 { color: var(--navy-800); margin-bottom: 1.5rem; }
        .modal-close { position: absolute; top: 1rem; right: 1.2rem; font-size: 1.5rem; cursor: pointer; color: var(--gray-500); }
        .modal-close:hover { color: var(--red-500); }
        .detail-row { display: flex; justify-content: space-between; padding: 0.7rem 0; border-bottom: 1px solid var(--gray-200); font-size: 0.92rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row .label { color: var(--gray-500); font-weight: 500; }
        .detail-row .value { color: var(--navy-800); font-weight: 600; text-align: right; }

        @media (max-width: 768px) {
            .admin-table { display: block; overflow-x: auto; white-space: nowrap; }
        }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_cars.php"><i class="fas fa-car"></i> Cars</a>
            <a href="manage_bookings.php" class="active"><i class="fas fa-calendar"></i> Bookings</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container">

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <?php
            $messages = [
                'updated' => 'Booking status updated.',
                'deleted' => 'Booking deleted successfully.',
            ];
            echo $messages[$_GET['msg']] ?? 'Action completed.';
            ?>
        </div>
    <?php endif; ?>

    <h2 class="page-heading"><i class="fas fa-list"></i> All Bookings (<?php echo count($bookings); ?>)</h2>

    <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No bookings yet. Once a customer reserves a car, it will appear here.</p>
        </div>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Booking</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong>#<?php echo str_pad($b['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                    <td>
                        <div class="customer-cell">
                            <strong><?php echo htmlspecialchars($b['full_name']); ?></strong>
                            <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($b['email']); ?></span>
                            <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($b['phone']); ?></span>
                        </div>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($b['brand'] . ' ' . $b['model']); ?><br>
                        <code><?php echo htmlspecialchars($b['plate_number']); ?></code>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($b['hire_date']); ?><br>
                        <span style="color: var(--gray-500); font-size: 0.8rem;">&rarr; <?php echo htmlspecialchars($b['return_date']); ?></span>
                    </td>
                    <td><strong>$<?php echo number_format((float) $b['total_amount'], 2); ?></strong></td>
                    <td>
                        <span class="badge badge-<?php echo htmlspecialchars($b['booking_status']); ?>">
                            <?php echo ucfirst($b['booking_status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-payment-<?php echo htmlspecialchars($b['payment_status']); ?>">
                            <?php echo ucfirst($b['payment_status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <a href="#" class="action-btn view" title="View details"
                               onclick='event.preventDefault(); showDetails(<?php echo json_encode($b, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($b['booking_status'] !== 'completed'): ?>
                                <a href="manage_bookings.php?id=<?php echo (int) $b['id']; ?>&status=completed" class="action-btn complete" title="Mark as completed" onclick="return confirm('Mark this booking as completed?')"><i class="fas fa-check"></i></a>
                            <?php endif; ?>
                            <?php if ($b['booking_status'] !== 'cancelled'): ?>
                                <a href="manage_bookings.php?id=<?php echo (int) $b['id']; ?>&status=cancelled" class="action-btn cancel" title="Cancel booking" onclick="return confirm('Cancel this booking?')"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                            <a href="manage_bookings.php?delete=<?php echo (int) $b['id']; ?>" class="action-btn delete" title="Delete" onclick="return confirm('Delete this booking permanently?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<!-- DETAILS MODAL -->
<div class="modal-overlay" id="detailsModal" onclick="if(event.target===this) closeDetails()">
    <div class="modal-box">
        <span class="modal-close" onclick="closeDetails()">&times;</span>
        <h2><i class="fas fa-file-alt"></i> Booking Details</h2>
        <div id="detailsContent"></div>
    </div>
</div>

<script>
function showDetails(b) {
    const html = `
        <div class="detail-row"><span class="label">Booking ID</span><span class="value">#${String(b.id).padStart(6, '0')}</span></div>
        <div class="detail-row"><span class="label">Customer</span><span class="value">${escapeHtml(b.full_name)}</span></div>
        <div class="detail-row"><span class="label">Email</span><span class="value">${escapeHtml(b.email)}</span></div>
        <div class="detail-row"><span class="label">Phone</span><span class="value">${escapeHtml(b.phone)}</span></div>
        <div class="detail-row"><span class="label">ID Number</span><span class="value">${escapeHtml(b.id_number)}</span></div>
        <div class="detail-row"><span class="label">Driver License</span><span class="value">${escapeHtml(b.driver_license)}</span></div>
        <div class="detail-row"><span class="label">Vehicle</span><span class="value">${escapeHtml(b.brand + ' ' + b.model)}</span></div>
        <div class="detail-row"><span class="label">Plate</span><span class="value"><code>${escapeHtml(b.plate_number)}</code></span></div>
        <div class="detail-row"><span class="label">Color</span><span class="value">${escapeHtml(b.color)}</span></div>
        <div class="detail-row"><span class="label">Hire Date</span><span class="value">${escapeHtml(b.hire_date)}</span></div>
        <div class="detail-row"><span class="label">Return Date</span><span class="value">${escapeHtml(b.return_date)}</span></div>
        <div class="detail-row"><span class="label">Total Amount</span><span class="value">$${parseFloat(b.total_amount).toFixed(2)}</span></div>
        <div class="detail-row"><span class="label">Booking Status</span><span class="value">${escapeHtml(b.booking_status)}</span></div>
        <div class="detail-row"><span class="label">Payment Status</span><span class="value">${escapeHtml(b.payment_status)}</span></div>
    `;
    document.getElementById('detailsContent').innerHTML = html;
    document.getElementById('detailsModal').classList.add('active');
}

function closeDetails() {
    document.getElementById('detailsModal').classList.remove('active');
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDetails();
});
</script>

</body>
</html>