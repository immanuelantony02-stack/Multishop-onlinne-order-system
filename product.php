<?php
session_start();

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

if(!isset($_SESSION['shop_id'])){
die("Vendor shop not found.");
}

$shop_id=$_SESSION['shop_id'];
$msg="";

/* ADD PRODUCT */
if(isset($_POST['add'])){
$name=trim($_POST['name']);
$price=floatval($_POST['price']);

if($name!="" && $price>0){

$stmt=$conn->prepare("
INSERT INTO menu_items (shop_id,item_name,price,is_available)
VALUES (?,?,?,1)
");

$stmt->bind_param("isd",$shop_id,$name,$price);
$stmt->execute();

$msg="Product added";
}
}

/* UPDATE PRODUCT */
if(isset($_POST['update'])){

$id=(int)$_POST['id'];
$name=$_POST['name'];
$price=$_POST['price'];

$stmt=$conn->prepare("
UPDATE menu_items
SET item_name=?,price=?
WHERE id=? AND shop_id=?
");

$stmt->bind_param("sdii",$name,$price,$id,$shop_id);
$stmt->execute();

$msg="Product updated";
}

/* DELETE PRODUCT */
if(isset($_POST['delete'])){

$id=(int)$_POST['id'];

$stmt=$conn->prepare("
DELETE FROM menu_items
WHERE id=? AND shop_id=?
");

$stmt->bind_param("ii",$id,$shop_id);
$stmt->execute();

$msg="Product deleted";
}

/* TOGGLE STOCK */
if(isset($_POST['stock'])){

$id=(int)$_POST['id'];

$stmt=$conn->prepare("
UPDATE menu_items
SET is_available = IF(is_available=1,0,1)
WHERE id=? AND shop_id=?
");

$stmt->bind_param("ii",$id,$shop_id);
$stmt->execute();

$msg="Stock updated";
}

/* FETCH PRODUCTS */
$stmt=$conn->prepare("
SELECT * FROM menu_items
WHERE shop_id=?
ORDER BY item_name
");

$stmt->bind_param("i",$shop_id);
$stmt->execute();

$products=$stmt->get_result();

include __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Home</a>

<h3 class="mb-3">Manage Products</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php endif; ?>

<h5>Add Product</h5>

<form method="post" class="d-flex gap-2 mb-4">

<input name="name" placeholder="Item name" required class="form-control">

<input name="price" type="number" step="0.01" placeholder="Price" required class="form-control">

<button name="add" class="btn btn-primary">Add</button>

</form>

<h5>Your Products</h5>

<table class="table table-bordered">

<thead>
<tr>
<th>Name</th>
<th>Price</th>
<th>Status</th>
<th>Sold Today</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php while($row=$products->fetch_assoc()): ?>

<tr>
<form method="post">

<td>
<input name="name" value="<?php echo htmlspecialchars($row['item_name']); ?>" class="form-control">
</td>

<td>
<input name="price" value="<?php echo $row['price']; ?>" class="form-control">
</td>

<td>
<?php
echo $row['is_available']
? "<span class='badge bg-success'>Available</span>"
: "<span class='badge bg-danger'>Out of Stock</span>";
?>
</td>

<td>
<?php

$stmt2=$conn->prepare("
SELECT SUM(quantity) sold
FROM order_items oi
JOIN orders o ON oi.order_id=o.id
WHERE oi.menu_item_id=?
AND DATE(o.created_at)=CURDATE()
");

$stmt2->bind_param("i",$row['id']);
$stmt2->execute();

$sold=$stmt2->get_result()->fetch_assoc()['sold'];

echo $sold ? $sold." sold" : "0";

?>
</td>

<td>

<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

<button name="update" class="btn btn-sm btn-primary">Update</button>

<button name="stock" class="btn btn-sm btn-warning">Stock</button>

<button name="delete" class="btn btn-sm btn-danger">Delete</button>

</td>

</form>
</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include __DIR__.'/../includes/footer.php'; ?>