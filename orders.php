<?php
session_start();

$base_url='..';
$page_title="Vendor Orders";
$required_role='vendor';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

$shop_id=$_SESSION['shop_id'];

/* UPDATE STATUS */
if(isset($_POST['order_id'])){
    $id=$_POST['order_id'];
    $status=$_POST['status'];

    $stmt=$conn->prepare("
    UPDATE orders SET order_status=? WHERE id=?
    ");
    $stmt->bind_param("si",$status,$id);
    $stmt->execute();
}

/* GET ORDERS (ONLY THEIR ITEMS) */
$orders=$conn->query("
SELECT DISTINCT o.*,u.name customer
FROM orders o
JOIN users u ON o.customer_id=u.id
JOIN order_items oi ON o.id=oi.order_id
JOIN menu_items mi ON oi.menu_item_id=mi.id
WHERE mi.shop_id=$shop_id
AND o.payment_status='paid'
ORDER BY o.created_at DESC
");

include __DIR__.'/../includes/header.php';
?>

<h3>Orders</h3>

<table class="table table-bordered">

<tr>
<th>Order</th>
<th>Customer</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($o=$orders->fetch_assoc()): ?>

<tr>

<td>
<b><?php echo $o['order_code']; ?></b><br>

<small>
<?php
$items=$conn->query("
SELECT mi.item_name,oi.quantity
FROM order_items oi
JOIN menu_items mi ON oi.menu_item_id=mi.id
WHERE oi.order_id=".$o['id']."
AND mi.shop_id=".$shop_id
);

while($it=$items->fetch_assoc()){
    echo $it['item_name']." x".$it['quantity']."<br>";
}
?>
</small>
</td>

<td><?php echo $o['customer']; ?></td>

<td>₹<?php echo $o['total_amount']; ?></td>

<td><?php echo $o['order_status']; ?></td>

<td>

<form method="post">
<input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">

<?php if($o['order_status']=="placed"): ?>
<input type="hidden" name="status" value="preparing">
<button class="btn btn-warning btn-sm">Preparing</button>

<?php elseif($o['order_status']=="preparing"): ?>
<input type="hidden" name="status" value="ready">
<button class="btn btn-success btn-sm">Ready</button>

<?php elseif($o['order_status']=="ready"): ?>
<input type="hidden" name="status" value="completed">
<button class="btn btn-primary btn-sm">Complete</button>
<?php endif; ?>

</form>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php include __DIR__.'/../includes/footer.php'; ?>