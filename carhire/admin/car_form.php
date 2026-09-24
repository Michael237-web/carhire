<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$conn = getDBConnection();
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$car = [
    'brand' => '', 'model' => '', 'year' => date('Y'),
    'price_per_day' => '', 'seats' => 5, 'fuel_type' => 'Petrol',
    'transmission' => 'Automatic', 'color' => '', 'plate_number' => '',
    'image_url' => '', 'status' => 'available'
];

// ===================== LOAD EXISTING CAR =====================
if ($isEdit) {
    $id = (int) $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM car_listings WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $loaded = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$loaded) {
        header('Location: manage_cars.php');
        exit;
    }
    $car = $loaded;
}

// ===================== HANDLE SUBMIT =====================
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand         = trim($_POST['brand'] ?? '');
    $model         = trim($_POST['model'] ?? '');
    $year          = (int) ($_POST['year'] ?? 0);
    $price         = (float) ($_POST['price_per_day'] ?? 0);
    $seats         = (int) ($_POST['seats'] ?? 5);
    $fuel          = trim($_POST['fuel_type'] ?? 'Petrol');
    $transmission  = trim($_POST['transmission'] ?? 'Automatic');
    $color         = trim($_POST['color'] ?? '');
    $plate         = trim($_POST['plate_number'] ?? '');
    $image         = trim($_POST['image_url'] ?? '');
    $status        = trim($_POST['status'] ?? 'available');

    if (!$brand || !$model || !$plate || !$price) {
        $error = 'Please fill in all required fields.';
    } else {
        if ($isEdit) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare("
                UPDATE car_listings 
                SET brand=?, model=?, year=?, price_per_day=?, seats=?, fuel_type=?, transmission=?, color=?, plate_number=?, image_url=?, status=? 
                WHERE id=?
            ");
            $stmt->bind_param("ssidissssssi", $brand, $model, $year, $price, $seats, $fuel, $transmission, $color, $plate, $image, $status, $id);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO car_listings 
                (brand, model, year, price_per_day, seats, fuel_type, transmission, color, plate_number, image_url, status) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->bind_param("ssidissssss", $brand, $model, $year, $price, $seats, $fuel, $transmission, $color, $plate, $image, $status);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: manage_cars.php?msg=saved');
            exit;
        } else {
            $error = 'Save failed: ' . $stmt->error;
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Car' : 'Add Car'; ?> | Admin</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f9fafb; }
        .admin-header { background: var(--navy-800); color: #fff; padding: 1rem 0; }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .admin-header h1 { font-size: 1.3rem; display: flex; align-items: center; gap: 0.6rem; }
        .admin-header h1 i { color: var(--blue-500); }
        .admin-header .admin-nav { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
        .admin-header a { color: #cbd5e1; text-decoration: none; padding: 0.5rem 1rem; border-radius: 30px; font-size: 0.88rem; font-weight: 500; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; }
        .admin-header a:hover { background: rgba(255,255,255,.1); color: var(--gold-400); }
        .admin-header a.logout:hover { background: rgba(239,68,68,.2); color: #fca5a5; }

        .form-card { background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(15,36,56,.06); border: 1px solid var(--gray-200); margin: 2rem 0; max-width: 800px; margin-left: auto; margin-right: auto; }
        .form-card h2 { color: var(--navy-800); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
        .form-grid .full { grid-column: 1 / -1; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.4rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--gray-300); border-radius: 10px; font-size: 0.95rem; transition: all 0.2s; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--blue-500); box-shadow: 0 0 0 3px rgba(59,130,246,.12); }
        .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn { padding: 0.85rem 2rem; border-radius: 40px; font-weight: 600; font-size: 0.95rem; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.25s; }
        .btn-primary { background: linear-gradient(135deg, var(--navy-700), var(--navy-600)); color: #fff; }
        .btn-primary:hover { background: linear-gradient(135deg, var(--blue-500), var(--blue-600)); transform: translateY(-2px); box-shadow: 0 12px 26px rgba(59,130,246,.4); }
        .btn-secondary { background: var(--gray-100); color: var(--gray-700); }
        .btn-secondary:hover { background: var(--gray-200); }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 1rem 1.2rem; border-radius: 12px; margin-bottom: 1.5rem; }
        @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="admin-header">
    <div class="container">
        <h1><i class="fas fa-car"></i> <?php echo $isEdit ? 'Edit Car' : 'Add New Car'; ?></h1>
        <div class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_cars.php"><i class="fas fa-car"></i> Cars</a>
            <a href="manage_bookings.php"><i class="fas fa-calendar"></i> Bookings</a>
            <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <h2><i class="fas fa-<?php echo $isEdit ? 'pen' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h2>

        <?php if ($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Brand *</label>
                    <input type="text" name="brand" value="<?php echo htmlspecialchars($car['brand']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Model *</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="number" name="year" value="<?php echo (int) $car['year']; ?>" min="2000" max="<?php echo date('Y') + 1; ?>">
                </div>
                <div class="form-group">
                    <label>Price per Day ($) *</label>
                    <input type="number" step="0.01" name="price_per_day" value="<?php echo htmlspecialchars($car['price_per_day']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Seats</label>
                    <input type="number" name="seats" value="<?php echo (int) $car['seats']; ?>" min="1" max="20">
                </div>
                <div class="form-group">
                    <label>Fuel Type</label>
                    <select name="fuel_type">
                        <?php foreach (['Petrol', 'Diesel', 'Hybrid', 'Electric'] as $f): ?>
                            <option value="<?php echo $f; ?>" <?php echo $car['fuel_type'] === $f ? 'selected' : ''; ?>><?php echo $f; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Transmission</label>
                    <select name="transmission">
                        <?php foreach (['Automatic', 'Manual'] as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo $car['transmission'] === $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Color</label>
                    <input type="text" name="color" value="<?php echo htmlspecialchars($car['color']); ?>">
                </div>
                <div class="form-group">
                    <label>Plate Number *</label>
                    <input type="text" name="plate_number" value="<?php echo htmlspecialchars($car['plate_number']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['available', 'hired', 'maintenance'] as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $car['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Image URL</label>
                    <input type="text" name="image_url" value="<?php echo htmlspecialchars($car['image_url']); ?>" placeholder="https://... or cars/toyota-prado.jpg">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Car</button>
                <a href="manage_cars.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>