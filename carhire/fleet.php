<?php
require_once 'config.php';
$conn = getDBConnection();

// Advanced filtering
$brand = isset($_GET['brand']) ? sanitize($_GET['brand']) : '';
$transmission = isset($_GET['transmission']) ? sanitize($_GET['transmission']) : '';
$seats = isset($_GET['seats']) ? intval($_GET['seats']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'brand';

$sql = "SELECT * FROM car_listings WHERE status = 'available'";
$params = [];
$types = '';

if ($brand) { $sql .= " AND brand = ?"; $params[] = $brand; $types .= 's'; }
if ($transmission) { $sql .= " AND transmission = ?"; $params[] = $transmission; $types .= 's'; }
if ($seats > 0) { $sql .= " AND seats >= ?"; $params[] = $seats; $types .= 'i'; }
if ($max_price > 0) { $sql .= " AND price_per_day <= ?"; $params[] = $max_price; $types .= 'd'; }

$orderMap = [
    'brand' => 'brand, model',
    'price_asc' => 'price_per_day ASC',
    'price_desc' => 'price_per_day DESC',
    'year' => 'year DESC'
];
$sql .= " ORDER BY " . ($orderMap[$sort] ?? 'brand, model');

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get brands for filter
$brands = $conn->query("SELECT DISTINCT brand FROM car_listings WHERE status = 'available' ORDER BY brand")->fetch_all(MYSQLI_ASSOC);
$conn->close();

$page_title  = 'Our Fleet | DriveElite';
$active_page = 'fleet';
include 'header.php';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Our Fleet</span>
        </nav>
        <h1>Browse Our Premium Fleet</h1>
        <p><?php echo count($cars); ?> vehicles available for hire right now</p>
    </div>
</section>

<section class="fleet-layout">
    <div class="container">
        <div class="fleet-grid-wrapper">

            <!-- SIDEBAR FILTERS -->
            <aside class="fleet-sidebar">
                <h3><i class="fas fa-filter"></i> Filters</h3>

                <form method="GET" action="fleet.php" id="filterForm">
                    <div class="filter-group">
                        <label>Brand</label>
                        <select name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo htmlspecialchars($b['brand']); ?>"
                                    <?php echo $brand === $b['brand'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b['brand']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Transmission</label>
                        <select name="transmission">
                            <option value="">Any</option>
                            <option value="Automatic" <?php echo $transmission === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                            <option value="Manual" <?php echo $transmission === 'Manual' ? 'selected' : ''; ?>>Manual</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Minimum Seats</label>
                        <select name="seats">
                            <option value="0">Any</option>
                            <option value="2" <?php echo $seats == 2 ? 'selected' : ''; ?>>2+ Seats</option>
                            <option value="4" <?php echo $seats == 4 ? 'selected' : ''; ?>>4+ Seats</option>
                            <option value="5" <?php echo $seats == 5 ? 'selected' : ''; ?>>5+ Seats</option>
                            <option value="7" <?php echo $seats == 7 ? 'selected' : ''; ?>>7+ Seats</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Max Price / Day ($)</label>
                        <input type="number" name="max_price" value="<?php echo $max_price ?: ''; ?>" placeholder="e.g., 150" min="0">
                    </div>

                    <div class="filter-group">
                        <label>Sort By</label>
                        <select name="sort">
                            <option value="brand" <?php echo $sort === 'brand' ? 'selected' : ''; ?>>Brand (A-Z)</option>
                            <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price (Low → High)</option>
                            <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price (High → Low)</option>
                            <option value="year" <?php echo $sort === 'year' ? 'selected' : ''; ?>>Newest First</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-apply"><i class="fas fa-search"></i> Apply Filters</button>
                    <a href="fleet.php" class="btn-clear"><i class="fas fa-times"></i> Clear All</a>
                </form>
            </aside>

            <!-- RESULTS -->
            <div class="fleet-results">
                <div class="listing-header">
                    <h2>
                        <?php echo $brand ? htmlspecialchars($brand) . ' Vehicles' : 'All Vehicles'; ?>
                        <span>(<?php echo count($cars); ?> found)</span>
                    </h2>
                </div>

                <?php if (empty($cars)): ?>
                    <div class="no-results">
                        <i class="fas fa-car-crash"></i>
                        <p>No vehicles match your filters. Try adjusting your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="car-grid">
                        <?php foreach ($cars as $car): ?>
                            <div class="car-card">
                                <div class="car-image">
                                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>"
                                         alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"
                                         loading="lazy">
                                    <span class="car-badge"><?php echo htmlspecialchars($car['brand']); ?></span>
                                    <span class="availability available">Available</span>
                                </div>
                                <div class="car-info">
                                    <h3><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                                    <div class="car-specs">
                                        <span><i class="fas fa-calendar"></i> <?php echo $car['year']; ?></span>
                                        <span><i class="fas fa-chair"></i> <?php echo $car['seats']; ?> Seats</span>
                                        <span><i class="fas fa-gas-pump"></i> <?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                        <span><i class="fas fa-cog"></i> <?php echo htmlspecialchars($car['transmission']); ?></span>
                                    </div>
                                    <div class="car-price">
                                        <?php echo formatCurrency($car['price_per_day']); ?> <small>/ day</small>
                                    </div>
                                    <div class="car-actions">
                                        <a href="car_detail.php?id=<?php echo $car['id']; ?>" class="btn-details">
                                            <i class="fas fa-info-circle"></i> Details
                                        </a>
                                        <button class="btn-hire" onclick='openBookingModal(<?php echo json_encode($car, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                            <i class="fas fa-key"></i> Hire Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php include 'footer.php'; ?>