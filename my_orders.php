<?php
session_start();

$base_url='..';
$page_title="My Orders – MultiShopOrder";
$required_role='customer';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

$uid=$_SESSION['user_id'];

/* GET ORDERS */
$stmt=$conn->prepare("
SELECT * 
FROM orders 
WHERE customer_id=? 
ORDER BY id DESC
");
$stmt->bind_param("i",$uid);
$stmt->execute();
$orders=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__.'/../includes/header.php';

function badge($t,$s){ 
    return "<span class='badge status-$s'>".ucfirst($t)."</span>"; 
}
function d($x){ 
    return date('d M Y h:i A',strtotime($x)); 
}
?>

<!-- 🔥 HARD FIX (THIS GUARANTEES VISIBILITY) -->
<style>
.badge {
    color: #111827 !important;
    font-weight: 600;
}

/* PAYMENT */
.status-paid {
    background-color: #DBEAFE !important;
    color: #1E40AF !important;
}

.status-unpaid {
    background-color: #E5E7EB !important;
    color: #111827 !important;
}

/* ORDER STATUS */
.status-placed {
    background-color: #E5E7EB !important;
    color: #111827 !important;
}

.status-preparing {
    background-color: #FEF3C7 !important;
    color: #92400E !important;
}

.status-ready {
    background-color: #D1FAE5 !important;
    color: #065F46 !important;
}

.status-completed {
    background-color: #A7F3D0 !important;
    color: #064E3B !important;
}

.status-cancelled {
    background-color: #FECACA !important;
    color: #7F1D1D !important;
}
</style>

<h3 class="fw-semibold mb-3">My Orders</h3>

<div class="card shadow-sm">

<table class="table align-middle mb-0">

<thead class="table-light">
<tr>
<th>Code</th>
<th>Items</th>
<th>Time</th>
<th>Payment</th>
<th>Status</th>
<th class="text-end">₹</th>
</tr>
</thead>

<tbody>

<?php if($orders): foreach($orders as $o): ?>

<tr>

<td>
<b><?php echo $o['order_code']; ?></b><br>
<small class="text-muted">Show at counter</small>
</td>

<td>
<?php
$items=$conn->query("
SELECT mi.item_name,oi.quantity
FROM order_items oi
JOIN menu_items mi ON oi.menu_item_id=mi.id
WHERE oi.order_id=".$o['id']
);

while($it=$items->fetch_assoc()){
    echo htmlspecialchars($it['item_name'])." x".$it['quantity']."<br>";
}
?>
</td>

<td><?php echo d($o['created_at']); ?></td>

<td><?php echo badge($o['payment_status'],$o['payment_status']); ?></td>

<td><?php echo badge($o['order_status'],$o['order_status']); ?></td>

<td class="text-end"><?php echo number_format($o['total_amount'],2); ?></td>

</tr>

<?php endforeach; else: ?>

<tr>
<td colspan="6" class="text-center text-muted py-5">No orders</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>

<?php include __DIR__.'/../includes/footer.php'; ?>