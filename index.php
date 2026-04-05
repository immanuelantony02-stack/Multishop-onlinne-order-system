<?php
session_start();
$base_url = '.';
$page_title = "MultiShopOrder – Order from 9 Campus Shops";
require_once __DIR__ . '/config/db.php';

// Fetch shops
$shops = [];
$sql = "SELECT * FROM shops WHERE is_active = 1 ORDER BY id ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $shops[] = $row;
}

include __DIR__ . '/includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6 mb-3">
        <h1 class="fw-bold text-dark mb-3">
            Order from 9 Campus Shops &amp; Pay at Counter
        </h1>
        <p class="lead text-muted">
            Skip the waiting line. Place your food order online, pay at the counter, and track your order status in real-time.
        </p>
        <div class="d-flex gap-2 mt-3">
            <a href="login.php" class="btn btn-primary btn-lg">Customer Login</a>
            <a href="register.php" class="btn btn-outline-primary btn-lg">Sign Up</a>
        </div>
    </div>
    <div class="col-md-6 text-md-end">
        <img src="https://via.placeholder.com/450x260?text=MultiShopOrder" class="img-fluid rounded shadow-sm" alt="MultiShopOrder">
    </div>
</div>

<h3 class="mb-3 fw-semibold">Our 9 Campus Shops</h3>
<div class="row g-3">
    <?php foreach ($shops as $shop): ?>
        <div class="col-md-4">
            <div class="card shop-card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($shop['name']); ?></h5>
                    <p class="card-text text-muted flex-grow-1">
                        <?php echo htmlspecialchars($shop['description']); ?>
                    </p>
                    <a href="login.php" class="btn btn-primary mt-2">View Menu</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
