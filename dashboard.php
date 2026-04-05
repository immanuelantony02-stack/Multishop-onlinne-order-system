<?php
session_start();

$base_url='..';
$page_title="Vendor Dashboard";
$required_role='vendor';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

$shop_id=$_SESSION['shop_id'] ?? 0;

$new_orders=$conn->query("
SELECT COUNT(*) c
FROM orders
WHERE shop_id=$shop_id
AND payment_status='paid'
AND order_status='placed'
")->fetch_assoc()['c'];

$preparing=$conn->query("
SELECT COUNT(*) c
FROM orders
WHERE shop_id=$shop_id
AND payment_status='paid'
AND order_status='preparing'
")->fetch_assoc()['c'];

$ready=$conn->query("
SELECT COUNT(*) c
FROM orders
WHERE shop_id=$shop_id
AND payment_status='paid'
AND order_status='ready'
")->fetch_assoc()['c'];

include __DIR__.'/../includes/header.php';
?>

<h2>Vendor Dashboard</h2>

<div class="row">

<div class="col">
<div class="card p-3">
New Orders
<h3><?php echo $new_orders; ?></h3>
</div>
</div>

<div class="col">
<div class="card p-3">
Preparing
<h3><?php echo $preparing; ?></h3>
</div>
</div>

<div class="col">
<div class="card p-3">
Ready
<h3><?php echo $ready; ?></h3>
</div>
</div>

</div>

<br>

<a class="btn btn-primary" href="orders.php">
View Orders
</a>

<a class="btn btn-success" href="product.php">
Manage Products
</a>

<?php include __DIR__.'/../includes/footer.php'; ?>