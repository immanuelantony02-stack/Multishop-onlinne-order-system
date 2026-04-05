<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($required_role) && $_SESSION['role'] !== $required_role) {
    // Simple access denied: send them to their own dashboard or logout
    if ($_SESSION['role'] === 'customer') {
        header("Location: ../customer/dashboard.php");
    } elseif ($_SESSION['role'] === 'vendor') {
        header("Location: ../vendor/dashboard.php");
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../logout.php");
    }
    exit;
}
?>
