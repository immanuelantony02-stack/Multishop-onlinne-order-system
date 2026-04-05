<?php
session_start();
$base_url = '.';
$page_title = "Customer Sign Up – MultiShopOrder";

require_once __DIR__ . '/config/db.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        // Check existing email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $errors[] = "Email already registered.";
        }
        $stmt->close();
    }

    if (!$errors) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';
        $shop_id = null;

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, shop_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $email, $hashed, $role, $shop_id);
        if ($stmt->execute()) {
            $success = "Registration successful. You can now log in.";
        } else {
            $errors[] = "Registration failed. Try again.";
        }
        $stmt->close();
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h3 class="fw-semibold mb-3 text-center">Customer Sign Up</h3>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success py-2"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (min 6 chars)</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <p class="mt-3 mb-0 text-center text-muted">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
