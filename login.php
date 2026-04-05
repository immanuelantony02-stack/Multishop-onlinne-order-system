<?php
session_start();
$base_url = '.';
$page_title = "Login – MultiShopOrder";

require_once __DIR__ . '/config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, shop_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['shop_id'] = $user['shop_id'];

                if ($user['role'] === 'customer') {
                    header("Location: customer/dashboard.php");
                } elseif ($user['role'] === 'vendor') {
                    header("Location: vendor/dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="fw-semibold mb-3 text-center">Login</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="mt-3 mb-0 text-center text-muted">
                    New here? <a href="register.php">Sign up as Customer</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
