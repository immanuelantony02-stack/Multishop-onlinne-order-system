<?php
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);
session_start();

$base_url='..';
$page_title="Menu – MultiShopOrder";
$required_role='customer';

require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../includes/auth_check.php';

$shop_id=(int)($_GET['shop_id'] ?? 0);

/* INIT CART */
if(!isset($_SESSION['cart'])){
    $_SESSION['cart']=[];
}

/* GET SHOP */
$stmt=$conn->prepare("SELECT * FROM shops WHERE id=? AND is_active=1");
$stmt->bind_param("i",$shop_id);
$stmt->execute();
$shop=$stmt->get_result()->fetch_assoc();

if(!$shop){
    die("Invalid shop");
}

$msg="";
$err="";

/* ADD TO CART */
if(isset($_POST['add_to_cart'])){

    $item_id=(int)$_POST['item_id'];
    $qty=max(1,(int)$_POST['quantity']);

    $stmt=$conn->prepare("
    SELECT id,item_name,price,is_available
    FROM menu_items
    WHERE id=? AND shop_id=?
    ");
    $stmt->bind_param("ii",$item_id,$shop_id);
    $stmt->execute();
    $item=$stmt->get_result()->fetch_assoc();

    if(!$item){
        $err="Invalid item";
    }
    elseif(!$item['is_available']){
        $err="Item unavailable";
    }
    else{

        if(!isset($_SESSION['cart'][$shop_id])){
            $_SESSION['cart'][$shop_id]=[];
        }

        if(isset($_SESSION['cart'][$shop_id][$item_id])){
            $_SESSION['cart'][$shop_id][$item_id]['qty'] += $qty;
        }else{
            $_SESSION['cart'][$shop_id][$item_id]=[
                'name'=>$item['item_name'],
                'price'=>$item['price'],
                'qty'=>$qty
            ];
        }

        $msg="Item added to cart";
    }
}

/* PLACE ORDER (FIXED FK + SINGLE BILL) */
if(isset($_POST['place_order'])){

    if(!empty($_SESSION['cart'])){

        $customer_id=$_SESSION['user_id'];
        $order_code="MSO".time().rand(100,999);

        $total=0;

        /* CALCULATE TOTAL */
        foreach($_SESSION['cart'] as $shop=>$items){
            foreach($items as $i){
                $total += $i['price'] * $i['qty'];
            }
        }

        /* IMPORTANT FIX: USE VALID SHOP ID */
        $first_shop_id = array_key_first($_SESSION['cart']);

        /* CREATE ORDER */
        $stmt=$conn->prepare("
        INSERT INTO orders
        (order_code,customer_id,shop_id,created_at,payment_status,order_status,total_amount)
        VALUES(?,?,?,NOW(),'unpaid','placed',?)
        ");

        $stmt->bind_param("siid",$order_code,$customer_id,$first_shop_id,$total);
        $stmt->execute();

        $order_id=$stmt->insert_id;

        /* INSERT ITEMS */
        $stmt2=$conn->prepare("
        INSERT INTO order_items
        (order_id,menu_item_id,quantity,price_at_order_time)
        VALUES(?,?,?,?)
        ");

        foreach($_SESSION['cart'] as $shop=>$items){
            foreach($items as $id=>$i){

                $stmt2->bind_param(
                    "iiid",
                    $order_id,
                    $id,
                    $i['qty'],
                    $i['price']
                );
                $stmt2->execute();
            }
        }

        unset($_SESSION['cart']);

        $msg="Order placed successfully (single bill)";
    }
}

/* MENU ITEMS */
$stmt=$conn->prepare("
SELECT * FROM menu_items
WHERE shop_id=? AND is_available=1
");
$stmt->bind_param("i",$shop_id);
$stmt->execute();
$items=$stmt->get_result();

include __DIR__.'/../includes/header.php';
?>

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-secondary mb-3">← Back</a>

<h3><?php echo htmlspecialchars($shop['name']); ?> Menu</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php endif; ?>

<?php if($err): ?>
<div class="alert alert-danger"><?php echo $err; ?></div>
<?php endif; ?>

<div class="row">

<div class="col-md-8">

<table class="table table-bordered">

<tr>
<th>Item</th>
<th>Price</th>
<th>Add</th>
</tr>

<?php while($row=$items->fetch_assoc()): ?>

<tr>

<td><?php echo htmlspecialchars($row['item_name']); ?></td>

<td>₹<?php echo number_format($row['price'],2); ?></td>

<td>
<form method="post" class="d-flex gap-2">
<input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">

<input type="number"
name="quantity"
value="1"
min="1"
class="form-control"
style="width:80px;">

<button name="add_to_cart" class="btn btn-primary btn-sm">
Add
</button>
</form>
</td>

</tr>

<?php endwhile; ?>

</table>

</div>

<div class="col-md-4">

<div class="card">
<div class="card-body">

<h5>Cart</h5>

<?php
$total=0;

if(!empty($_SESSION['cart'])){

foreach($_SESSION['cart'] as $items){
foreach($items as $i){

$line=$i['price']*$i['qty'];
$total+=$line;

echo "<div class='d-flex justify-content-between'>
<span>".htmlspecialchars($i['name'])." x".$i['qty']."</span>
<span>₹".$line."</span>
</div>";
}
}
?>

<hr>

<strong>Total ₹<?php echo $total; ?></strong>

<form method="post" class="mt-2">
<button name="place_order" class="btn btn-success w-100">
Place Order
</button>
</form>

<?php
}else{
echo "<p class='text-muted'>Cart empty</p>";
}
?>

</div>
</div>

</div>

</div>

</div>

<?php include __DIR__.'/../includes/footer.php'; ?>