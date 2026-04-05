<?php
session_start();

$base_url='..';
$page_title="Pay at Counter";
$required_role='admin';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

if(isset($_POST['order_id'])){
    $id=(int)$_POST['order_id'];
    $conn->query("UPDATE orders SET payment_status='paid' WHERE id=$id");
}

$orders=$conn->query("
SELECT o.*,u.name customer
FROM orders o
JOIN users u ON o.customer_id=u.id
WHERE o.payment_status='unpaid'
ORDER BY o.created_at DESC
");

include __DIR__.'/../includes/header.php';
?>

<h3>Pay at Counter</h3>

<table class="table table-bordered">

<tr>
<th>Order</th>
<th>Customer</th>
<th>Total</th>
<th>Approve</th>
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
WHERE oi.order_id=".$o['id']
);

while($it=$items->fetch_assoc()){
    echo $it['item_name']." x".$it['quantity']."<br>";
}
?>
</small>
</td>

<td><?php echo $o['customer']; ?></td>

<td>₹<?php echo $o['total_amount']; ?></td>

<td>
<form method="post">
<input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
<button class="btn btn-success btn-sm">Approve Payment</button>
</form>
</td>

</tr>

<?php endwhile; ?>

</table>

<?php include __DIR__.'/../includes/footer.php'; ?>