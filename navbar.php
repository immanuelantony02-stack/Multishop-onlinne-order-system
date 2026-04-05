<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top" style="background-color:#2563EB;">
    <div class="container">

        <a class="navbar-brand fw-bold" href="/multishoporder/index.php">
            Multishoporder
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center">

                <?php if (isset($_SESSION['role'])): ?>

                    <?php if ($_SESSION['role'] === 'customer'): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/customer/dashboard.php">Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/customer/my_orders.php">My Orders</a>
                        </li>

                    <?php elseif ($_SESSION['role'] === 'vendor'): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/vendor/dashboard.php">Vendor Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/vendor/orders.php">Orders</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/vendor/product.php">Products</a>
                        </li>

                    <?php elseif ($_SESSION['role'] === 'admin'): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/admin/dashboard.php">Admin Dashboard</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="/multishoporder/admin/pay_at_counter.php">Pay at Counter</a>
                        </li>

                    <?php endif; ?>

                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-light btn-sm text-primary fw-semibold" href="/multishoporder/logout.php">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="/multishoporder/login.php">Login</a>
                    </li>

                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-light btn-sm text-primary fw-semibold" href="/multishoporder/register.php">
                            Sign Up
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>